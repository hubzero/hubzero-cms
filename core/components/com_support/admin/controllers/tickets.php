<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Admin\Controllers;

use Components\Support\Helpers\ACL;
use Components\Support\Helpers\Utilities;
use Components\Support\Models\Ticket;
use Components\Support\Models\Comment;
use Components\Support\Models\Tags;
use Components\Support\Models\Query;
use Components\Support\Models\QueryFolder;
use Components\Support\Models\Attachment;
use Components\Support\Models\Watching;
use Components\Support\Models\Message;
use Components\Support\Models\Category;
use Hubzero\Component\AdminController;
use Hubzero\Browser\Detector;
use Hubzero\Content\Server;
use Hubzero\Utility\Validate;
use Exception;
use Filesystem;
use Request;
use Config;
use Route;
use Event;
use Lang;
use User;
use App;

include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'ticket.php';

/**
 * Support controller class for tickets
 */
class Tickets extends AdminController
{
	/**
	 * Execute a task
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
	 * Displays a list of tickets
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$total = 0;
		$tickets = array();

		$filters = array(
			// Paging
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			// Query to filter by
			'show' => Request::getState(
				$this->_option . '.' . $this->_controller . '.show',
				'show',
				0,
				'int'
			),
			'search'  => '',
			'sort'    => 'id',
			'sortdir' => 'DESC'
		);

		// Get query list
		$folders = QueryFolder::all()
			->whereEquals('user_id', User::get('id'))
			->order('ordering', 'asc')
			->rows();

		// Does the user have any folders?
		if (!count($folders))
		{
			// Get all the default folders
			$folders = QueryFolder::cloneCore(User::get('id'));
		}

		foreach ($folders as $folder)
		{
			foreach ($folder->queries->sort('ordering') as $query)
			{
				if ($query->id != $filters['show'])
				{
					$filters['search'] = '';
				}

				$query->set('count', Ticket::countWithQuery($query, $filters));

				if ($query->id == $filters['show'])
				{
					// Search
					$filters['search'] = urldecode(Request::getState(
						$this->_option . '.' . $this->_controller . '.search',
						'search',
						''
					));
					// Incoming sort
					$filters['sort'] = trim(Request::getState(
						$this->_option . '.' . $this->_controller . '.sort',
						'filter_order',
						$query->get('sort')
					));
					$filters['sortdir'] = trim(Request::getState(
						$this->_option . '.' . $this->_controller . '.sortdir',
						'filter_order_Dir',
						$query->get('sort_dir')
					));

					// Get the records
					$tickets = Ticket::allWithQuery($query, $filters);
				}
			}
		}

		if (!$filters['show'])
		{
			// Jump back to the beginning of the folders list
			// and try to find the first query available
			// to make it the current "active" query
			foreach ($folders as $folder)
			{
				if (count($folder->queries) > 0)
				{
					$query = $folder->queries->first();
					$filters['show'] = $query->get('id');
					break;
				}
				else
				{	// for no custom queries.
					$query = Query::blank();
					$query->set('count', 0);
					$query->set('sort', 'created');
					$query->set('sort_dir', 'desc');
				}
			}

			// Search
			$filters['search'] = urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			));
			// Set the total for the pagination
			$total = ($filters['search']) ? Ticket::countWithQuery($query, $filters) : $query->get('count');

			// Incoming sort
			$filters['sort']   = trim(Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				$query->get('sort')
			));
			$filters['sortdir'] = trim(Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				$query->get('sort_dir')
			));
			// Get the records
			$tickets = Ticket::allWithQuery($query, $filters);
		}

		$watching = Watching::all()
			->whereEquals('user_id', User::get('id'))
			->rows()
			->fieldsByKey('ticket_id');

		$watch = array(
			'open'   => Ticket::all()->whereEquals('open', 1)->whereIn('id', $watching)->total(),
			'closed' => Ticket::all()->whereEquals('open', 0)->whereIn('id', $watching)->total()
		);

		if ($filters['show'] < 0)
		{
			if (!isset($filters['sort']) || !$filters['sort'])
			{
				$filters['sort'] = trim(Request::getState(
					$this->_option . '.' . $this->_controller . '.sort',
					'filter_order',
					'created'
				));
			}
			if (!isset($filters['sortdir']) || !$filters['sortdir'])
			{
				$filters['sortdir'] = trim(Request::getState(
					$this->_option . '.' . $this->_controller . '.sortdir',
					'filter_order_Dir',
					'DESC'
				));
			}

			$tickets = Ticket::all()
				->whereEquals('open', ($filters['show'] == -1 ? 1 : 0))
				->whereIn('id', $watching)
				->order('created', 'desc')
				->rows();
		}

		// Output the HTML
		$this->view
			->set('watch', $watch)
			->set('filters', $filters)
			->set('folders', $folders)
			->set('total', $total)
			->set('rows', $tickets)
			->display();
	}

	/**
	 * Displays a ticket and comments
	 *
	 * @param   object  $comment
	 * @return  void
	 */
	public function editTask($comment = null)
	{
		Request::setVar('hidemainmenu', 1);

		$layout = 'edit';

		// Incoming
		$id = Request::getInt('id', 0);

		// Initiate database class and load info
		$ticket = Ticket::oneOrNew($id);

		// Editing or creating a ticket?
		if ($ticket->isNew())
		{
			$layout = 'add';

			// Creating a new ticket
			$ticket->set('severity', 'normal');
			$ticket->set('status', 0);
			$ticket->set('created', Date::toSql());
			$ticket->set('login', User::get('username'));
			$ticket->set('name', User::get('name'));
			$ticket->set('email', User::get('email'));
			$ticket->set('cookies', 1);

			$browser = new \Hubzero\Browser\Detector();

			$ticket->set('os', $browser->platform() . ' ' . $browser->platformVersion());
			$ticket->set('browser', $browser->name() . ' ' . $browser->version());

			$ticket->set('uas', Request::getString('HTTP_USER_AGENT', '', 'server'));

			$ticket->set('ip', Request::ip());
			$ticket->set('hostname', gethostbyaddr(Request::getString('REMOTE_ADDR', '', 'server')));
			$ticket->set('section', 1);
		}

		$filters = Utilities::getFilters();
		$lists = array();

		// Get messages
		$lists['messages'] = Message::all()->rows();

		// Get categories
		$lists['categories'] = Category::all()->rows();

		// Get severities
		$lists['severities'] = Utilities::getSeverities($this->config->get('severities'));

		if (trim($ticket->get('group_id')))
		{
			$lists['owner'] = $this->_userSelectGroup('ticket[owner]', $ticket->get('owner'), 1, '', trim($ticket->get('group_id')));
		}
		elseif (trim($this->config->get('group','')))
		{
			$lists['owner'] = $this->_userSelectGroup('ticket[owner]', $ticket->get('owner'), 1, '', trim($this->config->get('group','')));
		}
		else
		{
			$lists['owner'] = $this->_userSelect('ticket[owner]', $ticket->get('owner'), 1);
		}

		if ($watch = Request::getWord('watch', ''))
		{
			$watch = strtolower($watch);

			// Already watching
			if ($ticket->isWatching(User::get('id')))
			{
				// Stop watching?
				if ($watch == 'stop')
				{
					$ticket->stopWatching(User::get('id'));
				}
			}
			// Not already watching
			else
			{
				// Start watching?
				if ($watch == 'start')
				{
					if (!$ticket->watch(User::get('id')))
					{
						$this->setError(Lang::txt('COM_SUPPORT_ERROR_FAILED_TO_WATCH'));
					}
				}
			}
		}

		if (!$comment)
		{
			$comment = Comment::blank();
		}

		// Output the HTML
		$this->view
			->set('row', $ticket)
			->set('filters', $filters)
			->set('config', $this->config)
			->set('comment', $comment)
			->set('lists', $lists)
			->setLayout($layout)
			->display();
	}

