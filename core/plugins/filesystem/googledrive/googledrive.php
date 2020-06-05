<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Hubzero\Utility\Arr;

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
			\Session::set('googledrive.connection_to_set_up', Request::getInt('connection', 0));

			// Set upp a return and redirect to Google for auth
			$return = (Request::getString('return')) ? Request::getString('return') : Request::current(true);
			$return = base64_encode($return);

			$redirectUri      = trim(Request::root(), '/') . '/developer/callback/googledriveAuthorize';
			$client->setRedirectUri($redirectUri);

			Session::set('googledrive.state', $return);

			App::redirect($client->createAuthUrl());
		}
		$path = Arr::getValue($params, 'path', null);
		$path = explode('/', urldecode($path));
		$path = end($path);
		$client->setAccessToken($accessToken);
		$service = new \Google_Service_Drive($client);
		$adapter = new \Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter($service, $path);
		return $adapter;
	}
}
