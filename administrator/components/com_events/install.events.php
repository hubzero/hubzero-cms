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

function com_install() 
{
	$database =& JFactory::getDBO();

	// Do the clean up if installed on a previous installation
	$database->setQuery("SELECT count(id) as count, max(id) as lastInstalled FROM #__components WHERE name='Events'");
	$reginfo = $database->loadObjectList();
	$lastInstalled = $reginfo[0]->lastInstalled;

	// Check if there are more registered instances of the Events component
	if ($reginfo[0]->count <> "1") {
		// Get duplicates
		$sql = "SELECT * FROM #__components WHERE name='Events' AND id!='$lastInstalled' AND admin_menu_link LIKE 'option=com_events'";
		$database->setQuery($sql);
		$toberemoved = $database->loadObjectList();
		foreach ($toberemoved as $remid) 
		{
			// Delete duplicate entries
			$database->setQuery("DELETE FROM #__components WHERE id='$remid->id' or parent='$remid->id'");
			$database->query();
		}
	}
	
	// Well done
    echo "Installed Successfully";
    echo "<div align='left'>";
    include ("../components/com_events/index.html");
    echo "</div>";
}

?>
