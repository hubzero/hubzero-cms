<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Members\Helpers\Admin::getActions('component');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_ACCESSGROUPS') . ': ' . $text, 'user');
if ($canDo->get('core.edit')||$canDo->get('core.create'))
{
	Toolbar::apply();
	Toolbar::save();
}
if ($canDo->get('core.create'))
{
	Toolbar::save2new();
}
// If an existing item, can save to a copy.
if (!$this->row->get('id') && $canDo->get('core.create'))
{
	Toolbar::save2copy();
}
Toolbar::cancel();
Toolbar::divider();
Toolbar::help('group');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_MEMBERS_GROUP_FIELD_TITLE_DESC'); ?>">
					<label for="field-title"><?php echo Lang::txt('COM_MEMBERS_GROUP_FIELD_TITLE_LABEL'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape($this->row->get('title')); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_MEMBERS_GROUP_FIELD_PARENT_DESC'); ?>">
					<label for="field-parent_id"><?php echo Lang::txt('COM_MEMBERS_GROUP_FIELD_PARENT_LABEL'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<select name="fields[parent_id]" id="field-parent_id" class="required">
						<?php foreach ($this->options as $option):
							if (User::authorise('core.admin') || (!Hubzero\Access\Access::checkGroup($option->get('id'), 'core.admin')))
							{
								$level = Hubzero\Access\Group::all()
									->where('lft', '<', $option->get('lft'))
									->where('rgt', '>', $option->get('rgt'))
									->total();
								?>
								<option value="<?php echo $option->get('id'); ?>"<?php if ($option->get('id') == $this->row->get('parent_id')) { echo ' selected="selected"'; } ?>><?php echo str_repeat('- ', $level) . $option->get('title'); ?></option>
								<?php
							}
						endforeach; ?>
					</select>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_MEMBERS_FIELD_ID'); ?>:</th>
						<td>
							<?php echo $this->row->get('id'); ?>
							<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<input type="hidden" name="id" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
