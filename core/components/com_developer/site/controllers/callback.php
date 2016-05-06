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

namespace Components\Developer\Site\Controllers;

use Hubzero\Component\SiteController;

/**
 * Handles hub callbacks from external applications
 *
 * This is probably a placeholder.  It's needed because plugins aren't
 * directly routable at the moment.  Otherwise, these methods could live
 * in the specific plugin to which they pertain.
 */
class Callback extends SiteController
{
	/**
	 * Processes the dropbox callback from oauth authorize requests
	 *
	 * @return    void
	 **/
	public function dropboxAuthorizeTask()
	{
		$params = \Plugin::params('filesystem', 'dropbox');

		$info = [
			'key'    => $params->get('app_key'),
			'secret' => $params->get('app_secret')
		];

		$appInfo          = \Dropbox\AppInfo::loadFromJson($info);
		$clientIdentifier = 'hubzero-cms/2.0';
		$redirectUri      = trim(\Request::root(), '/') . '/developer/callback/dropboxAuthorize';
		$csrfTokenStore   = new \Dropbox\ArrayEntryStore($_SESSION, 'dropbox-auth-csrf-token');
		$oauth            = new \Dropbox\WebAuth($appInfo, $clientIdentifier, $redirectUri, $csrfTokenStore);

		list($accessToken, $userId, $urlState) = $oauth->finish($_GET);

		\Session::set('dropbox.token', $accessToken);

		// Redirect to the local endpoint
		App::redirect(base64_decode($urlState));
	}

	/**
	 * Processes the github callback from oauth authorize requests
	 *
	 * @return    void
	 **/
	public function githubAuthorizeTask()
	{
		$params = \Plugin::params('filesystem', 'github');

		if (!$code = Request::getVar('code'))
		{
			throw new \Exception("No code found", 400);
		}
		if (!$state = Request::getVar('state'))
		{
			throw new \Exception("No state found", 400);
		}
		if ($state != Session::get('github.state'))
		{
			throw new \Exception("State mismatch", 500);
		}

		$url = 'https://github.com/login/oauth/access_token';
		$fields = array(
			'client_id'     => $params->get('app_key'),
			'client_secret' => $params->get('app_secret'),
			'code'          => $code,
			'state'         => $state
		);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, count($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));

		$result = curl_exec($ch);

		curl_close($ch);

		$data = json_decode($result);

		\Session::set('github.token', $data->access_token);

		// Redirect to the local endpoint
		App::redirect(base64_decode($state));
	}

	/**
	 * Processes the Globus callback from oauth authorize requests
	 *
	 * @return    void
	 **/
	public function globusAuthorizeTask()
	{
		$params = \Plugin::params('filesystem', 'globus');

		if (!$code = Request::getVar('code'))
		{
			throw new \Exception("No code found", 400);
		}

		// Check state
		if (!$state = Request::getVar('state'))
		{
			throw new \Exception("No state found", 400);
		}
		if ($state != Session::get('globus.state'))
		{
			throw new \Exception("State mismatch", 500);
		}

		$provider = new \League\OAuth2\Client\Provider\Globus([
			'clientId'     => $params->get('app_key'),
			'clientSecret' => $params->get('app_secret'),
			'redirectUri'  => trim(Request::base(), '/') . '/developer/callback/globusAuthorize'
		]);

		// Try to get an access token using the authorization code grant
		$accessToken = $provider->getAccessToken('authorization_code', [
			'code' => $code
		]);

		\Session::set('globus.token', $accessToken);

		// Redirect to the local endpoint
		App::redirect(base64_decode($state));
	}
}
