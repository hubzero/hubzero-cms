<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Oaipmh;

if (!\JFactory::getUser()->authorise('core.manage', 'com_oaipmh')) 
{
	return \JError::raiseWarning(404, \JText::_('JERROR_ALERTNOAUTHOR'));
}

$controllerName = \JRequest::getCmd('controller', 'config');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'config';
}
require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

\JSubMenuHelper::addEntry(
	\JText::_('COM_OAIPMH_ABOUT'),
	\JRoute::_('index.php?option=com_oaipmh'),
	true
);
require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_plugins' . DS . 'helpers' . DS . 'plugins.php');
if (\PluginsHelper::getActions()->get('core.manage'))
{
	\JSubMenuHelper::addEntry(
		\JText::_('COM_OAIPMH_PLUGINS'),
		\JRoute::_('index.php?option=com_plugins&view=plugins&filter_folder=oaipmh&filter_type=oaipmh')
	);
}

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
