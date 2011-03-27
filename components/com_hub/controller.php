<?php
/**
 * @package     hubzero-cms
 * @author      Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class HubController extends JObject
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;
	
	//-----------
	
	public function __construct( $config=array() )
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';
		
		//Set the controller name
		if (empty( $this->_name ))
		{
			if (isset($config['name']))  {
				$this->_name = $config['name'];
			}
			else
			{
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}
		
		$this->_option = 'com_'.$this->_name;
	}
	
	//-----------
	
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}

	//-----------
	
	private function _cookie_check()
	{
		$xhub =& Hubzero_Factory::getHub();
		$jsession =& JFactory::getSession();
		$jcookie = $jsession->getName();

		if (!isset($_COOKIE[$jcookie])) {
			if (JRequest::getVar('cookie', '', 'get') != 'no') {
				$juri = JURI::getInstance();
				$juri->setVar('cookie','no');
				return $xhub->redirect($juri->toString());
			}       
			        
			echo HubHtml::error(
				'It seems cookies are disabled on your browser! Cookies are required for login.<br /><br />'.
				'<a href="/support/cookies">Click here to learn how to enable cookies.</a>'
			);

			return false;
		} else if (JRequest::getVar('cookie', '', 'get') == 'no') {
			$juri = JURI::getInstance();
			$juri->delVar('cookie');

			return $xhub->redirect($juri->toString());
		}

		return true;
	}
	
	//-----------

	protected function invalidRequest()
	{
		return JError::raiseError( 404, "Invalid Request" );
	}
	
	//-----------

	public function execute()
	{
		$this->_view = JRequest::getVar('view','','method');
		$this->_task = JRequest::getVar('task','','method');
		$this->_act  = JRequest::getVar('act','','method');
		
		$xhub =& Hubzero_Factory::getHub();

		if (!isset( $_SERVER['HTTPS'] ) || $_SERVER['HTTPS'] == 'off') {
			$xhub->redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); 
			die('insecure connection and redirection failed'); 
		}

		switch ($this->_view)
		{
			case 'login':
				$app =& JFactory::getApplication();
				$pathway =& $app->getPathway();
				if (count($pathway->getPathWay()) <= 0) {
					$pathway->addItem(JText::_('Login'),'/login');
				}
				switch ($this->_task)
				{
					case 'login':   
						$this->login($this->_act);
						break;
					case 'realm':
						$this->realm($this->_act);
						break;
					default:
						$this->invalidRequest();
						break;
				}
				break;

			case 'logout':
				switch ($this->_task)
				{
					case 'logout':
						$this->logout($this->_act);
						break;
					default:
						$this->invalidRequest();
						break;
				}
				break;
			
			// Account recovery
			case 'lostpassword':
				$this->lostpassword();
				break;
			case 'lostusername':
				$this->lostusername();
				break;

			default:
				$this->invalidRequest();
				break;
		}
	}

	//----------------------------------------------------------
	// Tasks
	//----------------------------------------------------------

	public function logout()
	{       
		$app  =& JFactory::getApplication();
		$xhub =& Hubzero_Factory::getHub();
		
		// Preform the logout action
		$error = $app->logout();
		
		if (!JError::isError($error)) {
			if ($return = JRequest::getVar('return', '', 'method', 'base64')) {
				$return = base64_decode($return);
			}

			if (empty($return)) {
				$return = '/';
			}

			// Redirect if the return url is not registration or login
			return $xhub->redirect( $return );
		}

		JError::raiseError( 500, $error->get('message') );
	}
	
	//-----------

	public function login($action = 'show')
	{
		$xhub =& Hubzero_Factory::getHub();
		$juser = &JFactory::getUser();

		$return = base64_decode( JRequest::getVar('return', '',  'method', 'base64') );

		if (empty($return)) {
		    	$hconfig = &JComponentHelper::getParams('com_hub');
			$r = $hconfig->get('LoginReturn');
			$return = ($r) ? $r : '/myhub';
		}

		if (!$juser->get('guest'))
			return $xhub->redirect($return);

		if (!$this->_cookie_check())
			return;

		if (empty($action))
			$action = 'show';

		if ($action != 'show' && $action != 'submit')
			return $this->invalidRequest();

		if ($action == 'submit')
		{
			$credentials = array();
			$credentials['username'] = JRequest::getVar('username', '', 'method', 'username');
			$credentials['password'] = JRequest::getString('passwd', '', 'post', JREQUEST_ALLOWRAW);
	
			$options = array();
			$options['remember'] = JRequest::getBool('remember', false);
			$options['domain'] = JRequest::getString('realm','','post');
	       	$options['return'] = $return;

			$login_attempts = JRequest::getInt('la',0,'post');

			if (!empty($credentials['username']) && !empty($credentials['password']))
			{
				$app   = &JFactory::getApplication();
				$error = $app->login($credentials, $options);

	        	if (!JError::isError($error))
				{
					return $xhub->redirect( $return );
				}

				$error_message = $error->get('message');
			}
			else if ($login_attempts > 0)
				$error_message = JText::_('E_LOGIN_AUTHENTICATE');
			else
				$error_message = '';
		
			$usrnm = $credentials['username'];
		}
		else
		{
			$usernm = '';
			$login_attempts = 0;
			$error_message = '';
		}

		$plugins = JPluginHelper::getPlugin('xauthentication');

		$realms = array();

		foreach ($plugins as $plugin)
		{
			$params = new JParameter($plugin->params);

			$realm = $params->get('domain');

			if (empty($realm))
				$realm = $plugin->name;

	       		if (!in_array($realm, $realms))
		       		$realms[$plugin->name] = $realm;
		}

		$login_attempts++;
		
		$realm = JRequest::getVar('realm', '', 'method');

		if (empty($realm) && count($realms) == 1)
			$realm = current( array_keys($realms) );

		if (!array_key_exists($realm, $realms))
			return JError::raiseError( 404, "Invalid Authentication Realm Requested" );

		$realmName = $realms[$realm];

		// @TODO this default should be provided by plugin and probably should be different than the realm name
  		// it should be a variable specifically for the login prompt.
		if ($realmName == 'hzldap')
		{
			$app =& JFactory::getApplication();
			$realmName = $app->getCfg('sitename') . ' Account';
		}
		
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		$registration_enabled = $usersConfig->get( 'allowUserRegistration' );
		
		unset($credentials,$options,$realms,$params,$plugins,$plugin,$action,$usersConfig,$app,$error);
		
		$hubShortName = $xhub->getCfg('hubShortName');

		echo HubHtml::div( HubHtml::hed(2, JText::_('Login')), 'full', 'content-header' );
		echo '<div class="main section">'.n;
		include $xhub->getComponentViewFilename($this->_option, 'login');
		echo '</div><!-- / .main section -->'.n;
	}
	
	//-----------

	protected function realm($action = null)
	{
		$xhub =& Hubzero_Factory::getHub();
		
		if (!$this->_cookie_check()) {
			return;
		}

		if (empty($action)) {
			$action = 'show';
		}
		
		$plugins = JPluginHelper::getPlugin('xauthentication');

		$realms = array();

		foreach ($plugins as $plugin)
		{
			$params = new JParameter($plugin->params);

			$realm = $params->get('domain');

			if (empty($realm))
				$realm = $plugin->name;

	       		if (!in_array($realm, $realms))
		       		$realms[$plugin->name] = $realm;
		}

		if (count($realms) == 1)
			return $this->login('show');

		if (count($realms) == 0)
			return JError::raiseError( '500', 'xHUB Configuration Error: No XAuthentication Plugins Enabled.'); 

		if ($action == 'submit') {
			if (JRequest::getVar('create', '', 'method')) 
				return $this->create('show');

			if (JRequest::getVar('realm', '', 'method'))
				return $this->login('show');
		}

		unset($action,$plugins,$plugin,$params,$realm);

		$hubShortName = $xhub->getCfg('hubShortName');

		include $xhub->getComponentViewFilename($this->_option, 'realm');
	}
	
	//-----------
	
	protected function lostusername() 
	{
		// Load some needed libraries
		ximport('Hubzero_Registration_Helper');

		$this->_view = $this->_task;
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_('Lost Username') );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		$pathway->addItem(JText::_('Lost Username'),'index.php?option='.$this->_option.'&task='.$this->_task);
		
		// Incoming
		$email  = JRequest::getVar('email', NULL, 'post');
		$resend = JRequest::getVar('resend', NULL, 'post');
		
		// Instantiate a new view
		$view = new JView( array('name'=>'lostusername') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->email = $email;
		$view->passed = false;
		
		// Was the form submitted?
		if ($resend) {
			if (empty($email)) {
				$this->setError( JText::_('Please provide a valid e-mail address.') );
			} else if (!Hubzero_Registration_Helper::validemail($email)) {
				$this->setError( JText::_('Invalid e-mail address. Example: someone@somewhere.com') );
			} else {
				// Send the account recovery
				$this->send_account_recovery($email);

				// Set a flag that all is well
				$view->passed = true;
			}
		}
		
		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------

	private function send_account_recovery($email)
	{
		ximport('Hubzero_User_Profile');
		ximport('Hubzero_User_Helper');
		ximport('Hubzero_Toolbox');
		
		$xhub =& Hubzero_Factory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');
		$hubLongURL = $xhub->getCfg('hubLongURL');
		$hubMonitorEmail = $xhub->getCfg('hubMonitorEmail');
		$hubHomeDir = $xhub->getCfg('hubHomeDir');

		// Attempt to load an account with this email address
		$emailusers = Hubzero_User_Profile_Helper::find_by_email($email);
		
		if (empty($emailusers)) {
			return JError::raiseError(403, 'Request Invalid: Error locating an account with the email address [' . $email . '].');
		}

		// Build the email subject
		$subject = $hubShortName . " Account Recovery";

		// Build the email message
		$message = "You recently requested your " . $hubShortName . " login be resent to this\r\n";
		$message .= "email address (" . $email . "). Our records show\r\n";
		$message .= count($emailusers) . " account";
		if (count($emailusers) > 1) {
			$message .= "s";
		}
		$message .= " registered to this address:\r\n";
		foreach ($emailusers as $emailuser) 
		{
			$xprofile =& Hubzero_User_Profile::getInstance($emailuser);

			$message .= "\t" . $xprofile->get('username') . "\t(" . $xprofile->get('name') . ")\r\n";
		}
		$message .= "\r\n";
		$message .= "You may login to " . $hubShortName . " using ";
		if (count($emailusers) > 1) {
		 	$message .= "one of these accounts";
		} else {
			$message .= "this account";
		}
		$message .= " here:\r\n";
		$message .= $hubLongURL . '/login' . "\r\n\r\n";
		$message .= "If you have also forgotten or lost your password, you can\r\n";
		$message .= "reset your password here:\r\n";
		$message .= $hubLongURL .DS.JRoute::_('index.php?option='.$this->_option.'&task=lostpassword') . "\r\n";

		// Send the email
		if (Hubzero_Toolbox::send_email($email, $subject, $message)) {
			// Admin email subject
			$subject = $hubShortName . " Account Recovery";
			
			// Admin email message
			$message = "A user has recovered account login information for the email address:\r\n";
			$message .= "\t" . $email . "\r\n\r\n";
			$message .= "Click the following link to look up this user's account(s):\r\n";
			$message .= $hubLongURL . '/members/whois/?email=' . $email . "\r\n";
			
			// Send the admin email
			Hubzero_Toolbox::send_email($hubMonitorEmail, $subject, $message);
		} else { 
			return JError::raiseError(500, 'Internal Error: Error emailing your account information to the email address [' . $email . '].');
		}
		
		return 0;
	}

	//-----------
	
	protected function lostpassword() 
	{
		// Load some needed libraries
		ximport('Hubzero_Registration_Helper');
		ximport('Hubzero_User_Helper');
		ximport('Hubzero_Toolbox');
		ximport('Hubzero_User_Profile');
		
		$xprofile =& Hubzero_Factory::getProfile();

		$this->_view = $this->_task;

		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_('Reset Password') );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		$pathway->addItem(JText::_('Reset Password'),'index.php?option='.$this->_option.'&task='.$this->_task);

		// Instantiate a new view
		$view = new JView( array('name'=>'lostpassword') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->login = null;
		$view->email = null;
		$view->reset = null;
		$view->passed = false;

		// Check if the user *can* reset their password
		if ( is_object($xprofile) && (Hubzero_User_Helper::isXDomainUser($xprofile->get('uidNumber'))) ) {
			$view->setError( JText::_('This is a linked account. To retrieve your password you must do so using the procedures available where the account your are linked to is managed.') );
			$view->display();
			return;
		}

		// Incoming
		$view->login = JRequest::getVar('login', '', 'post');
		$view->email = JRequest::getVar('email', '', 'post');
		$view->reset = JRequest::getVar('reset', '', 'post');
		
		// Was the form submitted?
		if ($view->reset) {
			// Attempt to load a user with the given username
			$xprofile =& Hubzero_User_Profile::getInstance($view->login);

			// Ensure we have a user with this login and e-mail
			if (!is_object($xprofile)) {
				$this->setError( JText::_('No account could be located matching this login. Please be sure to list your information exactly as originally specified.'));
			} elseif ($xprofile->get('email') != $view->email) {
				$this->setError( JText::_('Incorrect email address for this login. Please be sure to list your information exactly as originally specified.'));
			}

			if ($this->getError()) {
				$view->setError( $this->getError() );
				$view->display();
				return;
			}

			// Generate a new password
			$newpass = Hubzero_Registration_Helper::userpassgen();

			// Initiate profile class
			$profile = new Hubzero_User_Profile();
			$profile->load( $xprofile->get('uidNumber') );
			$profile->set('userPassword', Hubzero_User_Helper::encrypt_password($newpass));

			if (!$profile->update()) {
				$this->setError( JText::_('There was an error resetting your password.') );
			}

			if ($this->getError()) {
				$view->setError( $this->getError() );
				$view->display();
				return;
			}

			$jconfig =& JFactory::getConfig();
			$juri =& JURI::getInstance();
			
			// Email subject
			$subject = $jconfig->getValue('config.sitename') . " Account Password Reset";

			// Build the Admin email message
			$sef = JRoute::_('index.php?option=com_members&id='.$xprofile->get('uidNumber'));
			if (substr($sef,0,1) == '/') {
				$sef = substr($sef,1,strlen($sef));
			}
			$url = $juri->base().$sef."\r\n";

			$admmessage  = "The password has been reset for user '" . $view->login . "' on " . $jconfig->getValue('config.sitename') . ".\r\n\r\n";
			$admmessage .= "Please click the following link to review this user's information.\r\n";
			$admmessage .= $url . "\r\n";

			// Build the email message
			$sef = JRoute::_('index.php?option=com_members&id='.$xprofile->get('uidNumber').'&task=changepassword');
			if (substr($sef,0,1) == '/') {
				$sef = substr($sef,1,strlen($sef));
			}
			$url = $juri->base().$sef."\r\n";

			$usrmessage  = "The password has been reset for your account '" . $view->login . "' on " . $jconfig->getValue('config.sitename') . ".\r\n";
			$usrmessage .= "Your new password is:  " . $newpass . "\r\n\r\n";
			$usrmessage .= "Please click the following link to choose a new password.\r\n";
			$usrmessage .= $url . "\r\n\r\n";
			$usrmessage .= "If you feel this is in error, or you have any questions,\r\n";
			$usrmessage .= "contact " . $jconfig->getValue('config.sitename') . " administrators by replying to this message.";

			// Get the "from" info
			$from = array();
			$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
			$from['email'] = $jconfig->getValue('config.mailfrom');

			// E-mail the administrator
			if (!Hubzero_Toolbox::send_email($jconfig->getValue('config.mailfrom'), $subject, $admmessage)) {
				$this->setError(JText::_("There was an error emailing '" . htmlentities($jconfig->getValue('config.mailfrom'),ENT_COMPAT,'UTF-8') . "' about your password change request."));
			}

			// E-mail the user
			if (!Hubzero_Toolbox::send_email($xprofile->get('email'), $subject, $usrmessage)) {
				$this->setError(JText::_("There was an error emailing '" . htmlentities($xprofile->get('email'),ENT_COMPAT,'UTF-8') . "' your new password."));
			}

			$view->xprofile = $xprofile;
			$view->jconfig = $jconfig;
			$view->passed = true;
		}
		
		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
}

