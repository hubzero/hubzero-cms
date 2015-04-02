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
defined('_JEXEC') or die( 'Restricted access' );

$canDo = \Components\Members\Helpers\Permissions::getActions('component');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_PASSWORD_RULES') . ': '. $text, 'user.png');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
	Toolbar::cancel();
}
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_MEMBERS_PASSWORD_RULES'); ?></span></legend>

			<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />

			<div class="input-wrap">
				<label for="field-rule"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_RULES_RULE'); ?>:</label>
				<?php echo $this->rules_list; ?>
			</div>
			<div class="input-wrap">
				<label for="field-description"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_RULES_DESCRIPTION'); ?>:</label>
				<input type="text" name="fields[description]" id="field-description" value="<?php echo $this->escape(stripslashes($this->row->description)); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-failuremsg"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_RULES_FAILURE_MESSAGE'); ?>:</label>
				<input type="text" name="fields[failuremsg]" id="field-failuremsg" value="<?php echo $this->escape(stripslashes($this->row->failuremsg)); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-value"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_RULES_VALUE'); ?>:</label>
				<input type="text" name="fields[value]" id="field-value" value="<?php echo $this->escape(stripslashes($this->row->value)); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-group"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_RULES_GROUP'); ?>:</label>
				<input type="text" name="fields[group]" id="field-group" value="<?php echo $this->escape(stripslashes($this->row->grp)); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-class"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_RULES_CLASS'); ?>:</label>
				<input type="text" name="fields[class]" id="field-class" value="<?php echo $this->escape(stripslashes($this->row->class)); ?>" />
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo Lang::txt('COM_MEMBERS_PASSWORD_ID'); ?></th>
					<td><?php echo $this->row->id; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<?php echo JHTML::_('form.token'); ?>
</form>