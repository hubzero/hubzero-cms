<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = Components\Tags\Helpers\Permissions::getActions();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_TAGS') . ': ' . Lang::txt('COM_TAGS_TAGGED') . ': ' . $text, 'tags');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('edittagged');

Html::behavior('framework');
Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<?php
if ($this->getError())
{
	echo '<p class="error">' . implode('<br />', $this->getError()) . '</p>';
}
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_TAGS_FIELD_TAGID_HINT'); ?>">
					<label for="field-tagid"><?php echo Lang::txt('COM_TAGS_FIELD_TAGID'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[tagid]" id="field-tagid" maxlength="11" class="required" value="<?php echo $this->escape($this->row->get('tagid')); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_TAGS_FIELD_TAGID_HINT'); ?></span>
				</div>

				<div class="grid">
					<div class="col span6">
						<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_TAGS_FIELD_OBJECTID_HINT'); ?>">
							<label for="field-objectid"><?php echo Lang::txt('COM_TAGS_FIELD_OBJECTID'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
							<input type="text" name="fields[objectid]" id="field-objectid" maxlength="11" class="required" value="<?php echo $this->escape($this->row->get('objectid')); ?>" />
							<span class="hint"><?php echo Lang::txt('COM_TAGS_FIELD_OBJECTID_HINT'); ?></span>
						</div>
					</div>
					<div class="col span6">
						<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_TAGS_FIELD_TBL_HINT'); ?>">
							<label for="field-tbl"><?php echo Lang::txt('COM_TAGS_FIELD_TBL'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
							<input type="text" name="fields[tbl]" id="field-tbl" maxlength="250" class="required" value="<?php echo $this->escape($this->row->get('tbl')); ?>" />
							<span class="hint"><?php echo Lang::txt('COM_TAGS_FIELD_TBL_HINT'); ?></span>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_TAGS_FIELD_ID'); ?>:</th>
						<td>
							<?php echo $this->row->get('id'); ?>
							<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_TAGS_FIELD_CREATOR'); ?>:</th>
						<td>
							<?php
							$name = Lang::txt('COM_TAGS_UNKNOWN');
							if ($this->row->creator->get('id'))
							{
								$name = $this->row->creator->get('name');
							}
							echo $this->escape($name);
							?>
							<input type="hidden" name="fields[taggerid]" id="field-taggerid" value="<?php echo $this->escape($this->row->get('taggerid')); ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_TAGS_FIELD_CREATED'); ?>:</th>
						<td>
							<?php echo ($this->row->created() && $this->row->created() != '0000-00-00 00:00:00') ? $this->row->created() : Lang::txt('COM_TAGS_UNKNOWN'); ?>
							<input type="hidden" name="fields[taggedon]" id="field-taggedon" value="<?php echo $this->escape($this->row->get('taggedon')); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>