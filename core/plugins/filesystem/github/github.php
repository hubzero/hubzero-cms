<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Potherca\Flysystem\Github\Api;
use Potherca\Flysystem\Github\GithubAdapter;
use Potherca\Flysystem\Github\Settings;

/**
 * Plugin class for github filesystem connectivity
 */
class plgFilesystemGithub extends \Hubzero\Plugin\Plugin
{
	/**
	 * Initializes the github connection
	 *
	 * @param   array   $params  Any connection params needed
	 * @return  object
	 **/
	public static function init($params = [])
	{
		// Get the params
		$pparams = Plugin::params('filesystem', 'github');

		$app_key = $pparams['app_key'];
		$app_secret = $pparams['app_secret'];
		$repository = $params['repository'];

		$credentials = [];

		if (isset($params['access_token']))
		{
			$accessToken = $params['access_token'];
		}
		else
		{
			$base   = 'https://github.com/login/oauth/authorize';
			$params = '?client_id=' . $app_key;
			$scope  = '&scope=user,repo';

			$return = (Request::getString('return')) ? Request::getString('return') : Request::current(true);
			$return = base64_encode($return);
			$state  = '&state=' . $return;

			Session::set('github.state', $return);
			Session::set('github.connection_to_set_up', Request::getInt('connection', 0));
			Session::set('github.repo', $repository);

			App::redirect($base . $params . $scope . $state);
		}
		$credentials = [Settings::AUTHENTICATE_USING_TOKEN, $accessToken];

		$settings = new Settings($params['repository'], $credentials);
		$api      = new Api(new \Github\Client(), $settings);

		// Return the adapter
		return new GithubAdapter($api);
	}
}
