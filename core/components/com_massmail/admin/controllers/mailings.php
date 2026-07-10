<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Massmail\Models\Mailing;
use Components\Massmail\Models\Recipient;
use Components\Massmail\Models\Attachment;
use Components\Massmail\Helpers\Recipients;
use Components\Massmail\Helpers\Formatter;
use Components\Massmail\Helpers\Token;
use Filesystem;
use Request;
use Notify;
use Config;
use Route;
use User;
use Lang;
use Date;
use App;

require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'recipients.php';
require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'formatter.php';
require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'token.php';

/**
 * Admin controller for mass mailings.
 */
class Mailings extends AdminController
{
	/**
	 * Register tasks and execute
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * List mailings
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'search' => urldecode(Request::getState($this->_option . '.' . $this->_controller . '.search', 'search', '')),
			'status' => Request::getState($this->_option . '.' . $this->_controller . '.status', 'status', ''),
			'sort'   => Request::getState($this->_option . '.' . $this->_controller . '.sort', 'filter_order', 'created'),
			'sort_Dir' => Request::getState($this->_option . '.' . $this->_controller . '.sortdir', 'filter_order_Dir', 'DESC')
		);

		$rows = Mailing::all();

		if ($filters['search'])
		{
			$rows->whereLike('subject', strtolower((string) $filters['search']));
		}
		if ($filters['status'] !== '')
		{
			$rows->whereEquals('status', $filters['status']);
		}

		$rows = $rows
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->display();
	}

	/**
	 * Compose / edit a draft mailing
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row = null)
	{
		if (!User::authorise('core.edit', $this->_option) && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			$id = Request::getArray('id', array(0));
			$id = (is_array($id) && !empty($id)) ? $id[0] : $id;
			$row = Mailing::oneOrNew($id);
		}

		// Only drafts (or brand-new) may be edited.
		if (!$row->isNew() && $row->get('status') != Mailing::STATUS_DRAFT)
		{
			Notify::warning(Lang::txt('COM_MASSMAIL_ONLY_DRAFTS_EDITABLE'));
			return $this->cancelTask();
		}

		if ($row->isNew())
		{
			$row->set('from_name', Config::get('fromname'));
			$row->set('from_email', Config::get('mailfrom'));
		}

		$this->view
			->set('row', $row)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a draft mailing (and any uploaded attachments)
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		Request::checkToken();

		if (!User::authorise('core.edit', $this->_option) && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$fields = Request::getArray('fields', array(), 'post', 'none', 2);

		// The From/Reply-to address must match what the mail server is configured
		// to send as, so only a Super Administrator may set it; ignore any sender
		// fields submitted by anyone else (the form shows them read-only).
		if (!User::authorise('core.admin'))
		{
			unset($fields['from_name'], $fields['from_email'], $fields['reply_to']);
		}

		$row = Mailing::oneOrNew($fields['id'])->set($fields);

		// Never let a save move a mailing out of draft.
		if ($row->isNew() || !$row->get('status'))
		{
			$row->set('status', Mailing::STATUS_DRAFT);
		}

		// Assemble the recipient spec from the picker.
		$row->set('recipient_spec', json_encode($this->recipientSpec()));

		// Only honor the opt-out override for users allowed to request it; it
		// still needs separate approval before it can send.
		$override = Request::getInt('ignore_optout', 0)
			&& User::authorise('core.override', $this->_option);
		$row->set('ignore_optout', $override ? 1 : 0);

		// Optional scheduled delivery time: entered in the site's timezone,
		// stored as UTC. Empty clears any schedule (send as soon as launched).
		$sched = trim(Request::getString('scheduled_at', ''));
		if ($sched !== '')
		{
			try
			{
				$row->set('scheduled_at', Date::of($sched, Config::get('offset', 'UTC'))->toSql());
			}
			catch (\Exception $e)
			{
				$row->set('scheduled_at', null);
			}
		}
		else
		{
			$row->set('scheduled_at', null);
		}

		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		$this->handleUploads($row);

		Notify::success(Lang::txt('COM_MASSMAIL_MAILING_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}
		$this->cancelTask();
	}

	/**
	 * Build a recipient spec array from request input.
	 *
	 * @return  array
	 */
	protected function recipientSpec()
	{
		// Additive recipient list: any combination of everyone, groups, specific
		// users, and raw emails. Overlaps dedupe at expansion.
		$spec = array('all' => Request::getInt('rcpt_all', 0) ? true : false);

		// groups arrive as a single comma/space separated text field (groups[]) of
		// group IDs and/or cn aliases; keep the raw tokens (resolved at send time).
		$groupsRaw = implode(',', (array) Request::getArray('groups', array()));
		$spec['groups'] = array_values(array_filter(array_map('trim', preg_split('/[\s,]+/', $groupsRaw)), 'strlen'));

		// users: comma/space separated list of user IDs and/or usernames
		// (resolved at send time).
		$usersRaw = Request::getString('users', '');
		$spec['users'] = array_values(array_filter(array_map('trim', preg_split('/[\s,]+/', $usersRaw)), 'strlen'));

		// emails: one per line
		$emailsRaw = Request::getString('emails', '');
		$emails = array();
		foreach (preg_split('/[\r\n,;]+/', $emailsRaw) as $e)
		{
			$e = trim($e);
			if ($e !== '')
			{
				$emails[] = $e;
			}
		}
		$spec['emails'] = $emails;

		return $spec;
	}

