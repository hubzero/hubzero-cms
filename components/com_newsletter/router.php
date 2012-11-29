<?php
/**
 * @package		HUBzero CMS
 * @author		Christopher Smoak <csmoak@purdue.edu>
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

function NewsletterBuildRoute(&$query)
{
	$segments = array();
    
	if (!empty($query['id'])) 
	{   
		$database = JFactory::getDBO();
		$sql = "SELECT `name` FROM #__newsletter_campaign WHERE `id`=".$query['id'];
		$database->setQuery($sql);
		$campaign = $database->loadResult();
		$segments[] = strtolower(str_replace(" ", "", $campaign));
		unset($query['id']);
	}
	
	return $segments;
}

function NewsletterParseRoute($segments)
{
	$vars = array();
	
	if (empty($segments))
	{	
    	return $vars;
	}
	
	if (isset($segments[0])) 
	{
		$database = JFactory::getDBO();
		$sql = "SELECT `id`,`name` FROM #__newsletter_campaign";
		$database->setQuery($sql);
		$campaigns = $database->loadObjectList();

		foreach($campaigns as $c)
		{
			if(strtolower(str_replace(" ", "", $c->name)) == $segments[0])
			{
				$vars['id'] = $c->id;
			}
		}
	}

	return $vars;
}

?>
