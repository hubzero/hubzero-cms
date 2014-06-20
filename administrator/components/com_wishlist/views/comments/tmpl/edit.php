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

JToolBarHelper::title(JText::_('COM_WISHLIST') . ': ' . JText::_('COM_WISHLIST_WISH') . ': ' . $text, 'wishlist.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('comment');

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
	if (document.getElementById('field-content').value == ''){
		alert('<?php echo JText::_('COM_WISHLIST_ERROR_MISSING_TEXT'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_WISHLIST_DETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-content"><?php echo JText::_('COM_WISHLIST_COMMENT'); ?>:</label><br />
				<textarea name="fields[content]" id="field-content" cols="35" rows="30"><?php echo $this->escape(preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', stripslashes($this->row->content))); ?></textarea>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_WISHLIST_REFERENCEID'); ?>:</th>
					<td>
						<?php echo $this->row->item_id; ?>
						<input type="hidden" name="fields[item_id]" value="<?php echo $this->escape($this->row->item_id); ?>" />
					</td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_WISHLIST_FIELD_CATEGORY'); ?>:</th>
					<td>
						<?php echo $this->row->item_type; ?>
						<input type="hidden" name="fields[item_type]" value="<?php echo $this->escape($this->row->item_type); ?>" />
					</td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_WISHLIST_FIELD_ID'); ?>:</th>
					<td>
						<?php echo $this->row->id; ?>
						<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->escape($this->row->id); ?>" />
					</td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_WISHLIST_FIELD_CREATED'); ?>:</th>
					<td>
						<time datetime="<?php echo $this->escape($this->row->created); ?>"><?php echo $this->escape($this->row->created); ?></time>
						<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->escape($this->row->created); ?>" />
					</td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_WISHLIST_FIELD_CREATOR'); ?>:</th>
					<td>
						<?php
						$editor = JUser::getInstance($this->row->created_by);
						echo ($editor) ? $this->escape(stripslashes($editor->get('name'))) : JText::_('unknown');
						?>
						<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->escape($this->row->created_by); ?>" />
					</td>
				</tr>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_WISHLIST_PARAMETERS'); ?></span></legend>

			<div class="input-wrap">
				<input type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" <?php echo $this->row->anonymous ? 'checked="checked"' : ''; ?> />
				<label for="field-anonymous"><?php echo JText::_('COM_WISHLIST_ANONYMOUS'); ?></label>
			</div>

			<div class="input-wrap">
				<label for="field-state"><?php echo JText::_('COM_WISHLIST_STATUS'); ?>:</label>
				<select name="fields[state]" id="field-state">
					<option value="0"<?php echo ($this->row->state == 0) ? ' selected="selected"' : ''; ?>><?php echo JText::_('JUNPUBLISHED'); ?></option>
					<option value="1"<?php echo ($this->row->state == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('JPUBLISHED'); ?></option>
					<option value="2"<?php echo ($this->row->state == 2) ? ' selected="selected"' : ''; ?>><?php echo JText::_('JTRASHED'); ?></option>
				</select>
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<?php /*
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
	*/ ?>

	<input type="hidden" name="wish" value="<?php echo $this->wish; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
