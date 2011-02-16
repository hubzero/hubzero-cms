<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
JHTML::_('behavior.calendar');
?>
<form action="index.php?option=com_rsform&amp;task=forms.edit&amp;formId=<?php echo $this->form->FormId; ?>" method="post" name="adminForm" id="adminForm">
<div id="rsform_container">
	<div id="state"></div>
	<?php
	echo $this->tabs->startPane('form');
	echo $this->tabs->startPanel(JText::_('RSFP_FORM_COMPONENTS'), 'form-components');
		echo $this->loadTemplate('components');
	echo $this->tabs->endPanel();
	echo $this->tabs->startPanel(JText::_('RSFP_FORM_LAYOUT'), 'form-layout');
		echo $this->loadTemplate('layout');
	echo $this->tabs->endPanel();
	echo $this->tabs->startPanel(JText::_('RSFP_FORM_THEME'), 'form-theme');
		echo $this->loadTemplate('theme');
	echo $this->tabs->endPanel();
	echo $this->tabs->startPanel(JText::_('RSFP_CSS_JS'), 'form-layout');
		echo $this->loadTemplate('cssjs');
	echo $this->tabs->endPanel();
	echo $this->tabs->startPanel(JText::_('RSFP_FORM_EDIT'), 'form-edit');
		echo $this->loadTemplate('form');
	echo $this->tabs->endPanel();
	echo $this->tabs->startPanel(JText::_('RSFP_FORM_EDIT_ATTRIBUTES'), 'form-edit-attr');
		echo $this->loadTemplate('formattr');
	echo $this->tabs->endPanel();
	echo $this->tabs->startPanel(JText::_('RSFP_USER_EMAILS'), 'email-user');
		echo $this->loadTemplate('user');
	echo $this->tabs->endPanel();
	echo $this->tabs->startPanel(JText::_('RSFP_ADMIN_EMAILS'), 'email-admin');
		echo $this->loadTemplate('admin');
	echo $this->tabs->endPanel();
	echo $this->tabs->startPanel(JText::_('RSFP_FORM_SCRIPTS'), 'script-notify');
		echo $this->loadTemplate('scripts');
	echo $this->tabs->endPanel();
	echo $this->tabs->startPanel(JText::_('RSFP_EMAIL_SCRIPTS'), 'script-email');
		echo $this->loadTemplate('emailscripts');
	echo $this->tabs->endPanel();
	echo $this->tabs->startPanel(JText::_('RSFP_FORM_META_TAGS'), 'form-meta');
		echo $this->loadTemplate('meta');
	echo $this->tabs->endPanel();
	$this->triggerEvent('rsfp_bk_onAfterShowFormEditTabs');
	echo $this->tabs->endPane();
	?>
</div>
	
	<input type="hidden" name="tabposition" id="tabposition" value="0" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="formId" id="formId" value="<?php echo $this->form->FormId; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_rsform" />
</form>
	
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	
	if (pressbutton == 'forms.cancel')
	{
		removeExtraItems();
		submitform(pressbutton);
		return;
	}
	else if (pressbutton == 'forms.preview')
	{
		window.open('<?php echo JURI::root(); ?>index.php?option=com_rsform&formId=<?php echo $this->form->FormId; ?>');
		return;
	}
	else if (pressbutton == 'components.copy' || pressbutton == 'components.duplicate')
	{		
		if (form.boxchecked.value == 0)
		{
			alert('<?php echo JText::sprintf( 'Please make a selection from the list to', JText::_('copy')); ?>');
			return;
		}
		removeExtraItems();
		submitform(pressbutton);
	}
	else if (pressbutton == 'components.remove' || pressbutton == 'components.publish' || pressbutton == 'components.unpublish' || pressbutton == 'components.save')
	{
		removeExtraItems();
		submitform(pressbutton);
	}
	else
	{
		// do field validation
		if (document.getElementById('FormName').value == '')
			alert('<?php echo JText::_('RSFP_UNIQUE_NAME_MSG', true);?>');
		else
		{
			var dt = $('form').getElements('dt');
			for (var i=0; i<dt.length; i++)
			{
				if (dt[i].className.indexOf('open') != -1)
					$('tabposition').value = i;
			}
	
			submitform(pressbutton);
		}
	}
}

