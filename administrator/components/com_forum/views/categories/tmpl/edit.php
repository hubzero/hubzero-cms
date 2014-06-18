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
defined('_JEXEC') or die( 'Restricted access' );

$canDo = ForumHelper::getActions('section');

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_FORUM') . ': ' . JText::_('COM_FORUM_CATEGORIES') . ': ' . $text, 'forum.png');
JToolBarHelper::spacer();
if ($canDo->get('core.edit'))
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('category');

$create_date = NULL;
if (intval($this->row->created) <> 0)
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
	if (document.getElementById('field-title').value == ''){
		alert( '<?php echo JText::_('COM_FORUM_ERROR_MISSING_TITLE'); ?>' );
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
					<label for="field-scope]"><?php echo JText::_('COM_FORUM_FIELD_SCOPE'); ?>:</label><br />
					<input type="text" name="fields[scope]" id="field-scope]" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->scope)); ?>" />
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<label for="field-scope_id"><?php echo JText::_('COM_FORUM_FIELD_SCOPE_ID'); ?>:</label><br />
					<input type="text" name="fields[scope_id]" id="field-scope_id" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->scope_id)); ?>" />
				</div>
			</div>
			<div class="clr"></div>

			<div class="input-wrap">
				<label for="field-section_id"><?php echo JText::_('COM_FORUM_FIELD_SECTION'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<select name="fields[section_id]" id="field-section_id">
					<option value="-1"><?php echo JText::_('COM_FORUM_FIELD_SECTION_SELECT'); ?></option>
				<?php foreach ($this->sections as $group => $sections) { ?>
					<optgroup label="<?php echo $this->escape(stripslashes($group)); ?>">
					<?php foreach ($sections as $section) { ?>
						<option value="<?php echo $section->id; ?>"<?php if ($this->row->section_id == $section->id) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($section->title)); ?></option>
					<?php } ?>
					</optgroup>
				<?php } ?>
				</select>
			</div>

			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_('COM_FORUM_FIELD_TITLE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="fields[title]" id="field-title" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_FORUM_FIELD_ALIAS_HINT'); ?>">
				<label for="field-alias"><?php echo JText::_('COM_FORUM_FIELD_ALIAS'); ?>:</label><br />
				<input type="text" name="fields[alias]" id="field-alias" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->alias)); ?>" />
				<span class="hint"><?php echo JText::_('COM_FORUM_FIELD_ALIAS_HINT'); ?></span>
			</div>

			<div class="input-wrap">
				<label for="field-description"><?php echo JText::_('COM_FORUM_FIELD_DESCRIPTION'); ?></label><br />
				<textarea name="fields[description]" id="field-description" cols="35" rows="5"><?php echo $this->escape(stripslashes($this->row->description)); ?></textarea>
			</div>
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
				<input class="option" type="checkbox" name="fields[closed]" id="field-closed" value="1"<?php if ($this->row->closed) { echo ' checked="checked"'; } ?> />
				<label for="field-closed"><?php echo JText::_('COM_FORUM_FIELD_CLOSED'); ?></label>
			</div>

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

	<input type="hidden" name="fields[scope]" value="<?php echo $this->row->scope; ?>" />
	<input type="hidden" name="fields[scope_id]" value="<?php echo $this->row->scope_id; ?>" />
	<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
