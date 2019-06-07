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
	<h2><?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_VERIFY'); ?></h2>
</header>

<section class="main section">
	<form action="<?php echo Route::url('index.php?option=com_members&controller=credentials&task=verifying'); ?>" method="post" name="hubForm" id="hubForm">
		<fieldset>
			<legend><?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_CONFIRM_VERIFICATION_TOKEN'); ?></legend>

			<p>
				<?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_VERIFICATION_TOKEN_DESCRIPTION'); ?>
			</p>
			<label for="token">
				<?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_VERIFICATION_TOKEN_LABEL'); ?>:
				<span class="required"><?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_REQUIRED'); ?></span>
			</label>
			<input type="text" name="token" />
		</fieldset>
		<div class="clear"></div>

		<p class="submit"><button type="submit"><?php echo Lang::txt('Submit'); ?></button></p>
		<?php echo Html::input('token'); ?>
	</form>
</section>