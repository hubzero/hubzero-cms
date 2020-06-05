<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$this->css('userconsent');
?>

<section class="userconsent">
	<div class="wrap">
		<div class="title">
			<h2><?php echo Lang::txt('COM_LOGIN_USERCONSENT'); ?></h2>
		</div>

		<div><?php echo Lang::txt('COM_LOGIN_USERCONSENT_MESSAGE'); ?></div>

		<form method="POST" action="<?php echo Route::url('index.php?option=com_login&task=grantconsent'); ?>">
			<input type="hidden" name="return" value="<?php echo base64_encode(Request::current(true)); ?>" />
			<div class="actions">
				<button class="btn btn-success" type="submit"><?php echo Lang::txt('COM_LOGIN_USERCONSENT_AGREE'); ?></button>
				<a class="btn btn-secondary" href="/"><?php echo Lang::txt('JCANCEL'); ?></a>
			</div>
		</form>
	</div>
</section>