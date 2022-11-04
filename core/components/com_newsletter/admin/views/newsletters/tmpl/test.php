<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//set title
Toolbar::title(Lang::txt('COM_NEWSLETTER_TEST_SENDING') . ': ' . $this->newsletter->name, 'newsletter');

//add buttons to toolbar
Toolbar::custom('dosendtest', 'send', '', 'COM_NEWSLETTER_TOOLBAR_SEND_TEST', false);
Toolbar::cancel();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm">
	<div class="col span12">
		<?php if ($this->newsletter->id != null) : ?>
			<fieldset class="adminform" id="distribution">
				<legend><?php echo Lang::txt('COM_NEWSLETTER_TEST_SENDING'); ?></legend>
				<table class="admintable">
					<tbody>
						<tr>
							<th><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER'); ?>:</th>
							<td>
								<?php echo $this->escape($this->newsletter->name); ?>
							</td>
						</tr>
						<tr>
							<th>
								<?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEST_EMAILS'); ?>:<br />
								<span class="hint"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEST_EMAILS_HINT'); ?></span>
							</th>
							<td>
								<input type="text" name="emails" placeholder="<?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEST_EMAILS_PLACEHOLDER'); ?>" autocomplete="off" />
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		<?php endif; ?>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="dosendtest" />
	<input type="hidden" name="nid" value="<?php echo $this->newsletter->id; ?>" />

	<?php echo Html::input('token'); ?>
</form>