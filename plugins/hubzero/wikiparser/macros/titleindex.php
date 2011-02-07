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


class TitleIndexMacro extends WikiMacro 
{
	public function description() 
	{
		$txt = array();
		$txt['wiki'] = 'Inserts an alphabetic list of all wiki pages into the output. Accepts a prefix string as parameter: if provided, only pages with names that start with the prefix are included in the resulting list. If this parameter is omitted, all pages are listed.';
		$txt['html'] = '<p>Inserts an alphabetic list of all wiki pages into the output. Accepts a prefix string as parameter: if provided, only pages with names that start with the prefix are included in the resulting list. If this parameter is omitted, all pages are listed.</p><p>The list may have a sorting applied by adding the sort=[title,created(oldest to newest),modified(newest to oldest)] argument. For example, <code>[[TitleIndex(sort=modified)]]</code> will list all pages by their last modified date (most recent to oldest). If you have a page prefix, simply add a comma and the sort parameter <em>after</em>. For example: <code>[[TitleIndex(Help, sort=modified)]]</code></p>';
		return $txt['html'];
	}
	
	//-----------
	
	public function render() 
	{
		$et = $this->args;

		$sort = '';
		if ($et) {
			$et = strip_tags($et);
			
			if (strstr($et, ',')) {
				$attribs = explode(',', $et);
				$et = trim($attribs[0]);
				$sort = strtolower(trim($attribs[1]));
			}
			
			if (strtolower($et) == 'sort=modified' || strtolower($et) == 'sort=created' || strtolower($et) == 'sort=title') {
				$sort = $et;
				$et = '';
			}
		}
		
		// What pages are we getting?
		switch ($sort) 
		{
			case 'sort=modified':
				$sql = "SELECT p.`id`, p.`pagename`, p.`scope`, p.`group`, (CASE WHEN (p.`title` IS NOT NULL AND p.`title` !='') THEN p.`title` ELSE p.`pagename` END) AS `title`, v.`created` AS `modified`, MAX(v.`created`) FROM #__wiki_page AS p, #__wiki_version AS v WHERE v.pageid=p.id AND v.approved=1 AND ";
			break;
			case 'sort=created':
			case 'sort=title':
			default:
				$sql = "SELECT p.`id`, p.`pagename`, p.`scope`, p.`group`, (CASE WHEN (p.`title` IS NOT NULL AND p.`title` !='') THEN p.`title` ELSE p.`pagename` END) AS `title`, v.`created`, MAX(v.`version`) FROM #__wiki_page AS p, #__wiki_version AS v WHERE v.pageid=p.id AND v.approved=1 AND ";
			break;
		}
		
		if ($et) {
			// Get pages with a prefix
			if ($this->domain) {
				$sql .= "LOWER(p.pagename) LIKE '".strtolower($et)."%' AND p.`group`='".$this->domain."'";
			} else {
				$sql .= "LOWER(p.pagename) LIKE '".strtolower($et)."%'";
			}
		} else {
			// Get all pages
			if ($this->domain) {
				$sql .= "p.`group`='".$this->domain."'";
			} else {
				$sql .= "p.`group`=''";
			}
		}
		switch ($sort) 
		{
			case 'sort=created':
				$sql .= " GROUP BY v.pageid ORDER BY `created` ASC";
			break;
			case 'sort=modified':
				$sql .= " GROUP BY v.pageid ORDER BY `modified` DESC";
			break;
			case 'sort=title':
			default:
				$sql .= " GROUP BY v.pageid ORDER BY `title` ASC, `pagename` ASC";
			break;
		}
//echo '<!-- '.$sql.' -->';
		// Perform query
		$this->_db->setQuery( $sql );
		$rows = $this->_db->loadObjectList();
//echo '<!-- '.count($rows).' -->';
		// Did we get a result from the database?
		if ($rows) {
			// Build and return the link
			$html = '<ul>';
			foreach ($rows as $row) 
			{
				if ($row->pagename == $this->pagename) {
					continue;
				}
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
