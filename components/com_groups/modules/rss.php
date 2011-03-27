<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

Class RssModule
{
	
	function __construct( $group )
	{
		//group object
		$this->group = $group;
		
	}
	
	//-----
	
	function onManageModules()
	{
		$mod = array();
		
	}
	
	//-----
	
	function render()
	{
		//var to hold content being returned
		$content  = '';
		
		//default items to display
		$default_items = 4;
		
		//split the content into feed and number of items
		$parts = explode(";", $this->content);
		
		//set username
		$feed_url = $parts[0];
		
		//set user defined number of tweets
		$user_items = $parts[1];
		
		//determine limit of items based on if set by user
		$num_items = ($user_items != '' && is_numeric($user_items)) ? $user_items : $default_items;
		
		
		$options = array();
		$options['rssUrl'] = $feed_url;

		$rssDoc =& JFactory::getXMLparser('RSS', $options);

		$feed = new stdclass();

		if ($rssDoc != false)
		{
			// channel header and link
			$feed->title = $rssDoc->get_title();
			$feed->link = $rssDoc->get_link();
			$feed->description = $rssDoc->get_description();

			// channel image if exists
			$feed->image->url = $rssDoc->get_image_url();
			$feed->image->title = $rssDoc->get_image_title();
			
			// items
			$feed->items = $rssDoc->get_items(0, $num_items);
		} else {
			$feed = false;
		}
		
		
		//return the content
		return $content;
	}
	
	//-----
}

?>