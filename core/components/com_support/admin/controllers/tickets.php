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

namespace Components\Support\Admin\Controllers;

use Components\Support\Helpers\ACL;
use Components\Support\Helpers\Utilities;
use Components\Support\Models\Ticket;
use Components\Support\Models\Comment;
use Components\Support\Models\Tags;
use Components\Support\Tables;
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

include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'query.php');
include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'queryfolder.php');
include_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'ticket.php');

/**
 * Support controller class for tickets
 */
class Tickets extends AdminController
{
	/**
	 * Displays a list of tickets
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		$obj = new Tables\Ticket($this->database);

		// Get filters
		$this->view->total = 0;
		$this->view->rows = array();

		$this->view->filters = array(
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
		$sf = new Tables\QueryFolder($this->database);
		$this->view->folders = $sf->find('list', array(
			'user_id'  => User::get('id'),
			'sort'     => 'ordering',
			'sort_Dir' => 'asc'
		));

		// Does the user have any folders?
		if (!count($this->view->folders))
		{
			// Get all the default folders
			$this->view->folders = $sf->cloneCore(User::get('id'));
		}

		$sq = new Tables\Query($this->database);
		$queries = $sq->getRecords(array(
			'user_id'  => User::get('id'),
			'sort'     => 'ordering',
			'sort_Dir' => 'asc'
		));

		foreach ($queries as $query)
		{
			$filters = $this->view->filters;
			if ($query->id != $this->view->filters['show'])
			{
				$filters['search'] = '';
			}

			$query->query = $sq->getQuery($query->conditions);

			// Get a record count
			$query->count = $obj->getCount($query->query, $filters);

			foreach ($this->view->folders as $k => $v)
			{
				if (!isset($this->view->folders[$k]->queries))
				{
					$this->view->folders[$k]->queries = array();
				}
				if ($query->folder_id == $v->id)
				{
					$this->view->folders[$k]->queries[] = $query;
				}
			}

			if ($query->id == $this->view->filters['show'])
			{
				// Search
				$this->view->filters['search']       = urldecode(Request::getState(
					$this->_option . '.' . $this->_controller . '.search',
					'search',
					''
				));
				// Set the total for the pagination
				$this->view->total = ($this->view->filters['search']) ? $obj->getCount($query->query, $this->view->filters) : $query->count;

				// Incoming sort
				$this->view->filters['sort']         = trim(Request::getState(
					$this->_option . '.' . $this->_controller . '.sort',
					'filter_order',
					$query->sort
				));
				$this->view->filters['sortdir']     = trim(Request::getState(
					$this->_option . '.' . $this->_controller . '.sortdir',
					'filter_order_Dir',
					$query->sort_dir
				));
				// Get the records
				$this->view->rows  = $obj->getRecords($query->query, $this->view->filters);
			}
		}

		if (!$this->view->filters['show'])
		{
			// Jump back to the beginning of the folders list
			// and try to find the first query available
			// to make it the current "active" query
			reset($this->view->folders);
			foreach ($this->view->folders as $folder)
			{
				if (!empty($folder->queries))
				{
					$query = $folder->queries[0];
					$this->view->filters['show'] = $query->id;
					break;
				}
				else
				{	// for no custom queries.
					$query = new Tables\Query($this->database);
					$query->count = 0;
				}
			}
			//$folder = reset($this->view->folders);
			//$query = $folder->queries[0];
			// Search
			$this->view->filters['search'] = urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			));
			// Set the total for the pagination
			$this->view->total = ($this->view->filters['search']) ? $obj->getCount($query->query, $this->view->filters) : $query->count;

			// Incoming sort
			$this->view->filters['sort']   = trim(Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				$query->sort
			));
			$this->view->filters['sortdir'] = trim(Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				$query->sort_dir
			));
			// Get the records
			$this->view->rows = $obj->getRecords($query->query, $this->view->filters);

		}

		$watching = new Tables\Watching($this->database);
		$this->view->watch = array(
			'open' => $watching->count(array(
				'user_id' => User::get('id'),
				'open'    => 1
			)),
			'closed' => $watching->count(array(
				'user_id' => User::get('id'),
				'open'    => 0
			))
		);
		if ($this->view->filters['show'] < 0)
		{
			if (!isset($this->view->filters['sort']) || !$this->view->filters['sort'])
			{
				$this->view->filters['sort']         = trim(Request::getState(
					$this->_option . '.' . $this->_controller . '.sort',
					'filter_order',
					'created'
				));
			}
			if (!isset($this->view->filters['sortdir']) || !$this->view->filters['sortdir'])
			{
				$this->view->filters['sortdir']         = trim(Request::getState(
					$this->_option . '.' . $this->_controller . '.sortdir',
					'filter_order_Dir',
					'DESC'
				));
			}
			$records = $watching->find(array(
				'user_id' => User::get('id'),
				'open'    => ($this->view->filters['show'] == -1 ? 1 : 0)
			));
			if (count($records))
			{
				$ids = array();
				foreach ($records as $record)
				{
					$ids[] = $record->ticket_id;
				}
				$this->view->rows = $obj->getRecords("(f.id IN ('" . implode("','", $ids) . "'))", $this->view->filters);

			}
		}

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Show a form for creating a ticket
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Displays a ticket and comments
	 *
	 * @param   mixed  $comment
	 * @return  void
	 */
	public function editTask($comment = null)
	{
		Request::setVar('hidemainmenu', 1);

		$layout = 'edit';

		// Incoming
		$id = Request::getInt('id', 0);

		// Initiate database class and load info
		$row = Ticket::getInstance($id);

		// Editing or creating a ticket?
		if (!$row->exists())
		{
			$layout = 'add';

			// Creating a new ticket
			$row->set('severity', 'normal');
			$row->set('status', 0);
			$row->set('created', Date::toSql());
			$row->set('login', User::get('username'));
			$row->set('name', User::get('name'));
			$row->set('email', User::get('email'));
			$row->set('cookies', 1);

			$browser = new \Hubzero\Browser\Detector();

			$row->set('os', $browser->platform() . ' ' . $browser->platformVersion());
			$row->set('browser', $browser->name() . ' ' . $browser->version());

			$row->set('uas', Request::getVar('HTTP_USER_AGENT','','server'));

			$row->set('ip', Request::ip());
			$row->set('hostname', gethostbyaddr(Request::getVar('REMOTE_ADDR','','server')));
			$row->set('section', 1);
		}

		$this->view->filters = Utilities::getFilters();
		$this->view->lists = array();

		// Get messages
		$sm = new Tables\Message($this->database);
		$this->view->lists['messages'] = $sm->getMessages();

		// Get categories
		$sa = new Tables\Category($this->database);
		$this->view->lists['categories'] = $sa->find('list');

		// Get severities
		$this->view->lists['severities'] = Utilities::getSeverities($this->config->get('severities'));

		if (trim($row->get('group')))
		{
			$this->view->lists['owner'] = $this->_userSelectGroup('owner', $row->get('owner'), 1, '', trim($row->get('group')));
		}
		elseif (trim($this->config->get('group')))
		{
			$this->view->lists['owner'] = $this->_userSelectGroup('owner', $row->get('owner'), 1, '', trim($this->config->get('group')));
		}
		else
		{
			$this->view->lists['owner'] = $this->_userSelect('owner', $row->get('owner'), 1);
		}

		$this->view->row = $row;

		if ($watch = Request::getWord('watch', ''))
		{
			$watch = strtolower($watch);

			// Already watching
			if ($this->view->row->isWatching(User::getInstance()))
			{
				// Stop watching?
				if ($watch == 'stop')
				{
					$this->view->row->stopWatching(User::getInstance());
				}
			}
			// Not already watching
			else
			{
				// Start watching?
				if ($watch == 'start')
				{
					$this->view->row->watch(User::getInstance());
					if (!$this->view->row->isWatching(User::getInstance(), true))
					{
						$this->setError(Lang::txt('COM_SUPPORT_ERROR_FAILED_TO_WATCH'));
					}
				}
			}
		}

		if (!$comment)
		{
			$comment = new Comment();
		}
		$this->view->comment = $comment;

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view
			->set('config', $this->config)
			->setLayout($layout)
			->display();
	}

