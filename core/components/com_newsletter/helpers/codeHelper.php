<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Helpers;

$componentPath = Component::path('com_newsletter');

require_once  "$componentPath/models/accessCode.php";
require_once  "$componentPath/models/campaign.php";
require_once  "$componentPath/secrets/code.php";

use Components\Newsletter\Models\AccessCode;
use Components\Newsletter\Models\Campaign;

class CodeHelper
{

	// Validate that the user-supplied URL and code are valid:
	public static function validateCode($username, $campaignId, $pageId, $code)
	{
		// acquire user info
		$userId = User::whereEquals('username', $username)->row()->get('id');

		// acquire campaign info
		$campModel = Campaign::all()->whereEquals('id', $campaignId)->row();
		$campNotExpired = !$campModel->isExpired();

		// Does the access_code model contain this user and page:
		$accessCodeModel = AccessCode::whereEquals('user_id', $userId)->
								whereEquals('page_id', $pageId)->count();
		$userHasPageAccess = (bool)(1 == $accessCodeModel);

		// Calculate and compare hash of hub, user, and campaign secrets to passed code:
		$database = \App::get('db');
		$sql = "SELECT hash_access_code($campaignId, $username)";
		$database->setQuery($sql);

		$hashMatches = ($code == $database->loadResult());

		// Is this access valid?
		return ($hashMatches && $campNotExpired && $userHasPageAccess);
	}

	// Validate code obtained from user's URL, using email subscription page id
	public static function validateEmailSubscriptionsCode($username, $campaignId, $code)
	{
		// Acquire page Id for email subscription:
		$emailSubsPageId = CODE_SECRETS['email_subscriptions_page_id'];

		// Validate user-supplied URL and code for this page:
		return self::validateCode($username, $campaignId, $emailSubsPageId, $code);
	}
}
