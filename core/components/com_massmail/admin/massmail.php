<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Admin;

if (!\User::authorise('core.manage', 'com_massmail'))
{
	return \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include models
require_once dirname(__DIR__) . DS . 'models' . DS . 'mailing.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'recipient.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'attachment.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'status.php';

// Include helpers
require_once __DIR__ . DS . 'helpers' . DS . 'permissions.php';

// Instantiate the requested controller. Entering via the Components menu
// (no controller specified) lands on the status dashboard.
$controllerName = \Request::getCmd('controller', 'status');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'status';
}

// Component submenu
$menuItems = array(
	'status'   => \Lang::txt('COM_MASSMAIL_DASHBOARD'),
	'mailings' => \Lang::txt('COM_MASSMAIL_MAILINGS')
);
foreach ($menuItems as $k => $v)
{
	$active = (\Request::getCmd('controller', 'status') == $k);
	\Submenu::addEntry($v, \Route::url('index.php?option=com_massmail&controller=' . $k), $active);
}

// A mass-mail on/off status pill, pinned to the right of the component
// sub-navigation so the send state is visible at a glance on every page.
// Styles/behavior live in external assets (CSP-friendly); state is handed to
// the script through the #mm-pill-data element below.
// A single traffic-light status for the whole send pipeline, folding the two
// gates -- the sending toggle and the delivery cron -- into one indicator:
//   red    = sending disabled (nothing goes out; new mailings are held)
//   orange = sending enabled but the cron is stopped (mail queues, doesn't move)
//   green  = sending enabled and the cron is running (normal operation)
$mmSendingOn = (int) \Component::params('com_massmail')->get('sending_enabled', 0);

$mmDb = \App::get('db');
$mmDb->setQuery("SELECT state FROM `#__cron_jobs` WHERE plugin = 'massmail' AND event = 'processMassmailQueue' LIMIT 1");
$mmCron   = $mmDb->loadObject();
$mmCronOn = ($mmCron && (int) $mmCron->state == 1);

if (!$mmSendingOn)
{
	$mmState  = 'red';
	$mmPillText = \Lang::txt('COM_MASSMAIL_STATUS_DOWN');
	$mmDetail = \Lang::txt('COM_MASSMAIL_STATUS_DOWN_DETAIL');
}
elseif (!$mmCronOn)
{
	$mmState  = 'orange';
	$mmPillText = \Lang::txt('COM_MASSMAIL_STATUS_DEGRADED');
	$mmDetail = \Lang::txt('COM_MASSMAIL_STATUS_DEGRADED_DETAIL');
}
else
{
	$mmState  = 'green';
	$mmPillText = \Lang::txt('COM_MASSMAIL_STATUS_OK');
	$mmDetail = \Lang::txt('COM_MASSMAIL_STATUS_OK_DETAIL');
}

$mmAssetBase = str_replace('/administrator', '', \Request::base(true)) . '/core/components/com_massmail/admin/assets';
$mmAssetDir  = PATH_CORE . DS . 'components' . DS . 'com_massmail' . DS . 'admin' . DS . 'assets';
$mmCssVer    = @filemtime($mmAssetDir . DS . 'css' . DS . 'component.css');
$mmJsVer     = @filemtime($mmAssetDir . DS . 'js' . DS . 'component.js');
\Document::addStyleSheet($mmAssetBase . '/css/component.css' . ($mmCssVer ? '?v=' . $mmCssVer : ''));
\Document::addScript($mmAssetBase . '/js/component.js' . ($mmJsVer ? '?v=' . $mmJsVer : ''));
echo '<div id="mm-pill-data" hidden data-state="' . $mmState . '"'
	. ' data-label="' . htmlspecialchars($mmPillText, ENT_QUOTES, 'UTF-8') . '"'
	. ' data-detail="' . htmlspecialchars($mmDetail, ENT_QUOTES, 'UTF-8') . '"></div>';

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';

$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);
$controller = new $controllerName();
$controller->execute();