	/**
	 * Saves changes to a ticket, adds a new comment/changelog,
	 * notifies any relevant parties
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$isNew = true;
		$id = Request::getInt('id', 0);
		if ($id)
		{
			$isNew = false;
		}

		// Load the old ticket so we can compare for the changelog
		$old = Ticket::oneOrNew($id);
		$old->set('tags', $old->tags('string'));

		// Initiate class and bind posted items to database fields
		$data = Request::getArray('ticket', array(), 'post');
		$ticket = Ticket::oneOrNew($id)->set($data);

		if ($ticket->get('target_date') && $ticket->get('target_date') != '0000-00-00 00:00:00')
		{
			$ticket->set('target_date', Date::of($ticket->get('target_date'), Config::get('offset'))->toSql());
		}

		$text = Request::getString('comment', '', 'post');
		$comment = Comment::blank();
		$comment->set('ticket', $id);

		// Check if changes were made wthin the time the comment was started and posted
		if ($id)
		{
			$started = Request::getString('started', Date::toSql(), 'post');

			$lastcomment = $ticket->comments()
				->order('created', 'DESC')
				->limit(1)
				->start(0)
				->row();

			if ($lastcomment && $lastcomment->get('created') >= $started)
			{
				$comment->set('comment', $text);

				Notify::error(Lang::txt('Changes were made to this ticket in the time since you began commenting/making changes. Please review your changes before submitting.' . $lastcomment->get('created'). ' > ' . $started));
				return $this->editTask($comment);
			}
		}

		if ($id && $ticket->get('status') == 0)
		{
			$ticket->set('open', 0);
			$ticket->set('resolved', Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPT_CLOSED'));
		}

		$ticket->set('open', $ticket->status->get('open', 1));

		if ($id && isset($data['status']) && $data['status'] == 0)
		{
			$ticket->set('open', 0);
			$ticket->set('resolved', Lang::txt('COM_SUPPORT_COMMENT_OPT_CLOSED'));
		}

		// If an existing ticket AND closed AND previously open
		if ($id && !$ticket->get('open') && $ticket->get('open') != $old->get('open'))
		{
			// Record the closing time
			$ticket->set('closed', Date::toSql());
		}

		// Store new content
		if (!$ticket->save())
		{
			throw new Exception($ticket->getError(), 500);
		}

		// Save the tags
		$ticket->tag(Request::getString('tags', '', 'post'), User::get('id'));
		$ticket->set('tags', $ticket->tags('string'));

		$base = Request::base();
		if (substr($base, -14) == 'administrator/')
		{
			$base = substr($base, 0, strlen($base)-14);
		}

		$webpath = trim($this->config->get('webpath',''), '/');

		$allowEmailResponses = $this->config->get('email_processing');

		$this->config->set('email_terse', Request::getInt('email_terse', 0));

		if ($this->config->get('email_terse'))
		{
			$allowEmailResponses = false;
		}
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

		// If a new ticket...
		if ($isNew)
		{
			// Get any set emails that should be notified of ticket submission
			$defs = explode(',', $this->config->get('emails', '{config.mailfrom}'));

			if ($defs)
			{
				// Get some email settings
				$msg = new \Hubzero\Mail\Message();
				$msg->setSubject(Config::get('sitename') . ' ' . Lang::txt('COM_SUPPORT') . ', ' . Lang::txt('COM_SUPPORT_TICKET_NUMBER', $ticket->get('id')));
				$msg->addFrom(
					Config::get('mailfrom'),
					Config::get('sitename') . ' ' . Lang::txt(strtoupper($this->_option))
				);

				// Plain text email
				$eview = new \Hubzero\Mail\View(array(
					'base_path' => dirname(dirname(__DIR__)) . DS . 'site',
					'name'      => 'emails',
					'layout'    => 'ticket_plain'
				));
				$eview->option     = $this->_option;
				$eview->controller = $this->_controller;
				$eview->ticket     = $ticket;
				$eview->config     = $this->config;
				$eview->delimiter  = '';

				$plain = $eview->loadTemplate(false);
				$plain = str_replace("\n", "\r\n", $plain);

				$msg->addPart($plain, 'text/plain');

				// HTML email
				$eview->setLayout('ticket_html');

				$html = $eview->loadTemplate();
				$html = str_replace("\n", "\r\n", $html);

				if (!$this->config->get('email_terse'))
				{
					foreach ($ticket->attachments as $attachment)
					{
						if ($attachment->size() < 2097152)
						{
							if ($attachment->isImage())
							{
								$file = basename($attachment->path());
								$html = preg_replace('/<a class="img" data\-filename="' . str_replace('.', '\.', $file) . '" href="(.*?)"\>(.*?)<\/a>/i', '<img src="' . $message->getEmbed($attachment->path()) . '" alt="" />', $html);
							}
							else
							{
								$message->addAttachment($attachment->path());
							}
						}
					}
				}

				$msg->addPart($html, 'text/html');

				// Loop through the addresses
				foreach ($defs as $def)
				{
					$def = trim($def);

					// Check if the address should come from site config
					if ($def == '{config.mailfrom}')
					{
						$def = Config::get('mailfrom');
					}
					// Check for a valid address
					if (Validate::email($def))
					{
						// Send e-mail
						$msg->setTo(array($def));
						$msg->send();
					}
				}
			}
		}

		// Incoming comment
		if ($text)
		{
			// If a comment was posted always change status to open.  This is the typical expected behavior. 
			// Preventing new comments from re-opening a ticket can result in comments that are never responded to.
			if ((!$ticket->isOpen()) && $ticket->get('open') == $old->get('open') || $ticket->isWaiting() && User::get('username') == $ticket->get('login'))
			{
				$ticket->open();
			}
		}

		// Create a new support comment object and populate it
		$access = Request::getInt('access', 0);

		//$comment = new Comment();
		$comment->set('ticket', $ticket->get('id'));
		$comment->set('comment', $text);
		$comment->set('created', Date::toSql());
		$comment->set('created_by', User::get('id'));
		$comment->set('access', $access);

		// Compare fields to find out what has changed for this ticket and build a changelog
		$comment->changelog()->diff($old, $ticket);

		$comment->changelog()->cced(Request::getString('cc', ''));

		// Save the data
		if (!$comment->save())
		{
			throw new Exception($comment->getError(), 500);
		}

		Event::trigger('support.onTicketUpdate', array($ticket, $comment));

		if ($tmp = Request::getInt('tmp_dir'))
		{
			$attachments = Attachment::all()
				->whereEquals('comment_id', $tmp)
				->rows();

			foreach ($attachments as $attach)
			{
				$attach->set('comment_id', $comment->get('id'));
				$attach->save();
			}
		}

		if (!$isNew)
		{
			$attachment = $this->uploadTask($ticket->get('id'), $comment->get('id'));
		}

		// Only do the following if a comment was posted or ticket was reassigned
		// otherwise, we're only recording a changelog
		if ($comment->get('comment')
		 || $ticket->get('owner') != $old->get('owner')
		 || $ticket->get('group_id') != $old->get('group_id')
		 || $comment->attachments->count() > 0)
		{
			// Send e-mail to ticket submitter?
			if (Request::getInt('email_submitter', 0) == 1)
			{
				// Is the comment private? If so, we do NOT send e-mail to the
				// submitter regardless of the above setting
				if (!$comment->isPrivate())
				{
					$comment->addTo(array(
						'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_SUBMITTER'),
						'name'  => $ticket->submitter->get('name', $ticket->get('name')),
						'email' => $ticket->submitter->get('email', $ticket->get('email')),
						'id'    => $ticket->submitter->get('id')
					));
				}
			}

			// Send e-mail to ticket owner?
			if (Request::getInt('email_owner', 0) == 1)
			{
				if ($old->get('owner') && $ticket->get('owner') != $old->get('owner'))
				{
					$comment->addTo(array(
						'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_PRIOR_OWNER'),
						'name'  => $old->assignee->get('name'),
						'email' => $old->assignee->get('email'),
						'id'    => $old->assignee->get('id')
					));
				}
				if ($ticket->get('owner'))
				{
					$comment->addTo(array(
						'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_OWNER'),
						'name'  => $ticket->assignee->get('name'),
						'email' => $ticket->assignee->get('email'),
						'id'    => $ticket->assignee->get('id')
					));
				}
				elseif ($ticket->get('group_id'))
				{
					$group = \Hubzero\User\Group::getInstance($ticket->get('group_id'));

					if ($group)
					{
						foreach ($group->get('managers') as $manager)
						{
							$manager = User::getInstance($manager);

							if (!$manager || !$manager->get('id'))
							{
								continue;
							}

							$comment->addTo(array(
								'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_GROUPMANAGER'),
								'name'  => $manager->get('name'),
								'email' => $manager->get('email'),
								'id'    => $manager->get('id')
							));
						}
					}
				}
			}

			// Add any CCs to the e-mail list
			foreach ($comment->changelog()->get('cc') as $cc)
			{
				$comment->addTo($cc, Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC'));
			}

			// Message people watching this ticket,
			// but ONLY if the comment was NOT marked private
			$this->acl = ACL::getACL();
			foreach ($ticket->watchers as $watcher)
			{
				$this->acl->setUser($watcher->user_id);
				if (!$comment->isPrivate() || ($comment->isPrivate() && $this->acl->check('read', 'private_comments')))
				{
					$comment->addTo($watcher->user_id, 'watcher');
				}
			}
			$this->acl->setUser(User::get('id'));

			if (count($comment->to()))
			{
				// Build e-mail components
				$subject = Lang::txt('COM_SUPPORT_EMAIL_SUBJECT_TICKET_COMMENT', $ticket->get('id'));

				$from = array(
					'name'      => Lang::txt('COM_SUPPORT_EMAIL_FROM', Config::get('sitename')),
					'email'     => Config::get('mailfrom'),
					'multipart' => md5(date('U'))  // Html email
				);

				// Plain text email
				$eview = new \Hubzero\Mail\View(array(
					'base_path' => dirname(dirname(__DIR__)) . DS . 'site',
					'name'      => 'emails',
					'layout'    => 'comment_plain'
				));
				$eview->option     = $this->_option;
				$eview->controller = $this->_controller;
				$eview->comment    = $comment;
				$eview->ticket     = $ticket;
				$eview->config     = $this->config;
				$eview->delimiter  = ($allowEmailResponses ? '~!~!~!~!~!~!~!~!~!~!' : '');

				$message['plaintext'] = $eview->loadTemplate(false);
				$message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

				// HTML email
				$eview->setLayout('comment_html');

				$message['multipart'] = $eview->loadTemplate();
				$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

				$message['attachments'] = array();
				if (!$this->config->get('email_terse'))
				{
					foreach ($comment->attachments as $attachment)
					{
						if ($attachment->size() < 2097152)
						{
							$message['attachments'][] = $attachment->path();
						}
					}
				}

				// Send e-mail to admin?
				foreach ($comment->to('ids') as $to)
				{
					if ($allowEmailResponses)
					{
						// The reply-to address contains the token
						$token = $encryptor->buildEmailToken(1, 1, $to['id'], $id);
						$from['replytoemail'] = 'htc-' . $token . strstr(Config::get('mailfrom'), '@');
					}

					// Get the user's email address
					if (!Event::trigger('xmessage.onSendMessage', array('support_reply_submitted', $subject, $message, $from, array($to['id']), $this->_option)))
					{
						$this->setError(Lang::txt('COM_SUPPORT_ERROR_FAILED_TO_MESSAGE', $to['name'] . '(' . $to['role'] . ')'));
					}

					// Watching should be anonymous
					if ($to['role'] == 'watcher')
					{
						continue;
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
						$token = $encryptor->buildEmailToken(1, 1, -9999, $id);

						$email = array(
							$to['email'],
							'htc-' . $token . strstr(Config::get('mailfrom'), '@')
						);

						// In this case each item in email in an array, 1- To, 2:reply to address
						Utilities::sendEmail($email[0], $subject, $message, $from, $email[1]);
					}
					else
					{
						// Email is just a plain 'ol string
						Utilities::sendEmail($to['email'], $subject, $message, $from);
					}

					// Watching should be anonymous
					if ($to['role'] == 'watcher')
					{
						continue;
					}
					$comment->changelog()->notified(
						$to['role'],
						$to['name'],
						$to['email']
					);
				}
			}
			else
			{
				// Force entry to private if no comment or attachment was made
				if (!$comment->get('comment') && $comment->attachments->count() <= 0)
				{
					$comment->set('access', 1);
				}
			}

			// Were there any changes?
			if (count($comment->changelog()->get('notifications')) > 0 || $access != $comment->get('access'))
			{
				// Save the data
				if (!$comment->save())
				{
					throw new Exception($comment->getError(), 500);
				}
			}
		}

		// output messsage and redirect
		if ($this->getTask() != 'apply')
		{
			$filters = Request::getString('filters', '');
			$filters = str_replace('&amp;', '&', $filters);

			// Redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($filters ? '&' . $filters : ''), false),
				Lang::txt('COM_SUPPORT_TICKET_SUCCESSFULLY_SAVED', $ticket->get('id'))
			);
			return;
		}

		$this->editTask();
	}

	/**
	 * Display a form for processing tickets in a batch
	 *
	 * @return  void
	 */
	public function batchTask()
	{
		Request::setVar('hidemainmenu', 1);

		// Incoming
		$ids  = Request::getArray('id', array());
		$tmpl = Request::getString('tmpl', '');

		$filters = Utilities::getFilters();
		$lists = array();

		// Get categories
		$lists['categories'] = Category::all()->rows();

		// Get severities
		$lists['severities'] = Utilities::getSeverities($this->config->get('severities'));

		$lists['owner'] = $this->_userSelect('owner', '', 1);

		// Output the HTML
		$this->view
			->set('ids', $ids)
			->set('tmpl', $tmpl)
			->set('lists', $lists)
			->set('filters', $filters)
			->display();
	}

