<?php
/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Site\Controllers;

$componentPath = Component::path('com_newsletter');

require_once  "$componentPath/helpers/codeHelper.php";
require_once  "$componentPath/models/accessCode.php";
require_once  "$componentPath/models/reply.php";

use Components\Newsletter\Helpers\CodeHelper;
use Components\Newsletter\Models\AccessCode;
use Components\Newsletter\Models\Reply;
use Hubzero\Component\SiteController;

class Replies extends SiteController
{

	public function createTask()
	{
		Request::checkToken();

		$code = Request::getString('code');
		$pageId = Request::getInt('page_id');

		// TODO: pass these values
		$username = Request::getString('user');
		$campaignid = Request::getInt('campaign');

		// TODO: why false?
		//if (!CodeHelper::validateCode($code, $pageId, false))
		if (!CodeHelper::validateCode($username, $campaignId, $pageId, $code))
		{
			Notify::warning(Lang::txt('AUTH_CODE_INVALID'));
			App::redirect('/');
		}

		$userId = User::whereEquals('username', $username)->row()->get('id');

		$reply = Reply::blank();
		$reply->set([
			'input'=> json_encode(Request::getArray('reply')),
			'page_id' => $pageId,
			'user_id' => $userId,
			'created' => Date::toSql()
		]);

		if ($reply->save())
		{
			Notify::success(Lang::txt('REPLY_CREATION_SUCCESS'));
			App::redirect('/');
		}
		else
		{
			Notify::error(Lang::txt('REPLY_CREATION_FAILURE'));
			App::redirect("/newsletter/pages/$pageId?code=$code");
		}
	}

}
