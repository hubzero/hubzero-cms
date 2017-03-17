<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Api\Controllers;

use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Component;
use Exception;
use stdClass;
use Request;
use Config;
use Route;
use Lang;

require_once(dirname(dirname(__DIR__)) . '/models/orm/comment.php');
require_once(dirname(dirname(__DIR__)) . '/models/orm/ticket.php');
require_once(dirname(dirname(__DIR__)) . '/models/orm/status.php');
require_once(dirname(dirname(__DIR__)) . '/models/orm/attachment.php');
require_once(dirname(dirname(__DIR__)) . '/helpers/acl.php');
require_once(dirname(dirname(__DIR__)) . '/helpers/utilities.php');

/**
 * API controller class for support tickets
 */
class Commentsv2_0 extends ApiController
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
	 * Display comments for a ticket
	 *
	 * @apiMethod GET
	 * @apiUri    /support/comments
	 * @apiParameter {
	 * 		"name":          "ticket",
	 * 		"description":   "List comments from a specific ticket (by id)",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       null,
	 * }
	 * @apiParameter {
	 * 		"name":          "created_by",
	 * 		"description":   "List comments from a specific user (by id)",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       null,
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

		$comments = \Components\Support\Models\Orm\Comment::all();

		if (Request::getInt('ticket', null))
		{
			$comments = $comments->whereEquals('ticket', Request::get('ticket'));
		}
		if (Request::getInt('created_by', null))
		{
			$comments = $comments->whereEquals('created_by', Request::get('created_by'));
		}

		$response = new stdClass;
		$response->total = $comments->count();
		$response->comments = array();
		foreach ($comments->rows() as $row)
		{
			$temp = array();
			$changelog = json_decode($row->changelog);
			$temp['id'] = $row->id;
			$temp['comment'] = $row->comment;
			$temp['created'] = $row->created;
			$temp['created_by'] = $row->created_by;
			$temp['changelog'] = $changelog;
			$temp['private'] = $row->access;

			$response->comments[] = $temp;
		}
		$this->send($response);
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
	 * 		"default":		 null
	 * }
	 * @apiParameter {
	 * 		"name":        "comment",
	 * 		"description": "Comment text",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":		 null
	 * }
	 * @apiParameter {
	 * 		"name":        "group",
	 * 		"description": "Group to assign the ticket to (by alias)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":		 null
	 * }
	 * @apiParameter {
	 * 		"name":        "owner",
	 * 		"description": "Id of the owner to assign ticket to",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":		 null
	 * }
	 * @apiParameter {
	 * 		"name":        "severity",
	 * 		"description": "Severity of the ticket",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":		 null
	 *		"allowed_values":	"minor, normal, major, critical"
	 * }
	 * @apiParameter {
	 * 		"name":        "status",
	 * 		"description": "Status of the ticket",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":		 null
	 * }
	 * @apiParameter {
	 * 		"name":        "target_date",
	 * 		"description": "Target date for completion of ticket (YYYY-MM-DD hh:mm:ss)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":		 null
	 * }
	 * @apiParameter {
	 * 		"name":        "cc",
	 * 		"description": "Comma seperated list of email addresses to email updates to",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":		 submitter,owner
	 * }
	 * @apiParameter {
	 * 		"name":        "private",
	 * 		"description": "Should the comment be flagged as private",
	 * 		"type":        "boolean",
	 * 		"required":    false,
	 * 		"default":		 false
	 * }
	 * @apiParameter {
	 * 		"name":        "email_submitter",
	 * 		"description": "Should the submitter be emailed about this comment",
	 * 		"type":        "boolean",
	 * 		"required":    false,
	 * 		"default":		 false
	 * }
	 * @apiParameter {
	 * 		"name":        "email_owner",
	 * 		"description": "Should the ticket owner be emailed about this comment",
	 * 		"type":        "boolean",
	 * 		"required":    false,
	 * 		"default":		 false
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

		$ticket_id = Request::getInt('ticket', null);
		if (!isset($ticket_id))
		{
			throw new Exception(Lang::txt('Bad request - ticket ID required'), 400);
		}

		$comment_text = Request::getString('comment', '');
		if ($comment_text == '')
		{
			throw new Exception(Lang::txt('Bad request - comment required'), 400);
		}

		$ticket = \Components\Support\Models\Orm\Ticket::oneOrFail($ticket_id);
		$comment = new \Components\Support\Models\Orm\Comment();
		$changelog = new stdClass;

		$comment->set('ticket', Request::get('ticket', ''));
		$comment->set('comment', nl2br(Request::get('comment')));
		$comment->set('created_by', User::get('id'));
		$comment->set('access', (Request::get('private', false) == 'true' ? 1 : 0));

		$changes = array();
		foreach (['group', 'owner', 'severity', 'status', 'target_date', 'category'] as $index)
		{
			if (Request::get($index, null))
			{
				if (Request::get($index) != $ticket->get($index))
				{
					$temp = new stdClass;
					$temp->field = $index;
					$temp->before = $ticket->get($index);
					$temp->after = Request::get($index);
					if ($index == 'status')
					{
						if ($ticket->get('status') == 0)
						{
							$status_model = new \Components\Support\Models\Orm\Status();
							$status_model->set('title', 'Closed');
							$status_model->set('open', 0);
						}
						else
						{
							$status_model = \Components\Support\Models\Orm\Status::oneOrFail(Request::get('status'));
						}
						if ($ticket->get('status') == 0)
						{
							$old_status = new \Components\Support\Models\Orm\Status();
							$old_status->set('title', 'Closed');
							$old_status->set('open', 0);
						}
						else
						{
							$old_status = \Components\Support\Models\Orm\Status::oneOrFail($ticket->get('status'));
						}
						$temp->before = $old_status->get('title');
						$temp->after = $status_model->get('title');
						$ticket->set('open', $status_model->get('open'));
						if ($status_model->get('get') == 'open' && $ticket->get('status', null) == 'closed')
						{
							$tiket->set('closed', '0000-00-00 00:00:00');
						}
						if ($status_model->get('get') == 'closed' && $ticket->get('status', null) == 'open')
						{
							$ticket->set('closed', Date::toSql());
						}
					}
					if ($index == 'owner')
					{
						$old_owner = User::getInstance($ticket->get('owner'));
						$new_owner = User::getInstance(Request::get('owner'));
						$temp->before = $old_owner->get('username');
						$temp->after = $new_owner->get('username');
					}
					$ticket->set($index, Request::get($index));
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
			throw new Exception(print_r($comment->getErrors(),1), 500);
		}
		if (!$ticket->save())
		{
			throw new Exception(print_r($ticket->getErrors(),1), 500);
		}

		// There's now a ticket and a comment, lets add attachments
		\Components\Support\Helpers\Utilities::addAttachments($ticket->get('id'), $comment->get('id'));

		$msg = new stdClass;
		$msg->id  = $comment->get('id');
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
			include_once(PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'helpers' . DS . 'utilities.php');

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
				'base_path' => PATH_CORE . '/components/com_support/site',
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
		$comment = \Components\Support\Models\Orm\Comment::oneOrFail($id);

		$response = new stdClass;
		$response->id = $comment->get('id');
		$response->ticket = $comment->get('ticket');
		$response->comment = $comment->get('comment');
		$response->created = $comment->get('created');

		$creator = $comment->creator;
		$response->created_by = new stdClass;
		$response->created_by->username = $creator->get('username');
		$response->created_by->name     = $creator->get('name');
		$response->created_by->id       = $creator->get('id');
		$response->created_by->email    = $creator->get('email');

		$response->changelog = $comment->get('changelog');
		$response->private = ($comment->get('access') ? true : false);

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
