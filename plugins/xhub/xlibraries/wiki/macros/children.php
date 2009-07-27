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

class ChildrenMacro extends WikiMacro 
{
	public function description() 
	{
		$txt = array();
		$txt['wiki'] = 'Inserts an alphabetic list of all sub-pages (children) of the current page into the output. Accepts two parameters:
 * \'\'\'depth: how deep to mine for pages. Default is one level.
 * \'\'\'description: show/hide the first line of text from the page.';
		$txt['html'] = '<p>Inserts an alphabetic list of all sub-pages (children) of the current page into the output. Accepts one parameter:</p>
		<ul>
			<li><strong>depth</strong>: how deep to mine for pages. Default is one level.</li>
			<!-- <li><strong>description</strong>: show/hide the first line of text from the page</li> -->
		</ul>
		<p>Example usage: <code>[[Children(depth=3)]]</code></p>';
		return $txt['html'];
	}
	
	//-----------
	
	public function render() 
	{
		$depth = 1;
		$description = 0;
		
		if ($this->args) {
			$args = split(',', $this->args);
			if (is_array($args)) {
				foreach ($args as $arg) 
				{
					$arg = trim($arg);
					if (substr($arg, 0, 6) == 'depth=') {
		                $bits = split('=', $arg);
						$depth = intval(trim(end($bits)));
		                continue;
					}
					if (substr($arg, 0, 12) == 'description=') {
		                $bits = split('=', $arg);
						$description = intval(trim(end($bits)));
		                continue;
					}
				}
			} else {
				$arg = trim($args);
				if (substr($arg, 0, 6) == 'depth=') {
	                $bits = split('=', $arg);
					$depth = intval(trim(end($bits)));
				}
				if (substr($arg, 0, 12) == 'description=') {
	                $bits = split('=', $arg);
					$description = intval(trim(end($bits)));
				}
			}
		}

		$scope = ($this->scope) ? $this->scope.DS.$this->pagename : $this->pagename;
		
		return $this->listChildren( 1, $depth, $scope );
	}
	
	//-----------
	
	private function listChildren( $currentDepth, $targetDepth, $scope='' ) 
	{
		$html = '';
		
		if ($currentDepth > $targetDepth) {
			return $html;
		}
		
		$rows = $this->getchildren( $scope );
		
		if ($rows) {
			$html = '<ul>';
			foreach ($rows as $row) 
			{
				$title = ($row->title) ? $row->title : $row->pagename;

				$url  = substr($this->option,4,strlen($this->option)).DS;
				$url .= ($row->scope) ? $row->scope.DS : '';
				$url .= $row->pagename;

				/*$html .= ' * ['.$url;
				$html .= ($row->title) ? ' '.stripslashes($row->title) : ' '.$row->pagename;
				$html .= ']'."\n";*/
				$html .= '<li><a href="'.$url.'">';
				$html .= ($row->title) ? stripslashes($row->title) : $row->pagename;
				$html .= '</a>';
				$html .= $this->listChildren( $currentDepth+1, $targetDepth, $row->scope.DS.$row->pagename );
				$html .= '</li>'."\n";
			}
			$html .= '</ul>';
		} elseif ($currentDepth == 1) {
			// Return error message
			//return '(TitleIndex('.$et.') failed)';
			return '<p>(No sub-pages to display)</p>';
		}
		
		return $html;
	}
	
	//-----------
	
	private function getChildren($scope) 
	{
		// Get all pages
		$sql = "SELECT * FROM #__wiki_page WHERE scope='".$scope."' AND `group`='".$this->domain."' ORDER BY pagename ASC";

		// Perform query
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
}
?>