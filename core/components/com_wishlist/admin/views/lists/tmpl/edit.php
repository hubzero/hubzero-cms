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

$canDo = \Components\Wishlist\Helpers\Permissions::getActions('list');

$text = ($this->task == 'edit' ? Lang::txt('COM_WISHLIST_EDIT') : Lang::txt('COM_WISHLIST_NEW'));

Toolbar::title(Lang::txt('COM_WISHLIST') . ': ' . Lang::txt('COM_WISHLIST_LIST') . ': ' . $text, 'wishlist');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('list');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton =='resethits') {
		if (confirm('<?php echo Lang::txt('COM_WISHLIST_RESET_HITS_WARNING'); ?>')){
			submitform(pressbutton);
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// do field validation
	if (document.getElementById('field-title').value == ''){
		alert('<?php echo Lang::txt('COM_WISHLIST_ERROR_MISSING_TITLE'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_WISHLIST_DETAILS'); ?></span></legend>

				<div class="grid">
					<div class="col span6">
						<div class="input-wrap">
							<label for="field->category"><?php echo Lang::txt('COM_WISHLIST_CATEGORY'); ?>:</label><br />
							<select name="fields[category]" id="field-category">
								<option value=""<?php echo ($this->row->category == '') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_SELECT_CATEGORY'); ?></option>
								<option value="general"<?php echo ($this->row->category == 'general') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_CATEGORY_GENERAL'); ?></option>
								<option value="group"<?php echo ($this->row->category == 'group') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_CATEGORY_GROUP'); ?></option>
								<option value="resource"<?php echo ($this->row->category == 'resource') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_CATEGORY_RESOURCE'); ?></option>
							</select>
						</div>
					</div>
					<div class="col span6">
						<div class="input-wrap">
							<label for="field-referenceid"><?php echo Lang::txt('COM_WISHLIST_REFERENCEID'); ?>:</label><br />
							<input type="text" name="fields[referenceid]" id="field-referenceid" size="11" maxlength="11" value="<?php echo $this->escape(stripslashes($this->row->referenceid)); ?>" />
						</div>
					</div>
				</div>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_WISHLIST_TITLE'); ?>:</label><br />
					<input type="text" name="fields[title]" id="field-title" size="30" maxlength="150" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-description"><?php echo Lang::txt('COM_WISHLIST_DESCRIPTION'); ?>:</label><br />
					<input type="text" name="fields[description]" id="field-description" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->description)); ?>" />
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_WISHLIST_FIELD_ID'); ?>:</th>
						<td>
							<?php echo $this->row->id; ?>
							<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->row->id; ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_WISHLIST_FIELD_CREATED'); ?>:</th>
						<td>
							<time datetime="<?php echo $this->row->get('created'); ?>"><?php echo $this->row->get('created'); ?></time>
							<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->row->get('created'); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_WISHLIST_FIELD_CREATOR'); ?>:</th>
						<td>
							<?php
							$editor  = User::getInstance($this->row->get('created_by'));
							echo $this->escape(stripslashes($editor->get('name', Lang::txt('unknown'))));
							?>
							<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->row->get('created_by'); ?>" />
						</td>
					</tr>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_WISHLIST_PARAMETERS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('COM_WISHLIST_STATE'); ?></label>
					<select name="fields[state]" id="field-state">
						<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
						<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
						<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
					</select>
				</div>

				<div class="input-wrap">
					<label for="field-public"><?php echo Lang::txt('COM_WISHLIST_PRIVACY'); ?></label>
					<select name="fields[public]" id="field-public">
						<option value="0"<?php if ($this->row->get('public') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_WISHLIST_PRIVATE'); ?></option>
						<option value="1"<?php if ($this->row->get('public') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_WISHLIST_PUBLIC'); ?></option>
					</select>
				</div>
			</fieldset>
		</div>
	</div>

	<?php /*
			<div class="col width-100 fltlft">
				<fieldset class="panelform">
					<legend><span><?php echo Lang::txt('COM_WISHLIST_FIELDSET_RULES'); ?></span></legend>
					<?php echo $this->form->getLabel('rules'); ?>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>
			</div>
			<div class="clr"></div>
	*/ ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
