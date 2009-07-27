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

//----------------------------------------------------------

class TitleIndexMacro extends WikiMacro 
{
	public function description() 
	{
		$txt = array();
		$txt['wiki'] = 'Inserts an alphabetic list of all wiki pages into the output. Accepts a prefix string as parameter: if provided, only pages with names that start with the prefix are included in the resulting list. If this parameter is omitted, all pages are listed.';
		$txt['html'] = '<p>Inserts an alphabetic list of all wiki pages into the output. Accepts a prefix string as parameter: if provided, only pages with names that start with the prefix are included in the resulting list. If this parameter is omitted, all pages are listed.</p>';
		return $txt['html'];
	}
	
	//-----------
	
	public function render() 
	{
		$et = $this->args;

		// What pages are we getting?
		if ($et) {
			$et = strip_tags($et);
			// Get pages with a prefix
			//$sql  = "SELECT * FROM #__wiki_page WHERE LOWER(pagename) LIKE '".strtolower($et)."%' AND scope='".$this->scope."' ORDER BY pagename ASC";
			if ($this->domain) {
				$sql  = "SELECT * FROM #__wiki_page WHERE LOWER(pagename) LIKE '".strtolower($et)."%' AND `group`='".$this->domain."' ORDER BY pagename ASC";
			} else {
				$sql  = "SELECT * FROM #__wiki_page WHERE LOWER(pagename) LIKE '".strtolower($et)."%' ORDER BY pagename ASC";
			}
		} else {
			// Get all pages
			//$sql  = "SELECT * FROM #__wiki_page WHERE scope='".$this->scope."' ORDER BY pagename ASC";
			if ($this->domain) {
				$sql  = "SELECT * FROM #__wiki_page WHERE `group`='".$this->domain."' ORDER BY pagename ASC";
			} else {
				$sql  = "SELECT * FROM #__wiki_page WHERE `group`='' ORDER BY pagename ASC";
			}
		}

		// Perform query
		$this->_db->setQuery( $sql );
		$rows = $this->_db->loadObjectList();
		
		// Did we get a result from the database?
		if ($rows) {
			// Build and return the link
			$html = '<ul>';
			foreach ($rows as $row) 
			{
				$title = ($row->title) ? $row->title : $row->pagename;
				//$html .= '<li><a href="'.$this->scope.'/'.$row->pagename.'">'.$title.'</a></li>';
				$url  = substr($this->option,4,strlen($this->option)).DS;
				$url .= ($row->scope) ? $row->scope.DS : '';
				$url .= $row->pagename;
				
				/*$html .= ' * ['.$url;
				$html .= ($row->title) ? ' '.stripslashes($row->title) : ' '.$row->pagename;
				$html .= ']'."\n";*/
				$html .= '<li><a href="'.$url.'">';
				$html .= ($row->title) ? stripslashes($row->title) : $row->pagename;
				$html .= '</a></li>'."\n";
			}
			$html .= '</ul>';
		
			return $html;
		} else {
			// Return error message
			//return '(TitleIndex('.$et.') failed)';
			return '(No '.$et.' pages to display)';
		}
	}
}
?>