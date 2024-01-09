<?php
/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Site\Controllers;

$componentPath = Component::path('com_newsletter');

require_once  "$componentPath/helpers/codeHelper.php";

use Hubzero\Component\SiteController;
use Components\Newsletter\Helpers\CodeHelper;

class Pages extends SiteController
{

	public function displayTask()
	{
		$code = Request::getString('code');
		$pageId = Request::getInt('id');
		// TODO: pass these values
		$username = Request::getString('user');
		$campaignId = Request::getInt('campaign');

		//if (!CodeHelper::validateCode($code, $pageId))
		if (!CodeHelper::validateCode($username, $campaignId, $pageId, $code))
		{
			Notify::warning(Lang::txt('AUTH_CODE_INVALID'));
			App::redirect('/');
		}

		$this->setView('pages', "page$pageId");
		$this->view->set('code', $code);
		$this->view->set('pageId', $pageId);
		$this->view->display();
	}

}
