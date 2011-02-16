<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
	function changeLayout(layout)
	{
		document.getElementById('FormLayoutImage').src = 'components/com_rsform/assets/images/layouts/' + layout + '.gif';
		document.getElementById('FormLayoutXHTML').style.display = 'none';
		
		if (layout.indexOf('xhtml') != -1)
			document.getElementById('FormLayoutXHTML').style.display = '';
	}
	
	function changeAdminEmail(value)
	{
		if (value == 1)
			document.adminForm.AdminEmailTo.disabled = false;
		else
			document.adminForm.AdminEmailTo.disabled = true;
	}
	
	function changeSubmissionAction(value)
	{
		document.getElementById('RedirectTo1').style.display = 'none';
		document.getElementById('RedirectTo2').style.display = 'none';
		document.getElementById('ThankYou1').style.display = 'none';
		document.getElementById('ThankYou2').style.display = 'none';
		
		if (value == 'redirect')
		{
			document.getElementById('RedirectTo1').style.display = '';
			document.getElementById('RedirectTo2').style.display = '';
		}
		else if (value == 'thankyou')
		{
			document.getElementById('ThankYou1').style.display = '';
			document.getElementById('ThankYou2').style.display = '';
		}
	}
	
	function submitbutton(task)
	{
		if (task == 'forms.cancel')
		{
			submitform(task);
			return;
		}
		else
		{
			var form = document.adminForm;
			
			jQuery(form.FormTitle).removeClass('thisformError');
			jQuery(form.ReturnUrl).removeClass('thisformError');
			
			if (form.FormTitle.value.length == 0)
			{
				alert('<?php echo JText::_('RSFP_WHATS_FORM_TITLE_VALIDATION', true); ?>');
				jQuery(form.FormTitle).addClass('thisformError');
				return;
			}
			if (form.SubmissionAction.value == 'redirect' && form.ReturnUrl.value.length == 0)
			{
				alert('<?php echo JText::_('RSFP_SUBMISSION_REDIRECT_WHERE_VALIDATION', true); ?>');
				jQuery(form.ReturnUrl).addClass('thisformError');
				return;
			}
			
			submitform(task);
		}
	}
	
	<?php if (RSFormProHelper::isJ16()) { ?>
	Joomla.submitbutton = submitbutton;
	<?php } ?>
</script>

