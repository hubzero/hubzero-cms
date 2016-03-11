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

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('PLG_RESOURCES') . ': '.Lang::txt('PLG_RESOURCES_SPONSOR').': ' . $text, 'addedit.png');
Toolbar::save();
Toolbar::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	<?php echo $this->editor()->save('field-description'); ?>

	// form field validation
	if ($('#title').val() == '') {
		alert( '<?php echo Lang::txt('PLG_RESOURCES_SPONSORS_MISSING_TITLE'); ?>' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" id="item-form" name="adminForm">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="title"><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[title]" id="title" size="30" maxlength="100" value="<?php echo $this->escape($this->row->title); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_ALIAS_HINT'); ?>">
					<label for="alias"><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_ALIAS'); ?>:</label>
					<input type="text" name="fields[alias]" id="alias" size="30" maxlength="100" value="<?php echo $this->escape($this->row->alias); ?>" /><br />
					<span class="hint"><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_ALIAS_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-description"><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_DESCRIPTION'); ?>:</label></td>
					<?php echo $this->editor('fields[description]', stripslashes($this->row->description), 45, 10, 'field-description'); ?>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_ID'); ?></th>
						<td><?php echo $this->row->id; ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_CREATED'); ?></th>
						<td><?php echo $this->row->created; ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_CREATOR'); ?></th>
						<td><?php echo $this->row->created_by; ?></td>
					</tr>
					<?php if ($this->row->modified) { ?>
						<tr>
							<th><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_MODIFIED'); ?></th>
							<td><?php echo $this->row->modified; ?></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_MODIFIER'); ?></th>
							<td><?php echo $this->row->modified_by; ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="plugin" value="sponsors" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>