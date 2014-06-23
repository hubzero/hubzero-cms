<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = ResourcesHelperPermissions::getActions('contributor');

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_RESOURCES') . ': ' . JText::_('COM_RESOURCES_AUTHORS') . ': ' . $text, 'forum.png');
JToolBarHelper::spacer();
if ($canDo->get('core.edit')) {
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

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
	<div class="col width-70 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<table class="admintable">
				<thead>
					<tr>
						<th scope="col"><?php echo JText::_('COM_RESOURCES_COL_RESOURCE'); ?></th>
						<th scope="col"><?php echo JText::_('COM_RESOURCES_COL_NAME'); ?></th>
						<th scope="col"><?php echo JText::_('COM_RESOURCES_COL_ORGANIZATION'); ?></th>
						<th scope="col"><?php echo JText::_('COM_RESOURCES_COL_ROLE'); ?></th>
					</tr>
				</thead>
				<tbody>
<?php
	$i = 0;
	foreach ($this->rows as $row)
	{
?>
					<tr>
						<td>
							<input type="text" name="fields[<?php echo $i; ?>][subid]" maxlength="250" size="4" value="<?php echo $this->escape(stripslashes($row->subid)); ?>" />
							<input type="hidden" name="fields[<?php echo $i; ?>][ordering]" value="<?php echo $this->escape(stripslashes($row->ordering)); ?>" />
						</td>
						<td>
							<input type="text" name="fields[<?php echo $i; ?>][name]" maxlength="250" value="<?php echo $this->escape(stripslashes($row->name)); ?>" />
						</td>
						<td>
							<input type="text" name="fields[<?php echo $i; ?>][organization]" maxlength="250" value="<?php echo $this->escape(stripslashes($row->organization)); ?>" />
						</td>
						<td>
							<select name="fields[<?php echo $i; ?>][role]">
								<option value=""<?php if ($row->role == '') { echo ' selected="selected"'; }?>><?php echo JText::_('COM_RESOURCES_ROLE_AUTHOR'); ?></option>
								<?php
								if ($this->roles)
								{
									foreach ($this->roles as $role)
									{
										?>
										<option value="<?php echo $this->escape($role->alias); ?>"<?php if ($row->role == $role->alias) { echo ' selected="selected"'; }?>><?php echo $this->escape(stripslashes($role->title)); ?></option>
										<?php
									}
								}
								?>
							</select>
						</td>
					</tr>
<?php
		$i++;
	}
?>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-30 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_RESOURCES_FIELDSET_AUTHOR'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-authorid"><?php echo JText::_('COM_RESOURCES_FIELD_ID'); ?>:</label><br />
				<input type="text" name="authorid" id="field-authorid" value="<?php echo $this->escape($this->authorid); ?>" />
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="id" value="<?php echo $this->authorid; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
