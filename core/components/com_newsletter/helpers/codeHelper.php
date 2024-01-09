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

	// Validate code passed in user's URL, with user, campaign, and page information.
	public static function validateCode($username, $campaignId, $pageId, $code)
	{
		// acquire user info
		$userId = User::all()->whereEquals('username', $username)->get('id');

		// acquire campaign info
		$campModel = Campaign::all()->whereEquals('id', $campaignId)->row();
		$campNotExpired = !$campModel->isExpired();

		// the access_code model contains user/page relationship
		$codeModel = AccessCode::all()->whereEquals('user_id', $userId)->row();
		$codeMatchesPage = $codeModel->get('page_id') == $pageId;

		// Calculate and compare hash of hub, user, and campaign secrets to passed code:
		$database = \App::get('db');
		$sql = "SELECT hash_access_code($campaignId, $username)";
		$database->setQuery($sql);

		$hashMatches = ($code == $database->loadResult());

		return ($hashMatches && $campNotExpired && $codeMatchesPage);
	}

	// Validate code obtained from user's URL, using email subscription page id
	public static function validateEmailSubscriptionsCode($username, $campaignId, $code)
	{
		$emailSubsPageId = CODE_SECRETS['email_subscriptions_page_id'];

		return self::validateCode($username, $campaignId, $emailSubsPageId, $code);
	}
}
