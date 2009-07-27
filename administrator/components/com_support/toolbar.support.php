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

switch ($task) 
{
	// Section management
	case 'newsec':      SupportToolbar::_EDITSEC(0);   break;
	case 'editsec':     SupportToolbar::_EDITSEC(1);   break;
	case 'sections':    SupportToolbar::_DEFAULTSEC(); break;

	// Category management
	case 'newcat':      SupportToolbar::_EDITCAT(0);   break;
	case 'editcat':     SupportToolbar::_EDITCAT(1);   break;
	case 'categories':  SupportToolbar::_DEFAULTCAT(); break;
	
	// Tag/group auto-assignment management
	case 'newtg':       SupportToolbar::_EDITTG(0);    break;
	case 'edittg':      SupportToolbar::_EDITTG(1);    break;
	case 'taggroup':    SupportToolbar::_DEFAULTTG();  break;
	
	// Message management
	case 'newmsg':      SupportToolbar::_EDITMSG(0);   break;
	case 'editmsg':     SupportToolbar::_EDITMSG(1);   break;
	case 'messages':    SupportToolbar::_DEFAULTMSG(); break;
	
	// Resolution management
	case 'newres':      SupportToolbar::_EDITRES(0);   break;
	case 'editres':     SupportToolbar::_EDITRES(1);   break;
	case 'resolutions': SupportToolbar::_DEFAULTRES(); break;
	
	case 'abusereport':  SupportToolbar::_REPORT();    break;
	case 'abusereports': SupportToolbar::_REPORTS();   break;
	
	// Ticket management
	case 'add':         SupportToolbar::_EDIT(0);      break;
	case 'edit':        SupportToolbar::_EDIT(1);      break;
	case 'tickets':     SupportToolbar::_DEFAULT();    break;
	
	default: SupportToolbar::_DEFAULT(); break;
}
?>