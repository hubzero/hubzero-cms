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
	public $_scope = 'tags';

	/**
	 * The object to be tagged
	 * 
	 * @var unknown
	 */
	public $_scope_id = NULL;

	/**
	 * TagsTag
	 * 
	 * @var object
	 */
	private $_tbl = null;

	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $_config = null;

	/**
	 * Constructor
	 * 
	 * @param      integer $id Course ID or alias
	 * @return     void
	 */
	public function __construct($scope='site', $scope_id=0)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new TagsTag($this->_db);

		$this->_scope    = $scope;
		$this->_scope_id = $scope_id;

		$this->_config = JComponentHelper::getParams('com_tags');
	}

	/**
	 * Returns a reference to a forum model
	 *
	 * This method must be invoked as:
	 *     $offering = ForumModelCourse::getInstance($alias);
	 *
	 * @param      mixed $oid Course ID (int) or alias (string)
	 * @return     object ForumModelCourse
	 */
	static function &getInstance($scope='site', $scope_id=0)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		$key = $scope . '_' . $scope_id;

		if (!isset($instances[$key])) 
		{
			$instances[$key] = new TagsModelCloud($scope, $scope_id);
		}

		return $instances[$key];
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param	string $property The name of the property
	 * @param	mixed  $default The default value
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
	 * @param	mixed  $value The value of the property to set
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
	 * @return     void
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
	 * Get a list of categories for a forum
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function tags($rtrn='', $filters=array(), $clear=false)
	{
		if (!isset($filters['scope']))
		{
			$filters['scope'] = (string) $this->get('scope');
		}
		if (!isset($filters['scope_id']))
		{
			$filters['scope_id'] = (int) $this->get('scope_id');
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_cache['tags_count']) || $clear) // || $this->_cache['filters'] != serialize($filters))
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

	public function add()
	{

	}

	public function remove($tags, $tagger=0, $admin=0)
	{
		if (!$tags) 
		{
			$this->setError('No tag(s) provided.');
			return false;
		}

		if (!is_array($tag))
		{
			$tags = array($tags);
		}

		/*$tag_id = $this->_getTagId($tag);
		if (!$tag_id) 
		{
			return false;
		}
		$to = new \Components\Tags\Object($this->_db);*/
		foreach ($tags as $tg)
		{
			$tag = new TagsModelTag($tg);
			/*if (!$to->deleteObjects($tag->get('id'), $this->_scope, $this->_scope_id, $tagger, $admin)) 
			{
				$this->setError($to->getError());
				return false;
			}*/
			if (!$tag->removeFrom($this->_scope, $this->_scope_id, $tagger))
			{
				$this->setError($tag->getError());
			}
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
			$this->setError('get_tag_id argument missing');
			return false;
		}

		$t = new TagsTag($this->_db);
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
						$tags[] = $tag->raw_tag;
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
}

