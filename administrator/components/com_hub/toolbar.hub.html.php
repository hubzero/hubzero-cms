<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
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

class HubToolbar
{
	public function _MISC() 
	{
		JToolBarHelper::title( JText::_('HUB Configuration').': '.JText::_('Misc. Settings') );
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList();
	}
	
	//-----------

	public function _EDIT($edit) 
	{
		$text = ( $edit ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );
		
		JToolBarHelper::title( JText::_('HUB Configuration').': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}
	
	//-----------

	public function _SITE() 
	{
		JToolBarHelper::title( JText::_('HUB Configuration').': '.JText::_('Site'), 'addedit.png' );
		JToolBarHelper::preferences('com_hub', '550');
		JToolBarHelper::save('savesite');
		JToolBarHelper::cancel();
	}
	
	//-----------

	public function _REGISTRATION() 
	{
		JToolBarHelper::title( JText::_('HUB Configuration').': '.JText::_('Registration'), 'addedit.png' );
		JToolBarHelper::preferences('com_hub', '550');
		JToolBarHelper::save('savereg');
		JToolBarHelper::cancel();
	}
	
	//-----------

	public function _DATABASES() 
	{
		JToolBarHelper::title( JText::_('HUB Configuration').': '.JText::_('Databases'), 'addedit.png' );
		JToolBarHelper::save('savedb');
		JToolBarHelper::cancel();
	}
	
	//-----------

	public function _COMPONENTS() 
	{
		JToolBarHelper::title( JText::_('HUB Configuration').': '.JText::_('Components'), 'addedit.png' );
		JToolBarHelper::save('savecom');
		JToolBarHelper::cancel();
	}
	
	//-----------
	
	public function _EDIT_ORG($edit) 
	{
		$text = ( $edit ? JText::_( 'Edit Organization' ) : JText::_( 'New Organization' ) );
		
		JToolBarHelper::title( JText::_( 'HUB Configuration' ).': <small><small>[ '. $text.' ]</small></small>', 'user.png' );
		JToolBarHelper::save('saveorg');
		JToolBarHelper::cancel('cancelorg');
	}
	
	//-----------
	
	public function _ORGS() 
	{
		JToolBarHelper::title( JText::_( 'HUB Configuration' ).': <small><small>[ '. JText::_('Organizations').' ]</small></small>', 'user.png' );
		JToolBarHelper::addNew('addorg');
		JToolBarHelper::editList('editorg');
		JToolBarHelper::deleteList('Remove organization?','removeorg');
	}
}
?>
