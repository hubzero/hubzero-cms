<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

// Access check.
if (!User::authorise('core.manage', 'com_templates'))
{
	return App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Register helper
JLoader::register('TemplatesHelper', dirname(__FILE__) . '/helpers/templates.php');

$controller	= JControllerLegacy::getInstance('Templates');
$controller->execute(Request::getCmd('task'));
$controller->redirect();
