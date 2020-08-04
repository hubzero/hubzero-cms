<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
	const PASSPORT_API_ENDPOINT = 'https://api.openpassport.org/1.0.0/';

	/**
	 * API claim URL
	 *
	 * @var  string
	 */
	const PASSPORT_CLAIM_URL    = 'https://www.openpassport.org/MyBadges/Pending';

	/**
	 * API denied URL
	 *
	 * @var  string
	 */
	const PASSPORT_DENIED_URL   = 'https://www.openpassport.org/MyBadges/Denied';

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
	private $request = null;

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

		$this->request = new \OAuth($this->credentials->client_id, $this->credentials->client_secret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_FORM);

		$params['username'] = $this->credentials->username;
		$params['password'] = $this->credentials->password;
		$params['client_id'] = $this->credentials->client_id;
		$params['client_secret'] = $this->credentials->client_secret;
		$params['grant_type'] = 'password';
		$userAgent = $_SERVER['HTTP_USER_AGENT'];

		$this->request->fetch('https://www.openpassport.org/oauth/token', $params, OAUTH_HTTP_METHOD_POST, array('user-agent' => $userAgent));

		$access = json_decode($this->request->getLastResponse());
		$this->credentials->access_token = $access->access_token;
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
		$accessToken = $this->credentials->access_token;
		$userAgent = $_SERVER['HTTP_USER_AGENT'];

		$headers = [
			'Cache-Control: no-cache',
			'Content-Type: application/json',
			"Authorization: Bearer $accessToken",
			"user-agent: $userAgent"
		];

		$request = curl_init();
		curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($request, CURLOPT_URL, 'https://www.openpassport.org/1.0.0/badges');
		curl_setopt($request, CURLOPT_POSTFIELDS, $data);
		curl_setopt($request, CURLOPT_POST, 1);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($request, CURLOPT_VERBOSE, true);

		$response = curl_exec($request);
		$badge = json_decode($response);

		if (empty($badge->Id) || !$badge->Id)
		{
			throw new Exception($badge->message);
		}

		return $badge->Id;
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
				$this->request->fetch(self::PASSPORT_API_ENDPOINT . "assertions/", $assertionsData, OAUTH_HTTP_METHOD_POST, array('Content-Type' => 'application/json'));
			}
			catch (Exception $e)
			{
				throw new Exception('Badge grant request failed.');
			}

			$assertion = json_decode($this->request->getLastResponse());
		}
		else if ($this->request_type == 'curl' && get_resource_type($this->request) == 'curl')
		{
			curl_setopt($this->request, CURLOPT_URL, self::PASSPORT_API_ENDPOINT . "assertions/");
			curl_setopt($this->request, CURLOPT_POSTFIELDS, $assertionsData);
			curl_setopt($this->request, CURLOPT_RETURNTRANSFER, true);

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
			return self::PASSPORT_DENIED_URL;
			break;

		case 'Badges':
			return self::PASSPORT_BADGES_URL;
			break;

		default:
			return self::PASSPORT_CLAIM_URL;
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
			throw new Exception('You need to set the credentials first.');
		}

		if (!is_array($emailAddresses))
		{
			$emailAddresses = array($emailAddresses);
		}

		$query_params = implode('%20', $emailAddresses);
		$url = self::PASSPORT_API_ENDPOINT . "assertions?emailAddresses=" . $query_params;

		if ($this->request_type == 'oauth' && is_a($this->request, 'oauth'))
		{
			$this->request->setAuthType(OAUTH_AUTH_TYPE_URI);
			try
			{
				$this->request->fetch($url, null, OAUTH_HTTP_METHOD_GET, array('Content-Type' => 'application/json'));
			}
			catch (Exception $e)
			{
				throw new Exception('Assertations by email request failed.');
			}

			$response = json_decode($this->request->getLastResponse());
		}
		else if ($this->request_type == 'curl' && get_resource_type($this->request) == 'curl')
		{
			curl_setopt($this->request, CURLOPT_POST, false);
			curl_setopt($this->request, CURLOPT_URL, $url);
			curl_setopt($this->request, CURLOPT_RETURNTRANSFER, true);

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
