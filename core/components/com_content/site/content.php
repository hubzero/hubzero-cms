<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

// Include dependencies
require_once __DIR__.'/helpers/route.php';
require_once __DIR__.'/helpers/query.php';

$controller = JControllerLegacy::getInstance('Content');
$controller->execute(Request::getCmd('task'));
$controller->redirect();
