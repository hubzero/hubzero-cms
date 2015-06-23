<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

// Access check.
if (!User::authorise('core.manage', 'com_users'))
{
	return App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Register helper class
JLoader::register('UsersHelper', dirname(__FILE__) . '/helpers/users.php');

// Execute the task.
$controller	= JControllerLegacy::getInstance('Users');
$controller->execute(Request::getCmd('task'));
$controller->redirect();
