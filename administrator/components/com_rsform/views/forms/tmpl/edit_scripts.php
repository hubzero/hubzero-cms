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
		<td width="250" style="width: 250px;" align="right" class="key"><?php echo JText::_('RSFP_SCRIPTS_DISPLAY'); ?></td>
		<td><?php echo JText::_('RSFP_SCRIPTS_DISPLAY_DESC'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><textarea rows="20" cols="75" name="ScriptDisplay" id="ScriptDisplay" style="width:100%;"><?php echo $this->escape($this->form->ScriptDisplay);?></textarea></td>
	</tr>
	<tr>
		<td width="250" style="width: 250px;" align="right" class="key"><?php echo JText::_('RSFP_SCRIPTS_PROCESS'); ?></td>
		<td><?php echo JText::_('RSFP_SCRIPTS_PROCESS_DESC'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><textarea rows="20" cols="75" name="ScriptProcess" id="ScriptProcess" style="width:100%;"><?php echo $this->escape($this->form->ScriptProcess);?></textarea></td>
	</tr>
	<tr>
		<td width="250" style="width: 250px;" align="right" class="key"><?php echo JText::_('RSFP_SCRIPTS_PROCESS2'); ?></td>
		<td><?php echo JText::_('RSFP_SCRIPTS_PROCESS2_DESC'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><textarea rows="20" cols="75" name="ScriptProcess2" id="ScriptProcess2" style="width:100%;"><?php echo $this->escape($this->form->ScriptProcess2);?></textarea></td>
	</tr>
</table>