	/**
	 * Save an entry and return t the edit form
	 *
	 * @return     void
	 */
	public function applyTask()
	{
		$this->saveTask(0);
	}

	/**
	 * Saves changes to a ticket, adds a new comment/changelog,
	 * notifies any relevant parties
	 *
	 * @return void
	 */
	public function saveTask($redirect=1)
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
		$old = new Ticket($id);
		$old->set('tags', $old->tags('string'));

		// Initiate class and bind posted items to database fields
		$row = new Ticket($id);

		if (!$row->bind($_POST))
		{
			throw new Exception($row->getError(), 500);
		}

		if ($row->get('target_date') && $row->get('target_date') != '0000-00-00 00:00:00')
		{
			$row->set('target_date', Date::of($row->get('target_date'), Config::get('offset'))->toSql());
		}
		else
		{
			$row->set('target_date', '0000-00-00 00:00:00');
		}

		$comment = Request::getVar('comment', '', 'post', 'none', 2);
		$rowc = new Comment();
		$rowc->set('ticket', $id);

		// Check if changes were made inbetween the time the comment was started and posted
		if ($id)
		{
			$started = Request::getVar('started', Date::toSql(), 'post');
			$lastcomment = $row->comments('list', array(
				'sort'     => 'created',
				'sort_Dir' => 'DESC',
				'limit'    => 1,
				'start'    => 0,
				'ticket'   => $id
			))->first();

			if (isset($lastcomment) && $lastcomment->created() >= $started)
			{
				$rowc->set('comment', $comment);
				\Notify::error(Lang::txt('Changes were made to this ticket in the time since you began commenting/making changes. Please review your changes before submitting.'));
				return $this->editTask($rowc);
			}
		}

