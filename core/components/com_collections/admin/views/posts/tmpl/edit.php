<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Collections\Helpers\Permissions::getActions('post');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_COLLECTIONS') . ': ' . Lang::txt('COM_COLLECTIONS_POSTS') . ': ' . $text, 'collection');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('collection');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

if (!$this->row->get('id'))
{
	$this->row->set('created_by', User::get('id'));
	$this->row->set('created', Date::toSql());
}
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" class="editform form-validate" id="item-form" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
	<?php } ?>
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="grid">
					<div class="col span6">
						<div class="input-wrap">
							<label for="field-item_id"><?php echo Lang::txt('COM_COLLECTIONS_FIELD_ITEM_ID'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
							<input type="text" name="fields[item_id]" id="field-item_id" class="required" maxlength="11" value="<?php echo $this->escape(stripslashes($this->row->get('item_id'))); ?>" />
						</div>
					</div>
					<div class="col span6">
						<div class="input-wrap">
							<label for="field-collection_id"><?php echo Lang::txt('COM_COLLECTIONS_FIELD_COLLECTION_ID'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
							<input type="text" name="fields[collection_id]" id="field-collection_id" class="required" maxlength="11" value="<?php echo $this->escape(stripslashes($this->row->get('collection_id'))); ?>" />
						</div>
					</div>
				</div>

				<div class="input-wrap">
					<label for="field-description"><?php echo Lang::txt('COM_COLLECTIONS_FIELD_DESCRIPTION'); ?></label><br />
					<?php echo $this->editor('fields[description]', $this->escape($this->row->get('description')), 35, 10, 'field-description', array('class' => 'minimal no-footer', 'buttons' => false)); ?>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_COLLECTIONS_FIELD_CREATOR'); ?>:</th>
						<td>
							<?php
							$editor = User::getInstance($this->row->get('created_by'));
							echo $this->escape(stripslashes($editor->get('name')));
							?>
							<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->escape($this->row->get('created_by')); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_COLLECTIONS_FIELD_CREATED'); ?>:</th>
						<td>
							<?php echo Date::of($this->row->get('created'))->toLocal(); ?>
							<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->escape($this->row->get('created')); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_COLLECTIONS_FIELD_ORIGINAL'); ?>:</th>
						<td>
							<?php echo ($this->row->get('original')) ? Lang::txt('JYES') : Lang::txt('JNO'); ?>
						</td>
					</tr>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

				<div class="input-wrap">
					<input type="checkbox" name="fields[original]" id="field-original" value="1"<?php if ($this->row->get('original') == 1) { echo ' checked="checked"'; } ?> />
					<label for="field-original"><?php echo Lang::txt('COM_COLLECTIONS_FIELD_ORIGINAL'); ?></label>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>