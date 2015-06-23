<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

// Access check.
if (!User::authorise('core.manage', Request::getCmd('extension')))
{
	return App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Execute the task.
$controller	= JControllerLegacy::getInstance('Categories');
$controller->execute(Request::getVar('task'));
$controller->redirect();
