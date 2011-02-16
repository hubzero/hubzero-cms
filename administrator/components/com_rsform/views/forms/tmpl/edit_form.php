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
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('Published'); ?></td>
					<td><?php echo $this->lists['Published']; ?></td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_FORM_TITLE'); ?></td>
					<td><input name="FormTitle" value="<?php echo $this->escape($this->form->FormTitle); ?>" size="105" id="FormTitle" /></td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_FORM_NAME'); ?></td>
					<td><input name="FormName" value="<?php echo $this->escape($this->form->FormName); ?>" size="105" id="FormName" /></td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_RETURN_URL'); ?></td>
					<td><input name="ReturnUrl" value="<?php echo $this->escape($this->form->ReturnUrl); ?>" size="105" id="ReturnUrl" /></td>
				</tr>
				<tr>
					<td colspan="2"><?php echo JText::_('RSFP_RETURN_URL_DESC'); ?></td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SHOW_THANKYOU_MESSAGE'); ?></td>
					<td><?php echo $this->lists['ShowThankyou']; ?></td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_THANKYOU'); ?></td>
					<td>
						<a href="index.php?option=com_rsform&amp;task=richtext.show&amp;opener=Thankyou&amp;formId=<?php echo $this->form->FormId;?>&amp;tmpl=component" class="rsform_icon rsform_edit rsmodal" rel="{handler: 'iframe'}"><?php echo JText::_('RSFP_EDIT_THANKYOU'); ?></a>
						<a href="index.php?option=com_rsform&amp;task=richtext.preview&amp;opener=Thankyou&amp;formId=<?php echo $this->form->FormId; ?>&amp;tmpl=component" class="rsform_icon rsform_preview modal" rel="{handler: 'iframe'}"><?php echo JText::_('PREVIEW'); ?></a>
					</td>
				</tr>
				<tr>
					<td colspan="2"><?php echo JText::_('RSFP_THANKYOU_DESC'); ?></td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SHOW_CONTINUE'); ?></td>
					<td><?php echo $this->lists['ShowContinue']; ?></td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_REQUIRED'); ?></td>
					<td><input name="Required" value="<?php echo $this->escape($this->form->Required); ?>" size="105" id="Required" /></td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_ERROR_MESSAGE'); ?></td>
					<td>
						<a href="index.php?option=com_rsform&amp;task=richtext.show&amp;opener=ErrorMessage&amp;formId=<?php echo $this->form->FormId;?>&amp;tmpl=component" class="rsform_icon rsform_edit rsmodal" rel="{handler: 'iframe'}"><?php echo JText::_('RSFP_EDIT_ERROR_MESSAGE'); ?></a>
						<a href="index.php?option=com_rsform&amp;task=richtext.preview&amp;opener=ErrorMessage&amp;formId=<?php echo $this->form->FormId; ?>&amp;tmpl=component" class="rsform_icon rsform_preview modal" rel="{handler: 'iframe'}"><?php echo JText::_('PREVIEW'); ?></a>
					</td>
				</tr>
				<tr>
					<td colspan="2"><?php echo JText::_('RSFP_ERROR_MESSAGE_DESC'); ?></td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_MULTIPLE_SEPARATOR'); ?></td>
					<td><input name="MultipleSeparator" value="<?php echo $this->escape($this->form->MultipleSeparator); ?>" size="105" id="MultipleSeparator" /></td>
				</tr>
				<tr>
					<td colspan="2"><?php echo JText::_('RSFP_MULTIPLE_SEPARATOR_DESC'); ?></td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_TEXTAREA_NEW_LINES'); ?></td>
					<td><?php echo $this->lists['TextareaNewLines']; ?></td>
				</tr>
				<tr>
					<td colspan="2"><?php echo JText::_('RSFP_TEXTAREA_NEW_LINES_DESC'); ?></td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_AJAX_VALIDATION'); ?></td>
					<td><?php echo $this->lists['AjaxValidation']; ?></td>
				</tr>
				<tr>
					<td colspan="2"><?php echo JText::_('RSFP_AJAX_VALIDATION_DESC'); ?></td>
				</tr>
			</table>
		  </td>
			<td valign="top">
				<button type="button" onclick="toggleQuickAdd();"><?php echo JText::_('RSFP_TOGGLE_QUICKADD'); ?></button>
				<div id="QuickAdd2">
					<h3><?php echo JText::_('RSFP_QUICK_ADD');?></h3>
					<?php echo JText::_('RSFP_QUICK_ADD_DESC');?><br/><br/>
					<?php if(!empty($this->quickfields))
						foreach($this->quickfields as $quickfield) { ?>
							<strong><?php echo $quickfield;?></strong><br/>
							<pre>{<?php echo $quickfield; ?>:caption}</pre>
							<pre>{<?php echo $quickfield; ?>:value}</pre>
							<br/>
					<?php } ?>
				</div>
			</td>
	  </tr>

</table>