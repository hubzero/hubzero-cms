<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Members\Admin;

if (!\User::authorise('core.manage', 'com_members'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'imghandler.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'profile.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'association.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'password_rules.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'password_blacklist.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'quotas_classes.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'users_quotas.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php');

$controllerName = \Request::getCmd('controller', 'members');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'members';
}

$canDo = \Components\Members\Helpers\Permissions::getActions('component');

\Submenu::addEntry(
	\Lang::txt('COM_MEMBERS'),
	\Route::url('index.php?option=com_members'),
	$controllerName == 'members'
);
\Submenu::addEntry(
	\Lang::txt('COM_MEMBERS_MENU_ONLINE'),
	\Route::url('index.php?option=com_members&controller=whosonline'),
	$controllerName == 'whosonline'
);
\Submenu::addEntry(
	\Lang::txt('COM_MEMBERS_MENU_MESSAGING'),
	\Route::url('index.php?option=com_members&controller=messages'),
	$controllerName == 'messages'
);
if (\Component::params('com_members')->get('bankAccounts'))
{
	\Submenu::addEntry(
		\Lang::txt('COM_MEMBERS_MENU_POINTS'),
		\Route::url('index.php?option=com_members&controller=points'),
		$controllerName == 'points'
	);
}
\Submenu::addEntry(
	\Lang::txt('COM_MEMBERS_MENU_PLUGINS'),
	\Route::url('index.php?option=com_members&controller=plugins'),
	$controllerName == 'plugins'
);

if ($canDo->get('core.admin'))
{
	\Submenu::addEntry(
		\Lang::txt('COM_MEMBERS_PASSWORDS'),
		\Route::url('index.php?option=com_members&controller=passwordrules'),
		($controllerName == 'passwordrules' || $controllerName == 'passwordblacklist')
	);
}

\Submenu::addEntry(
	\Lang::txt('COM_MEMBERS_MENU_QUOTAS'),
	\Route::url('index.php?option=com_members&controller=quotas'),
	$controllerName == 'quotas'
);
\Submenu::addEntry(
	\Lang::txt('COM_MEMBERS_MENU_REGISTRATION'),
	\Route::url('index.php?option=com_members&controller=registration'),
	(in_array($controllerName, array('registration', 'organizations', 'employers', 'incremental', 'premis')))
);

if ($canDo->get('core.admin'))
{
	\Submenu::addEntry(
		\Lang::txt('COM_MEMBERS_MENU_IMPORT'),
		\Route::url('index.php?option=com_members&controller=import'),
		($controllerName == 'import' || $controllerName == 'importhooks')
	);
}

require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

