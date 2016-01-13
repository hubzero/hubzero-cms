<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'tag.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'object.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'substitute.php');

/**
 * Tag helper class for adding/removing/displaying tags on objects
 *
 * Generally, direct use is rare (and discouraged). It will
 * typically be extended by another component.
 */
class TagsHandler extends \Hubzero\Base\Object
{
	/**
	 * Database
	 *
	 * @var unknown
	 */
	public $_db  = NULL;

	/**
	 * Object type, used for linking objects (such as resources) to tags
	 *
	 * @var string
	 */
	public $_tbl = 'tags';

	/**
	 * The object to be tagged
	 *
	 * @var unknown
	 */
	public $_oid = NULL;  //

	/**
	 * The primary tag table
	 *
	 * @var string
	 */
	public $_tag_tbl = '#__tags';

	/**
	 * Tag/object mapping table
	 *
	 * @var string
	 */
	public $_obj_tbl = '#__tags_object';

	/**
	 * Constructor
	 *
	 * @param      object $db     JDatabase
	 * @param      array  $config Configuration options
	 * @return     void
	 */
	public function __construct($db, $config=array())
	{
		$this->_db = $db;
	}

	/**
	 * Get all the tags on an object
	 *
	 * @param      integer $object_id Object ID
	 * @param      integer $offset    Record offset
	 * @param      integer $limit     Record limit
	 * @param      integer $tagger_id Tagger ID (set this if you want to restrict tags only added by a specific user)
	 * @param      integer $strength  Tag strength (set this if you want to restrict tags by strength)
	 * @param      integer $admin     Has admin access?
	 * @return     array
	 */
	public function get_tags_on_object($object_id, $offset=0, $limit=10, $tagger_id=NULL, $strength=0, $admin=0, $label='')
	{
		if (!isset($object_id))
		{
			$this->setError('get_tags_on_object argument missing');
			return array();
		}

		$to = new \Components\Tags\Tables\Object($this->_db);
		$to->objectid = $object_id;
		$to->tbl = $this->_tbl;
		$to->strength = $strength;
		$to->taggerid = $tagger_id;
		$to->label = $label;

		$tags = $to->getTagsOnObject($object_id, $this->_tbl, $admin, $offset, $limit);
		if (!$tags)
		{
			$this->setError($to->getError());
			return array();
		}
		return $tags;
	}

	/**
	 * Add a tag to an object
	 * This will:
	 * 1) First, check if the tag already exists
	 *    a) if not, creates a database entry for the tag
	 * 2) Adds a reference linking tag with object
	 *
	 * @param      integer $tagger_id Tagger ID
	 * @param      integer $object_id Object ID
	 * @param      string  $tag       Tag
	 * @param      integer $strength  Tag strength
	 * @param      string  $tbl 		  Table name	
	 * @return     boolean True on success, fale if errors
	 */
	public function safe_tag($tagger_id, $object_id, $tag, $strength=1, $label='', $tbl='')
	{
		if (!isset($tagger_id) || !isset($object_id) || !isset($tag))
		{
			$this->setError('safe_tag argument missing');
			return false;
		}

		if ($this->normalize_tag($tag) === '0')
		{
			return true;
		}

		$to = new \Components\Tags\Tables\Object($this->_db);
		$to->objectid = $object_id;
		$to->tbl = ($tbl == '' ? $this->_tbl : $tbl);
		$to->label = $label;

		// First see if the tag exists.
		$t = new \Components\Tags\Tables\Tag($this->_db);
		$t->loadTag($t->normalize($tag));
		if (!$t->id)
		{
			// Add new tag!
			$t->tag = $t->normalize($tag);
			$t->raw_tag = addslashes($tag);
			$t->created = \Date::toSql();
			$t->created_by = $tagger_id;
			if (!$t->check())
			{
				$this->setError($t->getError());
				return false;
			}
			if (!$t->store())
			{
				$this->setError($t->getError());
				return false;
			}
			if (!$t->id)
			{
				return false;
			}
			$to->tagid = $t->id;
		}
		else
		{
			$to->tagid = $t->id;

			// Check if the object has already been tagged
			if ($to->getCountForObject() > 0)
			{
				return true;
			}
		}

		// Add an entry linking the tag to the object it was used on
		$to->strength = $strength;
		$to->taggerid = $tagger_id;
		$to->taggedon = Date::toSql();

		if (!$to->store())
		{
			$this->setError($to->getError());
			return false;
		}

		return true;
	}

