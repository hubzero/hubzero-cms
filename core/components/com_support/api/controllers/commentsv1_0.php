<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'ticket.php';
require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'acl.php';

/**
 * API controller class for support tickets
 */
class Commentsv1_0 extends ApiController
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
		$this->acl->setUser($userid);

		parent::execute();
	}

	/**
	 * Display comments for a ticket
	 *
	 * @apiMethod GET
	 * @apiUri    /support/{ticket}/comments/list
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "limitstart",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "search",
	 * 		"description":   "A word or phrase to search for.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "sort",
	 * 		"description":   "Field to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "created",
	 * 		"allowedValues": "created, id, state"
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

		$obj = \Components\Support\Models\Comment::all();

		$filters = array(
			'limit'      => Request::getInt('limit', 25),
			'start'      => Request::getInt('limitstart', 0),
			'search'     => Request::getString('search', ''),
			'sort'       => Request::getWord('sort', 'created'),
			'sortdir'    => strtoupper(Request::getWord('sort_Dir', 'DESC')),
			'group'      => Request::getString('group', ''),
			'reportedby' => Request::getString('reporter', ''),
			'owner'      => Request::getString('owner', ''),
			'type'       => Request::getInt('type', 0),
			'status'     => strtolower(Request::getWord('status', '')),
			'tag'        => Request::getWord('tag', ''),
		);

		$filters['opened'] = $this->_toTimestamp(Request::getString('opened', ''));
		$filters['closed'] = $this->_toTimestamp(Request::getString('closed', ''));

		$response = new stdClass;
		$response->success  = true;
		$response->total    = 0;
		$response->comments = array();

		// Get a list of all statuses
		$statuses = \Components\Support\Models\Status::all()->rows();

		// Get a count of tickets
		$response->total = $obj->rows()->count();

		if ($response->total)
		{
			$response->comments = $obj->rows();
		}

		$this->send($response);
	}

	/**
	 * Create a new comment
	 *
	 * @apiMethod POST
	 * @apiUri    /support/{ticket}/comments
	 * @apiParameter {
	 * 		"name":        "scope",
	 * 		"description": "Scope type (group, member, etc.)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "scope_id",
	 * 		"description": "Scope object ID",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "title",
	 * 		"description": "Entry title",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "alias",
	 * 		"description": "Entry alias",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
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

		$ticket_id = Request::getInt('ticket', 0, 'post');

		// Load the old ticket so we can compare for the changelog
		$old = \Components\Support\Models\Ticket::oneOrFail($ticket_id);
		$old->set('tags', $old->tags('string'));

		if (!$old->get('id'))
		{
			throw new Exception(500, Lang::txt('Ticket "%s" does not exist.', $ticket_id));
		}

		// Initiate class and bind posted items to database fields
		$ticket = \Components\Support\Models\Ticket::oneOrFail($ticket_id);
		$ticket->set('status', Request::getInt('status', $ticket->get('status'), 'post'));
		$ticket->set('open', Request::getInt('open', $ticket->get('open'), 'post'));
		$ticket->set('category', Request::getInt('category', $ticket->get('category'), 'post'));
		$ticket->set('severity', Request::getString('severity', $ticket->get('severity'), 'post'));
		$ticket->set('owner', Request::getInt('owner', $ticket->get('owner'), 'post'));
		$ticket->set('group', Request::getInt('group', $ticket->get('group'), 'post'));

		// If an existing ticket AND closed AND previously open
		if ($ticket_id && !$ticket->get('open') && $ticket->get('open') != $old->get('open'))
		{
			// Record the closing time
			$ticket->set('closed', Date::toSql());
		}

		// Any tags?
		if ($tags = trim(Request::getString('tags', '', 'post')))
		{
			$ticket->tag($tags, $user->get('uidNumber'));
			$ticket->set('tags', $ticket->tags('string'));
		}

		// Store new content
		if (!$ticket->store())
		{
			$this->errorMessage(500, $ticket->getError());
			return;
		}

		// Create a new comment
		$comment = new \Components\Support\Models\Comment();
		$comment->set('ticket', $ticket->get('id'));
		$comment->set('comment', nl2br(Request::getString('comment', '', 'post', 'none', 2)));
		if ($comment->get('comment'))
		{
			// If a comment was posted by the ticket submitter to a "waiting user response" ticket, change status.
			if ($ticket->isWaiting() && $user->get('username') == $ticket->get('login'))
			{
				$ticket->open();
			}
		}
		$comment->set('created', Date::toSql());
		$comment->set('created_by', $user->get('uidNumber'));
		$comment->set('access', Request::getInt('access', 0, 'post'));

		// Compare fields to find out what has changed for this ticket and build a changelog
		$comment->changelog()->diff($old, $ticket);

		$comment->changelog()->cced(Request::getString('cc', '', 'post'));

		// Store new content
		if (!$comment->store())
		{
			$this->errorMessage(500, $comment->getError());
			return;
		}

		if ($ticket->get('owner'))
		{
			$comment->addTo(array(
				'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_OWNER'),
				'name'  => $ticket->owner('name'),
				'email' => $ticket->owner('email'),
				'id'    => $ticket->owner('id')
			));
		}

		// Add any CCs to the e-mail list
		foreach ($comment->changelog()->get('cc') as $cc)
		{
			$comment->addTo($cc, Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC'));
		}

		// Check if the notify list has eny entries
		if (count($comment->to()))
		{
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

		// Were there any changes?
		if (count($comment->changelog()->get('notifications')) > 0
		 || count($comment->changelog()->get('cc')) > 0
		 || count($comment->changelog()->get('changes')) > 0)
		{
			// Save the data
			if (!$comment->store())
			{
				$this->errorMessage(500, $comment->getError());
				return;
			}
		}

		$msg = new stdClass;
		$msg->ticket   = $ticket->get('id');
		$msg->comment  = $comment->get('id');
		$msg->notified = $comment->changelog()->get('notifications');

		$this->setMessageType(Request::getString('format', 'json'));
		$this->send($msg, 200, 'OK');
	}

	/**
	 * Displays details for a ticket comment
	 *
	 * @apiMethod GET
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
	public function readTask()
	{
		$this->requiresAuthentication();

		if (!$this->acl->check('read', 'tickets'))
		{
			throw new Exception(Lang::txt('Not authorized'), 403);
		}

		// Initiate class and bind data to database fields
		$id = Request::getInt('comment', 0);

		// Initiate class and bind data to database fields
		$ticket = \Components\Support\Models\Comment::oneOrFail($id);

		$response = new stdClass;
		$response->id = $comment->get('id');
		$response->ticket = $comment->get('ticket');

		$response->owner = new stdClass;
		$response->owner->username = $ticket->owner('username');
		$response->owner->name     = $ticket->owner('name');
		$response->owner->id       = $ticket->owner('id');

		$response->content = $comment->content('raw');

		$response->url = str_replace('/api', '', rtrim(Request::base(), '/') . '/' . ltrim(Route::url('index.php?option=com_support&controller=tickets&task=tickets&id=' . $comment->get('ticket') . '#c' . $comment->get('id')), '/'));

		$response->private = ($comment->get('access') ? true : false);

		$this->send($response);
	}

	/**
	 * Update a ticket comment
	 *
	 * @apiMethod PUT
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
	public function updateTask()
	{
		$this->requiresAuthentication();

		if (!$this->acl->check('edit', 'comments'))
		{
			throw new Exception(Lang::txt('Not authorized'), 403);
		}

		$ticket_id  = Request::getInt('ticket', 0);
		$comment_id = Request::getInt('comment', 0);

		$ticket = \Components\Support\Models\Ticket::oneOrFail($ticket_id);

		$comment = \Components\Support\Models\Comment::oneOrFail($comment_id);

		$this->send(null, 204);
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

		$ticket_id  = Request::getInt('ticket', 0);
		$comment_id = Request::getInt('comment', 0);

		$ticket = \Components\Support\Models\Ticket::oneOrFail($ticket_id);

		if (!$ticket->get('id'))
		{
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_MISSING_RECORD'), 404);
		}

		$comment = \Components\Support\Models\Comment::oneOrFail($comment_id);

		if ($comment->isPrivate() && !$this->acl->check('delete', 'private_comments'))
		{
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_UNAUTHORIZED'), 403);
		}

		if (!$comment->destroy())
		{
			throw new Exception($comment->getError(), 500);
		}

		$this->send(null, 204);
	}
}
