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

$text = ($this->task == 'editClass' ? JText::_('EDIT') : JText::_('NEW'));

JToolBarHelper::title(JText::_('COM_MEMBERS_QUOTA_CLASSES').': <small><small>[ '. $text.' ]</small></small>', 'user.png');
JToolBarHelper::apply('applyClass');
JToolBarHelper::save('saveClass');
JToolBarHelper::cancel('cancelClass');
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

<?php if ($this->getError()) : ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php endif; ?>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_MEMBERS_QUOTA_CLASS_LEGEND'); ?></span></legend>

			<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="saveClass" />

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="field-alias"><?php echo JText::_('COM_MEMBERS_QUOTA_ALIAS'); ?>:</label></td>
						<td><input <?php echo ($this->row->alias == 'default') ? 'readonly' : ''; ?> type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape(stripslashes($this->row->alias)); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="field-soft_blocks"><?php echo JText::_('COM_MEMBERS_QUOTA_SOFT_BLOCKS'); ?>:</label></td>
						<td><input type="text" name="fields[soft_blocks]" id="field-soft_blocks" value="<?php echo $this->escape(stripslashes($this->row->soft_blocks)); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="field-hard_blocks"><?php echo JText::_('COM_MEMBERS_QUOTA_HARD_BLOCKS'); ?>:</label></td>
						<td><input type="text" name="fields[hard_blocks]" id="field-hard_blocks" value="<?php echo $this->escape(stripslashes($this->row->hard_blocks)); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="field-soft_files"><?php echo JText::_('COM_MEMBERS_QUOTA_SOFT_FILES'); ?>:</label></td>
						<td><input type="text" name="fields[soft_files]" id="field-soft_files" value="<?php echo $this->escape(stripslashes($this->row->soft_files)); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="field-hard_files"><?php echo JText::_('COM_MEMBERS_QUOTA_HARD_FILES'); ?>:</label></td>
						<td><input type="text" name="fields[hard_files]" id="field-hard_files" value="<?php echo $this->escape(stripslashes($this->row->hard_files)); ?>" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_MEMBERS_QUOTA_ID'); ?></th>
					<td><?php echo $this->row->id; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_MEMBERS_QUOTA_CLASS_USER_COUNT'); ?></th>
					<td><?php echo ($this->row->id) ? $this->user_count : 0; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>
	<?php echo JHTML::_('form.token'); ?>
</form>