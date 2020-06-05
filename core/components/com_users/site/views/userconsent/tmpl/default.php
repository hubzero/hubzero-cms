<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('userconsent.css');
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_USERS_USERCONSENT'); ?></h2>
</header>

<section class="section consent">
	<div><?php echo Lang::txt('COM_USERS_USERCONSENT_MESSAGE'); ?></div>

	<form method="post" action="<?php echo Route::url('index.php?option=' . $this->option . '&task=user.consent'); ?>">
		<input type="hidden" name="return" value="<?php echo base64_encode(Request::current(true)); ?>" />
		<?php echo Html::input('token'); ?>
		<div class="actions">
			<a class="btn btn-secondary" href="/"><?php echo Lang::txt('COM_USERS_USERCONSENT_CANCEL'); ?></a>
			<button class="btn btn-success" type="submit"><?php echo Lang::txt('COM_USERS_USERCONSENT_AGREE'); ?></button>
		</div>
	</form>
</section>