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

namespace Components\Update\Admin;

$option = 'com_update';

if (!User::authorise('core.admin', $option))
{
	return App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once dirname(__DIR__) . DS . 'helpers' . DS . 'cli.php';

$controllerName = Request::getCmd('controller', 'dashboard');

if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'dashboard';
}

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';

$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

\JSubMenuHelper::addEntry(
	Lang::txt('Dashboard'),
	'index.php?option='.$option.'&controller=dashboard',
	$controllerName == __NAMESPACE__ . '\\Controllers\\' . 'Dashboard'
);
\JSubMenuHelper::addEntry(
	Lang::txt('Repository'),
	'index.php?option='.$option.'&controller=repository',
	$controllerName == __NAMESPACE__ . '\\Controllers\\' . 'Repository'
);
\JSubMenuHelper::addEntry(
	Lang::txt('Database'),
	'index.php?option='.$option.'&controller=database',
	$controllerName == __NAMESPACE__ . '\\Controllers\\' . 'Database'
);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();