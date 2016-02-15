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

namespace Components\Tags\Models;

use Hubzero\Component\View;
use User;
use Date;

require_once(__DIR__ . DS . 'tag.php');

/**
 * Cloud model for Tags
 */
class Cloud extends \Hubzero\Base\Object
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
	 * @var integer
	 */
	protected $_scope_id = 0;

	/**
	 * Database
	 *
	 * @var object
	 */
	protected $_db = NULL;

	/**
	 * Container for properties
	 *
	 * @var array
	 */
	protected $_cache = array(
		'tags.one'    => null,
		'tags.count'  => null,
		'tags.list'   => null,
		'tags.string' => null,
		'tags.cloud'  => null
	);

	/**
	 * Constructor
	 *
	 * @param   integer  $scope_id
	 * @param   string   $scope
	 * @return  void
	 */
	public function __construct($scope_id=0, $scope='')
	{
		$this->_db = \App::get('db');

		if ($scope)
		{
			$this->_scope    = (string)$scope;
		}
		if ($scope_id)
		{
			$this->_scope_id = (int)$scope_id;
		}
	}

	/**
	 * Returns a reference to a tag cloud model
	 *
	 * @param   integer  $scope_id
	 * @param   string   $scope
	 * @return  object
	 */
	static function getInstance($scope_id=0, $scope='')
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		$key = (string) $scope . '_' . (int) $scope_id;

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($scope_id, $scope);
		}

		return $instances[$key];
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param   string  $property  The name of the property
	 * @param   mixed   $default   The default value
	 * @return  mixed   The value of the property
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

		return parent::get($property, $default);
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param   string  $property  The name of the property
	 * @param   mixed   $value     The value of the property to set
	 * @return  mixed   Previous value of the property
	 */
	public function set($property, $value = null)
	{
		if ($property == 'scope')
		{
			$this->_scope = $value;
			return $this;
		}
		if ($property == 'scope_id')
		{
			$this->_scope_id = $value;
			return $this;
		}

		return parent::set($property, $value);
	}

	/**
	 * Set and get a specific offering
	 *
	 * @param   mixed   $id  Integer or string of tag to look up
	 * @return  object
	 */
	public function tag($id=null)
	{
		if (!$this->_cache['tags.one']
		 || (
				$id !== null
			 && (int) $this->_cache['tags.one']->get('id') != $id
			 && (string) $this->_cache['tags.one']->get('tag') != $this->normalize($id)
			)
		 )
		{
			$this->_cache['tags.one'] = is_numeric($id) ? Tag::oneOrNew($id) : Tag::oneByTag($id);
		}

		return $this->_cache['tags.one'];
	}

	/**
	 * Get a list of tags
	 *
	 * @param   string   $rtrn     Format of data to return
	 * @param   array    $filters  Filters to apply
	 * @param   boolean  $clear    Clear cached data?
	 * @return  mixed
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

		$tbl = Object::blank()->getTableName();

		$results = Tag::all();
		$results
			->select($results->getTableName() . '.*');

		if (isset($filters['sort']) && $filters['sort'] == 'taggedon')
		{
			$results
				->select($tbl . '.taggedon')
				->join($tbl, $tbl . '.tagid', $results->getTableName() . '.id')
				->group($results->getTableName() . '.id');
		}

		if (isset($filters['tagger_id'])
		 || isset($filters['scope'])
		 || isset($filters['scope_id'])
		 || isset($filters['label']))
		{
			$results->join($tbl, $tbl . '.tagid', $results->getTableName() . '.id');

			if (isset($filters['tagger_id']) && $filters['tagger_id'])
			{
				$results->whereEquals($tbl . '.taggerid', (int) $filters['tagger_id']);
			}
			if (isset($filters['scope']) && $filters['scope'])
			{
				$results->whereEquals($tbl . '.tbl', (string) $filters['scope']);
			}
			if (isset($filters['scope_id']) && $filters['scope_id'])
			{
				$results->whereEquals($tbl . '.objectid', (int) $filters['scope_id']);
			}
			if (isset($filters['label']) && $filters['label'])
			{
				$results->whereEquals($tbl . '.label', (string) $filters['label']); // find labeled tags
			}
		}

		if (isset($filters['admin']) && $filters['admin'] !== null)
		{
			$results->whereEquals('admin', (int) $filters['admin']);
		}
		if (isset($filters['created_by']) && $filters['created_by'] > 0)
		{
			$results->whereEquals('created_by', (int) $filters['created_by']);
		}
		if (isset($filters['modified_by']) && $filters['modified_by'] > 0)
		{
			$results->whereEquals('modified_by', $filters['modified_by']);
		}
		if (isset($filters['search']) && $filters['search'])
		{
			$filters['search'] = strtolower((string)$filters['search']);

			$tbl = Substitute::blank()->getTableName();

			$results->select($results->getTableName() . '.*')
				->join($tbl, $tbl . '.tag_id', $results->getTableName() . '.id', 'left');

			$results->whereLike($results->getTableName() . '.raw_tag', $filters['search'], 1)
				->orWhereLike($results->getTableName() . '.tag', $filters['search'], 1)
				->orWhereLike($tbl . '.raw_tag', $filters['search'], 1)
				->resetDepth();
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_cache['tags.count']) || $clear)
				{
					$this->_cache['tags.count'] = (int) $results->total();
				}
				return $this->_cache['tags.count'];
			break;

			case 'top':
			break;

			case 'list':
			case 'results':
			default:
				if (!$this->_cache['tags.list'] || $clear)
				{
					if (isset($filters['limit']) && $filters['limit'] != 0  && $filters['limit'] != 'all')
					{
						if (!isset($filters['start']))
						{
							$filters['start'] = 0;
						}

						$results->limit($filters['limit']);
						$results->start($filters['start']);
					}
					if (isset($filters['sort']) && $filters['sort'] != '')
					{
						if ($filters['sort'] == 'total')
						{
							$filters['sort'] = 'objects';
						}

						$filters['sort_Dir'] = (isset($filters['sort_Dir']) && $filters['sort_Dir']) ? $filters['sort_Dir'] : "ASC";
						$results->order($filters['sort'], $filters['sort_Dir']);
					}

					$this->_cache['tags.list'] = $results->rows();
				}
				return $this->_cache['tags.list'];
			break;
		}
	}

	/**
	 * Add tags to an item
	 *
	 * @param   mixed    $tags      Array or string of tags
	 * @param   integer  $tagger    ID of user applying the tag
	 * @param   integer  $admin     Is it an admin tag?
	 * @param   integer  $strength  Tag strength
	 * @param   string   $label     Label to apply
	 * @return  mixed    False if errors, integer on success
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

		foreach ($this->_parse($tags, 1) as $tg => $raw)
		{
			$tag = Tag::oneByTag((string) $tg);

			// Does the tag already exist?
			if ($tag->isNew())
			{
				// Create it
				$tag->set('admin', $admin);
				$tag->set('tag', $tg);
				$tag->set('raw_tag', $raw);
				$tag->set('created', Date::toSql());
				$tag->set('created_by', $tagger);
				$tag->save();
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
	 * @param   mixed    $tags    Array or string of tags
	 * @param   integer  $tagger  ID of user to remove tags for
	 * @return  mixed    False if errors, integer on success
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

		foreach ($this->_parse($tags) as $tg)
		{
			$tag = Tag::oneByTag((string) $tg);

			// Does the tag exist?
			if (!$tag->get('id'))
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
	 * Remove all tags from an item
	 * Option User ID to remove tags added by just that user.
	 *
	 * @param   string  $tagger  User ID to remove tags for
	 * @return  mixed   False if errors, integer on success
	 */
	public function removeAll($tagger=0)
	{
		if (!$this->_scope_id)
		{
			$this->setError('Unable to remove tags: No objct ID provided.');
			return false;
		}

		$to = Object::all()
			->whereEquals('tbl', $this->_scope)
			->whereEquals('objectid', $this->_scope_id);

		if ($tagger)
		{
			$to->whereEquals('taggerid', $tagger);
		}

		$tags = array();

		foreach ($to->rows() as $row)
		{
			$tags[] = $row->get('tagid');

			if (!$row->destroy())
			{
				$this->setError($row->getError());
				return false;
			}
		}

		foreach ($tags as $tag_id)
		{
			$tag = Tag::oneOrFail($tag_id);
			$tag->set('objects', $tag->objects()->total())
				->save();
		}

		return true;
	}

	/**
	 * Get the ID of a normalized tag
	 *
	 * @param   string  $tag  Normalized tag
	 * @return  mixed   False if errors, integer on success
	 */
	private function _getTagId($tag)
	{
		if (!isset($tag))
		{
			$this->setError(__CLASS__ . '::' . __METHOD__ . ' - Tag argument missing.');
			return false;
		}

		$t = Tag::oneByTag($tag);

		return $t->get('id');
	}

	/**
	 * Render a tag cloud
	 *
	 * @param   string   $rtrn     Format to render
	 * @param   array    $filters  Filters to apply
	 * @param   boolean  $clear    Clear cached data?
	 * @return  string
	 */
	public function render($rtrn='html', $filters=array(), $clear=false)
	{
		switch (strtolower($rtrn))
		{
			case 'string':
				if (!isset($this->_cache['tags.string']) || $clear)
				{
					$tags = array();
					foreach ($this->tags('list', $filters, $clear) as $tag)
					{
						$tags[] = $tag->get('raw_tag');
					}
					$this->_cache['tags.string'] = implode(', ', $tags);
				}
				return $this->_cache['tags.string'];
			break;

			case 'array':
				$tags = array();
				foreach ($this->tags('list', $filters, $clear) as $tag)
				{
					$tags[] = $tag->get('tag');
				}
				return $tags;
			break;

			case 'cloud':
			case 'html':
			default:
				if (!isset($this->_cache['tags.cloud']) || $clear)
				{
					$view = new View(array(
						'base_path' => PATH_CORE . '/components/com_tags/site',
						'name'      => 'tags',
						'layout'    => '_cloud'
					));
					$view->set('config', \Component::params('com_tags'))
					     ->set('tags', $this->tags('list', $filters, $clear));

					$this->_cache['tags.cloud'] = $view->loadTemplate();
				}
				return $this->_cache['tags.cloud'];
			break;
		}
	}

	/**
	 * Tag an object
	 * This will get a list of old tags on object and will
	 *   1) add any new tags not in the old list
	 *   2) remove any tags in the old list not found in the new list
	 *
	 * @param   string   $tag_string  String of comma-separated tags
	 * @param   integer  $tagger_id   Tagger ID
	 * @param   boolean  $admin       Mark tags as admin?
	 * @param   integer  $strength    Tag strength
	 * @param   string   $label       Label to attach
	 * @return  boolean  True on success, false if errors
	 */
	public function setTags($tag_string, $tagger_id=0, $admin=0, $strength=1, $label='')
	{
		if (!$tagger_id)
		{
			$tagger_id = User::get('id');
		}

		$tagArray  = $this->_parse($tag_string);    // array of normalized tags
		$tagArray2 = $this->_parse($tag_string, 1); // array of normalized => raw tags

		$filters = array();
		if ($label)
		{
			$filters['label'] = $label;
		}
		/*if (!$admin)
		{
			$filters['by']        = 'user';
			$filters['admin']     = 0;
			$filters['tagger_id'] = $tagger_id;
		}*/
		$oldTags = $this->tags('list', $filters, true);

		$preserveTags = array();

		if (count($oldTags) > 0)
		{
			foreach ($oldTags as $tagItem)
			{
				if (!in_array($tagItem->get('tag'), $tagArray))
				{
					// We need to delete old tags that don't appear in the new parsed string.
					$this->remove($tagItem->get('tag')); //, ($admin ? 0 : $tagger_id));
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
	 * Normalize a raw tag
	 * Strips all non-alphanumeric characters
	 *
	 * @param   string  $tag  Raw tag
	 * @return  string
	 */
	public function normalize($tag)
	{
		return Tag::blank()->normalize($tag);
	}

	/**
	 * Turn a comma-separated string of tags into an array of normalized tags
	 *
	 * @param   mixed    $tags  Array or Comma-separated string of tags
	 * @param   integer  $keep  Use normalized tag as array key
	 * @return  array
	 */
	protected function _parse($tags, $keep=0)
	{
		if (is_string($tags))
		{
			$tags = trim($tags);
			$tags = preg_split("/(,|;)/", $tags);
		}

		$parsed = array();

		// If the tag list is empty, return the empty set.
		if (empty($tags))
		{
			return $parsed;
		}

		// Perform tag parsing
		foreach ($tags as $raw_tag)
		{
			$raw_tag = trim($raw_tag);
			$nrm_tag = $this->normalize($raw_tag);
			if ($keep != 0)
			{
				$parsed[$nrm_tag] = $raw_tag;
			}
			else
			{
				$parsed[] = $nrm_tag;
			}
		}
		return $parsed;
	}
}

