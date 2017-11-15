<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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

?>
<script type="text/javascript">
<!--
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// do field validation
	if (document.getElementById('field-title').value == ''){
		alert( '<?php echo Lang::txt('COM_SERVICES_ERROR_MISSING_TITLE'); ?>' );
	} else {
		submitform( pressbutton );
	}
}
//-->
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-category"><?php echo Lang::txt('COM_SERVICES_FIELD_CATEGORY'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[category]" id="field-category" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->category)); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_SERVICES_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[title]" id="field-title" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
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
						<th><?php echo Lang::txt('COM_SERVICES_FIELD_ID'); ?>:</th>
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
