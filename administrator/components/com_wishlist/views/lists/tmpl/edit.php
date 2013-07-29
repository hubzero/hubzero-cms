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

$canDo = WishlistHelper::getActions('list');

$text = ($this->task == 'edit' ? JText::_('COM_WISHLIST_EDIT') : JText::_('COM_WISHLIST_NEW'));

JToolBarHelper::title(JText::_('COM_WISHLIST') . ': ' . JText::_('COM_WISHLIST_LIST') . ': <small><small>[ ' . $text . ' ]</small></small>', 'wishlist.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	if (pressbutton =='resethits') {
		if (confirm(<?php echo JText::_('COM_WISHLIST_RESET_HITS_WARNING'); ?>)){
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
		alert(<?php echo JText::_('COM_WISHLIST_ERROR_MISSING_TITLE'); ?>);
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_WISHLIST_DETAILS'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<th class="key"><label for="field->category"><?php echo JText::_('COM_WISHLIST_CATEGORY'); ?>:</label></th>
						<td>
							<select name="fields[category]" id="field-category">
								<option value=""<?php echo ($this->row->category == '') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Category...'); ?></option>
								<option value="general"<?php echo ($this->row->category == 'general') ? ' selected="selected"' : ''; ?>><?php echo JText::_('general'); ?></option>
								<option value="group"<?php echo ($this->row->category == 'group') ? ' selected="selected"' : ''; ?>><?php echo JText::_('group'); ?></option>
								<option value="resource"<?php echo ($this->row->category == 'resource') ? ' selected="selected"' : ''; ?>><?php echo JText::_('resource'); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th class="key"><label for="field-referenceid"><?php echo JText::_('COM_WISHLIST_REFERENCEID'); ?>:</label></th>
						<td><input type="text" name="fields[referenceid]" id="field-referenceid" size="11" maxlength="11" value="<?php echo $this->escape(stripslashes($this->row->referenceid)); ?>" /></td>
					</tr>
					<tr>
						<th class="key"><label for="field-title"><?php echo JText::_('COM_WISHLIST_TITLE'); ?>:</label></th>
						<td><input type="text" name="fields[title]" id="field-title" size="30" maxlength="150" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" /></td>
					</tr>
					<tr>
						<th class="key"><label for="field-description"><?php echo JText::_('COM_WISHLIST_DESCRIPTION'); ?>:</label></th>
						<td><input type="text" name="fields[description]" id="field-description" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->description)); ?>" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta" summary="<?php echo JText::_('Metadata for this category'); ?>">
			<tbody>
				<tr>
					<th class="key"><?php echo JText::_('ID'); ?>:</th>
					<td>
						<?php echo $this->row->id; ?>
						<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->row->id; ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('Created'); ?>:</th>
					<td>
						<time datetime="<?php echo $this->row->created; ?>"><?php echo $this->row->created; ?></time>
						<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->row->created; ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('Created by'); ?>:</th>
					<td>
						<?php 
						$editor = JUser::getInstance($this->row->created_by);
						echo $this->escape(stripslashes($editor->get('name'))); 
						?>
						<input type="hidden" name="fields[create_by]" id="field-created_by" value="<?php echo $this->row->created_by; ?>" />
					</td>
				</tr>
			</tbody>
		</table>
		
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_WISHLIST_PARAMETERS'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="field-state"><?php echo JText::_('COM_WISHLIST_STATE'); ?>:</label></td>
						<td><input type="checkbox" name="fields[state]" id="field-state" value="1" <?php echo $this->row->state ? 'checked="checked"' : ''; ?> /></td>
					</tr>
					<tr>
						<td class="key"><label for="field-public"><?php echo JText::_('COM_WISHLIST_PUBLIC'); ?>:</label></td>
						<td><input type="checkbox" name="fields[public]" id="field-public" value="1" <?php echo $this->row->public ? 'checked="checked"' : ''; ?> /></td>
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
					<legend><span><?php echo JText::_('COM_WISHLIST_FIELDSET_RULES'); ?></span></legend>
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
