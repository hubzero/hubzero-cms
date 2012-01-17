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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'WhatsnewPeriod'
 * 
 * Long description (if any) ...
 */
class WhatsnewPeriod extends JObject
{

	/**
	 * Description for '_period'
	 * 
	 * @var unknown
	 */
	private $_period = NULL;     // The original search text - should NEVER BE CHANGED

	/**
	 * Description for '_data'
	 * 
	 * @var array
	 */
	private $_data   = array();  // Processed text

	//-----------

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $period Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( $period=NULL )
	{
		$this->_period = $period;
	}

	/**
	 * Short description for '__set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Short description for '__get'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}

	/**
	 * Short description for 'process'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function process()
	{
		if (trim($this->_period) == '') {
			return;
		}

		$period = $this->_period;

		// Determine last week and last month date
		$today = time();
		switch ($period)
		{
			case 'week':
				$endTime   = $today;
				$startTime = $endTime - (7*24*60*60);
				break;
			case 'month':
				$endTime   = $today;
				$startTime = $endTime - (31*24*60*60);
				break;
			case 'quarter':
				$endTime   = $today;
				$startTime = $endTime - (3*31*24*60*60);
				break;
			case 'year':
				$endTime   = $today;
				$startTime = $endTime - (365*24*60*60);
				break;
			default:
				if (substr($period, 0, 2) == 'c_') {
					$tokens = preg_split('#_#',$period);
					$period = $tokens[1];
					$endTime   = strtotime('12/31/'.$period);
					$startTime = strtotime('01/01/'.$period);
				} else {
					$endTime   = strtotime('08/31/'.$period);
					$startTime = strtotime('09/01/'.($period-1));
				}
				break;
		}

		$this->period    = $period;
		$this->endTime   = $endTime;
		$this->startTime = $startTime;
		$this->cStartDate = date("Y-m-d H:i:s", $startTime);
		$this->dStartDate = date("Y-m-d", $startTime);
		$this->cEndDate   = date("Y-m-d H:i:s", $endTime);
		$this->dEndDate   = date("Y-m-d", $endTime);
	}
}

