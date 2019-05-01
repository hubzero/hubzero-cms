<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('__default', 'authorize');

		parent::execute();
	}

	/**
	 * Authorize
	 *
	 * @return  void
	 */
	public function authorizeTask()
	{
		$oauth_token = Request::getString('oauth_token');

		if (empty($oauth_token))
		{
			throw new Exception('Forbidden', 403);
		}

		$db = \App::get('db');
		$db->setQuery("SELECT * FROM `#__oauthp_tokens` WHERE token=" . $db->quote($oauth_token) . " AND user_id=0 LIMIT 1;");

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
			$this->view->set('oauth_token', $oauth_token);
			$this->view->display();
			return;
		}

		if (Request::method() == 'POST')
		{
			$token = Request::getString('token', '', 'post');

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
