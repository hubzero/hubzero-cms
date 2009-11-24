<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

//----------------------------------------------------------
// Class for toolbar generation
//----------------------------------------------------------

class ContribtoolToolbar
{
	
	//-----------

	public function _DEFAULT($enabled) 
	{
		$text = (!$enabled) ? ' <small><small style="color:red;">(component is disabled)</small></small>' : '';
		JToolBarHelper::title( '<a href="index.php?option=com_contribtool">'.JText::_( 'Tool Manager' ).'</a>'.$text, 'addedit.png' );
		JToolBarHelper::preferences('com_contribtool', '550');
		JToolBarHelper::spacer();
	}

	public function _VIEWTOOLS($enabled) 
	{
		$text = (!$enabled) ? ' <small><small style="color:red;">(component is disabled)</small></small>' : '';
		JToolBarHelper::title( '<a href="index.php?option=com_contribtool">'.JText::_( 'Tool Manager' ).'</a>'.$text, 'addedit.png' );
		JToolBarHelper::preferences('com_contribtool', '550');
		JToolBarHelper::spacer();
	}

	public function _VIEWTOOLVERSIONS($edit,$toolid = 0)
	{
          $text = ( $edit ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );

          JToolBarHelper::title( JText::_( 'Tool Versions' ), 'user.png' );

		$bar =& JToolBar::getInstance('toolbar');
		$bar->appendButton('Link','back','Tool Manager','index.php?option=com_contribtool');
		$bar->appendButton('Link','edit','Edit Tool','index.php?option=com_contribtool&task=edit&toolid=' . $toolid);
		JToolBarHelper::divider();
          JToolBarHelper::editList();
          JToolBarHelper::publish();
          JToolBarHelper::unpublish();
	}

	public function _EDITTOOL($edit,$toolid=0)
	{
          $text = ( $edit ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );

          JToolBarHelper::title( JText::_( 'Tool' ).': <small><small>[ '. $text.' ]</small></small>', 'user.png' );
		$bar =& JToolBar::getInstance('toolbar');
		$bar->appendButton('Link','menus','Tool Versions','index.php?option=com_contribtool&task=view&toolid=' . $toolid);
		JToolBarHelper::divider();
          JToolBarHelper::apply();
          JToolBarHelper::save();
          JToolBarHelper::cancel();
	}

	public function _EDITTOOLVERSION($edit)
	{
          $text = ( $edit ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );

          JToolBarHelper::title( JText::_( 'Tool Version' ).': <small><small>[ '. $text.' ]</small></small>', 'user.png' );
          JToolBarHelper::apply();
          JToolBarHelper::save();
          JToolBarHelper::cancel();
	}
}
?>
