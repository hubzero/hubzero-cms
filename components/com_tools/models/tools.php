<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
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

/**
 * Tools Model for Tools Component
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

/**
 * Tools Model
 */

class ToolsModelTools extends JModel
{
	public function getApplicationTools()
	{
		$dh = opendir('/opt/trac/tools');
		$result = array();
		
		while (($file = readdir($dh)) !== false) 
		{
			if (is_dir('/opt/trac/tools/' . $file)) {
				if (strncmp($file,'.', 1) != 0) {
					$result[] = $file;
				}
			}
		}
		
		closedir($dh);
		
		sort($result);
		
		if (count($result) > 0) {
			$aliases = implode("','",$result);
			
			$database =& JFactory::getDBO();
			//include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.version.php' );
			//$tv = new ToolVersion( $database );
			//AND (state='1' OR state='3')
			$query = "SELECT v.id, v.instance, v.toolname, v.title, MAX(v.revision), v.toolaccess, v.codeaccess, v.state, t.state AS tool_state 
						FROM #__tool as t, #__tool_version as v 
						WHERE v.toolname IN ('".$aliases."') AND t.id=v.toolid
						AND (v.state='1' OR v.state='3')
						GROUP BY toolname
						ORDER BY v.toolname ASC";
			$database->setQuery( $query );
			return $database->loadObjectList();
		}
		
		return $result;
	}
}
?>