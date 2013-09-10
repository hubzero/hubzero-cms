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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'tables' . DS . 'tag.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'models' . DS . 'log.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'models' . DS . 'object.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'models' . DS . 'substitute.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'models' . DS . 'iterator.php');

if (version_compare(JVERSION, '1.6', 'ge'))
{
	define('TAGS_DATE_YEAR', "Y");
	define('TAGS_DATE_MONTH', "m");
	define('TAGS_DATE_DAY', "d");
	define('TAGS_DATE_TIMEZONE', true);
	define('TAGS_DATE_FORMAT', 'd M Y');
	define('TAGS_TIME_FORMAT', 'H:i p');
}
else
{
	define('TAGS_DATE_YEAR', "%Y");
	define('TAGS_DATE_MONTH', "%m");
	define('TAGS_DATE_DAY', "%d");
	define('TAGS_DATE_TIMEZONE', 0);
	define('TAGS_DATE_FORMAT', '%d %b %Y');
	define('TAGS_TIME_FORMAT', '%I:%M %p');
}

/**
 * Model class for a tag
 */
class TagsModelTag extends JObject
{
	/**
	 * Base URL to this tag
	 * 
	 * @var string
	 */
	protected $_base = null;

	/**
	 * Containe for cached data
	 * 
	 * @var array
	 */
	protected $_cache = array();

	/**
	 * TagsTag
	 * 
	 * @var object
	 */
	protected $_tbl = null;

	/**
	 * JUser
	 * 
	 * @var object
	 */
	protected $_creator = NULL;

	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	protected $_db = NULL;

	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	//protected $_config;

	/**
	 * Constructor
	 * 
	 * @param      integer $id Tag ID or raw tag
	 * @return     void
	 */
	public function __construct($oid)
	{
		// Set the database object
		$this->_db = JFactory::getDBO();

		// Set the table object
		$this->_tbl = new TagsTag($this->_db);

		// Load record
		if (is_numeric($oid))
		{
			$this->_tbl->load($oid);
		}
		else if (is_string($oid))
		{
			$this->_tbl->loadTag($oid);
		}
		else if (is_object($oid))
		{
			$this->_tbl->bind($oid);
			$properties = $this->_tbl->getProperties();
			foreach (get_object_vars($oid) as $key => $property)
			{
				if (!array_key_exists($key, $properties))
				{
					$this->_tbl->set('__' . $key, $property);
				}
			}
		}
		else if (is_array($oid))
		{
			$this->_tbl->bind($oid);
			$properties = $this->_tbl->getProperties();
			foreach (array_keys($oid) as $key)
			{
				if (!array_key_exists($key, $properties))
				{
					$this->_tbl->set('__' . $key, $oid[$key]);
				}
			}
		}

		// Set the base path to this tag
		$this->_base = 'index.php?option=com_tags&tag=' . $this->get('tag');

		// Get the component config
		//$this->_config = JComponentHelper::getParams('com_tags');
	}

