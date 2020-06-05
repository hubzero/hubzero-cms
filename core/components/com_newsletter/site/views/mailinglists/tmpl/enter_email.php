<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$mylistIds = array();

$this->css()
     ->js();
?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul>
			<li>
				<a class="btn icon-browse" href="<?php echo Route::url('index.php?option=com_newsletter'); ?>">
					<?php echo Lang::txt('COM_NEWSLETTER_BROWSE'); ?>
				</a>
			</li>
		</ul>
	</div>
</header>

<section class="main section">
	<?php
		if ($this->getError())
		{
			echo '<p class="error">' . $this->getError() . '</p>';
		}
	?>
	<div class="subscribe">
		<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=subscribe'); ?>" method="post" id="hubForm">
			<fieldset>
				<legend><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_GUEST_PROMPT'); ?></legend>

				<label for="email"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_EMAIL'); ?></label>
				<input type="email" name="e" id="email" placeholder="you@example.com" />
			</fieldset>
			<p class="submit">
				<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_CONTINUE_GUEST'); ?>">
				<a class="btn" href="<?php echo $this->redirect; ?>"><?php echo Lang::txt('COM_NEWSLETTER_LOGIN'); ?></a>
			</p>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="subscribe" />
		</form>
	</div>
</section>
