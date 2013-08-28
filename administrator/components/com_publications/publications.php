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

error_reporting(E_ALL);
@ini_set('display_errors','1');

$option = 'com_publications';

if (version_compare(JVERSION, '1.6', 'lt'))
{
	$jacl = JFactory::getACL();
	$jacl->addACL($option, 'manage', 'users', 'super administrator');
	$jacl->addACL($option, 'manage', 'users', 'administrator');
	$jacl->addACL($option, 'manage', 'users', 'manager');
	
	// Authorization check
	$user = JFactory::getUser();
	if (!$user->authorize($option, 'manage'))
	{
		$app = JFactory::getApplication();
		$app->redirect('index.php', JText::_('ALERTNOTAUTH'));
	}
}
else 
{
	if (!JFactory::getUser()->authorise('core.manage', $option)) 
	{
		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	}
}

jimport('joomla.application.component.helper');
ximport('Hubzero_View_Helper_Html');
ximport('Hubzero_Filter');

// Include scripts
require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'publication.php');
require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'author.php');
require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'attachment.php');
require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'category.php');
require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'access.php');
require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'review.php');
require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'audience.php');
require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'audience.level.php');
require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'license.php');
require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'master.type.php');
require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'screenshot.php');
require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'version.php');

require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'html.php');
require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'utilities.php');
require_once(JPATH_ROOT . DS . 'components' . DS . $option . DS . 'helpers' . DS . 'tags.php');

// Projects
require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'project.php');
require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'project.owner.php');
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects'. DS . 'helpers' . DS . 'html.php');
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_publications'. DS . 'helpers' . DS . 'helper.php');

$controllerName = JRequest::getCmd('controller', 'items');
if (!file_exists(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'items';
}

JSubMenuHelper::addEntry(
	JText::_('Publications'), 
	'index.php?option=' .  $option . '&controller=items', 
	$controllerName == 'items'
);

JSubMenuHelper::addEntry(
	JText::_('Licenses'), 
	'index.php?option=' .  $option . '&controller=licenses', 
	$controllerName == 'licenses'
);
JSubMenuHelper::addEntry(
	JText::_('Categories'), 
	'index.php?option=' .  $option . '&controller=categories', 
	$controllerName == 'categories'
);
JSubMenuHelper::addEntry(
	JText::_('Master Types'), 
	'index.php?option=' .  $option . '&controller=types', 
	$controllerName == 'types'
);

require_once(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'PublicationsController' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

