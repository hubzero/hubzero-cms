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

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );

switch ( $task ) 
{
	case 'configure': EventsToolbar::_CONF();       break;
	case 'new':       EventsToolbar::_NEW();        break;
	case 'add':       EventsToolbar::_NEW();        break;
	case 'edit':      EventsToolbar::_EDIT();       break;
	case 'editcat':   EventsToolbar::_EDITCAT();    break;
	case 'newcat':    EventsToolbar::_NEWCAT();     break;
	case 'cats':      EventsToolbar::_DEFAULTCAT(); break;
	case 'viewList': EventsToolbar::_VIEWLIST(); break;
	case 'viewrespondent': EventsToolbar::_VIEWRESPONDENT(); break;
	case 'pages':    EventsToolbar::_PAGES();     break;
	case 'addpage':  EventsToolbar::_EDITPAGE(0); break;
	case 'editpage': EventsToolbar::_EDITPAGE(1); break;
	default: EventsToolbar::_DEFAULT(); break;
}
?>