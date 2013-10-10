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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Premis Helper
 * 
 * Long description (if any) ...
 */
class Hubzero_Register_Premis
{
	
	/**
	 * Check if hub member is linked to a PREMIS id
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      	string 		Premis user ID
	 * @return		mixed 		int: User ID if exists, bool False otherwise
	 */
	public static function getPremisUser($premisUsername)
	{
		$db = & JFactory::getDBO();
		
		$sql = 'SELECT `userId` FROM `#__premis_users` WHERE `premisId` = ';
		$sql .= $db->quote($premisUsername);
		$sql .= ' ORDER BY `id` LIMIT 1';
		
		$db->setQuery($sql);
		$db->query();		
						
		return $db->loadResult();
	}
	
	/**
	 * Check if hub member is linked to a PREMIS id
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      	string 		Hub user ID
	 * @return		mixed 		char: Latest Premis User enrolment if exists, bool: False otherwise
	 */
	public static function getPremisUserId($uId)
	{
		$db = & JFactory::getDBO();
		
		$sql = 'SELECT `premisEnrollmentId` FROM `#__premis_users` WHERE `userId` = ';
		$sql .= $db->quote($uId);
		$sql .= ' ORDER BY `id` LIMIT 1';
		
		$db->setQuery($sql);
		$db->query();		
						
		return $db->loadResult();
	}
	
	/**
	 * Short description for 'savePremisUser'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      	array 		Premis user info
	 * @param      	int 		Hub user ID
	 * @return		void
	 */
	public static function savePremisUser($uId, $premisId, $premisEnrollmentId)
	{
		$db = & JFactory::getDBO();
		
		$sql = 	'INSERT INTO `#__premis_users` SET ' .
				'`premisId` = ' . $db->quote($premisId) . ', ' .
				'`userId` = ' . $db->quote($uId) . ', ' .
				'`premisEnrollmentId` = ' . $db->quote($premisEnrollmentId);
				
		$db->setQuery($sql);
		//echo $db->_sql;
		$db->query();	
	}
	
	/**
	 * Short description for 'isPremisUser'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      	array 		Premis user info
	 * @param      	int 		Hub user ID
	 * @return		void
	 */
	public static function isPremisUser($uId)
	{
		$db = & JFactory::getDBO();
		
		$sql = 	'SELECT * FROM `#__premis_users` WHERE ' .
				'`userId` = ' . $db->quote($uId);
				
		$db->setQuery($sql);
		$db->query();	
		
		if ($db->getNumRows() > 0)
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Short description for 'savePremisActivity'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      	array 		Premis user info
	 * @param      	int 		Hub user ID
	 * @return		void
	 */
	public static function savePremisActivity($user, $courses)
	{
		$db = & JFactory::getDBO();
		
		$sql = 	'INSERT INTO `#__premis_log` SET ' .
				'`premisId` = ' . $db->quote($user['premisId']) . ', ' .
				'`lName` = ' . $db->quote($user['lName']) . ', ' .
				'`fName` = ' . $db->quote($user['fName']) . ', ' . 
				'`email` = ' . $db->quote($user['email']) . ', ' .
				'`casId` = ' . $db->quote($user['casId']) . ', ' .
				'`add` = ' . $db->quote($courses['add']) . ', ' .
				'`drop` = ' . $db->quote($courses['drop']) . ', ' .
				'`when` = NOW()';
		$db->setQuery($sql);
		//echo $db->_sql;
		$db->query();	
	}
	
	/**
	 * Short description for 'doRegistration'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      	array 		Premis user info
	 * @param      	int 		Hub user ID
	 * @return		void
	 */
	public static function doRegistration($user, $courses)
	{
		$return['status'] = 'ok';
		
		// Check all minimally required data
		if (	(empty($user['casId']) && empty($user['password'])) || 
				(empty($user['premisId']) && empty($user['casId'])) || 
				(empty($user['email'])) || 
				(empty($courses['add']) && empty($courses['drop']))
			)
		{
			$return['status'] = 'error';
			$return['code'] = 400;
			$return['message'] = 'Some required data missing. Please check the API specs.';
			return $return;
		}
		
		// Clean and parse add and drop requests
		
		if (!empty($courses['add'])) {
			$add = explode(',', $courses['add']);
		}
		if (!empty($courses['drop'])) {
			$drop = explode(',', $courses['drop']);
		}
		
		// Parse PREMIS value and extract the section values
		$addValues = array();
		foreach ($add as $val)
		{
			$tmp = explode('section', $val);
			$addValues[] = $tmp[1];
		}
		
		$add = $addValues;		
		//print_r($add); die;	
		
		$dropValues = array();
		foreach ($drop as $val)
		{
			$tmp = explode('section', $val);
			$dropValues[] = $tmp[1];
		}
		
		$drop = $dropValues;		
		//print_r($drop); die;	
		
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
				
		// -- if no Purdue ID match -- match the PREMIS ID. Is it needed?
		/*
		if (empty($userId))
		{
			// do the PREMIS ID match
			$userId = Hubzero_Register_Premis::getPremisUser($user['premisId']);
		}
		*/		
		
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
					
					$return['status'] = 'error';
					$return['code'] = 400;
					$return['message'] = 'Some required data missing. Please check the API specs.';
					return $return;
				}
				
				foreach ($xregistration->_invalid as $k => $val)
				{
					// ignore weak password message
					if ($k == 'password' || $k == 'confirmPassword')
					{
						continue;	
					}
					
					$return['status'] = 'error';
					$return['code'] = 400;
					$return['message'] = 'Bad data. Please check the API specs.';
					return $return;
				}				
			}			
			
