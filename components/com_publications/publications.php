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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if (JFactory::getConfig()->getValue('config.debug')) 
{
	error_reporting(E_ALL);
	@ini_set('display_errors','1');
}

if (version_compare(JVERSION, '1.6', 'lt'))
{
	$jacl = JFactory::getACL();
	$jacl->addACL($option, 'manage', 'users', 'super administrator');
	$jacl->addACL($option, 'manage', 'users', 'administrator');
	$jacl->addACL($option, 'manage', 'users', 'manager');
}
else
{
	$option = JRequest::getCmd('option','');
}


require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'publication.php');
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'version.php');
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'access.php');
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'audience.level.php');
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'audience.php');
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'author.php');
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'license.php');
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'master.type.php');
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'category.php');
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'screenshot.php');
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'attachment.php');
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'review.php');

require_once( JPATH_ROOT.DS.'components'.DS.$option.DS.'helpers'.DS.'usage.php' );
require_once( JPATH_ROOT.DS.'components'.DS.$option.DS.'helpers'.DS.'tags.php' );
require_once( JPATH_ROOT.DS.'components'.DS.$option.DS.'helpers'.DS.'html.php' );
require_once( JPATH_ROOT.DS.'components'.DS.$option.DS.'helpers'.DS.'helper.php' );

require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_projects'.DS.'tables'.DS.'project.php' );
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_projects'.DS.'tables'.DS.'project.owner.php' );

jimport('joomla.application.component.helper');
jimport('joomla.application.component.view');
ximport('Hubzero_View_Helper_Html');
ximport('Hubzero_Filter');
ximport('Hubzero_Group');
ximport('Hubzero_User_Helper');
ximport('Hubzero_Document');

$controllerName = JRequest::getCmd('controller', 'publications');
if (!file_exists(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'publications';
}
require_once(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'PublicationsController' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
