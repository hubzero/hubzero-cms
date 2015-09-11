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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Developer\Site\Controllers;

use Components\Developer\Models;
use Hubzero\Component\SiteController;
use Hubzero\Oauth\Storage\Mysql as MysqlStorage;
use Exception;
use OAuth2;
use Lang;

/**
 * Handle Oauth Authorization & Tokens
 */
class Oauth extends SiteController
{
	/**
	 * OAuth Server Object
	 * 
	 * @var  object
	 */
	private $server = null;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		// create our oauth server
		$this->server = new \Hubzero\Oauth\Server(new MysqlStorage);

		// do the rest of setup
		$this->disableDefaultTask();

		parent::__construct();
	}

	/**
	 * Show Auth View
	 * 
	 * @return  void
	 */
	public function authorizeTask()
	{
		//create request & response objects
		$request  = OAuth2\Request::createFromGlobals();
		$response = new OAuth2\Response();

		// get the application model (by client ID)
		$model = new Models\Api\Application();
		$this->view->application = $model->loadByClientId($request->query('client_id'));

		// force query string redirect param
		if (!$request->query('redirect_uri'))
		{
			throw new Exception('No redirect URI', 400);
		}

		// validate the authorize request
		if (!$this->server->validateAuthorizeRequest($request, $response))
		{
			throw new Exception($response->getParameter('error_description'), 400);
		}

		// make sure were logged in
		if (User::isGuest())
		{
			// redirect to login
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($_SERVER['REQUEST_URI'])),
				Lang::txt('You must be logged in to authorize %s', $this->view->application->get('name')),
				'warning'
			);
		}

		// display authorize form
		$this->view->display();
	}

	/**
	 * Perform Authorization
	 * 
	 * @return  void
	 */
	public function doAuthorizeTask()
	{
		$request  = OAuth2\Request::createFromGlobals();
		$response = new OAuth2\Response();
		$is_authorized = ($request->request('authorize')) ? true : false;

		// rewrite POST params to GET for oauth server
		foreach (array('client_id', 'response_type', 'redirect_uri', 'state') as $param)
		{
			$request->query[$param] = $request->request($param);
		}

		// handle auth request
		$this->server->handleAuthorizeRequest($request, $response, $is_authorized, User::get('id'));
		$response->send();

		exit(0);
	}

	/**
	 * Validate Token
	 * 
	 * @return  void
	 */
	public function tokenTask()
	{
		// Handle a request for an OAuth2.0 Access Token and send the response to the client
		$this->server->handleTokenRequest(
			OAuth2\Request::createFromGlobals()
		)->send();

		exit();
	}

	/**
	 * Retreive Token Info
	 * 
	 * @return  void
	 */
	public function tokenInfoTask()
	{
		// get request & token params
		$request  = OAuth2\Request::createFromGlobals();
		$token = $request->request('token', $request->query('token'));

		// load token details
		$storage = $this->server->getStorage('access_token');
		$tokenDetails = $storage->getAccessToken($token);

		// return token details
		echo json_encode($tokenDetails, true);

		exit();
	}
}