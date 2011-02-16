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
		<td valign="top" align="left" width="1%">
			<table>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_FORM_ACTION'); ?></td>
					<td><input name="CSSAction" value="<?php echo $this->escape($this->form->CSSAction); ?>" size="105" id="CSSAction" /></td>
				</tr>
				<tr>
					<td colspan="2"><?php echo JText::_('RSFP_FORM_ACTION_DESC'); ?></td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_FORM_CSS_ID'); ?></td>
					<td><input name="CSSId" value="<?php echo $this->escape($this->form->CSSId); ?>" size="105" id="CSSId" /></td>
				</tr>
				<tr>
					<td colspan="2"><?php echo JText::_('RSFP_FORM_CSS_ID_DESC'); ?></td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_FORM_CSS_CLASS'); ?></td>
					<td><input name="CSSClass" value="<?php echo $this->escape($this->form->CSSClass); ?>" size="105" id="CSSClass" /></td>
				</tr>
				<tr>
					<td colspan="2"><?php echo JText::_('RSFP_FORM_CSS_CLASS_DESC'); ?></td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_FORM_CSS_NAME'); ?></td>
					<td><input name="CSSName" value="<?php echo $this->escape($this->form->CSSName); ?>" size="105" id="CSSName" /></td>
				</tr>
				<tr>
					<td colspan="2"><?php echo JText::_('RSFP_FORM_CSS_NAME_DESC'); ?></td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_FORM_ADDITIONAL_ATTRIBUTES'); ?></td>
					<td><input name="CSSAdditionalAttributes" value="<?php echo $this->escape($this->form->CSSAdditionalAttributes); ?>" size="105" id="CSSAdditionalAttributes" /></td>
				</tr>
				<tr>
					<td colspan="2"><?php echo JText::_('RSFP_FORM_ADDITIONAL_ATTRIBUTES_DESC'); ?></td>
				</tr>
			</table>
		  </td>
			<td valign="top">
				
			</td>
	  </tr>

</table>