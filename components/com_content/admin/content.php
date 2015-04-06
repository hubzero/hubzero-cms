<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Access check.
if (!User::authorise('core.manage', 'com_content')) {
	return App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Register helper class
JLoader::register('ContentHelper', dirname(__FILE__) . '/helpers/content.php');

$controller = JControllerLegacy::getInstance('Content');
$controller->execute(Request::getCmd('task'));
$controller->redirect();
