<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 * /administrator/components/com_support/controllers/tickets.php
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

include_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'query.php');
include_once(JPATH_COMPONENT_SITE . DS . 'models' . DS . 'ticket.php');

/**
 * Support controller class for tickets
 */
class SupportControllerTickets extends \Hubzero\Component\AdminController
{
	/**
	 * Displays a list of tickets
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Get configuration
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();

		$obj = new SupportTicket($this->database);

		// Get filters
		//$this->view->filters = SupportUtilities::getFilters();
		$this->view->total = 0;
		$this->view->rows = array();

		$this->view->filters = array();
		// Paging
		$this->view->filters['limit'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);
		// Query to filter by
		$this->view->filters['show'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.show',
			'show',
			0,
			'int'
		);
		$this->view->filters['search'] = '';

		// Get query list
		$sq = new SupportQuery($this->database);
		$this->view->queries = array(
			'common' => $sq->getCommon(),
			'mine'   => $sq->getMine(),
			'custom' => $sq->getCustom($this->juser->get('id'))
		);
		if (!$this->view->queries['common'] || count($this->view->queries['common']) <= 0)
		{
			$this->view->queries['common'] = $sq->populateDefaults('common');
		}
		if (!$this->view->queries['mine'] || count($this->view->queries['mine']) <= 0)
		{
			$this->view->queries['mine'] = $sq->populateDefaults('mine');
		}
		// If no query is set, default to the first one in the list
		if (!$this->view->filters['show'])
		{
			$this->view->filters['show'] = $this->view->queries['common'][0]->id;
		}
		// Loop through each grouping
		foreach ($this->view->queries as $key => $queries)
		{
			// Loop through each query in a group
			foreach ($queries as $k => $query)
			{
				// Build the query from the condition set
				//if (!$query->query)
				//{
					$query->query = $sq->getQuery($query->conditions);
				//}
				$filters = $this->view->filters;
				if ($query->id != $this->view->filters['show'])
				{
					$filters['search'] = '';
				}
				// Get a record count
				$this->view->queries[$key][$k]->count = $obj->getCount($query->query, $filters);
				// The query is the current active query
				// get records
				if ($query->id == $this->view->filters['show'])
				{
					// Search
					$this->view->filters['search']       = urldecode($app->getUserStateFromRequest(
						$this->_option . '.' . $this->_controller . '.search',
						'search',
						''
					));
					// Set the total for the pagination
					$this->view->total = ($this->view->filters['search']) ? $obj->getCount($query->query, $this->view->filters) : $this->view->queries[$key][$k]->count;

					// Incoming sort
					$this->view->filters['sort']         = trim($app->getUserStateFromRequest(
						$this->_option . '.' . $this->_controller . '.sort',
						'filter_order',
						$query->sort
					));
					$this->view->filters['sortdir']     = trim($app->getUserStateFromRequest(
						$this->_option . '.' . $this->_controller . '.sortdir',
						'filter_order_Dir',
						$query->sort_dir
					));
					//$this->view->filters['sort']    = $query->sort;
					//$this->view->filters['sortdir'] = $query->sort_dir;
					// Get the records
					$this->view->rows  = $obj->getRecords($query->query, $this->view->filters);
				}
			}
		}

		$watching = new SupportTableWatching($this->database);
		$this->view->watchcount = $watching->count(array(
			'user_id'   => $this->juser->get('id')
		));
		if ($this->view->filters['show'] == -1)
		{
			if (!isset($this->view->filters['sort']) || !$this->view->filters['sort'])
			{
				$this->view->filters['sort']         = trim($app->getUserStateFromRequest(
					$this->_option . '.' . $this->_controller . '.sort',
					'filter_order',
					'created'
				));
			}
			if (!isset($this->view->filters['sortdir']) || !$this->view->filters['sortdir'])
			{
				$this->view->filters['sortdir']         = trim($app->getUserStateFromRequest(
					$this->_option . '.' . $this->_controller . '.sortdir',
					'filter_order_Dir',
					'DESC'
				));
			}
			$records = $watching->find(array(
				'user_id'   => $this->juser->get('id')
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
			else
			{
				$this->view->rows = array();
			}
		}

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
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
	 * @return	void
	 */
	public function editTask()
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		// Incoming
		$id = JRequest::getInt('id', 0);

