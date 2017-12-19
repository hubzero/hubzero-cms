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
 * @author    Anthony Fuentes <fuentesa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Supportstats\Helpers;

use User;
use App;

/**
 * Handles basic authentication tasks
 */
class AuthHelper
{

	/**
	 * Handles redirecting unauthenticated users
	 *
	 * @param   string   $controller 	Name of controller to forward to on sign-in
	 * @param   string   $task			 	Name of task to forward to on sign-in
	 * @return  array
	 */
	public static function redirectUnlessAuthenticated($controller, $task = '')
	{
		if (User::isGuest())
		{
			$redirectUrl = self::_buildRedirectUrl($controller, $task);

			App::redirect(
				$redirectUrl,
				'Please sign in.',
				'warning'
			);
		}
	}

	/**
	 * Builds URL that the user will be redirected to including friendly-forwarding
	 *
	 * @param   string   $controller 	Name of controller to forward to on sign-in
	 * @param   string   $task			 	Name of task to forward to on sign-in
	 * @return  array
	 */
	protected static function _buildRedirectUrl($controller, $task)
	{
		$redirectUrl = '/login';
		$forwardingUrl = self::_buildForwardingUrl($controller, $task);

		$redirectUrl .= $forwardingUrl;

		return $redirectUrl;
	}

	/**
	 * Builds friendly-forwarding URL
	 *
	 * @param   string   $controller 	Name of controller to forward to on sign-in
	 * @param   string   $task			 	Name of task to forward to on sign-in
	 * @return  array
	 */
	protected static function _buildForwardingUrl($controller, $task)
	{
		$forwardingUrl = '';

		if ($controller)
		{
			$forwardingUrl = Route::url("index.php?controller=$controller");

			if ($task)
			{
				$forwardingUrl = Route::url("index.php?controller=$controller&task=$task");
			}
		}

		$friendlyForward = '?return=' . base64_encode($forwardingUrl);

		return $friendlyForward;
	}

}
