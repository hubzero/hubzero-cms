<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Newsletter\Helpers\Permissions::getActions('mailinglist');

$text = ($this->task == 'edit' ? Lang::txt('COM_NEWSLETTER_EDIT') : Lang::txt('COM_NEWSLETTER_NEW'));

Toolbar::title(Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILINGLISTS') . ': ' . $text, 'list');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<?php if (!$this->row->id) : ?>
		<p class="info"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_MUST_CREATE_BEFORE_ADD'); ?></p>
	<?php endif; ?>
	<fieldset class="adminform">
		<legend><span><?php echo $text; ?> <?php echo Lang::txt('COM_NEWSLETTER_LISTS'); ?></span></legend>

		<div class="input-wrap">
			<label for="field-name"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_NAME'); ?>:</label><br />
			<input type="text" name="field[name]" id="field-name" value="<?php echo $this->escape($this->row->name); ?>" /></td>
		</div>

		<div class="input-wrap">
			<label for="field-private"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_PRIVACY'); ?>:</label><br />
			<select name="field[private]" id="field-private">
				<option value="0" <?php echo ($this->row->private == 0) ? 'selected="selected"': ''; ?>><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_PRIVACY_PUBLIC'); ?></option>
				<option value="1" <?php echo ($this->row->private == 1) ? 'selected="selected"': ''; ?>><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_PRIVACY_PRIVATE'); ?></option>
			</select>
		</div>

		<div class="input-wrap">
			<label for="field-description"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_DESC'); ?>:</label><br />
			<textarea name="field[description]" id="field-description" rows="5"><?php echo $this->escape($this->row->description); ?></textarea>
		</div>
	</fieldset>

	<input type="hidden" name="field[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>