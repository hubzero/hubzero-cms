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

$canDo = CollectionsHelperPermissions::getActions('collection');

$text = ($this->task == 'edit' ? JText::_('Edit') : JText::_('New'));

JToolBarHelper::title(JText::_('COM_COLLECTIONS') . ': ' . $text, 'collection.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::spacer();
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
	if (form.greeting.value == ''){
		alert(<?php echo JText::_('Error! You must fill in a title!'); ?>);
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="index.php" method="post" name="adminForm" class="editform" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
		<legend><span><?php echo JText::_('Details'); ?></span></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key">
						<label for="field-object_type"><?php echo JText::_('Owner type'); ?>:</label><br />
						<select name="fields[object_type]" id="field-object_type">
							<!-- <option value="site"<?php if ($this->row->get('object_type') == 'site' || $this->row->get('object_type') == '') { echo ' selected="selected"'; } ?>>site</option> -->
							<option value="member"<?php if ($this->row->get('object_type') == 'member') { echo ' selected="selected"'; } ?>>member</option>
							<option value="group"<?php if ($this->row->get('object_type') == 'group') { echo ' selected="selected"'; } ?>>group</option>
						</select>
					</td>
					<td class="key">
						<label for="field-title"><?php echo JText::_('Owner ID'); ?>:</label><br />
						<input type="text" name="fields[object_id]" id="field-object_id" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->get('object_id'))); ?>" />
					</td>
				</tr>
				<tr>
					<td class="key" colspan="2">
						<label for="field-title"><?php echo JText::_('Title'); ?>:</label><br />
						<input type="text" name="fields[title]" id="field-title" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
					</td>
				</tr>
				<tr>
					<td class="key" colspan="2">
						<label for="field-alias"><?php echo JText::_('Alias'); ?>:</label><br />
						<input type="text" name="fields[alias]" id="field-alias" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
					</td>
				</tr>
				<tr>
					<td class="key" colspan="2">
						<label for="field-description"><?php echo JText::_('Description'); ?></label><br />
						<textarea name="fields[description]" id="field-description" cols="35" rows="10"><?php echo $this->escape($this->row->description('raw')); ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<fieldset class="adminform">
			<table class="meta">
				<tbody>
					<tr>
						<th class="key"><?php echo JText::_('Created By'); ?>:</th>
						<td>
							<?php 
							$editor = JUser::getInstance($this->row->get('created_by'));
							echo $this->escape(stripslashes($editor->get('name'))); 
							?>
							<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->escape($this->row->get('created_by')); ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo JText::_('Created Date'); ?>:</th>
						<td>
							<?php echo $this->row->get('created'); ?>
							<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->escape($this->row->get('created')); ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo JText::_('Likes'); ?>:</th>
						<td>
							<?php echo $this->row->get('positive', 0); ?>
							<input type="hidden" name="fields[positive]" id="field-positive" value="<?php echo $this->escape($this->row->get('positive', 0)); ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo JText::_('Posts'); ?>:</th>
						<td>
							<?php echo $this->row->count('post'); ?>
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo JText::_('Followers'); ?>:</th>
						<td>
							<?php echo $this->row->count('followers'); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Publishing'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<th class="key"><?php echo JText::_('State'); ?>:</th>
						<td>
							<select name="fields[state]">
								<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('Unpublished'); ?></option>
								<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('Published'); ?></option>
								<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('Archived'); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo JText::_('Access'); ?>:</th>
						<td>
							<select name="fields[access]">
								<option value="0"<?php if ($this->row->get('access') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('Public'); ?></option>
								<option value="1"<?php if ($this->row->get('access') == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('Registered'); ?></option>
								<!-- <option value="2"<?php if ($this->row->get('access') == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('Special'); ?></option> -->
								<option value="4"<?php if ($this->row->get('access') == 4) { echo ' selected="selected"'; } ?>><?php echo JText::_('Private'); ?></option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>