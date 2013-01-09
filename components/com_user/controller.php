<?php
/**
 * @version		$Id: controller.php 16385 2010-04-23 10:44:15Z ian $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * User Component Controller
 *
 * @package		Joomla
 * @subpackage	Weblinks
 * @since 1.5
 */
class UserController extends JController
{
	// @FIXME: Not sure why this is needed, we're probably doing something wrong
	function attach(){}

	function execute( $task )
	{
		$task = strtolower( $task );

		if ( ($task == 'register_save') || ($task == 'register') ) {
			return JError::raiseError( 404, JText::_('Task ['.$task.'] not found') );
		}

		parent::execute($task);
	}	

	/**
	 * Method to display a view
	 *
	 * @access	public
	 * @since	1.5
	 */
	function display()
	{
		global $mainframe;

		$view = strtolower( JRequest::getCmd( 'view', $this->getName() ) );

		if ($view == 'register') {
			$mainframe->redirect( JRoute::_('index.php?option=com_register') );
			JError::raiseError( 404, 'Not Found' );
		}

		parent::display();
	}

	function edit()
	{
		global $mainframe, $option;

		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();

		if ( $user->get('guest')) {
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return;
		}

		JRequest::setVar('layout', 'form');

		parent::display();
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$user	 =& JFactory::getUser();
		$userid = JRequest::getVar( 'id', 0, 'post', 'int' );

		// preform security checks
		if ($user->get('id') == 0 || $userid == 0 || $userid <> $user->get('id')) {
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return;
		}

		//clean request
		$post = JRequest::get( 'post' );
		$post['username']	= JRequest::getVar('username', '', 'post', 'username');
		$post['password']	= JRequest::getVar('password', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['password2']	= JRequest::getVar('password2', '', 'post', 'string', JREQUEST_ALLOWRAW);
	
		// get the redirect
		$return = JURI::base();
		
		// do a password safety check
		if(strlen($post['password']) || strlen($post['password2'])) { // so that "0" can be used as password e.g.
			if($post['password'] != $post['password2']) {
				$msg	= JText::_('PASSWORDS_DO_NOT_MATCH');
				// something is wrong. we are redirecting back to edit form.
				// TODO: HTTP_REFERER should be replaced with a base64 encoded form field in a later release
				$return = str_replace(array('"', '<', '>', "'"), '', @$_SERVER['HTTP_REFERER']);
				if (empty($return) || !JURI::isInternal($return)) {
					$return = JURI::base();
				}
				$this->setRedirect($return, $msg, 'error');
				return false;
			}
		}

		// we don't want users to edit certain fields so we will unset them
		unset($post['gid']);
		unset($post['block']);
		unset($post['usertype']);
		unset($post['registerDate']);
		unset($post['activation']);

		// store data
		$model = $this->getModel('user');

		if ($model->store($post)) {
			$msg	= JText::_( 'Your settings have been saved.' );
		} else {
			//$msg	= JText::_( 'Error saving your settings.' );
			$msg	= $model->getError();
		}
	
		ximport('Hubzero_User_Password');
		
		$result = Hubzero_User_Password::changePassword($user->get('username'), $post['password']);

        // Save the changes
        if (!$result) {
           	$model->setError( JText::_('MEMBERS_PASS_CHANGE_FAILED') );
			$msg = $model->getError();
        }

		$this->setRedirect( $return, $msg );
	}

	function cancel()
	{
		$this->setRedirect( JRoute::_('index.php') );
	}

	function login()
	{
		global $mainframe;

		$authenticator = JRequest::getVar('authenticator', '', 'method');

		// If a specific authenticator is specified try to call the login method for that plugin
		
		if (!empty($authenticator)) {
			JPluginHelper::importPlugin('authentication');

			$plugins = JPluginHelper::getPlugin('authentication');

			foreach ($plugins as $plugin)
			{
				$className = 'plg'.$plugin->type.$plugin->name;

				if ($plugin->name != $authenticator) {
					continue;
				}

				if (class_exists($className)) {
					if (method_exists($className,'login')) {

						$myplugin = new $className($this,(array)$plugin);

						$myplugin->login($credentials, $options);
						
						if (isset($options['return'])) {
								$return = $options['return'];
						}
					}

       				break;
	   			}
			}
		}
		
		// If no authenticator is specified, or the login method for that plugin did not exist then use joomla default

		if (!isset($myplugin)) {

			// Check for request forgeries
			JRequest::checkToken('request') or jexit( 'Invalid Token' );

			if ($return = JRequest::getVar('return', '', 'method', 'base64')) {
				$return = base64_decode($return);
				if (!JURI::isInternal($return)) {
					$return = '';
				}
			}

			if ($freturn = JRequest::getVar('freturn', '', 'method', 'base64')) {
				$freturn = base64_decode($freturn);
				if (!JURI::isInternal($freturn)) {
					$freturn = '';
				}
			}

			$options = array();
			$options['remember'] = JRequest::getBool('remember', false);
			$options['return'] = $return;
			if (!empty($authenticator))
				$options['authenticator'] = $authenticator;

			$credentials = array();
			$credentials['username'] = JRequest::getVar('username', '', 'method', 'username');
			$credentials['password'] = JRequest::getString('passwd', '', 'post', JREQUEST_ALLOWRAW);
		}


		//preform the login action
		$error = $mainframe->login($credentials, $options);
		
		if(!JError::isError($error))
		{
			// Redirect if the return url is not registration or login
			if ( ! $return ) {
				$return	= JRoute::_('index.php?option=com_user');
			}

			// If no_html is set, return json response
			if(JRequest::getInt('no_html', 0))
			{
				echo json_encode( array("success" => true, "redirect" => $return) );
				exit;
			}
			else
			{
				$mainframe->redirect( $return );
			}
		}
		else
		{
			// Facilitate third party login forms
			if ( ! $return ) {
				$return	= JRoute::_('index.php?option=com_user&view=login');
			}

			if (isset($freturn))
				$return = $freturn;

			// If no_html is set, return json response
			if(JRequest::getInt('no_html', 0))
			{
				echo json_encode( array("error" => JError::getError(), "freturn" => $return) );
				exit;
			}
			else
			{
				// Redirect to a login form
				$mainframe->redirect( $return );
			}
		}
	}

	function link()
	{
		global $mainframe;
		$user =& JFactory::getUser();

		// First, they should already be logged in, so check for that
		if($user->get('guest'))
		{
			JError::raiseError( 403, JText::_( 'You must be logged in to perform this function' ));
			return;
		}

		$authenticator = JRequest::getVar('authenticator', '', 'method');

		// If a specific authenticator is specified try to call the link method for that plugin
		if (!empty($authenticator)) {
			JPluginHelper::importPlugin('authentication');

			$plugin = JPluginHelper::getPlugin('authentication', $authenticator);

			$className = 'plg'.$plugin->type.$plugin->name;

			if (class_exists($className)) {
				if (method_exists($className,'link')) {

					$myplugin = new $className($this,(array)$plugin);

					$myplugin->link($credentials, $options);
				} else {
					// No Link method is availble
					$mainframe->redirect(JRoute::_('index.php?option=com_members&id=' . $user->get('id') . '&active=account'),
						'Linked accounts are not currently available for this provider.',
						'error');
				}
			}
		} else {
			// No authenticator provided...
			JError::raiseError( 400, JText::_( 'Missing authenticator' ));
			return;
		}

		// Success!  Redict with message
		$mainframe->redirect(JRoute::_('index.php?option=com_members&id=' . $user->get('id') . '&active=account'),
			'Your account has been successfully linked!');
	}

	function logout()
	{
		global $mainframe;

		$juser = JFactory::getUser();

		$authenticator = JRequest::getVar('authenticator', '', 'method');

		// If a specific authenticator is specified try to call the logout method for that plugin
		
		if (!empty($authenticator)) {
			JPluginHelper::importPlugin('authentication');

			$plugins = JPluginHelper::getPlugin('authentication');

			foreach ($plugins as $plugin)
			{
				$className = 'plg'.$plugin->type.$plugin->name;

				if ($plugin->name != $authenticator) {
					continue;
				}

				if (class_exists($className))
				{
					if (method_exists($className,'logout'))
					{
						$myplugin = new $className($this,(array)$plugin);

						$result = $myplugin->logout();
					}

					break;
	   			}
			}
		}
		
		//preform the logout action
		$error = $mainframe->logout();

		if(!JError::isError($error))
		{
			// If the authenticator is empty, but they have an active third party session,
			// redirect to a page indicating this and offering complete signout
			if($juser->auth_link_id && empty($authenticator))
			{
				ximport('Hubzero_Auth_Link');
				ximport('Hubzero_Auth_Domain');
				$auth_domain_id   = Hubzero_Auth_Link::find_by_id($juser->auth_link_id)->auth_domain_id;
				$auth_domain_name = Hubzero_Auth_Domain::find_by_id($auth_domain_id)->authenticator;

				// Redirect to user third party signout view
				// Only do this for PUCAS for the time being (it's the one that doesn't lose session info after hub logout)
				if($auth_domain_name == 'pucas')
				{
					$mainframe->redirect(JRoute::_('index.php?option=com_user&view=endsinglesignon&authenticator=' . $auth_domain_name, false));
					return;
				}
			}

			if ($return = JRequest::getVar('return', '', 'method', 'base64')) {
				$return = base64_decode($return);
				if (!JURI::isInternal($return)) {
					$return = '';
				}
			}

			// Redirect if the return url is not registration or login
			if ( $return && !( strpos( $return, 'com_user' )) ) {
				$mainframe->redirect( $return );
			}
		} else {
			parent::display();
		}
	}

	/**
	 * Prepares the registration form
	 * @return void
	 */
	function register()
	{
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		if (!$usersConfig->get( 'allowUserRegistration' )) {
			JError::raiseError( 403, JText::_( 'Access Forbidden' ));
			return;
		}

		$user 	=& JFactory::getUser();

		if ( $user->get('guest')) {
			JRequest::setVar('view', 'register');
		} else {
			$this->setRedirect(JRoute::_('index.php?option=com_user&task=edit'),JText::_('You are already registered.'));
		}

		parent::display();
	}

	/**
	 * Save user registration and notify users and admins if required
	 * @return void
	 */
	function register_save()
	{
		global $mainframe;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Get required system objects
		$user 		= clone(JFactory::getUser());
		$pathway 	=& $mainframe->getPathway();
		$config		=& JFactory::getConfig();
		$authorize	=& JFactory::getACL();
		$document   =& JFactory::getDocument();

		// If user registration is not allowed, show 403 not authorized.
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		if ($usersConfig->get('allowUserRegistration') == '0') {
			JError::raiseError( 403, JText::_( 'Access Forbidden' ));
			return;
		}

		// Initialize new usertype setting
		$newUsertype = $usersConfig->get( 'new_usertype' );
		if (!$newUsertype) {
			$newUsertype = 'Registered';
		}

		// Bind the post array to the user object
		if (!$user->bind( JRequest::get('post'), 'usertype' )) {
			JError::raiseError( 500, $user->getError());
		}

		// Set some initial user values
		$user->set('id', 0);
		$user->set('usertype', $newUsertype);
		$user->set('gid', $authorize->get_group_id( '', $newUsertype, 'ARO' ));

		$date =& JFactory::getDate();
		$user->set('registerDate', $date->toMySQL());

		// If user activation is turned on, we need to set the activation information
		$useractivation = $usersConfig->get( 'useractivation' );
		if ($useractivation == '1')
		{
			jimport('joomla.user.helper');
			$user->set('activation', JUtility::getHash( JUserHelper::genRandomPassword()) );
			$user->set('block', '1');
		}

		// If there was an error with registration, set the message and display form
		if ( !$user->save() )
		{
			JError::raiseWarning('', JText::_( $user->getError()));
			$this->register();
			return false;
		}

		// Send registration confirmation mail
		$password = JRequest::getString('password', '', 'post', JREQUEST_ALLOWRAW);
		$password = preg_replace('/[\x00-\x1F\x7F]/', '', $password); //Disallow control chars in the email
		UserController::_sendMail($user, $password);

		// Everything went fine, set relevant message depending upon user activation state and display message
		if ( $useractivation == 1 ) {
			$message  = JText::_( 'REG_COMPLETE_ACTIVATE' );
		} else {
			$message = JText::_( 'REG_COMPLETE' );
		}

		$this->setRedirect(JRoute::_('index.php'), $message);
	}

	function activate()
	{
		global $mainframe;

		// Initialize some variables
		$db			=& JFactory::getDBO();
		$user 		=& JFactory::getUser();
		$document   =& JFactory::getDocument();
		$pathway 	=& $mainframe->getPathWay();

		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		$userActivation			= $usersConfig->get('useractivation');
		$allowUserRegistration	= $usersConfig->get('allowUserRegistration');

		// Check to see if they're logged in, because they don't need activating!
		if ($user->get('id')) {
			// They're already logged in, so redirect them to the home page
			$mainframe->redirect( JRoute::_('index.php') );
		}

		if ($allowUserRegistration == '0' || $userActivation == '0') {
			JError::raiseError( 403, JText::_( 'Access Forbidden' ));
			return;
		}

		// create the view
		require_once (JPATH_COMPONENT.DS.'views'.DS.'register'.DS.'view.html.php');
		$view = new UserViewRegister();

		$message = new stdClass();

		// Do we even have an activation string?
		$activation = JRequest::getVar('activation', '', '', 'alnum' );
		$activation = $db->getEscaped( $activation );

		if (empty( $activation ))
		{
			// Page Title
			$document->setTitle( JText::_( 'REG_ACTIVATE_NOT_FOUND_TITLE' ) );
			// Breadcrumb
			$pathway->addItem( JText::_( 'REG_ACTIVATE_NOT_FOUND_TITLE' ));

			$message->title = JText::_( 'REG_ACTIVATE_NOT_FOUND_TITLE' );
			$message->text = JText::_( 'REG_ACTIVATE_NOT_FOUND' );
			$view->assign('message', $message);
			$view->display('message');
			return;
		}

		// Lets activate this user
		jimport('joomla.user.helper');
		if (JUserHelper::activateUser($activation))
		{
			// Page Title
			$document->setTitle( JText::_( 'REG_ACTIVATE_COMPLETE_TITLE' ) );
			// Breadcrumb
			$pathway->addItem( JText::_( 'REG_ACTIVATE_COMPLETE_TITLE' ));

			$message->title = JText::_( 'REG_ACTIVATE_COMPLETE_TITLE' );
			$message->text = JText::_( 'REG_ACTIVATE_COMPLETE' );
		}
		else
		{
			// Page Title
			$document->setTitle( JText::_( 'REG_ACTIVATE_NOT_FOUND_TITLE' ) );
			// Breadcrumb
			$pathway->addItem( JText::_( 'REG_ACTIVATE_NOT_FOUND_TITLE' ));

			$message->title = JText::_( 'REG_ACTIVATE_NOT_FOUND_TITLE' );
			$message->text = JText::_( 'REG_ACTIVATE_NOT_FOUND' );
		}

		$view->assign('message', $message);
		$view->display('message');
	}

	/**
	 * Password Reset Request Method
	 *
	 * @access	public
	 */
	function requestreset()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Get the input
		$username		= JRequest::getVar('username', null, 'post', 'string');

		// Get the model
		$model = &$this->getModel('Reset');

		// Request a reset
		if ($model->requestReset($username) === false)
		{
			$message = JText::sprintf('PASSWORD_RESET_REQUEST_FAILED', $model->getError());
			$this->setRedirect(JRoute::_('index.php?option=com_user&view=reset'), $message);
			return false;
		}

		$this->setRedirect(JRoute::_('index.php?option=com_user&view=reset&layout=confirm'));
	}

