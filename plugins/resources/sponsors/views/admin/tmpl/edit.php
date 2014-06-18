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

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('PLG_RESOURCES') . ': '.JText::_('PLG_RESOURCES_SPONSOR').': ' . $text, 'addedit.png');
JToolBarHelper::save();
JToolBarHelper::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	// form field validation
	if ($('title').value == '') {
		alert( '<?php echo JText::_('PLG_RESOURCES_SPONSORS_MISSING_TITLE'); ?>' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="index.php" method="post" id="item-form" name="adminForm">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="title"><?php echo JText::_('PLG_RESOURCES_SPONSORS_FIELD_TITLE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="fields[title]" id="title" size="30" maxlength="100" value="<?php echo $this->escape($this->row->title); ?>" />
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('PLG_RESOURCES_SPONSORS_FIELD_ALIAS_HINT'); ?>">
				<label for="alias"><?php echo JText::_('PLG_RESOURCES_SPONSORS_FIELD_ALIAS'); ?>:</label>
				<input type="text" name="fields[alias]" id="alias" size="30" maxlength="100" value="<?php echo $this->escape($this->row->alias); ?>" /><br />
				<span class="hint"><?php echo JText::_('PLG_RESOURCES_SPONSORS_FIELD_ALIAS_HINT'); ?></span>
			</div>

			<div class="input-wrap">
				<label><?php echo JText::_('PLG_RESOURCES_SPONSORS_FIELD_DESCRIPTION'); ?>:</label></td>
				<?php echo JFactory::getEditor()->display('fields[description]', stripslashes($this->row->description), '', '', '45', '10', false); ?>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<fieldset class="adminform">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo JText::_('PLG_RESOURCES_SPONSORS_FIELD_ID'); ?></th>
						<td><?php echo $this->row->id; ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('PLG_RESOURCES_SPONSORS_FIELD_CREATED'); ?></th>
						<td><?php echo $this->row->created; ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('PLG_RESOURCES_SPONSORS_FIELD_CREATOR'); ?></th>
						<td><?php echo $this->row->created_by; ?></td>
					</tr>
				<?php if ($this->row->modified) { ?>
					<tr>
						<th><?php echo JText::_('PLG_RESOURCES_SPONSORS_FIELD_MODIFIED'); ?></th>
						<td><?php echo $this->row->modified; ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('PLG_RESOURCES_SPONSORS_FIELD_MODIFIER'); ?></th>
						<td><?php echo $this->row->modified_by; ?></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>

			<p><?php echo JText::_('RESOURCES_REQUIRED_EXPLANATION'); ?></p>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="plugin" value="sponsors" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>