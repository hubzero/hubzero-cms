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

// No direct access
defined('_JEXEC') or die('Restricted access');

$canDo = WikiHelperPermissions::getActions('page');

$text = ($this->task == 'editrevision' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_WIKI') . ': ' . JText::_('COM_WIKI_REVISION') . ': ' . $text, 'wiki.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::save();
	JToolBarHelper::apply();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('revision');
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
	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-summary"><?php echo JText::_('COM_WIKI_FIELD_EDIT_SUMMARY'); ?>:</label><br />
				<input type="text" name="revision[summary]" id="field-summary" size="55" maxlength="255" value="<?php echo $this->escape(stripslashes($this->revision->get('summary'))); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-pagetext"><?php echo JText::_('COM_WIKI_FIELD_TEXT'); ?>:</label><br />
				<textarea name="revision[pagetext]" id="field-pagetext" cols="50" rows="40"><?php echo $this->escape(stripslashes($this->revision->get('pagetext'))); ?></textarea>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_WIKI_PAGE') . ' ' . JText::_('COM_WIKI_FIELD_TITLE'); ?>:</th>
					<td><?php echo $this->escape(stripslashes($this->page->get('title'))); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_WIKI_PAGE') . ' ' . JText::_('COM_WIKI_FIELD_PAGENAME'); ?>:</th>
					<td><?php echo $this->escape(stripslashes($this->page->get('pagename'))); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_WIKI_PAGE') . ' ' . JText::_('COM_WIKI_FIELD_SCOPE'); ?>:</th>
					<td><?php echo $this->escape(stripslashes($this->page->get('scope'))); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_WIKI_PAGE') . ' ' . JText::_('COM_WIKI_FIELD_GROUP'); ?>:</th>
					<td><?php echo $this->escape(stripslashes($this->page->get('group_cn'))); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_WIKI_PAGE') . ' ' . JText::_('COM_WIKI_FIELD_ID'); ?>:</th>
					<td><?php echo $this->escape($this->revision->get('pageid')); ?><input type="hidden" name="revision[pageid]" id="pageid" value="<?php echo $this->escape($this->revision->get('pageid')); ?>" /></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_WIKI_FIELD_ID'); ?>:</th>
					<td><?php echo $this->escape($this->revision->get('id')); ?><input type="hidden" name="revision[id]" id="revid" value="<?php echo $this->escape($this->revision->get('id')); ?>" /></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_WIKI_FIELD_REVISION'); ?>:</th>
					<td><?php echo $this->escape($this->revision->get('version')); ?><input type="hidden" name="revision[version]" id="version" value="<?php echo $this->escape($this->revision->get('version')); ?>" /></td>
				</tr>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_WIKI_FIELDSET_PARAMETERS'); ?></span></legend>

			<div class="input-wrap">
				<input type="checkbox" name="revision[minor_edit]" id="field-minor_edit" value="1" <?php echo $this->revision->get('minor_edit') ? 'checked="checked"' : ''; ?> />
				<label for="field-minor_edit"><?php echo JText::_('COM_WIKI_FIELD_MINOR_EDIT'); ?></label>
			</div>

			<div class="input-wrap">
				<label for="field-approved"><?php echo JText::_('COM_WIKI_FIELD_STATE'); ?>:</label><br />
				<select name="revision[approved]" id="field-approved">
					<option value="0"<?php echo $this->revision->get('approved') == 0 ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WIKI_STATE_NOT_APPROVED'); ?></option>
					<option value="1"<?php echo $this->revision->get('approved') == 1 ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WIKI_STATE_APPROVED'); ?></option>
					<option value="2"<?php echo $this->revision->get('approved') == 2 ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WIKI_STATE_TRASHED'); ?></option>
				</select>
			</div>

			<div class="input-wrap">
				<label><?php echo JText::_('COM_WIKI_FIELD_CREATOR'); ?>:</label><br />
				<?php echo JHTML::_('list.users', 'created_by', $this->revision->get('created_by'), 0, '', 'name', 1); ?>
			</div>

			<div class="input-wrap">
				<label for="field-created"><?php echo JText::_('COM_WIKI_FIELD_CREATED'); ?>:</label><br />
				<?php echo JHTML::_('calendar', $this->escape($this->revision->get('created')), 'revision[created]', 'field-created'); ?>
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="pageid" value="<?php echo $this->revision->get('pageid'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>