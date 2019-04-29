<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Filesystem\Dropbox;

use Stevenmaguire\OAuth2\Client\Provider\Dropbox as VendorDropboxOauthClient;
use Hubzero\Utility\Arr;

class DropboxOauthClient
{
	/**
	 * $client         Vendor Dropbox OAuth Client        @var object
	 * $clientId       Dropbox Application ID             @var string
	 * $clientSecret   Dropbox Application secret         @var string
	 * $redirectUri    Dropbox Application redirect URI   @var string
	 * $state          OAuth state                        @var string
	 **/
	protected $client, $clientId, $clientSecret, $redirectUri, $state;

	/**
	 * Initialize Dropbox OAuth Client
	 *
	 * @param   array   $args  Dropbox client data
	 * @return  void
	 **/
	public function __construct($args = [])
	{
		$config = \Plugin::params('filesystem', 'dropbox');
		$this->clientId = $config->get('app_key');
		$this->clientSecret = $config->get('app_secret');
		$this->redirectUri = Request::base() . 'developer/callback/dropboxAuthorize';

		$this->client = new VendorDropboxOauthClient([
			'clientId' => $this->clientId,
			'clientSecret' => $this->clientSecret,
			'redirectUri' => $this->redirectUri
		]);
	}

	/**
	 * Retrieves API access token using authorization code
	 *
	 * @param   string   $authorizationCode  Dropbox authorization code
	 * @return  string
	 **/
	public function getAccessToken($authorizationCode)
	{
		$accessToken = $this->client->getAccessToken('authorization_code', ['code' => $authorizationCode]);

		return $accessToken;
	}

	/**
	 * Retrieves API authorization code
	 *
	 * @param   string   Dropbox authorization code URL
	 * @return  string
	 **/
	public function getAuthorizationCode($authUrl)
	{
		header("Location: $authUrl");
		exit();
	}

	/**
	 * Generates Dropbox API authorization URL
	 *
	 * @return  string
	 **/
	public function getAuthorizationUrl()
	{
		$authorizationUrl = $this->client->getAuthorizationUrl();

		return $authorizationUrl;
	}

	/**
	 * Generates OAuth state
	 *
	 * @return  string
	 **/
	public function getState()
	{
		$state = $this->client->getState();
		$this->state = $state;

		return $state;
	}
}
