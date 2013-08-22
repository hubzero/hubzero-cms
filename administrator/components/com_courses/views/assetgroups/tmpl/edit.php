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

$text = ($this->task == 'edit' ? JText::_('EDIT') : JText::_('NEW'));

$canDo = CoursesHelper::getActions('unit');

JToolBarHelper::title(JText::_('COM_COURSES').': <small><small>[ ' . $text . ' ' . JText::_('Asset group') . ' ]</small></small>', 'courses.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

JHTML::_('behavior.modal');

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();
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
window.addEvent("domready", function() {
	SqueezeBox.initialize({});
	document.assetform = SqueezeBox;
});
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_COURSES_DETAILS'); ?></span></legend>

			<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
			<input type="hidden" name="fields[unit_id]" value="<?php echo $this->row->get('unit_id'); ?>" />
			<input type="hidden" name="unit" value="<?php echo $this->row->get('unit_id'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
			<input type="hidden" name="task" value="save" />

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="field-parent"><?php echo JText::_('Parent'); ?>:</label></td>
						<td>
							<select name="fields[parent]" id="field-parent">
								<option value="0"<?php if (0 == $this->row->get('parent')) { echo ' selected="selected"'; } ?>><?php echo JText::_('(none)'); ?></option>
<?php foreach ($this->assetgroups as $assetgroup) { ?>
								<option value="<?php echo $assetgroup->get('id'); ?>"<?php if ($assetgroup->get('id') == $this->row->get('parent')) { echo ' selected="selected"'; } ?>><?php echo $assetgroup->treename . $this->escape(stripslashes($assetgroup->get('title'))); ?></option>
<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-title"><?php echo JText::_('COM_COURSES_TITLE'); ?>:</label></td>
						<td><input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" size="50" /></td>
					</tr>
					<tr>
						<td class="key"><label for="field-alias"><?php echo JText::_('Alias'); ?>:</label></td>
						<td>
							<input type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" size="50" />
							<span class="hint"><?php echo JText::_('Alhpa-numeric characters only. If no alias is provided, one will be generated from the title.'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key" valign="top"><label for="field-description"><?php echo JText::_('Description'); ?>:</label></td>
						<td>
							<textarea name="fields[description]" id="field-description" cols="40" rows="5"><?php echo $this->escape(stripslashes($this->row->get('description'))); ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Assets'); ?></span></legend>
			<?php if ($this->row->get('id')) { ?>
						<iframe width="100%" height="400" name="assets" id="assets" frameborder="0" src="index.php?option=<?php echo $this->option; ?>&amp;controller=assets&amp;tmpl=component&amp;scope=asset_group&amp;scope_id=<?php echo $this->row->get('id'); ?>&amp;course_id=<?php echo $this->offering->get('course_id'); ?>"></iframe>
			<?php } else { ?>
						<p><?php echo JText::_('Entry must be saved before assets can be added.'); ?></p>
			<?php } ?>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta" summary="<?php echo JText::_('COM_COURSES_META_SUMMARY'); ?>">
			<tbody>
				<tr>
					<th><?php echo JText::_('Unit ID'); ?></th>
					<td><?php echo $this->escape($this->row->get('unit_id')); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('ID'); ?></th>
					<td><?php echo $this->escape($this->row->get('id')); ?></td>
				</tr>
<?php if ($this->row->get('created')) { ?>
				<tr>
					<th><?php echo JText::_('Created'); ?></th>
					<td><?php echo $this->escape($this->row->get('created')); ?></td>
				</tr>
<?php } ?>
<?php if ($this->row->get('created_by')) { ?>
				<tr>
					<th><?php echo JText::_('Creator'); ?></th>
					<td><?php 
					$creator = JUser::getInstance($this->row->get('created_by'));
					echo $this->escape(stripslashes($creator->get('name'))); ?></td>
				</tr>
<?php } ?>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Publishing'); ?></span></legend>
			
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key" valign="top"><label for="field-state"><?php echo JText::_('State'); ?>:</label></td>
						<td>
							<select name="fields[state]" id="field-state">
								<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('Unpublished'); ?></option>
								<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('Published'); ?></option>
								<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('Deleted'); ?></option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>

	<?php echo JHTML::_('form.token'); ?>
</form>
