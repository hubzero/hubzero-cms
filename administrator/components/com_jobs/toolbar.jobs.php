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

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );
//-----------

$method = JRequest::getVar( 'method', '' );
if ($method && $method == 'create') {
	$task = 'add';
}

switch ($task) 
{
	// Job editing
	case 'add':        		JobsToolbar::_EDIT(0);     	break;
	case 'edit':       		JobsToolbar::_EDIT(1);     	break;
		
	// Other views
	case 'categories':    	JobsToolbar::_CATS();   	break;
	case 'cancelcat': 		JobsToolbar::_CATS();     	break;
	case 'newcat':    		JobsToolbar::_EDITCAT(0); 	break;
	case 'editcat':   		JobsToolbar::_EDITCAT(1); 	break;
	
	case 'types':   		JobsToolbar::_TYPES();  	break;
	case 'canceltype': 		JobsToolbar::_TYPES();     	break;
	case 'newtype':    		JobsToolbar::_EDITTYPE(0); 	break;
	case 'edittype':   		JobsToolbar::_EDITTYPE(1); 	break;
	
	case 'jobs':     		JobsToolbar::_DEFAULT();   	break;
	
	default: JobsToolbar::_DEFAULT(); break;
}

?>