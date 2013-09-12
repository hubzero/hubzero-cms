<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'models' . DS . 'tag.php');

/**
 * Courses model class for a forum
 */
class TagsModelCloud extends JObject
{
	/**
	 * Object type, used for linking objects (such as resources) to tags
	 * 
	 * @var string
	 */
	protected $_scope = 'site';

	/**
	 * The object to be tagged
	 * 
	 * @var unknown
	 */
	protected $_scope_id = null;

	/**
	 * TagsTableTag
	 * 
	 * @var object
	 */
	protected $_tbl = null;

	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	protected $_db = NULL;

	/**
	 * JRegistry
	 * 
	 * @var array
	 */
	protected $_config = null;

	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	protected $_cache = array();

	/**
	 * Constructor
	 * 
	 * @param      integer $id Course ID or alias
	 * @return     void
	 */
	public function __construct($scope_id=0)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new TagsTableTag($this->_db);

		/*if ($scope)
		{
			$this->_scope    = $scope;
		}*/
		if ($scope_id)
		{
			$this->_scope_id = $scope_id;
		}

		$this->_config = JComponentHelper::getParams('com_tags');
	}

	/**
	 * Returns a reference to a tag cloud model
	 *
	 * @param      string  $scope
	 * @param      integer $scope_id
	 * @return     object TagsModelCloud
	 */
	static function &getInstance($scope_id=0, $scope='site')
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		$key = $scope . '_' . $scope_id;

		if (!isset($instances[$key])) 
		{
			$instances[$key] = new TagsModelCloud($scope_id);
		}

		return $instances[$key];
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param	string $property The name of the property
	 * @param	mixed  $default  The default value
	 * @return	mixed The value of the property
 	 */
	public function get($property, $default=null)
	{
		if ($property == 'scope')
		{
			return $this->_scope;
		}
		if ($property == 'scope_id')
		{
			return $this->_scope_id;
		}

		if (isset($this->_tbl->$property)) 
		{
			return $this->_tbl->$property;
		}
		else if (isset($this->_tbl->{'__' . $property})) 
		{
			return $this->_tbl->{'__' . $property};
		}
		return $default;
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param	string $property The name of the property
	 * @param	mixed  $value    The value of the property to set
	 * @return	mixed Previous value of the property
	 */
	public function set($property, $value = null)
	{
		if ($property == 'scope')
		{
			$previous = $this->_scope;
			$this->_scope = $value;
			return $previous;
		}
		if ($property == 'scope_id')
		{
			$previous = $this->_scope_id;
			$this->_scope_id = $value;
			return $previous;
		}

		if (!array_key_exists($property, $this->_tbl->getProperties()))
		{
			$property = '__' . $property;
		}
		$previous = isset($this->_tbl->$property) ? $this->_tbl->$property : null;
		$this->_tbl->$property = $value;
		return $previous;
	}

	/**
	 * Set and get a specific offering
	 * 
	 * @param      mixed $id Integer or string of tag to look up
	 * @return     object TagsModelTag
	 */
	public function tag($id=null)
	{
		if (!isset($this->_cache['tag']) 
		 || (
				$id !== null 
			 && (int) $this->_cache['tag']->get('id') != $id 
			 && (string) $this->_cache['tag']->get('tag') != $this->_tbl->normalize($id)
			)
		 )
		{
			$this->_cache['tag'] = null;
			if (isset($this->_cache['tags']) && is_a($this->_cache['tags'], 'TagsModelIterator'))
			{
				foreach ($this->_cache['tags'] as $key => $tag)
				{
					if ((int) $tag->get('id') == $id || (string) $tag->get('tag') == $this->_tbl->normalize($id))
					{
						$this->_cache['tag'] = $tag;
						break;
					}
				}
			}
			
			if (!$this->_cache['tag'])
			{
				$this->_cache['tag'] = TagsModelTag::getInstance($id);
			}
		}
		return $this->_cache['tag'];
	}

	/**
	 * Get a list of tags
	 * 
	 * @param      string  $rtrn    Format of data to return
	 * @param      array   $filters Filters to apply
	 * @param      boolean $clear   Clear cached data?
	 * @return     mixed
	 */
	public function tags($rtrn='', $filters=array(), $clear=false)
	{
		if (!isset($filters['scope']) && $this->get('scope') != 'site')
		{
			$filters['scope'] = (string) $this->get('scope');
		}
		if (!isset($filters['scope_id']) && $this->get('scope_id') != 0)
		{
			$filters['scope_id'] = (int) $this->get('scope_id');
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_cache['tags_count']) || $clear)
				{
					$this->_cache['tags_count'] = (int) $this->_tbl->getCount($filters);
				}
				return $this->_cache['tags_count'];
			break;

			case 'top':
			break;

			case 'list':
			case 'results':
			default:
				if (!isset($this->_cache['tags']) || !is_a($this->_cache['tags'], 'TagsModelIterator') || $clear)
				{
					if ($results = $this->_tbl->getRecords($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new TagsModelTag($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_cache['tags'] = new TagsModelIterator($results);
				}
				return $this->_cache['tags'];
			break;
		}
	}

	/**
	 * Add tags to an item
	 * 
	 * @param      string $tag Normalized tag
	 * @return     mixed False if errors, integer on success
	 */
	public function add($tags, $tagger=0, $admin=0, $strength=1, $label='')
	{
		if (!$this->_scope_id)
		{
			$this->setError('Unable to add tags: No objct ID provided.');
			return false;
		}

		if (!$tags) 
		{
			$this->setError('Unable to add tags: No tag(s) provided.');
			return false;
		}

		// Is it a comma-separated string?
		if (strstr($tags, ','))
		{
			// Turn into an array
			$tags = explode(',', $tags);
			$tags = array_map('trim', $tags);
		}

		// Force data to an array
		if (!is_array($tag))
		{
			$tags = array($tags);
		}

		foreach ($tags as $tg)
		{
			$tag = TagsModelTag::getInstance($tg);

			// Does the tag already exist?
			if (!$tag->exists())
			{
				// Create it
				$tag->set('admin', $admin);
				$tag->set('raw_tag', $tg);
				$tag->store();
			}

			// Add the tag to the object
			if (!$tag->addTo($this->_scope, $this->_scope_id, $tagger, $strength, $label))
			{
				$this->setError($tag->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Remove tags from an item
	 * 
	 * @param      string $tag Normalized tag
	 * @return     mixed False if errors, integer on success
	 */
	public function remove($tags, $tagger=0)
	{
		if (!$this->_scope_id)
		{
			$this->setError('Unable to remove tags: No objct ID provided.');
			return false;
		}

		if (!$tags) 
		{
			$this->setError('Unable to remove tags: No tag(s) provided.');
			return false;
		}

		// Is it a comma-separated string?
		if (strstr($tags, ','))
		{
			// Turn into an array
			$tags = explode(',', $tags);
			$tags = array_map('trim', $tags);
		}

		// Force data to an array
		if (!is_array($tag))
		{
			$tags = array($tags);
		}

		foreach ($tags as $tg)
		{
			$tag = TagsModelTag::getInstance($tg);

			// Does the tag exist?
			if (!$tag->exists())
			{
				// Tag doesn't exist, no point in going any further
				continue;
			}

			// Remove tag from object
			if (!$tag->removeFrom($this->_scope, $this->_scope_id, $tagger))
			{
				$this->setError($tag->getError());
			}
		}

		return true;
	}

	/**
	 * Remove tags from an item
	 * 
	 * @param      string $tag Normalized tag
	 * @return     mixed False if errors, integer on success
	 */
	public function removeAll($tagger=0)
	{
		if (!$this->_scope_id)
		{
			$this->setError('Unable to remove tags: No objct ID provided.');
			return false;
		}

		$to = new TagsTableObject($this->_db);
		if (!$to->removeAllTags($this->_scope, $this->_scope_id, $tagger)) 
		{
			$this->setError($to->getError());
			return false;
		}
		return true;
	}

	/**
	 * Get the ID of a normalized tag
	 * 
	 * @param      string $tag Normalized tag
	 * @return     mixed False if errors, integer on success
	 */
	private function _getTagId($tag)
	{
		if (!isset($tag)) 
		{
			$this->setError(__CLASS__ . '::' . __METHOD__ . ' - Tag argument missing.');
			return false;
		}

		$t = new TagsTableTag($this->_db);
		$t->loadTag($t->normalize($tag));

		return $t->id;
	}

	/**
	 * Render a tag cloud
	 * 
	 * @param      string  $rtrn    Format to render
	 * @param      array   $filters Filters to apply
	 * @param      boolean $clear   Clear cached data?
	 * @return     string
	 */
	public function render($rtrn='html', $filters=array(), $clear=false)
	{
		switch (strtolower($rtrn))
		{
			case 'string':
				if (!isset($this->_cache['tags_string']) || $clear)
				{
					$tags = array();
					foreach ($this->tags('list', $filters, $clear) as $tag)
					{
						$tags[] = $tag->get('raw_tag');
					}
					$this->_cache['tags_string'] = implode(', ', $tags);
				}
				return $this->_cache['tags_string'];
			break;

			case 'array':
				return $this->tags('list', $filters, $clear);
			break;

			case 'cloud':
			case 'html':
			default:
				if (!isset($this->_cache['tags_cloud']) || $clear)
				{
					$view = new JView(array(
						'base_path' => JPATH_ROOT . '/components/com_tags',
						'name'      => 'tags',
						'layout'    => '_cloud'
					));
					$view->config = $this->_config;
					$view->tags   = $this->tags('list', $filters, $clear);

					$this->_cache['tags_cloud'] = $view->loadTemplate();
				}
				return $this->_cache['tags_cloud'];
			break;
		}
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
	 * @return     boolean True on success, false if errors
	 */
	public function setTags($tag_string, $tagger_id=0, $admin=0, $strength=1, $label='')
	{
		if (!$tagger_id)
		{
			$tagger_id = JFactory::getUser()->get('id');
		}

		$tagArray  = $this->_parse($tag_string);   // array of normalized tags
		$tagArray2 = $this->_parse($tag_string, 1); // array of normalized => raw tags

		$filters = array();
		if (!$admin) 
		{
			$filters['by']        = 'user';
			$filters['admin']     = 0;
			$filters['tagger_id'] = $tagger_id;
		}
		$oldTags = $this->tags('list', $filters, true);

		$preserveTags = array();

		if (count($oldTags) > 0) 
		{
			foreach ($oldTags as $tagItem)
			{
				if (!in_array($tagItem->get('tag'), $tagArray)) 
				{
					// We need to delete old tags that don't appear in the new parsed string.
					$this->remove($tagItem->get('tag'), $tagger_id);
				} 
				else 
				{
					// We need to preserve old tags that appear (to save timestamps)
					$preserveTags[] = $tagItem->get('tag');
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

				$this->add($thistag, $tagger_id, $admin, $strength, $label);
			}
		}
		return true;
	}

	/**
	 * Turn a comma-separated string of tags into an array of normalized tags
	 * 
	 * @param      string  $tag_string Comma-separated string of tags
	 * @param      integer $keep       Use normalized tag as array key
	 * @return     array
	 */
	protected function _parse($tag_string, $keep=0)
	{
		$tag_string = trim($tag_string);

		$newwords = array();

		// If the tag string is empty, return the empty set.
		if ($tag_string == '') 
		{
			return $newwords;
		}

		// Perform tag parsing
		$raw_tags = explode(',', $tag_string);

		foreach ($raw_tags as $raw_tag)
		{
			$raw_tag = trim($raw_tag);
			$nrm_tag = $this->_tbl->normalize($raw_tag);
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

