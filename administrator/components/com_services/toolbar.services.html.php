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

class ServicesToolbar
{
	
	function _SERVICE() 
	{
		JToolBarHelper::title( '<a href="index.php?option=com_services">'.JText::_( 'Services &amp; Subscriptions Manager' ).'</a>', 'addedit.png' );
		JToolBarHelper::save('saveservice', 'Save Changes');
		JToolBarHelper::cancel();
	}

	//-----------

	function _SUBSCRIPTION( ) 
	{
		JToolBarHelper::title( '<a href="index.php?option=com_services">'.JText::_( 'Services &amp; Subscriptions Manager' ).'</a>', 'addedit.png' );
		JToolBarHelper::save('savesubscription', 'Save Changes' );
		JToolBarHelper::cancel();
	}


	//-----------

	function _SERVICES() 
	{
		JToolBarHelper::title( '<a href="index.php?option=com_services">'.JText::_( 'Services &amp; Subscriptions Manager' ).'</a>: <small><small>[ Services ]</small></small>', 'addedit.png' );
		//JToolBarHelper::addNew('newservice','New Service');
	
	}

	//-----------

	public function _DEFAULT() 
	{
		JToolBarHelper::title( '<a href="index.php?option=com_services">'.JText::_( 'Services &amp; Subscriptions Manager' ).'</a>: <small><small>[ Subscriptions ]</small></small>', 'addedit.png' );
		JToolBarHelper::preferences('com_services', '550');
		JToolBarHelper::spacer();
	}
}
?>