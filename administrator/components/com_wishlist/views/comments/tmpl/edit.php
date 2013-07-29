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

JToolBarHelper::title(JText::_('COM_WISHLIST') . ': ' . JText::_('COM_WISHLIST_WISH') . ': <small><small>[ ' . $text . ' ]</small></small>', 'wishlist.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();

JHTML::_('behavior.tooltip');
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// do field validation
	if (document.getElementById('field-comment').value == ''){
		alert(<?php echo JText::_('COM_WISHLIST_ERROR_MISSING_TEXT'); ?>);
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
						<td>
							<label for="field-comment"><?php echo JText::_('COM_WISHLIST_COMMENT'); ?>:</label><br />
							<textarea name="fields[comment]" id="field-comment" cols="35" rows="30"><?php echo $this->escape(stripslashes($this->row->comment)); ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta" summary="<?php echo JText::_('Metadata'); ?>">
			<tbody>
				<tr>
					<th class="key"><?php echo JText::_('Reference ID'); ?>:</th>
					<td>
						<?php echo $this->row->referenceid; ?>
						<input type="hidden" name="fields[referenceid]" value="<?php echo $this->row->referenceid; ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('Category'); ?>:</th>
					<td>
						<?php echo $this->row->category; ?>
						<input type="hidden" name="fields[category]" value="<?php echo $this->row->category; ?>" />
					</td>
				</tr>
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
						<time datetime="<?php echo $this->row->added; ?>"><?php echo $this->row->added; ?></time>
						<input type="hidden" name="fields[added]" id="field-added" value="<?php echo $this->row->added; ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('Created by'); ?>:</th>
					<td>
						<?php 
						$editor = JUser::getInstance($this->row->added_by);
						echo ($editor) ? $this->escape(stripslashes($editor->get('name'))) : JText::_('unknown'); 
						?>
						<input type="hidden" name="fields[added_by]" id="field-added_by" value="<?php echo $this->row->added_by; ?>" />
					</td>
				</tr>
			</tbody>
		</table>
		
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_WISHLIST_PARAMETERS'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<th class="key"><label for="field-anonymous"><?php echo JText::_('COM_WISHLIST_ANONYMOUS'); ?>:</label></th>
						<td><input type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" <?php echo $this->row->anonymous ? 'checked="checked"' : ''; ?> /></td>
					</tr>
					<tr>
						<th class="key"><label for="field-status"><?php echo JText::_('COM_WISHLIST_STATUS'); ?>:</label></th>
						<td>
							<select name="fields[status]" id="field-status">
								<option value="0"<?php echo ($this->row->state == 0) ? ' selected="selected"' : ''; ?>><?php echo JText::_('unpublished'); ?></option>
								<option value="1"<?php echo ($this->row->state == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('published'); ?></option>
								<option value="2"<?php echo ($this->row->state == 2) ? ' selected="selected"' : ''; ?>><?php echo JText::_('deleted'); ?></option>
							</select>
						</td>
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
	
	<input type="hidden" name="wish" value="<?php echo $this->wish; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>
