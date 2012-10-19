<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2011 Purdue University. All rights reserved.
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
 * @author    Chris Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2009-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class Hubzero_Whatsnew
{
	public static function getWhatsNewBasedOnPeriodAndCategory( $period = 'month', $category = '', $limit = 0 )
	{
		//parse the time period for use by the whats new plugins
		$p = Hubzero_Whatsnew::parseTimePeriod( $period );
		
		//load whats new plugins
		JPluginHelper::importPlugin('whatsnew');
		$dispatcher =& JDispatcher::getInstance();
		
		//get the search areas
		$areas = array();
		$search_areas = $dispatcher->trigger('onWhatsNewAreas');
		foreach($search_areas as $search_area)
		{
			$areas = array_merge($areas, $search_area);
		}
		
		//get the results
		$config = array($p, $limit, 0, $areas);
		$results = $dispatcher->trigger( 'onWhatsNew', $config );
		
		$new = array();
		$i = 0;
		foreach($areas as $k => $area)
		{
			$new[$i]['alias'] = $k;
			$new[$i]['title'] = ($k == 'resources') ? 'Resources' : $area;
			$new[$i]['results'] = $results[$i];
			$i++;
		}
		
		//check to see if we only want to return results for a certain category
		if($category != '')
		{
			$index = 0;
			foreach($areas as $k => $area)
			{
				if($category == $k)
				{
					return $new[$index];
				}
				$index++;
			}
		}
		
		return $new;
	}
	
	//-----
	
	public static function parseTimePeriod( $period )
	{
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_whatsnew' . DS . 'helpers' . DS . 'period.php'); 
		$p = new WhatsnewPeriod($period);
		$p->process();
		return $p;
	}
}