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

require_once(__DIR__ . '/entry.php');

/**
 * Blog model class
 */
class BlogModel extends \Hubzero\Base\Object
{
	/**
	 * BlogTableEntry
	 * 
	 * @var object
	 */
	private $_tbl = null;

	/**
	 * BlogModelEntry
	 * 
	 * @var object
	 */
	private $_entry = null;

	/**
	 * \Hubzero\Base\ItemList
	 * 
	 * @var object
	 */
	private $_entries = null;

	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * JRegistry
	 * 
	 * @var array
	 */
	private $_config;

	/**
	 * Constructor
	 * 
	 * @param      string  $scope    Blog scope [site, group, member]
	 * @param      integer $scope_id Scope ID if scope is member or group
	 * @return     void
	 */
	public function __construct($scope='site', $scope_id=0)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new BlogTableEntry($this->_db);

		$this->set('scope', $scope);
		switch ($scope)
		{
			case 'group':
				$this->set('group_id', $scope_id);
			break;
			case 'member':
				$this->set('group_id', 0);
				$this->set('created_by', $scope_id);
			break;
			case 'site':
			default:
				$this->set('group_id', 0);
			break;
		}

		$this->_config = JComponentHelper::getParams('com_blog');
	}

	/**
	 * Returns a reference to a blog model
	 *
	 * @param      string  $scope    Blog scope [site, group, member]
	 * @param      integer $scope_id Scope ID if scope is member or group
	 * @return     object BlogModel
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
			$instances[$key] = new self($scope, $scope_id);
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
		if (isset($this->_tbl->$property)) 
		{
			return $this->_tbl->$property;
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
		$previous = isset($this->_tbl->$property) ? $this->_tbl->$property : null;
		$this->_tbl->$property = $value;
		return $previous;
	}

	/**
	 * Set and get a specific offering
	 * 
	 * @return     void
	 */
	public function entry($id=null)
	{
		if (!isset($this->_entry) 
		 || ($id !== null && (int) $this->_entry->get('id') != $id && (string) $this->_entry->get('alias') != $id))
		{
			/*$this->_entry = null;
			if (isset($this->_entries) && ($this->_entries instanceof \Hubzero\Base\ItemList))
			{
				foreach ($this->_entries as $key => $entry)
				{
					if ((int) $entry->get('id') == $id || (string) $entry->get('alias') == $id)
					{
						$this->_entry = $entry;
						break;
					}
				}
			}
			else
			{*/
				$this->_entry = BlogModelEntry::getInstance($id, $this->get('scope'), ($this->get('scope') == 'member' ? $this->get('created_by') : $this->get('group_id')));
			//}
		}
		return $this->_entry;
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
	public function entries($rtrn='list', $filters=array())
	{
		if (!isset($filters['scope']))
		{
			$filters['scope'] = (string) $this->get('scope');
		}
		if (!isset($filters['group_id']))
		{
			$filters['group_id'] = (int) $this->get('group_id');
		}

		//$this->_filters = serialize($filters);
		switch (strtolower($rtrn))
		{
			case 'count':
				if (JFactory::getApplication()->getName() == 'administrator')
				{
					return (int) $this->_tbl->getEntriesCount($filters);
				}
				else
				{
					return (int) $this->_tbl->getCount($filters);
				}
			break;

			case 'first':
				$filters['limit'] = 1;
				$filters['start'] = 0;
				$filters['sort'] = 'publish_up';
				$filters['sort_Dir'] = 'ASC';
				$filters['order'] = $filters['sort'] . ' ' . $filters['sort_Dir'];
				$results = $this->_tbl->getRecords($filters);
				$res = isset($results[0]) ? $results[0] : null;
				return new BlogModelEntry($res);
			break;

			case 'popular':
				if ($results = $this->_tbl->getPopularEntries($filters))
				{
					foreach ($results as $key => $result)
					{
						$results[$key] = new BlogModelEntry($result);
					}
				}
				else
				{
					$results = array();
				}
				return new \Hubzero\Base\ItemList($results);
			break;

			case 'recent':
				if ($results = $this->_tbl->getRecentEntries($filters))
				{
					foreach ($results as $key => $result)
					{
						$results[$key] = new BlogModelEntry($result);
					}
				}
				else
				{
					$results = array();
				}
				return new \Hubzero\Base\ItemList($results);
			break;

			case 'list':
			case 'results':
			default:
				if (JFactory::getApplication()->getName() == 'administrator')
				{
					$results = $this->_tbl->getEntries($filters);
				}
				else
				{
					$results = $this->_tbl->getRecords($filters);
				}

				if ($results)
				{
					foreach ($results as $key => $result)
					{
						$results[$key] = new BlogModelEntry($result);
					}
				}
				else
				{
					$results = array();
				}
				return new \Hubzero\Base\ItemList($results);
			break;
		}
		return null;
	}

	/**
	 * Check a user's authorization
	 * 
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function access($action='view')
	{
		if (!$this->params->get('access-check-done', false))
		{
			$juser = JFactory::getUser();
			if ($juser->get('guest'))
			{
				$this->params->set('access-check-done', true);
			}
			else
			{
				// Check if they're a site admin
				if (version_compare(JVERSION, '1.6', 'lt'))
				{
					if ($juser->authorize('com_blog', 'manage')) 
					{
						$this->params->set('access-admin-entry', true);
						$this->params->set('access-manage-entry', true);
						$this->params->set('access-delete-entry', true);
						$this->params->set('access-edit-entry', true);
						$this->params->set('access-edit-state-entry', true);
						$this->params->set('access-edit-own-entry', true);
					}
				}
				else 
				{
					$this->params->set('access-admin-entry', $juser->authorise('core.admin', $this->get('id')));
					$this->params->set('access-manage-entry', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-delete-entry', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-edit-entry', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-edit-state-entry', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-edit-own-entry', $juser->authorise('core.manage', $this->get('id')));
				}

				$this->params->set('access-check-done', true);
			}
		}
		return $this->params->get('access-' . strtolower($action) . '-entry');
	}
}
