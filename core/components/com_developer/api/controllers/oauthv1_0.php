<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Developer\Api\Controllers;

use Hubzero\Component\ApiController;
use Hubzero\Oauth\Server;
use Hubzero\Oauth\Storage\Mysql as MysqlStorage;

/**
 * Oauth controller for the developer component
 */
class Oauthv1_0 extends ApiController
{
	/**
	 * Handle a request for an OAuth2.0 Access Token and send the response to the client
	 *
	 * @apiMethod POST
	 * @apiUri    /developer/oauth/token
	 * @return    void
	 */
	public function tokenTask()
	{
		$server = new Server(new MysqlStorage);

		$server->handleTokenRequest(
			\OAuth2\Request::createFromGlobals()
		)->send();

		exit();
	}
}