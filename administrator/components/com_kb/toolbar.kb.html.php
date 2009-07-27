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

class KbToolbar 
{
	public function _EDITCAT($edit) 
	{
		$text = ( $edit ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );
		
		JToolBarHelper::title( JText::_('KNOWLEDGE_BASE').': '.JText::_('CATEGORY').': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save( 'savecat', JText::_('SAVE') );
		JToolBarHelper::cancel();
	}

	//-----------

	public function _EDITFAQ($edit) 
	{
		$text = ( $edit ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );
		
		JToolBarHelper::title( JText::_('KNOWLEDGE_BASE').': '.JText::_('ARTICLE').': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save( 'savefaq', JText::_('SAVE') );
		JToolBarHelper::cancel();
	}

	//-----------

	public function _DELETECAT() 
	{
		JToolBarHelper::title( JText::_( 'KNOWLEDGE_BASE' ), 'addedit.png' );
		JToolBarHelper::cancel();
	}

	//-----------

	public function _VIEW() 
	{
		JToolBarHelper::title( JText::_( 'KNOWLEDGE_BASE' ), 'addedit.png' );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::spacer();
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList( '', 'deletecat', JText::_('DELETE') );
	}

	//-----------

	public function _VIEWFAQS() 
	{
		JToolBarHelper::title( JText::_('KNOWLEDGE_BASE').': '.JText::_('ARTICLES'), 'addedit.png' );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::spacer();
		JToolBarHelper::addNew( 'newfaq' );
		JToolBarHelper::editList();
		JToolBarHelper::deleteList( '', 'deletefaq', JText::_('DELETE') );
	}

	//-----------

	public function _DEFAULT() 
	{
		JToolBarHelper::title( JText::_( 'KNOWLEDGE_BASE' ), 'addedit.png' );
		JToolBarHelper::publishList( 'publishc' );
		JToolBarHelper::unpublishList( 'unpublishc' );
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'newfaq', 'new', '', JText::_('NEW_ARTICLE'), false );
		JToolBarHelper::spacer();
		JToolBarHelper::addNew( 'newcat', JText::_('NEW_CATEGORY'));
		JToolBarHelper::editList();
		JToolBarHelper::deleteList( '', 'deletecat', JText::_('DELETE_CATEGORY') );
	}
}
?>