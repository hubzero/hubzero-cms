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

namespace Components\Oaipmh\Admin;

if (!\User::authorise('core.manage', 'com_oaipmh')) 
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

$controllerName = \Request::getCmd('controller', 'config');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'config';
}
require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

$task = \Request::getCmd('task');

\JSubMenuHelper::addEntry(
	\Lang::txt('COM_OAIPMH_ABOUT'),
	\Route::url('index.php?option=com_oaipmh'),
	(!$task || $task == 'display')
);
\JSubMenuHelper::addEntry(
	\Lang::txt('COM_OAIPMH_SCHEMAS'),
	\Route::url('index.php?option=com_oaipmh&task=schemas'),
	($task == 'schemas')
);
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_plugins' . DS . 'admin' . DS . 'helpers' . DS . 'plugins.php');
if (\PluginsHelper::getActions()->get('core.manage'))
{
	\JSubMenuHelper::addEntry(
		\Lang::txt('COM_OAIPMH_PLUGINS'),
		\Route::url('index.php?option=com_plugins&view=plugins&filter_folder=oaipmh&filter_type=oaipmh')
	);
}

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
