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

require_once(__DIR__ . DS . 'entry.php');

/**
 * Blog archive model class
 */
class BlogModelArchive extends \Hubzero\Base\Object
{
	/**
	 * BlogTableEntry
	 *
	 * @var  object
	 */
	private $_tbl = null;

	/**
	 * BlogModelEntry
	 *
	 * @var  object
	 */
	private $_entry = null;

	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var  object
	 */
	private $_entries = null;

	/**
	 * JDatabase
	 *
	 * @var  object
	 */
	private $_db = NULL;

	/**
	 * JRegistry
	 *
	 * @var  object
	 */
	private $_config;

	/**
	 * File space path
	 *
	 * @var  string
	 */
	private $_path;

	/**
	 * Constructor
	 *
	 * @param   string   $scope     Blog scope [site, group, member]
	 * @param   integer  $scope_id  Scope ID if scope is member or group
	 * @return  void
	 */
	public function __construct($scope='site', $scope_id=0)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new BlogTableEntry($this->_db);

		$this->set('scope', $scope);
		$this->set('scope_id', $scope_id);
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
	}

	/**
	 * Returns a reference to a blog archive model
	 *
	 * @param   string   $scope     Blog scope [site, group, member]
	 * @param   integer  $scope_id  Scope ID if scope is member or group
	 * @return  object   BlogModelArchive
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
			$instances[$key] = new static($scope, $scope_id);
		}

		return $instances[$key];
	}

	/**
	 * Get a specific entry
	 *
	 * @param   mixed   $id  String (alias) or integer (ID) of an entry
	 * @return  object
	 */
	public function entry($id=null)
	{
		if (!isset($this->_entry)
		 || ($id !== null && (int) $this->_entry->get('id') != $id && (string) $this->_entry->get('alias') != $id))
		{
			$this->_entry = BlogModelEntry::getInstance($id, $this->get('scope'), $this->get('scope_id'));
		}
		return $this->_entry;
	}

	/**
	 * Get a count of, model for, or list of entries
	 *
	 * @param   string   $rtrn     Data to return
	 * @param   array    $filters  Filters to apply to data retrieval
	 * @param   boolean  $reset    Clear cached data?
	 * @return  mixed
	 */
	public function entries($rtrn='list', $filters=array(), $reset=false)
	{
		if (!isset($filters['scope']))
		{
			$filters['scope'] = (string) $this->get('scope');
		}
		if (!isset($filters['group_id']))
		{
			$filters['group_id'] = (int) $this->get('group_id');
		}

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
				$filters['limit']    = 1;
				$filters['start']    = 0;
				$filters['sort']     = 'publish_up';
				$filters['sort_Dir'] = 'ASC';
				$filters['order']    = $filters['sort'] . ' ' . $filters['sort_Dir'];

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

				return new \Hubzero\Base\ItemList($results);
			break;
		}
		return null;
	}

	/**
	 * Get file upload upload path
	 *
	 * @return  string
	 */
	public function filespace()
	{
		if (!isset($this->_path))
		{
			$this->_path = JPATH_ROOT;

			switch ($this->get('scope'))
			{
				case 'member':
					jimport('joomla.plugin.plugin');
					$plugin = JPluginHelper::getPlugin('members', 'blog');
					$params = new JRegistry($plugin->params);
					$p = $params->get('uploadpath');
					$p = str_replace('{{uid}}', \Hubzero\Utility\String::pad($this->get('scope_id')), $p);
				break;

				case 'group':
					$uploadpath = JComponentHelper::getParams('com_groups')->get('uploadpath', '/site/groups');
					$p = rtrim($uploadpath, DS) . DS . $this->get('scope_id') . DS . 'uploads' . DS  . 'blog';
				break;

				case 'site':
					$p = $this->config('uploadpath', '/site/blog');
				break;
			}
			$this->_path .= DS . trim($p, DS);
		}

		return $this->_path;
	}

	/**
	 * Get a parameter from the component config
	 *
	 * @param   string  $property  Param to return
	 * @param   mixed   $default   Value to return if property not found
	 * @return  object  JRegistry
	 */
	public function config($property=null, $default=null)
	{
		if (!isset($this->_config))
		{
			$this->_config = JComponentHelper::getParams('com_blog');
		}
		if ($property)
		{
			return $this->_config->get($property, $default);
		}
		return $this->_config;
	}

	/**
	 * Check a user's authorization
	 *
	 * @param   string   $action  Action to check
	 * @param   string   $item    Item type to check action against
	 * @return  boolean  True if authorized, false if not
	 */
	public function access($action='view', $item='entry')
	{
		if (!$this->config()->get('access-check-done', false))
		{
			$juser = JFactory::getUser();
			if ($juser->get('guest'))
			{
				$this->config()->set('access-check-done', true);
			}
			else
			{
				$this->config()->set('access-admin-entry', $juser->authorise('core.admin', $this->get('id')));
				$this->config()->set('access-manage-entry', $juser->authorise('core.manage', $this->get('id')));
				$this->config()->set('access-delete-entry', $juser->authorise('core.manage', $this->get('id')));
				$this->config()->set('access-edit-entry', $juser->authorise('core.manage', $this->get('id')));
				$this->config()->set('access-edit-state-entry', $juser->authorise('core.manage', $this->get('id')));
				$this->config()->set('access-edit-own-entry', $juser->authorise('core.manage', $this->get('id')));

				$this->config()->set('access-check-done', true);
			}
		}
		return $this->config()->get('access-' . strtolower($action) . '-entry');
	}
}
