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

$option = 'com_update';

if (!JFactory::getUser()->authorise('core.admin', $option))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'cli.php';

$controllerName = JRequest::getCmd('controller', 'dashboard');

if (!file_exists(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'dashboard';
}

require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php';

$controllerName = 'UpdateController' . ucfirst(strtolower($controllerName));

JSubMenuHelper::addEntry(
	JText::_('Dashboard'),
	'index.php?option='.$option.'&controller=dashboard',
	$controllerName == 'UpdateControllerDashboard'
);
JSubMenuHelper::addEntry(
	JText::_('Repository'),
	'index.php?option='.$option.'&controller=repository',
	$controllerName == 'UpdateControllerRepository'
);
JSubMenuHelper::addEntry(
	JText::_('Database'),
	'index.php?option='.$option.'&controller=database',
	$controllerName == 'UpdateControllerDatabase'
);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
