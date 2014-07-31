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

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));
if (!$this->tmpl)
{
	JToolBarHelper::title(JText::_('COM_COURSES') . ': ' . JText::_('COM_COURSES_ASSETS') . ': ' . $text, 'courses.png');
	if ($canDo->get('core.edit'))
	{
		JToolBarHelper::save();
	}
	JToolBarHelper::cancel();
}

JHTML::_('behavior.framework', true);

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
	if ($('#field-title').val() == '') {
		alert('<?php echo JText::_('COM_COURSES_ERROR_MISSING_TITLE'); ?>');
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
<form action="index.php" method="post" name="adminForm" id="<?php echo ($this->tmpl == 'component') ? 'component-form' : 'item-form'; ?>" enctype="multipart/form-data">
<?php if ($this->tmpl == 'component') { ?>
	<fieldset>
		<div class="configuration">
			<div class="configuration-options">
				<button type="button" onclick="saveAndUpdate();"><?php echo JText::_('COM_COURSES_SAVE'); ?></button>
				<button type="button" onclick="window.parent.document.assetform.close();"><?php echo JText::_('COM_COURSES_CANCEL'); ?></button>
			</div>
			<?php echo $text; ?>
		</div>
	</fieldset>
<?php } ?>
	<div class="col width-100">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->row->get('id')); ?>" />
			<input type="hidden" name="fields[course_id]" value="<?php echo $this->escape($this->course_id); ?>" />
			<input type="hidden" name="fields[scope]" value="<?php echo $this->escape($this->scope); ?>" />
			<input type="hidden" name="fields[scope_id]" value="<?php echo $this->escape($this->scope_id); ?>" />

			<input type="hidden" name="fields[lid]" value="<?php echo $this->escape($id); ?>" />

			<input type="hidden" name="tmpl" value="<?php echo $this->escape($this->tmpl); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>">
			<input type="hidden" name="task" value="save" />

			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label for="field-type"><?php echo JText::_('COM_COURSES_FIELD_TYPE'); ?>:</label>
					<select name="fields[type]" id="field-type">
						<option value="video"<?php if ($this->row->get('type') == 'video') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_ASSET_TYPE_VIDEO'); ?></option>
						<option value="file"<?php if ($this->row->get('type') == 'file') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_ASSET_TYPE_FILE'); ?></option>
						<option value="form"<?php if ($this->row->get('type') == 'form') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_ASSET_TYPE_FORM'); ?></option>
						<option value="text"<?php if ($this->row->get('type') == 'text') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_ASSET_TYPE_TEXT'); ?></option>
						<option value="url"<?php if ($this->row->get('type') == 'url') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_ASSET_TYPE_URL'); ?></option>
					</select>
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<label for="field-subtype"><?php echo JText::_('COM_COURSES_FIELD_SUBTYPE'); ?>:</label>
					<select name="fields[subtype]" id="field-subtype">
						<option value="video"<?php if ($this->row->get('subtype') == 'video') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_ASSET_TYPE_VIDEO'); ?></option>
						<option value="embedded"<?php if ($this->row->get('subtype') == 'embedded') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_ASSET_TYPE_EMBEDDED'); ?></option>
						<option value="file"<?php if ($this->row->get('subtype') == 'file') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_ASSET_TYPE_FILE'); ?></option>
						<option value="exam"<?php if ($this->row->get('subtype') == 'exam') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_ASSET_TYPE_EXAM'); ?></option>
						<option value="quiz"<?php if ($this->row->get('subtype') == 'quiz') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_ASSET_TYPE_QUIZ'); ?></option>
						<option value="homework"<?php if ($this->row->get('subtype') == 'homework') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_ASSET_TYPE_HOMEWORK'); ?></option>
						<option value="note"<?php if ($this->row->get('subtype') == 'note') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_ASSET_TYPE_NOTE'); ?></option>
						<option value="wiki"<?php if ($this->row->get('subtype') == 'wiki') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_ASSET_TYPE_WIKI'); ?></option>
					</select>
				</div>
			</div>
			<div class="clr"></div>

			<div class="input-wrap">
				<label for="field-state"><?php echo JText::_('COM_COURSES_FIELD_STATE'); ?>:</label>
				<select name="fields[state]" id="field-state">
					<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_UNPUBLISHED'); ?></option>
					<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_PUBLISHED'); ?></option>
					<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_TRASHED'); ?></option>
				</select>
			</div>
			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_('COM_COURSES_FIELD_TITLE'); ?>:</label>
				<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" size="50" />
			</div>
			<div class="input-wrap">
				<label for="field-url"><?php echo JText::_('COM_COURSES_FIELD_URL'); ?>:</label>
				<input type="text" name="fields[url]" id="field-url" value="<?php echo $this->escape(stripslashes($this->row->get('url'))); ?>" size="50" />
			</div>
			<div class="input-wrap">
				<label for="field-content"><?php echo JText::_('COM_COURSES_FIELD_CONTENT'); ?>:</label>
				<textarea name="fields[content]" id="field-content" rows="4" cols="35"><?php echo $this->escape(stripslashes($this->row->get('content'))); ?></textarea>
			</div>

			<iframe width="100%" height="225" name="filelist" id="filelist" frameborder="0" src="index.php?option=<?php echo $this->option; ?>&amp;controller=media&amp;tmpl=component&amp;listdir=<?php echo $id; ?>&amp;course=<?php echo $this->escape($this->course_id); ?>"></iframe>
		</fieldset>
	</div>
	<div class="clr"></div>

	<?php echo JHTML::_('form.token'); ?>
</form>
