<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Admin;

if (!\User::authorise('core.manage', 'com_members'))
{
	return \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
require_once(dirname(__DIR__) . DS . 'models' . DS . 'member.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'admin.php');

$controllerName = \Request::getCmd('controller', 'members');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'members';
}

// Build sub-menu
$canDo = \Components\Members\Helpers\Admin::getActions();

\Submenu::addEntry(
	\Lang::txt('COM_MEMBERS'),
	\Route::url('index.php?option=com_members'),
	$controllerName == 'members'
);
\Submenu::addEntry(
	\Lang::txt('COM_MEMBERS_MENU_NOTES'),
	\Route::url('index.php?option=com_members&controller=notes'),
	$controllerName == 'notes'
);
\Submenu::addEntry(
	\Lang::txt('COM_MEMBERS_MENU_ACCESS'),
	\Route::url('index.php?option=com_members&controller=accessgroups'),
	($controllerName == 'accessgroups' || $controllerName == 'accesslevels')
);
/*\Submenu::addEntry(
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
}*/
if ($canDo->get('core.admin'))
{
	\Submenu::addEntry(
		\Lang::txt('COM_MEMBERS_MENU_PASSWORDS'),
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
		\Route::url('index.php?option=com_members&controller=imports'),
		($controllerName == 'imports' || $controllerName == 'importhooks')
	);
}
\Submenu::addEntry(
	\Lang::txt('COM_MEMBERS_MENU_PLUGINS'),
	\Route::url('index.php?option=com_members&controller=plugins'),
	$controllerName == 'plugins'
);

require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
