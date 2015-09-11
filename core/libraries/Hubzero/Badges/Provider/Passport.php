<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Badges\Provider;

use Exception;

/**
 * Passport badges provider
 */
class Passport implements ProviderInterface
{
	/**
	 * API endpoint
	 *
	 * @var  string
	 */
	const passportApiEndpoint = 'https://api.openpassport.org/1.0.0/';

	/**
	 * API badges URL
	 *
	 * @var  string
	 */
	const passportBadgesUrl   = 'https://www.openpassport.org/MyBadges';

	/**
	 * API claim URL
	 *
	 * @var  string
	 */
	const passportClaimUrl    = 'https://www.openpassport.org/MyBadges/Pending';

	/**
	 * API denied URL
	 *
	 * @var  string
	 */
	const passportDeniedUrl   = 'https://www.openpassport.org/MyBadges/Denied';

	/**
	 * Credentials
	 *
	 * @var  array
	 */
	private $credentials = false;

	/**
	 * Request connection
	 *
	 * @var  resource
	 */
	private $request = NULL;

	/**
	 * Request type
	 *
	 * @var  string
	 */
	private $request_type = 'oauth';

	/**
	 * Constructor
	 * 
	 * @param   string  $request_type  Request type
	 * @return  void
	 */
	public function __construct($request_type = 'oauth')
	{
		$this->request_type = $request_type;
	}

