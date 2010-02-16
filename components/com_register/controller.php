<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

class RegisterController extends JObject
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
		
		// Set the controller name
		if (empty( $this->_name )) {
			if (isset($config['name'])) {
				$this->_name = $config['name'];
			} else {
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}
		
		// Set the component name
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
	
	public function execute()
	{
		$this->database = JFactory::getDBO();
		$this->jconfig = JFactory::getConfig();
		
		$juri =& JURI::getInstance();
		$this->baseURL = $juri->base();
		if (substr($this->baseURL,-1) == '/') {
			$this->baseURL = substr($this->baseURL,0, -1);
		}

		$this->_task = strtolower(JRequest::getVar( 'task', '' ));
		$act = strtolower(JRequest::getVar( 'act', '', 'post' ));

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
			
			// Account confirmation
			case 'resend':      $this->resend();      break;
			case 'change':      $this->change();      break;
			case 'confirm':     $this->confirm();     break;
			case 'unconfirmed': $this->unconfirmed(); break;
			
			//case 'login': $this->login($act); break;
			//case 'realm': $this->realm($act); break;
			
			default: $this->select(); break;
		}
	}
	
	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message, $this->_messageType );
		}
	}
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function edit()
	{
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return JError::raiseError(500, JText::_('COM_REGISTER_ERROR_GUEST_SESSION_EDITING'));
		}

		$xuser =& XFactory::getUser();
		$xhub  =& XFactory::getHub();
		$jsession =& JFactory::getSession();
		
		// Get the return URL
		$return = base64_decode( JRequest::getVar('return', '',  'method', 'base64') );
		if (!$return) {
			$return = $jsession->get('session.return');

			if (!$return) {
				$return = '/';
			}
		}

		$username = JRequest::getVar('username',$xuser->get('login'),'get');

		$target_xuser = XUser::getInstance($username);

		$admin = $juser->authorize($this->_option, 'manage');
		$self = ($xuser->get('login') == $username);
		
		if (!$admin && !$self) {
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
		$xregistration = new XRegistration();
		
		if (JRequest::getVar('edit', '', 'post')) {
			// Load POSTed data
			$xregistration->loadPOST();
		} else {
			// Load data from the user object
			$xregistration->loadXUser($target_xuser);
			return $this->_show_registration_form($xregistration, 'edit');
		}

		if ($username != $xregistration->get('login')) {
			return JError::raiseError(500, JText::_('COM_REGISTER_ERROR_REGISTRATION_DATA_MISMATCH'));
		}
		
		if (!$xregistration->check('edit')) {
			return $this->_show_registration_form($xregistration, 'edit');
		}

		$target_xuser->loadRegistration($xregistration);

		$hubMonitorEmail = $xhub->getCfg('hubMonitorEmail');
		$hubHomeDir      = $xhub->getCfg('hubHomeDir');
		$updateEmail     = false;

		if ($target_xuser->get('home') == '') {
			$target_xuser->set('home', $hubHomeDir . '/' . $target_xuser->get('login'));
		}

		if ($target_xuser->get('jobs_allowed') == '') {
			$target_xuser->set('jobs_allowed', 3);
		}

		if ($target_xuser->get('reg_ip') == '') {
			$target_xuser->set('reg_ip', $_SERVER['REMOTE_ADDR']);
		}

		if ($target_xuser->get('reg_host') == '') {
			if (isset($_SERVER['REMOTE_HOST'])) {
				$target_xuser->set('reg_host', $_SERVER['REMOTE_HOST']);
			}
		}
		
		if ($target_xuser->get('reg_date') == '') {
			$target_xuser->set('reg_date', date('Y-m-d H:i:s'));
		}

		if ($xregistration->get('email') != $target_xuser->get('email')) {
			$target_xuser->set('email_confirmed', -rand(1, pow(2, 31)-1) );
			$updateEmail = true;
		}

		$target_xuser->loadRegistration($xregistration);

		$target_xuser->update();

		if ($self) {
			// Notify the user
			if ($updateEmail) {
				$subject  = $this->jconfig->getValue('config.sitename') .' '.JText::_('COM_REGISTER_EMAIL_CONFIRMATION');

				$eview = new JView( array('name'=>'emails','layout'=>'update') );
				$eview->option = $this->_option;
				$eview->hubShortName = $this->jconfig->getValue('config.sitename');
				$eview->xuser = $target_xuser;
				$eview->baseURL = $this->baseURL;
				$message = $eview->loadTemplate();
				$message = str_replace("\n", "\r\n", $message);

				if (!XHubHelper::send_email($target_xuser->get('email'), $subject, $message)) {
					$this->setError(JText::sprintf('COM_REGISTER_ERROR_EMAILING_CONFIRMATION', $hubMonitorEmail));
				}
			}

			// Notify administration
			$subject = $this->jconfig->getValue('config.sitename') .' '.JText::_('COM_REGISTER_EMAIL_ACCOUNT_UPDATE');

			$eaview = new JView( array('name'=>'emails','layout'=>'adminupdate') );
			$eaview->option = $this->_option;
			$eaview->hubShortName = $this->jconfig->getValue('config.sitename');
			$eaview->xuser = $target_xuser;
			$eaview->baseURL = $this->baseURL;
			$message = $eaview->loadTemplate();
			$message = str_replace("\n", "\r\n", $message);

			XHubHelper::send_email($hubMonitorEmail, $subject, $message);

			// Determine action based on if the user chaged their email or not
			if (!$updateEmail) {
				// Redirect
				$jsession->clear('session.return');
				$xhub->redirect($return);
			}
		} else {
			if ($updateEmail) {
				$subject  = $this->jconfig->getValue('config.sitename') .' '.JText::_('COM_REGISTER_EMAIL_CONFIRMATION');

				$eview = new JView( array('name'=>'emails','layout'=>'updateproxy') );
				$eview->option = $this->_option;
				$eview->hubShortName = $this->jconfig->getValue('config.sitename');
				$eview->xuser = $target_xuser;
				$eview->baseURL = $this->baseURL;
				$message = $eview->loadTemplate();
				$message = str_replace("\n", "\r\n", $message);

				if (!XHubHelper::send_email($target_xuser->get('email'), $subject, $message)) {
					$this->setError(JText::sprintf('COM_REGISTER_ERROR_EMAILING_CONFIRMATION', $hubMonitorEmail));
				}
			}

			// Notify administration
			$subject = $this->jconfig->getValue('config.sitename') .' '.JText::_('COM_REGISTER_EMAIL_ACCOUNT_UPDATE');

			$eaview = new JView( array('name'=>'emails','layout'=>'adminupdateproxy') );
			$eaview->option = $this->_option;
			$eaview->hubShortName = $this->jconfig->getValue('config.sitename');
			$eaview->xuser = $target_xuser;
			$eaview->baseURL = $this->baseURL;
			$message = $eaview->loadTemplate();
			$message = str_replace("\n", "\r\n", $message);

			XHubHelper::send_email($hubMonitorEmail, $subject, $message);

			// Determine action based on if the user chaged their email or not
			if (!$updateEmail) {
				// Redirect
				$jsession->clear('session.return');
				$xhub->redirect($return);
			}
		}
		
		// Instantiate a new view
		$view = new JView( array('name'=>'update') );
		$view->option = $this->_option;
		$view->title = JText::_('COM_REGISTER_UPDATE');
		$view->hubShortName = $this->jconfig->getValue('config.sitename');
		$view->xuser = $target_xuser;
		$view->self = $self;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	} 
	
	//-----------

	protected function proxycreate($action='show')
	{
		$action = ($action) ? $action : 'show';
		
		if ($action != 'submit' && $action != 'show') {
			return JError::raiseError(404, JText::_('COM_REGISTER_ERROR_INVALID_REQUEST'));
		}

		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
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
		$xregistration = new XRegistration();

		// Show the form if needed
		if ($action == 'show') {
			$username = JRequest::getVar('username','','get');

			$xregistration->set('login', $username);

			return $this->_show_registration_form($xregistration, 'proxycreate');
		}

		// Load POSTed data
		if ($action == 'submit') {
			$xregistration->loadPost();
		}

		// Perform field validation
		if (!$xregistration->check('proxy')) {
			return $this->_show_registration_form($xregistration, 'proxycreate');
		}
		
		$xuser =& XFactory::getUser(); 
		$xhub  =& XFactory::getHub();
		
		// Get some settings
		$jconfig =& JFactory::getConfig();
		$this->jconfig = $jconfig;
		//$this->baseURL      = $xhub->getCfg('hubLongURL');
		$hubMonitorEmail = $xhub->getCfg('hubMonitorEmail');
		$hubHomeDir      = $xhub->getCfg('hubHomeDir');
                
		jimport('joomla.application.component.helper');
		$config   =& JComponentHelper::getParams( 'com_users' );
		$usertype = $config->get( 'new_usertype', 'Registered' );
		
		$acl =& JFactory::getACL();
		
		// Create a new Joomla user
		$target_juser = new JUser();
		$target_juser->set('id',0);
		$target_juser->set('name', $xregistration->get('name'));
		$target_juser->set('username', $xregistration->get('login'));
		$target_juser->set('password_clear','');
		$target_juser->set('email', $xregistration->get('email'));
		$target_juser->set('gid', $acl->get_group_id( '', $usertype));
		$target_juser->set('usertype', $usertype);
		$target_juser->save();

		// Attempt to retrieve the new user
		$target_xuser = XUser::getInstance($target_juser->get('id'));
		$result = is_object($target_xuser);
		
		// Did we successully create an account?
		if ($result) {
			$target_xuser->loadRegistration($xregistration);
			$target_xuser->set('home', $hubHomeDir . '/' . $target_xuser->get('login'));
			$target_xuser->set('jobs_allowed', 3);
			$target_xuser->set('reg_ip', $_SERVER['REMOTE_ADDR']);
			$target_xuser->set('email_confirmed', -rand(1, pow(2, 31)-1) );
			if (isset($_SERVER['REMOTE_HOST'])) {
				$target_xuser->set('reg_host', $_SERVER['REMOTE_HOST']);
			}
			$target_xuser->set('password', $xregistration->get('password'));
			$target_xuser->set('reg_date', date('Y-m-d H:i:s'));
			$target_xuser->set('proxy_uid', $juser->get('id'));
			$target_xuser->set('proxy_password', $xregistration->get('password'));
			
			// Update the account
			$result = $target_xuser->update();
		}

		// Did we successully create/update an account?
		if (!$result) {
			$view = new JView( array('name'=>'error') );
			$view->title = JText::_('COM_REGISTER_PROXY_CREATE');
			$view->setError( JText::sprintf('COM_REGISTER_ERROR_CREATING_ACCOUNT', $hubMonitorEmail) );
			$view->display();
			return;
		}
		
		// Instantiate a new view
		$view = new JView( array('name'=>'proxycreate') );
		$view->option = $this->_option;
		$view->title = JText::_('COM_REGISTER_PROXY_CREATE');
		$view->hubShortName = $this->jconfig->getValue('config.sitename');
		$view->target_juser = $target_juser;
		$view->target_xuser = $target_xuser;
		$view->xuser = $xuser;
		$view->hubLongURL = $this->baseURL;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------
	  
	protected function update($action='show')
	{
		$action = ($action) ? $action : 'show';
		
		// Add the CSS to the template
		$this->_getStyles();

		// Add some Javascript to the template
		$this->_getScripts();

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();
		
		// Check if the user is logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$view = new JView( array('name'=>'error') );
			$view->title = JText::_('COM_REGISTER_UPDATE');
			$view->setError( JText::_('COM_REGISTER_ERROR_SESSION_EXPIRED') );
			$view->display();
			return false;
		}

		// Instantiate a new registration object
		$xregistration = new XRegistration();

		$xprofile    =& XFactory::getProfile(); 
		$xhub     =& XFactory::getHub();
		$jsession =& JFactory::getSession();

		// Determine action
		if ($action == 'submit') {
			// Load POSTed data
			$xregistration->loadPOST();
		} else {
			// Load data from the user object
			$xregistration->loadProfile($xprofile);
		}
		
		if (!$xregistration->check('update', $juser->get('id'))) {
			// Check submitted data
			if ($action == 'submit') {
				if ($xprofile->hasTransientUsername()) {
					$xregistration->_encoded['login'] = $xregistration->get('login');
				}
				if ($xprofile->hasTransientEmail()) {
					$xregistration->_encoded['email'] = $xregistration->get('email');
				}
			}
			
			// Display the form
			return $this->_show_registration_form($xregistration, 'update');
		}
		
		if (!$xprofile->hasTransientUsername() && $xprofile->get('username') != $xregistration->get('login')) {
			return JError::raiseError(500, JText::_('COM_REGISTER_ERROR_REGISTRATION_FORM_SESSION_MISMATCH'));
		}
		
		$hubMonitorEmail = $xhub->getCfg('hubMonitorEmail');
		$hubHomeDir      = $xhub->getCfg('hubHomeDir');
		$updateEmail     = false;
		
		if ($xprofile->get('homeDirectory') == '') {
			$xprofile->set('homeDirectory', $hubHomeDir . '/' . $xprofile->get('username'));
		}
		
		if ($xprofile->get('jobsAllowed') == '') {
			$xprofile->set('jobsAllowed', 3);
		}
		
		if ($xprofile->get('regIP') == '') {
			$xprofile->set('regIP', $_SERVER['REMOTE_ADDR']);
		}
		
		if ($xprofile->get('regHost') == '') {
			if (isset($_SERVER['REMOTE_HOST'])) {
				$xprofile->set('regHost', $_SERVER['REMOTE_HOST']);
			}
		}
		
		if ($xprofile->get('registerDate') == '') {
			$xprofile->set('registerDate', date('Y-m-d H:i:s'));
		}
		
		if ($xregistration->get('email') != $xprofile->get('email')) {
			if ($xprofile->hasTransientEmail() && $xregistration->get('email') != $xprofile->getTransientEmail()) {
				$xprofile->set('emailConfirmed', '3');
			} else {
				$xprofile->set('emailConfirmed', -rand(1, pow(2, 31)-1) );
				$updateEmail = true;
			}
		}
		
		if ($xregistration->get('login') != $xprofile->get('username')) {
			if ($xprofile->hasTransientUsername()) {
				$xprofile->set('homeDirectory', $hubHomeDir . '/' . $xregistration->get('login'));
			}
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
		if ($myjuser->get('id') == $juser->get('id')) {
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

		// Notify the user
		if ($updateEmail) {
			$subject  = $this->jconfig->getValue('config.sitename') .' '.JText::_('COM_REGISTER_EMAIL_CONFIRMATION');

			$eview = new JView( array('name'=>'emails','layout'=>'update') );
			$eview->option = $this->_option;
			$eview->hubShortName = $this->jconfig->getValue('config.sitename');
			$eview->xprofile = $xprofile;
			$eview->baseURL = $this->baseURL;
			$message = $eview->loadTemplate();
			$message = str_replace("\n", "\r\n", $message);

			if (!XHubHelper::send_email($xprofile->get('username'), $subject, $message)) {
				$this->setError(JText::sprintf('COM_REGISTER_ERROR_EMAILING_CONFIRMATION',$hubMonitorEmail));
			}
		}

		// Notify administration
		if ($action == 'submit') {
			$subject = $this->jconfig->getValue('config.sitename') .' '.JText::_('COM_REGISTER_EMAIL_ACCOUNT_UPDATE');

			$eaview = new JView( array('name'=>'emails','layout'=>'adminupdate') );
			$eaview->option = $this->_option;
			$eaview->hubShortName = $this->jconfig->getValue('config.sitename');
			$eaview->xprofile  = $xprofile;
			$eaview->baseURL = $this->baseURL;
			$message = $eaview->loadTemplate();
			$message = str_replace("\n", "\r\n", $message);

			XHubHelper::send_email($hubMonitorEmail, $subject, $message);
		}

		if (!$updateEmail) {
			// Redirect
			$hconfig = &JComponentHelper::getParams('com_hub');
		    $r = $hconfig->get('LoginReturn');
		    $return = ($r) ? $r : '/myhub';
			$xhub->redirect($return); // @TODO not sure where it is meant to redirect to offhand. but the code below causes a redirect loop
			$xhub->redirect($_SERVER['REQUEST_URI']);
		} else {
			// Instantiate a new view
			$view = new JView( array('name'=>'update') );
			$view->option = $this->_option;
			$view->title = JText::_('COM_REGISTER_UPDATE');
			$view->hubShortName = $this->jconfig->getValue('config.sitename');
			$view->xprofile = $xprofile;
			$view->self = true;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
		}
	}
	
	//-----------

	protected function create($action='show')
	{
		$action = ($action) ? $action : 'show';
		
		// Add the CSS to the template
		$this->_getStyles();

		// Add some Javascript to the template
		$this->_getScripts();

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();
		
		if ($action != 'submit' && $action != 'show') {
			return JError::raiseError(404, JText::_('COM_REGISTER_ERROR_INVALID_REQUEST') );
		}

		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) {
			return JError::raiseError(500, JText::_('COM_REGISTER_ERROR_NONGUEST_SESSION_CREATION') );
		}
		
		// Instantiate a new registration object
		$xregistration = new XRegistration();

		if ($action == 'submit') {
			// Load POSTed data
			$xregistration->loadPost();
			
			// Perform field validation
			if (!$xregistration->check('create')) {
				return $this->_show_registration_form($xregistration,'create');
			}

			// Get some settings
			$xhub =& XFactory::getHub();
			$hubMonitorEmail = $xhub->getCfg('hubMonitorEmail');
			$hubHomeDir      = $xhub->getCfg('hubHomeDir');
	
			jimport('joomla.application.component.helper');
			$config   =& JComponentHelper::getParams( 'com_users' );
			$usertype = $config->get( 'new_usertype', 'Registered' );

			$acl =& JFactory::getACL();
			
			// Create a new Joomla user
			$target_juser = new JUser();
			$target_juser->set('id',0);
			$target_juser->set('name', $xregistration->get('name'));
			$target_juser->set('username', $xregistration->get('login'));
			$target_juser->set('password_clear','');
			$target_juser->set('email', $xregistration->get('email'));
			$target_juser->set('gid', $acl->get_group_id( '', $usertype));
			$target_juser->set('usertype', $usertype);
			$target_juser->save();
			
			// Attempt to get the new user
			$xuser = XUser::getInstance($target_juser->get('id'));

			$result = is_object($xuser);

			// Did we successfully create an account?
			if ($result) {
				$xuser->loadRegistration($xregistration);
				$xuser->set('home', $hubHomeDir . '/' . $xuser->get('login'));
				$xuser->set('jobs_allowed', 3);
				$xuser->set('reg_ip', $_SERVER['REMOTE_ADDR']);
				$xuser->set('email_confirmed', -rand(1, pow(2, 31)-1) );
				if (isset($_SERVER['REMOTE_HOST'])) {
					$xuser->set('reg_host', $_SERVER['REMOTE_HOST']);
				}
				$xuser->set('reg_date', date('Y-m-d H:i:s'));
				
				// Update the account
				$result = $xuser->update();
				
				// Do we have a return URL?
				$regReturn = JRequest::getVar('return', ''); 
				if ($regReturn) {
					$target_profile =& XProfile::getInstance( $target_juser->get('id') );
					
					if (is_object($target_profile)) {
						$target_profile->setParam('return', $regReturn);
						$target_profile->update();
					}
				}
			}
			
			// Did we successfully create/update an account?
			if (!$result) {
				$view = new JView( array('name'=>'error') );
				$view->title = JText::_('COM_REGISTER_CREATE_ACCOUNT');
				$view->setError( JText::sprintf('COM_REGISTER_ERROR_CREATING_ACCOUNT', $hubMonitorEmail) );
				$view->display();
				return;
			}

			// Notify the user
			$subject  = $this->jconfig->getValue('config.sitename').' '.JText::_('COM_REGISTER_EMAIL_CONFIRMATION');

			$eview = new JView( array('name'=>'emails','layout'=>'create') );
			$eview->option = $this->_option;
			$eview->hubShortName = $this->jconfig->getValue('config.sitename');
			$eview->xuser = $xuser;
			$eview->baseURL = $this->baseURL;
			$eview->xregistration = $xregistration;
			$message = $eview->loadTemplate();
			$message = str_replace("\n", "\r\n", $message);
	
			if (!XHubHelper::send_email($xuser->get('email'), $subject, $message)) {
				$this->setError( JText::sprintf('COM_REGISTER_ERROR_EMAILING_CONFIRMATION', $hubMonitorEmail) );
			}
			
			// Notify administration
			$subject = $this->jconfig->getValue('config.sitename') .' '.JText::_('COM_REGISTER_EMAIL_ACCOUNT_CREATION');

			$eaview = new JView( array('name'=>'emails','layout'=>'admincreate') );
			$eaview->option = $this->_option;
			$eaview->hubShortName = $this->jconfig->getValue('config.sitename');
			$eaview->xuser = $xuser;
			$eaview->baseURL = $this->baseURL;
			$message = $eaview->loadTemplate();
			$message = str_replace("\n", "\r\n", $message);
	
			XHubHelper::send_email($hubMonitorEmail, $subject, $message);

			// Instantiate a new view
			$view = new JView( array('name'=>'create') );
			$view->option = $this->_option;
			$view->title = JText::_('COM_REGISTER_CREATE_ACCOUNT');
			$view->hubShortName = $this->jconfig->getValue('config.sitename');
			$view->xuser = $xuser;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
			return;
		}

		return $this->_show_registration_form($xregistration, 'create');
	}
	
	//-----------

	/*protected function login($action='show')
	{
		$return = base64_decode( JRequest::getVar('return', '',  'method', 'base64') );

		if (empty($return)) {
			$hconfig = &JComponentHelper::getParams('com_hub');
			$r = $hconfig->get('LoginReturn');
			$return = ($r) ? $r : JRoute::_('index.php?option=com_myhub');
		}

		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) {
			return $xhub->redirect($return);
		}

		if (!$this->_cookie_check()) {
			return;
		}

		if ($action != 'show' && $action != 'submit') {
			return JError::raiseError(404, JText::_('Invalid Request') );
		}

		$xhub =& XFactory::getHub();

		if ($action == 'submit') {
			$credentials = array();
			$credentials['username'] = JRequest::getVar('username', '', 'method', 'username');
			$credentials['password'] = JRequest::getString('passwd', '', 'post', JREQUEST_ALLOWRAW);
	
			$options = array();
			$options['remember'] = JRequest::getBool('remember', false);
			$options['domain'] = JRequest::getString('realm','','post');
	       	$options['return'] = $return;

			$login_attempts = JRequest::getInt('la',0,'post');

			if (!empty($credentials['username']) && !empty($credentials['password'])) {
				$app   =& JFactory::getApplication();
				$error = $app->login($credentials, $options);

	        	if (!JError::isError($error)) {
					return $xhub->redirect( $return );
				}

				$error_message = $error->get('message');
			} else if ($login_attempts > 0) {
				$error_message = JText::_('E_LOGIN_AUTHENTICATE');
			} else {
				$error_message = '';
			}
			$usrnm = $credentials['username'];
		} else {
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

			if (empty($realm)) {
				$realm = $plugin->name;

				if (!in_array($realm, $realms)) {
					$realms[$plugin->name] = $realm;
				}
			}
		}

		$login_attempts++;
		
		$realm = JRequest::getVar('realm', '', 'method');

		if (empty($realm) && count($realms) == 1) {
			$realm = current( array_keys($realms) );
		}

		if (!array_key_exists($realm, $realms)) {
			return JError::raiseError( 404, JText::_('Invalid Authentication Realm Requested') );
		}
		
		$realmName = $realms[$realm];

		// @TODO this default should be provided by plugin and probably should be different than the realm name
  		// it should be a variable specifically for the login prompt.
		if ($realmName == 'hzldap') {
			$realmName = $this->jconfig->getValue('config.sitename') . ' Account';
		}
		
		$usersConfig =& JComponentHelper::getParams( 'com_users' );
		$registration_enabled = $usersConfig->get( 'allowUserRegistration' );
		
		unset($credentials,$options,$realms,$params,$plugins,$plugin,$action,$usersConfig,$app,$error);
		
		// Instantiate a new view
		$view = new JView( array('name'=>'login') );
		$view->option = $this->_option;
		$view->title = JText::_('Login');
		$view->hubShortName = $this->jconfig->getValue('config.sitename');
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}*/
	
	//-----------

	protected function select($action='show')
	{
		$action = ($action) ? $action : 'show';
		
		if ($action != 'submit' && $action != 'show') {
			return JError::raiseError(404, JText::_('COM_REGISTER_ERROR_INVALID_REQUEST'));
		}

		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) {
			return JError::raiseError(500, JText::_('COM_REGISTER_ERROR_NONGUEST_SESSION_CREATION'));
		}
		
		if (!$this->_cookie_check()) {
			return;
		}

		// Get all the authentication realms
		$plugins = JPluginHelper::getPlugin('xauthentication');

		$realms = array();

		foreach ($plugins as $plugin)
		{
			$params = new JParameter($plugin->params);
			
			$realm = $params->get('domain');
			
			if (empty($realm)) {
				$realm = $plugin->name;
			}
			
			if (!in_array($realm, $realms) && ($plugin->name != 'hzldap')) {
				$realms[$plugin->name] = $realm;
			}
		}
		
		// Choose action
		if ($action == 'submit') {
			if (JRequest::getVar('register', '', 'method')) {
				return $this->create('show');
			}

			if (JRequest::getVar('login', '', 'method')) {
				//return $this->login('show');
				// Instantiate a new view
				$view = new JView( array('name'=>'login') );
				$view->option = $this->_option;
				$view->title = JText::_('COM_REGISTER_LOGIN');
				$view->display();
				return;
			}
		}

		unset($plugins, $params, $realm, $action);
	
		// Push straight to the form if no realms found
		if (count($realms) == 0) {
			return $this->create('show');
		}
		
		// Add the CSS to the template
		$this->_getStyles();

		// Add some Javascript to the template
		$this->_getScripts();

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();
		
		// Instantiate a new view
		$view = new JView( array('name'=>'select') );
		$view->option = $this->_option;
		$view->title = JText::_('COM_REGISTER_SELECT_METHOD');
		$view->hubShortName = $this->jconfig->getValue('config.sitename');
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------

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

		$hconfig =& JComponentHelper::getParams('com_hub');
		
		$default = str_pad($default, '-', 4);
		$configured = $hconfig->get($name);
		if (empty($configured)) {
			$configured = $default;
		}
		$length = strlen($configured);
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

	private function _show_registration_form(&$xregistration=null, $task='create')
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'registration') );
		$view->option = $this->_option;
		$view->task = $task;
		$view->title = JText::_('COM_REGISTER');
		$view->hubShortName = $this->jconfig->getValue('config.sitename');

		$juser =& JFactory::getUser();
		$username = JRequest::getVar('username',$juser->get('username'),'get');
		$view->self = ($juser->get('username') == $username);

		// Get the registration object
		if (!is_object($xregistration)) {
			$view->xregistration = new XRegistration();
		} else {
			$view->xregistration = $xregistration;
		}
		
		// Push some values to the view
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
		$view->registrationTOU = $this->_registrationField('registrationTOU','HHHH',$task);

		if ($view->task == 'update') {
			if (empty($view->xregistration->_encoded['login'])) {
				$view->registrationUsername = REG_READONLY;
			} else {
				$view->registrationUsername = REG_REQUIRED;
				$view->registration['login'] = $view->xregistration->_encoded['login'];
			}

			$view->registrationPassword = REG_HIDE;
			$view->registrationConfirmPassword = REG_HIDE;
		}

		if ($view->task == 'edit') {
			$view->registrationUsername = REG_READONLY;
			$view->registrationPassword = REG_HIDE;
			$view->registrationConfirmPassword = REG_HIDE;
		}

		if ($view->registrationEmail == REG_REQUIRED 
		 || $view->registrationEmail == REG_OPTIONAL) {
			if (!empty($view->xregistration->_encoded['email'])) {
				$view->registration['email'] = $view->xregistration->_encoded['email'];
			}
		}

		if ($view->registrationConfirmEmail == REG_REQUIRED 
		 || $view->registrationConfirmEmail == REG_OPTIONAL) {
			if (!empty($view->xregistration->_encoded['email'])) {
				$view->registration['confirmEmail'] = $view->xregistration->_encoded['email']; 
			}
		}
		
		// Display the view
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------
	
	protected function passwordstrength($act) 
	{
		// Incoming
		$no_html = JRequest::getInt('no_html',0);
		$password = JRequest::getVar('pass','');
		$username = JRequest::getVar('user','');
		
		// Instantiate a new registration object
		$xregistration = new XRegistration();
		
		// Score the password
		$score = $xregistration->scorePassword($password, $username);
		
		// Determine strength
		if ($score < PASS_SCORE_MEDIOCRE) {
			$cls = 'bad';
			$txt = JText::_('COM_REGISTER_PASS_BAD');
		} else if ($score >= PASS_SCORE_MEDIOCRE && $score < PASS_SCORE_GOOD) {
			$cls = 'mediocre';
			$txt = JText::_('COM_REGISTER_PASS_MEDIOCRE');
		} else if ($score >= PASS_SCORE_GOOD && $score < PASS_SCORE_STRONG) {
			$cls = 'good';
			$txt = JText::_('COM_REGISTER_PASS_GOOD');
		} else if ($score >= PASS_SCORE_STRONG) {
			$cls = 'strong';
			$txt = JText::_('COM_REGISTER_PASS_STRONG');
		}
		
		// Build the HTML
		$html = '<span id="passwd-meter" style="width:'.$score.'%;" class="'.$cls.'"><span>'.JText::_($txt).'</span></span>';
		
		// Return the HTML
		if ($no_html) {
			echo $html;
		} else {
			return $html;
		}
	}

	//----------------------------------------------------------
	//  Email (account confirmation)
	//----------------------------------------------------------
	
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
		$juser = &JFactory::getUser();
		if ($juser->get('guest')) {
			// Instantiate a new view
			$view = new JView( array('name'=>'login') );
			$view->option = $this->_option;
			$view->title = JText::_('COM_REGISTER_RESEND');
			$view->setError( JText::_('COM_REGISTER_ERROR_LOGIN_TO_RESEND') );
			$view->display();
			return;
		}
		
		$xuser =& XFactory::getUser();
		$login = $xuser->get('login');
		$email = $xuser->get('email');
		$email_confirmed = $xuser->get('email_confirmed');
		
		// Incoming
		$return = urldecode( JRequest::getVar( 'return', '/' ) );
		
		if (($email_confirmed != 1) && ($email_confirmed != 3)) {
			$confirm = XRegistrationHelper::genemailconfirm();

			ximport('xprofile');
			$xprofile = new XProfile();
			$xprofile->load($login);
			$xprofile->set('emailConfirmed', $confirm);
			$xprofile->update();
			
			$subject  = $this->jconfig->getValue('config.sitename').' '.JText::_('COM_REGISTER_EMAIL_CONFIRMATION');

			$eview = new JView( array('name'=>'emails','layout'=>'confirm') );
			$eview->option = $this->_option;
			$eview->hubShortName = $this->jconfig->getValue('config.sitename');
			$eview->login = $login;
			$eview->baseURL = $this->baseURL;
			$eview->confirm = $confirm;
			$message = $eview->loadTemplate();
			$message = str_replace("\n", "\r\n", $message);

			if (!XHubHelper::send_email($email, $subject, $message)) {
				$this->setError(JText::sprintf('COM_REGISTER_ERROR_EMAILING_CONFIRMATION', $email));
			}
			
			// Instantiate a new view
			$view = new JView( array('name'=>'send') );
			$view->option = $this->_option;
			$view->title = JText::_('COM_REGISTER_RESEND');
			$view->login = $login;
			$view->email = $email;
			$view->return = $return;
			$view->show_correction_faq = true;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
		} else { 
			header("Location: " . urlencode($return));
		}
	}

	//-----------
	
	protected function change()
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
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			// Instantiate a new view
			$view = new JView( array('name'=>'login') );
			$view->option = $this->_option;
			$view->title = JText::_('COM_REGISTER_CHANGE');
			$view->setError( JText::_('COM_REGISTER_ERROR_LOGIN_TO_UPDATE') );
			$view->display();
			return;
		}
		
		$xuser =& XFactory::getUser();
		$login = $xuser->get('login');
		$email = $xuser->get('email');
		$email_confirmed = $xuser->get('email_confirmed');
		
		// Instantiate a new view
		$view = new JView( array('name'=>'change') );
		$view->option = $this->_option;
		$view->title = JText::_('COM_REGISTER_CHANGE');
		$view->login = $login;
		$view->email = $email;
		$view->return = $return;
		$view->email_confirmed = $email_confirmed;
		$view->success = false;
		
		// Incoming
		$return = urldecode( JRequest::getVar( 'return', '/' ) );
		
		// Check if a new email was submitted
		$pemail = JRequest::getVar('email', '', 'post');
		$update = JRequest::getVar('update', '', 'post');

		if ($update) {
			if (!$pemail) {
				$this->setError(JText::_('COM_REGISTER_ERROR_INVALID_EMAIL'));
			}
			if ($pemail && XRegistrationHelper::validemail($pemail) /*&& ($newemail != $email)*/ ) {
				// Check if the email address was actually changed
				if ($pemail == $email) {
					// Addresses are the same! Redirect
					$xhub->redirect($return);
				} else {
					// New email submitted - attempt to save it
					$xuser =& XUser::getInstance($login);
					if ($xuser) {
						$dtmodify = date("Y-m-d H:i:s");
						$xuser->set('email',$pemail);
						$xuser->set('mod_date',$dtmodify);
						if ($xuser->update()) {
							$juser =& JUser::getInstance($login);
							$juser->set('email', $pemail);
							$juser->save();
						} else {
							$this->setError(JText::_('COM_REGISTER_ERROR_UPDATING_ACCOUNT'));
						}
					} else {
						$this->setError(JText::_('COM_REGISTER_ERROR_UPDATING_ACCOUNT'));
					}

					// Any errors returned?
					if (!$this->getError()) {
						// No errors
						// Attempt to send a new confirmation code
						$confirm = XRegistrationHelper::genemailconfirm();

						ximport('xprofile');
						$xprofile = new XProfile();
						$xprofile->load($login);
						$xprofile->set('emailConfirmed', $confirm);
						$xprofile->update();

						$subject  = $this->jconfig->getValue('config.sitename').' '.JText::_('COM_REGISTER_EMAIL_CONFIRMATION');

						$eview = new JView( array('name'=>'emails','layout'=>'confirm') );
						$eview->option = $this->_option;
						$eview->hubShortName = $this->jconfig->getValue('config.sitename');
						$eview->login = $login;
						$eview->baseURL = $this->baseURL;
						$eview->confirm = $confirm;
						$message = $eview->loadTemplate();
						$message = str_replace("\n", "\r\n", $message);

						if (!XHubHelper::send_email($pemail, $subject, $message)) {
							$this->setError(JText::sprintf('COM_REGISTER_ERROR_EMAILING_CONFIRMATION', $pemail));
						}
						
						// Show the success form
						$view->success = true;
					}
				}
			} else {
				$this->setError(JText::_('COM_REGISTER_ERROR_INVALID_EMAIL'));
			}
		}
		
		// Output the view
		if ($this->getError()) {
			$view->email = $pemail;
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------
	
	protected function confirm()
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
		$juser = &JFactory::getUser();
		if ($juser->get('guest')) {
			// Instantiate a new view
			$view = new JView( array('name'=>'login') );
			$view->option = $this->_option;
			$view->title = JText::_('COM_REGISTER_CONFIRM');
			$view->setError( JText::_('COM_REGISTER_ERROR_LOGIN_TO_CONFIRM') );
			$view->display();
			return;
		}
		
		$xuser =& XFactory::getUser();

		// Incoming
		$code = JRequest::getVar( 'confirm', false );
		if (!$code) {
			$code = JRequest::getVar( 'code', false );
		}
		
		$email_confirmed = $xuser->get('email_confirmed');

		if (($email_confirmed == 1) || ($email_confirmed == 3)) {
			// All is well
		} elseif ($email_confirmed < 0 && $email_confirmed == -$code) {
			ximport('xprofile');
			$xprofile = new XProfile();
			$xprofile->load($xuser->get('login'));
			
			$myreturn = $xprofile->getParam('return');
			if ($myreturn) {
				$xprofile->setParam('return','');
			}
			$xprofile->set('emailConfirmed', 1);
			if ($xprofile->update()) {	
				$this->setError( JText::_('COM_REGISTER_ERROR_CONFIRMING') );
			}
			
			// Redirect
			if ($myreturn) {
				$xhub->redirect($myreturn);
			}
		} else {
			$this->setError(JText::_('COM_REGISTER_ERROR_INVALID_CONFIRMATION'));
		}
		
		// Instantiate a new view
		$view = new JView( array('name'=>'confirm') );
		$view->option = $this->_option;
		$view->title = JText::_('COM_REGISTER_CONFIRM');
		$view->login = $xuser->get('login');
		$view->email = $xuser->get('email');
		$view->code = $code;
		$view->hubShortName = $this->jconfig->getValue('config.sitename');
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------
	
	protected function unconfirmed()
	{
		$xuser =& XFactory::getUser();
		$email_confirmed = $xuser->get('email_confirmed');

		// Incoming
		$return = JRequest::getVar( 'return', urlencode('/') );
		
		// Check if the email has been confirmed
		if (($email_confirmed != 1) && ($email_confirmed != 3)) {
			// Add the CSS to the template
			$this->_getStyles();

			// Add some Javascript to the template
			$this->_getScripts();

			// Set the pathway
			$this->_buildPathway();

			// Set the page title
			$this->_buildTitle();
			
			// Check if the user is logged in
			$juser =& JFactory::getUser();
			if ($juser->get('guest')) {
				// Instantiate a new view
				$view = new JView( array('name'=>'login') );
				$view->option = $this->_option;
				$view->title = JText::_('COM_REGISTER_CONFIRM');
				$view->setError( JText::_('COM_REGISTER_ERROR_LOGIN_TO_CONFIRM') );
				$view->display();
				return;
			}
			
			// Instantiate a new view
			$view = new JView( array('name'=>'unconfirmed') );
			$view->option = $this->_option;
			$view->title = JText::_('COM_REGISTER_UNCONFIRMED');
			$view->email = $xuser->get('email');
			$view->return = $return;
			$view->hubShortName = $this->jconfig->getValue('config.sitename');
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
		} else {
			header("Location: " . urldecode($return));
		}
	}

	//----------------------------------------------------------
	// Private Functions
	//----------------------------------------------------------
	
	private function _getStyles()
	{
	    // add the CSS to the template and set the page title
		ximport('xdocument');
		XDocument::addComponentStylesheet($this->_option);
	}
	
	//-----------
	
	private function _getScripts()
	{
		$document =& JFactory::getDocument();
		if (is_file(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.$this->_name.'.js')) {
			$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
		}
	}
	
	//-----------

	private function _buildPathway() 
	{
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(
				JText::_('COM_REGISTER'),
				'index.php?option='.$this->_option
			);
		}
		if ($this->_task) {
			$pathway->addItem(
				JText::_('COM_REGISTER_'.strtoupper($this->_task)),
				'index.php?option='.$this->_option.'&task='.$this->_task
			);
		}
	}
	
	//-----------
	
	private function _buildTitle() 
	{
		if ($this->_task) {
			$title = JText::_('COM_REGISTER_'.strtoupper($this->_task));
		} else {
			$title = JText::_('COM_REGISTER');
		}
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
	}
	
	//-----------
	
	private function _cookie_check()
	{
		$xhub =& XFactory::getHub();
		$jsession =& JFactory::getSession();
		$jcookie = $jsession->getName();

		if (!isset($_COOKIE[$jcookie])) {
			if (JRequest::getVar('cookie', '', 'get') != 'no') {
				$juri = JURI::getInstance();
				$juri->setVar('cookie','no');
				return $xhub->redirect($juri->toString());
			}
			
			$view = new JView( array('name'=>'error') );
			$view->title = JText::_('COM_REGISTER');
			$view->setError( JText::_('COM_REGISTER_ERROR_COOKIES') );
			$view->display();

			return false;
		} else if (JRequest::getVar('cookie', '', 'get') == 'no') {
			$juri = JURI::getInstance();
			$juri->delVar('cookie');

			return $xhub->redirect($juri->toString());
		}

		return true;
	}
}
?>
