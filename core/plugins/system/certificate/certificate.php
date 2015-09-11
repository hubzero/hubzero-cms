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
 * @package   HUBzero
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * System plugin for client side certificate restrictions
 */
class plgSystemCertificate extends \Hubzero\Plugin\Plugin
{
	/**
	 * Hook for after parsing route
	 *
	 * @return void
	 */
	public function onAfterRoute()
	{
		// First, check for presence of subject dn, which is the minimum required field
		if (!isset($_SERVER['SSL_CLIENT_S_DN']) || !$_SERVER['SSL_CLIENT_S_DN'])
		{
			\App::redirect($this->params->get('failure_location', '/invalidcert.php'));
			return;
		}

		if (\User::isGuest())
		{
			// If so, redirect to login
			Request::setVar('option',        'com_users');
			Request::setVar('task',          'user.login');
			Request::setVar('authenticator', 'certificate');
			Request::setVar('return',        base64_encode(\Request::current()));

			return;
		}

		// Check if user is registered and if current session is linked to cert identity
		$hzad = \Hubzero\Auth\Domain::getInstance('authentication', 'certificate', $_SERVER['SSL_CLIENT_I_DN_CN']);
		if ($link = \Hubzero\Auth\Link::getInstance($hzad->id, $_SERVER['SSL_CLIENT_S_DN_CN']))
		{
			if ($link->user_id == \User::get('id'))
			{
				// All clear...return nothing
				return;
			}
		}

		// Otherwise, we have a cert-based user that doesn't match the current user
		Request::setVar('option', 'com_users');
		Request::setVar('task',   'user.logout');

		$this->event->stop();
	}
}