<?php if (RSFormProHelper::isJ16()) { ?>
	Joomla.submitbutton = submitbutton;
<?php } ?>

function removeExtraItems()
{
	var form = document.adminForm;
	
	form.formLayout.name = '';
	form.CSS.name = '';
	form.JS.name = '';
	form.ScriptDisplay.name = '';
	form.ScriptProcess.name = '';
	form.ScriptProcess2.name = '';
	form.UserEmailScript.name = '';
	form.AdminEmailScript.name = '';
	form.MetaDesc.name = '';
	form.MetaKeywords.name = '';
}

function listItemTask(cb, task)
{
	if (task == 'orderdown' || task == 'orderup')
	{
		var table = jQuery('#componentPreview');
		currentRow = jQuery(document.getElementById(cb)).parent().parent();		
		if (task == 'orderdown')
		{
			try { currentRow.insertAfter(currentRow.next()); }
			catch (dnd_e) { }
		}
		if (task == 'orderup')
		{
			try { currentRow.insertBefore(currentRow.prev()); }
			catch (dnd_e) { }
		}
		
		tidyOrder(true);
		return;
	}
	
	document.getElementById('state').innerHTML='Status: loading...';
	document.getElementById('state').style.color='rgb(255,0,0)';
	
	xml=buildXmlHttp();
	var url = 'index.php?option=com_rsform&task=' + task + '&format=raw&randomTime=' + Math.random();
	xml.open("POST", url, true);
	
	params = new Array();
	params.push('i=' + cb);
	params.push('componentId=' + document.getElementById(cb).value);
	params.push('formId=<?php echo $this->form->FormId; ?>');
	params = params.join('&');
	
	//Send the proper header information along with the request
	xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xml.setRequestHeader("Content-length", params.length);
	xml.setRequestHeader("Connection", "close");

	xml.send(params);
	xml.onreadystatechange=function()
	{
		if(xml.readyState==4)
		{
			var published_cell = jQuery(document.getElementById(cb)).parent().parent().children()[7];
			jQuery(published_cell).html(xml.responseText);
			
			document.getElementById('state').innerHTML='Status: ok';
			document.getElementById('state').style.color='';
			
			if (document.getElementById('FormLayoutAutogenerate').checked==true)
				generateLayout(<?php echo $this->form->FormId; ?>, 'no');
		}
	}
}

function saveorder(num, task)
{
	tidyOrder(true);
}

function returnQuickFields()
{
	var quickfields = new Array();
	
	<?php foreach ($this->quickfields as $quickfield) { ?>
	quickfields.push('<?php echo $quickfield; ?>');
	<?php } ?>
	
	return quickfields;
}

function enableAttachFile(value)
{
	if (value == 1)
	{
		document.getElementById('rsform_select_file').style.display = '';
		document.getElementById('UserEmailAttachFile').disabled = false;
	}
	else
	{
		document.getElementById('rsform_select_file').style.display = 'none';
		document.getElementById('UserEmailAttachFile').disabled = true;
	}
}

function enableEmailMode(type, value)
{
	var opener = type == 'User' ? 'UserEmailText' : 'AdminEmailText';
	var id = type == 'User' ? 'rsform_edit_user_email' : 'rsform_edit_admin_email';
	// HTML
	if (value == 1)
	{
		document.getElementById(id).href = 'index.php?option=com_rsform&task=richtext.show&opener=' + opener + '&tmpl=component&formId=<?php echo $this->form->FormId; ?>';
	}
	// Text
	else
	{
		document.getElementById(id).href = 'index.php?option=com_rsform&task=richtext.show&opener=' + opener + '&tmpl=component&formId=<?php echo $this->form->FormId; ?>&noEditor=1';
	}
}

function enableThankyou(value)
{
	if (value == 1)
	{
		document.getElementById('ShowContinue0').disabled = false;
		document.getElementById('ShowContinue1').disabled = false;
	}
	else
	{
		document.getElementById('ShowContinue0').disabled = true;
		document.getElementById('ShowContinue1').disabled = true;
	}
}

toggleQuickAdd();
</script>
	
<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>