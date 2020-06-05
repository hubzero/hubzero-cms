<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//set title
Toolbar::title(Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILINGLISTS') . ': ' . $this->list->name, 'list');

//add toolbar buttons
Toolbar::custom('doaddemail', 'save', '', 'COM_NEWSLETTER_TOOLBAR_SUBMIT', false);
Toolbar::cancel('cancelemail');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" enctype="multipart/form-data">
	<fieldset class="adminform">
		<legend><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS'); ?></legend>

		<table class="admintable">
			<tbody>
				<tr>
					<th><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_MAILINGLIST'); ?>:</th>
					<td><strong><?php echo $this->list->name; ?></strong></td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_CONFIRMATION'); ?>:</th>
					<td>
						<select name="email_confirmation">
							<option value="-1"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_CONFIRMATION_OPTION_NULL'); ?></option>
							<option value="1"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_CONFIRMATION_OPTION_YES'); ?></option>
							<option value="0"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_CONFIRMATION_OPTION_NO'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_FILE'); ?>:</th>
					<td>
						<input type="file" name="email_file" />
					</td>
				</tr>
				<?php if (!empty($this->groups)): ?>
					<tr>
						<td colspan="2">
							<strong><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_AND_OR'); ?></strong>
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_GROUP'); ?>:</th>
						<td>
							<select name="email_group">
								<option value=""><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_GROUP_OPTION_NULL'); ?></option>
								<?php foreach ($this->groups as $group) : ?>
									<option value="<?php echo $group->gidNumber; ?>"><?php echo $group->description; ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				<?php endif; ?>
				<tr>
					<td colspan="2">
						<strong><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_AND_OR'); ?></strong>
					</td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_RAW'); ?>:</th>
					<td>
						<textarea name="email_box" rows="10" cols="100"><?php echo $this->emailBox; ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="mid" value="<?php echo $this->list->id; ?>" />
	<input type="hidden" name="task" value="doaddemail" />

	<?php echo Html::input('token'); ?>
</form>
