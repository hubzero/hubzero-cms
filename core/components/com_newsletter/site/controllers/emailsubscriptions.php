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
require_once  "$componentPath/models/accessCode.php";
require_once  "$componentPath/models/emailSubscription.php";
require_once  "$componentPath/models/usersEmailSubscription.php";

use Components\Newsletter\Helpers\CodeHelper;
use Components\Newsletter\Helpers\SubscriptionsHelper;
use Components\Newsletter\Models\AccessCode;
use Components\Newsletter\Models\EmailSubscription;
use Components\Newsletter\Models\UsersEmailSubscription;

class Emailsubscriptions extends SiteController
{

	public function displayTask()
	{
		$code = Request::getString('code');
		// TODO: obtain values
		$username = Request::getString('username');
		$campaignId = Request::getInt('campaignId');

		// Verify that the user-supplied URL enables access:
		if (!CodeHelper::validateEmailSubscriptionsCode($username, $campaignId, $code))
		{
			Notify::warning(Lang::txt('AUTH_CODE_INVALID'));
			App::redirect('/');
		}

		$subHelper = new SubscriptionsHelper();
		$userId = User::whereEquals('username', $username)->get('id');
		$subscriptions = $subHelper->loadSubscriptions($userId);

		$this->view->set('userId', $userId);
		$this->view->set('subs', $subscriptions);
		$this->view->set('code', $code);
		$this->view->display();
	}

	public function updateTask()
	{
		Request::checkToken();

		$code = Request::getString('code');
		// TODO: obtain values
		$username = Request::getString('username');
		$campaignId = Request::getInt('campaignId');

		// Verify that the user-supplied URL enables access:
		if (!CodeHelper::validateEmailSubscriptionsCode($username, $campaignId, $code))
		{
			Notify::warning(Lang::txt('AUTH_CODE_INVALID'));
			App::redirect('/');
		}

		// Look up the subscription based on user id:
		$userId = User::whereEquals('username', $username)->get('id');
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
