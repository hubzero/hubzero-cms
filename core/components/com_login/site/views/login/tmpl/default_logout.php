<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

// If the user is already logged in, redirect to the return or profile page.
if (!User::isGuest())
{
	$return = base64_decode(Request::getString('return', ''));

	if ($return)
	{
		App::redirect(Route::url($return, false));
		return;
	}

	// Redirect to profile page.
	App::redirect(Route::url('index.php?option=com_members&task=myaccount', false));
	return;
}
?>
<div class="logout<?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	<?php endif; ?>

	<?php if (($this->params->get('logoutdescription_show') == 1 && str_replace(' ', '', $this->params->get('logout_description')) != '') || $this->params->get('logout_image') != '') : ?>
	<div class="logout-description">
	<?php endif; ?>

		<?php if ($this->params->get('logoutdescription_show') == 1) : ?>
			<?php echo $this->params->get('logout_description'); ?>
		<?php endif; ?>

		<?php if (($this->params->get('logout_image')!='')) :?>
			<img src="<?php echo $this->escape($this->params->get('logout_image')); ?>" class="logout-image" alt="<?php echo Lang::txt('COM_USER_LOGOUT_IMAGE_ALT')?>"/>
		<?php endif; ?>

	<?php if (($this->params->get('logoutdescription_show') == 1 && str_replace(' ', '', $this->params->get('logout_description')) != '') || $this->params->get('logout_image') != '') : ?>
	</div>
	<?php endif; ?>

	<form action="<?php echo Route::url('index.php?option=com_login&task=logout'); ?>" method="post">
		<div>
			<button type="submit" class="button"><?php echo Lang::txt('JLOGOUT'); ?></button>
			<input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('logout_redirect_url', $this->form->getValue('return'))); ?>" />
			<?php echo Html::input('token'); ?>
		</div>
	</form>
</div>
