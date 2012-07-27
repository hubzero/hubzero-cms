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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * HTML helper class
 */
Class JHTMLhtml {

	/**
	 * Build the menu item entry for each plugin
	 * 
	 * @param  string $title - plugin title text to be displayed on the menu
	 * @param  string $name - plugin title text to be displayed on the menu
	 * @param  string $active - currently active tab
	 * @param  string $option - current component 
	 * @return string Return - list item entry for menu item
	 */
	function displayMenu($title, $name, $active, $option)
	{
		$cls = ($active == $name) ? 'active' : '';

		$link = JRoute::_('index.php?option='.$option.'&active='.$name);

		return "<li><a class=\"{$cls}\" href=\"{$link}\">{$title}</a></li>";
	}

	/**
	 * Display plugin content for currently active tab
	 * 
	 * @param  string $active_tab - currently active tab 
	 * @param  array $sections - plugin content
	 * @param  array $time_plugins - plugins available
	 * @return mixed Return - plugin HTML content
	 */
	function displayContent($active_tab, $sections, $time_plugins)
	{
		for($i=0;$i < count($time_plugins); $i++)
		{
			if($active_tab == $time_plugins[$i]['name'])
			{
				return $sections[$i]['html'];
				break;
			}
		}
	}
}
