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
				<td colspan="2"><div class="rsform_error"><?php echo JText::_('RSFP_EMAILS_DESC'); ?></div></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><span style="color: red"><?php echo JText::_('RSFP_EMAILS_FROM'); ?></span></td>
				<td>
					<input name="AdminEmailFrom" id="AdminEmailFrom" value="<?php echo $this->escape($this->form->AdminEmailFrom); ?>" size="35" style="width:500px;" onkeydown="closeAllDropdowns();" onclick="toggleDropdown(this);" />
				</td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><span style="color: red"><?php echo JText::_('RSFP_EMAILS_FROM_NAME'); ?></td>
				<td>
					<input name="AdminEmailFromName" id="AdminEmailFromName" value="<?php echo $this->escape($this->form->AdminEmailFromName); ?>" size="35" style="width:500px;" onkeydown="closeAllDropdowns();" onclick="toggleDropdown(this);" />
				</td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_REPLY_TO'); ?>:</td>
				<td><input name="AdminEmailReplyTo" id="AdminEmailReplyTo" value="<?php echo $this->escape($this->form->AdminEmailReplyTo); ?>" style="width:500px;" onkeydown="closeAllDropdowns();" onclick="toggleDropdown(this);" /></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><span style="color: red"><?php echo JText::_('RSFP_EMAILS_TO'); ?></span></td>
				<td><input name="AdminEmailTo" id="AdminEmailTo" value="<?php echo $this->escape($this->form->AdminEmailTo); ?>" style="width:500px;" onkeydown="closeAllDropdowns();" onclick="toggleDropdown(this);" /></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_CC'); ?></td>
				<td><input name="AdminEmailCC" id="AdminEmailCC" value="<?php echo $this->escape($this->form->AdminEmailCC); ?>" style="width:500px;" onkeydown="closeAllDropdowns();" onclick="toggleDropdown(this);" /></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_BCC'); ?></td>
				<td><input name="AdminEmailBCC" id="AdminEmailBCC" value="<?php echo $this->escape($this->form->AdminEmailBCC); ?>" style="width:500px;" onkeydown="closeAllDropdowns();" onclick="toggleDropdown(this);" /></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><span style="color: red"><?php echo JText::_('RSFP_EMAILS_SUBJECT'); ?></span></td>
				<td><input name="AdminEmailSubject" id="AdminEmailSubject" value="<?php echo $this->escape($this->form->AdminEmailSubject); ?>" style="width:500px;" onkeydown="closeAllDropdowns();" onclick="toggleDropdown(this);" /></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_MODE'); ?></td>
				<td><?php echo $this->lists['AdminEmailMode'];?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><span style="color: red"><?php echo JText::_('RSFP_EMAILS_TEXT'); ?></span></td>
				<td>
					<a href="index.php?option=com_rsform&amp;task=richtext.show&amp;opener=AdminEmailText&amp;formId=<?php echo $this->form->FormId;?>&amp;tmpl=component<?php if (!$this->form->AdminEmailMode) { ?>&amp;noEditor=1<?php } ?>" class="rsform_icon rsform_edit rsmodal" id="rsform_edit_admin_email" rel="{handler: 'iframe'}"><?php echo JText::_('RSFP_EMAILS_EDIT_TEXT'); ?></a>
					<a href="index.php?option=com_rsform&amp;task=richtext.preview&amp;opener=AdminEmailText&amp;formId=<?php echo $this->form->FormId; ?>&amp;tmpl=component" class="rsform_icon rsform_preview modal" rel="{handler: 'iframe'}"><?php echo JText::_('PREVIEW'); ?></a>
				</td>
			</tr>
		</table>
	</td>
	<td valign="top">
		<button type="button" onclick="toggleQuickAdd();"><?php echo JText::_('RSFP_TOGGLE_QUICKADD'); ?></button>
			<div id="QuickAdd4">
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