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

$canDo = Components\Forum\Helpers\Permissions::getActions('thread');

$text  = ($this->row->get('parent') ? Lang::txt('COM_FORUM_POSTS') : Lang::txt('COM_FORUM_THREADS')) . ': ';
$text .= ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_FORUM') . ': ' . $text, 'forum');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('post');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	// do field validation
	if (document.getElementById('field-comment').value == ''){
		alert( '<?php echo Lang::txt('COM_FORUM_ERROR_MISSING_COMMENT'); ?>' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="grid">
					<div class="col span6">
						<div class="input-wrap">
							<label for="field-scope"><?php echo Lang::txt('COM_FORUM_FIELD_SCOPE'); ?>:</label><br />
							<input type="text" name="fields[scope]" id="field-scope" maxlength="150" value="<?php echo $this->escape($this->row->get('scope')); ?>" />
						</div>
					</div>
					<div class="col span6">
						<div class="input-wrap">
							<label for="field-scope_id"><?php echo Lang::txt('COM_FORUM_FIELD_SCOPE_ID'); ?>:</label><br />
							<input type="text" name="fields[scope_id]" id="field-scope_id" maxlength="11" value="<?php echo $this->escape($this->row->get('scope_id')); ?>" />
						</div>
					</div>
				</div>

				<div class="input-wrap">
					<label for="field-object_id"><?php echo Lang::txt('COM_FORUM_FIELD_OBJECT_ID'); ?>:</label><br />
					<input type="text" name="fields[object_id]" id="field-object_id" maxlength="11" value="<?php echo $this->escape($this->row->get('object_id')); ?>" />
				</div>

				<?php if (!$this->row->get('parent')) { ?>
					<div class="input-wrap">
						<label for="field-category_id"><?php echo Lang::txt('COM_FORUM_FIELD_CATEGORY'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
						<select name="fields[category_id]" id="field-category_id">
							<option value="-1"><?php echo Lang::txt('COM_FORUM_FIELD_CATEGORY_SELECT'); ?></option>
							<?php foreach ($this->sections as $group => $sections) { ?>
								<optgroup label="<?php echo $this->escape(stripslashes($group)); ?>">
									<?php foreach ($sections as $section) { ?>
										<optgroup label="&nbsp; &nbsp; <?php echo $this->escape(stripslashes($section->title)); ?>">
											<?php foreach ($section->categories as $category) { ?>
												<option value="<?php echo $category->id; ?>"<?php if ($this->row->category_id == $category->id) { echo ' selected="selected"'; } ?>>&nbsp; &nbsp; <?php echo $this->escape(stripslashes($category->title)); ?></option>
											<?php } ?>
										</optgroup>
									<?php } ?>
								</optgroup>
							<?php } ?>
						</select>
					</div>
				<?php } ?>

				<?php if ($this->row->get('parent')) { ?>
					<div class="input-wrap">
						<label for="field-title"><?php echo Lang::txt('COM_FORUM_FIELD_PARENT'); ?>:</label><br />
						<select name="fields[parent]" id="field-parent">
							<option value="0"><?php echo Lang::txt('COM_FORUM_FIELD_PARENT_SELECT'); ?></option>
							<?php
							$posts = Components\Forum\Models\Post::all()
								->whereEquals('thread', $this->row->get('thread'))
								->ordered()
								->rows();
							foreach ($posts as $post)
							{
								if ($post->get('id') == $this->row->get('id'))
								{
									continue;
								}
								?>
								<option value="<?php echo $post->id; ?>"<?php if ($this->row->get('parent') == $post->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($post->get('title'))); ?></option>
								<?php
							}
							?>
						</select>
					</div>
				<?php } ?>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_FORUM_FIELD_TITLE'); ?>:</label><br />
					<input type="text" name="fields[title]" id="field-title" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-comment"><?php echo Lang::txt('COM_FORUM_FIELD_COMMENTS'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<textarea name="fields[comment]" id="field-comment" cols="35" rows="10"><?php echo $this->escape($this->row->get('comment')); ?></textarea>
				</div>

				<?php if (!$this->row->get('parent')) { ?>
					<div class="input-wrap">
						<label for="field-tags"><?php echo Lang::txt('COM_FORUM_FIELD_TAGS'); ?></label><br />
						<textarea name="tags" id="field-tags" cols="35" rows="5"><?php echo $this->escape($this->row->tags('string')); ?></textarea>
					</div>
				<?php } ?>
			</fieldset>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_FORUM_LEGEND_ATTACHMENTS'); ?></span></legend>

				<?php $attachment = $this->row->attachments()->row(); ?>

				<div class="input-wrap">
					<label for="upload"><?php echo Lang::txt('COM_FORUM_FIELD_FILE'); ?> <?php if ($attachment->get('filename')) { echo '<strong>' . $this->escape(stripslashes($attachment->get('filename'))) . '</strong>'; } ?></label><br />
					<input type="file" name="upload" id="upload<" />
				</div>

				<div class="input-wrap">
					<label for="field-attach-descritpion"><?php echo Lang::txt('COM_FORUM_FIELD_DESCRIPTION'); ?></label><br />
					<input type="text" name="description" id="field-attach-descritpion" value="<?php echo $this->escape(stripslashes($attachment->get('description'))); ?>" />
					<input type="hidden" name="attachment" value="<?php echo $this->escape($attachment->get('id')); ?>" />
				</div>

				<?php if ($attachment->get('id')) { ?>
					<p class="warning">
						<?php echo Lang::txt('COM_FORUM_FIELD_FILE_WARNING'); ?>
					</p>
				<?php } ?>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<?php if ($this->row->get('parent')) { ?>
						<tr>
							<th><?php echo Lang::txt('COM_FORUM_FIELD_THREAD'); ?>:</th>
							<td>
								<?php
								echo $this->row->get('thread');
								?>
							</td>
						</tr>
					<?php } ?>
					<tr>
						<th><?php echo Lang::txt('COM_FORUM_FIELD_CREATOR'); ?>:</th>
						<td>
							<?php
							echo $this->escape($this->row->creator->get('name'));
							?>
							<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->row->get('created_by'); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_FORUM_FIELD_CREATED'); ?>:</th>
						<td>
							<?php echo $this->row->get('created', Date::of('now')->toSql()); ?>
							<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->row->get('created'); ?>" />
						</td>
					</tr>
					<?php if ($this->row->get('modified_by')) { ?>
						<tr>
							<th><?php echo Lang::txt('COM_FORUM_FIELD_MODIFIER'); ?>:</th>
							<td>
								<?php
								echo $this->escape($this->row->modifier->get('name'));
								?>
								<input type="hidden" name="fields[modified_by]" id="field-modified_by" value="<?php echo $this->row->get('modified_by'); ?>" />
							</td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_FORUM_FIELD_MODIFIED'); ?>:</th>
							<td>
								<?php echo $this->row->get('modified'); ?>
								<input type="hidden" name="fields[modified]" id="field-modified" value="<?php echo $this->row->get('modified'); ?>" />
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

				<div class="input-wrap">
					<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1"<?php if ($this->row->get('anonymous')) { echo ' checked="checked"'; } ?> />
					<label for="field-anonymous"><?php echo Lang::txt('COM_FORUM_FIELD_ANONYMOUS'); ?></label>
				</div>

				<?php if (!$this->row->get('parent')) { ?>
					<div class="input-wrap">
						<input class="option" type="checkbox" name="fields[sticky]" id="field-sticky" value="1"<?php if ($this->row->get('sticky')) { echo ' checked="checked"'; } ?> />
						<label for="field-sticky"><?php echo Lang::txt('COM_FORUM_FIELD_STICKY'); ?></label>
					</div>
				<?php } ?>

				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('COM_FORUM_FIELD_STATE'); ?>:</label><br />
					<select name="fields[state]" id="field-state">
						<option value="0"<?php echo ($this->row->get('state') == 0) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
						<option value="1"<?php echo ($this->row->get('state') == 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
						<option value="2"<?php echo ($this->row->get('state') == 2) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JTRASHED'); ?></option>
					</select>
				</div>

				<div class="input-wrap">
					<label for="field-access"><?php echo Lang::txt('COM_FORUM_FIELD_ACCESS'); ?>:</label><br />
					<select name="fields[access]" id="field-access">
						<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->row->get('access')); ?>
					</select>
				</div>
			</fieldset>
		</div>
	</div>

	<?php if ($canDo->get('core.admin')): ?>
		<div class="col width-100">
			<fieldset class="panelform">
				<legend><span><?php echo Lang::txt('COM_FORUM_FIELDSET_RULES'); ?></span></legend>
				<?php echo $this->form->getLabel('rules'); ?>
				<?php echo $this->form->getInput('rules'); ?>
			</fieldset>
		</div>
	<?php endif; ?>

	<?php if ($this->row->get('parent')) { ?>
		<input type="hidden" name="fields[category_id]" value="<?php echo $this->row->get('category_id'); ?>" />
	<?php } else { ?>
		<input type="hidden" name="fields[parent]" value="<?php echo $this->row->get('parent'); ?>" />
	<?php } ?>
	<input type="hidden" name="fields[thread]" value="<?php echo $this->row->get('thread'); ?>" />
	<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />

	<input type="hidden" name="thread" value="<?php echo $this->row->get('thread'); ?>" />
	<input type="hidden" name="parent" value="<?php echo $this->row->get('parent'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
