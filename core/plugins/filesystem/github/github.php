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

		$credentials = [];

		if (isset($params['username']) && isset($params['password']))
		{
			$credentials = [Settings::AUTHENTICATE_USING_PASSWORD, $params['username'], $params['password']];
		}
		else
		{
			$accessToken = Session::get('github.token', false);

			if (!$accessToken)
			{
				$base   = 'https://github.com/login/oauth/authorize';
				$params = '?client_id=' . $pparams->get('app_key');
				$scope  = '&scope=user,repo';

				$return = (Request::getVar('return')) ? Request::getVar('return') : Request::current(true);
				$return = base64_encode($return);
				$state  = '&state=' . $return;

				Session::set('github.state', $return);

				App::redirect($base . $params . $scope . $state);
			}

			$credentials = [Settings::AUTHENTICATE_USING_TOKEN, $accessToken];
		}

		$settings = new Settings($params['repository'], $credentials);
		$api      = new Api(new \Github\Client(), $settings);

		// Return the adapter
		return new GithubAdapter($api);
	}
}