		// Initiate database class and load info
		$row = SupportModelTicket::getInstance($id);

		// Editing or creating a ticket?
		if (!$row->exists())
		{
			$this->view->setLayout('add');

			// Creating a new ticket
			$row->set('severity', 'normal');
			$row->set('status', 0);
			$row->set('created', JFactory::getDate()->toSql());
			$row->set('login', $this->juser->get('username'));
			$row->set('name', $this->juser->get('name'));
			$row->set('email', $this->juser->get('email'));
			$row->set('cookies', 1);

			$browser = new \Hubzero\Browser\Detector();

			$row->set('os', $browser->platform() . ' ' . $browser->platformVersion());
			$row->set('browser', $browser->name() . ' ' . $browser->version());

			$row->set('uas', JRequest::getVar('HTTP_USER_AGENT','','server'));

			$row->set('ip', JRequest::ip());
			$row->set('hostname', gethostbyaddr(JRequest::getVar('REMOTE_ADDR','','server')));
			$row->set('section', 1);
		}

		$this->view->filters = SupportUtilities::getFilters();
		$this->view->lists = array();

		// Get resolutions
		$sr = new SupportResolution($this->database);
		$this->view->lists['resolutions'] = $sr->getResolutions();

		// Get messages
		$sm = new SupportMessage($this->database);
		$this->view->lists['messages'] = $sm->getMessages();

		// Get sections
		//$ss = new SupportSection($this->database);
		//$this->view->lists['sections'] = $ss->getSections();

		// Get categories
		$sa = new SupportCategory($this->database);
		$this->view->lists['categories'] = $sa->find('list');

		// Get severities
		$this->view->lists['severities'] = SupportUtilities::getSeverities($this->config->get('severities'));

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

		if ($watch = JRequest::getWord('watch', ''))
		{
			$watch = strtolower($watch);

			// Already watching
			if ($this->view->row->isWatching($this->juser))
			{
				// Stop watching?
				if ($watch == 'stop')
				{
					$this->view->row->stopWatching($this->juser);
				}
			}
			// Not already watching
			else
			{
				// Start watching?
				if ($watch == 'start')
				{
					$this->view->row->watch($this->juser);
					if (!$this->view->row->isWatching($this->juser, true))
					{
						$this->setError(JText::_('COM_SUPPORT_ERROR_FAILED_TO_WATCH'));
					}
				}
			}
		}

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
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
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$isNew = true;
		$id = JRequest::getInt('id', 0);
		if ($id)
		{
			$isNew = false;
		}

		// Load the old ticket so we can compare for the changelog
		$old = new SupportModelTicket($id);
		$old->set('tags', $old->tags('string'));

		// Trim and addslashes all posted items
		$_POST = array_map('trim', $_POST);

		// Initiate class and bind posted items to database fields
		$row = new SupportModelTicket($id);
		if (!$row->bind($_POST))
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		if ($id && isset($_POST['status']) && $_POST['status'] == 0)
		{
			$row->set('open', 0);
			$row->set('resolved', JText::_('COM_SUPPORT_TICKET_COMMENT_OPT_CLOSED'));
		}

		// Check content
		if (!$row->check())
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Store new content
		if (!$row->store())
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Save the tags
		$row->tag(JRequest::getVar('tags', '', 'post'), $this->juser->get('id'), 1);
		$row->set('tags', $row->tags('string'));

		$juri = JURI::getInstance();
		$jconfig = JFactory::getConfig();

		$base = $juri->base();
		if (substr($base, -14) == 'administrator/')
		{
			$base = substr($base, 0, strlen($base)-14);
		}

		$webpath = trim($this->config->get('webpath'), '/');

		$allowEmailResponses = false;
		if ($this->config->get('email_processing') and file_exists("/etc/hubmail_gw.conf"))
		{
			$allowEmailResponses = true;
		}
		if ($allowEmailResponses)
		{
			$encryptor = new \Hubzero\Mail\Token();
		}

