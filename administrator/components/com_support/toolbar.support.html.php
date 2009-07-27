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
// Support Administration Toolbar
//----------------------------------------------------------

class SupportToolbar 
{
	public function _EDITCAT($edit) 
	{
		$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );
		JToolBarHelper::title( JText::_( 'Category' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save('savecat');
		JToolBarHelper::cancel('cancelcat');
	}

	//-----------

	public function _EDITSEC($edit) 
	{
		$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );
		JToolBarHelper::title( JText::_( 'Ticket Section' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save('savesec');
		JToolBarHelper::cancel('cancelsec');
	}

	//-----------
	
	public function _EDITMSG($edit) 
	{
		$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );
		JToolBarHelper::title( JText::_( 'Ticket Message' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save('savemsg');
		JToolBarHelper::cancel('cancelmsg');
	}
	
	//-----------
	
	public function _EDITRES($edit) 
	{
		$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );
		JToolBarHelper::title( JText::_( 'Ticket Resolution' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save('saveres');
		JToolBarHelper::cancel('cancelres');
	}

	//-----------
	
	public function _EDITTG($edit) 
	{
		$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );
		JToolBarHelper::title( JText::_( 'Tag/Group' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save('savetg');
		JToolBarHelper::cancel('canceltg');
	}
	
	//-----------
	
	public function _EDIT($edit) 
	{
		$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );
		JToolBarHelper::title( JText::_( 'Ticket' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}

	//-----------
	
	public function _DEFAULTCAT() 
	{
		JToolBarHelper::title( JText::_( 'Ticket Categories' ), 'addedit.png' );
		JToolBarHelper::addNew('newcat');
		JToolBarHelper::editList('editcat');
		JToolBarHelper::deleteList('','deletecat');
	}
	
	//-----------
	
	public function _DEFAULTSEC() 
	{
		JToolBarHelper::title( JText::_( 'Ticket Sections' ), 'addedit.png' );
		JToolBarHelper::addNew('newsec');
		JToolBarHelper::editList('editsec');
		JToolBarHelper::deleteList('','deletesec');
	}
	
	//-----------
	
	public function _DEFAULTMSG() 
	{
		JToolBarHelper::title( JText::_( 'Support' ).': <small><small>[ '.JText::_( 'Ticket Messages' ).' ]</small></small>', 'addedit.png' );
		JToolBarHelper::addNew('newmsg');
		JToolBarHelper::editList('editmsg');
		JToolBarHelper::deleteList('','deletemsg');
	}

	//-----------
	
	public function _DEFAULTRES() 
	{
		JToolBarHelper::title( JText::_( 'Support' ).': <small><small>[ '.JText::_( 'Ticket Resolutions' ).' ]</small></small>', 'addedit.png' );
		JToolBarHelper::addNew('newres');
		JToolBarHelper::editList('editres');
		JToolBarHelper::deleteList('','deleteres');
	}

	//-----------
	
	public function _DEFAULTTG() 
	{
		JToolBarHelper::title( JText::_( 'Support' ).': <small><small>[ '.JText::_( 'Tag/Group' ).' ]</small></small>', 'addedit.png' );
		JToolBarHelper::addNew('newtg');
		JToolBarHelper::editList('edittg');
		JToolBarHelper::deleteList('','deletetg');
	}

	//-----------
	
	public function _DEFAULT() 
	{
		JToolBarHelper::title( JText::_( 'Support' ).': <small><small>[ '.JText::_( 'Tickets' ).' ]</small></small>', 'addedit.png' );
		JToolBarHelper::preferences('com_support', '550');
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList();
	}
	
	//-----------
	
	function _REPORT() 
	{
		JToolBarHelper::title( JText::_( 'Support' ).': <small><small>[ '.JText::_( 'REPORT_ABUSE' ).' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}

	//-----------

	function _REPORTS() 
	{
		JToolBarHelper::title( JText::_( 'Support' ).': <small><small>[ '.JText::_( 'REPORT_ABUSE' ).' ]</small></small>', 'addedit.png' );
	}
}
?>