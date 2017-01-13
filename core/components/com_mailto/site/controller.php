<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_mailto
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

/**
 * @package		Joomla.Site
 * @subpackage	com_mailto
 */
class MailtoController extends JControllerLegacy
{
	/**
	 * Show the form so that the user can send the link to someone
	 *
	 * @access public
	 * @since 1.5
	 */
	function mailto()
	{
		Session::set('com_mailto.formtime', time());
		Request::setVar('view', 'mailto');
		$this->display();
	}

	/**
	 * Send the message and display a notice
	 *
	 * @access public
	 * @since 1.5
	 */
	function send()
	{
		// Check for request forgeries
		Session::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		$timeout = Session::get('com_mailto.formtime', 0);
		if ($timeout == 0 || time() - $timeout < 20)
		{
			throw new Exception(Lang::txt('COM_MAILTO_EMAIL_NOT_SENT'), 500);
			return $this->mailto();
		}

		$SiteName = Config::get('sitename');
		$MailFrom = Config::get('mailfrom');
		$FromName = Config::get('fromname');

		$link = MailtoHelper::validateHash(Request::getCMD('link', '', 'post'));

		// Verify that this is a local link
		if (!$link || !JURI::isInternal($link))
		{
			//Non-local url...
			throw new Exception(Lang::txt('COM_MAILTO_EMAIL_NOT_SENT'), 500);
			return $this->mailto();
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

		/*
		 * Here is the meat and potatoes of the header injection test.  We
		 * iterate over the array of form input and check for header strings.
		 * If we find one, send an unauthorized header and die.
		 */
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
		if (! $email  || ! JMailHelper::isEmailAddress($email))
		{
			$error = Lang::txt('COM_MAILTO_EMAIL_INVALID', $email);
			Notify::warning($error);
		}

		// Check for a valid from address
		if (! $from || ! JMailHelper::isEmailAddress($from))
		{
			$error = Lang::txt('COM_MAILTO_EMAIL_INVALID', $from);
			Notify::warning($error);
		}

		if ($error)
		{
			return $this->mailto();
		}

		// Build the message to send
		$msg  = Lang::txt('COM_MAILTO_EMAIL_MSG');
		$body = sprintf($msg, $SiteName, $sender, $from, $link);

		// Clean the email data
		$subject = JMailHelper::cleanSubject($subject);
		$body    = JMailHelper::cleanBody($body);
		$sender  = JMailHelper::cleanAddress($sender);

		$mailer = new Hubzero\Mail\Message();
		$return = $mailer
			->addFrom($from, $sender)
			->addTo($email)
			->setSubject($subject)
			->setBody($body)
			->send();

		// Send the email
		if ($return !== true)
		{
			throw new Exception(Lang::txt('COM_MAILTO_EMAIL_NOT_SENT'), 500);
			return $this->mailto();
		}

		Request::setVar('view', 'sent');
		$this->display();
	}
}
