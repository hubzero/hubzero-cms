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

JToolBarHelper::title(JText::_('COM_SYSTEM_ROUTES_MANAGER') . ': ' . $text, 'config.png');
JToolBarHelper::save();
JToolBarHelper::cancel();

?>
<script type="text/javascript">
<!--
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	if (form.newurl.value == "") {
		alert("<?php echo JText::_('COM_SYSTEM_ROUTES_ERROR_MISSING_URL'); ?>");
	} else {
		submitform(pressbutton);
	}
}
//-->
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<?php
	if ($this->getError()) {
		echo '<p class="error">' . implode('<br />', $this->getErrors()) . '</p>';
	}
	?>
	<fieldset class="adminform">
		<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

		<div class="input-wrap">
			<label for="oldurl"><?php echo JText::_('COM_SYSTEM_ROUTES_FIELD_OLD_URL'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
			<input type="text" size="80" name="oldurl" id="oldurl" value="<?php echo $this->escape($this->row->oldurl); ?>" />
		</div>

		<div class="input-wrap" data-hint="<?php echo JText::_('COM_SYSTEM_ROUTES_FIELD_NEW_URL_HINT'); ?>">
			<label for="newurl"><?php echo JText::_('COM_SYSTEM_ROUTES_FIELD_NEW_URL'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
			<input type="text" size="80" name="newurl" id="newurl" value="<?php echo $this->escape($this->row->newurl); ?>" />
			<span class="hint"><?php echo JText::_('COM_SYSTEM_ROUTES_FIELD_NEW_URL_HINT'); ?></span>
		</div>
	</fieldset>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
