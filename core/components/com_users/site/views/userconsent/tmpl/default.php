<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

\Hubzero\Document\Assets::addComponentStylesheet('com_users', 'userconsent.css');
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_USERS_USERCONSENT'); ?></h2>
</header>

<section class="section consent">
	<div><?php echo Lang::txt('COM_USERS_USERCONSENT_MESSAGE'); ?></div>

	<form method="POST" action="<?php echo Route::url('index.php?option=com_users&task=user.consent'); ?>">
		<input type="hidden" name="return" value="<?php echo base64_encode(Request::current(true)); ?>" />
		<div class="actions">
			<a class="btn btn-secondary" href="/"><?php echo Lang::txt('COM_USERS_USERCONSENT_CANCEL'); ?></a>
			<button class="btn btn-success" type="submit"><?php echo Lang::txt('COM_USERS_USERCONSENT_AGREE'); ?></button>
		</div>
	</form>
</section>