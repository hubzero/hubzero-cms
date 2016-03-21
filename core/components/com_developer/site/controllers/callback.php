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

use Hubzero\Component\SiteController;

/**
 * Handles hub callbacks from external applications
 *
 * This is probably a placeholder.  It's needed because plugins aren't
 * directly routable at the moment.  Otherwise, these methods could live
 * in the specific plugin to which they pertain.
 */
class Callback extends SiteController
{
	/**
	 * Processes the dropbox callback from oauth authorize requests
	 *
	 * @return    void
	 **/
	public function dropboxAuthorizeTask()
	{
		$params = \Plugin::params('filesystem', 'dropbox');

		$info = [
			'key'    => $params->get('app_key'),
			'secret' => $params->get('app_secret')
		];

		$appInfo          = \Dropbox\AppInfo::loadFromJson($info);
		$clientIdentifier = 'hubzero-cms/2.0';
		$redirectUri      = trim(\Request::root(), '/') . '/developer/callback/dropboxAuthorize';
		$csrfTokenStore   = new \Dropbox\ArrayEntryStore($_SESSION, 'dropbox-auth-csrf-token');
		$oauth            = new \Dropbox\WebAuth($appInfo, $clientIdentifier, $redirectUri, $csrfTokenStore);

		list($accessToken, $userId, $urlState) = $oauth->finish($_GET);

		\Session::set('dropbox.token', $accessToken);

		// Redirect to the local endpoint
		App::redirect(base64_decode($urlState));
	}
}
