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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'models' . DS . 'entry.php');

/**
 * Blog model class
 */
class BlogModel extends JObject
{
	/**
	 * BlogTableEntry
	 * 
	 * @var object
	 */
	private $_tbl = null;

	/**
	 * ForumModelCategory
	 * 
	 * @var object
	 */
	private $_entry = null;

	/**
	 * ForumModelCategory
	 * 
	 * @var object
	 */
	private $_entries = null;

	/**
	 * Flag for if authorization checks have been run
	 * 
	 * @var mixed
	 */
	private $_authorized = false;

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
	private $_config;

	/**
	 * Serialized string of filters
	 * 
	 * @var string
	 */
	private $_filters;

	/**
	 * Constructor
	 * 
	 * @param      integer $id Course ID or alias
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
			$instances[$key] = new BlogModel($scope, $scope_id);
		}

		return $instances[$key];
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @access	public
	 * @param	string $property The name of the property
	 * @param	mixed  $default The default value
	 * @return	mixed The value of the property
	 * @see		getProperties()
	 * @since	1.5
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
	 * @access	public
	 * @param	string $property The name of the property
	 * @param	mixed  $value The value of the property to set
	 * @return	mixed Previous value of the property
	 * @see		setProperties()
	 * @since	1.5
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
			if (isset($this->_entries) && is_a($this->_entries, 'BlogModelIterator'))
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
				/*if (!isset($this->_entries['count']) || !is_numeric($this->_entries['count']))
				{
					$this->_entries['count'] = (int) $this->_tbl->count($filters);
				}*/
				return (int) $this->_tbl->getCount($filters); //$this->_entries['count'];
			break;

			case 'first':
				$filters['limit'] = 1;
				$filters['start'] = 0;
				$filters['sort'] = 'publish_up';
				$filters['sort_Dir'] = 'ASC';
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
				return new BlogModelIterator($results);
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
				return new BlogModelIterator($results);
			break;

			case 'list':
			case 'results':
			default:
				if (!isset($this->_entries) || !is_a($this->_entries, 'BlogModelIterator'))
				{
					if ($results = $this->_tbl->getRecords($filters))
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
					//$this->_entries = new BlogModelIterator($results);
				}
				return new BlogModelIterator($results);
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
		if (!$this->_authorized)
		{
			$juser = JFactory::getUser();
			if ($juser->get('guest'))
			{
				$this->_authorized = true;
			}
			else
			{
				// Anyone logged in can create a forum
				//$this->params->set('access-create-entry', true);

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

				$this->_authorized = true;
			}
		}
		return $this->params->get('access-' . strtolower($action) . '-entry');
	}
}
