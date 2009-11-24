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

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );
require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'contribtool.config.php' );
$tconfig = new ContribtoolConfig('com_contribtool');
$enabled = (isset($tconfig->parameters['contribtool_on'])) ? $tconfig->parameters['contribtool_on'] : 0;

//-----------
switch($task)
{
     case 'cancel':
	   	$toolid = JRequest::getInt('toolid',null,'method');
	   	$id = JRequest::getVar('id',null,'method');
	   	if (!is_null($toolid) && !is_null($id))
	   		ContribtoolToolbar::_VIEWTOOLVERSIONS(1,$toolid);
	   	else if (!is_null($toolid))
	   		ContribtoolToolbar::_VIEWTOOLS(1);
		else
			ContribtoolToolbar::_DEFAULT($enabled);
		break;
     case 'view':
	   	$toolid = JRequest::getInt('toolid',null,'method');
	   	if (!is_null($toolid))
	   		ContribtoolToolbar::_VIEWTOOLVERSIONS(1,$toolid);
		else 
			ContribtoolToolbar::_VIEWTOOLS(1);
	   	break;
     case 'apply':
	case 'edit':
	   	$toolid = JRequest::getInt('toolid',null,'method');
	   	$id = JRequest::getVar('id',null,'method');
	   	if (!is_null($toolid) && !is_null($id))
	   		ContribtoolToolbar::_EDITTOOLVERSION(1);
	   	else if (!is_null($toolid))
	   		ContribtoolToolbar::_EDITTOOL(1,$toolid);
		else
			ContribtoolToolbar::_DEFAULT($enabled);
	   	break;
    default:	
	   ContribtoolToolbar::_DEFAULT($enabled);
	   break;
}

?>
