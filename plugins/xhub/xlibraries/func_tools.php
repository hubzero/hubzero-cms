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

//-------------------------------------------------------------

class SessionUtils
{
	//-----------
	// Get a list of existing application sessions.

	function getSessions($administrator=0)
	{
		global $my;
		$xhub =& XFactory::getHub();

		$sesslist = array();
		$myappnum = array();
		$factory =& XFactory::getComponentFactory('mw');
		$mwdb =& $factory->getDBO();
		$tbl = $xhub->getCfg('mwDBDatabase');
		if (!$mwdb) {
			die('Could not connect: ' . mysql_error());
		}
	
		if ($administrator == 1) {
			$query = "SELECT * FROM $tbl.session ORDER BY $tbl.session.start";
		} else {
			$query = "SELECT * FROM $tbl.viewperm JOIN $tbl.session
			  ON $tbl.viewperm.sessnum = $tbl.session.sessnum
			  WHERE $tbl.viewperm.viewuser='".$my->username."'
			  ORDER BY $tbl.session.start";
		}
		$mwdb->setQuery($query);
		$rows = $mwdb->loadObjectList();

		$i = 0;
		if (!empty($rows))
		foreach($rows as $row) 
		{
			$name = $row->appname;
			/*$tool = $toollist[$name];
			if ($tool) {
				$caption = $tool->caption;
				$desc = $tool->desc;
			} else {*/
				$caption = $row->sessname;
				$desc = $row->appname;
			//}
			if(!isset($myappnum[$name])) {
				$myappnum[$name] = 1;
			} else {
				$myappnum[$name] = $myappnum[$name] + 1;
			}
			$sesslist[$i] = new App($name,
				$caption,
				$desc,
				'narwhal',
				$row->sessnum,
				$row->username,
				$myappnum[$name],
				0);
			$app = $sesslist[$i];
			$i++;
		}
	
		return $sesslist;
	}
}

//----------------------------------------------------------
// This class holds information about one application.
// It may be either a running session or an app that can be invoked.
//----------------------------------------------------------

class App 
{
	var $name;
	var $caption;
	var $desc;
	var $middleware;	// which environment to run in
	var $session;		// sessionid of application
	var $owner;		// owner of a running session
	var $num;		// Nth occurrence of this application in a list
	var $public;		// is this tool public?
	
	function App($n,$c,$d,$m,$s,$o,$num,$p) 
	{
		$this->name = $n;
		$this->caption = $c;
		$this->desc = $d;
		$this->middleware = $m;
		$this->session = $s;
		$this->owner = $o;
		$this->num = $num;
		$this->public = $p;
	}
}
?>
