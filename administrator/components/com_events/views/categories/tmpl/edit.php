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
		alert("<?php echo JText::_('Category must have a name'); ?>");
	} else {
		<?php echo JFactory::getEditor()->save('text'); ?>

		submitform(pressbutton);
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_EVENTS_DETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_('COM_EVENTS_CAL_LANG_CATEGORY_TITLE'); ?>:</td>
				<input type="text" name="category[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" maxlength="50" />
			</div>
			<div class="input-wrap">
				<label for="field-alias"><?php echo JText::_('COM_EVENTS_CATEGORY_ALIAS'); ?>:</label>
				<input type="text" name="category[alias]" id="field-alias" value="<?php echo $this->escape(stripslashes($this->row->alias)); ?>" maxlength="255" />
			</div>
			<div class="input-wrap">
				<label><?php echo JText::_('COM_EVENTS_CAL_LANG_CATEGORY_ORDERING'); ?>:</td>
				<?php echo $this->orderlist; ?>
			</div>
			<div class="input-wrap">
				<label for="field-description"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_DESCRIPTION'); ?>:</label>
				<?php echo JFactory::getEditor()->display('category[description]', $this->escape($this->row->description), '', '', 50, 15, false, 'field-description', null, null, array('class' => 'minimal no-footer')); ?>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
	</div>
	<div class="clr"></div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<input type="hidden" name="category[extension]" value="com_events" />
	<input type="hidden" name="category[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="category[access]" value="<?php echo $this->row->access; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>
