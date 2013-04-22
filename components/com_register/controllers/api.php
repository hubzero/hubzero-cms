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
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

JLoader::import('Hubzero.Api.Controller');

/**
 * API controller for the time component
 */
class RegisterControllerApi extends Hubzero_Api_Controller
{
	/**
	 * Execute!
	 * 
	 * @return void
	 */
	function execute()
	{
		// Import some Joomla libraries
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');

		// Get the request type
		$this->format = JRequest::getVar('format', 'application/json');

		// Get a database object
		$this->db = JFactory::getDBO();

		// Switch based on entity type and action
		switch($this->segments[0])
		{
			// Registration
			case 'premisRegister2':		$this->premisRegistration();		break;
			case 'premisRegister': 		$this->premisRegister();			break;
			case 'premisUpdateProfile':	$this->premisUpdateProfile();		break;

			default:                    $this->method_not_found();         	break;
		}
	}

	/**
	 * Premis registration request handling
	 * 
	 * @return 	OK on success
	 */
	private function premisRegistration()
	{
		$this->setMessageType($this->format);
		
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = Hubzero_User_Profile::getInstance($userid);
		//make sure we have a user
		if ($result === false)	return $this->not_found();
		
		$user['fName'] = JRequest::getVar('fName', '', 'post');
		$user['lName'] = JRequest::getVar('lName', '', 'post');
		$user['email'] = JRequest::getVar('email', '', 'post');
		$user['casId'] = JRequest::getVar('casId', '', 'post');
		$user['premisId'] = JRequest::getVar('premisId', '', 'post');
		$user['password'] = JRequest::getVar('password', '', 'post');
		
		$courses['add'] = JRequest::getVar('addRegistration', '', 'post');
		$courses['drop'] = JRequest::getVar('dropRegistration', '', 'post');
		
	}
	
	private function premisRegister()
	{
		// Get the authentication info		
		if (!$this->premisAuthenticate())
		{
			if (1)
			{
				$debug = $this->premisError;
			}
			
			$this->errorMessage(404, 'Can not authenticate. ' . $debug); 
			return;
		}
		
		$user['fName'] = JRequest::getVar('fName', '', 'post');
		$user['lName'] = JRequest::getVar('lName', '', 'post');
		$user['email'] = JRequest::getVar('email', '', 'post');
		$user['casId'] = JRequest::getVar('casId', '', 'post');
		$user['premisId'] = JRequest::getVar('premisId', '', 'post');
		$user['password'] = JRequest::getVar('password', '', 'post');
		
		$courses['add'] = JRequest::getVar('addRegistration', '', 'post');
		$courses['drop'] = JRequest::getVar('dropRegistration', '', 'post');
				
		// Check all minimally required data
		if ((empty($user['premisId']) && empty($user['casId'])) || empty($user['email']) || (empty($courses['add']) && empty($courses['drop'])))
		{
			$this->errorMessage(400, 'Some required data missing. Please check the API specs.');
			return;
		}
		
		// Clean and parse add and drop requests		
		$courses['add'] = preg_replace("/[^A-Za-z0-9_,\.]/", '', $courses['add']);
		$courses['drop'] = preg_replace("/[^A-Za-z0-9_,\.]/", '', $courses['drop']);		
		$add = explode(',', $courses['add']);
		$drop = explode(',', $courses['drop']);
		
		// *** Check if there is already a hub user
		
		// Initialize matched hub user ID
		$userId = NULL;
				
		// first check if there is a Purdue ID match
		if (!empty($user['casId']))
		{
			// do the CAS match	
			ximport('Hubzero_Auth_Domain');
			ximport('Hubzero_Auth_Link');
			
			$authDomain = Hubzero_Auth_Domain::getInstance('authentication', 'pucas', NULL);
			$auth = Hubzero_Auth_Link::getInstance($authDomain->__get('id'), $user['casId']);
			
			if (!empty($auth))
			{
				$userId = $auth->__get('user_id');
			}
		}	
				
		// -- if no Purdue ID match -- match the PREMIS ID
		if (empty($userId))
		{
			// do the PREMIS ID match
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'helpers' . DS . 'Premis.php');
			$userId = Hubzero_Course_Premis::getPremisUser($user['premisId']);
		}
		
		
		// -- if no match -- match the email
		if (empty($userId))
		{
			ximport('Hubzero_Registration');
			
			// do the email match	
			$userId = Hubzero_Registration::getEmailId($user['email']);	
		}
		
