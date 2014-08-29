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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// include needed models
require_once JPATH_ROOT . DS . 'components' . DS . 'com_groups' . DS . 'models' . DS . 'page.php';
require_once JPATH_ROOT . DS . 'components' . DS . 'com_groups' . DS . 'models' . DS . 'page' . DS . 'category' . DS . 'archive.php';

/**
 * Group page archive model class
 */
class GroupsModelPageArchive extends JObject
{
	/**
	 * \Hubzero\Base\Model
	 *
	 * @var object
	 */
	private $_pages = null;

	/**
	 * Page count
	 *
	 * @var integer
	 */
	private $_pages_count = null;

	/**
	 * JDatabase
	 *
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * \Hubzero\User\Group
	 *
	 * @var object
	 */
	private $_group = NULL;

	/**
	 * Constructor
	 *
	 * @return     void
	 */
	public function __construct()
	{
		$this->_db    = JFactory::getDBO();
		$this->_group = \Hubzero\User\Group::getInstance(JRequest::getVar('cn', ''));
	}

	/**
	 * Get Instance of Page Archive
	 *
	 * @param   string $key Instance Key
	 * @return  object GroupsModelPageArchive
	 */
	static function &getInstance($key=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self();
		}

		return $instances[$key];
	}

	/**
	 * Get a list of group pages
	 *
	 * @param      string  $rtrn    What data to return
	 * @param      array   $filters Filters to apply to data retrieval
	 * @param      boolean $boolean Clear cached data?
	 * @return     mixed
	 */
	public function pages($rtrn = 'list', $filters = array(), $clear = false)
	{
		switch (strtolower($rtrn))
		{
			case 'alias':
				$aliases = array();
				if ($results = $this->pages('list', $filters, true))
				{
					foreach ($results as $result)
					{
						$aliases[] = $result->get('alias');
					}
				}
				return $aliases;
			break;
			case 'unapproved':
				$unapproved = array();
				if ($results = $this->pages('list', $filters, true))
				{
					foreach ($results as $k => $result)
					{
						// get current version
						$version = $result->versions()->first();

						// if current version is unapproved return it
						if ($version->get('approved') == 0)
						{
							$unapproved[] = $result;
						}
					}
				}
				return new \Hubzero\Base\Model\ItemList($unapproved);
			break;
			case 'list':
			default:
				if (!($this->_pages instanceof \Hubzero\Base\Model\ItemList) || $clear)
				{
					$tbl = new GroupsTablePage($this->_db);
					if ($results = $tbl->find($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new GroupsModelPage($result);
						}
					}
					$this->_pages = new \Hubzero\Base\Model\ItemList($results);
				}
				return $this->_pages;
			break;
		}
	}

	/**
	 * Reset all pages with new value for key
	 *
	 * @param      string  $key         Page key to reset
	 * @param      string  $value       New value to set
	 * @param      array   $filters     Filters passed to pages() method
	 * @return     mixed
	 */
	public function reset($key = 'home', $value = 0, $filters = array())
	{
		// get list of pages
		$pages = $this->pages('list', $filters);

		// reset each page
		foreach ($pages as $page)
		{
			$page->set($key, $value);
			$page->store();
		}
	}
}