<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->js('setpassword')
     ->css('setpassword');
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_SET_PASSWORD'); ?></h2>
</header>

<section class="main section">
	<p class="error error-message"></p>
	<form action="<?php echo Route::url('index.php?option=com_members&controller=credentials&task=settingpassword'); ?>" method="post" name="hubForm" id="hubForm">
		<fieldset>
			<legend><?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_NEW_PASSWORD'); ?></legend>

			<p>
				<?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_PASSWORD_DESCRIPTION'); ?>
			</p>
			<label for="password1">
				<?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_PASSWORD1_LABEL'); ?>:
				<span class="required"><?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_REQUIRED'); ?></span>
			</label>
			<input type="password" name="password1" id="newpass" tabindex="1" />

			<label for="password2">
				<?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_PASSWORD2_LABEL'); ?>:
				<span class="required"><?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_REQUIRED'); ?></span>
			</label>
			<input type="password" name="password2" tabindex="2" />

			<?php if (count($this->password_rules) > 0) : ?>
				<ul id="passrules">
					<?php foreach ($this->password_rules as $rule) : ?>
						<?php if (!empty($rule)) : ?>
							<li class="empty"><?php echo $rule; ?></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" id="pass_no_html" name="no_html" value="0" />
		<p class="submit">
			<button type="submit" id="password-change-save">
				<?php echo Lang::txt('Submit'); ?>
			</button>
		</p>
		<?php echo Html::input('token'); ?>
	</form>
</section>