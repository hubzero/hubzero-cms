<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Plugins\Admin\Models;

use Component;
use Request;
use Lang;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of plugin records.
 */
class Plugins extends \JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 * @return  void
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'extension_id', 'a.extension_id',
				'name', 'a.name',
				'folder', 'a.folder',
				'element', 'a.element',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'state', 'a.state',
				'enabled', 'a.enabled',
				'access', 'a.access', 'access_level',
				'ordering', 'a.ordering',
				'client_id', 'a.client_id',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering
	 * @param   string  $direction
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Load the filter state.
		$search = Request::getState($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$accessId = Request::getState($this->context.'.filter.access', 'filter_access', null, 'int');
		$this->setState('filter.access', $accessId);

		$state = Request::getState($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		$folder = Request::getState($this->context.'.filter.folder', 'filter_folder', null, 'cmd');
		$this->setState('filter.folder', $folder);

		$language = Request::getState($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// Load the parameters.
		$params = Component::params('com_plugins');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('folder', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 * @return  string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':' . $this->getState('filter.search');
		$id	.= ':' . $this->getState('filter.access');
		$id	.= ':' . $this->getState('filter.state');
		$id	.= ':' . $this->getState('filter.folder');
		$id	.= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Returns an object list
	 *
	 * @param   string   $query       The query
	 * @param   integer  $limitstart  Offset
	 * @param   integer  $limit       The number of records
	 * @return  array
	 */
	protected function _getList($query, $limitstart=0, $limit=0)
	{
		$search   = $this->getState('filter.search');
		$ordering = $this->getState('list.ordering', 'ordering');
		if ($ordering == 'name' || (!empty($search) && stripos($search, 'id:') !== 0))
		{
			$this->_db->setQuery($query);
			$result = $this->_db->loadObjectList();

			$this->translate($result);

			if (!empty($search))
			{
				foreach ($result as $i=>$item)
				{
					if (!preg_match("/$search/i", $item->name))
					{
						unset($result[$i]);
					}
				}
			}

			$lang = Lang::getRoot();
			$direction = ($this->getState('list.direction') == 'desc') ? -1 : 1;
			\Hubzero\Utility\Arr::sortObjects($result, $ordering, $direction, true, $lang->getLocale());

			$total = count($result);
			$this->cache[$this->getStoreId('getTotal')] = $total;
			if ($total < $limitstart)
			{
				$limitstart = 0;
				$this->setState('list.start', 0);
			}
			return array_slice($result, $limitstart, $limit ? $limit : null);
		}
		else
		{
			if ($ordering == 'ordering')
			{
				$query->order('a.folder ASC');
				$ordering = 'a.ordering';
			}
			$query->order($this->_db->quoteName($ordering) . ' ' . $this->getState('list.direction'));
			if ($ordering == 'folder')
			{
				$query->order('a.ordering ASC');
			}
			$result = parent::_getList($query, $limitstart, $limit);
			$this->translate($result);
			return $result;
		}
	}

	/**
	 * Translate a list of objects
	 *
	 * @param   array  $items  The array of objects
	 * @return  array  The array of translated objects
	 */
	protected function translate(&$items)
	{
		$lang = Lang::getRoot();
		foreach ($items as &$item)
		{
			$source    = DS . 'plugins' . DS . $item->folder . DS . $item->element;
			$extension = 'plg_' . $item->folder . '_' . $item->element;

			$lang->load($extension . '.sys', JPATH_ADMINISTRATOR, null, false, true) ||
			$lang->load($extension . '.sys', PATH_APP . $source, null, false, true) ||
			$lang->load($extension . '.sys', PATH_CORE . $source, null, false, true);

			$item->name = Lang::txt($item->name);
		}
	}
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  object
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.extension_id , a.name, a.element, a.folder, a.checked_out, a.checked_out_time,' .
				' a.enabled, a.access, a.ordering'
			)
		);
		$query->from($db->quoteName('#__extensions').' AS a');

		$query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			$query->where('a.access = ' . (int) $access);
		}

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published))
		{
			$query->where('a.enabled = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.enabled IN (0, 1))');
		}

		// Filter by state
		$query->where('a.state >= 0');

		// Filter by folder.
		if ($folder = $this->getState('filter.folder'))
		{
			$query->where('a.folder = ' . $db->quote($folder));
		}

		// Filter by search in id
		$search = $this->getState('filter.search');
		if (!empty($search) && stripos($search, 'id:') === 0)
		{
			$query->where('a.extension_id = ' . (int) substr($search, 3));
		}

		return $query;
	}
}
