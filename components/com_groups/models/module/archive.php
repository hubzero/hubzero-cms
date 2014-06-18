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
require_once JPATH_ROOT . DS . 'components' . DS . 'com_groups' . DS . 'models' . DS . 'module.php';
require_once JPATH_ROOT . DS . 'components' . DS . 'com_groups' . DS . 'models' . DS . 'module' . DS . 'menu.php';

class GroupsModelModuleArchive extends JObject
{
	/**
	 * \Hubzero\Base\Model
	 *
	 * @var object
	 */
	private $_modules = null;

	/**
	 * Modules count
	 *
	 * @var integer
	 */
	private $_modules_count = null;

	/**
	 * JDatabase
	 *
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * JRegistry
	 *
	 * @var object
	 */
	private $_config;


	/**
	 * Constructor
	 *
	 * @param      integer $id Course ID or alias
	 * @return     void
	 */
	public function __construct()
	{
		$this->_db = JFactory::getDBO();
	}

	/**
	 * Get Instance of Module Archive
	 *
	 * @param   $key   Instance Key
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
			$instances[$key] = new GroupsModelModuleArchive();
		}

		return $instances[$key];
	}

	/**
	 * Get a list of group modules
	 *
	 * @param      string  $rtrn    What data to return
	 * @param      array   $filters Filters to apply to data retrieval
	 * @param      boolean $boolean Clear cached data?
	 * @return     mixed
	 */
	public function modules( $rtrn = 'list', $filters = array(), $clear = false )
	{

		switch (strtolower($rtrn))
		{
			case 'unapproved':
				$unapproved = array();
				if($results = $this->modules('list', $filters, true))
				{
					foreach ($results as $k => $result)
					{
						// if module is unapproved return it
						if ($result->get('approved') == 0)
						{
							$unapproved[] = $result;
						}
					}
				}
				return new \Hubzero\Base\Model\ItemList($unapproved);
			break;
			case 'list':
			default:
				$tbl = new GroupsTableModule($this->_db);
				if ($results = $tbl->find( $filters ))
				{
					foreach ($results as $key => $result)
					{
						$results[$key] = new GroupsModelModule($result);
					}
				}
				$this->_modules = new \Hubzero\Base\Model\ItemList($results);
				return $this->_modules;
			break;
		}
	}

}