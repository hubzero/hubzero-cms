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

$canDo = CoursesHelper::getActions();

$text = ($this->task == 'edit' ? JText::_('EDIT') : JText::_('NEW'));
if (!$this->tmpl)
{
	JToolBarHelper::title(JText::_('COM_COURSES').': ' . JText::_('Assets') . ': ' . $text, 'courses.png');
	if ($canDo->get('core.edit'))
	{
		JToolBarHelper::save();
	}
	JToolBarHelper::cancel();
}

if ($this->row->get('id'))
{
	$id = $this->row->get('id');
}
else
{
	$id = 'tmp' . time() . rand(0, 10000);
}
//jimport('joomla.html.editor');
//$editor = JEditor::getInstance();
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
	if ($('field-title').value == '') {
		alert('<?php echo JText::_('COM_COURSES_ERROR_MISSING_INFORMATION'); ?>');
	} else {
		submitform(pressbutton);
	}
}
function saveAndUpdate()
{
	submitbutton('save');
	window.top.setTimeout(function(){
		var src = window.parent.document.getElementById('assets').src;
		window.parent.document.getElementById('assets').src = src;

		window.parent.document.assetform.close();
	}, 700);
}
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
<form action="index.php" method="post" name="adminForm" id="item-form" enctype="multipart/form-data">
<?php if ($this->tmpl == 'component') { ?>
	<fieldset>
		<div style="float: right">
			<button type="button" onclick="saveAndUpdate();"><?php echo JText::_('Save'); ?></button>
			<button type="button" onclick="window.parent.document.assetform.close();"><?php echo JText::_('Cancel'); ?></button>
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

			<input type="hidden" name="fields[lid]" value="<?php echo $this->escape($id); ?>" />

			<input type="hidden" name="tmpl" value="<?php echo $this->escape($this->tmpl); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>">
			<input type="hidden" name="task" value="save" />

			<table class="admintable">
				<tbody>
<!-- 					<tr>
						<th><?php echo JText::_('ID'); ?></th>
						<td><?php echo $this->escape($this->row->get('id')); ?></td>

						<th><?php echo JText::_('Course ID'); ?></th>
						<td><?php echo $this->escape($this->course_id); ?></td>
					</tr>
	<?php if ($this->row->get('created')) { ?>
					<tr>
						<th><?php echo JText::_('Created'); ?></th>
						<td<?php echo (!$this->row->get('created_by')) ? ' colspan="3"' : ''; ?>><?php echo $this->escape($this->row->get('created')); ?></td>
	<?php } ?>
	<?php if ($this->row->get('created_by')) { ?>
						<th><?php echo JText::_('Creator'); ?></th>
						<td><?php
						$creator = JUser::getInstance($this->row->get('created_by'));
						echo $this->escape(stripslashes($creator->get('name'))); ?></td>
	<?php } ?>
	<?php if ($this->row->get('created') || $this->row->get('created_by')) { ?>
					</tr>
	<?php } ?>
-->
					<tr>
						<th class="key"><label for="field-type"><?php echo JText::_('Type'); ?>:</label></th>
						<td>
							<select name="fields[type]" id="field-type">
								<option value="video"<?php if ($this->row->get('type') == 'video') { echo ' selected="selected"'; } ?>><?php echo JText::_('Video'); ?></option>
								<option value="file"<?php if ($this->row->get('type') == 'file') { echo ' selected="selected"'; } ?>><?php echo JText::_('File'); ?></option>
								<option value="form"<?php if ($this->row->get('type') == 'form') { echo ' selected="selected"'; } ?>><?php echo JText::_('Form'); ?></option>
								<option value="text"<?php if ($this->row->get('type') == 'text') { echo ' selected="selected"'; } ?>><?php echo JText::_('Text'); ?></option>
								<option value="url"<?php if ($this->row->get('type') == 'url') { echo ' selected="selected"'; } ?>><?php echo JText::_('URL'); ?></option>
							</select>
						</td>
						<th class="key"><label for="field-subtype"><?php echo JText::_('Subtype'); ?>:</label></th>
						<td>
							<select name="fields[subtype]" id="field-subtype">
								<option value="video"<?php if ($this->row->get('subtype') == 'video') { echo ' selected="selected"'; } ?>><?php echo JText::_('Video'); ?></option>
								<option value="embedded"<?php if ($this->row->get('subtype') == 'embedded') { echo ' selected="selected"'; } ?>><?php echo JText::_('Embedded'); ?></option>
								<option value="file"<?php if ($this->row->get('subtype') == 'file') { echo ' selected="selected"'; } ?>><?php echo JText::_('File'); ?></option>
								<option value="exam"<?php if ($this->row->get('subtype') == 'exam') { echo ' selected="selected"'; } ?>><?php echo JText::_('Exam'); ?></option>
								<option value="quiz"<?php if ($this->row->get('subtype') == 'quiz') { echo ' selected="selected"'; } ?>><?php echo JText::_('Quiz'); ?></option>
								<option value="homework"<?php if ($this->row->get('subtype') == 'homework') { echo ' selected="selected"'; } ?>><?php echo JText::_('Homework'); ?></option>
								<option value="note"<?php if ($this->row->get('subtype') == 'note') { echo ' selected="selected"'; } ?>><?php echo JText::_('Note'); ?></option>
								<option value="wiki"<?php if ($this->row->get('subtype') == 'wiki') { echo ' selected="selected"'; } ?>><?php echo JText::_('Wiki'); ?></option>
							</select>
						</td>
						<th class="key"><label for="field-state"><?php echo JText::_('State'); ?>:</label></th>
						<td>
							<select name="fields[state]" id="field-state">
								<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('Unpublished'); ?></option>
								<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('Published'); ?></option>
								<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('Trashed'); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th class="key"><label for="field-title"><?php echo JText::_('COM_COURSES_TITLE'); ?>:</label></th>
						<td colspan="3"><input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" size="50" /></td>
					</tr>
					<tr>
						<th class="key"><label for="field-url"><?php echo JText::_('URL'); ?>:</label></th>
						<td colspan="3"><input type="text" name="fields[url]" id="field-url" value="<?php echo $this->escape(stripslashes($this->row->get('url'))); ?>" size="50" /></td>
					</tr>
					<!-- <tr>
						<td class="key"><label for="upload"><?php echo JText::_('File'); ?>:</label></td>
						<td colspan="3"><input type="file" name="upload" id="upload" /></td>
					</tr> -->
					<tr>
						<th class="key"><label for="field-content"><?php echo JText::_('Content'); ?>:</label></th>
						<td colspan="3">
							<textarea name="fields[content]" id="field-content" rows="4" cols="35"><?php echo $this->escape(stripslashes($this->row->get('content'))); ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		<!-- </fieldset>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Files'); ?> - <?php echo DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . $this->course_id . DS . $id; ?></span></legend> -->
			<iframe width="100%" height="225" name="filelist" id="filelist" frameborder="0" src="index.php?option=<?php echo $this->option; ?>&amp;controller=media&amp;tmpl=component&amp;listdir=<?php echo $id; ?>&amp;course=<?php echo $this->escape($this->course_id); ?>"></iframe>
		</fieldset>
	</div>
	<div class="clr"></div>

	<?php echo JHTML::_('form.token'); ?>
</form>
