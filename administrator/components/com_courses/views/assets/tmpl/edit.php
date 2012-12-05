<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//

$canDo = CoursesHelper::getActions('asset');

$text = ($this->task == 'edit' ? JText::_('EDIT') : JText::_('NEW'));
if (!$this->tmpl) 
{
	JToolBarHelper::title(JText::_('COM_COURSES').': <small><small>[ ' . $text . ' ]</small></small>', 'courses.png');
	if ($canDo->get('core.edit')) 
	{
		JToolBarHelper::save();
	}
	JToolBarHelper::cancel();
}

//jimport('joomla.html.editor');
//$editor =& JEditor::getInstance();
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	
	// form field validation
	if ($('field-alias').value == '') {
		alert('<?php echo JText::_('COM_COURSES_ERROR_MISSING_INFORMATION'); ?>');
	} else if ($('field-title').value == '') {
		alert('<?php echo JText::_('COM_COURSES_ERROR_MISSING_INFORMATION'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getError()); ?></p>
<?php } ?>
<form action="index.php" method="post" name="adminForm" id="item-form">
<?php if ($this->tmpl == 'component') { ?>
	<fieldset>
		<div style="float: right">
			<button type="button" onclick="saveAndUpdate();"><?php echo JText::_('Save'); ?></button>
			<button type="button" onclick="window.parent.document.getElementById('sbox-window').close();"><?php echo JText::_('Cancel'); ?></button>
		</div>
		<div class="configuration">
			<?php echo $text; ?>
		</div>
	</fieldset>
<?php } ?>
	<div class="col width-100">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_COURSES_DETAILS'); ?></span></legend>

			<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->row->get('id')); ?>" />
			<input type="hidden" name="fields[course_id]" value="<?php echo $this->escape($this->course_id); ?>" />
			<input type="hidden" name="fields[scope]" value="<?php echo $this->escape($this->scope); ?>" />
			<input type="hidden" name="fields[scope_id]" value="<?php echo $this->escape($this->scope_id); ?>" />

			<input type="hidden" name="tmpl" value="<?php echo $this->escape($this->tmpl); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>">
			<input type="hidden" name="task" value="save" />

			<table class="admintable">
				<tbody>
					<tr>
						<th><?php echo JText::_('ID'); ?></th>
						<td><?php echo $this->escape($this->row->get('id')); ?></td>

						<th><?php echo JText::_('Course ID'); ?></th>
						<td><?php echo $this->escape($this->course_id); ?></td>
					</tr>
	<?php if ($this->row->get('created')) { ?>
					<tr>
						<th><?php echo JText::_('Created'); ?></th>
						<td colspan="3"><?php echo $this->escape($this->row->get('created')); ?></td>
					</tr>
	<?php } ?>
	<?php if ($this->row->get('created_by')) { ?>
					<tr>
						<th><?php echo JText::_('Creator'); ?></th>
						<td colspan="3"><?php 
						$creator = JUser::getInstance($this->row->get('created_by'));
						echo $this->escape(stripslashes($creator->get('name'))); ?></td>
					</tr>
	<?php } ?>
					<tr>
						<td class="key"><label for="field-type"><?php echo JText::_('Type'); ?>:</label></td>
						<td colspan="3">
							<select name="fields[type]" id="field-type">
								<option value="video"<?php if ($this->row->get('type') == 'video') { echo ' selected="selected"'; } ?>><?php echo JText::_('Video'); ?></option>
								<option value="file"<?php if ($this->row->get('type') == 'file') { echo ' selected="selected"'; } ?>><?php echo JText::_('File'); ?></option>
								<option value="test"<?php if ($this->row->get('type') == 'test') { echo ' selected="selected"'; } ?>><?php echo JText::_('Quiz/Test'); ?></option>
								<option value="note"<?php if ($this->row->get('type') == 'note') { echo ' selected="selected"'; } ?>><?php echo JText::_('Note'); ?></option>
								<option value="link"<?php if ($this->row->get('type') == 'link') { echo ' selected="selected"'; } ?>><?php echo JText::_('Link'); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-title"><?php echo JText::_('COM_COURSES_TITLE'); ?>:</label></td>
						<td colspan="3"><input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" size="50" /></td>
					</tr>
					<tr>
						<td class="key"><label for="field-url"><?php echo JText::_('URL'); ?>:</label></td>
						<td colspan="3"><input type="text" name="fields[url]" id="field-url" value="<?php echo $this->escape(stripslashes($this->row->get('url'))); ?>" size="50" /></td>
					</tr>
					<tr>
						<td class="key"><label for="upload"><?php echo JText::_('File'); ?>:</label></td>
						<td colspan="3"><input type="file" name="upload" id="upload" /></td>
					</tr>
					<tr>
						<td class="key"><label for="field-description"><?php echo JText::_('Description'); ?>:</label></td>
						<td colspan="3">
							<textarea name="fields[description]" id="field-description" rows="5" cols="35"><?php echo $this->escape(stripslashes($this->row->get('description'))); ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>

	<?php echo JHTML::_('form.token'); ?>
</form>
