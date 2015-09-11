<?php
/**
 * @package     hubzero.cms.admin
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando sudheera@xconsole.org
 * @copyright   Copyright 2010-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

if (\Config::get('debug')) {
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
} else {
	error_reporting(0);
	ini_set('display_errors', '0');
}

require_once(JPATH_COMPONENT . DS . 'config.php');

// Libs
require_once(JPATH_COMPONENT . DS . 'libs' . DS . 'lib_messages.php');
require_once(JPATH_COMPONENT . DS . 'libs' . DS . 'lib_security.php');
require_once(JPATH_COMPONENT . DS . 'libs' . DS . 'lib_json.php');


$document = App::get('document');

// CSRF token
$document->addCustomTag('<meta name="csrf-token" content="' . DB_RID . '" />');

$document->addStyleSheet(DB_PATH . '/html/smoothness/jquery-ui.css');
$document->addStyleSheet(DB_PATH . '/html/main.css');

$document->addScript(DB_PATH . '/html/' . 'main.js');

$document->setTitle($conf['app_title']);

require_once(JPATH_COMPONENT . DS . 'controller.php');
controller_exec();

// Restore umask
umask($conf['sys_umask']);
?>
