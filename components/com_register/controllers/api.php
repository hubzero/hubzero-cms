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
 * @author    Ilya Shunko <ishunko@purdue.edu>
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
		
		$this->logFile = JPATH_ROOT . DS . 'site' . DS . 'com_register' . DS . 'premis_registrations.log';

		// Get the request type
		$this->format = JRequest::getVar('format', 'application/json');

		// Get a database object
		$this->db = JFactory::getDBO();
		
		// Debug mode
		$this->debug = true;

		// Switch based on entity type and action
		switch($this->segments[0])
		{
			// Registration
			case 'premisRegister': 		$this->premisRegister();			break;
			case 'premisUpdateProfile':	$this->premisUpdateProfile();		break;
			case 'premisDeleteProfile':	$this->premisDeleteProfile();		break;

			default:                    $this->method_not_found();         	break;
		}
	}

	/**
	 * Premis registration request handling API
	 * 
	 * @return 	OK on success
	 */
	private function premisRegisterApi()
	{
		$this->setMessageType($this->format);
		
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = Hubzero_User_Profile::getInstance($userid);
		//make sure we have a user
		if ($result === false)	return $this->not_found();
		
	}
	
	/**
	 * Premis registration request handling
	 *
	 * @param		void 
	 * @return 		void
	 */
	private function premisRegister()
	{
		// Get the authentication info		
		if (!$this->premisAuthenticate())
		{
			if ($this->debug)
			{
				$debug = $this->premisError;
				$this->errorMessage(400, 'Unable to authenticate. ' . $debug); 
			}
			return;
		}
		
		// Get the POST data from request
		$user['fName'] = JRequest::getVar('fName', '', 'post');
		$user['lName'] = JRequest::getVar('lName', '', 'post');
		$user['email'] = JRequest::getVar('email', '', 'post');
		$user['casId'] = JRequest::getVar('casId', '', 'post');
		$user['premisId'] = JRequest::getVar('premisId', '', 'post');
		$user['password'] = JRequest::getVar('password', '', 'post');
		$user['premisEnrollmentId'] = JRequest::getVar('premisEnrollmentId', '', 'post');
		
		$courses['add'] = JRequest::getVar('addRegistration', '', 'post');
		$courses['drop'] = JRequest::getVar('dropRegistration', '', 'post');
		
		// Include helper
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_register' . DS . 'helpers' . DS . 'Premis.php');

		// Handle registration request
		$return = Hubzero_Register_Premis::doRegistration($user, $courses);
		
		// Check request status and display a corresponding message
		if ($return['status'] != 'ok')		
		{
			$this->errorMessage($return['code'], $return['message']);	
		}
		else {
			// Success message
			$this->successMessage($return['code'], $return['message']);	
		}
				
	}
		
	private function premisUpdateProfile()
	{
		// Get the authentication info		
		if (!$this->premisAuthenticate())
		{
			if ($this->debug)
			{
				$debug = $this->premisError;
				$this->errorMessage(400, 'Unable to authenticate. ' . $debug); 
			}
			return;
		}
		
		$user['email'] = JRequest::getVar('email', '', 'post');
		$user['fName'] = JRequest::getVar('fName', '', 'post');
		$user['lName'] = JRequest::getVar('lName', '', 'post');
		$user['password'] = JRequest::getVar('password', '', 'post');
		
		/* Testing
		$user['email'] = 'ilya@shunko.com';
		$user['fName'] = 'Илья';
		$user['lName'] = 'Шунько';
		$user['password'] = ''; //eblan
		*/
		
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_register' . DS . 'helpers' . DS . 'Premis.php');
		$return = Hubzero_Register_Premis::doProfileUpdate($user);			
	}
	
	private function premisDeleteProfile()
	{
		// Get the authentication info		
		if (!$this->premisAuthenticate())
		{
			if ($this->debug)
			{
				$debug = $this->premisError;
				$this->errorMessage(400, 'Unable to authenticate. ' . $debug); 
			}
			return;
		}
		
		$user['email'] = JRequest::getVar('email', '', 'post');
		
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_register' . DS . 'helpers' . DS . 'Premis.php');
		$return = Hubzero_Register_Premis::doProfileDelete($user);	
		
		// Check request status and display a corresponding message
		if ($return['status'] != 'ok')		
		{
			$this->errorMessage($return['code'], $return['message']);	
		}
		else {
			// Success message
			$this->successMessage($return['code'], $return['message']);	
		}		
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
		
		// debug stuff
		$debug = 'IP: ' . $_SERVER['REMOTE_ADDR'] . "\n\n";
		$debug .= 'POST: **** ' . serialize($_POST) . ' ***';
		$debug .= "\n\n";
		$debug .= 'GET: **** ' . serialize($_GET) . ' ***';

		mail('ishunko@purdue.edu', 'API authentication', $debug);
		
		//return true;

		if (empty($_POST)) 
		{
			$this->premisError = 'Bad data.';
			return false;
		}
		
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
		if ($_SERVER['REMOTE_ADDR'] != '172.22.31.205' && 
			$_SERVER['REMOTE_ADDR'] != '128.46.23.36' && 
			$_SERVER['REMOTE_ADDR'] != '128.46.21.242' )
		{
			$this->premisError = 'Bad IP address.';
			return false;
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
		$this->dispatchMessage($code, $message, 'error');
		
		/*
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
		*/
	}
	
	private function successMessage($code, $message)
	{
		$this->dispatchMessage($code, $message);
		
		/*
		//build code and message
		$object = new stdClass();
		$object->ok->code = $code;
		$object->ok->message = $message;
				
		$this->setMessage($object);
		*/
	}
	
	/* *****************************************************************************************************
	   *****************************************************************************************************
	   *****************************************************************************************************
	   									Combine error and success messages and
											have the method to log _POST
											 and error/success messages
	   *****************************************************************************************************
	   *****************************************************************************************************
	   *****************************************************************************************************/
	   
	private function dispatchMessage($code, $message, $type = 'ok')
	{
		//build code and message
		$object = new stdClass();
		
		if ($type == 'error')
		{		
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
		
		elseif ($type == 'ok')
		{				
			$object->ok->code = $code;
			$object->ok->message = $message;
					
			$this->setMessage($object);
		}
		
		// Do logging: POST data and message (error or ok)
		// Server response: $type
		// Code: $code
		// Message: $message
		
		$message = 'Code: ' . $code . ', ' . $message;
		$message .= "\n";
		$message .= print_r(serialize($_POST), 1);
		$message .= "\n";
		
		//echo $this->logFile; die;
		
		ximport('Hubzero_Log');

		$hzl = new Hubzero_Log();
		$handler = new Hubzero_Log_FileHandler($this->logFile);
		
		$hzl->attach(HUBZERO_LOG_INFO, $handler);
		$hzl->attach(HUBZERO_LOG_ERR, $handler);
		$hzl->attach(HUBZERO_LOG_NOTICE, $handler);
		
		if ($type == 'error')
		{
			$log = $hzl->logError($message);			
		}
		else 
		{
			$log = $hzl->logInfo($message);
		}
		
		
	}
											 
}