	/**
	 * Tag an object
	 * This will get a list of old tags on object and will
	 * 1) add any new tags not in the old list
	 * 2) remove any tags in the old list not found in the new list
	 *
	 * @param      integer $tagger_id  Tagger ID
	 * @param      integer $object_id  Object ID
	 * @param      string  $tag_string String of comma-separated tags
	 * @param      integer $strength   Tag strength
	 * @param      boolean $admin      Has admin access?
	 * @param      string  $tbl 			 Table type of the tag 
	 * @return     boolean True on success, false if errors
	 */
	public function tag_object($tagger_id, $object_id, $tag_string, $strength, $admin=false, $label='', $tbl='')
	{
		$tagArray  = $this->_parse_tags($tag_string);   // array of normalized tags
		$tagArray2 = $this->_parse_tags($tag_string, 1); // array of normalized => raw tags
		if ($admin)
		{
			$oldTags = $this->get_tags_on_object($object_id, 0, 0, 0, 0, 1, $label, $tbl); // tags currently assigned to an object
		}
		else
		{
			$oldTags = $this->get_tags_on_object($object_id, 0, 0, $tagger_id, 0, 0, $label, $tbl); // tags currently assigned to an object
		}

		$preserveTags = array();

		if (count($oldTags) > 0)
		{
			foreach ($oldTags as $tagItem)
			{
				if (!in_array($tagItem['tag'], $tagArray))
				{
					// We need to delete old tags that don't appear in the new parsed string.
					$this->remove_tag($tagger_id, $object_id, $tagItem['tag'], $admin);
				}
				else
				{
					// We need to preserve old tags that appear (to save timestamps)
					$preserveTags[] = $tagItem['tag'];
				}
			}
		}
		$newTags = array_diff($tagArray, $preserveTags);

		foreach ($newTags as $tag)
		{
			$tag = trim($tag);
			if ($tag != '')
			{
				if (get_magic_quotes_gpc())
				{
					$tag = addslashes($tag);
				}
				$thistag = $tagArray2[$tag];
				$this->safe_tag($tagger_id, $object_id, $thistag, $strength, $label, $tbl);
			}
		}
		return true;
	}

	/**
	 * Remove a tag on an object
	 *
	 * @param      integer $tagger_id Tagger ID
	 * @param      integer $object_id Object ID
	 * @param      string  $tag       Tag to remove
	 * @param      integer $admin     Has admin access?
	 * @return     boolean True on success, false if errors
	 */
	public function remove_tag($tagger_id, $object_id, $tag, $admin)
	{
		if (!isset($object_id) || !isset($tag))
		{
			$this->setError('remove_tag argument missing');
			return false;
		}

		$tag_id = $this->get_tag_id($tag);
		if (!$tag_id)
		{
			return false;
		}

		$to = new \Components\Tags\Tables\Object($this->_db);
		if (!$to->deleteObjects($tag_id, $this->_tbl, $object_id, $tagger_id, $admin))
		{
			$this->setError($to->getError());
			return false;
		}
		return true;
	}

