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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Controller class for registration
 */
class RegisterController extends Hubzero_Controller
{
	/**
	 * Determine task and execute it
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$app = JFactory::getApplication();
		$this->jconfig = JFactory::getConfig();

		$juri =& JURI::getInstance();
		$this->baseURL = rtrim($juri->base(), DS);

		// Make sure we're using a secure connection
		if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off')
		{
			$app->redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], '', 'message', true);
			die('insecure connection and redirection failed');
		}

		$this->_task = strtolower(JRequest::getVar('task', ''));
		$act = strtolower(JRequest::getVar('act', '', 'post'));

		switch ($this->_task)
		{
			// Account creation/updating
			case 'select': $this->select($act); break;
			case 'create': $this->create($act); break;
			case 'edit':   $this->edit($act);   break;
			case 'update': $this->update($act); break;
			case 'proxy':  $this->proxycreate($act);  break;
			case 'proxycreate': $this->proxycreate($act); break;

			// AJAX methods
			case 'passwordstrength': $this->passwordstrength($act); break;
			case 'checkusername': $this->checkusername(); break;
			
			// Account confirmation
			case 'resend':      $this->resend();      break;
			case 'change':      $this->change();      break;
			case 'confirm':     $this->confirm();     break;
			case 'unconfirmed': $this->unconfirmed(); break;

			case 'raceethnic': $this->raceethnic(); break;
			default: $this->create(); break;
		}
	}

	/**
	 * Short description for 'edit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
	protected function edit()
	{
		$app = JFactory::getApplication();
		
		if ($this->juser->get('guest')) 
		{
			return JError::raiseError(500, JText::_('COM_REGISTER_ERROR_GUEST_SESSION_EDITING'));
		}

		$xprofile =& Hubzero_Factory::getProfile();
		$jsession =& JFactory::getSession();

		// Get the return URL
		$return = base64_decode(JRequest::getVar('return', '',  'method', 'base64'));
		if (!$return) 
		{
			$return = $jsession->get('session.return');

			if (!$return) 
			{
				$return = '/';
			}
		}

		$username = JRequest::getVar('username',$xprofile->get('username'),'get');

		$target_xprofile = Hubzero_User_Profile::getInstance($username);

		$admin = $this->juser->authorize($this->_option, 'manage');
		$self = ($xprofile->get('username') == $username);

		if (!$admin && !$self) 
		{
			return JError::raiseError(500, JText::_('COM_REGISTER_ERROR_INVALID_SESSION_EDITING'));
		}

		// Add the CSS to the template
		$this->_getStyles();

		// Add some Javascript to the template
		$this->_getScripts();

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Instantiate a new registration object
		$xregistration = new Hubzero_Registration();

		if (JRequest::getVar('edit', '', 'post')) 
		{
			// Load POSTed data
			$xregistration->loadPOST();
		} 
		else 
		{
			// Load data from the user object
			$xregistration->loadProfile($target_xprofile);
			return $this->_show_registration_form($xregistration, 'edit');
		}

		if ($username != $xregistration->get('login')) 
		{
			return JError::raiseError(500, JText::_('COM_REGISTER_ERROR_REGISTRATION_DATA_MISMATCH'));
		}

		if (!$xregistration->check('edit')) 
		{
			return $this->_show_registration_form($xregistration, 'edit');
		}

		$target_xprofile->loadRegistration($xregistration);
		
		$params =& JComponentHelper::getParams('com_members');
		
		$hubHomeDir = rtrim($params->get('homedir'),'/');
		
		$updateEmail     = false;

		if ($target_xprofile->get('homeDirectory') == '') 
		{
			$target_xprofile->set('homeDirectory', $hubHomeDir . '/' . $target_xprofile->get('username'));
		}

		if ($target_xprofile->get('jobsAllowed') == '') 
		{
			$target_xprofile->set('jobsAllowed', 3);
		}

		if ($target_xprofile->get('regIP') == '') 
		{
			$target_xprofile->set('regIP', JRequest::getVar('REMOTE_ADDR','','server'));
		}

		if ($target_xprofile->get('regHost') == '') 
		{
			if (isset($_SERVER['REMOTE_HOST'])) 
			{
				$target_xprofile->set('regHost', JRequest::getVar('REMOTE_HOST','','server'));
			}
		}

		if ($target_xprofile->get('registerDate') == '') 
		{
			$target_xprofile->set('registerDate', date('Y-m-d H:i:s'));
		}

		if ($xregistration->get('email') != $target_xprofile->get('email')) 
		{
			$target_xprofile->set('emailConfirmed', -rand(1, pow(2, 31)-1));
			$updateEmail = true;
		}

		$target_xprofile->loadRegistration($xregistration);

		$target_xprofile->update();

		if ($self) 
		{
			// Notify the user
			if ($updateEmail) 
			{
				$subject  = $this->jconfig->getValue('config.sitename') .' '.JText::_('COM_REGISTER_EMAIL_CONFIRMATION');

				$eview = new JView(array('name'=>'emails','layout'=>'update'));
				$eview->option = $this->_option;
				$eview->sitename = $this->jconfig->getValue('config.sitename');
				$eview->xprofile = $target_xprofile;
				$eview->baseURL = $this->baseURL;
				$message = $eview->loadTemplate();
				$message = str_replace("\n", "\r\n", $message);
				if (!Hubzero_Toolbox::send_email($target_xprofile->get('email'), $subject, $message)) 
				{
					$this->setError(JText::sprintf('COM_REGISTER_ERROR_EMAILING_CONFIRMATION'/*, $hubMonitorEmail*/));
					// @FIXME: LOG ERROR CONDITION SOMEWHERE
				}
			}

			// Notify administration
			$subject = $this->jconfig->getValue('config.sitename') .' '.JText::_('COM_REGISTER_EMAIL_ACCOUNT_UPDATE');

			$eaview = new JView(array('name'=>'emails','layout'=>'adminupdate'));
			$eaview->option = $this->_option;
			$eaview->sitename = $this->jconfig->getValue('config.sitename');
			$eaview->xprofile = $target_xprofile;
			$eaview->baseURL = $this->baseURL;
			$message = $eaview->loadTemplate();
			$message = str_replace("\n", "\r\n", $message);

			// Hubzero_Toolbox::send_email($hubMonitorEmail, $subject, $message);
			// @FIXME: LOG ACCOUNT UPDATE ACTIVITY SOMEWHERE

			// Determine action based on if the user chaged their email or not
			if (!$updateEmail) 
			{
				// Redirect
				$jsession->clear('session.return');
				$app->redirect($return,'','message',true);
			}
		} 
		else 
		{
			if ($updateEmail) 
			{
				$subject  = $this->jconfig->getValue('config.sitename') .' '.JText::_('COM_REGISTER_EMAIL_CONFIRMATION');

				$eview = new JView(array('name'=>'emails','layout'=>'updateproxy'));
				$eview->option = $this->_option;
				$eview->sitename = $this->jconfig->getValue('config.sitename');
				$eview->xprofile = $target_profile;
				$eview->baseURL = $this->baseURL;
				$message = $eview->loadTemplate();
				$message = str_replace("\n", "\r\n", $message);

				if (!Hubzero_Toolbox::send_email($target_xprofile->get('email'), $subject, $message)) 
				{
					$this->setError(JText::sprintf('COM_REGISTER_ERROR_EMAILING_CONFIRMATION'/*, $hubMonitorEmail*/));
					// @FIXME: LOG ERROR CONDITION SOMEWHERE
				}
			}

			// Notify administration
			$subject = $this->jconfig->getValue('config.sitename') .' '.JText::_('COM_REGISTER_EMAIL_ACCOUNT_UPDATE');

			$eaview = new JView(array('name'=>'emails','layout'=>'adminupdateproxy'));
			$eaview->option = $this->_option;
			$eaview->sitename = $this->jconfig->getValue('config.sitename');
			$eaview->xprofile = $target_xprofile;
			$eaview->baseURL = $this->baseURL;
			$message = $eaview->loadTemplate();
			$message = str_replace("\n", "\r\n", $message);

			// Hubzero_Toolbox::send_email($hubMonitorEmail, $subject, $message);
			// @FIXME: LOG ACCOUNT UPDATE ACTIVITY SOMEWHERE

			// Determine action based on if the user chaged their email or not
			if (!$updateEmail) 
			{
				// Redirect
				$jsession->clear('session.return');
				$app->redirect($return,'','message',true);
			}
		}

		// Instantiate a new view
		$view = new JView(array('name'=>'update'));
		$view->option = $this->_option;
		$view->title = JText::_('COM_REGISTER_UPDATE');
		$view->sitename = $this->jconfig->getValue('config.sitename');
		$view->xprofile = $target_xprofile;
		$view->self = $self;
		if ($this->getError()) 
		{
			$view->setError($this->getError());
		}
		$view->display();
	}

	/**
	 * Short description for 'proxycreate'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $action Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	protected function proxycreate($action='show')
	{
		ximport('Hubzero_User_Password');
		
		$action = ($action) ? $action : 'show';

		$admin = $this->juser->authorize($this->_option, 'manage');

		if (!$admin) 
		{
			return JError::raiseError(404, JText::_('COM_REGISTER_ERROR_INVALID_REQUEST'));
		}

		if ($action != 'submit' && $action != 'show') 
		{
			return JError::raiseError(404, JText::_('COM_REGISTER_ERROR_INVALID_REQUEST'));
		}

		if ($this->juser->get('guest')) 
		{
			return JError::raiseError(500, JText::_('COM_REGISTER_ERROR_GUEST_PROXY_CREATE'));
		}

		// Add the CSS to the template
		$this->_getStyles();

		// Add some Javascript to the template
		$this->_getScripts();

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Instantiate a new registration object
		$xregistration = new Hubzero_Registration();

		// Show the form if needed
		if ($action == 'show') 
		{
			$username = JRequest::getVar('username','','get');

			$xregistration->set('login', $username);

			return $this->_show_registration_form($xregistration, 'proxycreate');
		}

		// Load POSTed data
		if ($action == 'submit') 
		{
			$xregistration->loadPost();
		}

		// Perform field validation
		if (!$xregistration->check('proxy')) 
		{
			return $this->_show_registration_form($xregistration, 'proxycreate');
		}

		$xprofile =& Hubzero_Factory::getProfile();

		// Get some settings
		$jconfig =& JFactory::getConfig();
		$this->jconfig = $jconfig;
		$params =& JComponentHelper::getParams('com_members');
		$hubHomeDir = rtrim($params->get('homedir'),'/');
		
		jimport('joomla.application.component.helper');
		$config   =& JComponentHelper::getParams('com_users');
		$usertype = $config->get('new_usertype', 'Registered');

		$acl =& JFactory::getACL();

		// Create a new Joomla user
		$target_juser = new JUser();
		$target_juser->set('id',0);
		$target_juser->set('name', $xregistration->get('name'));
		$target_juser->set('username', $xregistration->get('login'));
		$target_juser->set('password_clear','');
		$target_juser->set('email', $xregistration->get('email'));
		$target_juser->set('gid', $acl->get_group_id('', $usertype));
		$target_juser->set('usertype', $usertype);
		$target_juser->save();

		// Attempt to retrieve the new user
		$target_xprofile = Hubzero_User_Profile::getInstance($target_juser->get('id'));
		$result = is_object($target_xprofile);

		// Did we successully create an account?
		if ($result) 
		{
			$target_xprofile->loadRegistration($xregistration);
			$target_xprofile->set('homeDirectory', $hubHomeDir . '/' . $target_xprofile->get('username'));
			$target_xprofile->set('jobsAllowed', 3);
			$target_xprofile->set('regIP', JRequest::getVar('REMOTE_ADDR','','server'));
			$target_xprofile->set('emailConfirmed', -rand(1, pow(2, 31)-1));
			
			if (isset($_SERVER['REMOTE_HOST'])) 
			{
				$target_xprofile->set('regHost', JRequest::getVar('REMOTE_HOST','','server'));
			}

			$target_xprofile->set('registerDate', date('Y-m-d H:i:s'));
			$target_xprofile->set('proxyUidNumber', $this->juser->get('id'));
			$target_xprofile->set('proxyPassword', $xregistration->get('password'));

			// Update the account
			$result = $target_xprofile->update();
		}

		if ($result) 
		{
			$result = Hubzero_User_Password::changePassword($target_xprofile->get('username'), $xregistration->get('password'));
		}
		
		// Did we successully create/update an account?
		if (!$result) 
		{
			// @FIXME: Should delete partially created records on failure
			$view = new JView(array('name'=>'error'));
			$view->title = JText::_('COM_REGISTER_PROXY_CREATE');
			$view->setError(JText::sprintf('COM_REGISTER_ERROR_CREATING_ACCOUNT'/*, $hubMonitorEmail*/));
			// @FIXME: LOG ERROR CONDITION SOMEWHERE
			$view->display();
			return;
		}

		// Instantiate a new view
		$view = new JView(array('name'=>'proxycreate'));
		$view->option = $this->_option;
		$view->title = JText::_('COM_REGISTER_PROXY_CREATE');
		$view->sitename = $this->jconfig->getValue('config.sitename');
		$view->target_juser = $target_juser;
		$view->target_xprofile = $target_xprofile;
		$view->xprofile = $xprofile;
		$view->live_site = $this->baseURL;
		if ($this->getError()) 
		{
			$view->setError($this->getError());
		}
		$view->display();
	}

	/**
	 * Short description for 'update'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
	protected function update()
	{
		ximport('Hubzero_Auth_Link');
		
		$app = JFactory::getApplication();
		
		$force = false;
		$updateEmail = false;

		// Add the CSS to the template
		$this->_getStyles();

		// Add some Javascript to the template
		$this->_getScripts();

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Check if the user is logged in
		if ($this->juser->get('guest')) 
		{
			$view = new JView(array('name'=>'error'));
			$view->title = JText::_('COM_REGISTER_UPDATE');
			$view->setError(JText::_('COM_REGISTER_ERROR_SESSION_EXPIRED'));
			$view->display();
			return false;
		}

		// Instantiate a new registration object
		$xregistration = new Hubzero_Registration();

		$xprofile    =& Hubzero_Factory::getProfile();
		$jsession =& JFactory::getSession();

		$hzal = Hubzero_Auth_Link::find_by_id($this->juser->get('auth_link_id'));

		if (JRequest::getMethod() == 'POST') 
		{
			// Load POSTed data
			$xregistration->loadPOST();
		} 
		else 
		{
			// Load data from the user object
			if (is_object($xprofile))
			{
				$xregistration->loadProfile($xprofile);
			}
			else 
			{
				$xregistration->loadAccount($this->juser);
			}

			ximport('Hubzero_Auth_Link');
			$username = $this->juser->get('username');
			$email = $this->juser->get('email');

			if ($username[0] == '-' && is_object($hzal)) 
			{
				$tmp_username = JFactory::getSession()->get('auth_link.tmp_username', '');
				$xregistration->set('login',$tmp_username);
				$xregistration->set('email',$hzal->email);
				$xregistration->set('confirmEmail',$hzal->email);
				$force = true;
			}
		}

		$check = $xregistration->check('update');

		if (!$force && $check && JRequest::getMethod() == 'GET') 
		{
			$jsession->set('registration.incomplete', false);
			if ($_SERVER['REQUEST_URI'] == '/register/update')
			{
				$app->redirect('/', '', 'message', true);
			}
			else
			{
				$app->redirect($_SERVER['REQUEST_URI'], '', 'message', true);
			}
			return(true);
		}

		if (!$force && $check && JRequest::getMethod() == 'POST') 
		{
			$params =& JComponentHelper::getParams('com_members');
			$hubHomeDir = rtrim($params->get('homedir'),'/');

			$updateEmail     = false;

			if ($xprofile->get('homeDirectory') == '') 
			{
				$xprofile->set('homeDirectory', $hubHomeDir . '/' . $xprofile->get('username'));
			}

			if ($xprofile->get('jobsAllowed') == '') 
			{
				$xprofile->set('jobsAllowed', 3);
			}

			if ($xprofile->get('regIP') == '') 
			{
				$xprofile->set('regIP', JRequest::getVar('REMOTE_ADDR','','server'));
			}

			if ($xprofile->get('regHost') == '') 
			{
				if (isset($_SERVER['REMOTE_HOST'])) 
				{
				$xprofile->set('regHost', JRequest::getVar('REMOTE_HOST','','server'));
				}
			}

			if ($xprofile->get('registerDate') == '') 
			{
				$xprofile->set('registerDate', date('Y-m-d H:i:s'));
			}

			if ($xregistration->get('email') != $xprofile->get('email')) 
			{
				if (is_object($hzal) && $xregistration->get('email') == $hzal->email) 
				{
					$xprofile->set('emailConfirmed',3);
				}
				else 
				{
					$xprofile->set('emailConfirmed', -rand(1, pow(2, 31)-1));
					$updateEmail = true;
				}
			}

			if ($xregistration->get('login') != $xprofile->get('username')) 
			{
				$xprofile->set('homeDirectory', $hubHomeDir . '/' . $xregistration->get('login'));
			}

			$xprofile->loadRegistration($xregistration);

			$xprofile->update();

			// Update juser table
			// TODO: only update if changed
			$myjuser = JUser::getInstance($xprofile->get('uidNumber'));
			$myjuser->set('username', $xprofile->get('username'));
			$myjuser->set('email', $xprofile->get('email'));
			$myjuser->set('name', $xprofile->get('name'));
			$myjuser->save();

			// Update current session if appropriate
			// TODO: update all session of this user
			// TODO: only update if changed
			if ($myjuser->get('id') == $this->juser->get('id')) 
			{
				$sjuser = $jsession->get('user');
				$sjuser->set('username', $xprofile->get('username'));
				$sjuser->set('email', $xprofile->get('email'));
				$sjuser->set('name', $xprofile->get('name'));
				$jsession->set('user', $sjuser);

				// Get the session object
				$table = & JTable::getInstance('session');
				$table->load($jsession->getId());
				$table->username = $xprofile->get('username');
				$table->update();
			}

			$jsession->set('registration.incomplete', false);

			// Notify the user
			if ($updateEmail) 
			{
				$subject  = $this->jconfig->getValue('config.sitename') .' '.JText::_('COM_REGISTER_EMAIL_CONFIRMATION');

				$eview = new JView(array('name'=>'emails','layout'=>'update'));
				$eview->option = $this->_option;
				$eview->sitename = $this->jconfig->getValue('config.sitename');
				$eview->xprofile = $xprofile;
				$eview->baseURL = $this->baseURL;
				$message = $eview->loadTemplate();
				$message = str_replace("\n", "\r\n", $message);

				if (!Hubzero_Toolbox::send_email($xprofile->get('email'), $subject, $message)) 
				{
					$this->setError(JText::sprintf('COM_REGISTER_ERROR_EMAILING_CONFIRMATION'/*,$hubMonitorEmail*/));
					// @FIXME: LOG ERROR SOMEWHERE
				}
			}

			// Notify administration
			if (JRequest::getMethod() == 'POST') 
			{
				$subject = $this->jconfig->getValue('config.sitename') .' '.JText::_('COM_REGISTER_EMAIL_ACCOUNT_UPDATE');

				$eaview = new JView(array('name'=>'emails','layout'=>'adminupdate'));
				$eaview->option = $this->_option;
				$eaview->sitename = $this->jconfig->getValue('config.sitename');
				$eaview->xprofile  = $xprofile;
				$eaview->baseURL = $this->baseURL;
				$message = $eaview->loadTemplate();
				$message = str_replace("\n", "\r\n", $message);

				// Hubzero_Toolbox::send_email($hubMonitorEmail, $subject, $message);
				// @FIXME: LOG ACCOUNT UPDATE ACTIVITY SOMEWHERE
			}

			if (!$updateEmail) 
			{
				$suri = JRequest::getVar('REQUEST_URI','/','server');
				if ($suri == '/register/update')
				{
					$app->redirect('/members/myaccount','','message',true);
				}
				else
				{
					$app->redirect($suri,'','message',true);
				}
			} 
			else 
			{
				// Instantiate a new view
				$view = new JView(array('name'=>'update'));
				$view->option = $this->_option;
				$view->title = JText::_('COM_REGISTER_UPDATE');
				$view->sitename = $this->jconfig->getValue('config.sitename');
				$view->xprofile = $xprofile;
				$view->self = true;
				$view->updateEmail = $updateEmail;
				if ($this->getError()) 
				{
					$view->setError($this->getError());
				}
				$view->display();
			}

			return true;
		}

		return $this->_show_registration_form($xregistration, 'update');
	}

	/**
	 * Short description for 'create'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
	protected function create()
	{
		ximport('Hubzero_Auth_Link');

		// Add the CSS to the template
		$this->_getStyles();

		// Add some Javascript to the template
		$this->_getScripts();

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		if (!$this->juser->get('guest') && !$this->juser->get('tmp_user')) 
		{
			$app =& JFactory::getApplication();
			$app->redirect("/members/myaccount", JText::_('COM_REGISTER_ERROR_NONGUEST_SESSION_CREATION'), "warning");
			return;
		}

		if ($this->juser->get('auth_link_id')) 
		{
			$hzal = Hubzero_Auth_Link::find_by_id($this->juser->get('auth_link_id'));
		}
		else 
		{
			$hzal = null;
		}

		// Instantiate a new registration object
		$xregistration = new Hubzero_Registration();

		if (JRequest::getMethod() == 'POST') 
		{
			// Check for request forgeries
			JRequest::checkToken() or jexit('Invalid Token');

			// Load POSTed data
			$xregistration->loadPost();

			// Perform field validation
			if ($xregistration->check('create')) 
			{
				// Get required system objects
				$user 		= clone(JFactory::getUser());
				$pathway 	=& JFactory::getApplication()->getPathway();
				$config		=& JFactory::getConfig();
				$authorize	=& JFactory::getACL();
				$document   =& JFactory::getDocument();

				// If user registration is not allowed, show 403 not authorized.
				$usersConfig = &JComponentHelper::getParams('com_users');
				if ($usersConfig->get('allowUserRegistration') == '0') 
				{
					JError::raiseError(403, JText::_('Access Forbidden'));
					return;
				}

				// Initialize new usertype setting
				$newUsertype = $usersConfig->get('new_usertype');
				if (!$newUsertype) 
				{
					$newUsertype = 'Registered';
				}

				$user->set('username', $xregistration->get('login'));
				$user->set('name', $xregistration->get('name'));
				$user->set('email', $xregistration->get('email'));
				/*
				// Bind the post array to the user object
				if (!$user->bind(JRequest::get('post'), 'usertype')) {
					JError::raiseError(500, $user->getError());
				}
				*/

				// Set some initial user values
				$user->set('id', 0);
				if (version_compare(JVERSION, '1.6', 'lt'))
				{
					$user->set('usertype', $newUsertype);
					$user->set('gid', $authorize->get_group_id('', $newUsertype, 'ARO'));
				}
				else
				{
					// There's got to be a better way to do this!
					$db = JFactory::getDbo();
					$query = $db->getQuery(true)
						->select('id')
						->from('#__usergroups')
						->where('title = ' . $db->quote(trim($newUsertype)));
					$db->setQuery($query);
					$result = $db->loadResult();

					$user->set('groups', array($result));
				}

				$date =& JFactory::getDate();
				$user->set('registerDate', $date->toMySQL());

				/*
				// If user activation is turned on, we need to set the activation information
				$useractivation = $usersConfig->get('useractivation');
				if ($useractivation == '1')
				{
					jimport('joomla.user.helper');
					$user->set('activation', JUtility::getHash(JUserHelper::genRandomPassword()));
					$user->set('block', '1');
				}
				*/

				// If there was an error with registration, set the message and display form
				if ($user->save()) 
				{
					/*
					// Send registration confirmation mail
					$password = JRequest::getString('password', '', 'post', JREQUEST_ALLOWRAW);
					$password = preg_replace('/[\x00-\x1F\x7F]/', '', $password); //Disallow control chars in the email
					UserController::_sendMail($user, $password);
			
					// Everything went fine, set relevant message depending upon user activation state and display message
					if ($useractivation == 1) {
						$message  = JText::_('REG_COMPLETE_ACTIVATE');
					} else {
						$message = JText::_('REG_COMPLETE');
					}
			
					$this->setRedirect('index.php', $message);
					*/

					// Get some settings
					$params =& JComponentHelper::getParams('com_members');
					$hubHomeDir = rtrim($params->get('homedir'), '/');
					
					// Attempt to get the new user
					$xprofile = Hubzero_User_Profile::getInstance($user->get('id'));

					$result = is_object($xprofile);
					
					// Did we successfully create an account?
					if ($result) 
					{
						$xprofile->loadRegistration($xregistration);

						if (is_object($hzal)) 
						{
							if ($xprofile->get('email') == $hzal->email) 
							{
								$xprofile->set('emailConfirmed', 3);
							}
							else 
							{
								$xprofile->set('emailConfirmed', -rand(1, pow(2, 31)-1));
							}
						}

						$xprofile->set('public', 0);

						// Do we have a return URL?
						$regReturn = JRequest::getVar('return', '');
						
						if ($regReturn) 
						{
							$xprofile->setParam('return', $regReturn);
						}

						// Unset password here so that change password below can be in charge of setting it initially
						$xprofile->set('password', '');
						$result = $xprofile->update();
					}

					if ($result) 
					{
						$result = Hubzero_User_Password::changePassword($xprofile->get('uidNumber'), $xregistration->get('password'));
						// Set password back here in case anything else down the line is looking for it
						$xprofile->set('password', $xregistration->get('password'));
					}
					
					// Did we successfully create/update an account?
					if (!$result) 
					{
						$view = new JView(array('name'=>'error'));
						$view->title = JText::_('COM_REGISTER_CREATE_ACCOUNT');
						$view->setError(JText::sprintf('COM_REGISTER_ERROR_CREATING_ACCOUNT'/*, $hubMonitorEmail*/));
						// @FIXME: LOG ERROR SOMEWHERE
						$view->display();
						return;
					}

					if ($xprofile->get('emailConfirmed') < 0) 
					{
						// Notify the user
						$subject  = $this->jconfig->getValue('config.sitename').' '.JText::_('COM_REGISTER_EMAIL_CONFIRMATION');

						$eview = new JView(array('name'=>'emails','layout'=>'create'));
						$eview->option = $this->_option;
						$eview->sitename = $this->jconfig->getValue('config.sitename');
						$eview->xprofile = $xprofile;
						$eview->baseURL = $this->baseURL;
						$eview->xregistration = $xregistration;
						$message = $eview->loadTemplate();
						$message = str_replace("\n", "\r\n", $message);

						$fullEmailAddress = $xprofile->get('name') . " <" . $xprofile->get('email') . ">";

						if (!Hubzero_Toolbox::send_email($fullEmailAddress, $subject, $message)) 
						{
							$this->setError(JText::sprintf('COM_REGISTER_ERROR_EMAILING_CONFIRMATION'/*, $hubMonitorEmail*/));
							// @FIXME: LOG ERROR SOMEWHERE
						}
					}

					// Notify administration
					$subject = $this->jconfig->getValue('config.sitename') .' '.JText::_('COM_REGISTER_EMAIL_ACCOUNT_CREATION');

					$eaview = new JView(array('name'=>'emails','layout'=>'admincreate'));
					$eaview->option = $this->_option;
					$eaview->sitename= $this->jconfig->getValue('config.sitename');
					$eaview->xprofile = $xprofile;
					$eaview->baseURL = $this->baseURL;
					$message = $eaview->loadTemplate();
					$message = str_replace("\n", "\r\n", $message);

					// Hubzero_Toolbox::send_email($hubMonitorEmail, $subject, $message);
					// @FIXME: LOG ACCOUNT CREATION ACTIVITY SOMEWHERE

					// Instantiate a new view
					$view = new JView(array('name'=>'create'));
					$view->option = $this->_option;
					$view->title = JText::_('COM_REGISTER_CREATE_ACCOUNT');
					$view->sitename = $this->jconfig->getValue('config.sitename');
					$view->xprofile = $xprofile;
					
					if ($this->getError()) 
					{
						$view->setError($this->getError());
					}
					
					$view->display();

					if (is_object($hzal)) 
					{
						$hzal->user_id = $user->get('id');
						
						if ($hzal->user_id > 0) 
						{
							$hzal->update();
						}
					}

					$this->juser->set('auth_link_id',null);
					$this->juser->set('tmp_user',null);
					$this->juser->set('username', $xregistration->get('login'));
					$this->juser->set('email', $xregistration->get('email'));
					$this->juser->set('id', $user->get('id'));
					
					return;
				}
			}
		}

		if (JRequest::getMethod() == 'GET') 
		{
			if ($this->juser->get('tmp_user')) 
			{
				$xregistration->loadAccount($this->juser);

				$username = $xregistration->get('login');
				$email = $xregistration->get('email');
				if (is_object($hzal)) 
				{
					$xregistration->set('login', $hzal->username);
					$xregistration->set('email', $hzal->email);
					$xregistration->set('confirmEmail', $hzal->email);
				}
			}
		}

		return $this->_show_registration_form($xregistration, 'create');
	}

	/**
	 * Short description for 'raceethnic'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function raceethnic()
	{
		// Add the CSS to the template
		$this->_getStyles();

		// Add some Javascript to the template
		$this->_getScripts();

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Instantiate a new view
		$view = new JView(array('name'=>'registration', 'layout'=>'raceethnic'));
		$view->option = $this->_option;
		$view->title = JText::_('COM_REGISTER_SELECT_METHOD');
		$view->sitename = $this->jconfig->getValue('config.sitename');
		if ($this->getError()) 
		{
			$view->setError($this->getError());
		}
		$view->display();
	}

	/**
	 * Short description for '_registrationField'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $name Parameter description (if any) ...
	 * @param      unknown $default Parameter description (if any) ...
	 * @param      string $task Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	private function _registrationField($name, $default, $task='create')
	{
		switch ($task)
		{
			case 'register':
			case 'create': $index = 0; break;
			case 'proxy':  $index = 1; break;
			case 'proxycreate':  $index = 1; break;
			case 'update': $index = 2; break;
			case 'edit':   $index = 3; break;
			default:       $index = 0; break;
		}

		$hconfig =& JComponentHelper::getParams('com_register');

		$default = str_pad($default, 4, '-');
		$configured = $hconfig->get($name);
		if (empty($configured)) 
		{
			$configured = $default;
		}
		$length = strlen($configured);
		if ($length > $index) 
		{
			$value = substr($configured, $index, 1);
		} 
		else 
		{
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

	/**
	 * Short description for '_show_registration_form'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$xregistration Parameter description (if any) ...
	 * @param      string $task Parameter description (if any) ...
	 * @return     void
	 */
	private function _show_registration_form(&$xregistration=null, $task='create')
	{
		// Instantiate a new view
		$view = new JView(array('name'=>'registration'));
		$view->option = $this->_option;
		$view->task = $task;
		$view->title = JText::_('COM_REGISTER');
		$view->sitename = $this->jconfig->getValue('config.sitename');

		$username = JRequest::getVar('username',$this->juser->get('username'),'get');
		$view->self = ($this->juser->get('username') == $username);

		// Get the registration object
		if (!is_object($xregistration)) 
		{
			$view->xregistration = new Hubzero_Registration();
		} 
		else 
		{
			$view->xregistration = $xregistration;
		}

		// Push some values to the view

		ximport('Hubzero_Password_Rule');
		$password_rules = Hubzero_Password_Rule::getRules();

		$view->password_rules = array();

		foreach ($password_rules as $rule) 
		{
			if (!empty($rule['description'])) 
			{
				$view->password_rules[] = $rule['description'];
			}
		}

		$view->showMissing = true;
		$view->registration = $view->xregistration->_registration;
		$view->registrationUsername = $this->_registrationField('registrationUsername','RROO',$task);
		$view->registrationPassword = $this->_registrationField('registrationPassword','RRHH',$task);
		$view->registrationConfirmPassword = $this->_registrationField('registrationConfirmPassword','RRHH',$task);
		$view->registrationFullname = $this->_registrationField('registrationFullname','RRRR',$task);
		$view->registrationEmail = $this->_registrationField('registrationEmail','RRRR',$task);
		$view->registrationConfirmEmail = $this->_registrationField('registrationConfirmEmail','RRRR',$task);
		$view->registrationURL = $this->_registrationField('registrationURL','HHHH',$task);
		$view->registrationPhone = $this->_registrationField('registrationPhone','HHHH',$task);
		$view->registrationEmployment = $this->_registrationField('registrationEmployment','HHHH',$task);
		$view->registrationOrganization = $this->_registrationField('registrationOrganization','HHHH',$task);
		$view->registrationCitizenship = $this->_registrationField('registrationCitizenship','HHHH',$task);
		$view->registrationResidency = $this->_registrationField('registrationResidency','HHHH',$task);
		$view->registrationSex = $this->_registrationField('registrationSex','HHHH',$task);
		$view->registrationDisability = $this->_registrationField('registrationDisability','HHHH',$task);
		$view->registrationHispanic = $this->_registrationField('registrationHispanic','HHHH',$task);
		$view->registrationRace = $this->_registrationField('registrationRace','HHHH',$task);
		$view->registrationInterests = $this->_registrationField('registrationInterests','HHHH',$task);
		$view->registrationReason = $this->_registrationField('registrationReason','HHHH',$task);
		$view->registrationOptIn = $this->_registrationField('registrationOptIn','HHHH',$task);
		$view->registrationCAPTCHA = $this->_registrationField('registrationCAPTCHA','HHHH',$task);
		$view->registrationTOU = $this->_registrationField('registrationTOU','HHHH',$task);

		if ($view->task == 'update') 
		{
			if (empty($view->xregistration->login)) 
			{
				$view->registrationUsername = REG_REQUIRED;
			} 
			else 
			{
				$view->registrationUsername = REG_READONLY;
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

		if ($this->juser->get('auth_link_id') && $view->task == 'create') 
		{
			$view->registrationPassword = REG_HIDE;
			$view->registrationConfirmPassword = REG_HIDE;
		}

		/*
		if ($view->registrationEmail == REG_REQUIRED || $view->registrationEmail == REG_OPTIONAL) {
			if (!empty($view->xregistration->email)) {
				$view->registration['email'] = $view->xregistration->_encoded['email'];
			}
		}

		if ($view->registrationConfirmEmail == REG_REQUIRED || $view->registrationConfirmEmail == REG_OPTIONAL) {
			if (!empty($view->xregistration->_encoded['email'])) {
				$view->registration['confirmEmail'] = $view->xregistration->_encoded['email']; 
			}
		}
		*/

		// Display the view
		if ($this->getError()) 
		{
			$view->setError($this->getError());
		}

		$view->display();
	}

	/**
	 * Check the strength of a password
	 * 
	 * @param      unknown $act Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	protected function passwordstrength($act)
	{
		// Incoming
		$no_html = JRequest::getInt('no_html',0);
		$password = JRequest::getVar('pass','','post');
		$username = JRequest::getVar('user','','post');

		// Instantiate a new registration object
		$xregistration = new Hubzero_Registration();

		// Score the password
		$score = $xregistration->scorePassword($password, $username);

		// Determine strength
		if ($score < PASS_SCORE_MEDIOCRE) 
		{
			$cls = 'bad';
			$txt = JText::_('COM_REGISTER_PASS_BAD');
		} 
		else if ($score >= PASS_SCORE_MEDIOCRE && $score < PASS_SCORE_GOOD) 
		{
			$cls = 'mediocre';
			$txt = JText::_('COM_REGISTER_PASS_MEDIOCRE');
		} 
		else if ($score >= PASS_SCORE_GOOD && $score < PASS_SCORE_STRONG) 
		{
			$cls = 'good';
			$txt = JText::_('COM_REGISTER_PASS_GOOD');
		} 
		else if ($score >= PASS_SCORE_STRONG) 
		{
			$cls = 'strong';
			$txt = JText::_('COM_REGISTER_PASS_STRONG');
		}

		// Build the HTML
		$html = '<span id="passwd-meter" style="width:' . $score . '%;" class="' . $cls . '"><span>' . JText::_($txt) . '</span></span>';

		// Return the HTML
		if ($no_html) 
		{
			echo $html;
		} 
		else 
		{
			return $html;
		}
	}
	
	/**
	 * Short description for 'checkusername'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string 
	 */
	protected function checkusername()
	{
		// Incoming
		$username = JRequest::getVar('userlogin', '', 'get');

		// Instantiate a new registration object
		$xregistration = new Hubzero_Registration();

		// Check the username
		$usernamechecked = $xregistration->checkusername($username);

		$ret = json_encode($usernamechecked);
		echo $ret;
		die;
	}

	/**
	 * Resend Email (account confirmation)
	 * 
	 * @return     void
	 */
	protected function resend()
	{
		// Add the CSS to the template
		$this->_getStyles();

		// Add some Javascript to the template
		$this->_getScripts();

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Check if the user is logged in
		if ($this->juser->get('guest')) 
		{
			// Instantiate a new view
			$view = new JView(array('name'=>'login'));
			$view->option = $this->_option;
			$view->title = JText::_('COM_REGISTER_RESEND');
			$view->setError(JText::_('COM_REGISTER_ERROR_LOGIN_TO_RESEND'));
			$view->display();
			return;
		}

		$xprofile =& Hubzero_Factory::getProfile();
		$login = $xprofile->get('username');
		$email = $xprofile->get('email');
		$email_confirmed = $xprofile->get('emailConfirmed');

		// Incoming
		$return = urldecode(JRequest::getVar('return', '/'));

		if (($email_confirmed != 1) && ($email_confirmed != 3)) 
		{
			$confirm = Hubzero_Registration_Helper::genemailconfirm();

			ximport('Hubzero_User_Profile');
			$xprofile = new Hubzero_User_Profile();
			$xprofile->load($login);
			$xprofile->set('emailConfirmed', $confirm);
			$xprofile->update();

			$subject  = $this->jconfig->getValue('config.sitename').' '.JText::_('COM_REGISTER_EMAIL_CONFIRMATION');

			$eview = new JView(array('name'=>'emails','layout'=>'confirm'));
			$eview->option = $this->_option;
			$eview->sitename = $this->jconfig->getValue('config.sitename');
			$eview->login = $login;
			$eview->baseURL = $this->baseURL;
			$eview->confirm = $confirm;
			$message = $eview->loadTemplate();
			$message = str_replace("\n", "\r\n", $message);

			if (!Hubzero_Toolbox::send_email($email, $subject, $message)) 
			{
				$this->setError(JText::sprintf('COM_REGISTER_ERROR_EMAILING_CONFIRMATION', $email));
			}

			// Instantiate a new view
			$view = new JView(array('name'=>'send'));
			$view->option = $this->_option;
			$view->title = JText::_('COM_REGISTER_RESEND');
			$view->login = $login;
			$view->email = $email;
			$view->return = $return;
			$view->show_correction_faq = true;
			$view->hubName = $this->jconfig->getValue('config.sitename');
			if ($this->getError()) 
			{
				$view->setError($this->getError());
			}
			$view->display();
		} 
		else 
		{
			header("Location: " . urlencode($return));
		}
	}

	/**
	 * Change registered email
	 * 
	 * @return     void
	 */
	protected function change()
	{
		$app = JFactory::getApplication();

		// Add the CSS to the template
		$this->_getStyles();

		// Add some Javascript to the template
		$this->_getScripts();

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Check if the user is logged in
		if ($this->juser->get('guest')) 
		{
			// Instantiate a new view
			$view = new JView(array('name' => 'login'));
			$view->option = $this->_option;
			$view->title = JText::_('COM_REGISTER_CHANGE');
			$view->setError(JText::_('COM_REGISTER_ERROR_LOGIN_TO_UPDATE'));
			$view->display();
			return;
		}

		$xprofile =& Hubzero_Factory::getProfile();
		$login = $xprofile->get('username');
		$email = $xprofile->get('email');
		$email_confirmed = $xprofile->get('emailConfirmed');

		// Instantiate a new view
		$view = new JView(array('name' => 'change'));
		$view->option = $this->_option;
		$view->title = JText::_('COM_REGISTER_CHANGE');
		$view->login = $login;
		$view->email = $email;
		$view->email_confirmed = $email_confirmed;
		$view->success = false;

		// Incoming
		$return = urldecode(JRequest::getVar('return', '/'));

		$view->return = $return;

		// Check if a new email was submitted
		$pemail = JRequest::getVar('email', '', 'post');
		$update = JRequest::getVar('update', '', 'post');

		if ($update) 
		{
			if (!$pemail) 
			{
				$this->setError(JText::_('COM_REGISTER_ERROR_INVALID_EMAIL'));
			}
			if ($pemail && Hubzero_Registration_Helper::validemail($pemail) /*&& ($newemail != $email)*/) 
			{
				// Check if the email address was actually changed
				if ($pemail == $email) 
				{
					// Addresses are the same! Redirect
					$app->redirect($return,'','message',true);
				} 
				else 
				{
					// New email submitted - attempt to save it
					$xprofile =& Hubzero_User_Profile::getInstance($login);
					if ($xprofile) 
					{
						$dtmodify = date("Y-m-d H:i:s");
						$xprofile->set('email',$pemail);
						$xprofile->set('modifiedDate',$dtmodify);
						if ($xprofile->update()) 
						{
							$juser =& JUser::getInstance($login);
							$juser->set('email', $pemail);
							$juser->save();
						} 
						else 
						{
							$this->setError(JText::_('COM_REGISTER_ERROR_UPDATING_ACCOUNT'));
						}
					} 
					else 
					{
						$this->setError(JText::_('COM_REGISTER_ERROR_UPDATING_ACCOUNT'));
					}

					// Any errors returned?
					if (!$this->getError()) 
					{
						// No errors
						// Attempt to send a new confirmation code
						$confirm = Hubzero_Registration_Helper::genemailconfirm();

						ximport('Hubzero_User_Profile');
						$xprofile = new Hubzero_User_Profile();
						$xprofile->load($login);
						$xprofile->set('emailConfirmed', $confirm);
						$xprofile->update();

						$subject  = $this->jconfig->getValue('config.sitename').' '.JText::_('COM_REGISTER_EMAIL_CONFIRMATION');

						$eview = new JView(array('name'=>'emails','layout'=>'confirm'));
						$eview->option = $this->_option;
						$eview->sitename = $this->jconfig->getValue('config.sitename');
						$eview->login = $login;
						$eview->baseURL = $this->baseURL;
						$eview->confirm = $confirm;
						$message = $eview->loadTemplate();
						$message = str_replace("\n", "\r\n", $message);

						if (!Hubzero_Toolbox::send_email($pemail, $subject, $message)) 
						{
							$this->setError(JText::sprintf('COM_REGISTER_ERROR_EMAILING_CONFIRMATION', $pemail));
						}

						// Show the success form
						$view->success = true;
					}
				}
			} 
			else 
			{
				$this->setError(JText::_('COM_REGISTER_ERROR_INVALID_EMAIL'));
			}
		}

		// Output the view
		if ($this->getError()) 
		{
			$view->email = $pemail;
			$view->setError($this->getError());
		}
		$view->display();
	}

	/**
	 * Conform user's registration code
	 * 
	 * @return     void
	 */
	protected function confirm()
	{
		$app = JFactory::getApplication();

		// Add the CSS to the template
		$this->_getStyles();

		// Add some Javascript to the template
		$this->_getScripts();

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Incoming
		$code = JRequest::getVar('confirm', false);
		if (!$code) 
		{
			$code = JRequest::getVar('code', false);
		}

		// Check if the user is logged in
		if ($this->juser->get('guest')) 
		{
			//send to login form with return to confirm
			$return = base64_encode(JRoute::_('index.php?option=' . $this->option . '&task=' . $this->_task . '&confirm=' . $code));
			$this->_message = "Please login in so we can confirm your account.";
			$this->_messageType = "Warning";
			$this->_redirect = JRoute::_('index.php?option=com_login&return=' . $return);
			return;
		}

		$xprofile =& Hubzero_Factory::getProfile();

		$email_confirmed = $xprofile->get('emailConfirmed');

		if (($email_confirmed == 1) || ($email_confirmed == 3)) 
		{
			// All is well
		} 
		elseif ($email_confirmed < 0 && $email_confirmed == -$code) 
		{
			ximport('Hubzero_User_Profile');
			$profile = new Hubzero_User_Profile();
			$profile->load($xprofile->get('username'));

			$myreturn = base64_decode($profile->getParam('return'));
			if ($myreturn) 
			{
				$profile->setParam('return','');
			}
			$profile->set('emailConfirmed', 1);

			$mconfig =& JComponentHelper::getParams('com_members');
			$profile->set('public', $mconfig->get('privacy', '0'));

			if (!$profile->update()) 
			{
				$this->setError(JText::_('COM_REGISTER_ERROR_CONFIRMING'));
			}

			$hconfig = &JComponentHelper::getParams('com_register');

			// Override any other return settings if $return is explicitly set
			$return = $hconfig->get('ConfirmationReturn');
			if ($return) 
			{
				$myreturn = $return;
			}

			// Redirect
			if (empty($myreturn)) 
			{
				$r = $hconfig->get('LoginReturn');
				$myreturn = ($r) ? $r : JRoute::_('index.php?option=com_members&task=myaccount');
				// consume cookie (yum) if available to return to whatever action prompted registration
				if (isset($_COOKIE['return'])) {
					$myreturn = $_COOKIE['return'];
					setcookie('return', '', time() - 3600);
				}
			}

			$app->redirect($myreturn,'','message',true);
		} 
		else 
		{
			$this->setError(JText::_('COM_REGISTER_ERROR_INVALID_CONFIRMATION'));
		}

		// Instantiate a new view
		$view = new JView(array('name' => 'confirm'));
		$view->option   = $this->_option;
		$view->title    = JText::_('COM_REGISTER_CONFIRM');
		$view->login    = $xprofile->get('username');
		$view->email    = $xprofile->get('email');
		$view->code     = $code;
		$view->sitename = $this->jconfig->getValue('config.sitename');
		if ($this->getError()) 
		{
			$view->setError($this->getError());
		}
		$view->display();
	}

	/**
	 * Show a "registration unconfirmed" message
	 * 
	 * @return     void
	 */
	protected function unconfirmed()
	{
		$xprofile =& Hubzero_Factory::getProfile();
		$email_confirmed = $xprofile->get('emailConfirmed');

		// Incoming
		$return = JRequest::getVar('return', urlencode('/'));

		// Check if the email has been confirmed
		if (($email_confirmed != 1) && ($email_confirmed != 3)) 
		{
			// Add the CSS to the template
			$this->_getStyles();

			// Add some Javascript to the template
			$this->_getScripts();

			// Set the pathway
			$this->_buildPathway();

			// Set the page title
			$this->_buildTitle();

			// Check if the user is logged in
			if ($this->juser->get('guest')) 
			{
				// Instantiate a new view
				$view = new JView(array('name' => 'login'));
				$view->option = $this->_option;
				$view->title = JText::_('COM_REGISTER_CONFIRM');
				$view->setError(JText::_('COM_REGISTER_ERROR_LOGIN_TO_CONFIRM'));
				$view->display();
				return;
			}

			// Instantiate a new view
			$view = new JView(array('name' => 'unconfirmed'));
			$view->option   = $this->_option;
			$view->title    = JText::_('COM_REGISTER_UNCONFIRMED');
			$view->email    = $xprofile->get('email');
			$view->return   = $return;
			$view->sitename = $this->jconfig->getValue('config.sitename');
			if ($this->getError()) 
			{
				$view->setError($this->getError());
			}
			$view->display();
		} 
		else 
		{
			header("Location: " . urldecode($return));
		}
	}

	/**
	 * Build pathway (breadcrumbs)
	 * 
	 * @return     void
	 */
	protected function _buildPathway()
	{
		$pathway =& JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_('COM_REGISTER'),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task) 
		{
			$pathway->addItem(
				JText::_('COM_REGISTER_' . strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
		}
	}

	/**
	 * Set the document title
	 * 
	 * @return     void
	 */
	protected function _buildTitle()
	{
		if ($this->_task) 
		{
			$title = JText::_('COM_REGISTER_' . strtoupper($this->_task));
		} 
		else 
		{
			$title = JText::_('COM_REGISTER');
		}
		$document =& JFactory::getDocument();
		$document->setTitle($title);
	}

	/**
	 * Determine if cookies are enabled
	 * 
	 * @return     boolean True if cookies are enabled
	 */
	private function _cookie_check()
	{
		$app = JFactory::getApplication();
		$jsession =& JFactory::getSession();
		$jcookie = $jsession->getName();

		if (!isset($_COOKIE[$jcookie])) 
		{
			if (JRequest::getVar('cookie', '', 'get') != 'no') 
			{
				$juri = JURI::getInstance();
				$juri->setVar('cookie', 'no');
				return $app->redirect($juri->toString(),'','message',true);
			}

			$view = new JView(array('name' => 'error'));
			$view->title = JText::_('COM_REGISTER');
			$view->setError(JText::_('COM_REGISTER_ERROR_COOKIES'));
			$view->display();

			return false;
		} 
		else if (JRequest::getVar('cookie', '', 'get') == 'no') 
		{
			$juri = JURI::getInstance();
			$juri->delVar('cookie');

			return $app->redirect($juri->toString(),'','message',true);
		}

		return true;
	}
}

