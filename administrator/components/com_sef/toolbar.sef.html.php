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

class SefToolbar 
{
	public function _EDIT($edit) 
	{
		$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );
		JToolBarHelper::title( '<a href="index.php?option=com_sef">'.JText::_( 'SEF Manager' ).'</a>: <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}
	
	//-----------
	
	public function _EDITCONFIG() 
	{
		JToolBarHelper::title( '<a href="index.php?option=com_sef">'.JText::_( 'SEF Manager' ).'</a>: <small><small>[ '. JText::_('Configuration').' ]</small></small>', 'addedit.png' );
		JToolBarHelper::preferences('com_sef', '550');
		//JToolBarHelper::save();
		//JToolBarHelper::cancel();
	}
	
	//-----------

	public function _INFO() 
	{
		JToolBarHelper::title( '<a href="index.php?option=com_sef">'.JText::_( 'SEF Manager' ).'</a>', 'addedit.png' );
		JToolBarHelper::back();
	}
	
	//-----------

	public function _DEFAULT() 
	{
		JToolBarHelper::title( '<a href="index.php?option=com_sef">'.JText::_( 'SEF Manager' ).'</a>', 'addedit.png' );
		JToolBarHelper::preferences('com_sef', '550');
		JToolBarHelper::spacer();
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList();
	}
}
?>