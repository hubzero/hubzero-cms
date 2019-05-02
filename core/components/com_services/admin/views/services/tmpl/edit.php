<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Services\Helpers\Permissions::getActions('service');

$text = ($this->row->id ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_SERVICES') . ': ' . Lang::txt('COM_SERVICES_SERVICES') . ': ' . $text, 'services');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-category"><?php echo Lang::txt('COM_SERVICES_FIELD_CATEGORY'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[category]" id="field-category" class="required" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->category)); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_SERVICES_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[title]" id="field-title" class="required" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_SERVICES_FIELD_ALIAS_HINT'); ?>">
					<label for="field-alias"><?php echo Lang::txt('COM_SERVICES_FIELD_ALIAS'); ?>:</label><br />
					<input type="text" name="fields[alias]" id="field-alias" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->alias)); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_SERVICES_FIELD_ALIAS_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-description"><?php echo Lang::txt('COM_SERVICES_FIELD_DESCRIPTION'); ?>:</label><br />
					<textarea name="fields[description]" id="field-description" rows="5" cols="35"><?php echo $this->escape(stripslashes($this->row->description)); ?></textarea>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_SERVICES_UNITS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-currency"><?php echo Lang::txt('COM_SERVICES_FIELD_CURRENCY'); ?>:</label><br />
					<input type="text" name="fields[currency]" id="field-currency" maxlength="10" value="<?php echo $this->escape(stripslashes($this->row->currency)); ?>" />
				</div>

					<div class="input-wrap">
						<label for="field-unitprice"><?php echo Lang::txt('COM_SERVICES_FIELD_UNITPRICE'); ?>:</label><br />
						<input type="text" name="fields[unitprice]" id="field-unitprice" value="<?php echo $this->escape(stripslashes($this->row->unitprice)); ?>" />
					</div>
					<div class="input-wrap">
						<label for="field-pointsprice"><?php echo Lang::txt('COM_SERVICES_FIELD_POINTSPRICE'); ?>:</label><br />
						<input type="text" name="fields[pointsprice]" id="field-pointsprice" maxlength="11" value="<?php echo $this->escape(stripslashes($this->row->pointsprice)); ?>" />
					</div>
				<div class="clr"></div>

					<div class="input-wrap">
						<label for="field-minunits"><?php echo Lang::txt('COM_SERVICES_FIELD_MINUNITS'); ?>:</label><br />
						<input type="text" name="fields[minunits]" id="field-minunits" maxlength="11" value="<?php echo $this->escape(stripslashes($this->row->minunits)); ?>" />
					</div>
					<div class="input-wrap">
						<label for="field-maxunits"><?php echo Lang::txt('COM_SERVICES_FIELD_MAXUNITS'); ?>:</label><br />
						<input type="text" name="fields[maxunits]" id="field-maxunits" value="<?php echo $this->escape(stripslashes($this->row->maxunits)); ?>" />
					</div>
				<div class="clr"></div>

					<div class="input-wrap">
						<label for="field-unitsize"><?php echo Lang::txt('COM_SERVICES_FIELD_UNITSIZE'); ?>:</label><br />
						<input type="text" name="fields[unitsize]" id="field-unitsize" maxlength="11" value="<?php echo $this->escape(stripslashes($this->row->unitsize)); ?>" />
					</div>
					<div class="input-wrap">
						<label for="field-unitmeasure"><?php echo Lang::txt('COM_SERVICES_FIELD_UNITMEASURE'); ?>:</label><br />
						<input type="text" name="fields[unitmeasure]" id="field-unitmeasure" value="<?php echo $this->escape(stripslashes($this->row->unitmeasure)); ?>" />
					</div>
				<div class="clr"></div>

					<div class="input-wrap">
						<label for="field-params"><?php echo Lang::txt('COM_SERVICES_FIELD_PARAMS'); ?>:</label><br />
						<input type="text" name="fields[params]" id="field-params" value="<?php echo $this->escape(stripslashes($this->row->params)); ?>" />
					</div>
				<div class="clr"></div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_SERVICES_FIELD_ID'); ?>:</th>
						<td>
							<?php echo $this->escape($this->row->id); ?>
						</td>
					</tr>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

				<div class="input-wrap">
					<input class="option" type="checkbox" name="fields[restricted]" id="field-restricted" value="1"<?php if ($this->row->restricted) { echo ' checked="checked"'; } ?> />
					<label for="field-restricted"><?php echo Lang::txt('COM_SERVICES_FIELD_RESTRICTED'); ?></label>
				</div>

				<div class="input-wrap">
					<label for="field-status"><?php echo Lang::txt('COM_SERVICES_FIELD_STATUS'); ?>:</label><br />
					<select name="fields[status]" id="field-status">
						<option value="0"<?php if ($this->row->status == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
						<option value="1"<?php if ($this->row->status == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
						<option value="2"<?php if ($this->row->status == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
					</select>
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
