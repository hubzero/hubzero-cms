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

switch ($task) 
{
	case 'add':  MwToolbar::_CANCEL(); break;
	case 'new':  MwToolbar::_CANCEL(); break;
	case 'edit': MwToolbar::_CANCEL(); break;
	
	case 'addlicense': MwToolbar::_EDIT_LICENSE(0); break;
	case 'editlicense': MwToolbar::_EDIT_LICENSE(1); break;
	case 'licenses': MwToolbar::_LICENSES(); break;
	
	case 'addlicenseassoc': MwToolbar::_EDIT_LICENSE_ASSOC(0); break;
	case 'editlicenseassoc': MwToolbar::_EDIT_LICENSE_ASSOC(1); break;
	case 'licenseassoc': MwToolbar::_LICENSE_ASSOC(); break;
	
	default: MwToolbar::_DEFAULT(); break;
}
?>