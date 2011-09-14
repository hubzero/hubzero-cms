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