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

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('New Host'));

JToolBarHelper::title(JText::_('COM_TOOLS') . ': ' . JText::_('COM_TOOLS_HOST_TYPES') . ': ' . $text, 'tools.png');
JToolBarHelper::apply();
JToolBarHelper::save();
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('hosttype');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-name"><?php echo JText::_('COM_TOOLS_FIELD_NAME'); ?>:</label><br />
				<input type="text" name="fields[name]" id="field-name" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->name)); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-value"><?php echo JText::_('COM_TOOLS_FIELD_VALUE'); ?>:</label><br />
				<input type="text" name="fields[value]" id="field-value" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->value)); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-description"><?php echo JText::_('COM_TOOLS_FIELD_DESCRIPTION'); ?>:</label><br />
				<input type="text" name="fields[description]" id="field-description" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->description)); ?>" />
			</div>
		</fieldset>
	</div>
	<div class="col width-50 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th scope="row"><?php echo JText::_('COM_TOOLS_COL_BIT'); ?></th>
					<td><?php echo $this->escape($this->bit); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo JText::_('COM_TOOLS_COL_REFERENCES'); ?></th>
					<td><?php echo $this->escape($this->refs); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="fields[status]" value="check" />
	<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->row->name); ?>" />

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>