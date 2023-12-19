<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Helpers;

$componentPath = Component::path('com_newsletter');

require_once  "$componentPath/models/accessCode.php";
require_once  "$componentPath/secrets/code.php";

use Components\Newsletter\Models\AccessCode;

class CodeHelper
{

	public static function validateCode($code, $pageId)
	{
		$codeModel = AccessCode::all()->whereEquals('code', $code)->row();

		$exists = !$codeModel->isNew();
		$notExpired = !$codeModel->isExpired();
		$matchesPage = $codeModel->get('page_id') == $pageId;

		return $exists && $notExpired && $matchesPage;
	}

	public static function validateEmailSubscriptionsCode($code)
	{
		$emailSubsPageId = CODE_SECRETS['email_subscriptions_page_id'];

		return self::validateCode($code, $emailSubsPageId);
	}

}