		if ($id && isset($_POST['status']) && $_POST['status'] == 0)
		{
			$row->set('open', 0);
			$row->set('resolved', Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPT_CLOSED'));
		}

		$row->set('open', $row->status('open'));

		// If an existing ticket AND closed AND previously open
		if ($id && !$row->get('open') && $row->get('open') != $old->get('open'))
		{
			// Record the closing time
			$row->set('closed', Date::toSql());
		}

		// Check content
		if (!$row->check())
		{
			throw new Exception($row->getError(), 500);
		}

		// Store new content
		if (!$row->store())
		{
			throw new Exception($row->getError(), 500);
		}

		// Save the tags
		$row->tag(Request::getVar('tags', '', 'post'), User::get('id'), 1);
		$row->set('tags', $row->tags('string'));

		$base = Request::base();
		if (substr($base, -14) == 'administrator/')
		{
			$base = substr($base, 0, strlen($base)-14);
		}

		$webpath = trim($this->config->get('webpath'), '/');

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
				$msg->setSubject(Config::get('sitename') . ' ' . Lang::txt('COM_SUPPORT') . ', ' . Lang::txt('COM_SUPPORT_TICKET_NUMBER', $row->get('id')));
				$msg->addFrom(
					Config::get('mailfrom'),
					Config::get('sitename') . ' ' . Lang::txt(strtoupper($this->_option))
				);

				// Plain text email
				$eview = new \Hubzero\Mail\View(array(
					'base_path' => PATH_CORE . DS . 'components' . DS . $this->_option . DS . 'site',
					'name'      => 'emails',
					'layout'    => 'ticket_plain'
				));
				$eview->option     = $this->_option;
				$eview->controller = $this->_controller;
				$eview->ticket     = $row;
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
					foreach ($row->attachments() as $attachment)
					{
						if ($attachment->size() < 2097152)
						{
							if ($attachment->isImage())
							{
								$file = basename($attachment->link('filepath'));
								$html = preg_replace('/<a class="img" data\-filename="' . str_replace('.', '\.', $file) . '" href="(.*?)"\>(.*?)<\/a>/i', '<img src="' . $message->getEmbed($attachment->link('filepath')) . '" alt="" />', $html);
							}
							else
							{
								$message->addAttachment($attachment->link('filepath'));
							}
						}
					}
				}

				$msg->addPart($html, 'text/html');

