<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------

class RecentPageMacro extends WikiMacro 
{
	public function description() 
	{
		$txt = array();
		$txt['wiki'] = 'Generates a link and optional bit of text to a recently created or updated page page.';
		$txt['html'] = '<p>Generates a link and optional bit of text to a recently created or updated page page.</p><p>Args:</p>
		<ul><li>limit - Number of articles to return. Defaults to 1</li><li>container class - A CSS class to be applied to the wrapper <code>DIV</code></li></ul>';
		return $txt['html'];
	}
	
	//-----------
	
	public function render() 
	{
		$limit = 1;
		$cls = '';
		$limitstart = 0;
		
		if ($this->args) {
			$args = explode(',', $this->args);
			if (isset($args[0])) {
				$args[0] = intval($args[0]);
				if ($args[0]) {
					$limit = $args[0];
				}
			}
			if (isset($args[1])) {
				$cls = $args[1];
			}
			if (isset($args[2])) {
				$args[2] = intval($args[2]);
				if ($args[2]) {
					$limitstart = $args[2];
				}
			}
		}
		
		$query = "SELECT f.pageid, f.title, f.pagename, f.scope, f.GROUP, f.access, f.created_by, f.created, f.pagehtml, MAX(f.version) AS version FROM (
					SELECT v.pageid, w.title, w.pagename, w.scope, w.group, w.access, v.version, v.created_by, v.created, v.pagehtml
					FROM #__wiki_page AS w, #__wiki_version AS v
					WHERE w.id=v.pageid AND v.approved=1 AND w.group='".$this->domain."' AND w.scope='".$this->scope."' AND w.access!=1
					ORDER BY created DESC
					) AS f GROUP BY pageid ORDER BY created DESC
					LIMIT $limitstart, $limit";
					
		// Perform query
		$this->_db->setQuery( $query );
		$rows = $this->_db->loadObjectList();
		
		$html = '';
		
		// Did we get a result from the database?
		if ($rows) {
			foreach ($rows as $row)
			{
				if ($row->version > 1) {
					$t = JText::_('Updated');
				} else {
					$t = JText::_('Created');
				}
				$html .= '<div';
				if ($cls) {
					$html .= ' class="'.$cls.'"';
				}
				$html .= '>'."\n";
				$html .= "\t".'<h3><a href="'.JRoute::_('index.php?option='.$this->option.'&pagename='.$row->pagename.'&scope='.$row->scope).'">'.stripslashes($row->title).'</a></h3>'."\n";
				$html .= "\t".'<p class="modified-date">'.$t.' on '.JHTML::_('date', $row->created, "%d %b. %Y").'</p>'."\n";
				$html .= $this->_shortenText($row->pagehtml);
				$html .= "\t".'<p><a href="'.JRoute::_('index.php?option='.$this->option.'&pagename='.$row->pagename.'&scope='.$row->scope).'">Read more &rsaquo;</a></p>'."\n";
				$html .= '</div>'."\n";
			}
			
		} else {
			$html .= '<p class="warning">No results found.</p>'."\n";
		}
		
		return $html;
	}

	//-----------
	
	private function _shortenText($text, $chars=300, $p=1) 
	{
		$text = strip_tags($text);
		$text = str_replace("\n",' ',$text);
		$text = str_replace("\r",' ',$text);
		$text = str_replace("\t",' ',$text);
		$text = str_replace('   ',' ',$text);
		$text = trim($text);

		if (strlen($text) > $chars) {
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
			$text = $text.' &#8230;';
		}
		
		if ($text == '') {
			$text = '&#8230;';
		}
		
		if ($p) {
			$text = '<p>'.$text.'</p>';
		}

		return $text;
	}
}

