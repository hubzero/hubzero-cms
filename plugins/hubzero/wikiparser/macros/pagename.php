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


class PageNameMacro extends WikiMacro 
{
	public function description() 
	{
		$txt = array();
		$txt['wiki'] = "Accepts either 'alias' or 'title' as arg. Returns either the alias (default if no args given) or title of the current page.";
		$txt['html'] = "<p>Accepts either 'alias' or 'title' as arg. Returns either the alias (default if no args given) or title of the current page.</p>";
		return $txt['html'];
	}
	
	//-----------
	
	public function render() 
	{
		$et = $this->args;
		
		switch (trim($et)) 
		{
			case 'title':
				$sql = "SELECT title FROM #__wiki_page WHERE pagename='".$this->pagename."' AND `group`='".$this->domain."' AND scope='".$this->scope."'";
				// Perform query
				$this->_db->setQuery( $sql );
				return stripslashes($this->_db->loadResult());
			break;
			
			case 'alias':
			default:
				return $this->pagename;
			break;
		}
	}
}
