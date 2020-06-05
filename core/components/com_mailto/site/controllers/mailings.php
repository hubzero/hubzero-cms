<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Mailto\Site\Controllers;

use Hubzero\Component\SiteController;
use Hubzero\Utility\Validate;
use Hubzero\Mail\Message;
use Components\Mailto\Helpers\Mailto as MailtoHelper;
use stdClass;
use Request;
use Session;
use Config;
use Notify;
use User;
use Lang;
use App;

/**
 * Mailings controller
 */
class Mailings extends SiteController
{
	/**
	 * Show the form so that the user can send the link to someone
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		Session::set('com_mailto.formtime', time());

		$data = new stdClass();

		$data->link = urldecode(Request::getString('link', '', 'method', 'base64'));

		if ($data->link == '')
		{
			App::abort(402, Lang::txt('COM_MAILTO_LINK_IS_MISSING'));
		}

		// Load with previous data, if it exists
		$mailto  = Request::getString('mailto', '', 'post');
		$sender  = Request::getString('sender', '', 'post');
		$from    = Request::getString('from', '', 'post');
		$subject = Request::getString('subject', '', 'post');

		if (User::get('id') > 0)
		{
			$data->sender = User::get('name');
			$data->from   = User::get('email');
		}
		else
		{
			$data->sender = $sender;
			$data->from   = $from;
		}

		$data->subject = $subject;
		$data->mailto  = $mailto;

		$this->view
			->set('data', $data)
			->setLayout('display')
			->display();
	}

	/**
	 * Send the message and display a notice
	 *
	 * @return  void
	 */
	public function sendTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$timeout = Session::get('com_mailto.formtime', 0);

		if ($timeout == 0 || time() - $timeout < 20)
		{
			Notify::error(Lang::txt('COM_MAILTO_EMAIL_NOT_SENT'));
			return $this->displayTask();
		}

		$SiteName = Config::get('sitename');
		$MailFrom = Config::get('mailfrom');
		$FromName = Config::get('fromname');

		$link = MailtoHelper::validateHash(Request::getCmd('link', '', 'post'));

		// Verify that this is a local link
		if (!$link || !\Hubzero\Utility\Uri::isInternal($link))
		{
			//Non-local url...
			Notify::error(Lang::txt('COM_MAILTO_EMAIL_NOT_SENT'));
			return $this->displayTask();
		}

		// An array of email headers we do not want to allow as input
		$headers = array(
			'Content-Type:',
			'MIME-Version:',
			'Content-Transfer-Encoding:',
			'bcc:',
			'cc:'
		);

		// An array of the input fields to scan for injected headers
		$fields = array(
			'mailto',
			'sender',
			'from',
			'subject',
		);

		// Here is the meat and potatoes of the header injection test.  We
		// iterate over the array of form input and check for header strings.
		// If we find one, send an unauthorized header and die.
		foreach ($fields as $field)
		{
			foreach ($headers as $header)
			{
				if (strpos($_POST[$field], $header) !== false)
				{
					App::abort(403, '');
				}
			}
		}

		// Free up memory
		unset($headers, $fields);

		$email           = Request::getString('mailto', '', 'post');
		$sender          = Request::getString('sender', '', 'post');
		$from            = Request::getString('from', '', 'post');
		$subject_default = Lang::txt('COM_MAILTO_SENT_BY', $sender);
		$subject         = Request::getString('subject', $subject_default, 'post');

		// Check for a valid to address
		$error = false;
		if (!$email || !Validate::email($email))
		{
			$error = Lang::txt('COM_MAILTO_EMAIL_INVALID', $email);
			Notify::warning($error);
		}

		// Check for a valid from address
		if (!$from || !Validate::email($from))
		{
			$error = Lang::txt('COM_MAILTO_EMAIL_INVALID', $from);
			Notify::warning($error);
		}

		if ($error)
		{
			return $this->displayTask();
		}

		// Build the message to send
		$msg  = Lang::txt('COM_MAILTO_EMAIL_MSG');
		$body = sprintf($msg, $SiteName, $sender, $from, $link);

		// Clean the email data
		$subject = MailtoHelper::cleanSubject($subject);
		$body    = MailtoHelper::cleanBody($body);
		$sender  = MailtoHelper::cleanAddress($sender);

		$mailer = new Message();
		$return = $mailer
			->addFrom($from, $sender)
			->addTo($email)
			->setSubject($subject)
			->setBody($body)
			->send();

		// Send the email
		if ($return !== true)
		{
			Notify::error(Lang::txt('COM_MAILTO_EMAIL_NOT_SENT'));
			return $this->displayTask();
		}

		$this->view
			->setLayout('sent')
			->display();
	}
}
