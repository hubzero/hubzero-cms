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

namespace Components\Developer\Site\Controllers;

use Components\Developer\Models\Application;
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

		// get the application model (by client ID)
		$application = Application::all();
			->whereEquals('client_id', $request->query('client_id'))
			->row();

		// make sure were logged in
		if (User::isGuest())
		{
			// redirect to login
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($_SERVER['REQUEST_URI'])),
				Lang::txt('You must be logged in to authorize %s', $application->get('name')),
				'warning'
			);
		}

		// display authorize form
		$this->view
			->set('application', $application)
			->display();
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