		// If a new ticket...
		if ($isNew)
		{
			// Get any set emails that should be notified of ticket submission
			$defs = str_replace("\r", '', $this->config->get('emails', '{config.mailfrom}'));
			$defs = str_replace('\n', "\n", $defs);
			$defs = explode("\n", $defs);

			if ($defs)
			{
				// Get some email settings
				$msg = new \Hubzero\Mail\Message();
				$msg->setSubject($jconfig->getValue('config.sitename') . ' ' . JText::_('COM_SUPPORT') . ', ' . JText::sprintf('COM_SUPPORT_TICKET_NUMBER', $row->get('id')));
				$msg->addFrom(
					$jconfig->getValue('config.mailfrom'),
					$jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_option))
				);

				// Plain text email
				$eview = new \Hubzero\Component\View(array(
					'base_path' => JPATH_ROOT . DS . 'components' . DS . $this->_option,
					'name'      => 'emails',
					'layout'    => 'ticket_plain'
				));
				$eview->option     = $this->_option;
				$eview->controller = $this->_controller;
				$eview->ticket     = $row;
				$eview->delimiter  = '';

				$plain = $eview->loadTemplate();
				$plain = str_replace("\n", "\r\n", $plain);

				$msg->addPart($plain, 'text/plain');

				// HTML email
				$eview->setLayout('ticket_html');

				$html = $eview->loadTemplate();
				$html = str_replace("\n", "\r\n", $html);

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

				$msg->addPart($html, 'text/html');

