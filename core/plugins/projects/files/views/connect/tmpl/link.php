<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$google = $this->connect->getConfigs('google');
$dropbox = $this->connect->getConfigs('dropbox');

// Some connection active
$active = ($google['active'] || $dropbox['active']) ? 1 : 0;
$on = ($google['on'] || $dropbox['on']) ? 1 : 0;

// Project creator
$creator = ($this->model->access('owner')) ? 1 : 0;

$limited = $this->params->get('connectedProjects') ? \Components\Projects\Helpers\Html::getParamArray($this->params->get('connectedProjects')) : array();

$authorized = (empty($limited) || (!empty($limited) && in_array($this->model->get('alias'), $limited))) ? true : false;

$connected = (($google && $this->oparams->get('google_token')) || ($dropbox && $this->oparams->get('dropbox_token'))) ? 1 : 0;
?>
<?php if ($on && (($google || $dropbox) && $active || (!$active && $creator && $authorized))) { ?>
<p id="connector">
	<span>
		<?php if (!$active || !$connected) {  ?>
		<?php if ($google) { ?>
		<span class="google"></span>
		<?php } ?>
		<?php if ($dropbox) { ?>
		<span class="dropbox"></span>
		<?php } ?>
		<a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=files&action=connect'); ?>"><?php echo Lang::txt('PLG_PROJECTS_FILES_CONNECT'); ?></a>
		<?php }
			// Connected to Google
			if ($this->oparams->get('google_token') && $active) {  ?>
				<span class="connect-email"><span class="google"></span> <?php echo $this->oparams->get('google_email'); ?> <a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=files') . '?action=connect'; ?>">[&raquo;]</a></span>
		<?php } ?>
	</span>
</p>
<?php } else { ?>
	<p class="editing mini pale"><?php echo Lang::txt('PLG_PROJECTS_FILES_MAX_UPLOAD') . ' ' . $this->sizelimit; ?></p>
<?php }