			//ximport('Hubzero_Factory');
			jimport('joomla.plugin.helper');
			
			//$xprofile =& Hubzero_Factory::getProfile();

			// Get some settings
			$jconfig =& JFactory::getConfig();
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
					//$result = Hubzero_User_Password::changePassword($target_xprofile->get('username'), $xregistration->get('password'), true);
					Hubzero_User_Password::changePasshash($target_xprofile->get('username'), $xregistration->get('password'));
				}
				$userId = $target_juser->get('id');
				
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
				$return['status'] = 'error';
				$return['code'] = 500;
				$return['message'] = 'Failed to create a new user.';
				return $return;
			}
		}
		else
		{
			// Update profile
			self::doProfileUpdate($user);	
		}
		
		// Save Premis info
		Hubzero_Register_Premis::savePremisActivity($user, $courses);
		Hubzero_Register_Premis::savePremisUser($userId, $user['premisId'], $user['premisEnrollmentId']);
		
		// Do we have a user ID?
		if (empty($userId))
		{
			$return['status'] = 'error';
			$return['code'] = 500;
			$return['message'] = 'Registration failed. Reason unknown.';
			return $return;
		}
		
		// Do the adds/drops
		
		// Section ID is always provided
		//if (!empty($courses['lookupByOfferingId']) && $courses['lookupByOfferingId']

		//include_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'offering.php');
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'section.php');
		
		if (!empty($add))
		{
			foreach ($add as $sectionId) {
				$section = CoursesModelSection::getInstance($sectionId);
				
				if (!$section->get('offering_id'))
				{
					$return['status'] = 'error';
					$return['code'] = 400;
					$return['message'] = 'Error: Section not found';
					return $return;
				}
												
				$section->add($userId);
				if ($section->getError())
				{
					$return['status'] = 'error';
					$return['code'] = 500;
					$return['message'] = 'Error: ' . $section->getError();
					return $return;
				}
			}
		}
	
		if (!empty($drop))
		{
			foreach ($drop as $sectionId) {
					
				$section = CoursesModelSection::getInstance($sectionId);
				
				if (!$section->get('offering_id'))
				{
					$return['status'] = 'error';
					$return['code'] = 400;
					$return['message'] = 'Error: Section not found';
					return $return;
				}
				
				$section->remove($userId);									
				if($section->getError())
				{
					$return['status'] = 'error';
					$return['code'] = 500;
					$return['message'] = 'Error: ' . $section->getError();
					return $return;
				}
			}
		}
		
		/*
		// Aliases are provided (change)
		else {
			
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');
			
			if (!empty($add))
			{
				foreach ($add as $courseId) {
					
					$course = CoursesModelCourse::getInstance($courseId);
					
					if (!$course->offerings()->count()) {
						$return['status'] = 'error';
						$return['code'] = 400;
						$return['message'] = 'Bad course id.';
						return $return;
					}
									
					// Get to the first and probably the only offering
					$offering = $course->offerings()->current();
					$offering->add($userId);				
				}
			}
		
			if (!empty($drop))
			{
				foreach ($drop as $courseId) {
						
					$course = CoursesModelCourse::getInstance($courseId);
					
					//print_r($course->offerings()->count()); die;
					
					$course->offerings()->total();
					
					if (!$course->offerings()->total()) {
						$return['status'] = 'error';
						$return['code'] = 400;
						$return['message'] = 'Bad course id.';
						return $return;
					}
					
					// Get to the first and probably the only offering
					$offering = $course->offerings()->current();
					$offering->remove($userId);					
				}
			}
		}
		*/	
				
						
		$return['message'] = 'User ID ' . $userId . ' registered.';
		$return['code'] = 201;
		return $return;		
	}
	
	/**
	 * Short description for 'doProfileUpdate'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      	array 		Premis user info
	 * @return		void
	 */
	public static function doProfileUpdate($user)
	{
		$return['status'] = 'ok';
		
		// Check all minimally required data
		if (empty($user['email']) || ( empty($user['fName']) && empty($user['lName']) && empty($user['password'])))
		{
			$return['status'] = 'error';
			$return['code'] = 400;
			$return['message'] = 'Some required data missing. Please check the API specs.';
			return $return;
		}
		
		if ((!empty($user['fName']) || !empty($user['lName'])) && (empty($user['fName']) || empty($user['lName'])))
		{
			$return['status'] = 'error';
			$return['code'] = 400;
			$return['message'] = 'Please provide both first and last names.';
			return $return;
		}
		
		// ** Update profile
		
		// Find user match by email
		ximport('Hubzero_Registration');
			
		// do the email match	
		$userId = Hubzero_Registration::getEmailId($user['email']);	
		
		// Error if not found
		if (!$userId)
		{
			$return['status'] = 'error';
			$return['code'] = 400;
			$return['message'] = 'Bad user ID.';
			return $return;
		}
				
		// Uppdate profile
		ximport('Hubzero_User_Profile');
		$userProfile = Hubzero_User_Profile::getInstance($userId);
		$result = is_object($userProfile);
				
		// Did we successully get an account?
		if ($result) 
		{
			jimport('joomla.plugin.helper');
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
				//$result = Hubzero_User_Password::changePassword($userProfile->get('username'), $xregistration->get('password'), true);
				Hubzero_User_Password::changePasshash($userProfile->get('username'), $xregistration->get('password'));
			}
			
			// update Premis 
			
		}		
		
		// Success
		$return['message'] = 'User profile updated.';
		$return['code'] = 201;
		return $return;	
	}
	
	/**
	 * Short description for 'doProfileDelete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      	array 		Premis user info
	 * @return		void
	 */
	public static function doProfileDelete($user)
	{
		$return['status'] = 'ok';
		
		// Check all minimally required data
		if (empty($user['email']))
		{
			$return['status'] = 'error';
			$return['code'] = 400;
			$return['message'] = 'Some required data missing. Please check the API specs.';
			return $return;
		}
		
		// Find user match by email
		ximport('Hubzero_Registration');
			
		// do the email match	
		$userId = Hubzero_Registration::getEmailId($user['email']);	
		
		// Error if not found
		if (!$userId)
		{
			$return['status'] = 'error';
			$return['code'] = 400;
			$return['message'] = 'Bad user email.';
			return $return;
		}
		
		// Check if profile exists and it is a Premis created profile
				
		// ** Delete profile
		if(!self::isPremisUser($userId))
		{
			$return['status'] = 'error';
			$return['code'] = 401;
			$return['message'] = 'You are not authorized to delete this user.';
			return $return;
		}
		
		$user =& JUser::getInstance((int)$userId);
		$user->delete();
		
		// Success
		$return['message'] = 'User profile deleted.';
		$return['code'] = 201;
		return $return;	
	}
}