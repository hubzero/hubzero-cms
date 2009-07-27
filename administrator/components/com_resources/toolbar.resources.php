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

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );

//-----------

$method = JRequest::getVar( 'method', '' );
if ($method && $method == 'create') {
	$task = 'add';
}

switch ($task) 
{
	// Resource editing
	case 'add':        ResourcesToolbar::_EDIT(0);     break;
	case 'edit':       ResourcesToolbar::_EDIT(1);     break;
	
	// Resource child management
	case 'addchild':   ResourcesToolbar::_ADDCHILD();  break;

	// Types management
	case 'viewtypes':  ResourcesToolbar::_TYPES();     break;
	case 'canceltype': ResourcesToolbar::_TYPES();     break;
	case 'newtype':    ResourcesToolbar::_EDITTYPE(0); break;
	case 'edittype':   ResourcesToolbar::_EDITTYPE(1); break;
	
	// Resource views
	case 'orphans':    ResourcesToolbar::_ORPHANS();   break;
	case 'children':   ResourcesToolbar::_CHILDREN();  break;
	case 'browse':     ResourcesToolbar::_DEFAULT();   break;
	
	// Tags management
	case 'edittags':   ResourcesToolbar::_EDITTAGS();  break;
	
	default: ResourcesToolbar::_DEFAULT(); break;
}
?>