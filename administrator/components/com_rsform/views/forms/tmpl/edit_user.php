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
					<input name="UserEmailFrom" id="UserEmailFrom" value="<?php echo $this->escape($this->form->UserEmailFrom); ?>" size="35" style="width:500px;" onkeydown="closeAllDropdowns();" onclick="toggleDropdown(this);" />
				</td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><span style="color: red"><?php echo JText::_('RSFP_EMAILS_FROM_NAME'); ?></td>
				<td>
					<input name="UserEmailFromName" id="UserEmailFromName" value="<?php echo $this->escape($this->form->UserEmailFromName); ?>" size="35" style="width:500px;" onkeydown="closeAllDropdowns();" onclick="toggleDropdown(this);" />
				</td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_REPLY_TO'); ?></td>
				<td>
					<input name="UserEmailReplyTo" id="UserEmailReplyTo" value="<?php echo $this->escape($this->form->UserEmailReplyTo); ?>" style="width:500px;" onkeydown="closeAllDropdowns();" onclick="toggleDropdown(this);" />
				</td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><span style="color: red"><?php echo JText::_('RSFP_EMAILS_TO'); ?></span></td>
				<td>
					<input name="UserEmailTo" id="UserEmailTo" value="<?php echo $this->escape($this->form->UserEmailTo); ?>" style="width:500px;" onkeydown="closeAllDropdowns();" onclick="toggleDropdown(this);" />
				</td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_CC'); ?></td>
				<td><input name="UserEmailCC" id="UserEmailCC" value="<?php echo $this->escape($this->form->UserEmailCC); ?>" style="width:500px;" onkeydown="closeAllDropdowns();" onclick="toggleDropdown(this);" /></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_BCC'); ?></td>
				<td><input name="UserEmailBCC" id="UserEmailBCC" value="<?php echo $this->escape($this->form->UserEmailBCC); ?>" style="width:500px;" onkeydown="closeAllDropdowns();" onclick="toggleDropdown(this);" /></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><span style="color: red"><?php echo JText::_('RSFP_EMAILS_SUBJECT'); ?></span></td>
				<td><input name="UserEmailSubject" id="UserEmailSubject" value="<?php echo $this->escape($this->form->UserEmailSubject); ?>" style="width:500px;" onkeydown="closeAllDropdowns();" onclick="toggleDropdown(this);" /></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_ATTACH_FILE'); ?></td>
				<td><?php echo $this->lists['UserEmailAttach'];?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_ATTACH_FILE_LOCATION'); ?></td>
				<td><input name="UserEmailAttachFile" id="UserEmailAttachFile" value="<?php echo !empty($this->form->UserEmailAttachFile) ? $this->form->UserEmailAttachFile : JPATH_SITE.DS.'components'.DS.'com_rsform'.DS.'uploads'; ?>" style="width:350px;" <?php if (!$this->form->UserEmailAttach) { ?>disabled="disabled"<?php } ?> />
				<a href="index.php?option=com_rsform&amp;controller=files&amp;task=display&amp;folder=<?php echo @dirname($this->form->UserEmailAttachFile); ?>&amp;tmpl=component" class="rsform_icon rsform_upload modal" rel="{handler: 'iframe'}" id="rsform_select_file" <?php if (!$this->form->UserEmailAttach) { ?>style="display: none"<?php } ?>><?php echo JText::_('RSFP_SELECT_FILE'); ?></a>
				<br />
				<?php if ($this->form->UserEmailAttach && (!file_exists($this->form->UserEmailAttachFile) || !is_file($this->form->UserEmailAttachFile))) { ?>
					<strong style="color: red"><?php echo JText::_('RSFP_EMAILS_ATTACH_FILE_WARNING'); ?></strong>
				<?php } ?>
				</td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_MODE'); ?></td>
				<td><?php echo $this->lists['UserEmailMode']; ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><span style="color: red"><?php echo JText::_('RSFP_EMAILS_TEXT'); ?></span></td>
				<td>
					<a href="index.php?option=com_rsform&amp;task=richtext.show&amp;opener=UserEmailText&amp;formId=<?php echo $this->form->FormId;?>&amp;tmpl=component<?php if (!$this->form->UserEmailMode) { ?>&amp;noEditor=1<?php } ?>" class="rsform_icon rsform_edit rsmodal" id="rsform_edit_user_email" rel="{handler: 'iframe'}"><?php echo JText::_('RSFP_EMAILS_EDIT_TEXT'); ?></a>
					<a href="index.php?option=com_rsform&amp;task=richtext.preview&amp;opener=UserEmailText&amp;formId=<?php echo $this->form->FormId; ?>&amp;tmpl=component" class="rsform_icon rsform_preview modal" rel="{handler: 'iframe'}"><?php echo JText::_('PREVIEW'); ?></a>
				</td>
			</tr>
		</table>
	</td>
	<td valign="top">
		<button type="button" onclick="toggleQuickAdd();"><?php echo JText::_('RSFP_TOGGLE_QUICKADD'); ?></button>
			<div id="QuickAdd3">
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