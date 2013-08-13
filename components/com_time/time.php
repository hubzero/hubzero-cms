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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$config = JFactory::getConfig();
$option = JRequest::getCmd('option', 'com_time');

jimport('joomla.application.component.view');
jimport('joomla.filesystem.folder');
ximport('Hubzero_View_Helper_Html');
ximport('Hubzero_User_Helper');
ximport('Hubzero_Plugin_Params');

require_once(JPATH_ROOT.DS.'components'.DS.$option.DS.'controllers'.DS.'time.php');

if (version_compare(JVERSION, '1.6', 'lt'))
{
	$jacl =& JFactory::getACL();
	$jacl->addACL( $option, 'manage', 'users', 'super administrator' );
	$jacl->addACL( $option, 'manage', 'users', 'administrator' );
	$jacl->addACL( $option, 'manage', 'users', 'manager' );
}

// Instantiate controller
$controller = new TimeController();
$controller->execute();
$controller->redirect();
