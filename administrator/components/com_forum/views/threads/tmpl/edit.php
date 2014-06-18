<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = ForumHelper::getActions('thread');

$text = ($this->row->parent ? JText::_('COM_FORUM_THREADS') : JText::_('COM_FORUM_POSTS')) . ': ';
$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_FORUM') . ': ' . $text, 'forum.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::save();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('post');

$post = new ForumModelPost($this->row);

$create_date = NULL;
if (intval( $this->row->created ) <> 0)
{
	$create_date = JHTML::_('date', $this->row->created);
}
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
		alert( '<?php echo JText::_('COM_FORUM_ERROR_MISSING_COMMENT'); ?>' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label for="field-scope"><?php echo JText::_('COM_FORUM_FIELD_SCOPE'); ?>:</label><br />
					<input type="text" name="fields[scope]" id="field-scope" maxlength="150" value="<?php echo $this->escape($this->row->scope); ?>" />
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<label for="field-scope_id"><?php echo JText::_('COM_FORUM_FIELD_SCOPE_ID'); ?>:</label><br />
					<input type="text" name="fields[scope_id]" id="field-scope_id" maxlength="11" value="<?php echo $this->escape($this->row->scope_id); ?>" />
				</div>
			</div>
			<div class="clr"></div>

			<div class="input-wrap">
				<label for="field-object_id"><?php echo JText::_('COM_FORUM_FIELD_OBJECT_ID'); ?>:</label><br />
				<input type="text" name="fields[object_id]" id="field-object_id" maxlength="11" value="<?php echo $this->escape($this->row->object_id); ?>" />
			</div>

		<?php if (!$this->row->parent) { ?>
			<div class="input-wrap">
				<label for="field-section_id"><?php echo JText::_('COM_FORUM_FIELD_SECTION'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<select name="fields[category_id]" id="field-category_id">
					<option value="-1"><?php echo JText::_('COM_FORUM_FIELD_CATEGORY_SELECT'); ?></option>
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

		<?php if ($this->row->parent) { ?>
			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_('COM_FORUM_FIELD_PARENT'); ?>:</label><br />
				<select name="fields[parent]" id="field-parent">
					<option value="0"><?php echo JText::_('COM_FORUM_FIELD_PARENT_SELECT'); ?></option>
				<?php foreach ($this->threads as $thread) { ?>
					<option value="<?php echo $thread->id; ?>"<?php if ($this->row->parent == $thread->id) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($thread->title)); ?></option>
				<?php } ?>
				</select>
			</div>
		<?php } ?>

			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_('COM_FORUM_FIELD_TITLE'); ?>:</label><br />
				<input type="text" name="fields[title]" id="field-title" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-comment"><?php echo JText::_('COM_FORUM_FIELD_COMMENTS'); ?> <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<textarea name="fields[comment]" id="field-comment" cols="35" rows="10"><?php echo $this->escape($post->content('raw')); ?></textarea>
			</div>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_FORUM_LEGEND_ATTACHMENTS'); ?></span></legend>

			<div class="input-wrap">
				<label for="upload"><?php echo JText::_('COM_FORUM_FIELD_FILE'); ?> <?php if ($post->attachment()->get('filename')) { echo '<strong>' . $this->escape(stripslashes($post->attachment()->get('filename'))) . '</strong>'; } ?></label><br />
				<input type="file" name="upload" id="upload" />
			</div>

			<div class="input-wrap">
				<label for="field-attach-descritpion"><?php echo JText::_('COM_FORUM_FIELD_DESCRIPTION'); ?></label><br />
				<input type="text" name="description" id="field-attach-descritpion" value="<?php echo $this->escape(stripslashes($post->attachment()->get('description'))); ?>" />
				<input type="hidden" name="attachment" value="<?php echo $this->escape(stripslashes($post->attachment()->get('id'))); ?>" />
			</div>

			<?php if ($post->attachment()->exists()) { ?>
				<p class="warning">
					<?php echo JText::_('COM_FORUM_FIELD_FILE_WARNING'); ?>
				</p>
			<?php } ?>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th class="key"><?php echo JText::_('COM_FORUM_FIELD_CREATOR'); ?>:</th>
					<td>
						<?php
						$editor = JUser::getInstance($this->row->created_by);
						echo $this->escape($editor->get('name'));
						?>
						<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->row->created_by; ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('COM_FORUM_FIELD_CREATED'); ?>:</th>
					<td>
						<?php echo $this->row->created; ?>
						<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->row->created; ?>" />
					</td>
				</tr>
			<?php if ($this->row->modified_by) { ?>
				<tr>
					<th class="key"><?php echo JText::_('COM_FORUM_FIELD_MODIFIER'); ?>:</th>
					<td>
						<?php
						$modifier = JUser::getInstance($this->row->modified_by);
						echo $this->escape($modifier->get('name'));
						?>
						<input type="hidden" name="fields[modified_by]" id="field-modified_by" value="<?php echo $this->row->modified_by; ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('COM_FORUM_FIELD_MODIFIED'); ?>:</th>
					<td>
						<?php echo $this->row->modified; ?>
						<input type="hidden" name="fields[modified]" id="field-modified" value="<?php echo $this->row->modified; ?>" />
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

			<div class="input-wrap">
				<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1"<?php if ($this->row->anonymous) { echo ' checked="checked"'; } ?> />
				<label for="field-anonymous"><?php echo JText::_('COM_FORUM_FIELD_ANONYMOUS'); ?></label>
			</div>

			<?php if (!$this->row->parent) { ?>
				<div class="input-wrap">
					<input class="option" type="checkbox" name="fields[sticky]" id="field-sticky" value="1"<?php if ($this->row->sticky) { echo ' checked="checked"'; } ?> />
					<label for="field-sticky"><?php echo JText::_('COM_FORUM_FIELD_STICKY'); ?></label>
				</div>
			<?php } ?>

			<div class="input-wrap">
				<label for="field-state"><?php echo JText::_('COM_FORUM_FIELD_STATE'); ?>:</label><br />
				<select name="fields[state]" id="field-state">
					<option value="0"<?php echo ($this->row->state == 0) ? ' selected="selected"' : ''; ?>><?php echo JText::_('JUNPUBLISHED'); ?></option>
					<option value="1"<?php echo ($this->row->state == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('JPUBLISHED'); ?></option>
					<option value="2"<?php echo ($this->row->state == 2) ? ' selected="selected"' : ''; ?>><?php echo JText::_('JTRASHED'); ?></option>
				</select>
			</div>

			<div class="input-wrap">
				<label for="field-access"><?php echo JText::_('COM_FORUM_FIELD_ACCESS'); ?>:</label><br />
				<select name="fields[access]" id="field-access">
					<option value="0"<?php echo ($this->row->access == 0) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_FORUM_ACCESS_PUBLIC'); ?></option>
					<option value="1"<?php echo ($this->row->access == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_FORUM_ACCESS_REGISTERED'); ?></option>
					<option value="2"<?php echo ($this->row->access == 2) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_FORUM_ACCESS_SPECIAL'); ?></option>
					<option value="3"<?php echo ($this->row->access == 3) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_FORUM_ACCESS_PROTECTED'); ?></option>
					<option value="4"<?php echo ($this->row->access == 4) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_FORUM_ACCESS_PRIVATE'); ?></option>
				</select>
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<?php if ($canDo->get('core.admin')): ?>
		<div class="col width-100 fltlft">
			<fieldset class="panelform">
				<legend><span><?php echo JText::_('COM_FORUM_FIELDSET_RULES'); ?></span></legend>
				<?php echo $this->form->getLabel('rules'); ?>
				<?php echo $this->form->getInput('rules'); ?>
			</fieldset>
		</div>
		<div class="clr"></div>
	<?php endif; ?>

	<?php if ($this->row->parent) { ?>
		<input type="hidden" name="fields[category_id]" value="<?php echo $this->row->category_id; ?>" />
	<?php } else { ?>
		<input type="hidden" name="fields[parent]" value="<?php echo $this->row->parent; ?>" />
	<?php } ?>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="parent" value="<?php echo $this->parent; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
