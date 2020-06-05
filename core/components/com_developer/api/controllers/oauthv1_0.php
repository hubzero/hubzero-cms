<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
