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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Plugin');

/**
 * Account Plugin class for members
 *
 * Manage password change/set (set for auth_link accounts, change for local accounts),
 * as well as uploading/managing ssh keys, and adding or remove linked accounts
 *
 */
class plgMembersAccount extends Hubzero_Plugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Event call to determine if this plugin should return data
	 * 
	 * @param      object  $user   JUser
	 * @param      object  $member MembersProfile
	 * @return     array   Plugin  name
	 */
	public function &onMembersAreas($user, $member)
	{
		// Default areas returned to nothing
		$areas = array();

		// If this is the logged in user show them
		if ($user->get('id') == $member->get('uidNumber'))
		{
			$areas['account'] = JText::_('PLG_MEMBERS_ACCOUNT');
		}

		return $areas;
	}

	/**
	 * Event call to return data for a specific member
	 * 
	 * @param      object  $user   JUser
	 * @param      object  $member MembersProfile
	 * @param      string  $option Component name
	 * @param      string  $areas  Plugins to return data
	 * @return     array   Return  array of html
	 */
	public function onMembers($user, $member, $option, $areas)
	{
		$returnhtml = true;
		$returnmeta = true;

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member))
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member))))
			{
				$returnhtml = false;
			}
		}

		$arr = array(
			'html'=>'',
			'metadata'=>''
		);

		// Imports (needed for view and metadata)
		ximport('Hubzero_User_Password');

		// Initialize a few things (needed for view and metadata)
		$this->member = $member;

		// Build the final HTML
		if ($returnhtml)
		{
			// Make sure we're using a secure connection
			$app = JFactory::getApplication();

			if (!isset( $_SERVER['HTTPS'] ) || $_SERVER['HTTPS'] == 'off')
			{
				$app->redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
				die('insecure connection and redirection failed');
			}

			// Import a few things (just needed for views)
			ximport('Hubzero_Document');
			ximport('Hubzero_Plugin_View');
			ximport('Hubzero_Auth_Link');
			ximport('Hubzero_Auth_Domain');

			// Add stylesheet
			Hubzero_Document::addPluginStylesheet('members', 'account');
			Hubzero_Document::addPluginScript('members', 'account');
			Hubzero_Document::addSystemScript('jquery.hoverIntent');

			// Add providers stylesheet
			$doc =& JFactory::getDocument();
			if (version_compare(JVERSION, '2.5', 'ge'))
			{
				$doc->addStylesheet(DS . 'components' . DS . 'com_users' . DS . 'assets' . DS . 'css' . DS . 'providers.css');
			}
			else
			{
				$doc->addStylesheet(DS . 'components' . DS . 'com_user' . DS . 'assets' . DS . 'css' . DS . 'providers.css');
			}

			// Initialize variables (just needed for views)
			$action       = JRequest::getWord('action', 'view');
			$this->user   = $user;
			$this->option = $option;

			switch ($action)
			{
				// Views
				case 'view':         $arr['html'] = $this->_view();         break;

				// Actions
				case 'unlink':                      $this->_unlink();       break;
				case 'uploadkey':                   $this->_uploadKey();    break;

				// Set local password
				case 'sendtoken':                   $this->sendtoken();     break;
				case 'confirmtoken': $arr['html'] = $this->confirmtoken();  break;
				case 'setlocalpass': $arr['html'] = $this->setlocalpass();  break;
				case 'checkPass':                   $this->checkPass();     break;

				// Default
				default:             $arr['html'] = $this->_view();         break;
			}
		}

		// Build the HTML for the account metadat portion
		if($returnmeta)
		{
			// Make sure only I can see this
			if($member->get('uidNumber') == $user->get("id"))
			{
				// Make sure a password is set and information has been found about it
				if($passinfo = $this->getPassInfo())
				{
					// If that password is within the warning or expiration period...
					if($passinfo['diff'] <= $passinfo['warning'] && $passinfo['diff'] > 0)
					{
						$title = 'Your password expires in ' . $passinfo['diff'] . ' days!';
						$link  = JRoute::_('index.php?option=com_members&id=' . $member->get('uidNumber') . '&active=account#password');

						$arr['metadata']['alert'] = '<a class="alrt" href="' . $link . '"><span><h5>Password Expiration</h5>' . $title . '</span></a>';
					}
					else if($passinfo['diff'] < 0)
					{
						$title = 'Your password has expired!';
						$link  = JRoute::_('index.php?option=com_members&id=' . $member->get('uidNumber') . '&active=account#password');

						$arr['metadata']['alert'] = '<a class="alrt" href="' . $link . '"><span><h5>Password Expiration</h5>' . $title . '</span></a>';
					}
				}
			}
		}

		return $arr;
	}

	//----------------------------------------------
	// Views
	//----------------------------------------------

	/**
	 * Primary/default view function
	 * 
	 * @return object Return
	 */
	private function _view()
	{
		// Setup our view
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => 'account',
				'name'    => 'overview'
			)
		);

		// Get linked accounts, if any
		$view->domains_avail = JPluginHelper::getPlugin('authentication');
		$view->hzalaccounts  = Hubzero_Auth_Link::find_by_user_id($this->user->get("id"));

		// Put the used domains into an array with details available from the providers (if applicable)
		$view->domains_used = array();
		$view->domain_names = array();
		if($view->hzalaccounts)
		{
			foreach($view->hzalaccounts as $authenticators)
			{
				JPluginHelper::importPlugin('authentication');

				$plugin = JPluginHelper::getPlugin('authentication', $authenticators['auth_domain_name']);

				$className = 'plg'.$plugin->type.$plugin->name;

				$details = array();

				if (class_exists($className))
				{
					if (method_exists($className, 'getInfo'))
					{
						$details = $className::getInfo($plugin->params);
					}
				}

				$view->domains_used[] = array('name' => $authenticators['auth_domain_name'], 'details' => $details);
				$view->domain_names[] = $authenticators['auth_domain_name'];
			}
		}

		// Get unused domains
		$view->domains_unused = array();
		foreach($view->domains_avail as $domain)
		{
			if($domain->name != 'hubzero' && !in_array($domain->name, $view->domain_names))
			{
				$view->domains_unused[] = $domain;
			}
		}

		// Determine what type of password change the user needs
		$hzup = Hubzero_User_Password::getInstance($this->member->get('uidNumber'));
		if(!empty($hzup->passhash))
		{
			// A password has already been set, now check if they're logged in with a linked account
			if(array_key_exists('auth_link_id', $this->user))
			{
				// Logged in with linked account
				$view->passtype = 'changelocal';
			}
			else
			{
				// Logged in with hub
				$view->passtype = 'changehub';
			}
		}
		else
		{
			// No password has been set...
			$view->passtype = 'set';
		}

		// Get password expiration information
		$view->passinfo = $this->getPassInfo();

		// Get the ssh key if it exists
		$view->key = $this->readKey();

		// Get the password rules
		ximport('Hubzero_Password_Rule');
		$password_rules = Hubzero_Password_Rule::getRules();

		// Get the password rule descriptions
		$view->password_rules = array();
		foreach($password_rules as $rule)
		{
			if (!empty($rule['description']))
			{
				$view->password_rules[] = $rule['description'];
			}
		}

		// A few more things...
		$view->option        = $this->option;
		$view->member        = $this->member;
		$view->params        = $this->params;
		$view->notifications = ($this->getPluginMessage()) ? $this->getPluginMessage() : array();

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}

		return $view->loadTemplate();
	}

	//----------------------------------------------
	// Set local password (for auth_link accounts)
	//----------------------------------------------

	/**
	 * Send out local password set confirmation token
	 * 
	 * @return void - redirect to confirm token view
	 */
	private function sendtoken()
	{
		// Import helpers/classes
		jimport('joomla.mail.helper');
		jimport('joomla.user.helper');
		ximport('Hubzero_Auth_Link');
		ximport('Hubzero_User_Password');

		// Make sure they're logged in
		if ($this->user->get('guest'))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' .
					base64_encode(JRoute::_('index.php?option=' . $this->option . '&task=myaccount&active=account&action=sendtoken'))),
				JText::_('You must be a logged in to access this area.'),
				'warning'
			);
			return;
		}

		// Make sure this is an auth link account (i.e. no password set)
		$hzup = Hubzero_User_Password::getInstance($this->member->get('uidNumber'));
		if(!empty($hzup->passhash))
		{
			JError::raiseError(404, JText::_('PLG_MEMBERS_ACCOUNT_NOT_LINKED_ACCOUNT'));
			return;
		}

		// Generate a new random token and hash it
		$token       = JUtility::getHash(JUserHelper::genRandomPassword());
		$salt        = JUserHelper::getSalt('crypt-md5');
		$hashedToken = md5($token.$salt).':'.$salt;

		// Store the hashed token
		$this->setToken($hashedToken);

		// Send the email with the token
		$this->sendEmail($token);

		// Redirect user to confirm token view page
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=account&task=confirmtoken'),
			JText::_('Please check the email associated with this account (' . $this->member->get('email') . ') for your confirmation token!'),
			'warning'
		);

		return;
	}

	/**
	 * Confirm the password set token
	 * 
	 * @return void - redirect to set local password view
	 */
	private function confirmtoken()
	{
		// Get global mainframe (for user state) and import needed classes
		global $mainframe;
		jimport('joomla.user.helper');

		// Check if they're logged in
		if ($this->user->get('guest'))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' .
					base64_encode(JRoute::_('index.php?option=' . $this->option . '&task=myaccount&active=account&action=confirmtoken'))),
				JText::_('You must be a logged in to access this area.'),
				'warning'
			);
			return;
		}

		// Get the form input
		$token  = JRequest::getVar('token', null, 'post', 'alnum');
		$change = JRequest::getVar('change', '', 'post');

		// Create the view
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => 'account',
				'name'    => 'setlocalpassword',
				'layout'  => 'confirmtoken'
			)
		);
		$view->option = $this->option;
		$view->id     = $this->user->get('id');

		// Blank form request (no data submitted)
		if(empty($change))
		{
			$view->notifications = ($this->getPluginMessage()) ? $this->getPluginMessage() : array();
			return $view->loadTemplate();
		}

		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Make sure the token is the proper length
		if(strlen($token) != 32)
		{
			// Oops, token wasn't the correct length (probably a copy/paste error)
			$this->addPluginMessage(JText::_('Invalid token length, please re-input token'), 'error');
			$view->notifications = ($this->getPluginMessage()) ? $this->getPluginMessage() : array();
			return $view->loadTemplate();
		}

		// Verify the token
		if (!$row = $this->getToken())
		{
			// Oops, user doesn't have a token set in the db (or their account is blocked)
			$this->addPluginMessage(JText::_('Invalid token.  You don\'t appear to have a token active, or your account is blocked.'), 'error');
			$view->notifications = ($this->getPluginMessage()) ? $this->getPluginMessage() : array();
			return $view->loadTemplate();
		}

		// Decrypt the token and compare to the one provided
		$parts = explode( ':', $row->activation );
		$crypt = $parts[0];

		// Invalide token
		if (!isset($parts[1]))
		{
			JError::raiseError(404, JText::_('INVALID_TOKEN'));
			return;
		}

		$salt = $parts[1];
		$testcrypt = JUserHelper::getCryptedPassword($token, $salt);

		// Verify the token provided and the one in the db match
		if (!($crypt == $testcrypt))
		{
			// Oops, user tokens don't match
			$this->addPluginMessage(JText::_('Invalid token.  Please try re-entering your token'), 'error');
			$view->notifications = ($this->getPluginMessage()) ? $this->getPluginMessage() : array();
			return $view->loadTemplate();
		}

		// All checks pass...
		// Push the token into the session
		$mainframe->setUserState($this->option . 'token', $crypt . ':' . $salt);

		// Redirect user to set local password view
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=account&task=setlocalpass'),
			JText::_('Please provide a new password'),
			'warning'
		);

		return;
	}

	/**
	 * Set local password
	 * 
	 * @return void - redirect to members account page
	 */
	private function setlocalpass()
	{
		// Get global mainframe (for user state)
		global $mainframe;

		// Logged in?
		if ($this->user->get('guest'))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' .
					base64_encode(JRoute::_('index.php?option=' . $this->option . '&task=myaccount&active=account&action=setlocalpass'))),
				JText::_('You must be a logged in to access this area.'),
				'warning'
			);
			return;
		}

		// Get the token from the user state variable
		$token = $mainframe->getUserState($this->option . 'token');

		// First check to make sure they're not trying to jump to this page without first verifying their token
		if (is_null($token))
		{
			// Tsk tsk, no sneaky business
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&id=' . $this->user->get('id') . '&active=account&task=sendtoken'),
				JText::_('You must first verify your email address by inputting the token.'),
				'error'
			);
			return;
		}

		// Get the password input
		$password1 = JRequest::getVar('password1', null, 'post', 'string', JREQUEST_ALLOWRAW);
		$password2 = JRequest::getVar('password2', null, 'post', 'string', JREQUEST_ALLOWRAW);
		$change    = JRequest::getVar('change', '', 'post');

		// Create the view
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => 'account',
				'name'    => 'setlocalpassword',
				'layout'  => 'setlocalpass'
			)
		);

		// Add a few more variables to the view
		$view->option = $this->option;
		$view->id     = $this->user->get('id');

		// Get the password rules
		ximport('Hubzero_Password_Rule');
		$password_rules = Hubzero_Password_Rule::getRules();

		// Get the password rule descriptions
		$view->password_rules = array();
		foreach($password_rules as $rule)
		{
			if (!empty($rule['description']))
			{
				$view->password_rules[] = $rule['description'];
			}
		}

		// Blank form request (no data submitted)
		if (empty($change))
		{
			$view->notifications = ($this->getPluginMessage()) ? $this->getPluginMessage() : array();
			return $view->loadTemplate();
		}

		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Load some needed libraries
		ximport('Hubzero_Registration_Helper');
		ximport('Hubzero_User_Helper');
		ximport('Hubzero_User_Profile');
		jimport('joomla.user.helper');

		// Initiate profile classs
		$profile = new Hubzero_User_Profile();
		$profile->load($this->user->get('id'));

		// Fire the onBeforeStoreUser trigger
		JPluginHelper::importPlugin('user');
		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger('onBeforeStoreUser', array($this->user->getProperties(), false));

		// Validate the password against password rules
		if (!empty($password1))
		{
			$msg = Hubzero_Password_Rule::validate($password1, $password_rules, $profile->get('username'));
		}
		else
		{
			$msg = array();
		}

		// Verify password
		$passrules = false;
		if (!$password1 || !$password2) // must enter password twice
		{
			$this->setError( JText::_('MEMBERS_PASS_MUST_BE_ENTERED_TWICE') );
		} 
		elseif ($password1 != $password2) // passwords don't match
		{
			$this->setError( JText::_('MEMBERS_PASS_NEW_CONFIRMATION_MISMATCH') );
		} 
		elseif (!empty($msg)) // password doesn't meet site requirements
		{
			$this->setError( JText::_('Password does not meet site password requirements. Please choose a password meeting all the requirements listed.') );
			$passrules = true;
		}

		// Were there any errors?
		if ($this->getError())
		{
			$change = array();
			$change['_missing']['password'] = $this->getError();

			if(!empty($msg) && $passrules)
			{
				//$change = $msg;
			}

			if(JRequest::getInt("no_html", 0))
			{
				echo json_encode($change);
				exit();
			}
			else
			{
				$view->setError( $this->getError() );
				return $view->loadTemplate();
			}
		}

		// No errors, so let's move on - encrypt the password and update the profile
		$result = Hubzero_User_Password::changePassword($profile->get('uidNumber'), $password1);

		// Save the changes
		if (!$result)
		{
			$view->setError( JText::_('MEMBERS_PASS_CHANGE_FAILED') );
			return $view->loadTemplate();
		}

		// Fire the onAfterStoreUser trigger
		$dispatcher->trigger('onAfterStoreUser', array($this->user->getProperties(), false, null, $this->getError()));

		// Flush the variables from the session
		$mainframe->setUserState($this->option . 'token', null);

		// Redirect
		if (JRequest::getInt('no_html', 0))
		{
			echo json_encode(
				array(
					"success" => true,
					"redirect" => JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=account'))
				);
			exit();
		}
		else
		{
			// Redirect user to confirm view page
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=account'),
				JText::_('Password reset successful'),
				'passed'
			);
		}

		return;
	}

	//----------------------------------------------
	// Miscellaneous functions
	//----------------------------------------------

	/**
	 * Remove linked account
	 * 
	 * @return void
	 */
	private function _unlink()
	{
		// Import a few things
		ximport('Hubzero_User_Password');
		
		// Get the id of the account to be unlinked
		$hzal_id = JRequest::getInt('hzal_id', null);

		// Get instance
		$hzal = Hubzero_Auth_Link::find_by_id($hzal_id);

		// Determine what type of password change the user needs
		$hzup = Hubzero_User_Password::getInstance($this->member->get('uidNumber'));
		if(empty($hzup->passhash) && count(Hubzero_Auth_Link::find_by_user_id($this->member->get('uidNumber'))) <= 1)
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=account'),
				JText::_('PLG_MEMBERS_ACCOUNT_CANT_REMOVE_ONLY_ACCESS'),
				'warning'
			);
		}

		// Delete the auth_link
		if(!$hzal->delete())
		{
			JError::raiseError(500, JText::_('PLG_MEMBERS_UNLINK_FAILED'));
			return;
		}

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=account'),
			JText::_('PLG_MEMBERS_ACCOUNT_UNLINKED'),
			'passed'
		);
	}

	/**
	 * Get information about the password expiration
	 * 
	 * @return array - password expiration information
	 */
	private function getPassInfo()
	{
		$hzup = Hubzero_User_Password::getInstance($this->member->get('uidNumber'));

		// Check to see if password expiration is even enforced
		if(empty($hzup->passhash) || $hzup->shadowMax === NULL)
		{
			return false;
		}

		$chgtime = time();
		$chgtime = intval($chgtime / 86400);
		$diff    = ($hzup->shadowLastChange + $hzup->shadowMax) - $chgtime;

		if($diff > $hzup->shadowWarning)
		{
			$message_style = 'info';
		}
		else if($diff <= $hzup->shadowWarning && $diff > 0)
		{
			$message_style = 'warning';
		}
		else
		{
			$message_style = 'error';
		}

		return array("diff" => $diff, "warning" => $hzup->shadowWarning, "max" => $hzup->shadowMax, "message_style" => $message_style);
	}

	/**
	 * Upload SSH key
	 * 
	 * @return void
	 */
	private function _uploadKey()
	{
		// Import a few things
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		// Webdav path
		$base = DS . 'webdav' . DS . 'home';
		$user = DS . $this->member->get('username');
		$ssh  = DS . '.ssh';
		$auth = DS . 'authorized_keys';

		// Real home directory
		$homeDir = $this->member->get('homeDirectory');

		// First, make sure webdav is there and that the necessary folders are there
		if(!JFolder::exists($base))
		{
			JError::raiseError(500, JText::_('PLG_MEMBERS_ACCOUNT_KEY_UPLOAD_NOT_AVAILABLE'));
			return;
		}
		if(!JFolder::exists($homeDir))
		{
			// Try to create their home directory
			require_once(JPATH_ROOT . DS .'components' . DS . 'com_tools' . DS . 'models' . DS . 'mw.utils.php');
			$mwUtils = new MwUtils();
			if (!$mwUtils->createHomeDirectory($this->member->get('username')))
			{
				JError::raiseError(500, JText::_('PLG_MEMBERS_ACCOUNT_KEY_UPLOAD_NO_HOME_DIRECTORY'));
				return;
			}
		}
		if(!JFolder::exists($base.$user.$ssh))
		{
			// User doesn't have an ssh directory, so try to create one (with appropriate permissions)
			if(!JFolder::create($base.$user.$ssh, 0700))
			{
				JError::raiseError(500, JText::_('PLG_MEMBERS_ACCOUNT_KEY_UPLOAD_CREATE_FOLDER_FAILED'));
				return;
			}
		}

		// Get the form input
		$content = JRequest::getVar('keytext', '');

		// Write to the file
		if(!JFile::write($base.$user.$ssh.$auth, $content) && $content != '')
		{
			JError::raiseError(500, JText::_('PLG_MEMBERS_ACCOUNT_KEY_UPLOAD_WRITE_FAILED'));
			return;
		}

		// Set correct permissions on authorized_keys file
		JPath::setPermissions($base.$user.$ssh.$auth, '0600');

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=account'),
			JText::_('PLG_MEMBERS_ACCOUNT_KEY_UPLOAD_SUCCESSFUL'),
			'passed'
		);
	}

	/**
	 * Read SSH key
	 * 
	 * @return string - .ssh/authorized_keys file content
	 */
	private function readKey()
	{
		// Import a few things
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		// Webdav path
		$base = DS . 'webdav' . DS . 'home';
		$user = DS . $this->member->get('username');
		$ssh  = DS . '.ssh';
		$auth = DS . 'authorized_keys';

		// Real home directory
		$homeDir = $this->member->get('homeDirectory');

		$key = '';

		// First, make sure webdav is there and that the necessary folders are there
		if(!JFolder::exists($base))
		{
			// Not sure what to do here
			return $key = false;
		}
		if(!JFolder::exists($homeDir))
		{
			// Try to create their home directory
			require_once(JPATH_ROOT . DS .'components' . DS . 'com_tools' . DS . 'models' . DS . 'mw.utils.php');
			$mwUtils = new MwUtils();
			if (!$mwUtils->createHomeDirectory($this->member->get('username')))
			{
				return $key = false;
			}
		}
		if(!JFolder::exists($base.$user.$ssh))
		{
			// User doesn't have an ssh directory, so try to create one (with appropriate permissions)
			if (!JFolder::create($base.$user.$ssh, 0700))
			{
				return $key = false;
			}
		}
		if(!JFile::exists($base.$user.$ssh.$auth))
		{
			// Try to create their authorized keys file
			JFile::write($base.$user.$ssh.$auth, '');
			if (!JFile::exists($base.$user.$ssh.$auth))
			{
				return $key = false;
			}
			else
			{
				// Set correct permissions on authorized_keys file
				JPath::setPermissions($base.$user.$ssh.$auth, '0600');

				return $key;
			}
		}

		// Read the file contents
		$key = JFile::read($base.$user.$ssh.$auth);

		return $key;
	}

	/**
	 * Set redirect
	 * 
	 * @return void
	 */
	private function setRedirect($url, $msg=null, $type='message')
	{
		if ($msg !== null)
		{
			$this->addPluginMessage($msg, $type);
		}
		$this->redirect($url);
	}

	/**
	 * Save password change token to the database
	 * 
	 * @return bool - true if token save successful
	 */
	private function setToken($hashedToken)
	{
		// Create the database object and set the token
		$db     =& JFactory::getDBO();
		$query	= 'UPDATE #__users'
				. ' SET activation = ' . $db->Quote($hashedToken)
				. ' WHERE id = ' . (int) $this->user->get('id')
				. ' AND block = 0'; // Can't do this if they are blocked

		// Set the query
		$db->setQuery($query);

		// Save the token
		if (!$db->query())
		{
			JError::raiseError(500, JText::_('PLG_MEMBERS_ACCOUNT_DATABASE_ERROR_TOKEN_NOT_SAVED'));
			return;
		}

		return true;
	}

	/**
	 * Retrieve activation token from the database
	 * 
	 * @return object - id and activation token for user
	 */
	private function getToken()
	{
		// Create database object and check that token matches that of the user stored in the db
		$db =& JFactory::getDBO();
		$db->setQuery('SELECT id, activation FROM #__users WHERE block = 0 AND username = '.$db->Quote($this->user->get('username')));

		return $db->loadObject();
	}

	/**
	 * Send token email
	 * 
	 * @return bool - true if email send successfully
	 */
	private function sendEmail($token)
	{
		ximport('Hubzero_Toolbox');

		// Create the email with the new token
		$url      = rtrim(JURI::base(),'/');
		$return   = $url . JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&acitve=account&task=confirmtoken');
		$subject  = 'Set local password, confirmation token for ' . $url;
		$message  = 'You have requested to set your local password at ' . $url . "\n\n";
		$message .= 'Your reset token is: ' . $token;

		// Send the email
		if (!Hubzero_Toolbox::send_email($this->user->get('email'), $subject, $message))
		{
			JError::raiseError(500, JText::_('PLG_MEMBERS_ACCOUNT_CONFIRMATION_EMAIL_NOT_SENT'));
			return;
		}

		return true;
	}

	/**
	 * Check password fuction for ajax password rules validation
	 * 
	 * @return string - html rules section with classes for passed/error on each rule
	 */
	public function checkPass()
	{
		// Get the password rules
		ximport('Hubzero_Password_Rule');
		$password_rules = Hubzero_Password_Rule::getRules();

		$pw_rules = array();

		// Get the password rule descriptions
		foreach($password_rules as $rule)
		{
			if (!empty($rule['description']))
			{
				$pw_rules[] = $rule['description'];
			}
		}

		// Get the password
		$pw = JRequest::getVar('password1', null, 'post');

		// Validate the password
		if (!empty($pw))
		{
			$msg = Hubzero_Password_Rule::validate($pw, $password_rules, $this->member->get('username'));
		}
		else
		{
			$msg = array();
		}

		// Iterate through the rules and add the appropriate classes (passed/error)
		if (count($pw_rules) > 0) {
			foreach ($pw_rules as $rule)
			{
				if (!empty($rule))
				{
					if (!empty($msg) && is_array($msg)) {
						$err = in_array($rule, $msg);
					} else {
						$err = '';
					}
					$mclass = ($err)  ? ' class="error"' : 'class="passed"';
					echo "<li $mclass>".$rule."</li>";
				}
			}
			if (!empty($msg) && is_array($msg)) {
				foreach ($msg as $message)
				{
					if (!in_array($message, $pw_rules)) {
						echo '<li class="error">'.$message."</li>";
					}
				}
			}
		}

		// Exit - don't go any further (i.e. no joomla template stuff)
		exit;
	}
}