		// No hub account found -- create new account set the password 
		if (empty($userId))
		{
			// Create new account
			
			// Generate a username
			if (!empty($user['casId']))
			{
				$preferredUsername = $user['casId'];
			}
			else {
				$preferredUsername = $user['email'];
			}
			$user['username'] = Hubzero_Registration::generateUsername($preferredUsername);
			
			ximport('Hubzero_User_Password');
			
			// Instantiate a new registration object
			$xregistration = new Hubzero_Registration();
			
			$xregistration->set('login', $user['username']);
			$xregistration->set('name', $user['fName'] . ' ' . $user['lName']);
			$xregistration->set('email', $user['email']);
			$xregistration->set('confirmEmail', $user['email']);
			if (!empty($user['password']))
			{
				$xregistration->set('password', $user['password']);
				$xregistration->set('confirmPassword', $user['password']);
			}			
	
			// Perform field validation
			if (!$xregistration->check('proxy')) 
			{				
				foreach ($xregistration->_missing as $k => $val)
				{
					// ignore password if CAS
					if (($k == 'password' || $k == 'confirmPassword') && !empty($user['casId']))
					{
						continue;	
					}
					
					$this->errorMessage(400, 'Some required data missing. Please check the API specs.');
					return;
				}
				
				foreach ($xregistration->_invalid as $k => $val)
				{
					// ignore weak password message
					if ($k == 'password' || $k == 'confirmPassword')
					{
						continue;	
					}
					$this->errorMessage(400, 'Bad data. Please check the API specs.');
					return;
				}				
			}			
			
			//ximport('Hubzero_Factory');
			jimport('joomla.plugin.helper');
			
			//$xprofile =& Hubzero_Factory::getProfile();

			// Get some settings
			$jconfig =& JFactory::getConfig();
			$this->jconfig = $jconfig;
			$params =& JComponentHelper::getParams('com_members');
			$hubHomeDir = rtrim($params->get('homedir'), '/');
			
			jimport('joomla.application.component.helper');
			$config   =& JComponentHelper::getParams('com_users');
			$usertype = $config->get('new_usertype', 'Registered');
	
			$acl =& JFactory::getACL();
	
			// Create a new Joomla user
			$target_juser = new JUser();
			$target_juser->set('id', 0);
			$target_juser->set('name', $xregistration->get('name'));
			$target_juser->set('username', $xregistration->get('login'));
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
				$target_xprofile->set('emailConfirmed', 1);
				
				if (isset($_SERVER['REMOTE_HOST'])) 
				{
					$target_xprofile->set('regHost', JRequest::getVar('REMOTE_HOST','','server'));
				}
	
				$target_xprofile->set('registerDate', date('Y-m-d H:i:s'));
	
				// Update the account
				$result = $target_xprofile->update();
			}
	
			if ($result) 
			{
				if (!empty($user['password']))
				{
					$result = Hubzero_User_Password::changePassword($target_xprofile->get('username'), $xregistration->get('password'), true);
				}
				$userId = $target_juser->get('id');
				
				// Associate newly created profile with Premis account ID and save all info
				if (!empty($user['premisId']))
				{
					Hubzero_Course_Premis::savePremisUser($user, $userId);
				}
				
				// Associate newly created profile with CAS account ID
				if( !empty($user['casId'])) 
				{
					$authDomain = Hubzero_Auth_Domain::getInstance('authentication', 'pucas', NULL);
					
					$auth = Hubzero_Auth_Link::createInstance($authDomain->__get('id'), $user['casId']);
					$auth = Hubzero_Auth_Link::getInstance($authDomain->__get('id'), $user['casId']);
					$auth->__set('user_id', $userId);
					$auth->__set('email', $target_xprofile->get('email'));
					$auth->update();
				}

			}
			
