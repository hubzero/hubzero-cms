<?php
/**
 * HUBzero CMS
 *
 * Copyright 2015 HUBzero Foundation, LLC.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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

		$db = \App::get('db');
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