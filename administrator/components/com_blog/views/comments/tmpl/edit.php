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

$canDo = BlogHelper::getActions('entry');

$text = ($this->task == 'edit' ? JText::_('Edit comment') : JText::_('New comment'));
JToolBarHelper::title(JText::_('Blog Manager') . ': <small><small>[ ' . $text . ' ]</small></small>', 'blog.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
	JToolBarHelper::apply();
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

	// do field validation
	if ($('field-content').value == ''){
		alert(<?php echo JText::_('Error! You must fill in a comment!'); ?>);
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="index.php" method="post" name="adminForm" class="editform">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
		<legend><span><?php echo JText::_('Details'); ?></span></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><label for="field-anonymous"><?php echo JText::_('Anonymous'); ?></label></td>
					<td><input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1"<?php if ($this->row->anonymous) { echo ' checked="checked"'; } ?> /></td>
				</tr>
				<tr>
					<td class="key"><label for="field-content"><?php echo JText::_('Content'); ?></label></td>
					<td><textarea name="fields[content]" id="field-content" cols="35" rows="15"><?php echo $this->escape(stripslashes($this->row->content)); ?></textarea></td>
				</tr>
			</tbody>
		</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<fieldset class="adminform">
			<table class="meta" summary="<?php echo JText::_('Metadata for this comment'); ?>">
				<tbody>
					<tr>
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
					<tr>
						<th class="key"><?php echo JText::_('Entry #'); ?>:</th>
						<td>
							<?php echo $this->row->entry_id; ?>
							<input type="hidden" name="fields[entry_id]" id="field-entry_id" value="<?php echo $this->row->entry_id; ?>" />
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="fields[parent]" value="<?php echo $this->row->parent; ?>" />
	<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>