	/**
	 * Remove all tags on an object
	 *
	 * @param      integer $object_id Object ID
	 * @return     boolean True on success, false if errors
	 */
	public function remove_all_tags($object_id)
	{
		if ($object_id > 0)
		{
			$to = new \Components\Tags\Tables\Object($this->_db);
			if (!$to->removeAllTags($this->_tbl, $object_id))
			{
				$this->setError($to->getError());
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Normalize a tag
	 * Strips spaces, punctuation, makes lowercase, and allows only alpha-numeric chars
	 *
	 * @param      string $tag Raw tag
	 * @return     string Normalized tag
	 */
	public function normalize_tag($tag)
	{
		$t = new \Components\Tags\Tables\Tag($this->_db);
		return $t->normalize($tag);
	}

	/**
	 * Get the ID of a normalized tag
	 *
	 * @param      string $tag Normalized tag
	 * @return     mixed False if errors, integer on success
	 */
	public function get_tag_id($tag)
	{
		if (!isset($tag))
		{
			$this->setError('get_tag_id argument missing');
			return false;
		}

		$t = new \Components\Tags\Tables\Tag($this->_db);
		$t->loadTag($t->normalize($tag));
		return $t->id;
	}

	/**
	 * Get the ID of a raw tag
	 *
	 * @param      string $tag Raw tag
	 * @return     mixed False if errors, integer on success
	 */
	public function get_raw_tag_id($tag)
	{
		if (!isset($tag))
		{
			$this->setError('get_raw_tag_id argument missing');
			return false;
		}
		return $this->get_tag_id($tag);
	}

	/**
	 * Get a count of tags
	 *
	 * @param      integer $admin     Show admin tags?
	 * @return     integer
	 */
	public function count_tags($admin=0)
	{
		$filters = array();
		$filters['by'] = 'user';
		if ($admin)
		{
			$filters['by'] = 'all';
		}
		$t = new \Components\Tags\Tables\Tag($this->_db);
		return $t->getCount($filters);
	}

	/**
	 * Get a tag cloud for an object
	 *
	 * @param      integer $showsizes Show tag size based on use?
	 * @param      integer $admin     Show admin tags?
	 * @param      integer $objectid  Object ID
	 * @return     mixed Return description (if any) ...
	 */
	public function get_tag_cloud($showsizes=0, $admin=0, $objectid=NULL)
	{
		$t = new \Components\Tags\Tables\Tag($this->_db);
		$tags = $t->getCloud($this->_tbl, $admin, $objectid);

		return $this->buildCloud($tags, 'alpha', $showsizes);
	}

	/**
	 * Build a tag cloud
	 *
	 * @param      array   $tags      List of tags
	 * @param      string  $sort      How to sort tags?
	 * @param      integer $showsizes Show tag size based on use?
	 * @return     string HTML
	 */
	public function buildCloud($tags, $sort='alpha', $showsizes=0)
	{
		$html = '';

		if ($tags && count($tags) > 0)
		{
			$min_font_size = 1;
			$max_font_size = 1.8;

			if ($showsizes)
			{
				$retarr = array();
				foreach ($tags as $tag)
				{
					$retarr[$tag->raw_tag] = $tag->count;
				}
				ksort($retarr);

				$max_qty = max(array_values($retarr));  // Get the max qty of tagged objects in the set
				$min_qty = min(array_values($retarr));  // Get the min qty of tagged objects in the set

				// For ever additional tagged object from min to max, we add $step to the font size.
				$spread = $max_qty - $min_qty;
				if (0 == $spread)
				{ // Divide by zero
					$spread = 1;
				}
				$step = ($max_font_size - $min_font_size)/($spread);
			}

			// build HTML
			$html .= '<ol class="tags">' . "\n";
			$tll = array();
			foreach ($tags as $tag)
			{
				$class = '';
				switch ($tag->admin)
				{
					/*case 0:
						$class = ' class="restricted"';
					break;*/
					case 1:
						$class = ' class="admin"';
					break;
				}

				$tag->raw_tag = stripslashes($tag->raw_tag);
				$tag->raw_tag = str_replace('&amp;', '&', $tag->raw_tag);
				$tag->raw_tag = str_replace('&', '&amp;', $tag->raw_tag);
				if ($showsizes == 1)
				{
					$size = $min_font_size + ($tag->count - $min_qty) * $step;
					$tll[$tag->tag] = "\t".'<li' . $class . '><span style="font-size: ' . round($size, 1) . 'em"><a href="' . Route::url('index.php?option=com_tags&tag=' . $tag->tag) . '">' . stripslashes($tag->raw_tag) . '</a></li>' . "\n"; //' <span>' . $tag->count . '</span></a></span></li>' . "\n";
				}
				elseif ($showsizes == 2)
				{
					$tll[$tag->tag] = "\t".'<li' . $class . '><a href="javascript:void(0);" onclick="addtag(\'' . $tag->tag . '\');">' . stripslashes($tag->raw_tag) . '</a></li>' . "\n"; //' <span>' . $tag->count . '</span></a></li>' . "\n";
				}
				else
				{
					$tll[$tag->tag] = "\t".'<li' . $class . '><a href="' . Route::url('index.php?option=com_tags&tag=' . $tag->tag) . '">' . stripslashes($tag->raw_tag) . '</a></li>' . "\n"; //' <span>' . $tag->count . '</span></a></li>' . "\n";
				}
			}
			if ($sort == 'alpha')
			{
				ksort($tll);
				$html .= implode('', $tll);
			}
			$html .= '</ol>' . "\n";
		}

		return $html;
	}

	/**
	 * Return a list of tags for an object as a comma-separated string
	 *
	 * @param      integer $oid       Object ID
	 * @param      integer $offset    Record offset
	 * @param      integer $limit     Number to return
	 * @param      integer $tagger_id Tagger ID
	 * @param      integer $strength  Tag strength
	 * @param      integer $admin     Admin tags?
	 * @return     string
	 */
	public function get_tag_string($oid, $offset=0, $limit=0, $tagger_id=NULL, $strength=0, $admin=0, $label='')
	{
		$tags = $this->get_tags_on_object($oid, $offset, $limit, $tagger_id, $strength, $admin, $label);

		if ($tags && count($tags) > 0)
		{
			$tagarray = array();
			foreach ($tags as $tag)
			{
				$tagarray[] = $tag['raw_tag'];
			}
			$tags = implode(', ', $tagarray);
		}
		else
		{
			$tags = (is_array($tags)) ? implode('', $tags) : '';
		}
		return $tags;
	}

	/**
	 * Turn a comma-separated string of tags into an array of normalized tags
	 *
	 * @param      string  $tag_string Comma-separated string of tags
	 * @param      integer $keep       Use normalized tag as array key
	 * @return     array
	 */
	public function _parse_tags($tag_string, $keep=0)
	{
		$newwords = array();

		if (is_string($tag_string))
		{
			// If the tag string is empty, return the empty set.
			if ($tag_string == '')
			{
				return $newwords;
			}

			// Perform tag parsing
			$tag_string = trim($tag_string);
			$raw_tags = explode(',', $tag_string);
		}
		else if (is_array($tag_string))
		{
			$raw_tags = $tag_string;
		}
		else
		{
			throw new \InvalidArgumentException(Lang::txt('Tag lsit must be an array or string. Type of "%s" passed.', gettype($tag_string)));
		}

		foreach ($raw_tags as $raw_tag)
		{
			$raw_tag = trim($raw_tag);
			$nrm_tag = $this->normalize_tag($raw_tag);
			if ($keep != 0)
			{
				$newwords[$nrm_tag] = $raw_tag;
			}
			else
			{
				$newwords[] = $nrm_tag;
			}
		}
		return $newwords;
	}
}

