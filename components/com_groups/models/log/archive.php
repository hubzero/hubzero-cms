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
require_once JPATH_COMPONENT_SITE . DS . 'models' . DS . 'log.php';

/**
 * Group log archive model class
 */
class GroupsModelLogArchive extends \Hubzero\Base\Model
{
	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var object
	 */
	private $_logs = null;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		// create database object
		$this->_db = JFactory::getDBO();
	}

	/**
	 * Get Instance of Page Archive
	 *
	 * @param   string $key Instance Key
	 * @return  object GroupsModelLogArchive
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
	 * Get a list of logs
	 *
	 * @param   string  $rtrn    What data to return
	 * @param   array   $filters Filters to apply to data retrieval
	 * @param   boolean $boolean Clear cached data?
	 * @return  object
	 */
	public function logs($rtrn = 'list', $filters = array(), $clear = false)
	{
		switch (strtolower($rtrn))
		{
			case 'list':
			default:
				if (!($this->_logs instanceof \Hubzero\Base\Model\ItemList) || $clear)
				{
					$tbl = new GroupsTableLog($this->_db);
					if ($results = $tbl->find( $filters ))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new GroupsModelLog($result);
						}
					}
					$this->_logs = new \Hubzero\Base\Model\ItemList($results);
				}
				return $this->_logs;
			break;
		}
	}
}