	/**
	 * Process a batch change
	 *
	 * @return  void
	 */
	public function processTask()
	{
		// Incoming
		$tmpl    = Request::getString('tmpl');

		$ids     = Request::getArray('id', array());
		$fields  = Request::getArray('fields', array());
		$tags    = Request::getString('tags', '');
		$access  = 1;

		$fields['owner'] = Request::getString('owner', '');
		/*$text = nl2br(Request::getString('comment', '', 'post', 'none', 2));
		$cc      = Request::getString('cc', '');
		$access  = Request::getInt('access', 0);
		$email_submitter = Request::getInt('email_submitter', 0);
		$email_owner = Request::getInt('email_owner', 0);

		$base = Request::base();
		if (substr($base, -14) == 'administrator/')
		{
			$base = substr($base, 0, strlen($base)-14);
		}

		$webpath = trim($this->config->get('webpath',''), '/');

		$allowEmailResponses = $this->config->get('email_processing');
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
		}*/

		// Only take the fields that have had a value set
		foreach ($fields as $key => $value)
		{
			if ($value === '')
			{
				unset($fields[$key]);
			}
		}

		$processed = array();

		foreach ($ids as $id)
		{
			if (!$id)
			{
				continue;
			}

			// Initiate class and bind posted items to database fields
			$ticket = Ticket::oneOrFail($id);

			$old = Ticket::oneOrFail($id);
			$old->set('tags', $old->tags('string'));

			$ticket->set($fields);

			// Store new content
			if (!$ticket->save())
			{
				$this->setError($ticket->getError());
			}

			// Only set the tags if any tags have been provided
			if ($tags)
			{
				$ticket->set('tags', $tags);
				$ticket->tag($ticket->get('tags'), User::get('id'));
			}
			else
			{
				$ticket->set('tags', $ticket->tags('string'));
			}

			// Create a new support comment object and populate it
			$comment = Comment::blank();
			$comment->set('ticket', $id);
			$comment->set('comment', $text);
			$comment->set('created', Date::toSql());
			$comment->set('created_by', User::get('id'));
			//$comment->set('access', $access);

			// Compare fields to find out what has changed for this ticket and build a changelog
			$comment->changelog()->diff($old, $ticket);
			$comment->changelog()->cced($cc);

			// Save the data
			if (!$comment->save())
			{
				$this->setError($comment->getError());
				continue;
			}

			// Only do the following if a comment was posted or ticket was reassigned
			// otherwise, we're only recording a changelog
			/*if ($comment->get('comment') || $ticket->get('owner') != $old->get('owner'))
			{
				// Send e-mail to ticket submitter?
				if ($email_submitter == 1 && !$comment->isPrivate())
				{
					$comment->addTo(array(
						'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_SUBMITTER'),
						'name'  => $ticket->submitter->get('name', $ticket->get('name')),
						'email' => $ticket->submitter->get('email', $ticket->get('email')),
						'id'    => $ticket->submitter->get('id')
					));
				}

				// Send e-mail to ticket owner?
				if ($email_owner == 1 && $ticket->get('owner'))
				{
					$comment->addTo(array(
						'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_OWNER'),
						'name'  => $ticket->assignee->get('name'),
						'email' => $ticket->assignee->get('email'),
						'id'    => $ticket->assignee->get('id')
					));
				}

				// Add any CCs to the e-mail list
				foreach ($comment->changelog()->get('cc') as $cc)
				{
					$comment->addTo($cc, Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC'));
				}

				// Message people watching this ticket,
				// but ONLY if the comment was NOT marked private
				if (!$comment->isPrivate())
				{
					foreach ($ticket->watchers as $watcher)
					{
						$comment->addTo($watcher->user_id, 'watcher');
					}
				}

				if (count($comment->to()))
				{
					// Build e-mail components
					$subject = Lang::txt('COM_SUPPORT_EMAIL_SUBJECT_TICKET_COMMENT', $ticket->get('id'));

					$from = array(
						'name'      => Lang::txt('COM_SUPPORT_EMAIL_FROM', Config::get('sitename')),
						'email'     => Config::get('mailfrom'),
						'multipart' => md5(date('U'))  // Html email
					);

					// Plain text email
					$eview = new \Hubzero\Component\View(array(
						'base_path' => dirname(dirname(__DIR__)) . DS . 'site',
						'name'      => 'emails',
						'layout'    => 'comment_plain'
					));
					$eview->option     = $this->_option;
					$eview->controller = $this->_controller;
					$eview->comment    = $comment;
					$eview->ticket     = $ticket;
					$eview->delimiter  = ($allowEmailResponses ? '~!~!~!~!~!~!~!~!~!~!' : '');

					$message['plaintext'] = $eview->loadTemplate();
					$message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

					// HTML email
					$eview->setLayout('comment_html');

					$message['multipart'] = $eview->loadTemplate();
					$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

					// Send e-mail to admin?
					Plugin::import('xmessage');

					foreach ($comment->to('ids') as $to)
					{
						if ($allowEmailResponses)
						{
							// The reply-to address contains the token
							$token = $encryptor->buildEmailToken(1, 1, $to['id'], $id);
							$from['replytoemail'] = 'htc-' . $token . strstr(Config::get('mailfrom'), '@');
						}

						// Get the user's email address
						if (!Event::trigger('xmessage.onSendMessage', array('support_reply_submitted', $subject, $message, $from, array($to['id']), $this->_option)))
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
							$token = $encryptor->buildEmailToken(1, 1, -9999, $id);

							$email = array(
								$to['email'],
								'htc-' . $token . strstr(Config::get('mailfrom'), '@')
							);

							// In this case each item in email in an array, 1- To, 2:reply to address
							Utilities::sendEmail($email[0], $subject, $message, $from, $email[1]);
						}
						else
						{
							// email is just a plain 'ol string
							Utilities::sendEmail($to['email'], $subject, $message, $from);
						}

						$comment->changelog()->notified(
							$to['role'],
							$to['name'],
							$to['email']
						);
					}

					// Were there any changes?
					if (count($comment->changelog()->get('notifications')) > 0)
					{
						// Save the data
						if (!$comment->store())
						{
							$this->setError($comment->getError());
						}
					}
				}
			}*/

			$processed[] = $id;
		}

		if ($tmpl)
		{
			echo Lang::txt('COM_SUPPORT_TICKETS_SUCCESSFULLY_SAVED', count($processed));
			return;
		}

		// Output messsage and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . (Request::getInt('no_html', 0) ? '&no_html=1' : ''), false),
			Lang::txt('COM_SUPPORT_TICKETS_SUCCESSFULLY_SAVED', count($ids))
		);
	}

	/**
	 * Removes a ticket and all associated records (tags, comments, etc.)
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getArray('id', array());

		// Check for an ID
		if (count($ids) < 1)
		{
			Notify::warning(Lang::txt('COM_SUPPORT_ERROR_SELECT_TICKET_TO_DELETE'));
			return $this->cancelTask();
		}

		$removed = 0;

		foreach ($ids as $id)
		{
			$id = intval($id);

			// Delete ticket
			$ticket = Ticket::oneOrFail($id);

			if (!$ticket->destroy())
			{
				Notify::error($ticket->getError());
				continue;
			}

			$removed++;
		}

		if ($removed)
		{
			Notify::success(Lang::txt('COM_SUPPORT_TICKET_SUCCESSFULLY_DELETED', $removed));
		}

		// Output messsage and redirect
		$this->cancelTask();
	}

	/**
	 * Generates a select list of Super Administrator names
	 *
	 * @param   string   $name        Select element 'name' attribute
	 * @param   string   $active      Selected option
	 * @param   integer  $nouser      Flag to set first option to 'No user'
	 * @param   string   $javascript  Any inline JS to attach to the element
	 * @param   string   $order       The sort order for items in the list
	 * @return  string   HTML select list
	 */
	private function _userSelect($name, $active, $nouser=0, $javascript=null, $order='a.name')
	{
		$database = App::get('db');

		$query = "SELECT a.id AS value, a.name AS text"
			. " FROM #__users AS a"
			. " INNER JOIN #__support_acl_aros AS aro ON aro.model='user' AND aro.foreign_key = a.id"
			. " WHERE a.block = '0'"
			. " ORDER BY ". $order;

		$database->setQuery($query);
		if ($nouser)
		{
			$users[] = \Html::select('option', '0', Lang::txt('COM_SUPPORT_NO_USER'), 'value', 'text');
			$users = array_merge($users, $database->loadObjectList());
		}
		else
		{
			$users = $database->loadObjectList();
		}

		$query = "SELECT a.id AS value, a.name AS text, aro.alias"
			. " FROM #__users AS a"
			. " INNER JOIN #__xgroups_members AS m ON m.uidNumber = a.id"
			. " INNER JOIN #__support_acl_aros AS aro ON aro.model='group' AND aro.foreign_key = m.gidNumber"
			. " WHERE a.block = '0'"
			. " ORDER BY ". $order;
		$database->setQuery($query);
		if ($results = $database->loadObjectList())
		{
			$groups = array();
			foreach ($results as $result)
			{
				if (!isset($groups[$result->alias]))
				{
					$groups[$result->alias] = array();
				}
				$groups[$result->alias][] = $result;
			}
			foreach ($groups as $nme => $gusers)
			{
				$users[] = \Html::select('optgroup', Lang::txt('COM_SUPPORT_GROUP') . ' ' . $nme);
				$users = array_merge($users, $gusers);
				$users[] = \Html::select('optgroup', Lang::txt('COM_SUPPORT_GROUP') . ' ' . $nme);
			}
		}

		$users = \Html::select('genericlist', $users, $name, ' '. $javascript, 'value', 'text', $active, false, false);

		return $users;
	}

	/**
	 * Generates a select list of names based off group membership
	 *
	 * @param   string   $name        Select element 'name' attribute
	 * @param   string   $active      Selected option
	 * @param   integer  $nouser      Flag to set first option to 'No user'
	 * @param   string   $javascript  Any inline JS to attach to the element
	 * @param   string   $group       The group to pull member names from
	 * @return  string   HTML select list
	 */
	private function _userSelectGroup($name, $active, $nouser=0, $javascript=null, $group='')
	{
		$users = array();
		if ($nouser)
		{
			$users[] = \Html::select('option', '0', Lang::txt('COM_SUPPORT_NO_USER'), 'value', 'text');
		}

		if (strstr($group, ','))
		{
			$groups = explode(',', $group);
			if (is_array($groups))
			{
				foreach ($groups as $g)
				{
					$hzg = \Hubzero\User\Group::getInstance(trim($g));

					if ($hzg->get('gidNumber'))
					{
						$members = $hzg->get('members');

						//$users[] = '<optgroup title="'.stripslashes($hzg->description).'">';
						$users[] = \Html::select('optgroup', stripslashes($hzg->description));
						foreach ($members as $member)
						{
							$u = User::getInstance($member);
							if (!is_object($u))
							{
								continue;
							}

							$m = new \stdClass();
							$m->value = $u->get('id');
							$m->text  = $u->get('name');
							$m->groupname = $g;

							$users[] = $m;
						}
						//$users[] = '</optgroup>';
						$users[] = \Html::select('option', '</OPTGROUP>');
					}
				}
			}
		}
		else
		{
			$hzg = \Hubzero\User\Group::getInstance($group);

			if ($hzg && $hzg->get('gidNumber'))
			{
				$members = $hzg->get('members');

				foreach ($members as $member)
				{
					$u = User::getInstance($member);
					if (!is_object($u))
					{
						continue;
					}

					$m = new \stdClass();
					$m->value = $u->get('id');
					$m->text  = $u->get('name');
					$m->groupname = $group;

					$names = explode(' ', $u->get('name'));
					$last = trim(end($names));

					$users[$last] = $m;
				}
			}

			ksort($users);
		}

		$users = \Html::select('genericlist', $users, $name, ' ' . $javascript, 'value', 'text', $active, false, false);

		return $users;
	}

	/**
	 * Serves up files only after passing access checks
	 *
	 * @return  void
	 */
	public function downloadTask()
	{
		// Get the ID of the file requested
		$id = Request::getInt('id', 0);

		// Instantiate an attachment object
		$attach = Attachment::oneOrFail($id);

		if (!$attach->get('filename'))
		{
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_FILE_NOT_FOUND'), 404);
		}

		$filename = $attach->path();

		// Ensure the file exist
		if (!file_exists($filename))
		{
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_FILE_NOT_FOUND') . ' ' . $filename, 404);
		}

		// Initiate a new content server and serve up the file
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($filename);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve())
		{
			// Should only get here on error
			throw new Exception(Lang::txt('COM_SUPPORT_SERVER_ERROR'), 404);
		}

		exit;
	}

	/**
	 * Uploads a file and generates a database entry for that item
	 *
	 * @param   string   $ticket_id   Sub-directory to upload files to
	 * @param   integer  $comment_id
	 * @return  string   Key to use in comment bodies (parsed into links or img tags)
	 */
	public function uploadTask($ticket_id, $comment_id = 0)
	{
		// Incoming
		$description = Request::getString('description', '');

		if (!$ticket_id)
		{
			$this->setError(Lang::txt('COM_SUPPORT_ERROR_NO_ID'));
			return '';
		}

		// Incoming file
		$file = Request::getArray('upload', '', 'files');
		if (!is_array($file) || !isset($file['name']) || !$file['name'])
		{
			$this->setError(Lang::txt('COM_SUPPORT_ERROR_NO_FILE'));
			return '';
		}

		$attachment = Attachment::blank();

		// Construct our file path
		$file_path = $attachment->rootPath() . DS . $ticket_id;

		if (!is_dir($file_path))
		{
			if (!Filesystem::makeDirectory($file_path))
			{
				$this->setError(Lang::txt('COM_SUPPORT_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return '';
			}
		}

		$mediaConfig = Component::params('com_media');

		$sizeLimit = $this->config->get('maxAllowed');
		if (!$sizeLimit)
		{
			// Size limit is in MB, so we need to turn it into just B
			$sizeLimit = $mediaConfig->get('upload_maxsize');
			$sizeLimit = $sizeLimit * 1024 * 1024;
		}

		if ($file['size'] > $sizeLimit)
		{
			$this->setError(Lang::txt('File is too large. Max file upload size is %s', Number::formatBytes($sizeLimit)));
			return '';
		}

		// Make the filename safe
		$file['name'] = Filesystem::clean($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		$ext = strtolower(Filesystem::extension($file['name']));

		$filename = Filesystem::name($file['name']);
		while (file_exists($file_path . DS . $filename . '.' . $ext))
		{
			$filename .= rand(10, 99);
		}

		$finalfile = $file_path . DS . $filename . '.' . $ext;

		$exts = $this->config->get('file_ext');
		$exts = $exts ?: $mediaConfig->get('upload_extensions');
		$allowed = array_values(array_filter(explode(',', $exts)));

		// Make sure that file is acceptable type
		if (!in_array($ext, $allowed))
		{
			$this->setError(Lang::txt('COM_SUPPORT_ERROR_INCORRECT_FILE_TYPE'));
			return '';
		}

		// Perform the upload
		if (!Filesystem::upload($file['tmp_name'], $finalfile))
		{
			$this->setError(Lang::txt('COM_SUPPORT_ERROR_UPLOADING'));
			return '';
		}
		else
		{
			// Scan for viruses
			if (!Filesystem::isSafe($finalfile))
			{
				if (Filesystem::delete($finalfile))
				{
					$this->setError(Lang::txt('COM_SUPPORT_ERROR_FAILED_SECURITY_SCAN'));
					return '';
				}
			}

			// File was uploaded
			// Create database entry
			$description = htmlspecialchars($description);

			$attachment->set(array(
				'id'          => 0,
				'ticket'      => $ticket_id,
				'comment_id'  => $comment_id,
				'filename'    => $filename . '.' . $ext,
				'description' => $description
			));
			if (!$attachment->save())
			{
				$this->setError($attachment->getError());
			}

			return '{attachment#' . $attachment->get('id') . '}';
		}
	}
}
