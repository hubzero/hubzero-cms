<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();



require_once __DIR__ . DS . 'controller.php';
require_once __DIR__ . DS . 'dv_config.php';

require_once __DIR__ . DS . 'lib' . DS. 'html.php';

require_once __DIR__ . DS . 'lib/db.php';
require_once __DIR__ . DS . 'lib/dl.php';

$document = App::get('document');

global $html_path, $com_name, $dv_conf;

$html_path = str_replace(PATH_ROOT, '', __DIR__) . '/html';
$com_name = basename(dirname(__DIR__));
$com_name = str_replace('com_', '', $com_name);
$com_name = trim($com_name, DS);
$dv_conf['settings']['com_name'] = $com_name;

controller();
return;