<form method="post" action="index.php?option=com_rsform&amp;task=forms.new.stepthree" name="adminForm">
	<fieldset>
		<h3><?php echo JText::_('RSFP_NEW_FORM_STEP_2_1'); ?></h3>
		<table class="admintable">
			<tr>
				<td width="350" style="width: 350px;" align="right" class="key"><?php echo JText::_('RSFP_WHATS_FORM_TITLE'); ?></td>
				<td><input type="text" class="inputbox" size="55" name="FormTitle" value="" /></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_('RSFP_WHATS_FORM_TITLE_DESC'); ?></td>
			</tr>
			<tr>
				<td width="350" style="width: 350px;" align="right" class="key"><?php echo JText::_('RSFP_WHATS_FORM_LAYOUT'); ?></td>
				<td>
					<input type="radio" id="formLayoutInline" name="FormLayout" value="inline" onclick="changeLayout(this.value)" checked="checked" /> <label for="formLayoutInline"><?php echo JText::_('RSFP_LAYOUT_INLINE');?></label>
					<input type="radio" id="formLayout2lines" name="FormLayout" value="2lines" onclick="changeLayout(this.value)" /> <label for="formLayout2lines"><?php echo JText::_('RSFP_LAYOUT_2LINES');?></label>
					<input type="radio" id="formLayout2colsinline" name="FormLayout" value="2colsinline" onclick="changeLayout(this.value)" /> <label for="formLayout2colsinline"><?php echo JText::_('RSFP_LAYOUT_2COLSINLINE');?></label>
					<input type="radio" id="formLayout2cols2lines" name="FormLayout" value="2cols2lines" onclick="changeLayout(this.value)" /> <label for="formLayout2cols2lines"><?php echo JText::_('RSFP_LAYOUT_2COLS2LINES');?></label>
					<input type="radio" id="formLayoutInlineXhtml" name="FormLayout" value="inline-xhtml" onclick="changeLayout(this.value)" /> <label for="formLayoutInlineXhtml"><?php echo JText::_('RSFP_LAYOUT_INLINE_XHTML');?></label>
					<input type="radio" id="formLayout2linesXhtml" name="FormLayout" value="2lines-xhtml" onclick="changeLayout(this.value)" /> <label for="formLayout2linesXhtml"><?php echo JText::_('RSFP_LAYOUT_2LINES_XHTML');?></label>
				</td>
			</tr>
			<tr>
				<td><?php echo JText::_('RSFP_WHATS_FORM_LAYOUT_DESC'); ?></td>
				<td><img src="components/com_rsform/assets/images/layouts/inline.gif" id="FormLayoutImage" width="175"/></td>
			</tr>
			<tr id="FormLayoutXHTML" style="display: none;">
				<td colspan="2"><?php echo JText::_('RSFP_WHATS_FORM_LAYOUT_XHTML'); ?></td>
			</tr>
		</table>
		
		<h3><?php echo JText::_('RSFP_NEW_FORM_STEP_2_2'); ?></h3>
		<table class="admintable">
			<tr>
				<td width="350" style="width: 350px;" align="right" class="key"><?php echo JText::_('RSFP_WANT_ADMIN_EMAIL_RESULTS'); ?></td>
				<td><?php echo $this->lists['AdminEmail']; ?></td>
			</tr>
			<tr>
				<td width="350" style="width: 350px;" align="right" class="key"><?php echo JText::_('RSFP_WHERE_EMAIL_RESULTS'); ?></td>
				<td><input type="text" class="inputbox" size="55" name="AdminEmailTo" value="<?php echo $this->adminEmail; ?>" /></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_('RSFP_WHERE_EMAIL_RESULTS_DESC'); ?></td>
			</tr>
			<tr>
				<td width="350" style="width: 350px;" align="right" class="key"><?php echo JText::_('RSFP_WANT_SUBMITTER_EMAIL_RESULTS'); ?></td>
				<td><?php echo $this->lists['UserEmail']; ?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_('RSFP_WANT_SUBMITTER_EMAIL_RESULTS_DESC'); ?></td>
			</tr>
		</table>
		
		<h3><?php echo JText::_('RSFP_NEW_FORM_STEP_2_3'); ?></h3>
		<table class="admintable">
			<tr>
				<td width="350" style="width: 350px;" align="right" class="key"><?php echo JText::_('RSFP_WHAT_DO_YOU_WANT_SUBMISSION'); ?></td>
				<td><?php echo $this->lists['SubmissionAction']; ?></td>
			</tr>
			<tr id="RedirectTo1" style="display: none;">
				<td width="350" style="width: 350px;" align="right" class="key"><?php echo JText::_('RSFP_SUBMISSION_REDIRECT_WHERE'); ?></td>
				<td><input type="text" class="inputbox" size="55" name="ReturnUrl" value="" /></td>
			</tr>
			<tr id="RedirectTo2" style="display: none;">
				<td colspan="2"><?php echo JText::_('RSFP_SUBMISSION_REDIRECT_WHERE_DESC'); ?></td>
			</tr>
			<tr id="ThankYou1" style="display: none;">
				<td width="350" style="width: 350px;" align="right" class="key"><?php echo JText::_('RSFP_SUBMISSION_WHAT_THANKYOU'); ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr id="ThankYou2" style="display: none;">
				<td><?php echo JText::_('RSFP_SUBMISSION_WHAT_THANKYOU_DESC'); ?></td>
				<td><?php echo $this->editor->display('Thankyou', JText::_('RSFP_THANKYOU_DEFAULT'),500,250,70,10); ?></td>
			</tr>
		</table>
		
		<p><button type="button" onclick="submitbutton('forms.new.stepthree');"><?php echo JText::_('Next'); ?></button></p>
	
	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="task" value="forms.new.stepthree" />
	</fieldset>
</form>

<?php JHTML::_('behavior.keepalive'); ?>