			// Did we successully create/update an account?
			if (!$result) 
			{
				$this->errorMessage(500, 'Failed to create a new user.');
				return;
			}
		}
		
		// Do we have a user ID?
		if (empty($userId))
		{
			$this->errorMessage(500, 'Registration failed. Reason unknown.');
			return;
		}
		
		// Do the adds/drops
		
		// Success message
		$this->successMessage(201, 'Success. User ID ' . $userId . ' registered.');
		
	}
		
	private function premisUpdateProfile()
	{
		// Get the authentication info		
		if (0 && !$this->premisAuthenticate())
		{
			if (1)
			{
				$debug = $this->premisError;
			}
			
			$this->errorMessage(404, 'Can not authenticate. ' . $debug); 
			return;
		}
		
		$user['premisId'] = JRequest::getVar('premisId', '', 'post');
		$user['fName'] = JRequest::getVar('fName', '', 'post');
		$user['lName'] = JRequest::getVar('lName', '', 'post');
		$user['password'] = JRequest::getVar('password', '', 'post');
		
		/* Testing
		$user['premisId'] = 'zuki';
		$user['fName'] = 'Илья';
		$user['lName'] = 'Шунько';
		$user['password'] = ''; //eblan
		*/
		
		// Check all minimally required data
		if (empty($user['premisId']) || ( empty($user['fName']) && empty($user['lName']) && empty($user['password'])))
		{
			$this->errorMessage(400, 'Some required data missing. Please check the API specs.');
			return;
		}
		
		if ((!empty($user['fName']) || !empty($user['lName'])) && (empty($user['fName']) || empty($user['lName'])))
		{
			$this->errorMessage(400, 'Please provide both first and last names.');
			return;
		}
		
		// ** Update profile
		
		// Find premis user match
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_register' . DS . 'helpers' . DS . 'Premis.php');
		$userId = Hubzero_Register_Premis::getPremisUser($user['premisId']);
		
		// Error if not found
		if (!$userId)
		{
			$this->errorMessage(400, 'Bad user ID.');
			return;	
		}
				
		// Uppdate profile
		$userProfile = Hubzero_User_Profile::getInstance($userId);
		$result = is_object($userProfile);
				
		// Did we successully get an account?
		if ($result) 
		{
			jimport('joomla.plugin.helper');
			ximport('Hubzero_Registration');
			$xregistration = new Hubzero_Registration();
			
			if (!empty($user['fName']))
			{
				$xregistration->set('name', $user['fName'] . ' ' . $user['lName']);
			}
			if (!empty($user['password']))
			{
				$xregistration->set('password', $user['password']);
			}
			
			$userProfile->loadRegistration($xregistration);
			$result = $userProfile->update();
			
			if (!empty($user['password']))
			{
				ximport('Hubzero_User_Password');
				$result = Hubzero_User_Password::changePassword($userProfile->get('username'), $xregistration->get('password'), true);
			}
		}		
		
		// Success
		$this->successMessage(201, 'Success. User profile updated.');
	}
	
	//--------------------------
	// Miscelaneous methods
	//--------------------------

	/**
	 * Default method - not found
	 * 
	 * @return 404, method not found error
	 */
	private function method_not_found()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Set the error message
		$this->_response->setErrorMessage(404, 'Not found');
		return;
	}
	
	private function premisAuthenticate()
	{
		// Request must be made to HTTPS
		if (!isset($_SERVER['HTTPS']))
		{
			$this->premisError = 'Not a HTTPS request.';
			return false;
		}
		
		// Secret string
		if (JRequest::getVar('ss', '', 'post') != 'VezefruchASpEdruvE_RAmE4pesWep!A')		
		{
			$this->premisError = 'Bad secret string.';
			return false;
		}
		
		// Check IP
		if ($_SERVER['REMOTE_ADDR'] != '28.46.16.98')
		{
			/*
			$this->premisError = 'Bad IP address.';
			return false;
			*/
		}
		
		return true;		
	}


	/**
	 * Short description for 'not_found'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	private function not_found()
	{
		$response = $this->getResponse();
		$response->setErrorMessage(404, 'Not Found');
	}
	
	/**
	 * Method to report errors. creates error node for response body as well
	 *
	 * @param	$code		Error Code
	 * @param	$message	Error Message
	 * @param	$format		Error Response Format
	 *
	 * @return     void
	 */
	private function errorMessage($code, $message)
	{
		//build error code and message
		$object = new stdClass();
		$object->error->code = $code;
		$object->error->message = $message;
		
		$exclude = array('message');
		
		foreach	($_POST as $k => $val)
		{
			if (!in_array($k, $exclude))
			{
				$object->debug->$k = $val;	
			}
		}
		
		//set http status code and reason
		$response = $this->getResponse();
		$response->setErrorMessage($object->error->code, $object->error->message, $object->error->message);
		
		//add error to message body
		$this->setMessage($object);
	}
	
	private function successMessage($code, $message)
	{
		//build error code and message
		$object = new stdClass();
		$object->ok->code = $code;
		$object->ok->message = $message;
				
		$this->setMessage($object);
	}
}
