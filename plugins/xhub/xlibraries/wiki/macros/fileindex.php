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

class FileIndexMacro extends WikiMacro 
{
	public function description() 
	{
		$txt = array();
		$txt['wiki'] = 'Inserts an alphabetic list of all files and images attached to this page into the output. Accepts a prefix string as parameter: if provided, only files with names that start with the prefix are included in the resulting list. If this parameter is omitted, all files are listed.';
		$txt['html'] = '<p>Inserts an alphabetic list of all files and images attached to this page into the output. Accepts a prefix string as parameter: if provided, only files with names that start with the prefix are included in the resulting list. If this parameter is omitted, all files are listed.</p>';
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
			$sql  = "SELECT * FROM #__wiki_attachments WHERE LOWER(filename) LIKE '".strtolower($et)."%' AND pageid='".$this->pageid."' ORDER BY filename ASC";
		} else {
			// Get all pages
			$sql  = "SELECT * FROM #__wiki_attachments WHERE pageid='".$this->pageid."' ORDER BY filename ASC";
		}

		// Perform query
		$this->_db->setQuery( $sql );
		$rows = $this->_db->loadObjectList();
		
		// Did we get a result from the database?
		if ($rows) {
			ximport('wiki.config');
			$configs = array();
			$configs['option'] = $this->option;
			if ($this->filepath != '') {
				$configs['filepath'] = $this->filepath;
			}
			$config = new WikiConfig( $configs );
			
			$xhub =& XFactory::getHub();
			
			// Build and return the link
			$html = '<ul>';
			foreach ($rows as $row) 
			{
				//$html .= '<li><a href="'.$this->scope.'/'.$row->pagename.'">'.$title.'</a></li>';
				$link = $xhub->getCfg('hubLongURL').$config->filepath.DS.$this->pageid.DS.$row->filename;
				
				/*$html .= ' * ['.$url;
				$html .= ($row->title) ? ' '.stripslashes($row->title) : ' '.$row->pagename;
				$html .= ']'."\n";*/
				$html .= '<li><a href="'.JRoute::_($link).'">'.$row->filename;
				$html .= ($row->description) ? '<br /><span>'.stripslashes($row->description).'</span>' : '';
				$html .= '</a></li>'."\n";
			}
			$html .= '</ul>';
		
			return $html;
		} else {
			// Return error message
			//return '(TitleIndex('.$et.') failed)';
			return '(No '.$et.' files to display)';
		}
	}
}
?>