	/**
	 * Is the mass-mail master switch on?
	 *
	 * @return  boolean
	 */
	protected function sendingEnabled()
	{
		return (bool) (int) \Component::params('com_massmail')->get('sending_enabled', 0);
	}

	/**
	 * Does this mailing carry a scheduled send time still in the future?
	 *
	 * @param   Mailing  $row
	 * @return  boolean
	 */
	protected function isScheduledFuture($row)
	{
		$s = (string) $row->get('scheduled_at');
		if ($s === '' || $s === '0000-00-00 00:00:00')
		{
			return false;
		}
		return strtotime($s . ' UTC') > time();
	}

	/**
	 * The status a launched/approved/released mailing should take: held when
	 * sending is off, scheduled when a future time is set, otherwise preparing.
	 *
	 * @param   Mailing  $row
	 * @return  string
	 */
	protected function dispatchStatus($row)
	{
		if (!$this->sendingEnabled())
		{
			return Mailing::STATUS_HELD;
		}
		if ($this->isScheduledFuture($row))
		{
			return Mailing::STATUS_SCHEDULED;
		}
		return Mailing::STATUS_PREPARING;
	}

	/**
	 * The scheduled time formatted in the viewer's local timezone.
	 *
	 * @param   Mailing  $row
	 * @return  string
	 */
	protected function scheduledLocal($row)
	{
		return Date::of($row->get('scheduled_at'))->toLocal('D, M j, Y g:i a T');
	}

	/**
	 * AJAX: autocomplete groups by cn alias or title for the recipient picker.
	 * Returns JSON [{cn, id, title}].
	 *
	 * @return  void
	 */
	public function grouplookupTask()
	{
		$q   = trim(Request::getString('q', ''));
		$out = array();

		if (strlen($q) >= 2)
		{
			$db   = \App::get('db');
			$like = $db->quote('%' . $q . '%');
			$db->setQuery(
				"SELECT gidNumber, cn, description FROM `#__xgroups`
				 WHERE published = 1 AND (cn LIKE $like OR description LIKE $like)
				 ORDER BY cn ASC LIMIT 15"
			);
			foreach ((array) $db->loadObjectList() as $g)
			{
				$out[] = array('cn' => $g->cn, 'id' => (int) $g->gidNumber, 'title' => (string) $g->description);
			}
		}

		header('Content-Type: application/json');
		echo json_encode($out);
		exit;
	}

