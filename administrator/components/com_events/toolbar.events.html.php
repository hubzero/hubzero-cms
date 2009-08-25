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

class EventsToolbar 
{
	public function _CONF() 
	{
		$text = JText::_( 'CONFIGURATION' );
		JToolBarHelper::title( JText::_( 'EVENTS_MANAGER' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save('saveconfig');
		JToolBarHelper::cancel('cancelconfig');
	}
	
	public function _NEW() 
	{ 
		$text = JText::_( 'NEW' );
		JToolBarHelper::title( JText::_( 'EVENT' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}
	
	public function _EDIT() 
	{
		$text = JText::_( 'EDIT' );
		JToolBarHelper::title( JText::_( 'EVENT' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}

	public function _DEFAULT() 
	{
		JToolBarHelper::title( JText::_( 'EVENTS_MANAGER' ), 'addedit.png' );
		JToolBarHelper::addNew( 'addpage', 'Add Page');
		JToolBarHelper::custom('viewList', 'assign', JText::_( 'VIEW_RESPONDENTS' ), JText::_( 'VIEW_RESPONDENTS' ), true, false);
		JToolBarHelper::spacer();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::spacer();
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList();
	}
	
	public function _DEFAULTCAT() 
	{
		JToolBarHelper::title( JText::_( 'EVENTS_MANAGER').': <small><small>[ '.JText::_('EVENTS_CAL_LANG_EVENT_CATEGORIES').' ]</small></small>', 'addedit.png' );
		JToolBarHelper::publishList('publishcat');
		JToolBarHelper::unpublishList('unpublishcat');
		JToolBarHelper::spacer();
		JToolBarHelper::addNew('newcat');
		JToolBarHelper::editList('editcat');
		JToolBarHelper::deleteList('','removecat',JText::_('DELETE_CATEGORY'));
	}
	
	public function _NEWCAT() 
	{ 
		$text = JText::_( 'NEW' );
		JToolBarHelper::title( JText::_( 'EVENT' ).': <small><small>[ '. $text.' '.JText::_('EVENTS_CAL_LANG_EVENT_CATEGORY').' ]</small></small>', 'addedit.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::save('savecat');
		JToolBarHelper::spacer();
		JToolBarHelper::media_manager();
		JToolBarHelper::cancel('cancelcat');
	}
	
	public function _EDITCAT() 
	{
		$text = JText::_( 'EDIT' );
		JToolBarHelper::title( JText::_( 'EVENT' ).': <small><small>[ '. $text.' '.JText::_('EVENTS_CAL_LANG_EVENT_CATEGORY').' ]</small></small>', 'addedit.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::save('savecat');
		JToolBarHelper::spacer();
		JToolBarHelper::media_manager();
		JToolBarHelper::cancel('cancelcat');
	}
	
	//-----------

	public function _PAGES() 
	{
		JToolBarHelper::title( '<a href="index.php?option=com_events">'.JText::_( 'EVENTS' ).'</a>: <small><small>[ '.JText::_('PAGES').' ]</small></small>', 'addedit.png' );
		JToolBarHelper::addNew( 'addpage', JText::_('ADD_PAGE'));
		JToolBarHelper::editList( 'editpage' );
		JToolBarHelper::deleteList( '', 'removepage', JText::_('REMOVE_PAGE') );
	}
	
	//-----------
	
	public function _EDITPAGE($edit) 
	{
		$text = ( $edit ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );
		
		JToolBarHelper::title( '<a href="index.php?option=com_events">'.JText::_( 'EVENTS_PAGE' ).'</a>: <small><small>[ '. $text.' ]</small></small>', 'user.png' );
		JToolBarHelper::save('savepage');
		JToolBarHelper::cancel('cancelpage');
	}

	//-----------
	
	public function _VIEWRESPONDENT()
	{
		JToolBarHelper::title( '<a href="index.php?option=com_events">'.JText::_( 'EVENTS' ).'</a>', 'user.png' );
		JToolBarHelper::cancel();
	}
	
	//-----------

	public function _VIEWLIST()
	{
		JToolBarHelper::title( '<a href="index.php?option=com_events">'.JText::_( 'EVENTS' ).'</a>', 'user.png' );
		JToolBarHelper::custom('downloadList', 'upload', JText::_('DOWNLOAD_CSV'), JText::_('DOWNLOAD_CSV'), false, false);
		JToolBarHelper::deleteList( '', 'removerespondent', JText::_('Delete') );
		JToolBarHelper::cancel();
	}
}
?>