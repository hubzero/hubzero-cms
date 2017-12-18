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
 * @author    Anthony Fuentes <fuentesa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Supportstats\Site\Controllers;

require_once Component::path('com_supportstats') . '/helpers/clientApiConfigHelper.php';
require_once Component::path('com_supportstats') . '/models/hubAuthorization.php';
require_once Component::path('com_supportstats') . '/models/hub.php';

use Components\Supportstats\Helpers\ClientApiConfigHelper;
use Components\Supportstats\Models\HubAuthorization;
use Components\Supportstats\Models\Hub;
use Hubzero\Component\SiteController;
use Notify;

/*
 * Handles routing users for OAuth purposes
 */
class OAuth extends SiteController
{

	public function execute()
	{
		parent::execute();
	}

	public function authorizeTask()
	{
		$hubId = Request::getInt('hubid');
		$hub = Hub::one($hubId);

		$hubAuthUrl = $hub->getAuthUrl();

		App::redirect($hubAuthUrl);
	}

	public function accessTokenTask()
	{
		$code = Request::getVar('code', null);
		$returnedState = Request::getCmd('state', null);
		$hubAuthorization = HubAuthorization::oneByState($returnedState);
		$redirectDestination = ClientApiConfigHelper::getOutstandingTicketsPageUrl();

		if ($code && !$hubAuthorization->isNew())
		{
			$hub = $hubAuthorization->getHub();
			$accessTokenData = $hub->fetchAccessToken($code);
			$hubAuthorization->saveAccessToken($accessTokenData);
			App::redirect($redirectDestination);
		}
		else
		{
			Notify::error('Authorization with the external site failed.');
			App::redirect($supportLandingPageUrl);
		}
	}

}
