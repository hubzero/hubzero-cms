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

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('joomla.event.plugin');

class plgSystemXFeed extends JPlugin
{
	function plgSystemXFeed(& $subject) 
	{
		parent::__construct($subject, NULL);
	}

	function onAfterInitialise()
	{
		$uri = $_SERVER['REQUEST_URI'];
		$bits = explode('?', $uri);
		$bit = $bits[0];
		$bi = explode('.',$bit);
		$b = end($bi);
		if ($b == strtolower('rss') || $b == strtolower('atom')) {
			$_GET['no_html'] = 1;
			$_REQUEST['no_html'] = 1;
		}
	}
}
