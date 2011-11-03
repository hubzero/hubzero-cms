<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------
// Class for toolbar generation
//----------------------------------------------------------

/**
 * Short description for 'ContribtoolToolbar'
 * 
 * Long description (if any) ...
 */
class ContribtoolToolbar
{

	/**
	 * Short description for '_DEFAULT'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $enabled Parameter description (if any) ...
	 * @return     void
	 */
	public function _DEFAULT($enabled)
	{
		$text = (!$enabled) ? ' <small><small style="color:red;">(component is disabled)</small></small>' : '';
		JToolBarHelper::title( '<a href="index.php?option=com_contribtool">'.JText::_( 'Tool Manager' ).'</a>'.$text, 'addedit.png' );
		JToolBarHelper::preferences('com_contribtool', '550');
		JToolBarHelper::spacer();
	}

	/**
	 * Short description for '_VIEWTOOLS'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $enabled Parameter description (if any) ...
	 * @return     void
	 */
	public function _VIEWTOOLS($enabled)
	{
		$text = (!$enabled) ? ' <small><small style="color:red;">(component is disabled)</small></small>' : '';
		JToolBarHelper::title( '<a href="index.php?option=com_contribtool">'.JText::_( 'Tool Manager' ).'</a>'.$text, 'addedit.png' );
		JToolBarHelper::preferences('com_contribtool', '550');
		JToolBarHelper::spacer();
	}

	/**
	 * Short description for '_VIEWTOOLVERSIONS'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $edit Parameter description (if any) ...
	 * @param      mixed $toolid Parameter description (if any) ...
	 * @return     void
	 */
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

	/**
	 * Short description for '_EDITTOOL'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $edit Parameter description (if any) ...
	 * @param      mixed $toolid Parameter description (if any) ...
	 * @return     void
	 */
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

	/**
	 * Short description for '_EDITTOOLVERSION'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $edit Parameter description (if any) ...
	 * @return     void
	 */
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