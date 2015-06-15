<?php
/**
 * HUBzero CMS
 *
 * Copyright 2015 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Oauth\Site\Controllers;

use Hubzero\Component\SiteController;
use Exception;
use Request;

/**
 * Controller for Authorizing OAuth
 */
class Authorize extends SiteController
{
	/**
	 * Authorize
	 *
	 * @return  void
	 */
	public function authorizeTask()
	{
		$oauth_token = \Request::getVar('oauth_token');

		if (empty($oauth_token))
		{
			throw new Exception('Forbidden', 403);
		}

		$db = \JFactory::getDBO();
		$db->setQuery("SELECT * FROM `#__oauthp_tokens` WHERE token=" . $db->Quote($oauth_token) . " AND user_id=0 LIMIT 1;");

		$result = $db->loadObject();

		if ($result === false)
		{
			throw new Exception('Internal Server Error', 500);
		}

		if (empty($result))
		{
			throw new Exception('Forbidden', 403);
		}

		if (Request::method() == 'GET')
		{
			$this->view->oauth_token = $oauth_token;
			$this->view->display();
			return;
		}

		if (Request::method() == 'POST')
		{
			$token = Request::get('token',''.'post');

			if ($token != sha1($this->verifier))
			{
				throw new Exception('Forbidden', 403);
			}

			echo "posted";
			return;
		}

		throw new Exception('Method Not Allowed', 405);
	}
}