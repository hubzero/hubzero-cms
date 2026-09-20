<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

// The admin application gates only on core.login.admin, not per component, so
// what separates one component's managers from another's is this test at the
// entry file -- 47 of the 55 admin entries make it. This one did not, and the
// component's own authorized() returns true for everyone whenever
// access_limit_to_group is unset, which is the default. Any account that can
// reach the admin area at all could therefore use the data viewer's admin.
if (!\User::authorise('core.manage', 'com_dataviewer'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once __DIR__ . DS . 'config.php';

// Libs
require_once __DIR__ . DS . 'libs' . DS . 'lib_messages.php';
require_once __DIR__ . DS . 'libs' . DS . 'lib_security.php';
require_once __DIR__ . DS . 'libs' . DS . 'lib_json.php';


$document = App::get('document');

// CSRF token
$document->addCustomTag('<meta name="csrf-token" content="' . DB_RID . '" />');

$document->addStyleSheet(DB_PATH . '/html/smoothness/jquery-ui.css');
$document->addStyleSheet(DB_PATH . '/html/main.css');

$document->addScript(DB_PATH . '/html/' . 'main.js');

$document->setTitle($conf['app_title']);

require_once __DIR__ . DS . 'controller.php';
controller_exec();

// Restore umask
umask($conf['sys_umask']);
