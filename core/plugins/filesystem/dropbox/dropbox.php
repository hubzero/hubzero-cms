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

require_once PATH_CORE . '/plugins/filesystem/dropbox/helpers/dropboxOauthClient.php';

use Plugins\Filesystem\Dropbox\DropboxOauthClient;
use Srmklive\Dropbox\Adapter\DropboxAdapter;
use Srmklive\Dropbox\Client\DropboxClient;
use Hubzero\Utility\Arr;
use Hubzero\Session;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin class for dropbox filesystem connectivity
 */
class plgFilesystemDropbox extends \Hubzero\Plugin\Plugin
{
	/**
	 * Initializes the Dropbox connection
	 *
	 * @param   array   $params  Client application data
	 * @return  DropboxAdapter
	 **/
	public static function init($params = [])
	{
		if (isset($params['app_token']))
		{
			$accessToken = $params['app_token']->access_token;
		}
		else
		{
			self::_getAccessToken($params);
		}

		// Create the client
		$client = new DropboxClient($accessToken);

		// Return the adapter
		return new DropboxAdapter($client, Arr::getValue($params, 'path', ''));
	}

	/**
	 * Retrieves Dropbox code used to get access token
	 *
	 * @param   array   $params  Client application data
	 * @return  void
	 **/
	protected static function _getAccessToken($params)
	{
		$oauthClient = new DropboxOauthClient();
		$authUrl = $oauthClient->getAuthorizationUrl();
		$oauthState = $oauthClient->getState();

		self::_setLocalOauthData($oauthState);

		$oauthClient->getAuthorizationCode($authUrl);
	}

	/**
	 * Sets OAuth-relevant data in local user session
	 *
	 * @param   array   $state  OAuth state
	 * @return  void
	 **/
	protected static function _setLocalOauthData($state)
	{
		$connectionId = Request::getInt('connection', 0);
		$projectsFilesUrl = Request::current();

		Session::set('dropbox.connection_to_set_up', $connectionId);
		Session::set('dropbox.local_origin_url', $projectsFilesUrl);
		Session::set('dropbox.state', $state);
	}
}
