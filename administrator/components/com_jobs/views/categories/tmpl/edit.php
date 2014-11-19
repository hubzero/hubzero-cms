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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = JobsHelperPermissions::getActions('category');

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_JOBS') . ': ' . JText::_('COM_JOBS_CATEGORIES') . ': ' . $text, 'addedit.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('category');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.getElementById('item-form');

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// form field validation
	if (form.category.value == '') {
		alert('<?php echo JText::_('COM_JOBS_ERROR_MISSING_TITLE'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>
<form action="index.php" method="post" id="item-form" name="adminForm">
	<?php if ($this->task == 'edit') { ?>
	<p class="warning">
		<?php echo JText::_('COM_JOBS_WARNING_EDIT_CATEGORY'); ?>
	</p>
	<?php } ?>
	<fieldset class="adminform">
		<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

		<div class="input-wrap">
			<label for="category"><?php echo JText::_('COM_JOBS_FIELD_TITLE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label>
			<input type="text" name="category" id="category" size="30" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->category)); ?>" />
		</div>
		<div class="input-wrap">
			<label for="description"><?php echo JText::_('COM_JOBS_FIELD_DESCRIPTION'); ?>: </label>
			<input type="text" name="description" id="description"  maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->description)); ?>" />
		</div>

		<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="save" />
	</fieldset>

	<?php echo JHTML::_('form.token'); ?>
</form>