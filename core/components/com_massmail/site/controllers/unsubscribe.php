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
 * Public, tokenized unsubscribe endpoint. Sets the user's "Receive email
 * updates" preference to no (#__users.sendEmail = 0), which is the same field
 * the member profile edits and the recipient filter reads.
 *
 * A GET only shows a confirmation button; the preference changes on POST.
 * Mail gateways (Safe Links, URL Defense, link prefetchers) fetch every link
 * in a message, so a GET that opted the recipient out would unsubscribe
 * people who never clicked. The POST also serves RFC 8058 one-click
 * unsubscribe from the List-Unsubscribe-Post header.
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
	 * Confirm (GET) or process (POST) an unsubscribe request
	 *
	 * @return  void
	 */
	public function unsubscribeTask()
	{
		$email = trim(Request::getString('e', ''));
		$mid   = Request::getInt('m', 0);
		$token = Request::getString('t', '');

		$valid = ($email !== '' && $mid && Token::verify($mid, $email, $token));
		$done  = false;

		if ($valid && Request::method() == 'POST')
		{
			$user = \Hubzero\User\User::oneByEmail($email);

			if ($user && $user->get('id'))
			{
				$db = App::get('db');
				$db->setQuery(
					"UPDATE `#__users` SET `sendEmail` = 0 WHERE `id` = " . (int) $user->get('id')
				);
				$db->execute();
			}

			// Valid token: treat as success even for raw (non-user) addresses so
			// we always confirm the request to the recipient.
			$done = true;
		}

		$this->view
			->set('valid', $valid)
			->set('done', $done)
			->set('email', $email)
			->set('mailing', $mid)
			->set('token', $token)
			->setLayout('default')
			->display();
	}
}