				// Loop through the addresses
				foreach ($defs As $def)
				{
					$def = trim($def);

					// Check if the address should come from Joomla config
					if ($def == '{config.mailfrom}')
					{
						$def = $jconfig->getValue('config.mailfrom');
					}
					// Check for a valid address
					if (\Hubzero\Utility\Validate::email($def))
					{
						// Send e-mail
						$msg->setTo(array($def));
						$msg->send();
					}
				}
			}
		}

		// Incoming comment
		$comment = JRequest::getVar('comment', '', 'post', 'none', 2);
		if ($comment)
		{
			// If a comment was posted by the ticket submitter to a "waiting user response" ticket, change status.
			if ($row->isWaiting() && $this->juser->get('username') == $row->get('login'))
			{
				$row->open();
			}
		}

		// Create a new support comment object and populate it
		$access = JRequest::getInt('access', 0);

		$rowc = new SupportModelComment();
		$rowc->set('ticket', $row->get('id'));
		$rowc->set('comment', nl2br($comment));
		$rowc->set('created', JFactory::getDate()->toSql());
		$rowc->set('created_by', $this->juser->get('id'));
		$rowc->set('access', $access);

		// Compare fields to find out what has changed for this ticket and build a changelog
		$rowc->changelog()->diff($old, $row);

		$rowc->changelog()->cced(JRequest::getVar('cc', ''));

		// Save the data
		if (!$rowc->store())
		{
			JError::raiseError(500, $rowc->getError());
			return;
		}

		JPluginHelper::importPlugin('support');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onTicketUpdate', array($row, $rowc));

		if (!$isNew)
		{
			$attachment = $this->uploadTask($row->get('id'), $rowc->get('id'));
		}

		// Only do the following if a comment was posted or ticket was reassigned
		// otherwise, we're only recording a changelog
		if ($rowc->get('comment') || $row->get('owner') != $old->get('owner') || $rowc->attachments()->total() > 0)
		{
			// Send e-mail to ticket submitter?
			if (JRequest::getInt('email_submitter', 0) == 1)
			{
				// Is the comment private? If so, we do NOT send e-mail to the
				// submitter regardless of the above setting
				if (!$rowc->isPrivate())
				{
					$rowc->addTo(array(
						'role'  => JText::_('COM_SUPPORT_COMMENT_SEND_EMAIL_SUBMITTER'),
						'name'  => $row->submitter('name'),
						'email' => $row->submitter('email'),
						'id'    => $row->submitter('id')
					));
				}
			}

			// Send e-mail to ticket owner?
			if (JRequest::getInt('email_owner', 0) == 1)
			{
				if ($row->get('owner'))
				{
					$rowc->addTo(array(
						'role'  => JText::_('COM_SUPPORT_COMMENT_SEND_EMAIL_OWNER'),
						'name'  => $row->owner('name'),
						'email' => $row->owner('email'),
						'id'    => $row->owner('id')
					));
				}
			}

			// Add any CCs to the e-mail list
			foreach ($rowc->changelog()->get('cc') as $cc)
			{
				$rowc->addTo($cc, JText::_('COM_SUPPORT_COMMENT_SEND_EMAIL_CC'));
			}

			// Message people watching this ticket, 
			// but ONLY if the comment was NOT marked private
			$this->acl = SupportACL::getACL();
			foreach ($row->watchers() as $watcher)
			{
				$this->acl->setUser($watcher->user_id);
				if (!$rowc->isPrivate() || ($rowc->isPrivate() && $this->acl->check('read', 'private_comments')))
				{
					$rowc->addTo($watcher->user_id, 'watcher');
				}
			}
			$this->acl->setUser($this->juser->get('id'));

			if (count($rowc->to()))
			{
				// Build e-mail components
				$subject = JText::sprintf('COM_SUPPORT_EMAIL_SUBJECT_TICKET_COMMENT', $row->get('id'));

				$from = array(
					'name'      => JText::sprintf('COM_SUPPORT_EMAIL_FROM', $jconfig->getValue('config.sitename')),
					'email'     => $jconfig->getValue('config.mailfrom'),
					'multipart' => md5(date('U'))  // Html email
				);

				// Plain text email
				$eview = new \Hubzero\Component\View(array(
					'base_path' => JPATH_ROOT . DS . 'components' . DS . $this->_option,
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

				$message['attachments'] = array();
				foreach ($rowc->attachments() as $attachment)
				{
					if ($attachment->size() < 2097152)
					{
						$message['attachments'][] = $attachment->link('filepath');
					}
				}

				// Send e-mail to admin?
				JPluginHelper::importPlugin('xmessage');

				foreach ($rowc->to('ids') as $to)
				{
					if ($allowEmailResponses)
					{
						// The reply-to address contains the token
						$token = $encryptor->buildEmailToken(1, 1, $to['id'], $id);
						$from['replytoemail'] = 'htc-' . $token . strstr($jconfig->getValue('config.mailfrom'), '@');
					}

					// Get the user's email address
					if (!$dispatcher->trigger('onSendMessage', array('support_reply_submitted', $subject, $message, $from, array($to['id']), $this->_option)))
					{
						$this->setError(JText::sprintf('COM_SUPPORT_ERROR_FAILED_TO_MESSAGE', $to['name'] . '(' . $to['role'] . ')'));
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
							'htc-' . $token . strstr($jconfig->getValue('config.mailfrom'), '@')
						);

						// In this case each item in email in an array, 1- To, 2:reply to address
						SupportUtilities::sendEmail($email[0], $subject, $message, $from, $email[1]);
					}
					else
					{
						// email is just a plain 'ol string
						SupportUtilities::sendEmail($to['email'], $subject, $message, $from);
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
					JError::raiseError(500, $rowc->getError());
					return;
				}
			}
		}

		// output messsage and redirect
		if ($redirect)
		{
			$filters = JRequest::getVar('filters', '');
			$filters = str_replace('&amp;','&', $filters);

			// Redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($filters ? '&' . $filters : ''),
				JText::sprintf('COM_SUPPORT_TICKET_SUCCESSFULLY_SAVED', $row->get('id'))
			);
		}
		else
		{
			$this->view->setLayout('edit');
			$this->editTask();
		}
	}

	/**
	 * Removes a ticket and all associated records (tags, comments, etc.)
	 *
	 * @return	void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		// Check for an ID
		if (count($ids) < 1)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_SUPPORT_ERROR_SELECT_TICKET_TO_DELETE'),
				'error'
			);
			return;
		}

		foreach ($ids as $id)
		{
			$id = intval($id);

			// Delete tags
			$tags = new SupportTags($this->database);
			$tags->remove_all_tags($id);

			// Delete comments
			$comment = new SupportComment($this->database);
			$comment->deleteComments($id);

			// Delete attachments
			$attach = new SupportAttachment($this->database);
			$attach->deleteAllForTicket($id);

			// Delete ticket
			$ticket = new SupportTicket($this->database);
			$ticket->delete($id);
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::sprintf('COM_SUPPORT_TICKET_SUCCESSFULLY_DELETED', count($ids))
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
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
			$users[] = JHTML::_('select.option', '0', JText::_('COM_SUPPORT_NO_USER'), 'value', 'text');
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
				$users[] = JHTML::_('select.optgroup', JText::_('COM_SUPPORT_GROUP') . ' ' . $nme);
				$users = array_merge($users, $gusers);
				$users[] = JHTML::_('select.optgroup', JText::_('COM_SUPPORT_GROUP') . ' ' . $nme);
			}
		}

		$users = JHTML::_('select.genericlist', $users, $name, ' '. $javascript, 'value', 'text', $active, false, false);

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
			$users[] = JHTML::_('select.option', '0', JText::_('COM_SUPPORT_NO_USER'), 'value', 'text');
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
						$users[] = JHTML::_('select.optgroup', stripslashes($hzg->description));
						foreach ($members as $member)
						{
							$u = JUser::getInstance($member);
							if (!is_object($u))
							{
								continue;
							}

							$m = new stdClass();
							$m->value = $u->get('id');
							$m->text  = $u->get('name');
							$m->groupname = $g;

							$users[] = $m;
						}
						//$users[] = '</optgroup>';
						$users[] = JHTML::_('select.option', '</OPTGROUP>');
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
					$u = JUser::getInstance($member);
					if (!is_object($u))
					{
						continue;
					}

					$m = new stdClass();
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

		$users = JHTML::_('select.genericlist', $users, $name, ' ' . $javascript, 'value', 'text', $active, false, false);

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
		$id = JRequest::getInt('id', 0);

		// Instantiate an attachment object
		$attach = new SupportAttachment($this->database);
		$attach->load($id);
		if (!$attach->filename)
		{
			JError::raiseError(404, JText::_('COM_SUPPORT_ERROR_FILE_NOT_FOUND'));
			return;
		}
		$file = $attach->filename;

		// Ensure we have a path
		if (empty($file))
		{
			JError::raiseError(404, JText::_('COM_SUPPORT_ERROR_FILE_NOT_FOUND'));
			return;
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

		// Add JPATH_ROOT
		$filename = JPATH_ROOT . $file;

		// Ensure the file exist
		if (!file_exists($filename))
		{
			JError::raiseError(404, JText::_('COM_SUPPORT_ERROR_FILE_NOT_FOUND') . ' ' . $filename);
			return;
		}

		// Initiate a new content server and serve up the file
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($filename);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve())
		{
			// Should only get here on error
			JError::raiseError(404, JText::_('COM_SUPPORT_SERVER_ERROR'));
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
		$description = JRequest::getVar('description', '');

		if (!$listdir)
		{
			$this->setError(JText::_('COM_SUPPORT_ERROR_NO_ID'));
			return '';
		}

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(JText::_('COM_SUPPORT_ERROR_NO_FILE'));
			return '';
		}

		// Construct our file path
		$file_path = JPATH_ROOT . DS . trim($this->config->get('webpath', '/site/tickets'), DS) . DS . $listdir;

		if (!is_dir($file_path))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($file_path))
			{
				$this->setError(JText::_('COM_SUPPORT_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return '';
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		$ext = strtolower(JFile::getExt($file['name']));

		$filename = JFile::stripExt($file['name']);
		while (file_exists($file_path . DS . $filename . '.' . $ext))
		{
			$filename .= rand(10, 99);
		}

		$finalfile = $file_path . DS . $filename . '.' . $ext;

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $finalfile))
		{
			$this->setError(JText::_('COM_SUPPORT_ERROR_UPLOADING'));
			return '';
		}
		else
		{
			// Scan for viruses
			//$path = $file_path . DS . $file['name']; //JPATH_ROOT . DS . 'virustest';
			exec("clamscan -i --no-summary --block-encrypted $finalfile", $output, $status);
			if ($status == 1)
			{
				if (JFile::delete($finalfile))
				{
					$this->setError(JText::_('COM_SUPPORT_ERROR_FAILED_SECURITY_SCAN'));
					return '';
				}
			}

			// File was uploaded
			// Create database entry
			$description = htmlspecialchars($description);

			$row = new SupportAttachment($this->database);
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
