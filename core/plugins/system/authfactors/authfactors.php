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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * System plugin checking auth factors after routing
 */
class plgSystemAuthfactors extends \Hubzero\Plugin\Plugin
{
	/**
	 * Hook for after parsing route
	 *
	 * @return  void
	 */
	public function onAfterRoute()
	{
		if (in_array(App::get('client')->id, $this->params->get('clients', [1])))
		{
			$exceptions = [
				'com_login.logout',
				'com_users.logout'
			];

			$current  = Request::getWord('option', '');
			$current .= ($task = Request::getWord('task', false)) ? '.' . $task : '';
			$current .= ($view = Request::getWord('view', false)) ? '.' . $view : '';

			// If guest, proceed as normal and they'll land on the login page
			if (!User::isGuest() && !in_array($current, $exceptions))
			{
				// Get factor status
				$status = App::get('session')->get('authfactors.status', null);

				if ($status === false)
				{
					// If not a guest, and auth factors checks are done and have failed,
					// log out so we start over
					$logout = 'logout' . ucfirst(App::get('client')->alias);
					self::$logout();
				}
				else if ($status === null)
				{
					// If not a guest, but no factor verification has been completed,
					// procede with auth factor checks as applicable
					$factors = 'factors' . ucfirst(App::get('client')->alias);
					self::$factors();
				}
			}
		}
	}

	/**
	 * Logs out of the admin client
	 *
	 * @return  void
	 **/
	private function logoutAdmin()
	{
		Request::setVar('option', 'com_login');
		Request::setVar('task', 'logout');
	}

	/**
	 * Logs out of the site client
	 *
	 * @return  void
	 **/
	private function logoutSite()
	{
		Request::setVar('option', 'com_users');
		Request::setVar('task', 'user.logout');
		Request::setVar('return', base64_encode('/'));
	}

	/**
	 * Sends to factor input view
	 *
	 * @return  void
	 **/
	private function factorsAdmin()
	{
		Request::setVar('option', 'com_login');
		Request::setVar('task', 'factors');
	}

	/**
	 * Sends to factor input view
	 *
	 * @return  void
	 **/
	private function factorsSite()
	{
		Request::setVar('option', 'com_users');
		Request::setVar('view', 'factors');
	}
}