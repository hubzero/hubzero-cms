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

$canDo = CoursesHelper::getActions('role');

$text = ($this->task == 'edit' ? JText::_('Edit Role') : JText::_('New Role'));

JToolBarHelper::title(JText::_('COM_COURSES') . ': <small><small>[ ' . $text . ' ]</small></small>', 'courses.png');
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
		alert( 'Type must have a title' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="index.php" method="post" id="item-form" name="adminForm">
	<div class="col width-70 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Details'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="field-offering_id"><?php echo JText::_('Offering'); ?>:</label></td>
						<td>
							<select name="fields[offering_id]" id="field-offering_id">
								<option value="0"<?php if (0 == $this->row->offering_id) { echo ' selected="selected"'; } ?>><?php echo JText::_('[none]'); ?></option>
<?php foreach ($this->courses as $course) { ?>
								<optgroup label="<?php echo $course->get('alias'); ?>">
	<?php foreach ($course->offerings() as $offering) { ?>
								<option value="<?php echo $offering->get('id'); ?>"<?php if ($offering->get('id') == $this->row->offering_id) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($offering->get('title'))); ?></option>
	<?php } ?>
								</optgroup>
<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-title"><?php echo JText::_('Title'); ?>:</label></td>
						<td><input type="text" name="fields[title]" id="field-title" size="50" value="<?php echo $this->escape($this->row->title); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="field-alias"><?php echo JText::_('Alias'); ?>:</label></td>
						<td>
							<input type="text" name="fields[alias]" id="field-alias" size="50" value="<?php echo $this->escape($this->row->alias); ?>" /><br />
							<span class="hint"><?php echo JText::_('If no alias is provided, one will be generated from the title.'); ?></span>
						</td>
					</tr>
				</tbody>
			</table>

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
					<th class="key"><?php echo JText::_('Offering ID'); ?></th>
					<td>
						<?php echo $this->row->offering_id; ?>
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('ID'); ?></th>
					<td>
						<?php echo $this->row->id; ?>
					</td>
				</tr>
				<?php /*<tr>
					<th class="key"><?php echo JText::_('Created By'); ?>:</th>
					<td>
						<?php 
						$editor = JUser::getInstance($this->row->created_by);
						echo $this->escape($editor->get('name')); 
						?>
						<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->row->created_by; ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('Created Date'); ?>:</th>
					<td>
						<?php echo $this->row->created; ?>
						<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->row->created; ?>" />
					</td>
				</tr>
<?php if ($this->row->modified_by) { ?>
				<tr>
					<th class="key"><?php echo JText::_('Modified By'); ?>:</th>
					<td>
						<?php 
						$modifier = JUser::getInstance($this->row->modified_by);
						echo $this->escape($modifier->get('name')); 
						?>
						<input type="hidden" name="fields[modified_by]" id="field-modified_by" value="<?php echo $this->row->modified_by; ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('Modified Date'); ?>:</th>
					<td>
						<?php echo $this->row->modified; ?>
						<input type="hidden" name="fields[modified]" id="field-modified" value="<?php echo $this->row->modified; ?>" />
					</td>
				</tr>
<?php } */ ?>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<?php echo JHTML::_('form.token'); ?>
</form>