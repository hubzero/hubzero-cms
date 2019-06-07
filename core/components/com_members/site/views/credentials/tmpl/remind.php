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
	<h2><?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_REMIND'); ?></h2>
</header>

<section class="main section">
	<form action="<?php echo Route::url('index.php?option=com_members&controller=credentials&task=reminding'); ?>" method="post" name="hubForm" id="hubForm">
		<div class="explaination">
			<p class="info">
				If you already know your username, and only need your password reset,
				<a href="<?php echo Route::url('index.php?option=com_members&task=reset'); ?>">go here now</a>.
			</p>
		</div>
		<fieldset>
			<legend>Recover Username(s)</legend>

			<p>
				<?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_REMIND_EMAIL_DESCRIPTION'); ?>
			</p>
			<label for="email">
				<?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_REMIND_EMAIL_LABEL'); ?>:
				<span class="required"><?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_REQUIRED'); ?></span>
			</label>
			<input type="text" name="email" />

			<div class="help">
				<h4>What if I have also lost my password?</h4>
				<p>
					Fill out this form to retrieve your username(s). Then go to the 
					<a href="<?php echo Route::url('index.php?option=com_members&task=reset'); ?>">password reset page</a>.
				</p>

				<h4>What if I have multiple accounts?</h4>
				<p>
					All accounts registered to your email address will be located, and you will be given a
					list of all of those usernames.
				</p>

				<h4>What if this cannot find my account?</h4>
				<p>
					It is possible you registered under a different email address.  Please try any other email
					addresses you have.
				</p>
			</div>
		</fieldset>
		<div class="clear"></div>

		<p class="submit"><button type="submit"><?php echo Lang::txt('Submit'); ?></button></p>
		<?php echo Html::input('token'); ?>
	</form>
</section>