<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$option = JRequest::getCmd('option', 'com_cron');

if (!JFactory::getUser()->authorise('core.manage', $option))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once(JPATH_COMPONENT_SITE . DS . 'models' . DS . 'jobs.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'cron.php');

$controllerName = JRequest::getCmd('controller', 'jobs');
if (!file_exists(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'jobs';
}

JSubMenuHelper::addEntry(
	JText::_('COM_CRON_JOBS'),
	'index.php?option=' .  $option . '&controller=jobs',
	($controllerName == 'jobs')
);

require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_plugins' . DS . 'helpers' . DS . 'plugins.php');
$canDo = PluginsHelper::getActions();
if ($canDo->get('core.manage'))
{
	JSubMenuHelper::addEntry(
		JText::_('COM_CRON_PLUGINS'),
		'index.php?option=com_plugins&view=plugins&filter_folder=cron&filter_type=cron'
	);
}

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'CronController' . ucfirst($controllerName);

// initiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
