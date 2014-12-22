<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'tables' . DS . 'tag.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'models' . DS . 'log.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'models' . DS . 'object.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'models' . DS . 'substitute.php');

/**
 * Model class for a tag
 */
class TagsModelTag extends \Hubzero\Base\Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'TagsTableTag';

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
	 * \Hubzero\User\Profile
	 *
	 * @var object
	 */
	protected $_creator = NULL;

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
		$this->_tbl = new TagsTableTag($this->_db);

		// Load record
		if (is_string($oid))
		{
			$this->_tbl->loadTag($oid);
		}
		else if (is_numeric($oid))
		{
			$this->_tbl->load($oid);
		}
		else if (is_object($oid) || is_array($oid))
		{
			$this->bind($oid);
		}

		// Set the base path to this tag
		$this->_base = 'index.php?option=com_tags&tag=' . $this->get('tag');
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
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire object
	 *
	 * @param      string $property Property to retrieve
	 * @param      mixed  $default  Default value if property not set
	 * @return     mixed
	 */
	public function creator($property=null, $default=null)
	{
		if (!($this->_creator instanceof \Hubzero\User\Profile))
		{
			$this->_creator = \Hubzero\User\Profile::getInstance($this->get('created_by'));
			if (!$this->_creator)
			{
				$this->_creator = new \Hubzero\User\Profile();
			}
		}
		if ($property)
		{
			$property = ($property == 'id' ? 'uidNumber' : $property);
			return $this->_creator->get($property, $default);
		}
		return $this->_creator;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function created($as='')
	{
		return $this->_datetime($as, 'created');
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function modified($as='')
	{
		if (!$this->get('modified') || $this->get('modified') == '0000-00-00 00:00:00')
		{
			$this->set('modified', $this->get('created'));
		}
		return $this->_datetime($as, 'modified');
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	private function _datetime($as='', $key='created')
	{
		switch (strtolower($as))
		{
			case 'date':
				return JHTML::_('date', $this->get($key), JText::_('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return JHTML::_('date', $this->get($key), JText::_('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get($key);
			break;
		}
	}

	/**
	 * Determine if record was modified
	 *
	 * @return     boolean True if modified, false if not
	 */
	public function wasModified()
	{
		if ($this->get('modified') && $this->get('modified') != '0000-00-00 00:00:00')
		{
			return true;
		}
		return false;
	}

	/**
	 * Store changes to this tag
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		if (!parent::store($check))
		{
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
	 * Store changes to this record
	 *
	 * @return    boolean False if error, True on success
	 */
	public function delete()
	{
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
		foreach ($this->substitutes('list', array('limit' => 0)) as $substitute)
		{
			if (!$substitute->delete())
			{
				$this->setError($substitute->getError());
				return false;
			}
		}

		return parent::delete();
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param      string $type The type of link to return
	 * @return     string
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
					$tbl = new TagsTableSubstitute($this->_db);
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
				if (!isset($this->_cache['subs']) || !($this->_cache['subs'] instanceof \Hubzero\Base\ItemList) || $clear)
				{
					$results = array();

					$tbl = new TagsTableSubstitute($this->_db);
					if ($res = $tbl->getRecords($filters['tag_id'], $filters['start'], $filters['limit']))
					{
						foreach ($res as $key => $result)
						{
							$results[] = new TagsModelSubstitute($result);
						}
					}

					$this->_cache['subs'] = new \Hubzero\Base\ItemList($results);
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
		if (isset($filters['tag_id']))
		{
			$filters['tagid'] = $filters['tag_id'];
		}
		if (!isset($filters['tagid']))
		{
			$filters['tagid'] = (int) $this->get('id');
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
					$tbl = new TagsTableObject($this->_db);
					$this->_cache['objects_count'] = (int) $tbl->count($filters);
				}
				return $this->_cache['objects_count'];
			break;

			case 'list':
			case 'results':
			default:
				if (!isset($this->_cache['objects']) || !($this->_cache['objects'] instanceof \Hubzero\Base\ItemList) || $clear)
				{
					$tbl = new TagsTableObject($this->_db);
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
					$this->_cache['objects'] = new \Hubzero\Base\ItemList($results);
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
					$tbl = new TagsTableLog($this->_db);
					$this->_cache['logs_count'] = (int) $tbl->count($filters);
				}
				return $this->_cache['logs_count'];
			break;

			case 'list':
			case 'results':
			default:
				if (!isset($this->_cache['logs']) || !($this->_cache['logs'] instanceof \Hubzero\Base\ItemList) || $clear)
				{
					$tbl = new TagsTableLog($this->_db);
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
					$this->_cache['logs'] = new \Hubzero\Base\ItemList($results);
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
	 * @param      integer $tagger   User ID of person to filter tag by
	 * @return     boolean
	 */
	public function removeFrom($scope, $scope_id, $tagger=0)
	{
		// Check if the relationship exists
		$to = new TagsModelObject($scope, $scope_id, $this->get('id'), $tagger);
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
	 * @param      integer $tagger   User ID of person adding tag
	 * @param      integer $strength Tag strength
	 * @param      string  $label    Label to apply
	 * @return     boolean
	 */
	public function addTo($scope, $scope_id, $tagger=0, $strength=1, $label='')
	{
		// Check if the relationship already exists
		$to = new TagsModelObject($scope, $scope_id, $this->get('id'), $tagger);
		if ($to->exists())
		{
			return true;
		}

		// Set some data
		$to->set('tbl', (string) $scope);
		$to->set('objectid', (int) $scope_id);
		$to->set('tagid', (int) $this->get('id'));
		$to->set('strength', (int) $strength);
		if ($label)
		{
			$to->set('label', (string) $label);
		}
		if ($tagger)
		{
			$to->set('taggerid', $tagger);
		}

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
		if (!$tag_id)
		{
			$this->setError(JText::_('Missing tag ID.'));
			return false;
		}

		// Get all the associations to this tag
		// Loop through the associations and link them to a different tag
		$to = new TagsTableObject($this->_db);
		if (!$to->moveObjects($this->get('id'), $tag_id))
		{
			$this->setError($to->getError());
			return false;
		}

		// Get all the substitutions to this tag
		// Loop through the records and link them to a different tag
		$ts = new TagsTableSubstitute($this->_db);
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
		if (!$tag_id)
		{
			$this->setError(JText::_('Missing tag ID.'));
			return false;
		}

		// Get all the associations to this tag
		// Loop through the associations and link them to a different tag
		$to = new TagsTableObject($this->_db);
		if (!$to->copyObjects($this->get('id'), $tag_id))
		{
			$this->setError($to->getError());
			return false;
		}

		return true;
	}
}

