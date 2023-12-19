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

		if (!CodeHelper::validateEmailSubscriptionsCode($code, false))
		{
			Notify::warning(Lang::txt('AUTH_CODE_INVALID'));
			App::redirect('/');
		}

		$subHelper = new SubscriptionsHelper();
		$codeM = AccessCode::all()->whereEquals('code', $code)->row();
		$userId = $codeM->get('user_id');
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

		if (!CodeHelper::validateEmailSubscriptionsCode($code, false))
		{
			Notify::warning(Lang::txt('AUTH_CODE_INVALID'));
			App::redirect('/');
		}

		$codeM = AccessCode::all()->whereEquals('code', $code)->row();
		$updatedSubscriptions = Request::getArray('subscriptions');
		$subHelper = new SubscriptionsHelper();

		$subHelper->updateSubscriptions(
			$codeM->get('user_id'),
			$updatedSubscriptions
		);

		Notify::success(Lang::txt('SUBSCRIPTION_UPDATE_SUCCESS'));
		App::redirect('/');
	}

}
