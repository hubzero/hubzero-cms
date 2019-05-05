<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_RESET'); ?></h2>
</header>

<section class="main section">
	<form action="<?php echo Route::url('index.php?option=com_members&controller=credentials&task=resetting'); ?>" method="post" name="hubForm" id="hubForm">
		<div class="explaination">
			<p class="info">
				<?php echo Lang::txt(
					'Forgot your username? Go <a href="%s">here</a> to recover it.',
					Route::url('index.php?option=com_members&task=remind')
				); ?>
			</p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_EMAIL_VERIFICATION_TOKEN'); ?></legend>

			<p>
				<?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_RESET_PASSWORD_DESCRIPTION'); ?>
			</p>
			<label for="username">
				<?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_RESET_PASSWORD_LABEL'); ?>:
				<span class="required"><?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_REQUIRED'); ?></span>
			</label>
			<input type="text" name="username" />
		</fieldset>
		<div class="clear"></div>

		<p class="submit"><button type="submit"><?php echo Lang::txt('Submit'); ?></button></p>
		<?php echo Html::input('token'); ?>
	</form>
</section>