	/**
	 * Set credentials
	 * 
	 * @param   object  $passportCredentials
	 * @return  void
	 */
	public function setCredentials($passportCredentials)
	{
		$this->credentials = $passportCredentials;

		// Setup request, based on type
		if ($this->request_type == 'oauth')
		{
			$this->request = new \OAuth($this->credentials->consumer_key, $this->credentials->consumer_secret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_FORM);

			/*$params['x_auth_username'] = $this->credentials->username;
			$params['x_auth_password'] = $this->credentials->password;
			$params['x_auth_mode']     = 'client_auth';

			$this->request->fetch(self::passportApiEndpoint . "access_token", $params,  OAUTH_HTTP_METHOD_POST);

			// Get token and secret and set them for future requests
			parse_str($this->request->getLastResponse(), $access);
			$this->request->setToken($access['oauth_token'], $access['oauth_token_secret']);*/
		}
		else if ($this->request_type == 'curl')
		{
			// Use curl as fallback/alternative to oauth
			$this->request = curl_init();
			curl_setopt($this->request, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($this->request, CURLOPT_USERPWD, $this->credentials->consumer_key . ":" . $this->credentials->consumer_secret);
			curl_setopt($this->request, CURLOPT_TIMEOUT, 30);
			curl_setopt($this->request, CURLOPT_POST, 1);
		}
		else
		{
			throw new Exception('Unsupported request type');
		}
	}

	/**
	 * Close connection
	 *
	 * @return  void
	 */
	public function destroy()
	{
		if ($this->request_type == 'oauth' && is_a($this->request, 'oauth'))
		{
			// Do nothing?
		}
		else if ($this->request_type == 'curl' && get_resource_type($this->request) == 'curl')
		{
			curl_close($this->request);
		}
		else
		{
			throw new Exception('Unsupported request type');
		}
	}

	/**
	 * Create a new badge
	 * 
	 * @param   array    $data  badge info. Must have the following:
	 *                          $data['Name']          = 'Badge name';
	 *                          $data['Description']   = 'Badge description';
	 *                          $data['CriteriaUrl']   = 'Badge criteria URL';
	 *                          $data['Version']       = 'Version';
	 *                          $data['BadgeImageUrl'] = 'URL of the badge image: square at least 450px x 450px';
	 * @return  integer  Freshly created badge ID
	 */
	public function createBadge($data)
	{
		if (!$this->credentialsSet())
		{
			throw new Exception('You need to set the credentials first.');
		}

		$data['IssuerId'] = $this->credentials->issuerId;

		$data = json_encode($data);

		if ($this->request_type == 'oauth' && is_a($this->request, 'oauth'))
		{
			$this->request->setAuthType(OAUTH_AUTH_TYPE_AUTHORIZATION);

			try
			{
				$this->request->fetch(self::passportApiEndpoint . 'badges/', $data, OAUTH_HTTP_METHOD_POST, array('Content-Type'=>'application/json'));
			}
			catch (Exception $e)
			{
				throw new Exception('Badge creation request failed.');
			}

			$badge = json_decode($this->request->getLastResponse());
		}
		else if ($this->request_type == 'curl' && get_resource_type($this->request) == 'curl')
		{
			curl_setopt($this->request, CURLOPT_URL, self::passportApiEndpoint . 'badges/');
			curl_setopt($this->request, CURLOPT_POSTFIELDS, $data);
			curl_setopt($this->request, CURLOPT_RETURNTRANSFER, TRUE);

			$response = curl_exec($this->request);
			$badge    = json_decode($response);
		}
		else
		{
			throw new Exception('Unsupported request type');
		}

		if (empty($badge->Id) || !$badge->Id)
		{
			throw new Exception($badge->message);
		}

		return($badge->Id);
	}

	/**
	 * Grant badges to users
	 * 
	 * @param   object  $badge  Badge info: ID, Evidence URL
	 * @param   mixed   $users  String (for single user) or array (for multiple users) of user email addresses
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

			$data['BadgeId']      = $badge->id;
			$data['EvidenceUrl']  = $badge->evidenceUrl;
			$data['EmailAddress'] = $user;
			$data['ClientId']     = $this->credentials->clientId;

			$assertions[] = $data;
			unset($data);
		}

		$assertionsData = json_encode($assertions);

		if ($this->request_type == 'oauth' && is_a($this->request, 'oauth'))
		{
			$this->request->setAuthType(OAUTH_AUTH_TYPE_AUTHORIZATION);
			try
			{
				$this->request->fetch(self::passportApiEndpoint . "assertions/", $assertionsData, OAUTH_HTTP_METHOD_POST, array('Content-Type'=>'application/json'));
			}
			catch (Exception $e)
			{
				error_log($e->getCode());
			}

			$assertion = json_decode($this->request->getLastResponse());
		}
		else if ($this->request_type == 'curl' && get_resource_type($this->request) == 'curl')
		{
			curl_setopt($this->request, CURLOPT_URL, self::passportApiEndpoint . "assertions/");
			curl_setopt($this->request, CURLOPT_POSTFIELDS, $assertionsData);
			curl_setopt($this->request, CURLOPT_RETURNTRANSFER, TRUE);

			$response  = curl_exec($this->request);
			$assertion = json_decode($response);
		}
		else
		{
			throw new Exception('Unsupported request type');
		}

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

	/**
	 * Return a URL
	 * 
	 * @param   string  $type
	 * @return  bool
	 */
	public function getUrl($type = 'Claim')
	{
		switch ($type)
		{
			case 'Denied':
				return self::passportDeniedUrl;
			break;

			case 'Badges':
				return self::passportBadgesUrl;
			break;

			default:
				return self::passportClaimUrl;
			break;
		}
	}

	/**
	 * Get assertions by email address
	 * 
	 * @param   mixed  $emailAddresses  String (for single user) or array (for multiple users) of user email addresses
	 * @return  array
	 */
	public function getAssertionsByEmailAddress($emailAddresses)
	{
		if (!$this->credentialsSet())
		{
			throw new \Exception('You need to set the credentials first.');
		}

		if (!is_array($emailAddresses))
		{
			$emailAddresses = array($emailAddresses);
		}

		$query_params = implode('%20', $emailAddresses);
		$url = self::passportApiEndpoint . "assertions?emailAddresses=" . $query_params;

		if ($this->request_type == 'oauth' && is_a($this->request, 'oauth'))
		{
			$this->request->setAuthType(OAUTH_AUTH_TYPE_URI);
			try
			{
				$this->request->fetch($url, null, OAUTH_HTTP_METHOD_GET, array('Content-Type'=>'application/json'));
			}
			catch (Exception $e)
			{
				error_log($e->getCode());
			}

			$response = json_decode($this->request->getLastResponse());
		}
		else if ($this->request_type == 'curl' && get_resource_type($this->request) == 'curl')
		{
			curl_setopt($this->request, CURLOPT_POST, false);
			curl_setopt($this->request, CURLOPT_URL, $url);
			curl_setopt($this->request, CURLOPT_RETURNTRANSFER, TRUE);

			$response = curl_exec($this->request);
			$response = json_decode($response);
		}
		else
		{
			throw new Exception('Unsupported request type');
		}

		return $response;
	}
}