	/**
	 * Returns a reference to a tag model
	 *
	 * @param      mixed $oid Tag ID or raw tag
	 * @return     object TagsModelTag
	 */
	static function &getInstance($oid=0)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $oid;
		}
		else if (is_object($oid))
		{
			$key = $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'];
		}

		if (!isset($instances[$oid])) 
		{
			$instances[$oid] = new TagsModelTag($oid);
		}

		return $instances[$oid];
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
		if (!array_key_exists($property, $this->_tbl->getProperties()))
		{
			$property = '__' . $property;
		}
		$previous = isset($this->_tbl->$property) ? $this->_tbl->$property : null;
		$this->_tbl->$property = $value;
		return $previous;
	}

	/**
	 * Check if the data exists
	 * 
	 * @return     boolean
	 */
	public function exists()
	{
		if ($this->get('id') && (int) $this->get('id') > 0) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Get the creator of this entry
	 * 
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire JUser object
	 *
	 * @return     mixed
	 */
	public function creator($property=null)
	{
		if (!isset($this->_creator) || !is_object($this->_creator))
		{
			$this->_creator = JUser::getInstance($this->get('created_by'));
		}
		if ($property && is_a($this->_creator, 'JUser'))
		{
			return $this->_creator->get($property);
		}
		return $this->_creator;
	}

	/**
	 * Return a formatted timestamp
	 * 
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function created($rtrn='')
	{
		switch (strtolower($rtrn))
		{
			case 'date':
				return JHTML::_('date', $this->get('created'), TAGS_DATE_FORMAT, TAGS_DATE_TIMEZONE);
			break;

			case 'time':
				return JHTML::_('date', $this->get('created'), TAGS_TIME_FORMAT, TAGS_DATE_TIMEZONE);
			break;

			default:
				return $this->get('created');
			break;
		}
	}

	/**
	 * Return a formatted timestamp
	 * 
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function modified($rtrn='')
	{
		switch (strtolower($rtrn))
		{
			case 'date':
				return JHTML::_('date', $this->get('modified'), TAGS_DATE_FORMAT, TAGS_DATE_TIMEZONE);
			break;

			case 'time':
				return JHTML::_('date', $this->get('modified'), TAGS_TIME_FORMAT, TAGS_DATE_TIMEZONE);
			break;

			default:
				return $this->get('modified');
			break;
		}
	}

	/**
	 * Bind data to this model
	 * Accepts an array or object
	 * 
	 * @param      mixed $data Data to bind to this model
	 * @return     boolean
	 */
	public function bind($data=null)
	{
		if (is_object($data))
		{
			$res = $this->_tbl->bind($data);

			$properties = $this->_tbl->getProperties();
			foreach (get_object_vars($data) as $key => $property)
			{
				if (!array_key_exists($key, $properties))
				{
					$this->_tbl->set('__' . $key, $property);
				}
			}
		}
		else if (is_array($data))
		{
			$res = $this->_tbl->bind($data);

			$properties = $this->_tbl->getProperties();
			foreach (array_keys($data) as $key)
			{
				if (!array_key_exists($key, $properties))
				{
					$this->_tbl->set('__' . $key, $data[$key]);
				}
			}
		}
		return $res;
	}

	/**
	 * Store changes to this tag
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		// Ensure we have a database to work with
		if (empty($this->_db))
		{
			return false;
		}

		// Validate data?
		if ($check)
		{
			// Is data valid?
			if (!$this->_tbl->check())
			{
				$this->setError($this->_tbl->getError());
				return false;
			}
		}

		if (!$this->exists())
		{
			if ($this->_tbl->checkExistence()) 
			{
				$this->setError(JText::_('COM_TAGS_TAG_EXIST'));
				return false;
			}
		}

		// Attempt to store data
		if (!$this->_tbl->store())
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		if (!$this->_tbl->saveSubstitutions($this->get('substitutions')))
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		return true;
	}

	/**
	 * Store changes to this offering
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function delete()
	{
		// Ensure we have a database to work with
		if (empty($this->_db))
		{
			$this->setError(JText::_('Database not found.'));
			return false;
		}

		// Can't delete what doesn't exist
		if (!$this->exists()) 
		{
			return true;
		}

		// Remove associations
		foreach ($this->objects() as $obj)
		{
			if (!$obj->delete())
			{
				$this->setError($obj->getError());
				return false;
			}
		}

		// Remove substitutes
		foreach ($this->substitutes() as $substitute)
		{
			if (!$substitute->delete())
			{
				$this->setError($substitute->getError());
				return false;
			}
		}

		// Attempt to delete the record
		if (!$this->_tbl->delete())
		{
			$this->setError($this->_tbl->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 * 
	 * @param      string $type The type of link to return
	 * @return     boolean
	 */
	public function link($type='')
	{
		$link  = $this->_base;

		switch (strtolower($type))
		{
			case 'edit':
				$link .= '&task=edit';
			break;

			case 'delete':
				$link .= '&task=delete';
			break;

			case 'permalink':
			default:

			break;
		}

		return $link;
	}

	/**
	 * Return a list or count of substitutions on this tag
	 * 
	 * @param      string  $rtrn    What data to return (ex: 'list', 'count')
	 * @param      array   $filters Filters to apply for data retrieval
	 * @param      boolean $clear   Clear cached data?
	 * @return     mixed
	 */
	public function substitutes($rtrn='list', $filters=array(), $clear=false)
	{
		if (!isset($filters['tag_id']))
		{
			$filters['tag_id'] = (int) $this->get('id');
		}
		if (!isset($filters['start']))
		{
			$filters['start'] = 0;
		}
		if (!isset($filters['limit']))
		{
			$filters['limit'] = 100;
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_cache['sub_count']) || $clear)
				{
					$tbl = new TagsSubstitute($this->_db);
					$this->_cache['sub_count'] = (int) $tbl->getCount($filters);
				}
				return $this->_cache['sub_count'];
			break;

			case 'string':
				$subs = array();
				foreach ($this->substitutes('list', $filters) as $foo => $substitution)
				{
					$subs[] = $substitution->get('raw_tag');
				}
				return implode(', ', $subs);
			break;

			case 'list':
			case 'results':
			default:
				if (!isset($this->_cache['subs']) || !is_a($this->_cache['subs'], 'TagsModelIterator') || $clear)
				{
					$results = array();

					$tbl = new TagsSubstitute($this->_db);
					if ($res = $tbl->getRecords($filters['tag_id'], $filters['start'], $filters['limit']))
					{
						foreach ($res as $key => $result)
						{
							$results[] = new TagsModelSubstitute($result);
						}
					}

					$this->_cache['subs'] = new TagsModelIterator($results);
				}
				return $this->_cache['subs'];
			break;
		}
	}

	/**
	 * Return a list or count of objects associated with this tag
	 * 
	 * @param      string  $rtrn    What data to return (ex: 'list', 'count')
	 * @param      array   $filters Filters to apply for data retrieval
	 * @param      boolean $clear   Clear cached data?
	 * @return     mixed
	 */
	public function objects($rtrn='list', $filters=array(), $clear=false)
	{
		if (!isset($filters['tag_id']))
		{
			$filters['tag_id'] = (int) $this->get('id');
		}
		if (!isset($filters['start']))
		{
			$filters['start'] = 0;
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_cache['objects_count']) || $clear)
				{
					$tbl = new TagsObject($this->_db);
					$this->_cache['objects_count'] = (int) $tbl->count($filters);
				}
				return $this->_cache['objects_count'];
			break;

			case 'list':
			case 'results':
			default:
				if (!isset($this->_cache['objects']) || !is_a($this->_cache['objects'], 'TagsModelIterator') || $clear)
				{
					$tbl = new TagsObject($this->_db);
					if ($results = $tbl->find($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new TagsModelObject($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_cache['objects'] = new TagsModelIterator($results);
				}
				return $this->_cache['objects'];
			break;
		}
	}

	/**
	 * Return a list or count of objects associated with this tag
	 * 
	 * @param      string  $rtrn    What data to return (ex: 'list', 'count')
	 * @param      array   $filters Filters to apply for data retrieval
	 * @param      boolean $clear   Clear cached data?
	 * @return     mixed
	 */
	public function logs($rtrn='list', $filters=array(), $clear=false)
	{
		if (!isset($filters['tag_id']))
		{
			$filters['tag_id'] = (int) $this->get('id');
		}
		if (!isset($filters['start']))
		{
			$filters['start'] = 0;
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_cache['logs_count']) || $clear)
				{
					$tbl = new TagsLog($this->_db);
					$this->_cache['logs_count'] = (int) $tbl->count($filters);
				}
				return $this->_cache['logs_count'];
			break;

			case 'list':
			case 'results':
			default:
				if (!isset($this->_cache['logs']) || !is_a($this->_cache['logs'], 'TagsModelIterator') || $clear)
				{
					$tbl = new TagsLog($this->_db);
					if ($results = $tbl->find($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new TagsModelLog($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_cache['logs'] = new TagsModelIterator($results);
				}
				return $this->_cache['logs'];
			break;
		}
	}

	/**
	 * Remove this tag from an object
	 *
	 * If $taggerid is provided, it will only remove the tags added to an object by 
	 * that specific user
	 * 
	 * @param      string  $scope    Object type (ex: resource, ticket)
	 * @param      integer $scope_id Object ID (e.g., resource ID, ticket ID)
	 * @param      integer $taggerid User ID of person to filter tag by
	 * @return     boolean
	 */
	public function removeFrom($scope, $scope_id, $tagger=0)
	{
		// Check if the relationship exists
		$to = new TagsModelObject($scope, $scope_id, $this->get('id'));
		if (!$to->exists())
		{
			return true;
		}

		// Attempt to delete the record
		if (!$to->delete())
		{
			$this->setError($to->getError());
			return false;
		}

		return true;
	}

	/**
	 * Add this tag to an object
	 * 
	 * @param      string  $scope    Object type (ex: resource, ticket)
	 * @param      integer $scope_id Object ID (e.g., resource ID, ticket ID)
	 * @param      integer $taggerid User ID of person adding tag
	 * @return     boolean
	 */
	public function addTo($scope, $scope_id, $taggerid=0)
	{
		// Check if the relationship already exists
		$to = new TagsModelObject($scope, $scope_id, $this->get('id'));
		if ($to->exists())
		{
			return true;
		}

		// Set some data
		$to->set('objectid', (int) $scope_id);
		$to->set('tagid', (int) $this->get('id'));
		if ($taggerid)
		{
			$to->set('taggerid', $taggerid);
		}
		$to->set('tbl', (string) $scope);

		// Attempt to store the new record
		if (!$to->store(true))
		{
			$this->setError($to->getError());
			return false;
		}

		return true;
	}

	/**
	 * Move all data from this tag to another, including the tag itself
	 * 
	 * @param      integer $tag_id ID of tag to merge with
	 * @return     boolean
	 */
	public function mergeWith($tag_id)
	{
		// Get all the associations to this tag
		// Loop through the associations and link them to a different tag
		$to = new TagsObject($this->_db);
		if (!$to->moveObjects($this->get('id'), $tag_id))
		{
			$this->setError($to->getError());
			return false;
		}

		// Get all the substitutions to this tag
		// Loop through the records and link them to a different tag
		$ts = new TagsSubstitute($this->_db);
		if (!$ts->moveSubstitutes($this->get('id'), $tag_id))
		{
			$this->setError($ts->getError());
			return false;
		}

		// Make the current tag a substitute for the new tag
		$sub = new TagsModelSubstitute(0);
		$sub->set('raw_tag', $this->get('raw_tag'));
		$sub->set('tag_id', $tag_id);
		if (!$sub->store(true))
		{
			$this->setError($sub->getError());
			return false;
		}

		if (!$this->delete())
		{
			return false;
		}

		return true;
	}

	/**
	 * Copy associations from this tag to another
	 * 
	 * @param      integer $tag_id ID of tag to copy associations to
	 * @return     boolean
	 */
	public function copyTo($tag_id)
	{
		// Get all the associations to this tag
		// Loop through the associations and link them to a different tag
		$to = new TagsObject($this->_db);
		if (!$to->copyObjects($this->get('id'), $tag_id))
		{
			$this->setError($to->getError());
			return false;
		}

		return true;
	}
}

