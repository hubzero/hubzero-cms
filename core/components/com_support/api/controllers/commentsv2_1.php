<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Api\Controllers;

use Components\Support\Models\Comment;
use Components\Support\Models\Ticket;
use Components\Support\Models\Status;
use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Component;
use Exception;
use stdClass;
use Request;
use Config;
use Route;
use Lang;
use User;

require_once dirname(dirname(__DIR__)) . '/models/ticket.php';
require_once dirname(dirname(__DIR__)) . '/helpers/acl.php';
require_once dirname(dirname(__DIR__)) . '/helpers/utilities.php';

/**
 * API controller class for support tickets
 */
class Commentsv2_1 extends ApiController
{
	/**
	 * Execute a request
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->config   = Component::params('com_support');
		$this->database = \App::get('db');

		$this->acl = \Components\Support\Helpers\ACL::getACL();
		$this->acl->setUser(User::get('id'));

		parent::execute();
	}

	/**
	 * Display ticket comments
	 *
	 * @apiMethod GET
	 * @apiUri    /support/comments/list
	 * @apiParameter {
	 * 		"name":          "ticket",
	 * 		"description":   "List comments from a specific ticket (by id)",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "created_by",
	 * 		"description":   "List comments from a specific user (by id)",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "access",
	 * 		"description":   "Show only private (1) or non-private (0) comments",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0,
	 * 		"allowedValues": "0, 1"
	 * }
	 * @apiParameter {
	 * 		"name":          "search",
	 * 		"description":   "A word or phrase to search for.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "created",
	 * 		"description":   "A timestamp (YYYY-MM-DD HH:mm:ss) for items created on or after the specified date. A time window can be specified adding a second timestamp, separated by a comma. Example: 2018-01-01,2018-12-31",
	 * 		"type":          "string|integer",
	 * 		"required":      false,
	 * 		"default":       null,
	 * 		"allowedValues": "YYYY-MM or YYYY-MM-DD or YYYY-MM-DD HH:mm:ss"
	 * }
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "start",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "sort",
	 * 		"description":   "Field to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "created",
	 * 		"allowedValues": "id, created, created_by, comment, ticket, access"
	 * }
	 * @apiParameter {
	 * 		"name":          "sort_Dir",
	 * 		"description":   "Direction to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "desc",
	 * 		"allowedValues": "asc, desc"
	 * }
	 * @return    void
	 */
	public function listTask()
	{
		$this->requiresAuthentication();

		if (!$this->acl->check('read', 'tickets'))
		{
			throw new Exception(Lang::txt('Not authorized'), 403);
		}

		$comments = Comment::all();

		if ($ticket = Request::getInt('ticket', 0))
		{
			$comments->whereEquals('ticket', $ticket);
		}

		if ($created_by = Request::getInt('created_by', 0))
		{
			$comments->whereEquals('created_by', $created_by);
		}

		if ($search = Request::getString('search', ''))
		{
			$comments->whereLike('comment', $search);
		}

		$created = $this->toTimestamp(Request::getString('created', ''));
		if ($created)
		{
			if (is_array($created) && count($created) > 1)
			{
				$comments->where('created', '>=', $created[0], 1)
					->orWhere('created', '<', $created[1], 1)
					->resetDepth();
			}
			else
			{
				if (is_array($created))
				{
					$created = implode('', $created);
				}
				$comments->where('created', '>=', $created);
			}
		}

		$access = Request::getInt('access', 0);

		if ($access != 0 && !$this->acl->check('read', 'private_comments'))
		{
			$access = 0;
		}

		$comments->whereEquals('access', $access);

		$total = clone $comments;

		$sort = Request::getWord('sort', 'created');
		if (!in_array($sort, array('id', 'created', 'created_by', 'comment', 'ticket', 'access')))
		{
			$sort = 'created';
		}
		$sort_dir = Request::getWord('sort_Dir', 'desc');
		if (!in_array($sort_dir, array('asc', 'desc')))
		{
			$sort_dir = 'desc';
		}

		$rows = $comments->order($sort, $sort_dir)
			->limit(Request::getInt('limit', 25))
			->start(Request::getInt('start', 0))
			->rows();

		$base = rtrim(Request::base(), '/');

		$response = new stdClass;
		$response->total = $total->total();
		$response->comments = array();
		foreach ($rows as $row)
		{
			$temp = $row->toArray();

			$temp['comment'] = $row->comment;
			$temp['created'] = with(new Date($temp['created']))->format('Y-m-d\TH:i:s\Z');
			$temp['changelog'] = json_decode($row->changelog);
			$temp['link'] = str_replace('/api', '', $base . '/' . ltrim(Route::url($row->link()), DS));

			$response->comments[] = $temp;
		}
		$this->send($response);
	}

