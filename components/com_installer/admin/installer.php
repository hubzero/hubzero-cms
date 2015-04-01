<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.0
 */

defined('_JEXEC') or die;

// Access check.
if (!User::authorise('core.manage', 'com_installer'))
{
	return App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
}

$controller	= JControllerLegacy::getInstance('Installer');
$controller->execute(Request::getCmd('task'));
$controller->redirect();
