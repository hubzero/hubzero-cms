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

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin class for dropbox filesystem connectivity
 */
class plgFilesystemDropbox extends \Hubzero\Plugin\Plugin
{
	/**
	 * Initializes the dropbox connection
	 *
	 * @param   array   $params  Any connection params needed
	 * @return  \League\Flysystem\Dropbox\DropboxAdapter
	 **/
	public static function init($params = [])
	{
		// Get the params
		$pparams = Plugin::params('filesystem', 'dropbox');

		if (isset($params['app_token']))
		{
			$accessToken = $params['app_token'];
		}
		else
		{
			$info = [
				'key'    => isset($params['app_key']) ? $params['app_key'] : $pparams->get('app_key'),
				'secret' => isset($params['app_secret']) ? $params['app_secret'] : $pparams->get('app_secret')
			];

			\Session::set('dropbox.app_key', $info['key']);
			\Session::set('dropbox.app_secret', $info['secret']);
			\Session::set('dropbox.connection_to_set_up', Request::getVar('connection', 0));

			$appInfo          = \Dropbox\AppInfo::loadFromJson($info);
			$clientIdentifier = 'hubzero-cms/2.0';
			$redirectUri      = trim(Request::root(), '/') . '/developer/callback/dropboxAuthorize';
			$csrfTokenStore   = new \Dropbox\ArrayEntryStore($_SESSION, 'dropbox-auth-csrf-token');
			$oauth            = new \Dropbox\WebAuth($appInfo, $clientIdentifier, $redirectUri, $csrfTokenStore);

			// Redirect to dropbox
			// We hide the return url in the state field...that's not exactly what
			// it was intended for, but it does the trick
			$return = (Request::getVar('return')) ? Request::getVar('return') : Request::current(true);
			$return = base64_encode($return);
			App::redirect($oauth->start($return));
		}

		$app_secret = isset($params['app_secret']) ? $params['app_secret'] : $pparams->get('app_secret');
		// Create the client
		$client = new \Dropbox\Client($accessToken, $app_secret);

		// Return the adapter
		return new \League\Flysystem\Dropbox\DropboxAdapter($client, (isset($params['subdir']) ? $params['subdir'] : null));
	}
}
