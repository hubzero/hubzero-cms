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

$canDo = Components\Kb\Admin\Helpers\Permissions::getActions('article');

$text = ($this->task == 'edit' ? Lang::txt('COM_KB_EDIT') : Lang::txt('COM_KB_NEW'));

Toolbar::title(Lang::txt('COM_KB') . ': ' . Lang::txt('COM_KB_ARTICLE') . ': ' . $text, 'kb');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('article');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton =='resethits') {
		if (confirm('<?php echo Lang::txt('COM_KB_RESET_HITS_WARNING'); ?>')) {
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

	<?php echo $this->editor()->save('text'); ?>

	// do field validation
	if (document.getElementById('field-title').value == ''){
		alert('<?php echo Lang::txt('COM_KB_ERROR_MISSING_TITLE'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_KB_DETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-section"><?php echo Lang::txt('COM_KB_CATEGORY'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<?php echo Components\Kb\Admin\Helpers\Html::categories($this->categories, $this->row->get('category'), 'fields[category]', 'field-category'); ?>
				</div>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_KB_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[title]" id="field-title" size="100" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_KB_ALIAS_HINT'); ?>">
					<label for="field-alias"><?php echo Lang::txt('COM_KB_ALIAS'); ?>:</label><br />
					<input type="text" name="fields[alias]" id="field-alias" size="30" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_KB_ALIAS_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-fulltxt"><?php echo Lang::txt('COM_KB_BODY'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<?php echo $this->editor('fields[fulltxt]', $this->escape(stripslashes($this->row->get('fulltxt'))), 60, 30, 'field-fulltxt', array('buttons' => array('pagebreak', 'readmore', 'article'))); ?>
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_KB_FIELD_TAGS_HINT'); ?>">
					<label for="field-tags"><?php echo Lang::txt('COM_KB_TAGS'); ?>:</label><br />
					<textarea name="tags" id="field-tags" cols="50" rows="3"><?php echo $this->escape(stripslashes($this->row->tags('string'))); ?></textarea>
					<span class="hint"><?php echo Lang::txt('COM_KB_FIELD_TAGS_HINT'); ?></span>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_KB_ID'); ?>:</th>
						<td>
							<?php echo $this->row->get('id', 0); ?>
							<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->row->get('id'); ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_KB_CREATED'); ?>:</th>
						<td><?php echo $this->row->get('created'); ?></td>
					</tr>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_KB_CREATOR'); ?>:</th>
						<td><?php echo $this->escape($this->row->creator->get('name', Lang::txt('COM_KB_UNKNOWN'))); ?></td>
					</tr>
					<?php if (!$this->row->isNew() && $this->row->get('modified') != '0000-00-00 00:00:00') { ?>
						<tr>
							<th class="key"><?php echo Lang::txt('COM_KB_LAST_MODIFIED'); ?>:</th>
							<td><?php echo $this->row->get('modified'); ?></td>
						</tr>
						<?php
						$modifier = User::getInstance($this->row->get('modified_by'));
						if (is_object($modifier)) {?>
							<tr>
								<th class="key"><?php echo Lang::txt('COM_KB_MODIFIER'); ?>:</th>
								<td><?php echo $this->escape($modifier->get('name', Lang::txt('COM_KB_UNKNOWN'))); ?></td>
							</tr>
						<?php } ?>
					<?php } ?>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_KB_HITS'); ?>:</th>
						<td>
							<?php echo $this->row->get('hits', 0); ?>
							<?php if ($this->row->get('hits', 0)) { ?>
								<input type="button" name="reset_hits" id="reset_hits" value="<?php echo Lang::txt('COM_KB_RESET_HITS'); ?>" onclick="submitbutton('resethits');" />
							<?php } ?>
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_KB_VOTES'); ?>:</th>
						<td>
							+<?php echo $this->row->get('helpful', 0); ?> -<?php echo $this->row->get('nothelpful', 0); ?>
							<?php if ($this->row->get('helpful', 0) > 0 || $this->row->get('nothelpful', 0) > 0) { ?>
								<input type="button" name="reset_votes" value="<?php echo Lang::txt('COM_KB_RESET_VOTES'); ?>" onclick="submitbutton('resetvotes');" />
							<?php } ?>
						</td>
					</tr>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_KB_STATE'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('COM_KB_PUBLISH'); ?>:</label>
					<select name="fields[state]" id="field-state">
						<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
						<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
						<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
					</select>
				</div>
				<div class="input-wrap">
					<label for="field-access"><?php echo Lang::txt('COM_KB_ACCESS_LEVEL'); ?>:</label>
					<select name="fields[access]" id="field-access">
						<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->row->get('access')); ?>
					</select>
				</div>
			</fieldset>

			<fieldset class="adminform paramlist">
				<legend><span><?php echo Lang::txt('COM_KB_PARAMETERS'); ?></span></legend>

				<?php echo $this->params->render(); ?>
			</fieldset>
		</div>
	</div>

	<?php /*
		<?php if ($canDo->get('core.admin')): ?>
			<div class="col width-100 fltlft">
				<fieldset class="panelform">
					<?php echo $this->form->getLabel('rules'); ?>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>
			</div>
			<div class="clr"></div>
		<?php endif; ?>
	*/ ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
