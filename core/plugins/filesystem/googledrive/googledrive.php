<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2016 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2016 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Plugin class for Google Drive filesystem connectivity
 */
class plgFilesystemGoogleDrive extends \Hubzero\Plugin\Plugin
{
	/**
	 * Initializes the Google Drive connection
	 *
	 * @param   array   $params  Any connection params needed
	 * @return  object
	 **/
	public static function init($params = [])
	{
		// Get the params
		$pparams = Plugin::params('filesystem', 'googledrive');

		$app_id = isset($params['app_id']) && $params['app_id'] != '' ? $params['app_id'] : $pparams->get('app_id');
		$app_secret = isset($params['app_secret']) && $params['app_secret'] != '' ? $params['app_secret'] : $pparams->get('app_secret');

		$client = new \Google_Client();
		$client->setClientId($app_id);
		$client->setClientSecret($app_secret);
		$client->addScope(Google_Service_Drive::DRIVE);
		$client->setAccessType('offline');
		$client->setApprovalPrompt('force');
		$client->setIncludeGrantedScopes(true);


		if (isset($params['app_token']))
		{
			$accessToken = $params['app_token'];
			// json encode turned our array into an object, we need to undo that
			$accessToken = (array)$accessToken;
		}
		else
		{
			\Session::set('googledrive.app_id', $app_id);
			\Session::set('googledrive.app_secret', $app_secret);
			\Session::set('googledrive.connection_to_set_up', Request::getVar('connection', 0));

			// Set upp a return and redirect to Google for auth
			$return = (Request::getVar('return')) ? Request::getVar('return') : Request::current(true);
			$return = base64_encode($return);

			$redirectUri      = trim(Request::root(), '/') . '/developer/callback/googledriveAuthorize';
			$client->setRedirectUri($redirectUri);

			Session::set('googledrive.state', $return);

			App::redirect($client->createAuthUrl());
		}
		$client->setAccessToken($accessToken);
		$service = new \Google_Service_Drive($client);
		$adapter = new \Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter($service, 'root');
		return $adapter;
	}
}
