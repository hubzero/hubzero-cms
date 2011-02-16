<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<table class="admintable">
	<tr>
		<td width="250" style="width: 250px;" align="right" class="key"><?php echo JText::_('RSFP_USER_EMAIL_SCRIPT'); ?></td>
		<td><?php echo JText::_('RSFP_USER_EMAIL_SCRIPT_DESC'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><textarea rows="20" cols="75" name="UserEmailScript" id="UserEmailScript" style="width:100%;"><?php echo $this->escape($this->form->UserEmailScript);?></textarea></td>
	</tr>
	<tr>
		<td width="250" style="width: 250px;" align="right" class="key"><?php echo JText::_('RSFP_ADMIN_EMAIL_SCRIPT'); ?></td>
		<td><?php echo JText::_('RSFP_ADMIN_EMAIL_SCRIPT_DESC'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><textarea rows="20" cols="75" name="AdminEmailScript" id="AdminEmailScript" style="width:100%;"><?php echo $this->escape($this->form->AdminEmailScript);?></textarea></td>
	</tr>
</table>