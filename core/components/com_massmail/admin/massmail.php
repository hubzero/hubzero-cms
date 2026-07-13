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
$mmSendingOn = (int) \Component::params('com_massmail')->get('sending_enabled', 0);
$mmPillClass = $mmSendingOn ? 'mm-pill-on' : 'mm-pill-off';
$mmPillText  = \Lang::txt($mmSendingOn ? 'COM_MASSMAIL_SENDING_ON' : 'COM_MASSMAIL_SENDING_OFF');
\Document::addStyleDeclaration(
	'#submenu li.mm-pill-li { float:right; margin:0; padding:0; border:none; }'
	. '.mm-status-pill { display:inline-flex; align-items:center; gap:6px; margin:2px 0; padding:4px 12px; border-radius:12px; font-size:12px; font-weight:600; line-height:1.4; }'
	. '.mm-status-pill .mm-dot { width:8px; height:8px; border-radius:50%; background:currentColor; }'
	. '.mm-pill-on { background:#c6f6d5; color:#22543d; }'
	. '.mm-pill-off { background:#fed7d7; color:#9b2c2c; }'
);
\Document::addScriptDeclaration(
	'document.addEventListener("DOMContentLoaded", function () {'
	. ' var ul = document.getElementById("submenu");'
	. ' if (!ul || document.querySelector(".mm-status-pill")) { return; }'
	. ' var li = document.createElement("li"); li.className = "mm-pill-li";'
	. ' var pill = document.createElement("span"); pill.className = "mm-status-pill ' . $mmPillClass . '";'
	. ' pill.innerHTML = \'<span class="mm-dot"></span>\' + ' . json_encode($mmPillText) . ';'
	. ' li.appendChild(pill); ul.appendChild(li);'
	. '});'
);

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';

$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);
$controller = new $controllerName();
$controller->execute();
