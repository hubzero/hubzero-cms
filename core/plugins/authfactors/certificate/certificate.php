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

use \Hubzero\Notification\Handler;
use \Hubzero\Notification\Storage\Cookie;

/**
 * Factor Auth plugin for certificate based identity verification
 */
class plgAuthfactorsCertificate extends \Hubzero\Plugin\Plugin
{
	/**
	 * Renders the auth factor challenge
	 *
	 * @return string
	 **/
	public function onRenderChallenge()
	{
		// There's not really anything to render for this one, you either have
		// a cert or your don't.  If the user does, we'll just redirect.  Otherwise,
		// perhaps another plugin will give them another option.
		if ($this->isAuthenticated())
		{
			// Update session and reload the current page
			App::get('session')->set('authfactors.status', true);
			App::redirect(Request::current());
		}
		else
		{
			// Update session and reload the current page
			App::get('session')->set('authfactors.status', false);

			// Register an error with the cookie handler so that it outlives session termination
			with(new Handler(new Cookie(1)))->error(Lang::txt('COM_LOGIN_FACTORS_FAILED'));

			App::redirect(Request::current());
		}
	}

	/**
	 * Encapsulates auth check for internal plugin use
	 *
	 * @return  bool
	 */
	private function isAuthenticated()
	{
		return (isset($_SERVER['SSL_CLIENT_S_DN']) && $_SERVER['SSL_CLIENT_S_DN']);
	}
}