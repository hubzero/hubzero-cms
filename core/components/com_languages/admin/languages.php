<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_languages
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Access check.
if (!User::authorise('core.manage', 'com_languages'))
{
	return App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
}

$controller	= JControllerLegacy::getInstance('Languages');
$controller->execute(Request::getCmd('task'));
$controller->redirect();