				// Loop through the addresses
				foreach ($defs As $def)
				{
					$def = trim($def);

					// Check if the address should come from Joomla config
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
		if ($comment)
		{
			// If a comment was posted by the ticket submitter to a "waiting user response" ticket, change status.
			if ($row->isWaiting() && User::get('username') == $row->get('login'))
			{
				$row->open();
			}
		}

		// Create a new support comment object and populate it
		$access = Request::getInt('access', 0);

		//$rowc = new Comment();
		$rowc->set('ticket', $row->get('id'));
		$rowc->set('comment', nl2br($comment));
		$rowc->set('created', Date::toSql());
		$rowc->set('created_by', User::get('id'));
		$rowc->set('access', $access);

		// Compare fields to find out what has changed for this ticket and build a changelog
		$rowc->changelog()->diff($old, $row);

		$rowc->changelog()->cced(Request::getVar('cc', ''));

		// Save the data
		if (!$rowc->store())
		{
			throw new Exception($rowc->getError(), 500);
		}

		Event::trigger('support.onTicketUpdate', array($row, $rowc));

		if ($tmp = Request::getInt('tmp_dir'))
		{
			$attach = new Tables\Attachment($this->database);
			$attach->updateCommentId($tmp, $rowc->get('id'));
		}

		if (!$isNew)
		{
			$attachment = $this->uploadTask($row->get('id'), $rowc->get('id'));
		}

		// Only do the following if a comment was posted or ticket was reassigned
		// otherwise, we're only recording a changelog
		if ($rowc->get('comment')
		 || $row->get('owner') != $old->get('owner')
		 || $row->get('group') != $old->get('group')
		 || $rowc->attachments()->total() > 0)
		{
			// Send e-mail to ticket submitter?
			if (Request::getInt('email_submitter', 0) == 1)
			{
				// Is the comment private? If so, we do NOT send e-mail to the
				// submitter regardless of the above setting
				if (!$rowc->isPrivate())
				{
					$rowc->addTo(array(
						'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_SUBMITTER'),
						'name'  => $row->submitter('name'),
						'email' => $row->submitter('email'),
						'id'    => $row->submitter('id')
					));
				}
			}

			// Send e-mail to ticket owner?
			if (Request::getInt('email_owner', 0) == 1)
			{
				if ($old->get('owner') && $row->get('owner') != $old->get('owner'))
				{
					$rowc->addTo(array(
						'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_PRIOR_OWNER'),
						'name'  => $old->owner('name'),
						'email' => $old->owner('email'),
						'id'    => $old->owner('id')
					));
				}
				if ($row->get('owner'))
				{
					$rowc->addTo(array(
						'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_OWNER'),
						'name'  => $row->owner('name'),
						'email' => $row->owner('email'),
						'id'    => $row->owner('id')
					));
				}
				elseif ($row->get('group'))
				{
					$group = \Hubzero\User\Group::getInstance($row->get('group'));

					if ($group)
					{
						foreach ($group->get('managers') as $manager)
						{
							$manager = User::getInstance($manager);

							if (!$manager || !$manager->get('id'))
							{
								continue;
							}

							$rowc->addTo(array(
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
			foreach ($rowc->changelog()->get('cc') as $cc)
			{
				$rowc->addTo($cc, Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC'));
			}

			// Message people watching this ticket,
			// but ONLY if the comment was NOT marked private
			$this->acl = ACL::getACL();
			foreach ($row->watchers() as $watcher)
			{
				$this->acl->setUser($watcher->user_id);
				if (!$rowc->isPrivate() || ($rowc->isPrivate() && $this->acl->check('read', 'private_comments')))
				{
					$rowc->addTo($watcher->user_id, 'watcher');
				}
			}
			$this->acl->setUser(User::get('id'));

			if (count($rowc->to()))
			{
				// Build e-mail components
				$subject = Lang::txt('COM_SUPPORT_EMAIL_SUBJECT_TICKET_COMMENT', $row->get('id'));

				$from = array(
					'name'      => Lang::txt('COM_SUPPORT_EMAIL_FROM', Config::get('sitename')),
					'email'     => Config::get('mailfrom'),
					'multipart' => md5(date('U'))  // Html email
				);

				// Plain text email
				$eview = new \Hubzero\Mail\View(array(
					'base_path' => PATH_CORE . DS . 'components' . DS . $this->_option . DS . 'site',
					'name'      => 'emails',
					'layout'    => 'comment_plain'
				));
				$eview->option     = $this->_option;
				$eview->controller = $this->_controller;
				$eview->comment    = $rowc;
				$eview->ticket     = $row;
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
					foreach ($rowc->attachments() as $attachment)
					{
						if ($attachment->size() < 2097152)
						{
							$message['attachments'][] = $attachment->link('filepath');
						}
					}
				}

				// Send e-mail to admin?
				foreach ($rowc->to('ids') as $to)
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
					$rowc->changelog()->notified(
						$to['role'],
						$to['name'],
						$to['email']
					);
				}

				foreach ($rowc->to('emails') as $to)
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
					$rowc->changelog()->notified(
						$to['role'],
						$to['name'],
						$to['email']
					);
				}
			}
			else
			{
				// Force entry to private if no comment or attachment was made
				if (!$rowc->get('comment') && $rowc->attachments()->total() <= 0)
				{
					$rowc->set('access', 1);
				}
			}

			// Were there any changes?
			if (count($rowc->changelog()->get('notifications')) > 0 || $access != $rowc->get('access'))
			{
				// Save the data
				if (!$rowc->store())
				{
					throw new Exception($rowc->getError(), 500);
				}
			}
		}

		// output messsage and redirect
		if ($redirect)
		{
			$filters = Request::getVar('filters', '');
			$filters = str_replace('&amp;','&', $filters);

			// Redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($filters ? '&' . $filters : ''), false),
				Lang::txt('COM_SUPPORT_TICKET_SUCCESSFULLY_SAVED', $row->get('id'))
			);
			return;
		}

		$this->view->setLayout('edit');
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
		$this->view->ids = Request::getVar('id', array());
		$this->view->tmpl = Request::getVar('tmpl', '');

		$this->view->filters = Utilities::getFilters();
		$this->view->lists = array();

		// Get categories
		$sa = new Tables\Category($this->database);
		$this->view->lists['categories'] = $sa->find('list');

		// Get severities
		$this->view->lists['severities'] = Utilities::getSeverities($this->config->get('severities'));

		$this->view->lists['owner'] = $this->_userSelect('owner', '', 1);

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Process a batch change
	 *
	 * @return  void
	 */
	public function processTask()
	{
		// Incoming
		$tmpl    = Request::getVar('tmpl');

		$ids     = Request::getVar('id', array());
		$fields  = Request::getVar('fields', array());
		$tags    = Request::getVar('tags', '');
		$access  = 1;

		$fields['owner'] = Request::getVar('owner', '');
		/*$comment = nl2br(Request::getVar('comment', '', 'post', 'none', 2));
		$cc      = Request::getVar('cc', '');
		$access  = Request::getInt('access', 0);
		$email_submitter = Request::getInt('email_submitter', 0);
		$email_owner = Request::getInt('email_owner', 0);

		$base = Request::base();
		if (substr($base, -14) == 'administrator/')
		{
			$base = substr($base, 0, strlen($base)-14);
		}

		$webpath = trim($this->config->get('webpath'), '/');

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
			$row = new Ticket($id);
			if (!$row->exists())
			{
				continue;
			}

			$old = new Ticket($id);
			$old->set('tags', $old->tags('string'));

			if (!$row->bind($fields))
			{
				$this->setError($row->getError());
				continue;
			}

			// Store new content
			if (!$row->store(true))
			{
				$this->setError($row->getError());
			}

			// Only set the tags if any tags have been provided
			if ($tags)
			{
				$row->set('tags', $tags);
				$row->tag($row->get('tags'), User::get('id'), 1);
			}
			else
			{
				$row->set('tags', $row->tags('string'));
			}

			// Create a new support comment object and populate it
			$rowc = new Comment();
			$rowc->set('ticket', $id);
			$rowc->set('comment', $comment);
			$rowc->set('created', Date::toSql());
			$rowc->set('created_by', User::get('id'));
			//$rowc->set('access', $access);

			// Compare fields to find out what has changed for this ticket and build a changelog
			$rowc->changelog()->diff($old, $row);
			$rowc->changelog()->cced($cc);

			// Save the data
			if (!$rowc->store())
			{
				$this->setError($rowc->getError());
				continue;
			}

			// Only do the following if a comment was posted or ticket was reassigned
			// otherwise, we're only recording a changelog
			/*if ($rowc->get('comment') || $row->get('owner') != $old->get('owner'))
			{
				// Send e-mail to ticket submitter?
				if ($email_submitter == 1 && !$rowc->isPrivate())
				{
					$rowc->addTo(array(
						'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_SUBMITTER'),
						'name'  => $row->submitter('name'),
						'email' => $row->submitter('email'),
						'id'    => $row->submitter('id')
					));
				}

				// Send e-mail to ticket owner?
				if ($email_owner == 1 && $row->get('owner'))
				{
					$rowc->addTo(array(
						'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_OWNER'),
						'name'  => $row->owner('name'),
						'email' => $row->owner('email'),
						'id'    => $row->owner('id')
					));
				}

				// Add any CCs to the e-mail list
				foreach ($rowc->changelog()->get('cc') as $cc)
				{
					$rowc->addTo($cc, Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC'));
				}

				// Message people watching this ticket,
				// but ONLY if the comment was NOT marked private
				if (!$rowc->isPrivate())
				{
					foreach ($row->watchers() as $watcher)
					{
						$rowc->addTo($watcher->user_id, 'watcher');
					}
				}

				if (count($rowc->to()))
				{
					// Build e-mail components
					$subject = Lang::txt('COM_SUPPORT_EMAIL_SUBJECT_TICKET_COMMENT', $row->get('id'));

					$from = array(
						'name'      => Lang::txt('COM_SUPPORT_EMAIL_FROM', Config::get('sitename')),
						'email'     => Config::get('mailfrom'),
						'multipart' => md5(date('U'))  // Html email
					);

					// Plain text email
					$eview = new \Hubzero\Component\View(array(
						'base_path' => PATH_CORE . DS . 'components' . DS . $this->_option . DS . 'site',
						'name'      => 'emails',
						'layout'    => 'comment_plain'
					));
					$eview->option     = $this->_option;
					$eview->controller = $this->_controller;
					$eview->comment    = $rowc;
					$eview->ticket     = $row;
					$eview->delimiter  = ($allowEmailResponses ? '~!~!~!~!~!~!~!~!~!~!' : '');

					$message['plaintext'] = $eview->loadTemplate();
					$message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

					// HTML email
					$eview->setLayout('comment_html');

					$message['multipart'] = $eview->loadTemplate();
					$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

					// Send e-mail to admin?
					Plugin::import('xmessage');

					foreach ($rowc->to('ids') as $to)
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
						$rowc->changelog()->notified(
							$to['role'],
							$to['name'],
							$to['email']
						);
					}

					foreach ($rowc->to('emails') as $to)
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

						$rowc->changelog()->notified(
							$to['role'],
							$to['name'],
							$to['email']
						);
					}

					// Were there any changes?
					if (count($rowc->changelog()->get('notifications')) > 0)
					{
						// Save the data
						if (!$rowc->store())
						{
							$this->setError($rowc->getError());
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
	 * @return	void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getVar('id', array());

		// Check for an ID
		if (count($ids) < 1)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_SUPPORT_ERROR_SELECT_TICKET_TO_DELETE'),
				'error'
			);
			return;
		}

		foreach ($ids as $id)
		{
			$id = intval($id);

			// Delete tags
			$tags = new Tags($id);
			$tags->removeAll();

			// Delete comments
			$comment = new Tables\Comment($this->database);
			$comment->deleteComments($id);

			// Delete attachments
			$attach = new Tables\Attachment($this->database);
			$attach->deleteAllForTicket($id);

			// Delete ticket
			$ticket = new Tables\Ticket($this->database);
			$ticket->delete($id);
		}

		// Output messsage and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_SUPPORT_TICKET_SUCCESSFULLY_DELETED', count($ids))
		);
	}

	/**
	 * Generates a select list of Super Administrator names
	 *
	 * @param  $name        Select element 'name' attribute
	 * @param  $active      Selected option
	 * @param  $nouser      Flag to set first option to 'No user'
	 * @param  $javascript  Any inline JS to attach to the element
	 * @param  $order       The sort order for items in the list
	 * @return string       HTML select list
	 */
	private function _userSelect($name, $active, $nouser=0, $javascript=NULL, $order='a.name')
	{
		$query = "SELECT a.id AS value, a.name AS text"
			. " FROM #__users AS a"
			. " INNER JOIN #__support_acl_aros AS aro ON aro.model='user' AND aro.foreign_key = a.id"
			. " WHERE a.block = '0'"
			. " ORDER BY ". $order;

		$this->database->setQuery($query);
		if ($nouser)
		{
			$users[] = \Html::select('option', '0', Lang::txt('COM_SUPPORT_NO_USER'), 'value', 'text');
			$users = array_merge($users, $this->database->loadObjectList());
		}
		else
		{
			$users = $this->database->loadObjectList();
		}

		$query = "SELECT a.id AS value, a.name AS text, aro.alias"
			. " FROM #__users AS a"
			. " INNER JOIN #__xgroups_members AS m ON m.uidNumber = a.id"
			. " INNER JOIN #__support_acl_aros AS aro ON aro.model='group' AND aro.foreign_key = m.gidNumber"
			. " WHERE a.block = '0'"
			. " ORDER BY ". $order;
		$this->database->setQuery($query);
		if ($results = $this->database->loadObjectList())
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
	 * @param  $name        Select element 'name' attribute
	 * @param  $active      Selected option
	 * @param  $nouser      Flag to set first option to 'No user'
	 * @param  $javascript  Any inline JS to attach to the element
	 * @param  $group       The group to pull member names from
	 * @return string       HTML select list
	 */
	private function _userSelectGroup($name, $active, $nouser=0, $javascript=NULL, $group='')
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
	 * @return void
	 */
	public function downloadTask()
	{
		// Get the ID of the file requested
		$id = Request::getInt('id', 0);

		// Instantiate an attachment object
		$attach = new Tables\Attachment($this->database);
		$attach->load($id);
		if (!$attach->filename)
		{
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_FILE_NOT_FOUND'), 404);
			return;
		}
		$file = $attach->filename;

		// Ensure we have a path
		if (empty($file))
		{
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_FILE_NOT_FOUND'), 404);
		}

		// Get the configured upload path
		$basePath = DS . trim($this->config->get('webpath', '/site/tickets'), DS) . DS . $attach->ticket;

		$file = DS . ltrim($file, DS);
		// Does the beginning of the $attachment->path match the config path?
		if (substr($file, 0, strlen($basePath)) == $basePath)
		{
			// Yes - this means the full path got saved at some point
		}
		else
		{
			// No - append it
			$file = $basePath . $file;
		}

		// Add root path
		$filename = PATH_APP . $file;

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
		else
		{
			exit;
		}
		return;
	}

	/**
	 * Uploads a file and generates a database entry for that item
	 *
	 * @param  $listdir Sub-directory to upload files to
	 * @return string   Key to use in comment bodies (parsed into links or img tags)
	 */
	public function uploadTask($listdir, $comment = 0)
	{
		// Incoming
		$description = Request::getVar('description', '');

		if (!$listdir)
		{
			$this->setError(Lang::txt('COM_SUPPORT_ERROR_NO_ID'));
			return '';
		}

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!is_array($file) || !isset($file['name']) || !$file['name'])
		{
			$this->setError(Lang::txt('COM_SUPPORT_ERROR_NO_FILE'));
			return '';
		}

		// Construct our file path
		$file_path = PATH_APP . DS . trim($this->config->get('webpath', '/site/tickets'), DS) . DS . $listdir;

		if (!is_dir($file_path))
		{
			if (!Filesystem::makeDirectory($file_path))
			{
				$this->setError(Lang::txt('COM_SUPPORT_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return '';
			}
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

		// Perform the upload
		if (!Filesystem::upload($file['tmp_name'], $finalfile))
		{
			$this->setError(Lang::txt('COM_SUPPORT_ERROR_UPLOADING'));
			return '';
		}
		else
		{
			// Scan for viruses
			//$path = $file_path . DS . $file['name']; //PATH_CORE . DS . 'virustest';

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

			$row = new Tables\Attachment($this->database);
			$row->bind(array(
				'id'          => 0,
				'ticket'      => $listdir,
				'comment_id'  => $comment,
				'filename'    => $filename . '.' . $ext,
				'description' => $description
			));
			if (!$row->check())
			{
				$this->setError($row->getError());
			}
			if (!$row->store())
			{
				$this->setError($row->getError());
			}
			if (!$row->id)
			{
				$row->getID();
			}

			return '{attachment#' . $row->id . '}';
		}
	}
}