	/**
	 * AJAX: autocomplete users by username or name for the recipient picker.
	 * Returns JSON [{cn, id, title}] (cn = username).
	 *
	 * @return  void
	 */
	public function userlookupTask()
	{
		$q   = trim(Request::getString('q', ''));
		$out = array();

		if (strlen($q) >= 2)
		{
			$db   = \App::get('db');
			$like = $db->quote('%' . $q . '%');
			$db->setQuery(
				"SELECT id, username, name FROM `#__users`
				 WHERE block = 0 AND (username LIKE $like OR name LIKE $like)
				 ORDER BY username ASC LIMIT 15"
			);
			foreach ((array) $db->loadObjectList() as $u)
			{
				$out[] = array('cn' => $u->username, 'id' => (int) $u->id, 'title' => (string) $u->name);
			}
		}

		header('Content-Type: application/json');
		echo json_encode($out);
		exit;
	}

	/**
	 * AJAX: predict how many recipients the current picker selection reaches,
	 * so the compose form can show a live audience count. Returns JSON.
	 *
	 * @return  void
	 */
	public function previewTask()
	{
		$spec   = $this->recipientSpec();
		$ignore = Request::getInt('ignore_optout', 0) ? true : false;
		$count  = Recipients::previewCount($spec, $ignore);

		header('Content-Type: application/json');
		echo json_encode(array(
			'count' => $count,
			'text'  => Lang::txt('COM_MASSMAIL_WILL_REACH', number_format($count))
		));
		exit;
	}

	/**
	 * Store any uploaded attachment files for a mailing.
	 *
	 * @param   Mailing  $mailing
	 * @return  void
	 */
	protected function handleUploads($mailing)
	{
		$files = Request::getArray('attachment', array(), 'files');
		if (empty($files) || empty($files['name']))
		{
			return;
		}

		$params  = \Component::params('com_massmail');
		$maxSize = (int) $params->get('max_attachment_size', 10485760);

		$dir = PATH_APP . DS . 'site' . DS . 'massmail' . DS . $mailing->get('id');
		if (!is_dir($dir))
		{
			Filesystem::makeDirectory($dir, 0755, true);
		}
		// Defense in depth: never let anything in the attachment store execute
		// or be fetched directly, even if it lands under a served path.
		$htaccess = PATH_APP . DS . 'site' . DS . 'massmail' . DS . '.htaccess';
		if (!is_file($htaccess))
		{
			Filesystem::write($htaccess, "Deny from all\n");
		}

		$names = (array) $files['name'];
		foreach ($names as $i => $name)
		{
			if ($name == '' || $files['error'][$i] != 0)
			{
				continue;
			}
			if ($maxSize && $files['size'][$i] > $maxSize)
			{
				Notify::warning(Lang::txt('COM_MASSMAIL_ATTACHMENT_TOO_LARGE', $name));
				continue;
			}

			$clean = basename(Filesystem::cleanPath($name));

			// Reject files whose name carries an executable/script extension in
			// ANY segment (blocks tricks like "report.php.pdf").
			if ($this->hasExecutableExtension($clean))
			{
				Notify::warning(Lang::txt('COM_MASSMAIL_ATTACHMENT_BLOCKED', $clean));
				continue;
			}

			$target = $dir . DS . $clean;

			if (Filesystem::upload($files['tmp_name'][$i], $target))
			{
				// Platform virus/safety scan; remove anything that fails.
				if (!Filesystem::isSafe($target))
				{
					Filesystem::delete($target);
					Notify::warning(Lang::txt('COM_MASSMAIL_ATTACHMENT_BLOCKED', $clean));
					continue;
				}

				$att = Attachment::blank()->set(array(
					'mailing_id' => $mailing->get('id'),
					'filename'   => $clean,
					'stored_path'=> $target,
					'size'       => (int) $files['size'][$i],
					'mimetype'   => (string) $files['type'][$i]
				));
				$att->save();
			}
		}
	}

