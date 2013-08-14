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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Passport badges provider
 * 
 * Long description (if any) ...
 */
class Hubzero_Badges_Passport_BadgesProvider
{	
	private $credentials = false;
	private $oauth;
	
	const passportApiEndpoint = 'https://api.openpassport.org/1.0.0/';
	//const passportApiEndpoint = 'https://dev.api.openpassport.org/1.0.0/';
	
	/**
	 * Constructor
	 * 
	 * @param 	void
	 * @return  void
	 */
	public function __construct()
	{		
	}
	
	/**
	 * Set credentials
	 * 
	 * @param 	object passportCredentials
	 * @return  void
	 */
	public function setCredentials($passportCredentials) 
	{
		$this->credentials = $passportCredentials;
		
		$this->oauth = new OAuth($this->credentials->consumer_key, $this->credentials->consumer_secret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_FORM);
		
		/*
		$params['x_auth_username'] = $this->credentials->username;
		$params['x_auth_password'] = $this->credentials->password;
		$params['x_auth_mode'] = 'client_auth';
				
		$this->oauth->fetch(Hubzero_Badges_Passport_BadgesProvider::passportApiEndpoint . "access_token", $params,  OAUTH_HTTP_METHOD_POST);
		
		// get token and secret and set them for future requests
		parse_str($oauth->getLastResponse(), $access);
		$this->oauth->setToken($access['oauth_token'], $access['oauth_token_secret']);
		
		*/
	}
	
	/**
	 * Create a new badge
	 * 
	 * @param 	object		data: badge info. Must have the following:
	 						$data['Name'] = 'Badge name';
							$data['Description'] = 'Badge description';
							$data['CriteriaUrl'] = 'Badge criteria URL';
							$data['Version'] = 'Version';
							$data['BadgeImageUrl'] = 'URL of the bagde image: suare at least 460px x 460px';
	 * @return  int			Freshly created badge ID
	 */
	public function createBadge($data) 
	{		
		if (!$this->credentialsSet())
		{
			throw new Exception('You need to set the credentials first.');
		}	
		
		$data['IssuerId'] = $this->credentials->issuerId;
		
		$data = json_encode($data);
		
		$this->oauth->setAuthType(OAUTH_AUTH_TYPE_AUTHORIZATION); 
		
		try 
		{
			$this->oauth->fetch(Hubzero_Badges_Passport_BadgesProvider::passportApiEndpoint . 'badges/', $data, OAUTH_HTTP_METHOD_POST, array('Content-Type'=>'application/json'));
		}
		catch (Exception $e) 
		{
		}
						
		$badge = json_decode($this->oauth->getLastResponse());
						
		if (empty($badge->Id) || !$badge->Id) 
		{
			throw new Exception($badge->message);
		}
		
		return($badge->Id);
	}
	
	/**
	 * Grant badges to users
	 * 
	 * @param 	object		Badge info: ID, Evidence URL
	 * @param 	mixed		string (for single user) or array (for multiple users) of user email addresses
	 * @return  void
	 */
	public function grantBadge($badge, $users) 
	{
		if (!$this->credentialsSet())
		{
			throw new Exception('You need to set the credentials first.');
		}
		
		if (!is_array($users))
		{
			$users = array($users);	
		}

		$assertions = array();

		foreach ($users as $user)
		{
			$data = array();
			
			$data['BadgeId'] = $badge->id;
			$data['EvidenceUrl'] = $badge->evidenceUrl;
			$data['EmailAddress'] = $user;
			$data['ClientId'] = $this->credentials->clientId;
			
			$assertions[] = $data;
			unset($data);
		}
		
		$assertionsData = json_encode($assertions);
		
		$this->oauth->setAuthType(OAUTH_AUTH_TYPE_AUTHORIZATION);
		try 
		{
			$this->oauth->fetch(Hubzero_Badges_Passport_BadgesProvider::passportApiEndpoint . "assertions/", $assertionsData, OAUTH_HTTP_METHOD_POST, array('Content-Type'=>'application/json'));
		}
		catch (Exception $e)
		{
		}
		
		$assertion = json_decode($this->oauth->getLastResponse());
		
		foreach ($assertion as $ass)
		{	
			if (empty($ass->Id) || !$ass->Id)
			{
				throw new Exception($ass->message);
			}
		}		
	}
			
	/**
	 * Check if credentials are set
	 * 
	 * @param 	void
	 * @return  bool
	 */
	private function credentialsSet() 
	{
		if (empty($this->credentials))
		{
			return false;
		}
		return true;
	}

}