<?php
/**
 * Shared setup for the noVNC connection-recovery overlay.
 *
 * Registers reconnect.js / reconnect.css and emits a JSON config the engine
 * (default.php or vanilla.php) hands to NoVNCReconnect.init(). Included from
 * within a Hubzero\Plugin\View, so $this is the session view.
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css('./css/reconnect.css')
     ->js('./js/reconnect.js');

$base = rtrim(Request::root(), '/');

$reconnectConfig = array(
	'sess'    => (int) $this->app->sess,
	'apiBase' => $base . '/api/tools',
	'links'   => array(
		'account' => Route::url('index.php?option=com_members&task=myaccount'),
		'home'    => $base . '/'
	),
	'strings' => array(
		'lostTitle'    => Lang::txt('PLG_TOOLS_NOVNC_RECONNECT_LOST_TITLE'),
		'checking'     => Lang::txt('PLG_TOOLS_NOVNC_RECONNECT_CHECKING'),
		'waiting'      => Lang::txt('PLG_TOOLS_NOVNC_RECONNECT_WAITING'),
		'reconnecting' => Lang::txt('PLG_TOOLS_NOVNC_RECONNECT_RECONNECTING'),
		'statePrefix'  => Lang::txt('PLG_TOOLS_NOVNC_RECONNECT_STATE_PREFIX'),
		'stalled'      => Lang::txt('PLG_TOOLS_NOVNC_RECONNECT_STALLED'),
		'endedTitle'   => Lang::txt('PLG_TOOLS_NOVNC_RECONNECT_ENDED_TITLE'),
		'endedMsg'     => Lang::txt('PLG_TOOLS_NOVNC_RECONNECT_ENDED_MSG'),
		'displayTitle' => Lang::txt('PLG_TOOLS_NOVNC_RECONNECT_DISPLAY_TITLE'),
		'displayMsg'   => Lang::txt('PLG_TOOLS_NOVNC_RECONNECT_DISPLAY_MSG'),
		'authTitle'    => Lang::txt('PLG_TOOLS_NOVNC_RECONNECT_AUTH_TITLE'),
		'authMsg'      => Lang::txt('PLG_TOOLS_NOVNC_RECONNECT_AUTH_MSG'),
		'reload'       => Lang::txt('PLG_TOOLS_NOVNC_RECONNECT_RELOAD'),
		'account'      => Lang::txt('PLG_TOOLS_NOVNC_RECONNECT_ACCOUNT'),
		'home'         => Lang::txt('PLG_TOOLS_NOVNC_RECONNECT_HOME')
	)
);
?>
<script type="application/json" id="novnc-reconnect-config"><?php echo json_encode($reconnectConfig); ?></script>
