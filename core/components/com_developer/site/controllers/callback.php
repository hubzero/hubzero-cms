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
		$pparams = \Plugin::params('filesystem', 'dropbox');

		$app_key = \Session::get('dropbox.app_key', false);
		$app_secret = \Session::get('dropbox.app_secret', false);
		$new_connection = Session::get('dropbox.connection_to_set_up', false);

		$info = [
			'key'    => isset($app_key) ? $app_key : $pparams->get('app_key'),
			'secret' => isset($app_secret) ? $app_secret : $pparams->get('app_secret'),
		];

		$appInfo          = \Dropbox\AppInfo::loadFromJson($info);
		$clientIdentifier = 'hubzero-cms/2.0';
		$redirectUri      = trim(\Request::root(), '/') . '/developer/callback/dropboxAuthorize';
		$csrfTokenStore   = new \Dropbox\ArrayEntryStore($_SESSION, 'dropbox-auth-csrf-token');
		$oauth            = new \Dropbox\WebAuth($appInfo, $clientIdentifier, $redirectUri, $csrfTokenStore);

		\Session::set('dropbox.app_key', false);
		\Session::set('dropbox.app_secret', false);

		list($accessToken, $userId, $urlState) = $oauth->finish($_GET);

		//if this is a new connection, we can save the token on the server to ensure that it is used next time
		if ($new_connection)
		{
			require_once PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'models' . DS . 'orm' . DS . 'connection.php';
			$connection = \Components\Projects\Models\Orm\Connection::oneOrFail($new_connection);
			$connection_params = json_decode($connection->get('params'));
			$connection_params->app_token = $accessToken;
			$connection->set('params', json_encode($connection_params));
			$connection->save();
		}

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
		$app_key = \Session::get('github.app_key', false);
		$app_secret = \Session::get('github.app_secret', false);

		\Session::set('github.app_key', false);
		\Session::set('github.app_secret', false);

		$fields = array(
			'client_id'     => isset($app_key) ? $app_key : $params->get('app_key'),
			'client_secret' => isset($app_secret) ? $app_secret : $params->get('app_secret'),
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

	/**
	 * Processes the google callback from oauth authorize requests
	 *
	 * @return    void
	 **/
	public function googledriveAuthorizeTask()
	{
		$pparams = \Plugin::params('filesystem', 'google');

		$app_id = \Session::get('googledrive.app_id', false);
		$app_secret = \Session::get('googledrive.app_secret', false);
		$new_connection = Session::get('googledrive.connection_to_set_up', false);

		$info = [
			'key'    => isset($app_key) ? $app_key : $pparams->get('app_key'),
			'secret' => isset($app_secret) ? $app_secret : $pparams->get('app_secret'),
		];

		$client = new \Google_Client();
		$client->setClientId($app_id);
		$client->setClientSecret($app_secret);
		$client->setAccessType('offline');
		$client->addScope(\Google_Service_Drive::DRIVE);
		$redirectUri      = trim(Request::root(), '/') . '/developer/callback/googledriveAuthorize';
		$client->setRedirectUri($redirectUri);

		$code = Request::get('code', false);

		if ($code)
		{
			$client->authenticate($code);
			$accessToken = $client->getAccessToken();
		}
		else
		{
			throw new \Exception("No state found", 400);
		}

		//if this is a new connection, we can save the token on the server to ensure that it is used next time
		if ($new_connection)
		{
			require_once PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'models' . DS . 'orm' . DS . 'connection.php';
			$connection = \Components\Projects\Models\Orm\Connection::oneOrFail($new_connection);
			$connection_params = json_decode($connection->get('params'));
			$connection_params->app_token = $accessToken;
			$connection->set('params', json_encode($connection_params));
			$connection->save();
		}

		// Redirect to the local endpoint
		App::redirect(base64_decode(\Session::get('googledrive.state')));
	}
}
