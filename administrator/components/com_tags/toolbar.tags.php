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

switch ($task) 
{
	case 'pierce':   TagsToolbar::_Pierce();    break;
	case 'merge':    TagsToolbar::_Merge();     break;
	case 'add':      TagsToolbar::_Edit(0);     break;
	case 'edit':     TagsToolbar::_Edit(1);     break;
	case 'edittags': TagsToolbar::_Edittags(1); break;
	
	default: TagsToolbar::_Default(); break;
}

//----------------------------------------------------------
// Tags Administration Toolbar
//----------------------------------------------------------

class TagsToolbar 
{
	public function _Default() 
	{
		JToolBarHelper::title( JText::_( 'TAGS' ), 'addedit.png' );
		JToolBarHelper::preferences('com_tags', '550');
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'pierce', 'copy', '', JText::_('PIERCE'), false );
		JToolBarHelper::custom( 'merge', 'forward', '', JText::_('MERGE'), false );
		JToolBarHelper::spacer();
		JToolBarHelper::addNew();
		JToolBarHelper::deleteList();
	}

	public function _Pierce() 
	{
		JToolBarHelper::title( JText::_( 'TAGS' ).': <small><small>[ '.JText::_('PIERCE').' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save('pierce');
		JToolBarHelper::cancel();
	}
	
	public function _Merge() 
	{
		JToolBarHelper::title( JText::_( 'TAGS' ).': <small><small>[ '.JText::_('MERGE').' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save('merge');
		JToolBarHelper::cancel();
	}

	public function _Edit($edit) 
	{
		$text = ( $edit ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );

		JToolBarHelper::title( JText::_( 'TAGS' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}

	public function _Edittags($edit) 
	{
		$text = ( $edit ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );

		JToolBarHelper::title( JText::_( 'TAGS' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save('savetags');
		JToolBarHelper::cancel('canceltags');
	}
}
?>