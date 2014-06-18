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

/**
 * Short description for 'WhatsnewPeriod'
 *
 * Long description (if any) ...
 */
class WhatsnewPeriod extends JObject
{

	/**
	 * The original search text - should NEVER BE CHANGED
	 *
	 * @var string
	 */
	private $_period = NULL;

	/**
	 * Container for storing overloaded data
	 *
	 * @var array
	 */
	private $_data   = array();

	/**
	 * Constructor
	 *
	 * @param      string $period Time period (month, week, etc)
	 * @return     void
	 */
	public function __construct($period=NULL)
	{
		$this->_period = $period;
	}

	/**
	 * Method to set an overloaded variable to the component
	 *
	 * @param	string	$property	Name of overloaded variable to add
	 * @param	mixed	$value 		Value of the overloaded variable
	 * @return	void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Method to get an overloaded variable of the component
	 *
	 * @param	string	$property	Name of overloaded variable to retrieve
	 * @return	mixed 	Value of the overloaded variable
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property]))
		{
			return $this->_data[$property];
		}
	}

	/**
	 * Processes the _period text into actual dates
	 *
	 * @return     void
	 */
	public function process()
	{
		if (trim($this->_period) == '')
		{
			return;
		}

		$this->period = $this->_period;

		// Determine last week and last month date
		//$today = time();
		switch ($this->period)
		{
			case 'week':
				$this->endTime   = JFactory::getDate('now')->toSql(); //$today;
				$this->startTime = JFactory::getDate('-1 week')->toSql(); //$this->endTime - (7*24*60*60);
			break;

			case 'month':
				$this->endTime   = JFactory::getDate('now')->toSql(); //$today;
				$this->startTime = JFactory::getDate('-1 month')->toSql(); //$this->endTime - (31*24*60*60);
			break;

			case 'quarter':
				$this->endTime   = JFactory::getDate('now')->toSql(); //$today;
				$this->startTime = JFactory::getDate('-3 months')->toSql(); //$this->endTime - (3*31*24*60*60);
			break;

			case 'year':
				$this->endTime   = JFactory::getDate('now')->toSql(); //$today;
				$this->startTime = JFactory::getDate('-1 year')->toSql(); //$this->endTime - (365*24*60*60);
			break;

			default:
				if (substr($this->period, 0, 2) == 'c_')
				{
					$tokens = preg_split('/_/', $this->period);
					$this->period = $tokens[1];

					$this->endTime   = strtotime('12/31/' . $this->period);
					$this->startTime = strtotime('01/01/' . $this->period);
				}
				else
				{
					$this->endTime   = strtotime('08/31/' . $this->period);
					$this->startTime = strtotime('09/01/' . ($this->period-1));
				}
				$this->endTime   = date("Y-m-d H:i:s", $this->endTime);
				$this->startTime = date("Y-m-d H:i:s", $this->startTime);
			break;
		}

		$this->cStartDate = $this->startTime; //date("Y-m-d H:i:s", $this->startTime);
		$this->dStartDate = substr($this->startTime, 0, strlen('0000-00-00')); //date("Y-m-d", $this->startTime);
		$this->cEndDate   = $this->endTime; //date("Y-m-d H:i:s", $this->endTime);
		$this->dEndDate   = substr($this->endTime, 0, strlen('0000-00-00')); //date("Y-m-d", $this->endTime);
	}
}

