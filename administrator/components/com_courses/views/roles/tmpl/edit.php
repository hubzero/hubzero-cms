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

$canDo = CoursesHelper::getActions();

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_COURSES') . ': ' . JText::_('COM_COURSES_ROLES') . ': ' . $text, 'courses.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.getElementById('adminForm');

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	// form field validation
	var field = document.getElementById('field-title');
	if (field.value == '') {
		alert('<?php echo JText::_('COM_COURSES_ERROR_MISSING_TITLE'); ?>');
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="index.php" method="post" id="item-form" name="adminForm">
	<div class="col width-70 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-offering_id"><?php echo JText::_('COM_COURSES_OFFERING'); ?>:</label><br />
				<select name="fields[offering_id]" id="field-offering_id">
					<option value="0"<?php if (0 == $this->row->offering_id) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_NONE'); ?></option>
					<?php foreach ($this->courses as $course) { ?>
							<optgroup label="<?php echo $course->get('alias'); ?>">
						<?php foreach ($course->offerings() as $offering) { ?>
								<option value="<?php echo $offering->get('id'); ?>"<?php if ($offering->get('id') == $this->row->offering_id) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($offering->get('title'))); ?></option>
						<?php } ?>
							</optgroup>
					<?php } ?>
				</select>
			</div>
			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_('COM_COURSES_FIELD_TITLE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="fields[title]" id="field-title" size="50" value="<?php echo $this->escape($this->row->title); ?>" />
			</div>
			<div class="input-wrap" data-hint="<?php echo JText::_('COM_COURSES_FIELD_ALIAS_HINT'); ?>">
				<label for="field-alias"><?php echo JText::_('COM_COURSES_FIELD_ALIAS'); ?>:</label><br />
				<input type="text" name="fields[alias]" id="field-alias" size="50" value="<?php echo $this->escape($this->row->alias); ?>" />
				<span class="hint"><?php echo JText::_('COM_COURSES_FIELD_ALIAS_HINT'); ?></span>
			</div>

			<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />
		</fieldset>
	</div>
	<div class="col width-30 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th class="key"><?php echo JText::_('COM_COURSES_FIELD_OFFERING'); ?></th>
					<td>
						<?php echo $this->row->offering_id; ?>
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('COM_COURSES_FIELD_ID'); ?></th>
					<td>
						<?php echo $this->row->id; ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<?php echo JHTML::_('form.token'); ?>
</form>