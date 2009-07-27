<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------

class WhatsnewPeriod extends JObject
{
	private $_period = NULL;     // The original search text - should NEVER BE CHANGED
	private $_data   = array();  // Processed text
	
	//-----------
	
	public function __construct( $period=NULL )
	{		
		$this->_period = $period;
	}

	//-----------
	
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}

	//-----------
	
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
					$tokens = split('_',$period);
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
?>