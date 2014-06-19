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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_TOOLS') . ': '. $text, 'tools.png');
JToolBarHelper::save();
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('version');
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.getElementById('item-form');
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_TOOLS_FIELD_VERSION_DETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-command"><?php echo JText::_('COM_TOOLS_FIELD_COMMAND'); ?>:</label><br />
				<input type="text" name="fields[vnc_command]" id="field-command" value="<?php echo $this->escape(stripslashes($this->row->vnc_command));?>" size="50" />
			</div>

			<div class="input-wrap">
				<label for="field-timeout"><?php echo JText::_('COM_TOOLS_FIELD_TIMEOUT'); ?>:</label><br />
				<input type="text" name="fields[vnc_timeout]" id="field-timeout" value="<?php echo $this->escape(stripslashes($this->row->vnc_timeout));?>" size="50" />
			</div>

			<div class="input-wrap">
				<label for="field-hostreq"><?php echo JText::_('COM_TOOLS_FIELD_HOSTREQ'); ?>:</label><br />
				<input type="text" name="fields[hostreq]" id="field-hostreq" value="<?php echo $this->escape(stripslashes(implode(', ', $this->row->hostreq)));?>" size="50" />
			</div>

			<div class="input-wrap">
				<label for="field-mw"><?php echo JText::_('COM_TOOLS_FIELD_MIDDLEWARE'); ?>:</label><br />
				<input type="text" name="fields[mw]" id="field-mw" value="<?php echo $this->escape(stripslashes($this->row->mw));?>" size="50" />
			</div>

			<div class="input-wrap">
				<label for="field-params"><?php echo JText::_('COM_TOOLS_FIELD_PARAMS'); ?>:</label><br />
				<textarea name="fields[params]" id="field-params" cols="50" rows="10"><?php echo $this->escape(stripslashes($this->row->params));?></textarea>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_TOOLS_FIELD_TITLE'); ?>:</th>
					<td><?php echo $this->escape(stripslashes($this->parent->title));?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_TOOLS_FIELD_TOOLNAME'); ?>:</th>
					<td><?php echo $this->escape(stripslashes($this->parent->toolname));?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_TOOLS_FIELD_VERSION'); ?>:</th>
					<td><?php echo $this->escape($this->row->id);?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->parent->id; ?>" />
	<input type="hidden" name="fields[version]" value="<?php echo $this->row->id; ?>" />

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>