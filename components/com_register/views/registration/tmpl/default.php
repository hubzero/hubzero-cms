<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

	ximport('Hubzero_User_Helper');
	ximport('Hubzero_Environment');
	ximport('Hubzero_Geo');

	$html  = '<div id="content-header" class="full">'."\n";
	$html .= "\t".'<h2>'.JText::_('COM_REGISTER_'.strtoupper($this->task)).'</h2>'."\n";
	$html .= '</div><!-- / #content-header -->'."\n";
	$html .= '<div class="main section">'."\n";

	switch ($this->task) {
		case 'update':
			if (!empty($this->xregistration->_missing)) {
				$html .= '<div class="help">'."\n";
				$html .= $this->sitename.' requires additional registration information before your account can be used.';
				$html .= '<br />All fields marked <span class="required">required</span> must be filled in.'."\n";
				$html .= '</div>'."\n";
			}

			if (!JRequest::getVar('update', false, 'post')) {
				$this->showMissing = false;
			}
		break;

		case 'edit':
			if ($this->self) {
				$juser =& JFactory::getUser();

				$html .= '<div class="help">'."\n";
				$html .= "\t".'<h4>How do I change my password?</h4>'."\n";
				$html .= "\t".'<p>Passwords can be changed with <a href="'.JRoute::_('index.php?option=com_members&id='.$juser->get('id').'&task=changepassword').'" title="Change password form">this form</a>.</p>'."\n";
				$html .= '</div>'."\n";
			}
		break;

		case 'proxycreate':
			$html .= '<div class="help">'."\n";
			$html .= "\t".'<h4>Proxy Account Creation Questions</h4>'."\n";
			$html .= "\t".'<p>Simply fill out the form below and an account will be created for that person. ';
			$html .= 'You will then be shown the basic text of an email which you <strong>MUST</strong> then copy ';
			$html .= 'and paste and send to that person. This email will provide them a random initial password ';
			$html .= 'and their email confirmation link, and you may add any other information about contributed ';
			$html .= 'resources or the reason for their account you deem appropriate.</p>'."\n";
			//$html .= "\t".'<h4>What if I need to find the contents of the email to the user again?</h4>'."\n";
			//$html .= "\t".'<p>You can retrieve the same email template and contents at any time from the user\'s ';
			//$html .= '<a href="'.JRoute::_('index.php?option=com_members&task=whois').'">whois page</a>, under their confirmation email.</p>'."\n";
			$html .= '</div>'."\n";
		break;

		default:
		break;
	}
	
	//get return url
	$form_redirect = "";
	if(JRequest::getVar('return', null, 'get'))
	{
		// urldecode is due to round trip XSS protection added to this field, see ticket 1411
		$form_redirect = urldecode(JRequest::getVar('return', null, 'get'));
	}
	
	if ($this->task == 'create') {
		$form_action = JRoute::_('index.php?option='.$this->option."&return=".$form_redirect);
	} else {
		$form_action = JRoute::_('index.php?option='.$this->option.'&task='.$this->task);
	}
	$html .= '<form action="'. $form_action .'" method="post" id="hubForm">'."\n";


	$emailusers = Hubzero_User_Profile_Helper::find_by_email($this->registration['email']);

	if($this->task == 'create' && empty($this->xregistration->_invalid) && empty($this->xregistration->_missing)) {
		// Check to see if third party auth plugins are enabled
		$plugins        = JPluginHelper::getPlugin('authentication');
		$authenticators = array();

		foreach($plugins as $p)
		{
			if($p->name != 'hubzero')
			{
				$paramsClass = 'JParameter';
				if (version_compare(JVERSION, '1.6', 'ge'))
				{
					$paramsClass = 'JRegistry';
				}

				$pparams = new $paramsClass($p->params);
				$display = $pparams->get('display_name', ucfirst($p->name));
				$authenticators[] = array('name' => $p->name, 'display' => $display);
			}
		}

		// There are third party plugins, so show them on the registration form
		if(!empty($authenticators))
		{
			$doc =& JFactory::getDocument();
			$com_users = (version_compare(JVERSION, '1.6', 'ge')) ? 'com_users' : 'com_user';
			$doc->addStylesheet(DS . 'components' . DS . $com_users . DS . 'assets' . DS . 'css' . DS . 'providers.css');
			$html .= '<div class="explaination"><p class="info">You can choose to log in via one of these services, and we\'ll help you fill in the info below!</p></div>';
			$html .= '<fieldset>';
			$html .= '<legend>Connect With</legend>';
			$html .= '<div id="providers">';
			foreach($authenticators as $a)
			{
				$html .= '<a class="account-group" id="'.$a['name'].'" href="'.JRoute::_('index.php?option='.$com_users.'&view=login&authenticator='.$a['name']).'">';
				$html .= '<p>'.$a['display'].' account</p>';
				$html .= '</a>';
			}
			$html .= '</div>';
			$html .= '</fieldset>';
			$html .= '<div class="clear"></div>';
		}
	}

	if (($this->task == 'create' || $this->task == 'proxycreate') && $emailusers) {
		$html .= '<div class="error">'."\n";
		$html .= "\t".'<p>The email address "' . htmlentities($this->registration['email'],ENT_COMPAT,'UTF-8') . '" is already registered. If you have lost or forgotten this ' . $this->sitename . ' login information, we can resend it to you at that email address now:</p>'."\n";
		$html .= "\t".'<p class="submit"><input type="submit" name="resend" value="Email Existing Account Information" /></p>'."\n";
		$html .= "\t".'<p>If you are aware you already have another account registered to this email address, and are requesting another account because you need more resources, ' . $this->sitename . ' would be happy to work with you to raise your resource limits instead:</p>'."\n";
		$html .= "\t".'<p class="submit"><input type="submit" name="raiselimit" value="Raise Existing Resource Limits" /></p>'."\n";
		$html .= '</div>'."\n";
	}

	if (!empty($this->xregistration->_invalid) || !empty($this->xregistration->_missing)) {
		$html .= '<div class="error">Please correct the indicated invalid fields in the form below.';
	}
	if ($this->showMissing && !empty($this->xregistration->_missing)) {
		if ($this->task == 'update') {
			$html .= '<br />We are missing some vital information regarding your account! Please confirm the information below so we can better serve you. Thank you!';
		} else {
			$html .= '<br />Missing required information:';
		}
		$html .= '<ul>'."\n";
		foreach ($this->xregistration->_missing as $miss) {
			$html .= ' <li>'. $miss .'</li>'."\n";
		}
		$html .= '</ul>'."\n";
	}
	if (!empty($this->xregistration->_invalid) || !empty($this->xregistration->_missing)) {
		$html .= '</div>'."\n";
	}

	// Login information
	if ($this->registrationUsername != REG_HIDE || $this->registrationPassword != REG_HIDE) {
		$html .= "\t".'<div class="explaination">'."\n";
		$html .= "\t\t".'<p>' . JText::_('COM_REGISTER_CANNOT_CHANGE_USERNAME') . '</p>'."\n";

		if ($this->task == 'create' || $this->task == 'proxycreate') {
			$html .= "\t\t".'<p>'.JText::_('COM_REGISTER_PASSWORD_CHANGE_HINT').'</p>'."\n";
		}

		$html .= "\t".'</div>'."\n";

		$html .= "\t".'<fieldset>'."\n";
		$html .= "\t\t".'<legend>'.JText::_('COM_REGISTER_LOGIN_INFORMATION').'</legend>'."\n";

		// Username
		if ($this->registrationUsername == REG_READONLY) {
			$html .= "\t\t".'<label>'.JText::_('COM_REGISTER_USER_LOGIN').': '."\n";
			$html .= "\t\t\t".htmlentities($this->registration['login'],ENT_COMPAT,'UTF-8')."\n";
			$html .= "\t\t\t".'<input name="login" id="login" type="hidden" value="'. htmlentities($this->registration['login'],ENT_COMPAT,'UTF-8') .'" />'."\n";
			$html .= "\t\t".'</label>'."\n";
		} else if ($this->registrationUsername != REG_HIDE) {
			$required = ($this->registrationUsername == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_REGISTER_FORM_REQUIRED').'</span>' : '';
			$message = (!empty($this->xregistration->_invalid['login'])) ? '<span class="error">' . $this->xregistration->_invalid['login'] . '</span>' : '';
			$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

			$html .= "\t\t".'<div class="group">'."\n";
			$html .= "\t\t\t".'<label '.$fieldclass.'>'."\n";
			$html .= "\t\t\t\t".JText::_('COM_REGISTER_USER_LOGIN').': '.$required."\n";
			$html .= "\t\t\t\t".'<input name="login" id="userlogin" type="text" maxlength="32" value="'.htmlentities($this->registration['login'],ENT_COMPAT,'UTF-8') .'" />' ."\n";
			$html .= ($message) ? "\t\t\t\t" . $message . "\n" : '';
			$html .= "\t\t\t".'</label>'."\n";
			$html .= "\t\t\t".'<p class="hint" id="usernameHint">'.JText::_('COM_REGISTER_USERNAME_HINT').'</p>'."\n";
			$html .= "\t\t".'</div>'."\n";
		}

		// Password
		if ($this->registrationPassword != REG_HIDE) {
			$required = ($this->registrationPassword == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_REGISTER_FORM_REQUIRED').'</span>' : '';

			if (!empty($this->xregistration->_invalid['password']) && !is_array($this->xregistration->_invalid['password']))
				$message ='<span class="error">' . $this->xregistration->_invalid['password'] . '</span>';
			else
				$message = '';

			$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

			$html .= "\t\t".'<div class="group">'."\n";
			$html .= "\t\t\t".'<label '.$fieldclass.'>'."\n";
			$html .= "\t\t\t\t".JText::_('COM_REGISTER_PASSWORD').': '.$required."\n";
			$html .= "\t\t\t\t".'<input name="password" id="password" type="password" value="'. htmlentities($this->registration['password'],ENT_COMPAT,'UTF-8') .'" />'."\n";
			$html .= ($message) ? "\t\t\t\t" . $message . "\n" : '';
			$html .= "\t\t\t".'</label>'."\n";

			// Confirm password
			if ($this->registrationConfirmPassword != REG_HIDE) {
				$required = ($this->registrationConfirmPassword == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_REGISTER_FORM_REQUIRED').'</span>' : '';
				$message = (!empty($this->xregistration->_invalid['confirmPassword'])) ? '<span class="error">' . $this->xregistration->_invalid['confirmPassword'] . '</span>' : '';
				$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

				$html .= "\t\t\t".'<label '.$fieldclass.'>'."\n";
				$html .= "\t\t\t\t".JText::_('COM_REGISTER_CONFIRM_PASSWORD').': '.$required."\n";
				$html .= "\t\t\t\t".'<input name="password2" id="password2" type="password" value="'. htmlentities($this->registration['confirmPassword'],ENT_COMPAT,'UTF-8') .'" />'."\n";
				$html .= ($message) ? "\t\t\t\t" . $message . "\n" : '';
				$html .= "\t\t\t".'</label>'."\n";
			}

			$html .= "\t\t".'</div>'."\n";
			if (count($this->password_rules) > 0) {
				$html .= "\t\t"."<ul>"."\n";
				foreach ($this->password_rules as $rule)
				{
					if (!empty($rule))
					{
						if (!empty($this->xregistration->_invalid['password']) && is_array($this->xregistration->_invalid['password'])) {
							$err = in_array($rule, $this->xregistration->_invalid['password']);
						} else {
							$err = '';
						}
						$mclass = ($err)  ? ' class="error"' : '';
						$html .= "\t\t\t"."<li $mclass>".$rule."</li>"."\n";
					}
				}
				if (!empty($this->xregistration->_invalid['password']) && is_array($this->xregistration->_invalid['password'])) {
					foreach ($this->xregistration->_invalid['password'] as $msg) 
					{
						if (!in_array($msg,$this->password_rules)) {
							$html .= "\t\t\t".'<li class="error">'.$msg."</li>"."\n";
						}
					}
				}
				$html .= "\t\t"."</ul>"."\n";
			}
		}
		$hconfig =& JComponentHelper::getParams('com_register');
		if ($hconfig->get('passwordMeter')) {
			$html .= "\t".'<input type="hidden" id="passmeter" value="on" />'."\n";
		}
		$html .= "\t".'</fieldset>'."\n";
		$html .= "\t".'<div class="clear"></div>'."\n";
	}

	if ($this->registrationFullname != REG_HIDE
	 || $this->registrationEmail != REG_HIDE
	 || $this->registrationURL != REG_HIDE
	 || $this->registrationPhone != REG_HIDE
	) {
		$html .= "\t".'<div class="explaination">'."\n";

		if ($this->task == 'create') {
			$html .= "\t\t".'<p>'.JText::_('COM_REGISTER_ACTIVATION_EMAIL_HINT').'</p>'."\n";
		} else if ($this->task == 'proxycreate') {
			$html .= "\t\t".'<p>Once you create an account, the new account owner will be sent an email containing an activation link.</p>'."\n";
		}
		$html .= "\t\t".'<p>'.JText::_('COM_REGISTER_PRIVACY_HINT').'</p>'."\n";
		$html .= "\t".'</div>'."\n";

		$html .= "\t".'<fieldset>'."\n";
		$html .= "\t\t".'<legend>'.JText::_('COM_REGISTER_CONTACT_INFORMATION').'</legend>'."\n";

		// Name
		if ($this->registrationFullname != REG_HIDE) {
			$required = ($this->registrationFullname == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_REGISTER_FORM_REQUIRED').'</span>' : '';
			$message = (!empty($this->xregistration->_invalid['name'])) ? '<p class="error">' . $this->xregistration->_invalid['name'] . '</p>' : '';
			$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

			$givenName = '';
			$middleName = '';
			$surname = '';

			$bits = explode(' ', $this->registration['name']);
			$surname = array_pop($bits);
			if (count($bits) >= 1) {
				$givenName = array_shift($bits);
			}
			if (count($bits) >= 1) {
				$middleName = implode(' ', $bits);
			}

			$html .= "\t\t".'<div class="threeup group">'."\n";
			$html .= "\t\t\t".'<label'.$fieldclass.'>'."\n";
			$html .= "\t\t\t\t".JText::_('COM_REGISTER_FIRST_NAME').': '.$required."\n";
			$html .= "\t\t\t\t".'<input type="text" name="name[first]" value="'. htmlentities(trim($givenName), ENT_COMPAT, 'UTF-8') .'" />'."\n";
			$html .= "\t\t\t".'</label>'."\n";
			$html .= "\t\t\t".'<label>'."\n";
			$html .= "\t\t\t\t".JText::_('COM_REGISTER_MIDDLE_NAME').':'."\n";
			$html .= "\t\t\t\t".'<input type="text" name="name[middle]" value="'. htmlentities(trim($middleName), ENT_COMPAT, 'UTF-8') .'" />'."\n";
			$html .= "\t\t\t".'</label>'."\n";
			$html .= "\t\t\t".'<label'.$fieldclass.'>'."\n";
			$html .= "\t\t\t\t".JText::_('COM_REGISTER_LAST_NAME').': '.$required."\n";
			$html .= "\t\t\t\t".'<input type="text" name="name[last]" value="'. htmlentities(trim($surname), ENT_COMPAT, 'UTF-8') .'" />'."\n";
			$html .= "\t\t\t".'</label>'."\n";
			$html .= "\t\t".'</div>'."\n";
			$html .= ($message) ? "\t\t" . $message . "\n" : '';
		}

		// Email / confirm email
		if ($this->registrationEmail != REG_HIDE || $this->registrationConfirmEmail != REG_HIDE) {
			$html .= "\t\t".'<div class="group">'."\n";

			// Email
			if ($this->registrationEmail != REG_HIDE) {
				$required = ($this->registrationEmail == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_REGISTER_FORM_REQUIRED').'</span>' : '';
				$message = (!empty($this->xregistration->_invalid['email'])) ? RegistrationHelperHtml::error($this->xregistration->_invalid['email'],'span') : '';
				$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

				$html .= "\t\t\t".'<label '.$fieldclass.'>'."\n";
				$html .= "\t\t\t\t".JText::_('COM_REGISTER_VALID_EMAIL').': '.$required."\n";
				$html .= "\t\t\t\t".'<input name="email" id="email" type="text" value="'.htmlentities($this->registration['email'],ENT_COMPAT,'UTF-8').'" />'."\n";
				$html .= ($message) ? "\t\t\t\t" . $message . "\n" : '';
				$html .= "\t\t\t".'</label>'."\n";

			}

			// Confirm email
			if ($this->registrationConfirmEmail != REG_HIDE) {
				$message = '';

				if (!empty($this->xregistration->_invalid['email'])) {
					$this->registration['confirmEmail'] = '';
				}
				if (!empty($this->xregistration->_invalid['confirmEmail'])) {
					$message = RegistrationHelperHtml::error($this->xregistration->_invalid['confirmEmail'],'span');
				}

				$required = ($this->registrationConfirmEmail == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_REGISTER_FORM_REQUIRED').'</span>' : '';
				$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

				$html .= "\t\t\t".'<label'.$fieldclass.'>'."\n";
				$html .= "\t\t\t\t".JText::_('COM_REGISTER_CONFIRM_EMAIL').': '.$required."\n";
				$html .= "\t\t\t\t".'<input name="email2" id="email2" type="text" value="'.htmlentities($this->registration['confirmEmail'],ENT_COMPAT,'UTF-8').'" />'."\n";
				$html .= ($message) ? "\t\t\t\t" . $message . "\n" : '';
				$html .= "\t\t\t".'</label>'."\n";
			}

			$html .= "\t\t".'</div>'."\n";

			if ($this->registrationEmail != REG_HIDE) {
				$jconfig =& JFactory::getConfig();
				if ($this->task == 'proxycreate') {
					$html .= "\t\t".RegistrationHelperHtml::warning('Important! The user <strong>must</strong> confirm receipt of confirmation e-mail from '.RegistrationHelperHtml::obfuscate($jconfig->getValue('config.mailfrom')).' in order to complete registration.');
				} else if ($this->task == 'create') {
					$html .= "\t\t".RegistrationHelperHtml::warning(JText::sprintf('COM_REGISTER_YOU_MUST_CONFIRM_EMAIL', RegistrationHelperHtml::obfuscate($jconfig->getValue('config.mailfrom'))));
				} else {
					$html .= "\t\t".RegistrationHelperHtml::warning('Important! If you change your e-mail address you <strong>must</strong> confirm receipt of the confirmation e-mail from '.RegistrationHelperHtml::obfuscate($jconfig->getValue('config.mailfrom')).' in order to re-activate your account.');
				}
			}
		}

		// Website
		if ($this->registrationURL != REG_HIDE) {
			$required = ($this->registrationURL == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_REGISTER_FORM_REQUIRED').'</span>' : '';
			$message = (!empty($this->xregistration->_invalid['web'])) ? RegistrationHelperHtml::error($this->xregistration->_invalid['web']) : '';
			$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

			$html .= "\t\t\t".'<label'.$fieldclass.'>'."\n";
			$html .= "\t\t\t\t".JText::_('Website URL').': '.$required."\n";
			$html .= "\t\t\t\t".'<input name="web" id="web" type="text" value="'.htmlentities($this->registration['web'],ENT_COMPAT,'UTF-8').'" />'."\n";
			$html .= ($message) ? "\t\t\t\t" . $message . "\n" : '';
			$html .= "\t\t\t".'</label>'."\n";
		}

		// Telephone
		if ($this->registrationPhone != REG_HIDE) {
			$required = ($this->registrationPhone == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_REGISTER_FORM_REQUIRED').'</span>' : '';
			$message = (!empty($this->xregistration->_invalid['phone'])) ? RegistrationHelperHtml::error($this->xregistration->_invalid['phone']) : '';
			$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

			$html .= "\t\t\t".'<label '.$fieldclass.'>'."\n";
			$html .= "\t\t\t\t".JText::_('Telephone (###-###-####)').': '.$required."\n";
			$html .= "\t\t\t\t".'<input name="phone" id="phone" type="text" value="'.htmlentities($this->registration['phone'],ENT_COMPAT,'UTF-8').'" />'."\n";
			$html .= ($message) ? "\t\t\t\t" . $message . "\n" : '';
			$html .= "\t\t\t".'</label>'."\n";
		}

		$html .= "\t".'</fieldset>';
		$html .= '<div class="clear"></div>'."\n";
	}

	// Personal information section
	if ($this->registrationEmployment != REG_HIDE
	 || $this->registrationOrganization != REG_HIDE
	 || $this->registrationInterests != REG_HIDE
	 || $this->registrationReason != REG_HIDE
	) {
		$html .= "\t".'<div class="explaination">'."\n";
		$html .= "\t\t".'<p>By providing this information you are helping us target our efforts to our users. We will <em>not</em> disclose your personal information to others unless required by law';
		if ($this->registrationEmployment != REG_HIDE || $this->registrationOrganization != REG_HIDE ) {
			$html .= ', and we will <em>not</em> contact your employer';
		}
		$html .= '.</p>'."\n";
		if ($this->registrationCitizenship != REG_HIDE
		 || $this->registrationResidency != REG_HIDE
		 || $this->registrationSex != REG_HIDE
		 || $this->registrationDisability != REG_HIDE
		) {
			$html .= "\t\t".'<p>We operate as a community service and are committed to serving a diverse population of users. This information helps us assess our progress towards that goal.</p>'."\n";
		}
		$html .= "\t".'</div>'."\n";

		$html .= "\t".'<fieldset>'."\n";
		$html .= "\t\t".'<legend>'.JText::_('Personal Information').'</legend>'."\n";

		// Employment status
		if ($this->registrationEmployment != REG_HIDE) {
			$required = ($this->registrationEmployment == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_REGISTER_FORM_REQUIRED').'</span>' : '';
			$message = (!empty($this->xregistration->_invalid['orgtype'])) ? RegistrationHelperHtml::error($this->xregistration->_invalid['orgtype']) : '';
			$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

			$html .= "\t\t\t" . '<label' . $fieldclass . '>' . "\n";
			$html .= "\t\t\t\t" . JText::_('Employment Status') . ': ' . $required . "\n";
			$html .= "\t\t\t\t" . '<select name="orgtype" id="orgtype">' . "\n";

			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_register' . DS . 'tables' . DS . 'organizationtype.php');
			$database =& JFactory::getDBO();
			$rot = new RegisterOrganizationType($database);
			$types = $rot->getTypes();

			if (!$types || count($types) <= 0) {
				$types = array(
					'universityundergraduate' => 'University / College Undergraduate',
					'universitygraduate'      => 'University / College Graduate Student',
					'universityfaculty'       => 'University / College Faculty', // university
					'universitystaff'         => 'University / College Staff',
					'precollegestudent'       => 'K-12 (Pre-College) Student',
					'precollegefacultystaff'  => 'K-12 (Pre-College) Faculty/Staff', // precollege
					'nationallab'             => 'National Laboratory',
					'industry'                => 'Industry / Private Company',
					'government'              => 'Government Agency',
					'military'                => 'Military',
					'unemployed'              => 'Retired / Unemployed'
				);
			}

			if (empty($this->registration['orgtype']) || !empty($this->xregistration->_invalid['orgtype'])) {
				$html .= "\t\t\t\t\t" . '<option value="" selected="selected">' . JText::_('COM_REGISTER_FORM_SELECT_FROM_LIST') . '</option>' . "\n";
			}
			foreach ($types as $type => $title) {
				$html .= "\t\t\t\t\t".'<option value="' . $type . '"';
				if ($this->registration['orgtype'] == $type) {
					$html .= ' selected="selected"';
				}
				$html .= '>' . $title . '</option>' . "\n";
			}
			$html .= "\t\t\t\t" . '</select>' . "\n";
			$html .= ($message) ? "\t\t\t\t" . $message . "\n" : '';
			$html .= "\t\t\t" . '</label>' . "\n";
		}

		// Organization
		if ($this->registrationOrganization != REG_HIDE) {
			$required = ($this->registrationOrganization == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_REGISTER_FORM_REQUIRED').'</span>' : '';
			$message = (!empty($this->xregistration->_invalid['org'])) ? RegistrationHelperHtml::error($this->xregistration->_invalid['org']) : '';
			$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

			$html .= "\t\t\t".'<label '.$fieldclass.'>'."\n";
			$html .= "\t\t\t\t".JText::_('Organization or School').': '.$required."\n";
			$html .= "\t\t\t\t".'<select name="org" id="org">'."\n";

			$orgtext = $this->registration['org'];
			$org_known = 0;

			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_register' . DS . 'tables' . DS . 'organization.php');
			$database =& JFactory::getDBO();
			$xo = new RegisterOrganization($database);
			$orgs = $xo->getOrgs();

			if (!$orgs || count($orgs) <= 0) {
				$orgs[0] = 'Purdue University';
				$orgs[1] = 'University of Pennsylvania';
				$orgs[2] = 'University of California at Berkeley';
				$orgs[3] = 'Vanderbilt University';
			}

			foreach ($orgs as $org) {
				if ($org == $this->registration['org']) {
					$org_known = 1;
				}
			}
			$html .= "\t\t\t\t\t".'<option value=""';
			if (!$org_known) {
				$html .= ' selected="selected"';
			}
			$html .= '>';
			if ($org_known) {
				$html .= JText::_('(other / none)');
			} else {
				$html .= JText::_('COM_REGISTER_FORM_SELECT_OR_ENTER');
			}
			$html .= '</option>'."\n";

			foreach ($orgs as $org) {
				$html .= "\t\t\t\t\t".'<option value="'. htmlentities($org, ENT_COMPAT, 'UTF-8') .'"';
				if ($org == $this->registration['org']) {
					$orgtext = '';
					$html .= ' selected="selected"';
				}
				$html .= '>' . htmlentities($org, ENT_COMPAT, 'UTF-8') . '</option>' . "\n";
			}
			$html .= "\t\t\t\t" . '</select>' . "\n";

			$html .= ($message) ? "\t\t\t\t". $message . "\n" : '';
			$html .= "\t\t\t".'</label>'."\n";
			$html .= "\t\t\t".'<input name="orgtext" id="orgtext" type="text" value="'.htmlentities($this->registration['orgtext'], ENT_COMPAT, 'UTF-8').'" />'."\n";
		}

		// Reason
		if ($this->registrationReason != REG_HIDE) {
			$required = ($this->registrationReason == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_REGISTER_FORM_REQUIRED').'</span>' : '';
			$message = (!empty($this->xregistration->_invalid['reason'])) ? RegistrationHelperHtml::error($this->xregistration->_invalid['reason']) : '';
			$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

			$reasons = array(
				'Required for class',
				'Developing a new course',
				'Using in an existing course',
				'Using simulation tools for research',
				'Using as background for my research',
				'Learning about subject matter',
				'Keeping current in subject matter'
			);
			$otherreason = '';

			$html .= "\t\t".'<label'.$fieldclass.'>'."\n";
			$html .= "\t\t\t".JText::_('Reason for Account').': '.$required."\n";;
			$html .= "\t\t\t".'<select name="reason" id="reason">'."\n";
			if (!in_array($this->registration['reason'], $reasons)) {
				$otherreason = htmlentities($this->registration['reason'], ENT_COMPAT, 'UTF-8');
				$html .= "\t\t\t\t".'<option value="" selected="selected">'.JText::_('COM_REGISTER_FORM_SELECT_OR_ENTER').'</option>'."\n";
			}
			foreach ($reasons as $reason) {
				$html .= "\t\t\t\t".'<option value="'.$reason.'"';
				if ($this->registration['reason'] == $reason) {
					$html .= ' selected="selected"';
				}
				$html .= '>'.JText::_($reason).'</option>'."\n";
			}

			$html .= "\t\t\t".'</select>'."\n";
			$html .= "\t\t".'</label>'."\n";
			$html .= "\t\t".'<input name="reasontxt" id="reasontxt" type="text" value="'.htmlentities($otherreason, ENT_COMPAT, 'UTF-8').'" />'."\n";
			$html .= ($message) ? "\t\t" . $message . "\n" : '';
		}

		// Interests
		if ($this->registrationInterests != REG_HIDE) {
			$required = ($this->registrationInterests == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_REGISTER_FORM_REQUIRED').'</span>' : '';
			$message = (!empty($this->xregistration->_invalid['interests'])) ? RegistrationHelperHtml::error($this->xregistration->_invalid['interests']) : '';
			$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';

			if (!is_array($this->registration['edulevel']))
				$this->registration['edulevel'] = array();

			if (!is_array($this->registration['role']))
				$this->registration['role'] = array();

			$html .= "\t\t".'<fieldset'.$fieldclass.'>'."\n";
			$html .= "\t\t\t".'<legend>'.JText::_('What are you interested in?').' '.$required.'</legend>'."\n";

			$html .= "\t\t\t".'<p class="hint">'.JText::_('Materials for ... (Check all roles and levels you are interested in.)').'</p>'."\n";

			$html .= "\t\t\t".'<input type="hidden" name="interests" value="unspecified" />'."\n";

			$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="rolestudent" id="rolestudent" ';
			if (in_array("student", $this->registration['role']))
				$html .= 'checked="checked" value="on"';
			$html .= '/> '.JText::_('Students').'</label>'."\n";

			$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="roleeducator" id="roleeducator" ';
			if (in_array("educator", $this->registration['role']))
				$html .= 'checked="checked" value="on"';
			$html .= '/> '.JText::_('Educators').'</label>'."\n";

			$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="roleresearcher" id="roleresearcher" ';
			if (in_array("researcher", $this->registration['role']))
				$html .= 'checked="checked" value="on"';
			$html .= '/> '.JText::_('Researchers').'</label>'."\n";

			$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="roledeveloper" id="roledeveloper" ';
			if (in_array("developer", $this->registration['role']))
				$html .= 'checked="checked" value="on"';
			$html .= '/> '.JText::_('Developers').'</label>'."\n";

			$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="edulevelk12" id="edulevelk12" ';
			if (in_array("k12", $this->registration['edulevel']))
				$html .= 'checked="checked" value="on"';
			$html .= '/> '.JText::_('K - 12 (Pre-College)').'</label>'."\n";

			$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="edulevelundergraduate" id="edulevelundergraduate" ';
			if (in_array("undergraduate", $this->registration['edulevel']))
				$html .= 'checked="checked" value="on"';
			$html .= '/> '.JText::_('Undergraduate').'</label>'."\n";

			$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="edulevelgraduate" id="edulevelgraduate" ';
			if (in_array("graduate", $this->registration['edulevel']))
				$html .= 'checked="checked" value="on"';
			$html .= '/> '.JText::_('Graduate / Professional').'</label>'."\n";

			$html .= ($message) ? "\t\t\t" . $message . "\n" : '';
			$html .= "\t\t".'</fieldset>'."\n";
		}
		$html .= "\t".'</fieldset><div class="clear"></div>'."\n";
	}

	if ($this->registrationCitizenship != REG_HIDE
	 || $this->registrationResidency != REG_HIDE
	 || $this->registrationSex != REG_HIDE
	 || $this->registrationDisability != REG_HIDE
	 || $this->registrationHispanic != REG_HIDE
	 || $this->registrationRace != REG_HIDE
	) {
		$html .= "\t".'<div class="explaination">'."\n";

		if ($this->registrationHispanic != REG_HIDE) {
			$html .= "\t\t".'<p>All users are asked to clarify if they are of Hispanic origin or descent.';
		}
		if ($this->registrationRace != REG_HIDE) {
			$html .= ', but only United States citizens and Permanent Resident Visa holders need answer the next section';
		}
		$html .= '</p>'."\n";

		$html .= "\t\t".'<p>Please provide this information if you feel comfortable doing so. This information will not affect the level of service you receive.</p>'."\n";
		$html .= "\t".'</div>'."\n";
		$html .= "\t".'<fieldset>'."\n";
		$html .= "\t\t".'<legend>'.JText::_('Demographics').'</legend>'."\n";

		// Citizenship
		if ($this->registrationCitizenship != REG_HIDE) {
			$required = ($this->registrationCitizenship == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_REGISTER_FORM_REQUIRED').'</span>' : '';
			$message = (!empty($this->xregistration->_invalid['countryorigin'])) ? RegistrationHelperHtml::error($this->xregistration->_invalid['countryorigin']) : '';
			$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';

			if (!$this->registration['countryorigin']) {
				$userCountry = Hubzero_Geo::ipcountry(Hubzero_Environment::ipAddress());
				$this->registration['countryorigin'] = $userCountry;
			}

			$html .= "\t\t".'<fieldset'.$fieldclass.'>'."\n";
			$html .= "\t\t\t".'<legend>Are you a Legal Citizen or Permanent Resident of the <abbr title="United States">US</abbr>? ';
			$html .= $required;
			$html .= '</legend>'."\n";
			$html .= ($message) ? "\t\t\t" . $message . "\n" : '';

			$html .= "\t\t\t".'<label><input type="radio" class="option" name="corigin_us" id="corigin_usyes" value="yes"';
			if (strcasecmp($this->registration['countryorigin'],'US') == 0) {
				$html .= ' checked="checked"';
			}
			$html .= ' /> '.JText::_('COM_REGISTER_FORM_YES').'</label>'."\n";

			$html .= "\t\t\t\t".'<label><input type="radio" class="option" name="corigin_us" id="corigin_usno" value="no"';
			if (!empty($this->registration['countryorigin']) && (strcasecmp($this->registration['countryorigin'], 'US') != 0)) {
				$html .= ' checked="checked"';
			}
			$html .= ' /> '.JText::_('COM_REGISTER_FORM_NO').'</label>'."\n";

			$html .= "\t\t\t".'<label>'."\n";
			$html .= "\t\t\t\t".JText::_('Citizen or Permanent Resident of').':'."\n";
			$html .= "\t\t\t\t".'<select name="corigin" id="corigin">'."\n";

			if (!$this->registration['countryorigin'] || $this->registration['countryorigin'] == 'US') {
				$html .= "\t\t\t\t\t".'<option value="">'.JText::_('COM_REGISTER_FORM_SELECT_FROM_LIST').'</option>'."\n";
			}

			$countries = Hubzero_Geo::getcountries();
			if ($countries) {
				foreach ($countries as $country)
				{
					//if (strtoupper($country['code']) != 'US') {
						$html .= "\t\t\t\t\t".'<option value="' . $country['code'] . '"';
						if ($this->registration['countryorigin'] == $country['code']) {
							$html .= ' selected="selected"';
						}
						$html .= '>' . htmlentities($country['name'], ENT_COMPAT, 'UTF-8') . '</option>'."\n";
					//}
				}
			}
			$html .= "\t\t\t\t".'</select>'."\n";
			$html .= "\t\t\t".'</label>'."\n";
			$html .= "\t\t".'</fieldset>'."\n";
		}

		// Residency
		if ($this->registrationResidency != REG_HIDE) {
			$required = ($this->registrationResidency == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_REGISTER_FORM_REQUIRED').'</span>' : '';
			$message = (!empty($this->xregistration->_invalid['countryresident'])) ? RegistrationHelperHtml::error($this->xregistration->_invalid['countryresident']) : '';
			$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';

			if (!$this->registration['countryresident']) {
				if (!isset($userCountry) || !$userCountry) {
					$userCountry = Hubzero_Geo::ipcountry(Hubzero_Environment::ipAddress());
				}
				$this->registration['countryresident'] = $userCountry;
			}

			$html .= "\t\t".'<fieldset'.$fieldclass.'>'."\n";
			$html .= "\t\t\t".'<legend>'.JText::_('Do you Currently Live in the <abbr title="United States">US</abbr>?').' ';
			$html .= $required;
			$html .= '</legend>'."\n";
			$html .= ($message) ? "\t\t\t" . $message . "\n" : '';
			$html .= "\t\t\t".'<label><input type="radio" class="option" name="cresident_us" id="cresident_usyes" value="yes"';
			if (strcasecmp($this->registration['countryresident'], 'US') == 0) {
				$html .= ' checked="checked"';
			}
			$html .= ' /> '.JText::_('COM_REGISTER_FORM_YES').'</label>'."\n";
			$html .= "\t\t\t".'<label><input type="radio" class="option" name="cresident_us" id="cresident_usno" value="no"';
			if (!empty($this->registration['countryresident']) && strcasecmp($this->registration['countryresident'], 'US') != 0) {
				$html .= ' checked="checked"';
			}
			$html .= ' /> '.JText::_('COM_REGISTER_FORM_NO').'</label>'."\n";
			$html .= "\t\t\t".'<label>'."\n";
			$html .= "\t\t\t\t".JText::_('Currently Living in').':'."\n";
			$html .= "\t\t\t\t".'<select name="cresident" id="cresident">'."\n";

			if (!$this->registration['countryresident'] || strcasecmp($this->registration['countryresident'], 'US') == 0) {
				$html .= "\t\t\t\t\t".'<option value="">'.JText::_('COM_REGISTER_FORM_SELECT_FROM_LIST').'</option>'."\n";
			}

			if (!isset($countries) || !$countries) {
				$countries = Hubzero_Geo::getcountries();
			}
			if ($countries) {
				foreach ($countries as $country) {
					//if (strcasecmp($country['code'], 'US') != 0) {
						$html .= "\t\t\t\t\t".'<option value="' . $country['code'] . '"';
						if (strcasecmp($this->registration['countryresident'], $country['code']) == 0) {
							$html .= ' selected="selected"';
						}
						$html .= '>' . htmlentities($country['name'], ENT_COMPAT, 'UTF-8') . '</option>'."\n";
					//}
				}
			}
			$html .= "\t\t\t\t".'</select>'."\n";
			$html .= "\t\t\t".'</label>'."\n";
			$html .= "\t\t".'</fieldset>'."\n";
		}

		// Sex
		if ($this->registrationSex != REG_HIDE) {
			$required = ($this->registrationSex == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_REGISTER_FORM_REQUIRED').'</span>' : '';
			$message = (!empty($this->xregistration->_invalid['sex'])) ? RegistrationHelperHtml::error($this->xregistration->_invalid['sex']) : '';
			$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';

			$html .= "\t\t".'<fieldset'.$fieldclass.'>'."\n";
			$html .= "\t\t\t".'<legend>'.JText::_('COM_REGISTER_FORM_GENDER').': '.$required.'</legend>'."\n";
			$html .= ($message) ? "\t\t\t" . $message . "\n" : '';
			$html .= "\t\t\t".'<input type="hidden" name="sex" value="unspecified" />'."\n";
			$html .= "\t\t\t".'<label>'.RegistrationHelperHtml::radio('sex','male','option',$this->registration['sex']).' '.JText::_('COM_REGISTER_FORM_MALE').'</label>'."\n";
			$html .= "\t\t\t".'<label>'.RegistrationHelperHtml::radio('sex','female','option',$this->registration['sex']).' '.JText::_('COM_REGISTER_FORM_FEMALE').'</label>'."\n";
			$html .= "\t\t\t".'<label>'.RegistrationHelperHtml::radio('sex','refused','option',$this->registration['sex']).' '.JText::_('COM_REGISTER_FORM_REFUSED').'</label>'."\n";
			$html .= "\t\t".'</fieldset>'."\n";
		}

		// Disability
		if ($this->registrationDisability != REG_HIDE) {
			$required = ($this->registrationDisability == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_REGISTER_FORM_REQUIRED').'</span>' : '';
			$message = (!empty($this->xregistration->_invalid['disability'])) ? RegistrationHelperHtml::error($this->xregistration->_invalid['disability']) : '';
			$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';

			$disabilityyes = false;
			$disabilityother = '';

			if (!is_array($this->registration['disability'])) {
				$this->registration['disability'] = array();
			}

			foreach ($this->registration['disability'] as $disabilityitem) {
				if ($disabilityitem != 'no' && $disabilityitem != 'refused') {
					if (!$disabilityyes) {
						$disabilityyes = true;
					}
					if ($disabilityitem != 'blind'
					 && $disabilityitem != 'deaf'
					 && $disabilityitem != 'physical'
					 && $disabilityitem != 'learning'
					 && $disabilityitem != 'vocal'
					 && $disabilityitem != 'yes'
					) {
						$disabilityother = $disabilityitem;
					}
				}
			}

			$html .= "\t\t".'<fieldset'.$fieldclass.'>'."\n";
			$html .= "\t\t\t".'<legend>'.JText::_('Disability').': '.$required.'</legend>'."\n";
			$html .= ($message) ? "\t\t\t" . $message . "\n" : '';
			$html .= "\t\t\t".'<label><input type="radio" class="option" name="disability" id="disabilityyes" value="yes"';
			if ($disabilityyes) {
				$html .= ' checked="checked"';
			}
			$html .= ' /> '.JText::_('Yes').'</label>'."\n";
			$html .= "\t\t\t".'<fieldset>'."\n";
			$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="disabilityblind" id="disabilityblind" ';
			if (in_array('blind', $this->registration['disability'])) {
				$html .= 'checked="checked" ';
			}
			$html .= '/> '.JText::_('Blind / Visually Impaired').'</label>'."\n";
			$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="disabilitydeaf" id="disabilitydeaf" ';
			if (in_array('deaf', $this->registration['disability'])) {
				$html .= 'checked="checked" ';
			}
			$html .= '/> '.JText::_('Deaf / Hard of Hearing').'</label>'."\n";
			$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="disabilityphysical" id="disabilityphysical" ';
			if (in_array('physical', $this->registration['disability'])) {
				$html .= 'checked="checked" ';
			}
			$html .= '/> '.JText::_('Physical / Orthopedic Disability').'</label>'."\n";
			$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="disabilitylearning" id="disabilitylearning" ';
			if (in_array('learning', $this->registration['disability'])) {
				$html .= 'checked="checked" ';
			}
			$html .= '/> '.JText::_('Learning / Cognitive Disability').'</label>'."\n";
			$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="disabilityvocal" id="disabilityvocal" ';
			if (in_array('vocal', $this->registration['disability'])) {
				$html .= 'checked="checked" ';
			}
			$html .= '/> '.JText::_('Vocal / Speech Disability').'</label>'."\n";
			$html .= "\t\t\t\t".'<label>'.JText::_('Other (please specify)').':'."\n";
			$html .= "\t\t\t\t".'<input name="disabilityother" id="disabilityother" type="text" value="'. htmlentities($disabilityother,ENT_COMPAT,'UTF-8') .'" /></label>'."\n";
			$html .= "\t\t\t".'</fieldset>'."\n";
			$html .= "\t\t\t".'<label><input type="radio" class="option" name="disability" id="disabilityno" value="no"';
			if (in_array('no', $this->registration['disability'])) {
				$html .= ' checked="checked" ';
			}
			$html .= '/> '.JText::_('No (none)').'</label>'."\n";
			$html .= "\t\t\t".'<label><input type="radio" class="option" name="disability" id="disabilityrefused" value="refused"';
			if (in_array('refused', $this->registration['disability'])) {
				$html .= ' checked="checked" ';
			}
			$html .= '/> '.JText::_('Do not wish to reveal').'</label>'."\n";
			$html .= "\t\t".'</fieldset>'."\n";
		}

		// Hispanic
		if ($this->registrationHispanic != REG_HIDE) {
			$required = ($this->registrationHispanic == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_REGISTER_FORM_REQUIRED').'</span>' : '';
			$message = (!empty($this->xregistration->_invalid['hispanic'])) ? RegistrationHelperHtml::error($this->xregistration->_invalid['hispanic']) : '';
			$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';

			$hispanicyes = false;
			$hispanicother = '';

			if (!is_array($this->registration['hispanic'])) {
				$this->registration['hispanic'] = array();
			}

			foreach ($this->registration['hispanic'] as $hispanicitem) {
				if ($hispanicitem != 'no' && $hispanicitem != 'refused') {
					if (!$hispanicyes) {
						$hispanicyes = true;
					}
					if ($hispanicitem != 'cuban'
					 && $hispanicitem != 'mexican'
					 && $hispanicitem != 'puertorican'
					) {
						$hispanicother = $hispanicitem;
					}
				}
			}

			$html .= "\t\t".'<fieldset'.$fieldclass.'>'."\n";
			$html .= "\t\t\t".'<legend>Hispanic or Latino (<a class="popup 700x500" href="/register/raceethnic">more information</a>) ';

			$html .= $required;
			$html .= '</legend>'."\n";
			$html .= $message;

			$html .= "\t\t\t\t".'<label><input type="radio" class="option" name="hispanic" id="hispanicyes" value="yes" ';
			if ($hispanicyes) {
				$html .= 'checked="checked"';
			}
			$html .= ' /> '.JText::_('Yes (Hispanic Origin or Descent)').'</label>'."\n";

			$html .= "\t\t\t".'<fieldset>'."\n";

			$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="hispaniccuban" id="hispaniccuban" ';
			if (in_array('cuban', $this->registration['hispanic'])) {
				$html .= 'checked="checked" ';
			}
			$html .= '/> '.JText::_('Cuban').'</label>'."\n";

			$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="hispanicmexican" id="hispanicmexican" ';
			if (in_array('mexican', $this->registration['hispanic'])) {
				$html .= 'checked="checked" ';
			}
			$html .= '/> '.JText::_('Mexican American or Chicano').'</label>'."\n";

			$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="hispanicpuertorican" id="hispanicpuertorican" ';
			if (in_array('puertorican', $this->registration['hispanic'])) {
				$html .= 'checked="checked" ';
			}
			$html .= '/> '.JText::_('Puerto Rican').'</label>'."\n";

			$html .= "\t\t\t\t".'<label>'."\n";
			$html .= "\t\t\t\t\t".JText::_('Other Hispanic or Latino').':'."\n";
			$html .= "\t\t\t\t\t".'<input name="hispanicother" id="hispanicother" type="text" value="'. htmlentities($hispanicother,ENT_COMPAT,'UTF-8') .'" />'."\n";
			$html .= "\t\t\t\t".'</label>'."\n";
			$html .= "\t\t\t".'</fieldset>'."\n";

			$html .= "\t\t\t".'<label><input type="radio" class="option" name="hispanic" id="hispanicno" value="no"';
			if (in_array('no', $this->registration['hispanic'])) {
				$html .= ' checked="checked" ';
			}
			$html .= '/> '.JText::_('No (not Hispanic or Latino)').'</label>'."\n";

			$html .= "\t\t\t".'<label><input type="radio" class="option" name="hispanic" id="hispanicrefused" value="refused"';
			if (in_array('refused', $this->registration['hispanic'])) {
				$html .= ' checked="checked" ';
			}
			$html .= '/> '.JText::_('Do not wish to reveal').'</label>'."\n";

			$html .= "\t\t".'</fieldset>'."\n";
		}

		// Racial background
		if ($this->registrationRace != REG_HIDE) {
			$required = ($this->registrationRace == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_REGISTER_FORM_REQUIRED').'</span>' : '';
			$message = (!empty($this->xregistration->_invalid['race'])) ? RegistrationHelperHtml::error($this->xregistration->_invalid['race']) : '';
			$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';

			if (!is_array($this->registration['race'])) {
				$this->registration['race'] = array(trim($this->registration['race']));
			}

			$html .= "\t\t".'<fieldset'.$fieldclass.'>'."\n";
			$html .= "\t\t\t".'<legend>U.S. Citizens and Permanent Residents Only (<a class="popup 675x678" href="/register/raceethnic">more information</a>) '.$required.'</legend>'."\n";
			$html .= "\t\t\t".'<p class="hint">'.JText::_('Select one or more that apply.').'</p>'."\n";

			$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="racenativeamerican" id="racenativeamerican" value="nativeamerican" ';
			if (in_array('nativeamerican', $this->registration['race'])) {
				$html .= 'checked="checked" ';
			}
			$html .= '/> '.JText::_('American Indian or Alaska Native').'</label>'."\n";

			$html .= "\t\t\t".'<label class="indent">'."\n";
			$html .= "\t\t\t\t".JText::_('Tribal Affiliation(s)').': '."\n";
			$html .= "\t\t\t\t".'<input name="racenativetribe" id="racenativetribe" type="text" value="'. htmlentities($this->registration['nativetribe'],ENT_COMPAT,'UTF-8') .'" />'."\n";
			$html .= "\t\t\t".'</label>'."\n";

			$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="raceasian" id="raceasian" ';
			if (in_array('asian', $this->registration['race'])) {
				$html .= 'checked="checked" ';
			}
			$html .= '/> '.JText::_('Asian').'</label>'."\n";

			$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="raceblack" id="raceblack" ';
			if (in_array('black', $this->registration['race'])) {
				$html .= 'checked="checked" ';
			}
			$html .= '/> '.JText::_('Black or African American').'</label>'."\n";

			$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="racehawaiian" id="racehawaiian" ';
			if (in_array('hawaiian', $this->registration['race'])) {
				$html .= 'checked="checked" ';
			}
			$html .= '/> '.JText::_('Native Hawaiian or Other Pacific Islander').'</label>'."\n";

			$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="racewhite" id="racewhite" ';
			if (in_array('white', $this->registration['race'])) {
				$html .= 'checked="checked" ';
			}
			$html .= '/> '.JText::_('White').'</label>'."\n";

			$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="racerefused" id="racerefused" ';
			if (in_array('refused', $this->registration['race'])) {
				$html .= 'checked="checked" ';
			}
			$html .= '/> '.JText::_('Do not wish to reveal').'</label>'."\n";

			$html .= ($message) ? "\t\t\t" . $message . "\n" : '';
			$html .= "\t\t".'</fieldset>'."\n";
		}
		$html .= "\t".'</fieldset>'."\n";
		$html .= "\t".'<div class="clear"></div>'."\n";
	}

	// Newsletter Opt-In
	if ($this->registrationOptIn != REG_HIDE) {
		$required = ($this->registrationOptIn == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_REGISTER_FORM_REQUIRED').'</span>' : '';
		$message = (!empty($this->xregistration->_invalid['mailPreferenceOption'])) ? RegistrationHelperHtml::error($this->xregistration->_invalid['mailPreferenceOption']) : '';
		$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';
		
		//define mail preference options
		$options = array(
			'-1' => '- Select email option &mdash;',
			'1'  => 'Yes, send me emails',
			'0'  => 'No, don\'t send me emails'
		);
		
		//if we dont have a mail pref option set to unanswered
		if(!isset($this->registration['mailPreferenceOption']) || $this->registration['mailPreferenceOption'] == '')
		{
			$this->registration['mailPreferenceOption'] = '-1';
		}
		
		$html .= "\t".'<fieldset>'."\n";
		$html .= "\t\t".'<legend>'.JText::_('Receive Email Updates').'</legend>'."\n";
		$html .= '<label for="mailPreferenceOption">';
		$html .= 'Would you like to receive email updates (newsletters, etc.)? ' . $required;
		$html .= '<select name="mailPreferenceOption">';
		foreach ($options as $key => $value)
		{
			$sel = ($key == $this->registration['mailPreferenceOption']) ? 'selected="selected"' : '';
			$html .= '<option '.$sel.' value="'. $key .'">' . $value . '</option>';
		}
		$html .= '</select>';
		$html .= '</label>';
		$html .= $message;
		$html .= "\t".'</fieldset><div class="clear"></div>'."\n";
	}

	// CAPTCHA
	if ($this->registrationCAPTCHA != REG_HIDE) {
		$required = ($this->registrationCAPTCHA == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_REGISTER_FORM_REQUIRED').'</span>' : '';
		$message = (isset($this->xregistration->_invalid['captcha']) && !empty($this->xregistration->_invalid['captcha'])) ? RegistrationHelperHtml::error($this->xregistration->_invalid['captcha']) : '';

		JPluginHelper::importPlugin( 'hubzero' );
		$dispatcher =& JDispatcher::getInstance();
		$captchas = $dispatcher->trigger( 'onGetCaptcha' );

		if (count($captchas) > 0) {
			$html .= "\t".'<fieldset>'."\n";
			$html .= "\t\t".'<legend>'.JText::_('Human Check').'</legend>'."\n";
			$html .= ($message) ? $message : '';
		}
		$html .= "\t\t".'<label id="botcheck-label" for="botcheck">'."\n";
		$html .= "\t\t\t".JText::_('Please leave this field blank.').' '.$required."\n";
		$html .= "\t\t\t".'<input type="text" name="botcheck" id="botcheck" value="" />'."\n";
		$html .= "\t\t".'</label>'."\n";
		if (count($captchas) > 0) {
			foreach ($captchas as $captcha) {
				$html .= $captcha;
			}
			$html .= "\t".'</fieldset>'."\n";
		}
	}

	// Terms of use
	if ($this->registrationTOU != REG_HIDE) {
		$html .= "\t".'<fieldset>'."\n";
		$html .= "\t\t".'<legend>'.JText::_('COM_REGISTER_TERMS_AND_CONDITIONS').'</legend>'."\n";

		if ($this->registrationTOU != REG_HIDE) {
			$required = ($this->registrationTOU == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_REGISTER_FORM_REQUIRED').'</span>' : '';
			$message = (!empty($this->xregistration->_invalid['usageAgreement'])) ? RegistrationHelperHtml::error($this->xregistration->_invalid['usageAgreement']) : '';
			$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

			$html .= "\t\t".'<label'.$fieldclass.'>'."\n";
			$html .= "\t\t\t".'<input type="checkbox" class="option" id="usageAgreement" value="1" name="usageAgreement" ';
			if ($this->registration['usageAgreement']) {
				$html .= 'checked="checked" ';
			}
			$html .= '/> ';
			$html .= $required;
			$html .= ' '.JText::_('Yes, I have read and agree to the <a class="popup 700x500" href="/legal/terms">Terms of Use</a>.')."\n";
			$html .= "\t\t".'</label>'."\n";

			$html .= $message;
			$html .= "\t".'</fieldset>'."\n";
			$html .= "\t".'<div class="clear"></div>'."\n";
		}
	} else if ($this->registration['usageAgreement']) {
		$html .= "\t".'<input name="usageAgreement" type="hidden" id="usageAgreement" value="checked" />'."\n";
		$html .= "\t".'<div class="clear"></div>'."\n";
	}

	$html .= "\t".'<p class="submit"><input type="submit" name="'.$this->task.'" value="'.JText::_('COM_REGISTER_BUTTON_'.strtoupper($this->task)).'" /></p>'."\n";
	$html .= "\t".'<input type="hidden" name="option" value="'.$this->option.'" />'."\n";
	$html .= "\t".'<input type="hidden" name="task" value="'.$this->task.'" />'."\n";
	$html .= "\t".'<input type="hidden" name="act" value="submit" />'."\n";
	$html .= "\t".JHTML::_( 'form.token' );

	// urlencode is XSS protection added to this field, see ticket 1411
	$html .= "\t".'<input type="hidden" name="return" value="' . urlencode($form_redirect) . '" />'."\n";

	$html .= '</form>'."\n";
	$html .= '</div><!-- / .main section -->'."\n";

	echo $html;

