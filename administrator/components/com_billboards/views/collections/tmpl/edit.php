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

$text = ($this->task == 'editcollection' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_BILLBOARDS_MANAGER') . ': ' . JText::_('COM_BILLBOARDS_COLLECTIONS') . ': ' . $text, 'addedit.png');
JToolBarHelper::save('savecollection');
JToolBarHelper::cancel('cancelcollection');
JToolBarHelper::spacer();
JToolBarHelper::help('collection');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancelcollection') {
		submitformpressbutton);
		return;
	}

	// form field validation
	if ($('name').value == '') {
		alert('<?php echo JText::_('COM_BILLBOARDS_ERROR_COLLECTION_NO_NAME'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<fieldset class="adminform">
		<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

		<div class="input-wrap">
			<label for="name"><?php echo JText::_('COM_BILLBOARDS_FIELD_COLLECTION_NAME'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
			<input type="text" name="collection[name]" id="name" value="<?php echo $this->escape(stripslashes($this->row->name)); ?>" size="50" />
		</div>
	</fieldset>
	<input type="hidden" name="collection[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
