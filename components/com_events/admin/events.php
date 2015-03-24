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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Events\Admin;

if (!\User::authorise('core.manage', 'com_events'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once(dirname(__DIR__) . DS . 'models' . DS . 'tags.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'date.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'category.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'event.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'config.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'page.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'respondent.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'html.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'csv.php');

$controllerName = \Request::getCmd('controller', 'events');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'events';
}

\JSubMenuHelper::addEntry(
	\Lang::txt('COM_EVENTS'),
	\Route::url('index.php?option=com_events&controller=events'),
	$controllerName == 'events'
);
\JSubMenuHelper::addEntry(
	\Lang::txt('COM_EVENTS_CATEGORIES'),
	\Route::url('index.php?option=com_categories&extension=com_events'),
	$controllerName == 'categories'
);
\JSubMenuHelper::addEntry(
	\Lang::txt('COM_EVENTS_CONFIGURATION'),
	\Route::url('index.php?option=com_events&controller=configure'),
	$controllerName == 'configure'
);

require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
