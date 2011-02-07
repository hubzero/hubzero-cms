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


class RandomPageMacro extends WikiMacro 
{
	public function description() 
	{
		$txt = array();
		$txt['wiki'] = 'Generates a link to a random page.';
		$txt['html'] = '<p>Generates a link to a random page.</p>';
		return $txt['html'];
	}
	
	//-----------
	
	public function render() 
	{
		// Perform query
		$this->_db->setQuery( "SELECT pagename, scope, title FROM #__wiki_page ORDER BY rand() LIMIT 1" );
		$a = $this->_db->loadRow();
		
		// Did we get a result from the database?
		if ($a) {
			$title = ($a[2]) ? stripslashes($a[2]) : $a[0];
			
			// Build and return the link
			//return '['.JRoute::_('index.php?option='.$this->option.a.'scope='.$a[1].a.'pagename='.$a[0]).' '.$title.']';
			return '<a href="'.JRoute::_('index.php?option='.$this->option.'&scope='.$a[1].'&pagename='.$a[0]).'">'.$title.'</a>';
		}
	}
}
