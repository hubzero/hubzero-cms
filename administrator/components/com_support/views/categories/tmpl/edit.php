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

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_SUPPORT_TICKETS') . ': ' . JText::_('COM_SUPPORT_CATEGORIES') . ': ' . $text, 'support.png');
JToolBarHelper::apply();
JToolBarHelper::save();
JToolBarHelper::spacer();
JToolBarHelper::cancel();
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	// form field validation
	if ($('#field-title').val() == '') {
		alert('<?php echo JText::_('COM_SUPPORT_CATEGORY_ERROR_NO_TEXT'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_('COM_SUPPORT_FIELD_TITLE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape($this->row->title); ?>" />
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_SUPPORT_FIELD_ALIAS_HINT'); ?>">
				<label for="field-alias"><?php echo JText::_('COM_SUPPORT_FIELD_ALIAS'); ?>:</label>
				<input type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape($this->row->alias); ?>" />
				<span class="hint"><?php echo JText::_('COM_SUPPORT_FIELD_ALIAS_HINT'); ?></span>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th class="key"><?php echo JText::_('COM_SUPPORT_FIELD_ID'); ?>:</th>
					<td>
						<?php echo $this->row->id; ?>
						<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->escape($this->row->id); ?>" />
					</td>
				</tr>
			<?php if ($this->row->created_by) { ?>
				<tr>
					<th class="key"><?php echo JText::_('COM_SUPPORT_FIELD_CREATED'); ?>:</th>
					<td>
						<?php echo JHTML::_('date', $this->row->created, 'Y-m-d H:i:s'); ?>
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('COM_SUPPORT_FIELD_CREATOR'); ?>:</th>
					<td>
						<?php
						$user = JUser::getInstance($this->row->created_by);
						echo $this->escape($user->get('name'));
						?>
					</td>
				</tr>
				<?php if ($this->row->modified_by) { ?>
					<tr>
						<th class="key"><?php echo JText::_('COM_SUPPORT_FIELD_MODIFIED'); ?>:</th>
						<td>
							<?php echo JHTML::_('date', $this->row->modified, 'Y-m-d H:i:s'); ?>
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo JText::_('COM_SUPPORT_FIELD_MODIFIER'); ?>:</th>
						<td>
							<?php
							$user = JUser::getInstance($this->row->modified_by);
							echo $this->escape($user->get('name'));
							?>
						</td>
					</tr>
				<?php } ?>
			<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>