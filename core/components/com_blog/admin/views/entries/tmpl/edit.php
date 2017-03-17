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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Blog\Admin\Helpers\Permissions::getActions('entry');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_BLOG_TITLE') . ': ' . $text, 'blog');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('entry');
?>
<script type="text/javascript">
Joomla.submitbutton = function(pressbutton) {
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		Joomla.submitform(pressbutton, document.getElementById('item-form'));
		return;
	}

	<?php echo $this->editor()->save('text'); ?>

	// do field validation
	if ($('#field-title').val() == ''){
		alert("<?php echo Lang::txt('COM_BLOG_ERROR_MISSING_TITLE'); ?>");
	} else if ($('#field-content').val() == ''){
		alert("<?php echo Lang::txt('COM_BLOG_ERROR_MISSING_CONTENT'); ?>");
	} else {
		Joomla.submitform(pressbutton, document.getElementById('item-form'));
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" class="editform" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="grid">
					<div class="col span6">
						<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_BLOG_FIELD_SCOPE_HINT'); ?>">
							<label for="field-scope"><?php echo Lang::txt('COM_BLOG_FIELD_SCOPE'); ?>:</label><br />
							<?php if ($this->row->isNew() || User::authorise('core.admin', $this->option)) { ?>
								<?php echo Components\Blog\Admin\Helpers\Html::scopes($this->row->get('scope'), 'fields[scope]', 'field-scope'); ?>
							<?php } else { ?>
								<input type="text" name="fields[scope]" id="field-scope" disabled="disabled" value="<?php echo $this->escape(stripslashes($this->row->get('scope'))); ?>" />
							<?php } ?>
						</div>
					</div>
					<div class="col span6">
						<div class="input-wrap">
							<label for="field-scope_id"><?php echo Lang::txt('COM_BLOG_FIELD_SCOPE_ID'); ?>:</label><br />
							<?php if ($this->row->isNew() || User::authorise('core.admin', $this->option)) { ?>
								<input type="text" name="fields[scope_id]" id="field-scope_id" value="<?php echo $this->escape(stripslashes($this->row->get('scope_id'))); ?>" />
							<?php } else { ?>
								<input type="text" name="fields[scope_id]" id="field-scope_id" disabled="disabled" value="<?php echo $this->escape(stripslashes($this->row->get('scope_id'))); ?>" />
							<?php } ?>
						</div>
					</div>
				</div>
				<?php if (!$this->row->isNew() && User::authorise('core.admin', $this->option)) { ?>
					<div class="input-wrap">
						<p class="warning"><?php echo Lang::txt('COM_BLOG_FIELD_SCOPE_WARNING'); ?></p>
					</div>
				<?php } ?>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_BLOG_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[title]" id="field-title" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_BLOG_FIELD_ALIAS_HINT'); ?>">
					<label for="field-alias"><?php echo Lang::txt('COM_BLOG_FIELD_ALIAS'); ?>:</label><br />
					<input type="text" name="fields[alias]" id="field-alias" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_BLOG_FIELD_ALIAS_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-content"><?php echo Lang::txt('COM_BLOG_FIELD_CONTENT'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<?php echo $this->editor('fields[content]', $this->escape($this->row->get('content')), 50, 30, 'field-content', array('buttons' => false)); ?>
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_BLOG_FIELD_TAGS_HINT'); ?>">
					<label for="field-tags"><?php echo Lang::txt('COM_BLOG_FIELD_TAGS'); ?>:</label><br />
					<textarea name="tags" id="field-tags" cols="35" rows="3"><?php echo $this->escape(stripslashes($this->row->tags('string'))); ?></textarea>
					<span class="hint"><?php echo Lang::txt('COM_BLOG_FIELD_TAGS_HINT'); ?></span>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_BLOG_FIELD_ID'); ?>:</th>
						<td>
							<?php echo $this->row->get('id', 0); ?>
							<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
							<input type="hidden" name="id" value="<?php echo $this->row->get('id'); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_BLOG_FIELD_CREATOR'); ?>:</th>
						<td>
							<?php
							$editor = User::getInstance($this->row->get('created_by'));
							echo $this->escape(stripslashes($editor->get('name')));
							?>
							<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->escape($this->row->get('created_by')); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_BLOG_FIELD_CREATED'); ?>:</th>
						<td>
							<?php echo $this->row->get('created'); ?>
							<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->escape($this->row->get('created')); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_BLOG_FIELD_HITS'); ?>:</th>
						<td>
							<?php echo $this->row->get('hits'); ?>
							<input type="hidden" name="fields[hits]" id="field-hits" value="<?php echo $this->escape($this->row->get('hits')); ?>" />
						</td>
					</tr>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

				<div class="input-wrap">
					<input class="option" type="checkbox" name="fields[allow_comments]" id="field-allow_comments" value="1"<?php if ($this->row->get('allow_comments')) { echo ' checked="checked"'; } ?> />
					<label for="field-allow_comments"><?php echo Lang::txt('COM_BLOG_FIELD_ALLOW_COMMENTS'); ?></label>
				</div>

				<div class="input-wrap">
					<label for="field-access"><?php echo Lang::txt('COM_BLOG_FIELD_ACCESS_LEVEL'); ?>:</label>
					<select name="fields[access]" id="field-access">
						<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->row->get('access')); ?>
					</select>
				</div>

				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('COM_BLOG_FIELD_STATE'); ?>:</label><br />
					<select name="fields[state]" id="field-state">
						<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
						<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
						<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
					</select>
				</div>

				<div class="input-wrap">
					<label for="field-publish_up"><?php echo Lang::txt('COM_BLOG_FIELD_PUBLISH_UP'); ?>:</label><br />
					<?php echo Html::input('calendar', 'fields[publish_up]', ($this->row->get('publish_up') != '0000-00-00 00:00:00' ? $this->escape(Date::of($this->row->get('publish_up'))->toLocal('Y-m-d H:i:s')) : ''), array('id' => 'field-publish_up')); ?>
				</div>

				<div class="input-wrap">
					<label for="field-publish_down"><?php echo Lang::txt('COM_BLOG_FIELD_PUBLISH_DOWN'); ?>:</label><br />
					<?php echo Html::input('calendar', 'fields[publish_down]', ($this->row->get('publish_down') != '0000-00-00 00:00:00' ? $this->escape(Date::of($this->row->get('publish_down'))->toLocal('Y-m-d H:i:s')) : ''), array('id' => 'field-publish_down')); ?>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>