	/**
	 * Password Reset Confirmation Method
	 *
	 * @access	public
	 */
	function confirmreset()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Get the input
		$token = JRequest::getVar('token', null, 'post', 'alnum');
		$username = JRequest::getVar('username', null, 'post');

		// Get the model
		$model = &$this->getModel('Reset');

		// Verify the token
		if ($model->confirmReset($token, $username) !== true)
		{
			$message = JText::sprintf('PASSWORD_RESET_CONFIRMATION_FAILED', $model->getError());
			$this->setRedirect(JRoute::_('index.php?option=com_user&view=reset&layout=confirm'), $message);
			return false;
		}
		$this->setRedirect(JRoute::_('index.php?option=com_user&view=reset&layout=complete'));
	}

	/**
	 * Password Reset Completion Method
	 *
	 * @access	public
	 */
	function completereset()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Get the input
		$password1 = JRequest::getVar('password1', null, 'post', 'string', JREQUEST_ALLOWRAW);
		$password2 = JRequest::getVar('password2', null, 'post', 'string', JREQUEST_ALLOWRAW);

		// Get the model
		$model = &$this->getModel('Reset');

		// Reset the password
		if ($model->completeReset($password1, $password2) === false)
		{
			$message = JText::sprintf('PASSWORD_RESET_FAILED', $model->getError());
			$this->setRedirect(JRoute::_('index.php?option=com_user&view=reset&layout=complete'), $message, 'error');
			return false;
		}

		$message = JText::_('PASSWORD_RESET_SUCCESS');
		$this->setRedirect(JRoute::_('index.php?option=com_user&view=login'), $message);
	}

	/**
	 * Username Reminder Method
	 *
	 * @access	public
	 */
	function remindusername()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Get the input
		$email = JRequest::getVar('email', null, 'post', 'string');

		// Get the model
		$model = &$this->getModel('Remind');

		// Send the reminder
		if ($model->remindUsername($email) === false)
		{
			$message = JText::sprintf('USERNAME_REMINDER_FAILED', $model->getError());
			$this->setRedirect(JRoute::_('index.php?option=com_user&view=remind'), $message);
			return false;
		}

		$message = JText::sprintf('USERNAME_REMINDER_SUCCESS', $email);
		$this->setRedirect(JRoute::_('index.php?option=com_user&view=login'), $message);
	}

	function _sendMail(&$user, $password)
	{
		global $mainframe;

		$db		=& JFactory::getDBO();

		$name 		= $user->get('name');
		$email 		= $user->get('email');
		$username 	= $user->get('username');

		$usersConfig 	= &JComponentHelper::getParams( 'com_users' );
		$sitename 		= $mainframe->getCfg( 'sitename' );
		$useractivation = $usersConfig->get( 'useractivation' );
		$mailfrom 		= $mainframe->getCfg( 'mailfrom' );
		$fromname 		= $mainframe->getCfg( 'fromname' );
		$siteURL		= JURI::base();

		$subject 	= sprintf ( JText::_( 'Account details for' ), $name, $sitename);
		$subject 	= html_entity_decode($subject, ENT_QUOTES);

		if ( $useractivation == 1 ){
			$message = sprintf ( JText::_( 'SEND_MSG_ACTIVATE' ), $name, $sitename, $siteURL.JRoute::_("index.php?option=com_user&task=activate&activation=".$user->get('activation')), $siteURL, $username, $password);
		} else {
			$message = sprintf ( JText::_( 'SEND_MSG' ), $name, $sitename, $siteURL);
		}

		$message = html_entity_decode($message, ENT_QUOTES);

		//get all super administrator
		$query = 'SELECT name, email, sendEmail' .
				' FROM #__users' .
				' WHERE LOWER( usertype ) = "super administrator"';
		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		// Send email to user
		if ( ! $mailfrom  || ! $fromname ) {
			$fromname = $rows[0]->name;
			$mailfrom = $rows[0]->email;
		}

		JUtility::sendMail($mailfrom, $fromname, $email, $subject, $message);

		// Send notification to all administrators
		$subject2 = sprintf ( JText::_( 'Account details for' ), $name, $sitename);
		$subject2 = html_entity_decode($subject2, ENT_QUOTES);

		// get superadministrators id
		foreach ( $rows as $row )
		{
			if ($row->sendEmail)
			{
				$message2 = sprintf ( JText::_( 'SEND_MSG_ADMIN' ), $row->name, $sitename, $name, $email, $username);
				$message2 = html_entity_decode($message2, ENT_QUOTES);
				JUtility::sendMail($mailfrom, $fromname, $row->email, $subject2, $message2);
			}
		}
	}
}
?>
