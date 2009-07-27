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
// Class for toolbar generation
//----------------------------------------------------------

class ResourcesToolbar
{
	//----------------------------------------------------------
	// Resource editing
	//----------------------------------------------------------
	
	public function _EDIT($edit) 
	{
		$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );
		JToolBarHelper::title( JText::_( 'Resource' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		//JToolBarHelper::preview( 'resourcewindow', true );
		JToolBarHelper::spacer();
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}
	
	//----------------------------------------------------------
	// Resource child management
	//----------------------------------------------------------
	
	public function _ADDCHILD()	
	{
		JToolBarHelper::title( '<a href="index.php?option=com_resources">'.JText::_( 'Resource Manager' ).'</a>', 'addedit.png' );
		JToolBarHelper::cancel();
	}

	//----------------------------------------------------------
	// Types management
	//----------------------------------------------------------

	public function _TYPES()
	{
		JToolBarHelper::title( '<a href="index.php?option=com_resources">'.JText::_( 'Resources' ).'</a>: <small><small>[ Types ]</small></small>', 'addedit.png' );
		JToolBarHelper::addNew( 'newtype', 'New Type' );
		JToolBarHelper::editList( 'edittype' );
		JToolBarHelper::deleteList( '', 'deletetype', 'Delete' );
	}
	
	//-----------
	
	public function _EDITTYPE($edit)	
	{
		$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );
		JToolBarHelper::title( '<a href="index.php?option=com_resources">'.JText::_( 'Resource Type' ).'</a>: <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save('savetype');
		JToolBarHelper::cancel('canceltype');
	}
	
	//----------------------------------------------------------
	// Tags
	//----------------------------------------------------------

	public function _EDITTAGS() 
	{
		JToolBarHelper::title( JText::_( 'Resource Manager' ), 'addedit.png' );
		JToolBarHelper::save('savetags');
		JToolBarHelper::cancel('canceltags');
	}
	
	//----------------------------------------------------------
	// Resource views
	//----------------------------------------------------------
	
	public function _ORPHANS() 
	{
		JToolBarHelper::title( '<a href="index.php?option=com_resources">'.JText::_( 'Resource Manager' ).'</a>', 'addedit.png' );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::spacer();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList();
	}
	
	//-----------

	public function _CHILDREN() 
	{
		JToolBarHelper::title( '<a href="index.php?option=com_resources">'.JText::_( 'Resource Manager' ).'</a>', 'addedit.png' );
		JToolBarHelper::addNew( 'addchild', 'Add Child');
		JToolBarHelper::deleteList( '', 'removechild', 'Remove Child' );
		JToolBarHelper::spacer();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList();
	}

	//-----------

	public function _DEFAULT() 
	{
		JToolBarHelper::title( '<a href="index.php?option=com_resources">'.JText::_( 'Resource Manager' ).'</a>', 'addedit.png' );
		JToolBarHelper::preferences('com_resources', '550');
		JToolBarHelper::spacer();
		JToolBarHelper::addNew( 'addchild', 'Add Child');
		JToolBarHelper::spacer();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::spacer();
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList();
	}
}
?>