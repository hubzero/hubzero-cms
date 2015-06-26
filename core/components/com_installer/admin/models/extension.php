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

namespace Components\Installer\Admin\Models;

use Lang;

// Import library dependencies
jimport('joomla.application.component.modellist');

/**
 * Extension Manager Abstract Extension Model
 */
class Extension extends \JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'name',
				'client_id',
				'enabled',
				'type',
				'folder',
				'extension_id',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Returns an object list
	 *
	 * @param	string The query
	 * @param	int Offset
	 * @param	int The number of records
	 * @return	array
	 */
	protected function _getList($query, $limitstart = 0, $limit = 0)
	{
		$ordering = $this->getState('list.ordering');
		$search   = $this->getState('filter.search');
		// Replace slashes so preg_match will work
		$search = str_replace('/', ' ', $search);
		$db = $this->getDbo();

		if ($ordering == 'name' || (!empty($search) && stripos($search, 'id:') !== 0))
		{
			$db->setQuery($query);
			$result = $db->loadObjectList();
			$lang = Lang::getRoot();
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
			\Hubzero\Utility\Arr::sortObjects($result, $this->getState('list.ordering'), $this->getState('list.direction') == 'desc' ? -1 : 1, true, $lang->getLocale());
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
			$query->order($db->quoteName($ordering) . ' ' . $this->getState('list.direction'));
			$result = parent::_getList($query, $limitstart, $limit);
			$this->translate($result);
			return $result;
		}
	}

	/**
	 * Translate a list of objects
	 *
	 * @param	array The array of objects
	 * @return	array The array of translated objects
	 */
	private function translate(&$items)
	{
		$lang = Lang::getRoot();
		foreach ($items as &$item)
		{
			if (strlen($item->manifest_cache))
			{
				$data = json_decode($item->manifest_cache);
				if ($data)
				{
					foreach ($data as $key => $value)
					{
						if ($key == 'type')
						{
							// ignore the type field
							continue;
						}
						$item->$key = $value;
					}
				}
			}
			$item->author_info = @$item->authorEmail .'<br />'. @$item->authorUrl;
			$item->client = $item->client_id ? Lang::txt('JADMINISTRATOR') : Lang::txt('JSITE');

			$path = PATH_APP . DS . 'app'; //$item->client_id ? JPATH_ADMINISTRATOR : JPATH_SITE;

			switch ($item->type)
			{
				case 'component':
					$extension = $item->element;
					$source = PATH_APP . '/components/' . $extension . '/admin';

					$lang->load("$extension.sys", PATH_APP, null, false, true) ||
					$lang->load("$extension.sys", $source, null, false, true);
				break;
				case 'file':
					$extension = 'files_' . $item->element;
					$lang->load("$extension.sys", PATH_APP, null, false, true);
				break;
				case 'library':
					$extension = 'lib_' . $item->element;
					$lang->load("$extension.sys", PATH_APP, null, false, true) ||
					$lang->load("$extension.sys", PATH_CORE, null, false, true);
				break;
				case 'module':
					$extension = $item->element;
					$source = $path . '/modules/' . $extension;
						$lang->load("$extension.sys", $path, null, false, true)
					||	$lang->load("$extension.sys", $source, null, false, true);
				break;
				case 'package':
					$extension = $item->element;
					$lang->load("$extension.sys", PATH_APP, null, false, true) ||
					$lang->load("$extension.sys", PATH_CORE, null, false, true);
				break;
				case 'plugin':
					$extension = 'plg_' . $item->folder . '_' . $item->element;
					$source = PATH_CORE . '/plugins/' . $item->folder . '/' . $item->element;

					$lang->load("$extension.sys", PATH_APP, null, false, true) ||
					$lang->load("$extension.sys", $source, null, false, true);
				break;
				case 'template':
					$extension = 'tpl_' . $item->element;
					$source = $path . '/templates/' . $item->element;

					$lang->load("$extension.sys", $path, null, false, true) ||
					$lang->load("$extension.sys", $source, null, false, true);
				break;
			}

			if (!in_array($item->type, array('language', 'template', 'library')))
			{
				$item->name = Lang::txt($item->name);
			}

			settype($item->description, 'string');
			if (!in_array($item->type, array('language')))
			{
				$item->description = Lang::txt($item->description);
			}
		}
	}
}
