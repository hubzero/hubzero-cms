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

$canDo = ResourcesHelperPermissions::getActions('license');

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_RESOURCES') . ': ' . JText::_('COM_RESOURCES_LICENSES') . ': ' . $text, 'addedit.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::save();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// form field validation
	if ($('#field-title').val() == '') {
		alert('<?php echo JText::_('COM_RESOURCES_ERROR_MISSING_TITLE'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="index.php" method="post" id="item-form" name="adminForm">
	<div class="col width-70 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_('COM_RESOURCES_FIELD_TITLE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="fields[title]" id="field-title" size="35" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_RESOURCES_FIELD_ALIAS_HINT'); ?>">
				<label for="field-name"><?php echo JText::_('COM_RESOURCES_FIELD_ALIAS'); ?>:</label><br />
				<input type="text" name="fields[name]" id="field-name" size="35" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->name)); ?>" /><br />
				<span class="hint"><?php echo JText::_('COM_RESOURCES_FIELD_ALIAS_HINT'); ?></span>
			</div>

			<div class="input-wrap">
				<label for="field-url"><?php echo JText::_('COM_RESOURCES_FIELD_URL'); ?>:</label><br />
				<input type="text" name="fields[url]" id="field-url" size="35" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->url)); ?>" /><br />
			</div>

			<div class="input-wrap">
				<label for="field-text"><?php echo JText::_('COM_RESOURCES_FIELD_CONTENT'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<textarea name="fields[text]" id="field-text" cols="45" rows="15"><?php echo $this->escape(stripslashes($this->row->text)); ?></textarea>
				<?php //echo JFactory::getEditor()->display('fields[text]', $this->escape(stripslashes($this->row->text)), '', '', 45, 15, false, 'field-text', null, null, array('class' => 'minimal no-footer')); ?>
			</div>

			<input type="hidden" name="fields[ordering]" value="<?php echo $this->row->ordering; ?>" />
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
					<th><?php echo JText::_('COM_RESOURCES_FIELD_ID'); ?></th>
					<td><?php echo $this->row->id; ?></td>
				</tr>
			<?php if ($this->row->id) { ?>
				<tr>
					<th><?php echo JText::_('COM_RESOURCES_FIELD_ORDERING'); ?></th>
					<td><?php echo $this->row->ordering; ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<?php echo JHTML::_('form.token'); ?>
</form>