	/**
	 * Does a filename carry a script/executable extension in any segment?
	 *
	 * @param   string   $name
	 * @return  boolean
	 */
	protected function hasExecutableExtension($name)
	{
		$executable = array(
			'php', 'php3', 'php4', 'php5', 'php7', 'phtml', 'phar', 'js', 'exe', 'java', 'perl', 'pl',
			'py', 'asp', 'aspx', 'dll', 'go', 'ade', 'adp', 'bat', 'chm', 'cmd', 'com', 'cpl', 'hta',
			'htaccess', 'ins', 'isp', 'jse', 'lib', 'mde', 'msc', 'msi', 'msp', 'mst', 'pif', 'scr',
			'sct', 'sh', 'shb', 'svg', 'sys', 'vb', 'vbe', 'vbs', 'vxd', 'wsc', 'wsf', 'wsh'
		);

		foreach (explode('.', strtolower($name)) as $segment)
		{
			if (in_array($segment, $executable))
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Remove an attachment
	 *
	 * @return  void
	 */
	public function removeattachmentTask()
	{
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit', $this->_option) && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$att = Attachment::oneOrFail(Request::getInt('attachment', 0));
		$mailingId = $att->get('mailing_id');

		if ($att->get('stored_path') && is_file($att->get('stored_path')))
		{
			Filesystem::delete($att->get('stored_path'));
		}
		$att->destroy();

		Request::setVar('id', array($mailingId));
		return $this->editTask();
	}

	/**
	 * Launch a mailing: freeze the rendered bodies and hand it to the queue
	 * (or to the approval gate when opt-out is being overridden).
	 *
	 * @return  void
	 */
	public function launchTask()
	{
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.send', $this->_option))
		{
			App::abort(403, Lang::txt('COM_MASSMAIL_NOT_AUTHORIZED_SEND'));
		}

		$id  = Request::getInt('id', 0);
		$row = Mailing::oneOrFail($id);

		if ($row->get('status') != Mailing::STATUS_DRAFT)
		{
			Notify::warning(Lang::txt('COM_MASSMAIL_ONLY_DRAFTS_LAUNCHABLE'));
			return $this->cancelTask();
		}

		// Validate content + audience.
		$spec = json_decode((string) $row->get('recipient_spec'), true) ?: array();
		$preview = Recipients::previewCount($spec, (int) $row->get('ignore_optout') ? true : false);

		if (!trim((string) $row->get('subject')) || !trim(strip_tags((string) $row->get('body'))))
		{
			Notify::error(Lang::txt('COM_MASSMAIL_MISSING_SUBJECT_OR_BODY'));
			return $this->editTask($row);
		}
		if ($preview < 1)
		{
			Notify::error(Lang::txt('COM_MASSMAIL_NO_RECIPIENTS'));
			return $this->editTask($row);
		}

		// Freeze the rendered bodies at launch time.
		$footer = (string) \Component::params('com_massmail')->get('footer_text', '');
		$row->set('body_text', Formatter::buildText($row->get('body'), $footer));
		$row->set('body_html', Formatter::buildHtml($row->get('body'), $footer));

		if ((int) $row->get('ignore_optout'))
		{
			// Overriding opt-out requires sign-off from someone with core.approve.
			$row->set('status', Mailing::STATUS_PENDING_APPROVAL);
			$row->save();
			Notify::warning(Lang::txt('COM_MASSMAIL_QUEUED_FOR_APPROVAL'));
		}
		else
		{
			// Hold (sending off), schedule (future time set), or send now.
			$status = $this->dispatchStatus($row);
			$row->set('status', $status);
			$row->save();

			if ($status == Mailing::STATUS_HELD)
			{
				Notify::warning(Lang::txt('COM_MASSMAIL_HELD_ON_LAUNCH'));
			}
			elseif ($status == Mailing::STATUS_SCHEDULED)
			{
				Notify::success(Lang::txt('COM_MASSMAIL_SCHEDULED_FOR', $this->scheduledLocal($row)));
			}
			else
			{
				Notify::success(Lang::txt('COM_MASSMAIL_LAUNCHED', $preview));
			}
		}

		$this->cancelTask();
	}

	/**
	 * Approve an opt-out-override mailing (requires core.approve)
	 *
	 * @return  void
	 */
	public function approveTask()
	{
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.approve', $this->_option))
		{
			App::abort(403, Lang::txt('COM_MASSMAIL_NOT_AUTHORIZED_APPROVE'));
		}

		$row = Mailing::oneOrFail(Request::getInt('id', 0));

		if ($row->get('status') != Mailing::STATUS_PENDING_APPROVAL)
		{
			Notify::warning(Lang::txt('COM_MASSMAIL_NOT_PENDING_APPROVAL'));
			return $this->cancelTask();
		}

		$row->set('approved_by', User::get('id'));
		$row->set('approved_on', Date::toSql());

		$status = $this->dispatchStatus($row);
		$row->set('status', $status);
		$row->save();

		if ($status == Mailing::STATUS_HELD)
		{
			Notify::warning(Lang::txt('COM_MASSMAIL_APPROVED_HELD'));
		}
		elseif ($status == Mailing::STATUS_SCHEDULED)
		{
			Notify::success(Lang::txt('COM_MASSMAIL_APPROVED_SCHEDULED', $this->scheduledLocal($row)));
		}
		else
		{
			Notify::success(Lang::txt('COM_MASSMAIL_APPROVED'));
		}
		$this->cancelTask();
	}

	/**
	 * Release a held mailing into the delivery queue (requires core.release).
	 * Holds only ever leave this state by an explicit human action, so a
	 * backlog accumulated while sending was off never auto-sends.
	 *
	 * @return  void
	 */
	public function releaseTask()
	{
		Request::checkToken(['get', 'post']);

		// Releasing a held backlog is its own permission (defaults to nobody but
		// Super Administrators until explicitly granted).
		if (!User::authorise('core.release', $this->_option))
		{
			App::abort(403, Lang::txt('COM_MASSMAIL_NOT_AUTHORIZED_RELEASE'));
		}

		$row = Mailing::oneOrFail(Request::getInt('id', 0));

		if ($row->get('status') != Mailing::STATUS_HELD)
		{
			Notify::warning(Lang::txt('COM_MASSMAIL_NOT_HELD'));
			return $this->cancelTask();
		}

		// A release respects any still-future schedule rather than sending now.
		if ($this->isScheduledFuture($row))
		{
			$row->set('status', Mailing::STATUS_SCHEDULED);
			$row->save();
			Notify::success(Lang::txt('COM_MASSMAIL_RELEASED_SCHEDULED', $this->scheduledLocal($row)));
		}
		else
		{
			$row->set('status', Mailing::STATUS_PREPARING);
			$row->save();
			if (!$this->sendingEnabled())
			{
				Notify::warning(Lang::txt('COM_MASSMAIL_RELEASED_SENDING_OFF'));
			}
			else
			{
				Notify::success(Lang::txt('COM_MASSMAIL_RELEASED'));
			}
		}
		$this->cancelTask();
	}

	/**
	 * Pause an active mailing
	 *
	 * @return  void
	 */
	public function pauseTask()
	{
		$this->transition(array(Mailing::STATUS_SENDING, Mailing::STATUS_PREPARING), Mailing::STATUS_PAUSED, 'COM_MASSMAIL_PAUSED');
	}

	/**
	 * Resume a paused mailing
	 *
	 * @return  void
	 */
	public function resumeTask()
	{
		$this->transition(array(Mailing::STATUS_PAUSED), Mailing::STATUS_SENDING, 'COM_MASSMAIL_RESUMED');
	}

	/**
	 * Cancel a mailing (stop sending permanently)
	 *
	 * @return  void
	 */
	public function stopTask()
	{
		$this->transition(
			array(Mailing::STATUS_SENDING, Mailing::STATUS_PREPARING, Mailing::STATUS_PAUSED, Mailing::STATUS_PENDING_APPROVAL),
			Mailing::STATUS_CANCELLED,
			'COM_MASSMAIL_CANCELLED'
		);
	}

	/**
	 * Shared state transition (edit.state gated)
	 *
	 * @param   array   $from
	 * @param   string  $to
	 * @param   string  $msg
	 * @return  void
	 */
	protected function transition($from, $to, $msg)
	{
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$row = Mailing::oneOrFail(Request::getInt('id', 0));

		if (!in_array($row->get('status'), $from))
		{
			Notify::warning(Lang::txt('COM_MASSMAIL_INVALID_TRANSITION'));
			return $this->cancelTask();
		}

		$row->set('status', $to);
		$row->save();

		Notify::success(Lang::txt($msg));
		$this->cancelTask();
	}

	/**
	 * Send a single test copy to the current user
	 *
	 * @return  void
	 */
	public function sendtestTask()
	{
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.send', $this->_option) && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$row = Mailing::oneOrFail(Request::getInt('id', 0));

		$footer = (string) \Component::params('com_massmail')->get('footer_text', '');
		$text = Formatter::buildText($row->get('body'), $footer);
		$html = Formatter::buildHtml($row->get('body'), $footer);
		$unsub = Token::unsubscribeUrl($row->get('id'), User::get('email'));
		$text = str_replace(Formatter::UNSUBSCRIBE_TOKEN, $unsub, $text);
		$html = str_replace(Formatter::UNSUBSCRIBE_TOKEN, $unsub, $html);

		try
		{
			$message = new \Hubzero\Mail\Message();
			$message->setSubject('[TEST] ' . $row->get('subject'));
			$message->addFrom($row->get('from_email') ?: Config::get('mailfrom'), $row->get('from_name') ?: Config::get('fromname'));
			$message->addPart($text, 'text/plain');
			$message->addPart($html, 'text/html');
			foreach ($row->attachments()->rows() as $att)
			{
				if ($att->get('stored_path') && is_file($att->get('stored_path')))
				{
					$message->addAttachment($att->get('stored_path'), $att->get('filename'));
				}
			}
			$message->setTo(array(User::get('email')));
			$message->send();
			Notify::success(Lang::txt('COM_MASSMAIL_TEST_SENT', User::get('email')));
		}
		catch (\Exception $e)
		{
			Notify::error(Lang::txt('COM_MASSMAIL_TEST_FAILED', $e->getMessage()));
		}

		Request::setVar('id', array($row->get('id')));
		return $this->editTask();
	}

	/**
	 * Show a mailing's delivery status/detail
	 *
	 * @return  void
	 */
	public function showTask()
	{
		$row = Mailing::oneOrFail(Request::getInt('id', 0));

		$failures = Recipient::all()
			->whereEquals('mailing_id', $row->get('id'))
			->whereEquals('status', Recipient::FAILED)
			->limit(100)
			->rows();

		$this->view
			->set('row', $row)
			->set('failures', $failures)
			->setLayout('show')
			->display();
	}

	/**
	 * Delete mailing(s) and their recipients/attachments
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$removed = 0;
		foreach ($ids as $id)
		{
			$row = Mailing::oneOrFail(intval($id));

			// Purge recipients
			$db = App::get('db');
			$db->setQuery("DELETE FROM `#__massmail_recipients` WHERE mailing_id = " . (int) $row->get('id'));
			$db->execute();

			// Purge attachments (rows + files)
			foreach ($row->attachments()->rows() as $att)
			{
				if ($att->get('stored_path') && is_file($att->get('stored_path')))
				{
					Filesystem::delete($att->get('stored_path'));
				}
				$att->destroy();
			}

			if ($row->destroy())
			{
				$removed++;
			}
		}

		if ($removed)
		{
			Notify::success(Lang::txt('COM_MASSMAIL_MAILINGS_DELETED', $removed));
		}
		$this->cancelTask();
	}
}
