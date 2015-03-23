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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Jobs\Admin;

if (!\JFactory::getUser()->authorise('core.manage', 'com_jobs'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'admin.php');
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'application.php');
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'category.php');
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'employer.php');
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'job.php');
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'prefs.php');
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'resume.php');
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'seeker.php');
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'shortlist.php');
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'stats.php');
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'type.php');
include_once(dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php');
include_once(dirname(__DIR__) . DS . 'helpers' . DS . 'html.php');

$controllerName = \JRequest::getCmd('controller', 'jobs');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'jobs';
}

\JSubMenuHelper::addEntry(
	\Lang::txt('COM_JOBS_JOBS'),
	\Route::url('index.php?option=com_jobs&controller=jobs'),
	$controllerName == 'jobs'
);
\JSubMenuHelper::addEntry(
	\Lang::txt('COM_JOBS_CATEGORIES'),
	\Route::url('index.php?option=com_jobs&controller=categories'),
	$controllerName == 'categories'
);
\JSubMenuHelper::addEntry(
	\Lang::txt('COM_JOBS_TYPES'),
	\Route::url('index.php?option=com_jobs&controller=types'),
	$controllerName == 'types'
);

require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Initiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

