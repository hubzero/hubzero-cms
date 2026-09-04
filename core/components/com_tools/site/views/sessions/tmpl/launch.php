<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css('launch.css')
     ->js('launch.js');

$base = rtrim(Request::root(), '/');

$config = array(
	'app'     => $this->app,
	'version' => $this->version,
	'mode'    => $this->mode,
	'base'    => $base,
	// Routed (SEF) viewer URL with a placeholder the JS fills in with the sess
	'viewerUrl' => Route::url('index.php?option=com_tools&controller=sessions&app=' . urlencode($this->app) . '&task=session&sess=__SESS__', false),
	'strings' => array(
		'starting'    => Lang::txt('COM_TOOLS_LAUNCH_STARTING'),
		'waiting'     => Lang::txt('COM_TOOLS_LAUNCH_WAITING'),
		'connecting'  => Lang::txt('COM_TOOLS_LAUNCH_CONNECTING'),
		'opening'     => Lang::txt('COM_TOOLS_LAUNCH_OPENING'),
		'loadingView' => Lang::txt('COM_TOOLS_LAUNCH_LOADING_VIEWER'),
		'errStart'    => Lang::txt('COM_TOOLS_LAUNCH_ERROR_START'),
		'errExited'   => Lang::txt('COM_TOOLS_LAUNCH_ERROR_EXITED'),
		'errDisplay'  => Lang::txt('COM_TOOLS_LAUNCH_ERROR_DISPLAY'),
		'errTimeout'  => Lang::txt('COM_TOOLS_LAUNCH_ERROR_TIMEOUT')
	)
);

$classicUrl = Route::url('index.php?option=com_tools&controller=sessions&app=' . urlencode($this->app) . '&task=invoke&version=' . urlencode($this->version) . '&launch=legacy');
?>
<div class="main section">
	<div id="tool-launch" class="tool-launch">
		<script type="application/json" id="tool-launch-config"><?php echo json_encode($config); ?></script>

		<div class="tool-launch-card">
			<h2 class="tool-launch-heading"><?php echo Lang::txt('COM_TOOLS_LAUNCH_HEADING'); ?></h2>
			<p class="tool-launch-tool"><?php echo $this->escape($this->toolTitle); ?></p>

			<div id="launch-spinner" class="tool-launch-spinner" aria-hidden="true"><span></span><span></span><span></span></div>

			<ol class="tool-launch-steps" aria-live="polite">
				<li id="step-session" class="wait"><?php echo Lang::txt('COM_TOOLS_LAUNCH_STEP_SESSION'); ?></li>
				<li id="step-token" class="wait"><?php echo Lang::txt('COM_TOOLS_LAUNCH_STEP_TOKEN'); ?></li>
				<?php if ($this->mode == 'vnc') : ?>
					<li id="step-display" class="wait"><?php echo Lang::txt('COM_TOOLS_LAUNCH_STEP_DISPLAY'); ?></li>
				<?php endif; ?>
				<li id="step-connect" class="wait"><?php echo Lang::txt('COM_TOOLS_LAUNCH_STEP_CONNECT'); ?></li>
			</ol>

			<p id="launch-status" class="tool-launch-status" role="status" aria-live="polite"><?php echo Lang::txt('COM_TOOLS_LAUNCH_STARTING'); ?></p>

			<div id="launch-error" class="tool-launch-error" hidden>
				<p id="launch-error-msg" class="tool-launch-error-msg"></p>
				<p class="tool-launch-actions">
					<a href="#" id="launch-retry" class="btn"><?php echo Lang::txt('COM_TOOLS_LAUNCH_RETRY'); ?></a>
					<a href="<?php echo $classicUrl; ?>" id="launch-classic" class="btn btn-secondary"><?php echo Lang::txt('COM_TOOLS_LAUNCH_CLASSIC'); ?></a>
				</p>
			</div>

			<p class="tool-launch-cancel">
				<a href="#" id="launch-cancel"><?php echo Lang::txt('COM_TOOLS_LAUNCH_CANCEL'); ?></a>
			</p>

			<noscript>
				<p class="tool-launch-status"><?php echo Lang::txt('COM_TOOLS_LAUNCH_NOSCRIPT'); ?>
					<a href="<?php echo $classicUrl; ?>"><?php echo Lang::txt('COM_TOOLS_LAUNCH_CLASSIC'); ?></a>
				</p>
			</noscript>
		</div>
	</div>
</div>
