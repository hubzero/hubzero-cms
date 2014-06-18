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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'com_install'
 *
 * Long description (if any) ...
 *
 * @return void
 */
function com_install()
{
	$database = JFactory::getDBO();

	// Do the clean up if installed on a previous installation
	$database->setQuery("SELECT count(extension_id) as count, max(extension_id) as lastInstalled FROM #__extensions WHERE element='com_events'");
	$reginfo = $database->loadObjectList();
	$lastInstalled = $reginfo[0]->lastInstalled;

	// Check if there are more registered instances of the Events component
	if ($reginfo[0]->count <> "1") {
		// Get duplicates
		$sql = "SELECT * FROM #__extensions WHERE element='com_events' AND extension_id!='$lastInstalled'";
		$database->setQuery($sql);
		$toberemoved = $database->loadObjectList();
		foreach ($toberemoved as $remid)
		{
			// Delete duplicate entries
			$database->setQuery("DELETE FROM #__extensions WHERE extension_id='$remid->id'");
			$database->query();
		}
	}

	// Well done
    echo "Installed Successfully";
    echo "<div align='left'>";
    include ("../components/com_events/index.html");
    echo "</div>";
}
