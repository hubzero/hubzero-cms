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
		<td width="160" style="width: 160px;" align="right" class="key"><?php echo JText::_('RSFP_META_DESC'); ?></td>
		<td><?php echo JText::_('RSFP_META_DESC_DESC'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><textarea rows="10" cols="75" name="MetaDesc" id="MetaDesc" style="width:100%;"><?php echo $this->escape($this->form->MetaDesc); ?></textarea></td>
	</tr>
	<tr>
		<td width="160" style="width: 160px;" align="right" class="key"><?php echo JText::_('RSFP_META_KEY'); ?></td>
		<td><?php echo JText::_('RSFP_META_KEY_DESC'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><textarea rows="10" cols="75" name="MetaKeywords" id="MetaKeywords" style="width:100%;"><?php echo $this->escape($this->form->MetaKeywords); ?></textarea></td>
	</tr>
	<tr>
		<td width="160" style="width: 160px;" align="right" class="key"><?php echo JText::_('RSFP_META_TITLE'); ?></td>
		<td><?php echo $this->lists['MetaTitle'];?></td>
	</tr>
</table>