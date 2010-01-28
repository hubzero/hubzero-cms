<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


//----------------------------------------------------------
// Class for toolbar generation
//----------------------------------------------------------

class JobsToolbar
{
	public function _EDIT($edit) 
	{
		$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );
		JToolBarHelper::title( JText::_( 'Job' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}
	//-----------
	
	public function _TYPES()
	{
		JToolBarHelper::title( '<a href="index.php?option=com_jobs">'.JText::_( 'Jobs Manager' ).'</a>: <small><small>[ Types ]</small></small>', 'addedit.png' );
		JToolBarHelper::addNew( 'newtype', 'New Type' );
		JToolBarHelper::editList( 'edittype' );
		JToolBarHelper::deleteList( '', 'deletetype', 'Delete' );
	}
	//-----------
	
	public function _EDITTYPE($edit)	
	{
		$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );
		JToolBarHelper::title( '<a href="index.php?option=com_resources&task=types">'.JText::_( 'Job Types' ).'</a>: <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save('savetype');
		JToolBarHelper::cancel('canceltype');
	}
	
	//-----------
	
	public function _CATS()
	{
		JToolBarHelper::title( '<a href="index.php?option=com_jobs">'.JText::_( 'Jobs Manager' ).'</a>: <small><small>[ Categories ]</small></small>', 'addedit.png' );
		JToolBarHelper::addNew( 'newcat', 'New Category' );
		JToolBarHelper::editList( 'editcat' );
		JToolBarHelper::save( 'saveorder', 'Save Order' );
		JToolBarHelper::deleteList( '', 'deletecat', 'Delete' );
	}
	
	//-----------
	
	public function _EDITCAT($edit)	
	{
		$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );
		JToolBarHelper::title( '<a href="index.php?option=com_jobs&task=categories">'.JText::_( 'Job Categories' ).'</a>: <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save('savecat');
		JToolBarHelper::cancel('cancelcat');
	}
	
	//-----------

	public function _DEFAULT() 
	{
		JToolBarHelper::title( '<a href="index.php?option=com_jobs">'.JText::_( 'Jobs Manager' ).'</a>', 'addedit.png' );
		JToolBarHelper::preferences('com_jobs', '550');
		JToolBarHelper::spacer();
		JToolBarHelper::spacer();
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		//JToolBarHelper::deleteList('remove');
	}
	
}
?>