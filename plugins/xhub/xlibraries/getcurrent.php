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

//----------------------------------------------//
//  Get Current Usage Statistics from Database  //
//----------------------------------------------//
function getcurrent($totalid = 11, $period = 1) 
{
	global $usagestats_dbhost, $usagestats_username, $usagestats_password, $usagestats_database;
	
	$total = array();

	// Set hub...
	//------------
	$hub = 1;

	// Look up totals information...
	//-------------------------------
	$db = @mysql_connect($usagestats_dbhost, $usagestats_username, $usagestats_password);
	if($db) {
		mysql_select_db($usagestats_database, $db);
		$sql = "SELECT totals.valfmt, totals.name, totalvals.value FROM totals, totalvals WHERE totals.total = totalvals.total AND totalvals.hub = '" . mysql_escape_string($hub) . "' AND totalvals.total = '" . mysql_escape_string($totalid) . "' AND totalvals.period = '" . mysql_escape_string($period) . "' ORDER BY totalvals.datetime DESC LIMIT 1";
		$result = mysql_query($sql, $db);
		if($result) {
			if(mysql_num_rows($result) > 0) {
				if($row = mysql_fetch_row($result)) {
					$total['name'] = preg_replace("/\\$\{([0-9]+)\}/", "", $row[1]);
					$total['value'] = $row[2];
					$total['text'] = valformat($row[2], $row[0]);
				}
			}
			mysql_free_result($result);
		}
		mysql_close();
	}

	return($total);
}

?>