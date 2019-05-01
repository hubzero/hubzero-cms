<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<div class="width-100">
	<fieldset title="<?php echo Lang::txt('COM_CONFIG_FTP_DETAILS'); ?>" class="adminform">
		<legend><span><?php echo Lang::txt('COM_CONFIG_FTP_DETAILS'); ?></span></legend>
		<?php echo Lang::txt('COM_CONFIG_FTP_DETAILS_TIP'); ?>

		<?php if ($this->ftp instanceof Exception): ?>
			<p><?php echo Lang::txt($this->ftp->message); ?></p>
		<?php endif; ?>

		<div class="input-wrap">
			<label for="username"><?php echo Lang::txt('JGLOBAL_USERNAME'); ?></label>
			<input type="text" id="username" name="username" class="input_box" size="70" value="" />
		</div>

		<div class="input-wrap">
			<label for="password"><?php echo Lang::txt('JGLOBAL_PASSWORD'); ?></label>
			<input type="password" id="password" name="password" class="input_box" size="70" value="" />
		</div>
	</fieldset>
</div>
