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
	
	private function getTask()
	{
		$task = JRequest::getVar('task','','method');
		$this->_task = $task;
		return $task;
	}

	//-----------

	private function getView()
	{
		$view = JRequest::getVar('view','','method');
		$this->_view = $view;
		return $view;
	}

	//-----------

	private function getAction()
	{
		$act = JRequest::getVar('act','','method');
		$this->_act = $act;
		return $act;
	}

	//-----------
	
	private function cookie_check()
	{
		$xhub  = &XFactory::getHub();
		$jsession =& JFactory::getSession();
		$jcookie  =  $jsession->getName();

		if (!isset($_COOKIE[$jcookie]))
		{
			if (JRequest::getVar('cookie', '', 'get') != 'no')
			{
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
		$view = $this->getView();
		$task = $this->getTask();
		$act  = $this->getAction();
		$xhub =& XFactory::getHub();

		if ( !isset( $_SERVER['HTTPS'] ) || $_SERVER['HTTPS'] == 'off' )
		{
			$xhub->redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); 
			die('insecure connection and redirection failed'); 
		}

		switch( $view )
		{
			case 'registration':
				$app =& JFactory::getApplication();
				$pathway =& $app->getPathway();
				if (count($pathway->getPathWay()) <= 0) {
					$pathway->addItem(JText::_('Register'),'/register');
				}
				switch ($task) 
				{
					case 'select':
						$this->select($act);
						break;
					case 'create': 
						$this->create($act);
						break;
					case 'edit':  
						$this->edit($act);
						break;
					case 'update':  
						$this->update($act);
						break;
					case 'proxycreate': 
						$this->proxycreate($act);
						break;
					case 'passwordstrength': 
						$this->passwordstrength($act);
						break;
					// Email (account confirmation)
					case 'resend':
						$this->resend();
						break;
					case 'change':
						$this->change();
						break;
					case 'confirm':
						$this->confirm();
						break;
					case 'unconfirmed':
						$this->unconfirmed();
						break;
					default:
						$this->invalidRequest();
						break;
				}
				break;

			case 'login':
				$app =& JFactory::getApplication();
				$pathway =& $app->getPathway();
				if (count($pathway->getPathWay()) <= 0) {
					$pathway->addItem(JText::_('Login'),'/login');
				}
				switch($task)
				{
					case 'login':   
						$this->login($act);
						break;
					case 'realm':
						$this->realm($act);
						break;
					default:
						$this->invalidRequest();
						break;
				}
				break;

			case 'logout':
				switch($task)
				{
					case 'logout':
						$this->logout($act);
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
		$app  = &JFactory::getApplication();
		$xhub = &XFactory::getHub();
		
		// Preform the logout action
		$error = $app->logout();
		
		if (!JError::isError($error)) 
		{
			if ($return = JRequest::getVar('return', '', 'method', 'base64'))
				$return = base64_decode($return);

			if (empty($return))
				$return = '/';

			// Redirect if the return url is not registration or login
			return $xhub->redirect( $return );
		}

		echo HubHtml::error( $error->get('message') );
	}
	
	//-----------

	protected function edit()
	{
		ximport('xhubhelper');
		ximport('xregistration');
		ximport('xregistrationhelper');
			
		$app   = &JFactory::getApplication();
		$xuser = &XFactory::getUser();
		$xhub  = &XFactory::getHub();
		$juser = &JFactory::getUser();
		$jsession = &JFactory::getSession();

		if ($juser->get('guest'))
			return JError::raiseError( '500', 'xHUB Internal Error: Guest sessions may not edit user registrations.');
		
		$return = base64_decode( JRequest::getVar('return', '',  'method', 'base64') );

		if (empty($return))
		{
			$return = $jsession->get('session.return');

			if (empty($return))
				$return = '/';
		}

		$username = JRequest::getVar('username',$xuser->get('login'),'get');

		$target_xuser = XUser::getInstance($username);

		$app->SetPageTitle( JText::_('Edit Registration Information') );
		 
		$admin = $juser->authorize($this->_option, 'manage');
		$self = ($xuser->get('login') == $username);
		
		if (!$admin && !$self)
			return JError::raiseError( '500', 'xHUB Internal Error: Invalid Request to edit another user\'s registration.');

		$xregistration = new XRegistration();
			
		if (JRequest::getVar('edit', '', 'post'))
			$xregistration->loadPOST();
		else
		{
			$xregistration->loadXUser($target_xuser);
			return $this->show_registration_form($xregistration, 'edit');
		}

		if ($username != $xregistration->get('login'))
			return JError::raiseError( '500', 'xHUB Internal Error: Registration doesn\'t match user\'s data.');
		
		if (!$xregistration->check('edit'))
			return $this->show_registration_form($xregistration, 'edit');

		$target_xuser->loadRegistration($xregistration);

		$hubShortName    =  $xhub->getCfg('hubShortName');
		$hubLongURL      =  $xhub->getCfg('hubLongURL');
		$hubMonitorEmail =  $xhub->getCfg('hubMonitorEmail');
		$hubHomeDir      =  $xhub->getCfg('hubHomeDir');
		$updateEmail     =  false;

		if ($target_xuser->get('home') == '')
			$target_xuser->set('home', $hubHomeDir . '/' . $target_xuser->get('login'));

		if ($target_xuser->get('jobs_allowed') == '')
			$target_xuser->set('jobs_allowed', 3);

		if ($target_xuser->get('reg_ip') == '')
			$target_xuser->set('reg_ip', $_SERVER['REMOTE_ADDR']);

		if ($target_xuser->get('reg_host') == '')
			if (isset($_SERVER['REMOTE_HOST']))
			        $target_xuser->set('reg_host', $_SERVER['REMOTE_HOST']);

		if ($target_xuser->get('reg_date') == '')
			$target_xuser->set('reg_date', date('Y-m-d H:i:s'));

		if ($xregistration->get('email') != $target_xuser->get('email'))
		{
			$target_xuser->set('email_confirmed', -rand(1, pow(2, 31)-1) );
			$updateEmail = true;
		}

		$target_xuser->loadRegistration($xregistration);
		$target_xuser->update();

		echo HubHtml::div( HubHtml::hed(2, JText::_('Account Update')), 'full', 'content-header' );
		echo '<div class="main section">'.n;
		
		if ($self)
		{
			echo '<p class="passed">Your account has been updated successfully.</p>';

			if ($updateEmail)
			{
				$subject  = $hubShortName . " Account E-Mail Confirmation";

				$message  = 'Thank you for updating your account on ' . $hubShortName . '!' . "\r\n\r\n";
				$message .= 'Since you have changed your e-mail address you must click the following ';
				$message .= "link to confirm your new email address and reactivate your account:\r\n";

				//$message .= $hubLongURL . '/email/confirm/' . -$target_xuser->get('email_confirmed') . "\r\n\r\n";
				$message .= $hubLongURL . JRoute::_('index.php?option='.$this->_option.a.'task=registration'.a.'view=confirm'.a.'confirm='. -$target_xuser->get('email_confirmed')) . "\r\n\r\n";
				$message .= 'Do not reply to this email.  Replying to this email will not confirm or activate ';
				$message .= 'your account.' . "\r\n";

				echo '<p>Thank you for updating your account. In order to continue to use ';
				echo 'this account you must verify your new email address.';

				if (XHubHelper::send_email($target_xuser->get('email'), $subject, $message))
				{
					echo 'A confirmation email has been sent to \'' . $target_xuser->get('email');
					echo '\'. You must click the link in ';
					echo 'that email to activate your account and begin using ' . $hubShortName;
					echo '.</p>';
				}
				else
				{
					echo '</p><p class="error">We were unable to e-mail your account cofirmation code. ';
					echo 'to ' . $target_xuser->get('email') . '. Please contact ';
					echo 'support at ';
					echo $hubMonitorEmail . ' for assistance in (re)activating your account.</p>';
				}
			}

			$subject = $hubShortName . ' Account Update';

			$message = $target_xuser->get('name');

			if ($target_xuser->get('org'))
				$message .= ' / ' . $target_xuser->get('org');

			$message .= ' (' . $target_xuser->get('email') . ')';
			$message .= 'has updated their account \'' . $target_xuser->get('login') . '\' on ' . $hubShortName;
			$message .= '.' . "\r\n\r\n";
			$message .= 'Click the following link to review this user\'s account:' . "\r\n";
			$message .= $hubLongURL . '/whois?username=' . $xuser->get('login') . "\r\n";

			XHubHelper::send_email($hubMonitorEmail, $subject, $message);

			if (!$updateEmail)
			{
				$jsession->clear('session.return');
				$xhub->redirect($return);
			}
		}
		else
		{
			echo '<p class="passed">The account has been updated successfully.</p>';

			if ($updateEmail)
			{
				$subject  = $hubShortName . " Account E-Mail Confirmation";

				$message  = 'An administrative process has updated your account on ' . $hubShortName . '!' . "\r\n\r\n";
				$message .= 'This process has changed your registered e-mail address. You must click the following ';
				$message .= "link to confirm that you received this e-mail at the new address and reactivate your account:\r\n";

				//$message .= $hubLongURL . '/email/confirm/' . -$target_xuser->get('email_confirmed') . "\r\n\r\n";
				$message .= $hubLongURL . JRoute::_('index.php?option='.$this->_option.a.'task=registration'.a.'view=confirm'.a.'confirm='. -$target_xuser->get('email_confirmed')) . "\r\n\r\n";
				$message .= 'Do not reply to this email.  Replying to this email will not confirm or activate ';
				$message .= 'your account.' . "\r\n";

				echo '<p>The user of this account has been notified of the change. In order to continue to use ';
				echo 'this account they will need to verify the new email address.';

				if (XHubHelper::send_email($target_xuser->get('email'), $subject, $message))
				{
					echo 'A confirmation email has been sent to \'' . $target_xuser->get('email');
					echo '\'. They must click the link in ';
					echo 'that email to activate their account and continue using ' . $hubShortName;
					echo '.</p>';
				}
				else
				{
					echo 'We were unable to e-mail the account cofirmation code. ';
					echo 'to ' . $target_xuser->get('email') . '. Please contact ';
					echo 'support at ';
					echo $hubMonitorEmail . ' for assistance.</p>';
				}
			}

			$subject = $hubShortName . ' Account Update';

			$message = $target_xuser->get('name');

			if ($target_xuser->get('org'))
				$message .= ' / ' . $target_xuser->get('org');

			$message .= ' (' . $target_xuser->get('email') . ')';
			$message .= '\'s account \'' . $target_xuser->get('login') . '\' on ' . $hubShortName;
			$message .= 'has been edited by an adminnistrator.' . "\r\n\r\n";
			$message .= 'Click the following link to review this user\'s account:' . "\r\n";
			$message .= $hubLongURL . '/whois?username=' . $xuser->get('login') . "\r\n";

			XHubHelper::send_email($hubMonitorEmail, $subject, $message);

			if (!$updateEmail)
			{
				$jsession->clear('session.return');
				$xhub->redirect($return);
			}
		}
		
		echo '</div><!-- / .main section -->'.n;
	} 
	
	//-----------

	protected function proxycreate($action = '')
	{
		$juser = &JFactory::getUser(); 

		if (empty($action))
			$action = 'show';

		if ($action != 'submit' && $action != 'show')
			return JError::raiseError( 404, "Invalid Request" );

		if ($juser->get('guest'))
			return JError::raiseError( '500', 'xHUB Internal Error: Invalid request for guest to proxy a registration.');

		ximport('xregistration');
		ximport('xregistrationhelper');
		ximport('xhubhelper');

		$app   = &JFactory::getApplication();
		$xuser = &XFactory::getUser(); 
		$xhub  = &XFactory::getHub();
		
		$app->SetPageTitle('Proxy Create New Account');

		$xregistration = new XRegistration();

		if ($action == 'show')
		{
			$app =& JFactory::getApplication();
			$pathway =& $app->getPathway();
			$pathway->addItem(JText::_('Proxy Create'),'/hub/registration/proxycreate');
			
			$username = JRequest::getVar('username','','get');

			$xregistration->set('login', $username);

			return $this->show_registration_form($xregistration, 'proxy');
		}

		if ($action == 'submit')
			$xregistration->loadPost();

		if (!$xregistration->check('proxy'))
			return $this->show_registration_form($xregistration, 'proxy');

		$hubShortName    = $xhub->getCfg('hubShortName');
		$hubLongURL      = $xhub->getCfg('hubLongURL');
		$hubMonitorEmail = $xhub->getCfg('hubMonitorEmail');
		$hubHomeDir      = $xhub->getCfg('hubHomeDir');
                
		jimport('joomla.application.component.helper');
		$config   = &JComponentHelper::getParams( 'com_users' );
		$usertype = $config->get( 'new_usertype', 'Registered' );

		$acl =& JFactory::getACL();
		$target_juser = new JUser();
		$target_juser->set('id',0);
		$target_juser->set('name', $xregistration->get('name'));
		$target_juser->set('username', $xregistration->get('login'));
		$target_juser->set('password_clear','');
		$target_juser->set('email', $xregistration->get('email'));
		$target_juser->set('gid', $acl->get_group_id( '', $usertype));
		$target_juser->set('usertype', $usertype);
		$target_juser->save();

		$target_xuser = XUser::getInstance($target_juser->get('id'));
		$result = is_object($target_xuser);

		if ($result)
		{
			$target_xuser->loadRegistration($xregistration);
			$target_xuser->set('home', $hubHomeDir . '/' . $target_xuser->get('login'));
			$target_xuser->set('jobs_allowed', 3);
			$target_xuser->set('reg_ip', $_SERVER['REMOTE_ADDR']);
			$target_xuser->set('email_confirmed', -rand(1, pow(2, 31)-1) );

			if (isset($_SERVER['REMOTE_HOST']))
				$target_xuser->set('reg_host', $_SERVER['REMOTE_HOST']);

			$target_xuser->set('password', $xregistration->get('password'));
			$target_xuser->set('reg_date', date('Y-m-d H:i:s'));
			$target_xuser->set('proxy_uid', $juser->get('id'));
			$target_xuser->set('proxy_password', $xregistration->get('password'));
			$result = $target_xuser->update();
		}

		if (!$result)
		{
			echo HubHtml::div( HubHtml::hed(2, JText::_('Error Creating User By Proxy')), 'full', 'content-header' );
			echo HubHtml::div( HubHtml::error( 'There was an error creating the account. Please contact support at'. $hubMonitorEmail . ' for assistance in creating this account. Or try again later.' ), 'main section' );
			return;
		}

		echo HubHtml::div( HubHtml::hed(2, JText::_('User Created by Proxy')), 'full', 'content-header' );
		echo '<div class="main section">'.n;
		echo '<p class="passed">This proxy account has been created successfully. You may add to the text below, but you MUST ';
		echo 'send an email including all of this text to the new user at ' . $target_xuser->get('email') . ':</p>';
		echo '<blockquote><pre>';
		echo 'An account has been created on your behalf at ' . $hubShortName . ' by ';
		echo $xuser->get('name') . ".\r\n\r\n";
		echo 'Your initial new account username is:  ' . $target_juser->get('username') . "\r\n";
		echo 'Your initial new account password is:  ' . $target_xuser->get('proxy_password') . "\r\n";
		echo 'You must click the following link to confirm your email address and activate your account:' . "\r\n";
		//echo $hubLongURL . '/email/confirm/' . -$target_xuser->get('email_confirmed') . "\r\n";
		echo $hubLongURL . JRoute::_('index.php?option='.$this->_option.a.'task=registration'.a.'view=confirm'.a.'confirm='. -$target_xuser->get('email_confirmed')) . "\r\n\r\n";
		echo '(Do not reply to this email.  Replying to this email  will not confirm or activate your account.)' . "\r\n\r\n";
		echo 'After confirming your account, you may click the following link to set a new password:' . "\r\n";
		//echo $hubLongURL . '/password/change' . "\r\n\r\n";
		echo $hubLongURL . JRoute::_('index.php?option=com_members'.a.'id='.$target_juser->get('id').a.'task=changepassword') . "\r\n\r\n";
		echo '</pre></blockquote>';
		echo '<p>New user\'s profile page: <a href="'.JRoute::_('index.php?option=com_members'.a.'id='.$target_juser->get('id')).'">'.$target_juser->get('name').' ('.$target_juser->get('username').')</a></p>'.n;
		echo '</div><!-- / .main section -->'.n;
	}
	
	//-----------
	  
	protected function update($action = '')
	{
		ximport('xregistration');
		ximport('xregistrationhelper');
		ximport('xhubhelper');
		
		if (empty($action))
			$action = 'show';

		$app      = &JFactory::getApplication();
		$xprofile = &XFactory::getProfile();
		$xhub     = &XFactory::getHub();
		$juser    = &JFactory::getUser();
		$jsession = &JFactory::getSession();

		if ($juser->get('guest'))
		{
			echo HubHtml::error( JText::_('Session Expired') );
			return false;
		}

		$app->SetPageTitle( JText::_('Update Registration Information') );

		$xregistration = new XRegistration();

		if ($action == 'submit')
			$xregistration->loadPOST();
		else
			$xregistration->loadProfile($xprofile);

		if (!$xregistration->check('update', $juser->get('id')))
		{
			if ($action == 'submit')
			{
				if ($xprofile->hasTransientUsername())
					$xregistration->_encoded['login'] = $xregistration->get('login');

				if ($xprofile->hasTransientEmail())
					$xregistration->_encoded['email'] = $xregistration->get('email');
			}

			return $this->show_registration_form($xregistration, 'update');
		}
		
		if (!$xprofile->hasTransientUsername() && $xprofile->get('username') != $xregistration->get('login'))
			return JError::raiseError( '500', 'xHUB Internal Error: Registration form doesn\'t belong to current session.');
		
		$hubShortName    =  $xhub->getCfg('hubShortName');
		$hubLongURL      =  $xhub->getCfg('hubLongURL');
		$hubMonitorEmail =  $xhub->getCfg('hubMonitorEmail');
		$hubHomeDir      =  $xhub->getCfg('hubHomeDir');
		$updateEmail     =  false;
		
		if ($xprofile->get('home') == '')
			$xprofile->set('home', $hubHomeDir . '/' . $xprofile->get('login'));
			
		if ($xprofile->get('jobs_allowed') == '')
			$xprofile->set('jobs_allowed', 3);
			
		if ($xprofile->get('reg_ip') == '')
			$xprofile->set('reg_ip', $_SERVER['REMOTE_ADDR']);
			
		if ($xprofile->get('reg_host') == '')
			if (isset($_SERVER['REMOTE_HOST']))
			        $xprofile->set('reg_host', $_SERVER['REMOTE_HOST']);
			        
		if ($xprofile->get('reg_date') == '')
			$xprofile->set('reg_date', date('Y-m-d H:i:s'));
			
		if ($xregistration->get('email') != $xprofile->get('email'))
		{
			if ($xprofile->hasTransientEmail() && $xregistration->get('email') != $xprofile->getTransientEmail())
				$xprofile->set('email_confirmed', '3');
			else
			{
				$xprofile->set('email_confirmed', -rand(1, pow(2, 31)-1) );
				$updateEmail = true;
			}
		}

		if ($xregistration->get('login') != $xprofile->get('username'))
		{
			if ($xprofile->hasTransientUsername())
				$xprofile->set('home', $hubHomeDir . '/' . $xregistration->get('login'));
		}

		$xprofile->loadRegistration($xregistration);
		$xprofile->update();

		/* update juser table */
		/* TODO: only update if changed */

		$myjuser = JUser::getInstance($xprofile->get('uidNumber'));
		$myjuser->set('username', $xprofile->get('username'));
		$myjuser->set('email', $xprofile->get('email'));
		$myjuser->set('name', $xprofile->get('name'));
		$myjuser->save();

		/* update current session if appropriate */
		/* TODO: update all session of this user */
		/* TODO: only update if changed          */

		if ($myjuser->get('id') == $juser->get('id'))
		{
			$sjuser = $jsession->get('user');
			$sjuser->set('username', $xprofile->get('username'));
			$sjuser->set('email', $xprofile->get('email'));
			$sjuser->set('name', $xprofile->get('name'));
			$jsession->set('user', $sjuser);
			
			// Get the session object
			$table = & JTable::getInstance('session');
			$table->load( $jsession->getId() );
			$table->username = $xprofile->get('username');
			$table->update();
		}

		$jsession->set('registration.incomplete', false);

		echo HubHtml::div( HubHtml::hed(2, JText::_('Account Update')), 'full', 'content-header' );
		echo '<div class="main section">'.n;
		echo '<p class="passed">Your account has been updated successfully.</p>';
		
		if ($updateEmail)
		{
			$subject  = $hubShortName . " Account E-Mail Confirmation";

			$message  = 'Thank you for updating your account on ' . $hubShortName . '!' . "\r\n\r\n";
			$message .= 'Since you have changed your e-mail address you must click the following ';
			$message .= "link to confirm your new email address and reactivate your account:\r\n";

			//$message .= $hubLongURL . '/email/confirm/' . -$xprofile->get('email_confirmed') . "\r\n\r\n";
			$message .= $hubLongURL . JRoute::_('index.php?option='.$this->_option.a.'task=registration'.a.'view=confirm'.a.'confirm='. -$xprofile->get('email_confirmed')) . "\r\n\r\n";
			$message .= 'Do not reply to this email.  Replying to this email will not confirm or activate ';
			$message .= 'your account.' . "\r\n";

			echo '<p>Thank you for updating your account. In order to continue to use ';
			echo 'this account you must verify your new email address. ';

			if (XHubHelper::send_email($xprofile->get('email'), $subject, $message))
			{
			        echo 'A confirmation email has been sent to \'' . $xprofile->get('email');
			        echo '\'. You must click the link in ';
			        echo 'that email to activate your account and begin using ' . $hubShortName;
			        echo '.</p>';
			}
			else
			{
			        echo '</p><p class="error">We were unable to e-mail your account confirmation code. ';
			        echo 'to ' . $xprofile->get('email') . '. Please contact ';
			        echo 'support at ';
			        echo $hubMonitorEmail . ' for assistance in (re)activating your account.</p>';
			}
		}
		
		echo '</div><!-- / .main section -->'.n;

		if ($action == "submit")
		{
			$subject = $hubShortName . ' Account Update';

			$message = $xprofile->get('name');

			if ($xprofile->get('organization'))
				$message .= ' / ' . $xprofile->get('organization');

			$message .= ' (' . $xprofile->get('email') . ')';
			$message .= 'has updated their account \'' . $xprofile->get('username') . '\' on ' . $hubShortName;
			$message .= '.' . "\r\n\r\n";
			$message .= 'Click the following link to review this user\'s account:' . "\r\n";
			$message .= $hubLongURL . '/whois?username=' . $xprofile->get('username') . "\r\n";

			XHubHelper::send_email($hubMonitorEmail, $subject, $message);
		}

		if (!$updateEmail)
			$xhub->redirect($_SERVER['REQUEST_URI']);
	}
	
	//-----------

	protected function create($action = '')
	{
		ximport('xregistration');
		ximport('xregistrationhelper');
		ximport('xuser');
		ximport('xhubhelper');

		if (empty($action))
			$action = 'show';

		if ($action != 'submit' && $action != 'show')
			return JError::raiseError( 404, "Invalid Request" );

		$juser = &JFactory::getUser();

		if (!$juser->get('guest'))
			return JError::raiseError( '500', 'xHUB Internal Error: Non-guest sessions may not create new user registrations.');

		$xregistration = new XRegistration();

		if ($action == 'submit')
		{
			$xregistration->loadPost();
		
			if (!$xregistration->check('create'))
				return $this->show_registration_form($xregistration,'create');

			$xhub            =& XFactory::getHub();
			$hubShortName    =  $xhub->getCfg('hubShortName');
			$hubLongURL      =  $xhub->getCfg('hubLongURL');
			$hubMonitorEmail =  $xhub->getCfg('hubMonitorEmail');
			$hubHomeDir      =  $xhub->getCfg('hubHomeDir');
	
			jimport('joomla.application.component.helper');
			$config   = &JComponentHelper::getParams( 'com_users' );
			$usertype = $config->get( 'new_usertype', 'Registered' );

			$acl =& JFactory::getACL();
			$target_juser = new JUser();
			$target_juser->set('id',0);
			$target_juser->set('name', $xregistration->get('name'));
			$target_juser->set('username', $xregistration->get('login'));
			$target_juser->set('password_clear','');
			$target_juser->set('email', $xregistration->get('email'));
			$target_juser->set('gid', $acl->get_group_id( '', $usertype));
			$target_juser->set('usertype', $usertype);
			$target_juser->save();

			$xuser = XUser::getInstance($target_juser->get('id'));

			$result = is_object($xuser);

			if ($result)
			{
				$xuser->loadRegistration($xregistration);
				$xuser->set('home', $hubHomeDir . '/' . $xuser->get('login'));
				$xuser->set('jobs_allowed', 3);
				$xuser->set('reg_ip', $_SERVER['REMOTE_ADDR']);
				$xuser->set('email_confirmed', -rand(1, pow(2, 31)-1) );

				if (isset($_SERVER['REMOTE_HOST']))
			        	$xuser->set('reg_host', $_SERVER['REMOTE_HOST']);

				$xuser->set('reg_date', date('Y-m-d H:i:s'));
				$result = $xuser->update();

				$regReturn = JRequest::getVar('return', ''); 

				if (!empty($regReturn))
				{
					$target_profile =& XProfile::getInstance( $target_juser->get('id') );
					
					if (is_object($target_profile))
					{
						$target_profile->setParam('return', $regReturn);
						$target_profile->update();
					}
				}
			}

			echo HubHtml::div( HubHtml::hed(2, JText::_('Register')), 'full', 'content-header' );
			echo '<div class="main section">'.n;

			if (!$result)
			{
			        echo HubHtml::error( 
						'There was an error creating the account [' . $xregistration->get('login') . ']. '.
						'Please contact support at '. $hubMonitorEmail . ' for assistance in creating your account. Or try again later.'
					);
			        return;
			}

			echo '<p class="passed">Your account has been created successfully.</p>';

			$subject  = $hubShortName . " Account Confirmation";
			$message  = 'Thank you for creating an account on ' . $hubShortName . '! Your username is: ' .$xregistration->get('login'). "\r\n\r\n";
			$message .= 'You must click the following link to confirm your email address and activate your account:' . "\r\n";
			//$message .= $hubLongURL . '/email/confirm/' . -$xuser->get('email_confirmed') . "\r\n\r\n";
			$message .= $hubLongURL . JRoute::_('index.php?option='.$this->_option.a.'task=registration'.a.'view=confirm'.a.'confirm='. -$xuser->get('email_confirmed')) . "\r\n\r\n";
			$message .= 'Do not reply to this email.  Replying to this email will not confirm or activate ';
			$message .= 'your account.' . "\r\n";
	
			if (XHubHelper::send_email($xuser->get('email'), $subject, $message))
			{
			        echo '<p>A confirmation email has been sent to \'' . $xuser->get('email') . '\'. You must click the link in ';
			        echo 'that email to activate your account and resume using ' . $hubShortName . '.</p>';
			}
			else
			{
			        echo HubHtml::error( 'We were unable to e-mail your account confirmation code. Please contact support at'. $hubMonitorEmail . ' for assistance in activating your account.' );
			}
			
			echo '</div><!-- / .main section -->'.n;
	
			$subject = $hubShortName . ' Account Creation';
			$message = $xuser->get('name');
	
			if ($xuser->get('org'))
			        $message .= ' / ' . $xuser->get('org');
	
			$message .= ' (' . $xuser->get('email') . ')' . "\r\n";
			$message .= 'has requested the new account \'' . $xuser->get('login') . '\' on ' . $hubShortName;
			$message .= '.' . "\r\n\r\n";
			$message .= 'Click the following link to review this user\'s account:' . "\r\n";
			$message .= $hubLongURL . '/whois?username=' . $xuser->get('login') . "\r\n";
	
			XHubHelper::send_email($hubMonitorEmail, $subject, $message);
	
			return;
		}

		return $this->show_registration_form($xregistration, 'create');
	}
	
	//-----------

	protected function select($action = null)
	{
		$xhub  =& XFactory::getHub();
		$juser =& JFactory::getUser();

		if (empty($action))
			$action = 'show';

		if ($action != 'submit' && $action != 'show')
			return JError::raiseError( 404, "Invalid Request" );

		if (!$juser->get('guest'))
			return JError::raiseError( '500', 'xHUB Internal Error: Non-guest sessions may not create new user registrations.');
		
		if (!$this->cookie_check())
			return;

		$plugins = JPluginHelper::getPlugin('xauthentication');

		$realms = array();

		foreach ($plugins as $plugin)
		{
			$params = new JParameter($plugin->params);

	 	       	$realm = $params->get('domain');

	        	if (empty($realm))
	        		$realm = $plugin->name;
			
			if (!in_array($realm, $realms) && ($plugin->name != 'hzldap'))
				$realms[$plugin->name] = $realm;
		}

		if ($action == 'submit')
		{
			if (JRequest::getVar('register', '', 'method')) 
				return $this->create('show');

			if (JRequest::getVar('login', '', 'method'))
				return $this->login('show');
		}

		unset($plugins, $params, $realm, $action);
	
		if (count($realms) == 0)
			return $this->create('show');

		$hubShortName = $xhub->getCfg('hubShortName');

		include $xhub->getComponentViewFilename($this->_option, 'select');
	}
	
	//-----------

	private function registrationField($name, $default, $task = 'create')
	{
		if (($task == 'register') || ($task == 'create'))
			$index = 0;
		else if ($task == 'proxy')
			$index = 1;
		else if ($task == 'update')
			$index = 2;
		else if ($task == 'edit')
			$index = 3;
		else
			$index = 0;

          $hconfig =& JComponentHelper::getParams('com_hub');

          $default    = str_pad($default, '-', 4);
          $configured  = $hconfig->get($name);
          if (empty($configured))
               $configured = $default;
          $length     = strlen($configured);
          if ($length > $index) {
               $value = substr($configured, $index, 1);
          } else {
               $value = substr($default, $index, 1);
          }

		switch ($value)
		{
			case 'R': return(REG_REQUIRED);
			case 'O': return(REG_OPTIONAL);
			case 'H': return(REG_HIDE);
			case '-': return(REG_HIDE);
			case 'U': return(REG_READONLY);
			default : return(REG_HIDE);
		}
	}
	
	//-----------

	private function show_registration_form(&$xregistration = null, $task = 'create')
	{
		include_once( JPATH_ROOT.DS.'components'.DS.$this->_option.DS.'views'.DS.'registration'.DS.'view.html.php' );
		ximport('xregistration');
		ximport('xregistrationhelper');
		ximport('xuserhelper');

		$view = new HubViewRegistration();
		$view->task = $task;

		$app =& JFactory::getApplication();
		$xhub =& XFactory::getHub();

		if (!is_object($xregistration))
			$view->xregistration = new XRegistration();
		else
			$view->xregistration = $xregistration;

		$view->showMissing = true;
		$view->registration = $view->xregistration->_registration;
		$view->registrationUsername = $this->registrationField('registrationUsername','RROO',$task);
		$view->registrationPassword = $this->registrationField('registrationPassword','RRHH',$task);
		$view->registrationConfirmPassword = $this->registrationField('registrationConfirmPassword','RRHH',$task);
		$view->registrationFullname = $this->registrationField('registrationFullname','RRRR',$task);
		$view->registrationEmail = $this->registrationField('registrationEmail','RRRR',$task);
		$view->registrationConfirmEmail = $this->registrationField('registrationConfirmEmail','RRRR',$task);
		$view->registrationURL = $this->registrationField('registrationURL','HHHH',$task);
		$view->registrationPhone = $this->registrationField('registrationPhone','HHHH',$task);
		$view->registrationEmployment = $this->registrationField('registrationEmployment','HHHH',$task);
		$view->registrationOrganization = $this->registrationField('registrationOrganization','HHHH',$task);
		$view->registrationCitizenship = $this->registrationField('registrationCitizenship','HHHH',$task);
		$view->registrationResidency = $this->registrationField('registrationResidency','HHHH',$task);
		$view->registrationSex = $this->registrationField('registrationSex','HHHH',$task);
		$view->registrationDisability = $this->registrationField('registrationDisability','HHHH',$task);
		$view->registrationHispanic = $this->registrationField('registrationHispanic','HHHH',$task);
		$view->registrationRace = $this->registrationField('registrationRace','HHHH',$task);
		$view->registrationInterests = $this->registrationField('registrationInterests','HHHH',$task);
		$view->registrationReason = $this->registrationField('registrationReason','HHHH',$task);
		$view->registrationOptIn = $this->registrationField('registrationOptIn','HHHH',$task);
		$view->registrationTOU = $this->registrationField('registrationTOU','HHHH',$task);

		if ($view->task == 'update')
		{
			if (empty($view->xregistration->_encoded['login']))
				$view->registrationUsername = REG_READONLY;
			else
			{
				$view->registrationUsername = REG_REQUIRED;
				$view->registration['login'] = $view->xregistration->_encoded['login'];
			}

			$view->registrationPassword = REG_HIDE;
			$view->registrationConfirmPassword = REG_HIDE;
		}

		if ($view->task == 'edit')
		{
			$view->registrationUsername = REG_READONLY;
			$view->registrationPassword = REG_HIDE;
			$view->registrationConfirmPassword = REG_HIDE;
		}

		if ($view->registrationEmail == REG_REQUIRED || $view->registrationEmail == REG_OPTIONAL)
			if (!empty($view->xregistration->_encoded['email']))
				$view->registration['email'] = $view->xregistration->_encoded['email'];

		if ($view->registrationConfirmEmail == REG_REQUIRED || $view->registrationConfirmEmail == REG_OPTIONAL)
			if (!empty($view->xregistration->_encoded['email']))
				$view->registration['confirmEmail'] = $view->xregistration->_encoded['email'];

		$view->display();
	}
	
	//-----------

	public function login($action = 'show')
	{
		$xhub =& XFactory::getHub();
		$juser = &JFactory::getUser();

		$return = base64_decode( JRequest::getVar('return', '',  'method', 'base64') );

		if (empty($return)) {
		    	$hconfig = &JComponentHelper::getParams('com_hub');
			$r = $hconfig->get('LoginReturn');
			$return = ($r) ? $r : '/myhub';
		}

		if (!$juser->get('guest'))
			return $xhub->redirect($return);

		if (!$this->cookie_check())
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
		$xhub =& XFactory::getHub();
		
		if (!$this->cookie_check()) {
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
	
	protected function passwordstrength($act) 
	{
		$no_html = JRequest::getInt('no_html',0);
		$password = JRequest::getVar('pass','');
		$username = JRequest::getVar('user','');
		
		ximport('xregistration');
		$xregistration = new XRegistration();
		
		$score = $xregistration->scorePassword($password, $username);
		
		if ($score < PASS_SCORE_MEDIOCRE) {
			$cls = 'bad';
			$txt = 'Bad';
		} else if ($score >= PASS_SCORE_MEDIOCRE && $score < PASS_SCORE_GOOD) {
			$cls = 'mediocre';
			$txt = 'Mediocre';
		} else if ($score >= PASS_SCORE_GOOD && $score < PASS_SCORE_STRONG) {
			$cls = 'good';
			$txt = 'Good';
		} else if ($score >= PASS_SCORE_STRONG) {
			$cls = 'strong';
			$txt = 'Strong';
		}
		
		//$html  = '<span id="meter-container" class="hide">';
		$html = '<span id="passwd-meter" style="width:'.$score.'%;" class="'.$cls.'"><span>'.JText::_($txt).'</span></span>';
		//$html .= '</span>';
		
		if ($no_html) {
			echo $html;
		} else {
			return $html;
		}
	}

	//-----------
	
	protected function lostusername() 
	{
		// Load some needed libraries
		ximport('xregistrationhelper');
		ximport('xuserhelper');
		
		$this->_view = $this->_task;
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_('Lost Username') );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		$pathway->addItem(JText::_('Lost Username'),'index.php?option='.$this->_option.a.'task='.$this->_task);
		
		// Incoming
		$email  = JRequest::getVar('email', NULL, 'post');
		$resend = JRequest::getVar('resend', NULL, 'post');
		
		// Was the form submitted?
		if ($resend) {
			if (empty($email)) {
				$this->setError( JText::_('Please provide a valid e-mail address.') );
			} else if (!XRegistrationHelper::validemail($email)) {
				$this->setError( JText::_('Invalid e-mail address. Example: someone@somewhere.com') );
			} else {
				// Send the account recovery
				$this->send_account_recovery($email);

				// Output HTML
				echo HubHtml::div( HubHtml::hed(2, JText::_('Lost Username')), 'full', 'content-header');
				echo HubHtml::div( '<p class="passed">Your account information has been emailed to you at "'. htmlentities($email,ENT_COMPAT,'UTF-8') .'". Please check your email for details on how to login.</p>', 'main section' );
				return;
			}
		}
		
		// Load the HTML
		$xhub =& XFactory::getHub();
		
		include $xhub->getComponentViewFilename($this->_option, 'recovery');
	}
	
	//-----------

	private function send_account_recovery($email)
	{
		ximport('xuser');
		ximport('xhubhelper');
		
		$xhub =& XFactory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');
		$hubLongURL = $xhub->getCfg('hubLongURL');
		$hubMonitorEmail = $xhub->getCfg('hubMonitorEmail');
		$hubHomeDir = $xhub->getCfg('hubHomeDir');

		// Attempt to load an account with this email address
		$emailusers = XUserHelper::getemailusers($email);

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
			$xuser =& XUser::getInstance($emailuser);
			$emailuserobj = $xuser->getuser();

			$message .= "\t" . $xuser->get('login') . "\t(" . $xuser->get('name') . ")\r\n";
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
		if (XHubHelper::send_email($email, $subject, $message)) {
			// Admin email subject
			$subject = $hubShortName . " Account Recovery";
			
			// Admin email message
			$message = "A user has recovered account login information for the email address:\r\n";
			$message .= "\t" . $email . "\r\n\r\n";
			$message .= "Click the following link to look up this user's account(s):\r\n";
			$message .= $hubLongURL . '/whois/?email=' . $email . "\r\n";
			
			// Send the admin email
			XHubHelper::send_email($hubMonitorEmail, $subject, $message);
		} else { 
			return JError::raiseError(500, 'Internal Error: Error emailing your account information to the email address [' . $email . '].');
		}
		
		return 0;
	}

	//-----------
	
	protected function lostpassword() 
	{
		// Load some needed libraries
		ximport('xregistrationhelper');
		ximport('xuserhelper');
		ximport('xhubhelper');
		ximport('xprofile');
		
		$xuser =& XFactory::getUser();

		$this->_view = $this->_task;

		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_('Reset Password') );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		$pathway->addItem(JText::_('Reset Password'),'index.php?option='.$this->_option.a.'task='.$this->_task);

		// Check if the user *can* reset their password
		if ( is_object($xuser) && (XUserHelper::isXDomainUser($xuser->get('uid'))) ) {
			echo HubHtml::div( HubHtml::hed(2, JText::_('Reset Password')), 'full', 'content-header');
			echo HubHtml::div( HubHtml::warning( "This is a linked account. To retrieve your password you must do so using the procedures available where the account your are linked to is managed." ), 'main section' );
			return;
		}
		
		$xhub =& XFactory::getHub();

		// Incoming
		$login = JRequest::getVar('login', '', 'post');
		$email = JRequest::getVar('email', '', 'post');
		$reset = JRequest::getVar('reset', '', 'post');
		
		// Was the form submitted?
		if ($reset) {
			// Attempt to load a user with the given username
			$xuser =& XUser::getInstance($login);

			// Ensure we have a user with this login and e-mail
			if (!is_object($xuser)) {
				$this->setError( JText::_('No account could be located matching this login. Please be sure to list your information exactly as originally specified.'));
			} elseif($xuser->get('email') != $email) {
				$this->setError( JText::_('Incorrect email address for this login. Please be sure to list your information exactly as originally specified.'));
			}

			if ($this->getError()) {
				include $xhub->getComponentViewFilename($this->_option, 'password');
				return;
			}

			// Generate a new password
			$newpass = XRegistrationHelper::userpassgen();

			// Initiate profile class
			$profile = new XProfile();
			$profile->load( $xuser->get('uid') );
			$profile->set('userPassword', XUserHelper::encrypt_password($newpass));

			if (!$profile->update()) {
				$this->setError( JText::_('There was an error resetting your password.') );
			}

			if ($this->getError()) {
				include $xhub->getComponentViewFilename($this->_option, 'password');
				return;
			}

			$xhub =& XFactory::getHub();
			$juri =& JURI::getInstance();
			
			// Email subject
			$subject = $xhub->getCfg('hubShortName') . " Account Password Reset";

			// Build the Admin email message
			$sef = JRoute::_('index.php?option=com_members&id='.$xuser->get('uid'));
			if (substr($sef,0,1) == '/') {
				$sef = substr($sef,1,strlen($sef));
			}
			$url = $juri->base().$sef."\r\n";

			$admmessage  = "The password has been reset for user '" . $login . "' on " . $xhub->getCfg('hubShortName') . ".\r\n\r\n";
			$admmessage .= "Please click the following link to review this user's information.\r\n";
			$admmessage .= $url . "\r\n";

			// Build the email message
			$sef = JRoute::_('index.php?option=com_members&id='.$xuser->get('uid').a.'task=changepassword');
			if (substr($sef,0,1) == '/') {
				$sef = substr($sef,1,strlen($sef));
			}
			$url = $juri->base().$sef."\r\n";

			$usrmessage  = "The password has been reset for your account '" . $login . "' on " . $xhub->getCfg('hubShortName') . ".\r\n";
			$usrmessage .= "Your new password is:  " . $newpass . "\r\n\r\n";
			$usrmessage .= "Please click the following link to choose a new password.\r\n";
			$usrmessage .= $url . "\r\n\r\n";
			$usrmessage .= "If you feel this is in error, or you have any questions,\r\n";
			$usrmessage .= "contact " . $xhub->getCfg('hubShortName') . " administrators by replying to this message.";

			// Get the "from" info
			$from = array();
			$from['name']  = $xhub->getCfg('hubShortName').' '.JText::_(strtoupper($this->_name));
			$from['email'] = $xhub->getCfg('hubMonitorEmail');

			// E-mail the administrator
			$message = '';
			if (!XHubHelper::send_email($xhub->getCfg('hubMonitorEmail'), $subject, $admmessage)) {
				$message .= '<p class="passed">Your password has been reset to a new password successfully.</p>';
				$message .= HubHtml::error("There was an error emailing '" . htmlentities($emailadmin,ENT_COMPAT,'UTF-8') . "' about your password change request.");
			}

			// E-mail the user
			if (!XHubHelper::send_email($xuser->get('email'), $subject, $usrmessage)) {
				$message .= HubHtml::error("There was an error emailing '" . htmlentities($xuser->get('email'),ENT_COMPAT,'UTF-8') . "' your new password.");
			} else {
				$message .= '<p class="passed">Your password has been reset to a new password successfully.<br />';
				$message .= '<br />Your new password has been emailed to you at "' . htmlentities($xuser->get('email'),ENT_COMPAT,'UTF-8') . '". If you do not receive it or have any questions, please contact administrators at <a href="mailto:' . htmlentities($xhub->getCfg('hubMonitorEmail'),ENT_COMPAT,'UTF-8') . '">' . htmlentities($xhub->getCfg('hubMonitorEmail'),ENT_COMPAT,'UTF-8') . '</a>.</p>'.n;
			}

			// Output HTML
			echo HubHtml::div( HubHtml::hed(2, JText::_('Reset Password')), 'full', 'content-header');
			echo HubHtml::div( $message, 'main section');
			return;
		}
		
		// Load the HTML
		include $xhub->getComponentViewFilename($this->_option, 'password');
	}
	
	//----------------------------------------------------------
	//  Email (account confirmation)
	//----------------------------------------------------------
	
	protected function resend()
	{
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_('Register').': '.JText::_('Resend Confirmation Email') );

		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_('Register'),'/register');
		}
		$pathway->addItem(JText::_('Resend Confirmation Email'),'index.php?option='.$this->_option.a.'task=registration'.a.'view='.$this->_task);
		
		$juser = &JFactory::getUser();
		if ($juser->get('guest')) {
			echo HubHtml::div( HubHtml::hed(2, JText::_('Resend Confirmation Email')), 'full', 'content-header' );
			echo '<div class="main section">'.n;
			echo HubHtml::warning( JText::_('To resend your account confirmation email, you must provide a valid login.') );
			ximport('xmodule');
			XModuleHelper::displayModules('force_mod');
			echo '</div><!-- / .main section -->'.n;
			return;
		}
		
		$xuser =& XFactory::getUser();
		$login = $xuser->get('login');
		$email = $xuser->get('email');
		$email_confirmed = $xuser->get('email_confirmed');
		
		ximport('xuser');
		
		// Incoming
		$return = urldecode( JRequest::getVar( 'return', '/' ) );
		
		if (($email_confirmed != 1) && ($email_confirmed != 3)) {
			// Include view file
			$xhub =& XFactory::getHub();
			include $xhub->getComponentViewFilename($this->_option, 'email');
			
			// Output HTML
			echo HubHtml::div( HubHtml::hed(2, JText::_('Resend Confirmation Email')), 'full', 'content-header' );
			echo HubHtml::div( EmailHtml::send_code($login,$email,$return,$this->_option), 'main section' );
		} else { 
			header("Location: " . urlencode($return));
		}
	}

	//-----------
	
	protected function change()
	{
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_('Register').': '.JText::_('Change Email Address') );

		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_('Register'),'/register');
		}
		$pathway->addItem(JText::_('Change Email Address'),'index.php?option='.$this->_option.a.'task=registration'.a.'view='.$this->_task);
		
		// Check if the user is logged in
		$juser = &JFactory::getUser();
		if ($juser->get('guest')) {
			echo HubHtml::div( HubHtml::hed(2, JText::_('Change Email Address')), 'full', 'content-header' );
			echo '<div class="main section">'.n;
			echo HubHtml::warning( JText::_('To update your account information, you must provide a valid login.') );
			ximport('xmodule');
			XModuleHelper::displayModules('force_mod');
			echo '</div><!-- / .main section -->'.n;
			return;
		}
		
		// Load some needed libraries
		ximport('xregistrationhelper');
		ximport('xuser');
		
		$xuser =& XFactory::getUser();
		$login = $xuser->get('login');
		$email = $xuser->get('email');
		$email_confirmed = $xuser->get('email_confirmed');
		
		// Incoming
		$return = urldecode( JRequest::getVar( 'return', '/' ) );
		
		// Include view file
		$xhub =& XFactory::getHub();
		include $xhub->getComponentViewFilename($this->_option, 'email');
		
		// Check if a new email was submitted
		$html = EmailHtml::change($this->_option, $email, $email_confirmed, $return);
		if (!$html) {
			$pemail = JRequest::getVar( 'email', '', 'post' );
			
			// Check if the email address was actually changed
			if ($pemail == $email) {
				// Addresses are the same! Redirect
				$xhub->redirect($return);
			} else {
				// New email submitted - attempt to save it
				$html = EmailHtml::save_change($login, $pemail);

				// Any errors returned?
				if (!$html) {
					// No errors
					// Attempt to send a new confirmation code
					$html  = '<p class="passed">'.JText::_('Your account has been updated successfully.').'</p>'.n;
					$html .= EmailHtml::send_code($login, $pemail, $return, $this->_option);
				}
			}
		}
		
		// Output HTML
		echo HubHtml::div( HubHtml::hed(2, JText::_('Change Email Address')), 'full', 'content-header' );
		echo HubHtml::div( $html, 'main section' );
	}

	//-----------
	
	protected function confirm()
	{
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_('Register').': '.JText::_('Confirm Email Address') );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_('Register'),'/register');
		}
		$pathway->addItem(JText::_('Confirm Email Address'),'index.php?option='.$this->_option.a.'task=registration'.a.'view='.$this->_task);
		
		// Check if the user is logged in
		$juser = &JFactory::getUser();
		if ($juser->get('guest')) {
			echo HubHtml::div( HubHtml::hed(2, JText::_('Confirm Email Address')), 'full', 'content-header' );
			echo '<div class="main section">'.n;
			echo HubHtml::warning( JText::_('To confirm your account information, you must first login.') );
			ximport('xmodule');
			XModuleHelper::displayModules('force_mod');
			echo '</div><!-- / .main section -->'.n;
			return;
		}
		
		// Load some needed libraries
		ximport('xregistrationhelper');
		ximport('xuser');
		
		$xuser =& XFactory::getUser();
		$login = $xuser->get('login');
		$email = $xuser->get('email');
		$email_confirmed = $xuser->get('email_confirmed');
		
		// Incoming
		$code = JRequest::getVar( 'confirm', false );
		if (!$code) {
			$code = JRequest::getVar( 'code', false );
		}
		
		// Include view file
		$xhub =& XFactory::getHub();
		include $xhub->getComponentViewFilename($this->_option, 'email');

		// Output HTML
		echo EmailHtml::confirm($this->_option, $login, $email, $email_confirmed, $code);
	}
	
	//-----------
	
	protected function unconfirmed()
	{
		// Load some needed libraries
		ximport('xregistrationhelper');
		ximport('xuser');
		
		$xuser =& XFactory::getUser();
		$email = $xuser->get('email');
		$email_confirmed = $xuser->get('email_confirmed');
		
		// Incoming
		$return = JRequest::getVar( 'return', urlencode('/') );
		
		// Check if the email has been confirmed
		if (($email_confirmed != 1) && ($email_confirmed != 3)) {
			// Set the page title
			$document =& JFactory::getDocument();
			$document->setTitle( JText::_('Register').': '.JText::_('Email Address Unconfirmed') );

			// Set the pathway
			$app =& JFactory::getApplication();
			$pathway =& $app->getPathway();
			if (count($pathway->getPathWay()) <= 0) {
				$pathway->addItem(JText::_('Register'),'/register');
			}
			$pathway->addItem(JText::_('Email Address Unconfirmed'),'index.php?option='.$this->_option.a.'task=registration'.a.'view='.$this->_task);
			
			// Check if the user is logged in
			$juser = &JFactory::getUser();
			if ($juser->get('guest')) {
				echo HubHtml::div( HubHtml::hed(2, JText::_('Confirm Email Address')), 'full', 'content-header' );
				echo '<div class="main section">'.n;
				echo HubHtml::warning( JText::_('To confirm your account information, you must first login.') );
				ximport('xmodule');
				XModuleHelper::displayModules('force_mod');
				echo '</div><!-- / .main section -->'.n;
				return;
			}
			
			// Include view file
			$xhub =& XFactory::getHub();
			include $xhub->getComponentViewFilename($this->_option, 'email');
			
			// Output HTML
			echo EmailHtml::unconfirmed($this->_option, $return, $email, $xhub);
		} else {
			header("Location: " . urldecode($return));
		}
	}
}
?>
