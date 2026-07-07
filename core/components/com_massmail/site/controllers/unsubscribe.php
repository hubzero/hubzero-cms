<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Massmail\Helpers\Token;
use Request;
use App;

/**
 * Public, tokenized unsubscribe endpoint. Sets the user's mail preference to
 * "do not send" (jos_xprofiles.mailPreferenceOption = 0).
 */
class Unsubscribe extends SiteController
{
	/**
	 * Default to the unsubscribe task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('__default', 'unsubscribe');

		parent::execute();
	}

	/**
	 * Process an unsubscribe request
	 *
	 * @return  void
	 */
	public function unsubscribeTask()
	{
		$email = trim(Request::getString('e', ''));
		$mid   = Request::getInt('m', 0);
		$token = Request::getString('t', '');

		$ok = false;

		if ($email !== '' && $mid && Token::verify($mid, $email, $token))
		{
			$user = \Hubzero\User\User::oneByEmail($email);

			if ($user && $user->get('id'))
			{
				$db = App::get('db');
				$db->setQuery(
					"UPDATE `#__xprofiles` SET `mailPreferenceOption` = 0 WHERE `uidNumber` = " . (int) $user->get('id')
				);
				$db->execute();
			}

			// Valid token: treat as success even for raw (non-user) addresses so
			// we always confirm the request to the recipient.
			$ok = true;
		}

		$this->view
			->set('ok', $ok)
			->set('email', $email)
			->setLayout('default')
			->display();
	}
}
