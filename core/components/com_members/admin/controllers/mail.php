<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Form\Form;
use Exception;
use Request;
use Config;
use Notify;
use Route;
use Lang;
use App;

/**
 * Send mass email to members
 */
class Mail extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		Lang::load($this->_option . '.mail', dirname(__DIR__));

		parent::execute();
	}

	/**
	 * Display a form for sending an email
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$session = App::get('session');
		$registry = $session->get('registry');

		$dflt = array();

		if (!is_null($registry))
		{
			$data = $registry->get('com_members.display.mail.data', $dflt);
		}

		$file = dirname(dirname(__DIR__)) . '/models/forms/mail.xml';
		$file = \Filesystem::cleanPath($file);

		//Form::addFieldPath(__DIR__ . '/fields');

		$form = new Form('mail', array('control' => 'fields'));

		if (!$form->loadFile($file, false, '//form'))
		{
			Notify::error(Lang::txt('JERROR_LOADFILE_FAILED'));
		}

		$form->bind($data);

		Request::setVar('hidemainmenu', 1);

		// Output the HTML
		$this->view
			->set('form', $form)
			->display();
	}

	/**
	 * Send an email
	 *
	 * @return  void
	 */
	public function sendTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Initialise variables.
		$data = Request::getArray('fields', array(), 'post');
		$db   = App::get('db');

		$mode     = array_key_exists('mode', $data) ? intval($data['mode']) : 0;
		$subject  = array_key_exists('subject', $data) ? $data['subject'] : '';
		$grp      = array_key_exists('group', $data) ? intval($data['group']) : 0;
		$recurse  = array_key_exists('recurse', $data) ? intval($data['recurse']) : 0;
		$bcc      = array_key_exists('bcc', $data) ? intval($data['bcc']) : 0;
		$disabled = array_key_exists('disabled', $data) ? intval($data['disabled']) : 0;
		$message_body = array_key_exists('message', $data) ? $data['message'] : '';

		// automatically removes html formatting
		if (!$mode)
		{
			$message_body = \Hubzero\Utility\Sanitize::clean($message_body);
		}

		// Check for a message body and subject
		if (!$message_body || !$subject)
		{
			$this->setUserState('com_members.display.mail.data', $data);

			Notify::error(Lang::txt('COM_MEMBERS_MAIL_PLEASE_FILL_IN_THE_FORM_CORRECTLY'));
			return $this->cancelTask();
		}

		// get users in the group out of the acl
		$to = \Hubzero\Access\Access::getUsersByGroup($grp, $recurse);

		// Get all users email and group except for senders
		$query = $db->getQuery();
		$query->select('email');
		$query->from('#__users');
		$query->where('id', '!=', (int) User::get('id'));
		if ($grp !== 0)
		{
			if (!empty($to))
			{
				$query->whereIn('id', $to);
			}
		}

		if ($disabled == 0)
		{
			$query->whereEquals('block', 0);
		}

		$db->setQuery($query->toString());
		$rows = $db->loadColumn();

		// Check to see if there are any users in this group before we continue
		if (!count($rows))
		{
			$this->setUserState('com_members.display.mail.data', $data);

			if (in_array($user->id, $to))
			{
				Notify::error(Lang::txt('COM_MEMBERS_MAIL_ONLY_YOU_COULD_BE_FOUND_IN_THIS_GROUP'));
			}
			else
			{
				Notify::error(Lang::txt('COM_MEMBERS_MAIL_NO_USERS_COULD_BE_FOUND_IN_THIS_GROUP'));
			}
			return $this->cancelTask();
		}

		// Get the Mailer
		$mailer = new \Hubzero\Mail\Message();

		// Build email message format.
		$mailer->setFrom(Config::get('mailfrom'), Config::get('fromname'));
		$mailer->setSubject($this->config->get('mailSubjectPrefix') . stripslashes($subject));
		$mailer->setBody($message_body . $this->config->get('mailBodySuffix'));

		// Add recipients
		if ($bcc)
		{
			$mailer->setBcc($rows);
			$mailer->addTo(Config::get('mailfrom'));
		}
		else
		{
			$mailer->setTo($rows);
		}

		// Send the Mail
		$rs = $mailer->send();

		// Check for an error
		if ($rs instanceof Exception)
		{
			$this->setUserState('com_members.display.mail.data', $data);

			Notify::error($rs->getError());
		}
		elseif (empty($rs))
		{
			$this->setUserState('com_members.display.mail.data', $data);

			Notify::error(Lang::txt('COM_MEMBERS_MAIL_THE_MAIL_COULD_NOT_BE_SENT'));
		}
		else
		{
			// Fill the data (specially for the 'mode', 'group' and 'bcc': they could not exist in the array
			// when the box is not checked and in this case, the default value would be used instead of the '0'
			// one)
			$data['mode'] = $mode;
			$data['subject'] = $subject;
			$data['group'] = $grp;
			$data['recurse'] = $recurse;
			$data['bcc'] = $bcc;
			$data['message'] = $message_body;

			$this->setUserState('com_members.display.mail.data', array());

			Notify::success(Lang::txts('COM_MEMBERS_MAIL_EMAIL_SENT_TO_N_USERS', count($rows)));
		}

		$this->cancelTask();
	}

	/**
	 * Set user state
	 *
	 * @param   string  $key
	 * @param   mixed   $val
	 * @return  void
	 */
	protected function setUserState($key, $val)
	{
		$session = App::get('session');
		$registry = $session->get('registry');

		if (!is_null($registry))
		{
			$registry->set($key, $val);
		}
	}

	/**
	 * Redirect back to main members listing
	 *
	 * @return  void
	 */
	public function cancelmailTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option, false)
		);
	}
}
