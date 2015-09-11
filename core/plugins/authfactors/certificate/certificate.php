<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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