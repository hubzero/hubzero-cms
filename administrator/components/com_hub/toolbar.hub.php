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

//----------------------------------------------------------

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );

switch ($task)
{
	case 'site':         HubToolbar::_SITE();         break;
	case 'registration': HubToolbar::_REGISTRATION(); break;
	case 'databases':    HubToolbar::_DATABASES();    break;
	case 'misc':         HubToolbar::_MISC();         break;
	case 'components':   HubToolbar::_COMPONENTS();   break;
	case 'new':          HubToolbar::_EDIT(0);        break;
	case 'edit':         HubToolbar::_EDIT(1);        break;

	case 'addorg':  HubToolbar::_EDIT_ORG(0); break;
	case 'editorg': HubToolbar::_EDIT_ORG(1); break;
	case 'orgs':    HubToolbar::_ORGS();      break;

	default: HubToolbar::_SITE(); break;
}
?>
