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

// include calendar model
require_once JPATH_ROOT . DS . 'components' . DS . 'com_events' . DS . 'models' . DS . 'calendar.php';

class EventsModelCalendarArchive extends \Hubzero\Base\Model
{
	/**
	 * \Hubzero\Base\ItemList
	 * 
	 * @var object
	 */
	private $_calendars = null;

	/**
	 * Constructor
	 * 
	 * @param      mixed     Object Id
	 * @return     void
	 */
	public function __construct()
	{
		// create needed objects
		$this->_db = JFactory::getDBO();
	}

	/**
	 * Get Instance this Model
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
			$instances[$key] = new self();
		}
		
		return $instances[$key];
	}

	/**
	 * Get a list of event calendars
	 *
	 * @param      string  $rtrn    What data to return
	 * @param      array   $filters Filters to apply to data retrieval
	 * @param      boolean $boolean Clear cached data?
	 * @return     mixed
	 */
	public function calendars( $rtrn = 'list', $filters = array(), $clear = false )
	{
		switch (strtolower($rtrn))
		{
			case 'list':
			default:
				if (!($this->_calendars instanceof \Hubzero\Base\Model\ItemList) || $clear)
				{
					$tbl = new EventsCalendar($this->_db);
					if ($results = $tbl->find( $filters ))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new EventsModelCalendar($result);
						}
					}
					$this->_calendars = new \Hubzero\Base\Model\ItemList($results);
				}
				return $this->_calendars;
			break;
		}
	}
}