<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSForm_Forms extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $FormId = null;
	
	var $FormName = '';
	var $FormLayout = '';
	var $FormLayoutName = 'inline';
	var $FormLayoutAutogenerate = 1;
	var $CSS = '';
	var $JS = '';
	var $FormTitle = '';
	var $Lang = '';
	var $ReturnUrl = '';
	var $ShowThankyou = 1;
	var $Thankyou = '';
	var $ShowContinue = 1;
	var $UserEmailText = '';
	var $UserEmailTo = '';
	var $UserEmailCC = '';
	var $UserEmailBCC = '';
	var $UserEmailFrom = '';
	var $UserEmailReplyTo = '';
	var $UserEmailFromName = '';
	var $UserEmailSubject = '';
	var $UserEmailMode = 1;
	var $UserEmailAttach = 0;
	var $UserEmailAttachFile = '';
	var $AdminEmailText = '';
	var $AdminEmailTo = '';
	var $AdminEmailCC = '';
	var $AdminEmailBCC = '';
	var $AdminEmailFrom = '';
	var $AdminEmailReplyTo = '';
	var $AdminEmailFromName = '';
	var $AdminEmailSubject = '';
	var $AdminEmailMode = 1;
	var $ScriptProcess = '';
	var $ScriptProcess2 = '';
	var $ScriptDisplay = '';
	var $UserEmailScript = '';
	var $AdminEmailScript = '';
	var $MetaTitle = '';
	var $MetaDesc = '';
	var $MetaKeywords = '';
	var $Required = '(*)';
	var $ErrorMessage = '<p class="formRed">Please complete all required fields!</p>';
	var $MultipleSeparator = '\n';
	var $TextareaNewLines = 1;
	var $CSSClass = '';
	var $CSSId = 'userForm';
	var $CSSName = '';
	var $CSSAction = '';
	var $CSSAdditionalAttributes = '';
	var $AjaxValidation = 0;
	var $ThemeParams = '';
	
	var $Published = 1;
		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableRSForm_Forms(& $db)
	{
		parent::__construct('#__rsform_forms', 'FormId', $db);
	}
}