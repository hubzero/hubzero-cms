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

$canDo = KbHelper::getActions('category');

$text = ($this->task == 'edit' ? JText::_('COM_KB_EDIT') : JText::_('COM_KB_NEW'));

JToolBarHelper::title(JText::_('COM_KB') . ': ' . JText::_('COM_KB_CATEGORY') . ': ' . $text, 'kb.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

jimport('joomla.html.editor');
$editor = JEditor::getInstance();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	if (pressbutton =='resethits') {
		if (confirm("<?php echo JText::_('COM_KB_RESET_HITS_WARNING'); ?>")){
			submitform(pressbutton);
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// do field validation
	if (document.getElementById('field-title').value == ''){
		alert("<?php echo JText::_('COM_KB_ERROR_MISSING_TITLE'); ?>");
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_KB_DETAILS'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<th class="key"><label for="field->section"><?php echo JText::_('COM_KB_PARENT_CATEGORY'); ?>:</label></th>
						<td><?php echo KbHelperHtml::sectionSelect($this->sections, $this->row->get('section'), 'fields[section]'); ?></td>
					</tr>
					<tr>
						<th class="key"><label for="field-title"><?php echo JText::_('COM_KB_TITLE'); ?>:</label></th>
						<td><input type="text" name="fields[title]" id="field-title" size="30" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" /></td>
					</tr>
					<tr>
						<th class="key"><label for="field-alias"><?php echo JText::_('COM_KB_ALIAS'); ?>:</label></th>
						<td><input type="text" name="fields[alias]" id="field-alias" size="30" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" /></td>
					</tr>
					<tr>
						<th class="key"><label for="field-description"><?php echo JText::_('COM_KB_DESCRIPTION'); ?>:</label></th>
						<td><?php echo $editor->display('fields[description]', $this->escape(stripslashes($this->row->get('description'))), '', '', '50', '10'); ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th class="key"><?php echo JText::_('ID'); ?>:</th>
					<td>
						<?php echo $this->row->get('id'); ?>
						<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->escape($this->row->get('id')); ?>" />
					</td>
				</tr>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_KB_PARAMETERS'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="field-state"><?php echo JText::_('COM_KB_PUBLISH'); ?>:</label></td>
						<td><input type="checkbox" name="fields[state]" id="field-state" value="1" <?php echo $this->row->get('state') ? 'checked="checked"' : ''; ?> /></td>
					</tr>
					<tr>
						<td class="key"><label for="field-access"><?php echo JText::_('COM_KB_ACCESS_LEVEL'); ?>:</label></td>
						<td>
							<select name="fields[access]" id="field-access">
								<option value="0"<?php if ($this->row->get('access') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('Public'); ?></option>
								<option value="1"<?php if ($this->row->get('access') == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('Registered'); ?></option>
								<option value="2"<?php if ($this->row->get('access') == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('Special'); ?></option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>

	<?php /*if (version_compare(JVERSION, '1.6', 'ge')) { ?>
		<?php if ($canDo->get('core.admin')): ?>
			<div class="col width-100 fltlft">
				<fieldset class="panelform">
					<?php echo $this->form->getLabel('rules'); ?>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>
			</div>
			<div class="clr"></div>
		<?php endif; ?>
	<?php }*/ ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
