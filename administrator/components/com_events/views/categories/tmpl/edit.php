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

$text = ($this->task == 'editcat') ? JText::_('COM_EVENTS_EDIT') : JText::_('COM_EVENTS_NEW');
JToolBarHelper::title(JText::_('COM_EVENTS_EVENT').': '. $text.' '.JText::_('COM_EVENTS_CAL_LANG_EVENT_CATEGORY'), 'event.png');
JToolBarHelper::spacer();
JToolBarHelper::save();
//JToolBarHelper::spacer();
//JToolBarHelper::media_manager();
JToolBarHelper::cancel();

if ($this->row->image == '') {
	$this->row->image = 'blank.png';
}

$editor = JFactory::getEditor();
?>

<script language="javascript" type="text/javascript">
function submitbutton(pressbutton, section)
{
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	if (document.adminForm.name.value == ''){
		alert("Category must have a name");
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="index.php" method="post" name="adminForm">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_EVENTS_DETAILS'); ?></span></legend>

			<?php if (version_compare(JVERSION, '1.6', 'ge')) { ?>
			<input type="hidden" name="access" value="<?php echo $this->row->access; ?>" />
			<?php } ?>

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_CATEGORY_TITLE'); ?>:</td>
						<td><input type="text" name="category[title]" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" size="50" maxlength="50" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('COM_EVENTS_CATEGORY_ALIAS'); ?>:</td>
						<td><input type="text" name="category[alias]" value="<?php echo $this->escape(stripslashes($this->row->name)); ?>" size="50" maxlength="255" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_CATEGORY_ORDERING'); ?>:</td>
						<td><?php echo $this->orderlist; ?></td>
					</tr>
				<?php if (version_compare(JVERSION, '1.6', 'lt')) { ?>
					<tr>
						<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_ACCESSLEVEL'); ?>:</td>
						<td><?php echo $this->glist; ?></td>
					</tr>
				<?php } ?>
					<tr>
						<td class="key" style="vertical-align: top;"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_DESCRIPTION'); ?>:</td>
						<td><?php echo $editor->display('category[description]', stripslashes($this->row->description), '100%', 'auto', '45', '10', false); ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
	</div>
	<div class="clr"></div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="category[extension]" value="com_events" />
	<input type="hidden" name="category[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
