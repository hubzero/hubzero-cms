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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------
// Class for toolbar generation
//----------------------------------------------------------

class AnswersToolbar
{
	public function _EDITQ($edit) 
	{
		$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );
		JToolBarHelper::title( JText::_( 'Question' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		JToolBarHelper::spacer();
		if($edit) {
		JToolBarHelper::addNew( 'newa', 'Add Answer');
		}		
		JToolBarHelper::save( 'saveq', 'Save Question' );
		JToolBarHelper::cancel();
	}

	//-----------

	public function _EDITA($edit) 
	{
		$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );
	
		JToolBarHelper::title( JText::_( 'Answer' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save( 'savea', 'Save Answer' );
		JToolBarHelper::cancel();
	}

	//-----------

	public function _DELETE() 
	{
		JToolBarHelper::title( JText::_( 'Answers Manager' ), 'addedit.png' );
		JToolBarHelper::cancel();
	}

	//-----------

	public function _VIEW() 
	{
		JToolBarHelper::title( JText::_( 'Answers Manager' ), 'addedit.png' );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::spacer();
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList( '','deleteq', 'Delete' );
	}

	//-----------

	public function _VIEWA() 
	{
		JToolBarHelper::title( JText::_( 'Answers Manager' ), 'addedit.png' );
		JToolBarHelper::addNew( 'newa', 'New Answer' );
		JToolBarHelper::editList();
		JToolBarHelper::deleteList( '', 'deletea', 'Delete' );
	}

	//-----------

	public function _DEFAULT() 
	{
		JToolBarHelper::title( JText::_( 'Answers Manager' ), 'addedit.png' );
		//JToolBarHelper::publishList();
		//JToolBarHelper::unpublishList();
		JToolBarHelper::spacer();
		JToolBarHelper::addNew( 'newq', 'New Question');
		//JToolBarHelper::editList();
		JToolBarHelper::deleteList( '', 'remove', 'Delete Question' );
		
	}
}
?>