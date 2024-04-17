<?php
/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Site\Controllers;

use Hubzero\Component\SiteController;

$componentPath = Component::path('com_newsletter');

require_once  "$componentPath/helpers/codeHelper.php";
require_once  "$componentPath/helpers/subscriptionsHelper.php";
require_once  "$componentPath/models/emailSubscription.php";

use Components\Newsletter\Helpers\CodeHelper;
use Components\Newsletter\Helpers\SubscriptionsHelper;
use Components\Newsletter\Models\EmailSubscription;

class Emailsubscriptions extends SiteController
{

	public function displayTask()
	{
		$code = Request::getString('code');
		$username = Request::getString('user');
		$campaignId = Request::getInt('campaign');

		// Verify that the user-supplied URL and code are valid:
		if (!CodeHelper::validateEmailSubscriptionsCode($username, $campaignId, $code))
		{
			Notify::warning(Lang::txt('AUTH_CODE_INVALID'));
			App::redirect('/');
		}

		$subHelper = new SubscriptionsHelper();

		// acquire user info
		$userId = User::whereEquals('username', $username)->row()->get('id');

		$subscriptions = $subHelper->loadSubscriptions($userId);

		$this->view->set('userId', $userId);
		$this->view->set('campaignId', $campaignId);
		$this->view->set('subs', $subscriptions);
		$this->view->set('code', $code);
		$this->view->display();
	}

	public function updateTask()
	{
		Request::checkToken();

		$code = Request::getString('code');
		$userId = Request::getString('userId');
		$username = User::whereEquals('id', $userId)->row()->get('username');
		$campaignId = Request::getInt('campaign');

		// Verify that the user-supplied URL and code are valid:
		if (!CodeHelper::validateEmailSubscriptionsCode($username, $campaignId, $code))
		{
			Notify::warning(Lang::txt('AUTH_CODE_INVALID'));
			App::redirect('/');
		}

		// Look up the subscription based on user id:
		$updatedSubscriptions = Request::getArray('subscriptions');
		$subHelper = new SubscriptionsHelper();

		$subHelper->updateSubscriptions(
			$userId,
			$updatedSubscriptions
		);

		Notify::success(Lang::txt('SUBSCRIPTION_UPDATE_SUCCESS'));
		App::redirect('/');
	}

}