	/**
	 * Ensure timestamp follows accepted pattern
	 *
	 * @param   string  $val  Timestamp or two timestamps separated by a comma
	 *                        YYYY-MM or YYYY-MM-DD or YYYY-MM-DD HH:mm:ss or YYYY-MM,YYYY-MM
	 * @return  mixed   string or null if not a valid timestamp
	 */
	private function toTimestamp($val=null)
	{
		if ($val)
		{
			$val = strtolower($val);

			if (strstr($val, ','))
			{
				$vals = explode(',', $val);
				foreach ($vals as $i => $v)
				{
					$vals[$i] = $this->toTimestamp(trim($v));
				}
				return $vals;
			}

			if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $val, $regs))
			{
				// Time already matches pattern so do nothing.
			}
			else if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $val, $regs))
			{
				$val .= ' 00:00:00';
			}
			else if (preg_match("/([0-9]{4})-([0-9]{2})/", $val, $regs))
			{
				$val .= '-01 00:00:00';
			}
			else
			{
				// Not an acceptable time
				$val = null;
			}
		}

		return $val;
	}

	/**
	 * Create a new comment
	 *
	 * @apiMethod POST
	 * @apiUri    /support/comments
	 * @apiParameter {
	 * 		"name":        "ticket",
	 * 		"description": "Id of the ticket to make a comment on",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "comment",
	 * 		"description": "Comment text",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "group",
	 * 		"description": "Group to assign the ticket to (by alias)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "owner",
	 * 		"description": "Id of the owner to assign ticket to",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "severity",
	 * 		"description": "Severity of the ticket",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null,
	 *		"allowed_values": "minor, normal, major, critical"
	 * }
	 * @apiParameter {
	 * 		"name":        "status",
	 * 		"description": "Status of the ticket",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "target_date",
	 * 		"description": "Target date for completion of ticket (YYYY-MM-DD hh:mm:ss)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "cc",
	 * 		"description": "Comma separated list of email addresses to email updates to",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "submitter, owner"
	 * }
	 * @apiParameter {
	 * 		"name":        "private",
	 * 		"description": "Should the comment be flagged as private",
	 * 		"type":        "boolean",
	 * 		"required":    false,
	 * 		"default":     false
	 * }
	 * @apiParameter {
	 * 		"name":        "email_submitter",
	 * 		"description": "Should the submitter be emailed about this comment",
	 * 		"type":        "boolean",
	 * 		"required":    false,
	 * 		"default":     false
	 * }
	 * @apiParameter {
	 * 		"name":        "email_owner",
	 * 		"description": "Should the ticket owner be emailed about this comment",
	 * 		"type":        "boolean",
	 * 		"required":    false,
	 * 		"default":     false
	 * }
	 * @return     void
	 */
	public function createTask()
	{
		$this->requiresAuthentication();

		if (!$this->acl->check('create', 'comments'))
		{
			throw new Exception(Lang::txt('Not authorized'), 403);
		}

		$ticket_id = Request::getInt('ticket', 0);
		if (!$ticket_id)
		{
			throw new Exception(Lang::txt('Bad request - ticket ID required'), 400);
		}

		$comment_text = Request::getString('comment', '');
		if ($comment_text == '')
		{
			throw new Exception(Lang::txt('Bad request - comment required'), 400);
		}

		$ticket = Ticket::oneOrFail($ticket_id);

		$comment = Comment::blank();
		$changelog = new stdClass;

		$comment->set('ticket', $ticket->get('id'));
		$comment->set('comment', nl2br(Request::getString('comment')));
		$comment->set('created_by', User::get('id'));
		$comment->set('access', (Request::getBool('private', false) == 'true' ? 1 : 0));

		$changes = array();
		foreach (['group_id', 'owner', 'severity', 'status', 'target_date', 'category'] as $index)
		{
			if ($val = Request::getVar($index, null))
			{
				if ($val != $ticket->get($index))
				{
					$temp = new stdClass;
					$temp->field  = $index;
					$temp->before = $ticket->get($index);
					$temp->after  = $val;

					if ($index == 'status')
					{
						if ($ticket->get('status') == 0)
						{
							$status_model = Status::blank();
							$status_model->set('title', 'Closed');
							$status_model->set('open', 0);
						}
						else
						{
							$status_model = Status::oneOrFail(Request::getInt('status'));
						}

						if ($ticket->get('status') == 0)
						{
							$old_status = Status::blank();
							$old_status->set('title', 'Closed');
							$old_status->set('open', 0);
						}
						else
						{
							$old_status = Status::oneOrFail($ticket->get('status'));
						}

						$temp->before = $old_status->get('title');
						$temp->after  = $status_model->get('title');

						$ticket->set('open', $status_model->get('open'));

						if ($status_model->get('get') == 'open' && $ticket->get('status', null) == 'closed')
						{
							$tiket->set('closed', null);
						}

						if ($status_model->get('get') == 'closed' && $ticket->get('status', null) == 'open')
						{
							$ticket->set('closed', Date::toSql());
						}
					}

					if ($index == 'owner')
					{
						$old_owner = User::getInstance($ticket->get('owner'));
						$new_owner = User::getInstance(Request::getInt('owner'));
						$temp->before = $old_owner->get('username');
						$temp->after  = $new_owner->get('username');
					}

					$ticket->set($index, $val);

					$changes[] = $temp;
				}
			}
		}

		$changelog->changes = $changes;

		if ($comment->get('comment'))
		{
			// If a comment was posted by the ticket submitter to a "waiting user response" ticket, change status.
			$user = User::getInstance(User::get('id'));
			if ($ticket->get('status') == 2 && $user->get('username') == $ticket->get('login'))
			{
				$ticket->set('status', 0);
			}
		}

		$comment->set('changelog', json_encode($changelog));
		if (!$comment->save())
		{
			throw new Exception($comment->getErrors(), 500);
		}
		if (!$ticket->save())
		{
			throw new Exception($ticket->getErrors(), 500);
		}

		// There's now a ticket and a comment, lets add attachments
		\Components\Support\Helpers\Utilities::addAttachments($ticket->get('id'), $comment->get('id'));

		$msg = new stdClass;
		$msg->id = $comment->get('id');
		$msg->notified = $comment->get('changelog');

		$this->send($msg, 200, 'OK');
		/*
		$changlog->notifications = array();
		if (Request::get('email_owner'))
		{
			$comment->addTo(array(
				'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_OWNER'),
				'name'  => $ticket->get_owner->get('name'),
				'email' => $ticket->get_owner->get('email'),
				'id'    => $ticket->get_owner->get('id')
			));
			$changelog->notifications[] = json_encode(array('role'=>'Ticket owner', 'address'=>$ticket->get_owner()->get('email'), 'name'=>$ticket->get_owner()->get('name')));
		}

		// Add any CCs to the e-mail list
		$cc = Request::get('cc', null);
		if ($cc)
		{
			$cc = explode(',', $cc);
			foreach ($cc)
			{
				$comment->addTo($cc, Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC'));
			}
			$changelog->cc = json_encode($cc);
		}

		// Check if the notify list has eny entries
		if (count($comment->to()))
		{
			include_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'utilities.php';

			$allowEmailResponses = $ticket->config('email_processing');
			if ($allowEmailResponses)
			{
				try
				{
					$encryptor = new \Hubzero\Mail\Token();
				}
				catch (Exception $e)
				{
					$allowEmailResponses = false;
				}
			}

			$subject = Lang::txt('COM_SUPPORT_EMAIL_SUBJECT_TICKET_COMMENT', $ticket->get('id'));

			$from = array(
				'name'      => Lang::txt('COM_SUPPORT_EMAIL_FROM', Config::get('sitename')),
				'email'     => Config::get('mailfrom'),
				'multipart' => md5(date('U'))
			);

			$message = array();

			// Plain text email
			$eview = new \Hubzero\Mail\View(array(
				'base_path' => dirname(dirname(__DIR__)) . '/site',
				'name'      => 'emails',
				'layout'    => 'comment_plain'
			));
			$eview->option     = 'com_support';
			$eview->controller = 'tickets';
			$eview->comment    = $comment;
			$eview->ticket     = $ticket;
			$eview->delimiter  = ($allowEmailResponses ? '~!~!~!~!~!~!~!~!~!~!' : '');

			$message['plaintext'] = $eview->loadTemplate(false);
			$message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

			// HTML email
			$eview->setLayout('comment_html');
			$message['multipart'] = $eview->loadTemplate();

			// Send e-mail to admin?
			foreach ($comment->to('ids') as $to)
			{
				if ($allowEmailResponses)
				{
					// The reply-to address contains the token
					$token = $encryptor->buildEmailToken(1, 1, $to['id'], $ticket->get('id'));
					$from['replytoemail'] = 'htc-' . $token . strstr(Config::get('mailfrom'), '@');
				}

				// Get the user's email address
				if (!Event::trigger('xmessage.onSendMessage', array('support_reply_submitted', $subject, $message, $from, array($to['id']), 'com_support')))
				{
					$this->setError(Lang::txt('COM_SUPPORT_ERROR_FAILED_TO_MESSAGE', $to['name'] . '(' . $to['role'] . ')'));
				}
				$comment->changelog()->notified(
					$to['role'],
					$to['name'],
					$to['email']
				);
			}

			foreach ($comment->to('emails') as $to)
			{
				if ($allowEmailResponses)
				{
					$token = $encryptor->buildEmailToken(1, 1, -9999, $ticket->get('id'));

					$email = array(
						$to['email'],
						'htc-' . $token . strstr(Config::get('mailfrom'), '@')
					);

					// In this case each item in email in an array, 1- To, 2:reply to address
					\Components\Support\Helpers\Utilities::sendEmail($email[0], $subject, $message, $from, $email[1]);
				}
				else
				{
					// email is just a plain 'ol string
					\Components\Support\Helpers\Utilities::sendEmail($to['email'], $subject, $message, $from);
				}

				$comment->changelog()->notified(
					$to['role'],
					$to['name'],
					$to['email']
				);
			}
		}
		$comment->set('changelog', json_encode($changelog));
		$comment->save();
		$ticket->save();

		$msg = new stdClass;
		$msg->id  = $comment->get('id');
		$msg->notified = $comment->get('changelog');

		$this->send($msg, 200, 'OK');
		*/
	}

	/**
	 * Displays details for a ticket comment
	 *
	 * @apiMethod GET
	 * @apiUri    /support/comments/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Comment identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     0
	 * }
	 * @return    void
	 */
	public function readTask()
	{
		$this->requiresAuthentication();

		if (!$this->acl->check('read', 'tickets'))
		{
			throw new Exception(Lang::txt('Not authorized'), 403);
		}

		// Initiate class and bind data to database fields
		$id = Request::getInt('id', 0);

		// Initiate class and bind data to database fields
		$comment = Comment::oneOrFail($id);

		if ($comment->isPrivate() && !$this->acl->check('read', 'private_comments'))
		{
			throw new Exception(Lang::txt('Not authorized'), 403);
		}

		$changelog = json_decode($comment->changelog);

		$response = $comment->toObject();
		$response->changelog = $changelog;
		$response->created = with(new Date($response->created))->format('Y-m-d\TH:i:s\Z');

		$base = rtrim(Request::base(), '/');
		$response->link = str_replace('/api', '', $base . '/' . ltrim(Route::url($comment->link()), DS));

		$this->send($response);
	}

	/**
	 * Update a ticket comment
	 *
	 * @apiMethod PUT
	 * @apiUri    /support/comments/{id}
	 * @apiParameter {
	 * 		"name":        "comment",
	 * 		"description": "Comment identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     0
	 * }
	 * @return    void
	 */
	public function updateTask()
	{
		$this->requiresAuthentication();

		if (!$this->acl->check('edit', 'comments'))
		{
			throw new Exception(Lang::txt('Not authorized'), 403);
		}

		throw new Exception(Lang::txt('Operation not supported'), 404);
	}

	/**
	 * Delete a ticket comment
	 *
	 * @apiMethod DELETE
	 * @apiUri    /support/{ticket}/comments/{comment}
	 * @apiParameter {
	 * 		"name":        "ticket",
	 * 		"description": "Ticket identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "comment",
	 * 		"description": "Comment identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     0
	 * }
	 * @return    void
	 */
	public function deleteTask()
	{
		$this->requiresAuthentication();

		if (!$this->acl->check('delete', 'comments'))
		{
			throw new Exception(Lang::txt('Not authorized'), 403);
		}

		throw new Exception(Lang::txt('Operation not supported'), 404);
	}
}
