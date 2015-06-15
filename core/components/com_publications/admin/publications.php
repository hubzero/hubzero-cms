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

namespace Components\Publications\Admin;

$option = Request::getCmd('option','com_publications');
$task = Request::getWord('task', '');

if (!\User::authorise('core.manage', 'com_publications'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
require_once(dirname(__DIR__) . DS . 'models' . DS . 'publication.php');

// get controller name
$controllerName = Request::getCmd('controller', 'items');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'items';
}

\JSubMenuHelper::addEntry(
	Lang::txt('COM_PUBLICATIONS_PUBLICATIONS'),
	'index.php?option=' .  $option . '&controller=items',
	$controllerName == 'items'
);
\JSubMenuHelper::addEntry(
	Lang::txt('COM_PUBLICATIONS_LICENSES'),
	'index.php?option=' .  $option . '&controller=licenses',
	$controllerName == 'licenses'
);
\JSubMenuHelper::addEntry(
	Lang::txt('COM_PUBLICATIONS_CATEGORIES'),
	'index.php?option=' .  $option . '&controller=categories',
	$controllerName == 'categories'
);
\JSubMenuHelper::addEntry(
	Lang::txt('COM_PUBLICATIONS_MASTER_TYPES'),
	'index.php?option=' .  $option . '&controller=types',
	$controllerName == 'types'
);
\JSubMenuHelper::addEntry(
	Lang::txt('COM_PUBLICATIONS_BATCH_CREATE'),
	'index.php?option=' .  $option . '&controller=batchcreate',
	$controllerName == 'batchcreate'
);

require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
