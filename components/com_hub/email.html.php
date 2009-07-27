<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class EmailHtml
{
	public function save_change($login, $newemail) 
	{
		$xuser =& XUser::getInstance($login);
		if ($xuser) {
			$dtmodify = date("Y-m-d H:i:s");
			$xuser->set('email',$newemail);
			$xuser->set('mod_date',$dtmodify);
			if ($xuser->update()) {
				$juser =& JUser::getInstance($login);
				$juser->set('email', $newemail);
				$juser->save();
				return false;
			}
		}
		return EmailHtml::error('Error updating user account');
	}
	
	//-----------

	public function change($option, $email, $email_confirmed, $return) 
	{
		$html = '';

		$newemail = JRequest::getVar('email', '', 'post');
		$update   = JRequest::getVar('update', '', 'post');

		if ($update) {
			if (!empty($newemail) && XRegistrationHelper::validemail($newemail) /*&& ($newemail != $email)*/ ) {
				return false;
			}
			
			$html .= HubHtml::error('Invalid email. Please enter a valid e-mail address.');
		}

		if (!empty($newemail)) {
			$email = $newemail;
		}

		$html .= '<form action="'. JRoute::_('index.php?option='.$option.a.'task=registration'.a.'view=change') .'" method="post" id="hubForm">'.n;
		if (($email_confirmed != 1) && ($email_confirmed != 3)) {
			$html .= '<div class="explaination">'.n;
			$html .= EmailHtml::faq($option, false, $return, $email);
			$html .= '</div>'.n;
		}
		$html .= t.'<fieldset>'.n;
		$html .= t.t.HubHtml::hed(3,'Correct Email Address').n;
		$html .= t.t.'<label';
		if (!$email || !XRegistrationHelper::validemail($email)) {
			$html .= ' class="fieldWithErrors"';
		}
		$html .= '>Valid E-mail:'.n;
		$html .= t.t.'<input name="email" id="email" type="text" size="51" value="'.htmlentities($email,ENT_COMPAT,'UTF-8').'" /></label>'.n;
		if (!$email) {
			$html .= HubHtml::error('Please provide a valid e-mail address.');
		}
		if ($email && !XRegistrationHelper::validemail($email)) {
			$html .= HubHtml::error('Invalid e-mail address. Example: someone@somewhere.com');
		}
		$html .= t.'</fieldset><div class="clear"></div>'.n;
		$html .= t.'<input type="hidden" name="option" value="'.$option.'" />'.n;
		$html .= t.'<input type="hidden" name="view" value="registration" />'.n;
		$html .= t.'<input type="hidden" name="task" value="change" />'.n;
		$html .= t.'<input type="hidden" name="act" value="show" />'.n;
		$html .= t.'<p class="submit"><input type="submit" name="update" value="Update Email" /></p>'.n;
		$html .= '</form>'.n;

		return $html;
	}

	//-----------

	public function confirm($option, $login, $email, $email_confirmed, $code) 
	{
		$xhub =& XFactory::getHub();
		
		$html  = HubHtml::div( HubHtml::hed(2, JText::_('Confirm Email Address')), 'full', 'content-header' );
		$html .= '<div class="main section">'.n;
		if (($email_confirmed == 1) || ($email_confirmed == 3)) {
			$html .= '<p class="passed">Your email address "'. htmlentities($email,ENT_COMPAT,'UTF-8') .'" has already been confirmed. You should be able to use '. $xhub->getCfg('hubShortName') .' now. Thank you.</p>'.n;
		} elseif ($email_confirmed < 0 && $email_confirmed == -$code) {
			ximport('xprofile');
			$xprofile = new XProfile();
			$xprofile->load($login);
                        $myreturn = $xprofile->getParam('return');
                        if (!empty($myreturn))
                        	$xprofile->setParam('return','');
			$xprofile->set('emailConfirmed', 1);
			if ($xprofile->update()) {	
				$html .= '<p class="passed">Your email address "'. htmlentities($email,ENT_COMPAT,'UTF-8') .'" has been confirmed. Your '. $xhub->getCfg('hubShortName') .' account is now activated. Thank you.</p>'.n;
			} else {
				$html  = HubHtml::error( JText::_('An error occurred confirming your email address.') );
				$html .= EmailHtml::faq($option, false,false,$email);
			}

                        if (!empty($myreturn))
				$xhub->redirect($myreturn);

		} else {
			$html .= '<div class="aside">'.n;
			$html .= t.EmailHtml::faq($option, false, false, $email);
			$html .= '</div><!-- / .aside -->'.n;
			$html .= '<div class="subject">'.n;
			$html .= t.'<div class="error">'.n;
			$html .= t.t.HubHtml::hed(4,JText::_('Invalid Confirmation')).n;
			$html .= t.t.'<p>The email confirmation link you followed is no longer valid. Your email address "'. htmlentities($email,ENT_COMPAT,'UTF-8') .'" has not been confirmed.</p>'.n;
			$html .= t.t.'<p>Please be sure to click the link from the latest confirmation email received.  Earlier confirmation emails will be invalid. If you cannot locate a newer confirmation email, you may <a href="'.JRoute::_('index.php?option='.$option.a.'task=registration'.a.'view=resend').'">resend a new confirmation email</a>.</p>'.n;
			$html .= t.'</div>'.n;
			$html .= '</div><!-- / .subject -->'.n;
		}
		$html .= '</div><!-- / .main section -->'.n;

		return $html;
	}

	//-----------

	public function send_code($login, $email, $return, $option) 
	{
		$html = '';
		
		$xhub =& XFactory::getHub();
		$hubName = $xhub->getCfg('hubShortName');
		$hubUrl =  $xhub->getCfg('hubLongURL');
		
		ximport('xregistrationhelper');
		$confirm = XRegistrationHelper::genemailconfirm();

		ximport('xprofile');
		$xprofile = new XProfile();
		$xprofile->load($login);
		$xprofile->set('emailConfirmed', $confirm);
		$xprofile->update();

		$subject  = $hubName .' '.JText::_('Account Email Confirmation');
		$message  = "This email is to confirm the email address for the $hubName account: $login.\r\n\r\n";
		$message .= "Click the following link to confirm your email address and activate your " . $hubName . " account.\r\n\r\n";
		$message .= $hubUrl . JRoute::_('index.php?option='.$option.a.'task=registration'.a.'view=confirm'.a.'confirm='. -$confirm) . "\r\n";
		
		ximport('xhubhelper');
		if (XHubHelper::send_email($email, $subject, $message)) {
			$html .= '<p class="passed">A confirmation email has been sent to "'. htmlentities($email,ENT_COMPAT,'UTF-8') .'".  You must click the link in that email to activate your account and resume using '. $hubName .'.</p>'."\n";
			$html .= EmailHtml::faq($option, true, $return, $email);
		} else {
			$html .= HubHtml::error('An error occurred emailing "'. htmlentities($_POST['email'],ENT_COMPAT,'UTF-8') .'" your confirmation.');
		}
		
		return $html;
	}
	
	//-----------
	
	public function unconfirmed($option, $return, $email, $xhub) 
	{
		$html  = HubHtml::div( HubHtml::hed(2, JText::_('Email Address Unconfirmed')), 'full', 'content-header' );
		$html .= '<div class="main section">'.n;
		$html .= t.'<div class="twocolumn left">'.n;
		$html .= t.t.HubHtml::error('Your email address "'. htmlentities($email,ENT_COMPAT,'UTF-8') .'" has not been confirmed. Please check your email for a confirmation notice. You must click the link in that email to activate your account and resume using '. $xhub->getCfg('hubShortName') .'.').n;
		$html .= t.'</div><!-- / .twocolumn left -->'.n;
		$html .= t.'<div class="twocolumn right">'.n;
		$html .= t.t.EmailHtml::faq($option, true, $return, $email);
		$html .= t.'</div><!-- / .twocolumn right -->'.n;
		$html .= '</div><!-- / .main.section -->'.n;
		
		return $html;
	}
	
	//-----------

	public function faq($option, $show_correction_faq, $return, $email) 
	{
		// this doesn't get show on the email change page since we are already there
		$html  = '';
		if ($show_correction_faq) {
			$html .= t.HubHtml::hed(4,'Wrong email address?').n;
			$html .= t.'<p>You can correct your email address by <a href="'.JRoute::_('index.php?option='.$option.a.'task=registration'.a.'view=change'.a.'return='.$return).'">clicking here</a>.</p>'.n;
		}
		$html .= t.HubHtml::hed(4,'Never received or cannot find the confirmation email?').n;
		$html .= t.'<p>You can have a new confirmation email sent to "'. htmlentities($email,ENT_COMPAT,'UTF-8') .'" by <a href="'.JRoute::_('index.php?option='.$option.a.'task=registration'.a.'view=resend'.a.'return='.$return).'">clicking here</a>.</p>'.n;

		return $html;
	}
}
?>
