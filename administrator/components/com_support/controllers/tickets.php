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

ximport('Hubzero_Controller');

include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'query.php');
include_once(JPATH_ROOT . DS . 'libraries' . DS . 'Hubzero' . DS . 'EmailToken.php');

/**
 * Support controller class for tickets
 */
class SupportControllerTickets extends Hubzero_Controller
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
		$app =& JFactory::getApplication();

		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'assets' . DS . 'css' . DS . $this->_name . '.css');

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
				if (!$query->query)
				{
					$query->query = $sq->getQuery($query->conditions);
				}
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

		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'assets' . DS . 'css' . DS . $this->_name . '.css');
		//$document->addScript('/components' . DS . 'com_support' . DS . 'autosave.js');

		// Incoming
		$id = JRequest::getInt('id', 0);

		$this->view->filters = SupportUtilities::getFilters();

		// Initiate database class and load info
		$row = new SupportTicket($this->database);
		$row->load($id);

		// Editing or creating a ticket?
		if ($id)
		{
			// Get comments
			$sc = new SupportComment($this->database);
			$comments = $sc->getComments('admin', $row->id);

			// Parse comment text for attachment tags
			$juri =& JURI::getInstance();
			$webpath = str_replace('/administrator/', '/', $juri->base() . $this->config->get('webpath', '/site/tickets') . DS . $id);
			$webpath = str_replace('//','/',$webpath);
			if (isset($_SERVER['HTTPS']))
			{
				$webpath = str_replace('http:','https:',$webpath);
			}
			if (!strstr($webpath, '://'))
			{
				$webpath = str_replace(':/','://',$webpath);
			}

			$attach = new SupportAttachment($this->database);
			$attach->webpath = $webpath;
			$attach->uppath  = JPATH_ROOT . $this->config->get('webpath') . DS . $id;
			$attach->output  = 'web';
			for ($i=0; $i < count($comments); $i++)
			{
				$comment =& $comments[$i];
				$comment->comment = $attach->parse($comment->comment);
			}

			$row->statustext = SupportHtml::getStatus($row->status);
		}
		else
		{
			// Creating a new ticket
			$row->severity = 'normal';
			$row->status   = 0;
			$row->created  = date('Y-m-d H:i:s', time());
			$row->login    = $this->juser->get('username');
			$row->name     = $this->juser->get('name');
			$row->email    = $this->juser->get('email');
			$row->cookies  = 1;

			ximport('Hubzero_Browser');
			$browser = new Hubzero_Browser();

			$row->os = $browser->getOs() . ' ' . $browser->getOsVersion();
			$row->browser = $browser->getBrowser() . ' ' . $browser->getBrowserVersion();

			$row->uas = JRequest::getVar('HTTP_USER_AGENT','','server');

			ximport('Hubzero_Environment');
			$row->ip = Hubzero_Environment::ipAddress();
			$row->hostname = gethostbyaddr(JRequest::getVar('REMOTE_ADDR','','server'));
			$row->section = 1;

			$comments = array();
		}

		// Do some text cleanup
		//$row->summary = html_entity_decode(stripslashes($row->summary), ENT_COMPAT, 'UTF-8');
		//$row->summary = str_replace('&quote;','&quot;',$row->summary);
		//$row->summary = htmlentities($row->summary, ENT_COMPAT, 'UTF-8');

		//$row->report  = html_entity_decode(stripslashes($row->report), ENT_COMPAT, 'UTF-8');
		//$row->report  = str_replace('&quote;','&quot;',$row->report);
		//$row->report  = str_replace("<br />","",$row->report);
		//$row->report  = htmlentities($row->report, ENT_COMPAT, 'UTF-8');
		$row->report  = $this->view->escape($row->report);
		$row->report  = nl2br($row->report);
		$row->report  = str_replace("\t",' &nbsp; &nbsp;',$row->report);
		//$row->report  = str_replace("    ",'&nbsp;&nbsp;&nbsp;&nbsp;',$row->report);

		if ($id)
		{
			$row->report = $attach->parse($row->report);
		}

		$this->view->lists = array();

		// Get resolutions
		$sr = new SupportResolution($this->database);
		$this->view->lists['resolutions'] = $sr->getResolutions();

		// Get messages
		$sm = new SupportMessage($this->database);
		$this->view->lists['messages'] = $sm->getMessages();

		// Get sections
		//$ss = new SupportSection($this->database);
		//$this->view->listslists['sections'] = $ss->getSections();

		// Get categories
		//$sa = new SupportCategory($this->database);
		//$this->view->listslists['categories'] = $sa->getCategories($row->section);

		// Get Tags
		$st = new SupportTags($this->database);
		$this->view->lists['tags'] = $st->get_tag_string($row->id, 0, 0, NULL, 0, 1);
		$this->view->lists['tagcloud'] = $st->get_tag_cloud(3, 1, $row->id);

		// Get severities
		$this->view->lists['severities'] = SupportUtilities::getSeverities($this->config->get('severities'));

		if (trim($row->group))
		{
			$this->view->lists['owner'] = $this->_userSelectGroup('owner', $row->owner, 1, '', trim($row->group));
		}
		elseif (trim($this->config->get('group')))
		{
			$this->view->lists['owner'] = $this->_userSelectGroup('owner', $row->owner, 1, '', trim($this->config->get('group')));
		}
		else
		{
			$this->view->lists['owner'] = $this->_userSelect('owner', $row->owner, 1);
		}

		$this->view->row = $row;
		$this->view->comments = $comments;

		if ($watch = JRequest::getWord('watch', ''))
		{
			$watch = strtolower($watch);

			$watching = new SupportTableWatching($this->database);
			$watching->load($this->view->row->id, $this->juser->get('id'));

			// Not already watching
			if (!$watching->id) 
			{
				// Start watching?
				if ($watch == 'start')
				{
					$watching->ticket_id = $this->view->row->id;
					$watching->user_id   = $this->juser->get('id');
					$watching->store();
				}
				// Otherwise, do nothing
			}
			else
			// Already watching
			{
				// Stop watching?
				if ($watch == 'stop')
				{
					$watching->delete();
				}
				// Otherwise, do nothing
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
		$id = JRequest::getInt('id', 0);

		$allowEmailResponses = $this->config->get('email_processing');

		if ($allowEmailResponses)
		{
			$encryptor = new Hubzero_EmailToken();
		}

		// Instantiate the tagging class - we'll need this a few times
		$st = new SupportTags($this->database);

		// Load the old ticket so we can compare for the changelog
		if ($id)
		{
			$old = new SupportTicket($this->database);
			$old->load($id);

			// Get Tags
			$oldtags = $st->get_tag_string($id, 0, 0, NULL, 0, 1);
		}

		// Trim and addslashes all posted items
		$_POST = array_map('trim', $_POST);

		// Initiate class and bind posted items to database fields
		$row = new SupportTicket($this->database);
		if (!$row->bind($_POST))
		{
			JError::raiseError(500, $row->getError());
			return;
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

		$row->load($id);

		// Save the tags
		$tags = JRequest::getVar('tags', '', 'post');

		$st->tag_object($this->juser->get('id'), $row->id, $tags, 0, true);

		// We must have a ticket ID before we can do anything else
		if ($id)
		{
			// Incoming comment
			$comment = JRequest::getVar('comment', '', 'post', 'none', 2);
			//$comment = Hubzero_Filter::cleanXss($comment);
			if ($comment)
			{
				// If a comment was posted to a closed ticket, re-open it.
				if ($old->open == 0 && $row->status == 0)
				{
					$row->open = 1;
					$row->status = 1;
					$row->resolved = '';
					$row->store();
				}
				// If a comment was posted by the ticket submitter to a "waiting user response" ticket, change status.
				$ccreated_by = JRequest::getVar('username', '');
				if ($row->status == 1 && $ccreated_by == $row->login)
				{
					$row->open = 1;
					$row->status = 1;
					$row->resolved = '';
					$row->store();
				}
			}

			// Compare fields to find out what has changed for this ticket and build a changelog
			$log = array(
				'changes'       => array(),
				'notifications' => array()
			);

			// Did the tags change?
			if ($tags != $oldtags)
			{
				$oldtags = (trim($oldtags) == '') ? JText::_('BLANK') : $oldtags;
				$log['changes'][] = array(
					'field'  => JText::_('TICKET_FIELD_TAGS'),
					'before' => $oldtags,
					'after'  => $tags
				);
			}
			// Did group change?
			if ($row->group != $old->group)
			{
				$log['changes'][] = array(
					'field'  => JText::_('TICKET_FIELD_GROUP'),
					'before' => $old->group,
					'after'  => $row->group
				);
			}
			// Did severity change?
			if ($row->severity != $old->severity)
			{
				$log['changes'][] = array(
					'field'  => JText::_('TICKET_FIELD_SEVERITY'),
					'before' => $old->severity,
					'after'  => $row->severity
				);
			}
			// Did owner change?
			if ($row->owner != $old->owner)
			{
				$log['changes'][] = array(
					'field'  => JText::_('TICKET_FIELD_OWNER'),
					'before' => $old->owner,
					'after'  => $row->owner
				);
			}
			// Did the resolution change?
			if ($row->resolved != $old->resolved)
			{
				$row->resolved = ($row->resolved) ? $row->resolved : '[unresolved]';
				$log['changes'][] = array(
					'field'  => JText::_('TICKET_FIELD_RESOLUTION'),
					'before' => $old->resolved,
					'after'  => $row->resolved
				);
			}
			// Did the status change?
			if ($row->status != $old->status)
			{
				$log['changes'][] = array(
					'field'  => JText::_('TICKET_FIELD_STATUS'),
					'before' => SupportHtml::getStatus($old->open, $old->status),
					'after'  => SupportHtml::getStatus($row->open, $row->status)
				);
			}

			$attachment = $this->uploadTask($row->id);
			$comment .= ($attachment) ? "\n\n".$attachment : '';

			// Create a new support comment object and populate it
			$rowc = new SupportComment($this->database);
			$rowc->ticket     = $id;
			$rowc->comment    = nl2br($comment);
			$rowc->comment    = str_replace('<br>', '<br />', $rowc->comment);
			$rowc->created    = date('Y-m-d H:i:s', time());
			$rowc->created_by = JRequest::getVar('username', '');
			$rowc->changelog  = json_encode($log);
			$rowc->access     = JRequest::getInt('access', 0);

			if ($rowc->check())
			{
				// If we're only recording a changelog, make it private
				if ($rowc->changelog && !$rowc->comment)
				{
					$rowc->access = 1;
				}
				// Save the data
				if (!$rowc->store())
				{
					JError::raiseError(500, $rowc->getError());
					return;
				}

				// Only do the following if a comment was posted or ticket was reassigned
				// otherwise, we're only recording a changelog
				if ($comment || $row->owner != $old->owner)
				{
					$juri =& JURI::getInstance();
					$jconfig =& JFactory::getConfig();

					$base = $juri->base();
					if (substr($base, -14) == 'administrator/')
					{
						$base = substr($base, 0, strlen($base)-14);
					}

					$webpath = $this->config->get('webpath');
					if (substr($webpath, 0, 1) == '/')
					{
						$webpath = substr($webpath, 1, strlen($webpath));
					}

					// Parse comments for attachments
					$attach = new SupportAttachment($this->database);
					$attach->webpath = $base . $webpath . DS . $id;
					$attach->uppath  = JPATH_ROOT . $this->config->get('webpath') . DS . $id;
					$attach->output  = 'email';

					// Build e-mail components
					$admin_email = $jconfig->getValue('config.mailfrom');

					$subject = ucfirst($this->_name) . ', Ticket #' . $row->id;

					$from = array();
					$from['name']  = $jconfig->getValue('config.sitename') . ' ' . ucfirst($this->_name);
					$from['email'] = $jconfig->getValue('config.mailfrom');

					$message = array();
					$message['plaintext']  = '----------------------------'."\r\n";
					$message['plaintext'] .= strtoupper(JText::_('TICKET')).': '.$row->id."\r\n";
					$message['plaintext'] .= strtoupper(JText::_('TICKET_DETAILS_SUMMARY')).': '.stripslashes($row->summary)."\r\n";
					$message['plaintext'] .= strtoupper(JText::_('TICKET_DETAILS_CREATED')).': '.$row->created."\r\n";
					$message['plaintext'] .= strtoupper(JText::_('TICKET_DETAILS_CREATED_BY')).': '.$row->name;
					$message['plaintext'] .= ($row->login) ? ' ('.$row->login.')'."\r\n" : "\r\n";
					$message['plaintext'] .= strtoupper(JText::_('TICKET_FIELD_STATUS')).': '.SupportHtml::getStatus($row->status)."\r\n";
					$message['plaintext'] .= '----------------------------'."\r\n\r\n";
					$message['plaintext'] .= JText::sprintf('TICKET_EMAIL_COMMENT_POSTED',$row->id).': '.$rowc->created_by."\r\n";
					$message['plaintext'] .= JText::_('TICKET_EMAIL_COMMENT_CREATED').': '.$rowc->created."\r\n\r\n";
					if ($row->owner != $old->owner)
					{
						if ($old->owner == '')
						{
							$message['plaintext'] .= JText::_('TICKET_FIELD_OWNER').' '.JText::_('TICKET_SET_TO').' "'.$row->owner.'"'."\r\n\r\n";
						}
						else
						{
							$message['plaintext'] .= JText::_('TICKET_FIELD_OWNER').' '.JText::_('TICKET_CHANGED_FROM').' "'.$old->owner.'" to "'.$row->owner.'"'."\r\n\r\n";
						}
					}
					$message['plaintext'] .= $attach->parse($comment) . "\r\n\r\n";

                    // Prepare message to allow email responses to be parsed and added to the ticket
					if ($allowEmailResponses)
					{
						$live_site = rtrim(JURI::base(),'/');
						
						$ticketURL = $live_site . JRoute::_('index.php?option=' . $this->option);
						
						$prependtext = "~!~!~!~!~!~!~!~!~!~!\r\n";
						$prependtext .= "You can reply to this message, just include your reply text above this area\r\n" ;
						$prependtext .= "Attachments (up to 2MB each) are permitted\r\n" ;
						$prependtext .= "Message from " . $live_site . " / Ticket #" . $row->id . "\r\n";

						$message['plaintext'] = $prependtext . "\r\n\r\n" . $message['plaintext'];
					}

					// Build the link to the ticket
					//   NOTE: We don't use JRoute as it will have no affect on the back-end
					//   and it would return only the script name and querystring (index.php?option=...)
					//   We need nice URLs that can be clicked.
					$sef = $this->_name . '/ticket/' . $row->id;
					$message['plaintext'] .= rtrim($base, DS) . DS . ltrim($sef, DS) . "\r\n";

					// Html email
					$from['multipart'] = md5(date('U'));

					//$rowc->comment   = $attach->parse($rowc->comment);
					$rowc->changelog = $log;

					$eview = new JView(array(
						'base_path' => JPATH_ROOT . DS . 'components' . DS . $this->_option,
						'name'      => 'emails',
						'layout'    => 'comment'
					));
					$eview->option     = $this->_option;
					$eview->controller = $this->_controller;
					$eview->comment    = $rowc;
					$eview->ticket     = $row;
					$eview->delimiter  = '~!~!~!~!~!~!~!~!~!~!';
					$eview->boundary   = $from['multipart'];
					$eview->attach     = $attach;

					$message['multipart'] = $eview->loadTemplate();
					$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

					// An array for all the addresses to be e-mailed
					$emails = array();
					$emaillog = array();

					// Send e-mail to admin?
					JPluginHelper::importPlugin('xmessage');
					$dispatcher =& JDispatcher::getInstance();

					// Send e-mail to ticket submitter?
					$email_submitter = JRequest::getInt('email_submitter', 0);
					if ($email_submitter == 1)
					{
						// Is the comment private? If so, we do NOT send e-mail to the 
						// submitter regardless of the above setting
						if ($rowc->access != 1)
						{
							jimport('joomla.user.helper');
							if (($zid = JUserHelper::getUserId($row->login)))
							{
								$zuser =& JUser::getInstance($row->login);
							}
							// Make sure there even IS an e-mail and it's valid
							if (is_object($zuser) && $zuser->get('id'))
							{
								$type = 'support_reply_submitted';
								if ($row->status == 1)
								{
									$element = $row->id;
									$description = 'index.php?option=' . $this->_option . '&task=ticket&id=' . $row->id;
								}
								else
								{
									$element = null;
									$description = '';
									if ($row->status == 2)
									{
										$type = 'support_close_submitted';
									}
								}

								// Only build tokens in if component is configured to allow email responses to tickets and ticket comments
								if ($allowEmailResponses)
								{
									// The reply-to address contains the token 
									$token = $encryptor->buildEmailToken(1, 1, $zuser->get('id'), $id);
									$from['replytoemail'] = 'htc-' . $token;
								}

								if (!$dispatcher->trigger('onSendMessage', array($type, $subject, $message, $from, array($zuser->get('id')), $this->_option)))
								{
									$this->setError(JText::_('Failed to message ticket submitter.'));
								}
								else
								{
									$log['notifications'][] = array(
										'role'    => JText::_('COMMENT_SEND_EMAIL_SUBMITTER'),
										'name'    => $row->name,
										'address' => $row->email
									);
								}
							}
							else if ($row->email && SupportUtilities::checkValidEmail($row->email))
							{
								if ($allowEmailResponses)
								{
									// Build a temporary token for this user, userid will not be valid, but the token will
									$token = $encryptor->buildEmailToken(1, 1, 1, $id);
									$emails[] = array($row->email, 'htc-' . $token);
								}
								else
								{
									$emails[] = $row->email;
								}
							}
						}
					}

					// Send e-mail to ticket owner?
					$email_owner = JRequest::getInt('email_owner', 0);
					if ($email_owner == 1)
					{
						if ($row->owner)
						{
							$juser =& JUser::getInstance($row->owner);

							// Only put tokens in if component is configured to allow email responses to tickets and ticket comments
							if ($allowEmailResponses)
							{
								// The reply-to address contains the token 
								$token = $encryptor->buildEmailToken(1, 1, $juser->get('id'), $id);
								$from['replytoemail'] = 'htc-' . $token;									
							}

							if (!$dispatcher->trigger('onSendMessage', array('support_reply_assigned', $subject, $message, $from, array($juser->get('id')), $this->_option)))
							{
								$this->setError(JText::_('Failed to message ticket owner.'));
							}
							else
							{
								$log['notifications'][] = array(
									'role'    => JText::_('COMMENT_SEND_EMAIL_OWNER'),
									'name'    => $juser->get('name'),
									'address' => $juser->get('email')
								);
							}
						}
					}

					// Add any CCs to the e-mail list
					$cc = JRequest::getVar('cc', '', 'post');
					if (trim($cc))
					{
						$cc = explode(',', $cc);
						$cc = array_map('trim', $cc);
						foreach ($cc as $acc)
						{
							// Check the format accepted [ID, username, Name (username), Name (email)]
							if (!is_numeric($acc) && strstr($acc, '('))
							{
								$acc = trim(preg_replace('/(.+?)\s+\((.+?)\)/i', '$2', $acc));
							}

							// Is this a username/ID or email address?
							if (!strstr($acc, '@'))
							{
								// Username or user ID - load the user
								$juser =& JUser::getInstance($acc);

								// Did we find an account?
								if (!is_object($juser))
								{
									// Move on - nothing else we can do here
									continue;
								}

								if ($allowEmailResponses)
								{
									// The reply-to address contains the token 
									$token = $encryptor->buildEmailToken(1, 1, -9999, $id);
									$from['replytoemail'] = 'htc-' . $token;
								}

								// Is this the same account as the submitter? If so, ignore
								if (strtolower($row->login) == strtolower($juser->get('username')) 
								  || strtolower($row->email) == strtolower($juser->get('email')))
								{
									continue;
								}

								// Send message
								if (!$dispatcher->trigger('onSendMessage', array('support_reply_assigned', $subject, $message, $from, array($juser->get('id')), $this->_option)))
								{
									$this->setError(JText::_('Failed to message user.'));
								}
								else 
								{
									// Add to log
									$log['notifications'][] = array(
										'role'    => JText::_('COMMENT_SEND_EMAIL_CC'),
										'name'    => $juser->get('name'),
										'address' => $juser->get('email')
									);
									$log['cc'][] = $juser->get('username');
								}
							}
							// Make sure it's a valid e-mail address
							elseif (SupportUtilities::checkValidEmail($acc))
							{
								// Is the comment private? If so, we do NOT send e-mail to submitter
								if ($rowc->access != 1 && strtolower($row->email) == strtolower($acc)) 
								{
									continue;
								}

								if ($allowEmailResponses)
								{
									// The reply-to address contains the token
									$token = $encryptor->buildEmailToken(1, 1, -9999, $id);
									$emails[] = array($acc, 'htc-' . $token);
								}
								else
								{
									$emails[] = $acc;
								}
								$log['cc'][] = $acc;
							}
						}
					}

					// Send an e-mail to each address
					foreach ($emails as $email)
					{
						
						if ($allowEmailResponses)
						{
							// In this case each item in email in an array, 1- To, 2:reply to address
							if (SupportUtilities::sendEmail($email[0], $subject, $message, $from, $email[1]))
							{
								if (strtolower($row->email) == $email[0])
								{
									$emaillog[] = '<li>'.JText::_('TICKET_EMAILED_SUBMITTER').' - '.$row->email.'</li>';
								}
								else 
								{
									$emaillog[] = '<li>' . JText::_('TICKET_EMAILED_CC') . ' - ' . $email[0] . '</li>';
								}
							}
						}
						else 
						{
							// email is just a plain 'ol string
							if (SupportUtilities::sendEmail($email, $subject, $message, $from))
							{
								if (strtolower($row->email) == $email)
								{
									$log['notifications'][] = array(
										'role'    => JText::_('COMMENT_SEND_EMAIL_SUBMITTER'),
										'name'    => $row->name,
										'address' => $row->email
									);
								}
								else 
								{
									$log['notifications'][] = array(
										'role'    => JText::_('COMMENT_SEND_EMAIL_CC'),
										'name'    => JText::_('[none]'),
										'address' => $email
									);
								}
							}
						}
					}

					// Were there any changes?
					if (count($log['notifications']) > 0) 
					{
						$rowc->changelog = json_encode($log);

						// Save the data
						if (!$rowc->store())
						{
							JError::raiseError(500, $rowc->getError());
							return;
						}
					}
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
				JText::sprintf('TICKET_SUCCESSFULLY_SAVED', $row->id)
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
				JText::_('SUPPORT_ERROR_SELECT_TICKET_TO_DELETE'),
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
			JText::sprintf('TICKET_SUCCESSFULLY_DELETED', count($ids))
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
		$group_id = 'g.id';
		$aro_id = 'aro.id';

		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$query = "SELECT a.id AS value, a.name AS text, g.title AS groupname"
			. "\n FROM #__users AS a"
			. "\n INNER JOIN #__user_usergroup_map AS gm ON gm.user_id = a.id"	// map aro to group
			. "\n INNER JOIN #__usergroups AS g ON " . $group_id . " = gm.group_id"
			. "\n WHERE a.block = '0' AND " . $group_id . "=8"
			. "\n ORDER BY ". $order;
		}
		else
		{
			$query = "SELECT a.id AS value, a.name AS text, g.name AS groupname"
			. "\n FROM #__users AS a"
			. "\n INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id"	// map user to aro
			. "\n INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = " . $aro_id . ""	// map aro to group
			. "\n INNER JOIN #__core_acl_aro_groups AS g ON " . $group_id . " = gm.group_id"
			. "\n WHERE a.block = '0' AND " . $group_id . "=25"
			. "\n ORDER BY ". $order;
		}

		$this->database->setQuery($query);
		if ($nouser)
		{
			$users[] = JHTML::_('select.option', '', 'No User', 'value', 'text');
			$users = array_merge($users, $this->database->loadObjectList());
		}
		else
		{
			$users = $this->database->loadObjectList();
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
			$users[] = JHTML::_('select.option', '', 'No User', 'value', 'text');
		}

		ximport('Hubzero_Group');

		if (strstr($group, ','))
		{
			$groups = explode(',', $group);
			if (is_array($groups))
			{
				foreach ($groups as $g)
				{
					$hzg = Hubzero_Group::getInstance(trim($g));

					if ($hzg->get('gidNumber'))
					{
						$members = $hzg->get('members');

						//$users[] = '<optgroup title="'.stripslashes($hzg->description).'">';
						$users[] = JHTML::_('select.optgroup', stripslashes($hzg->description));
						foreach ($members as $member)
						{
							$u =& JUser::getInstance($member);
							if (!is_object($u))
							{
								continue;
							}

							$m = new stdClass();
							$m->value = $u->get('username');
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
			$hzg = Hubzero_Group::getInstance($group);

			if ($hzg && $hzg->get('gidNumber'))
			{
				$members = $hzg->get('members');

				foreach ($members as $member)
				{
					$u =& JUser::getInstance($member);
					if (!is_object($u))
					{
						continue;
					}

					$m = new stdClass();
					$m->value = $u->get('username');
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
		// Get some needed libraries
		ximport('Hubzero_Content_Server');

		// Ensure we have a database object
		if (!$this->database)
		{
			JError::raiseError(500, JText::_('SUPPORT_DATABASE_NOT_FOUND'));
			return;
		}

		// Get the ID of the file requested
		$id = JRequest::getInt('id', 0);

		// Instantiate an attachment object
		$attach = new SupportAttachment($this->database);
		$attach->load($id);
		if (!$attach->filename)
		{
			JError::raiseError(404, JText::_('SUPPORT_FILE_NOT_FOUND'));
			return;
		}
		$file = $attach->filename;

		// Ensure we have a path
		if (empty($file))
		{
			JError::raiseError(404, JText::_('SUPPORT_FILE_NOT_FOUND'));
			return;
		}
		if (preg_match("/^\s*http[s]{0,1}:/i", $file))
		{
			JError::raiseError(404, JText::_('SUPPORT_BAD_FILE_PATH'));
			return;
		}
		if (preg_match("/^\s*[\/]{0,1}index.php\?/i", $file))
		{
			JError::raiseError(404, JText::_('SUPPORT_BAD_FILE_PATH'));
			return;
		}
		// Disallow windows drive letter
		if (preg_match("/^\s*[.]:/", $file))
		{
			JError::raiseError(404, JText::_('SUPPORT_BAD_FILE_PATH'));
			return;
		}
		// Disallow \
		if (strpos('\\', $file))
		{
			JError::raiseError(404, JText::_('SUPPORT_BAD_FILE_PATH'));
			return;
		}
		// Disallow ..
		if (strpos('..', $file))
		{
			JError::raiseError(404, JText::_('SUPPORT_BAD_FILE_PATH'));
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
			JError::raiseError(404, JText::_('SUPPORT_FILE_NOT_FOUND') . ' ' . $filename);
			return;
		}

		// Initiate a new content server and serve up the file
		$xserver = new Hubzero_Content_Server();
		$xserver->filename($filename);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve())
		{
			// Should only get here on error
			JError::raiseError(404, JText::_('SUPPORT_SERVER_ERROR'));
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
	public function uploadTask($listdir)
	{
		// Incoming
		$description = JRequest::getVar('description', '');

		if (!$listdir)
		{
			$this->setError(JText::_('COM_SUPPORT_NO_ID'));
			return '';
		}

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(JText::_('COM_SUPPORT_NO_FILE'));
			return '';
		}

		// Construct our file path
		$file_path = JPATH_ROOT . $this->config->get('webpath') . DS . $listdir;

		if (!is_dir($file_path))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($file_path, 0777))
			{
				$this->setError(JText::_('COM_SUPPORT_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return '';
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $file_path . DS . $file['name']))
		{
			$this->setError(JText::_('COM_SUPPORT_ERROR_UPLOADING'));
			return '';
		}
		else
		{
			// Scan for viruses
			$path = $file_path . DS . $file['name']; //JPATH_ROOT . DS . 'virustest';
			exec("clamscan -i --no-summary --block-encrypted $path", $output, $status);
			if ($status == 1)
			{
				if (JFile::delete($path)) 
				{
					$this->setError(JText::_('File rejected due to possible security risk.'));
					return '';
				}
			}

			// File was uploaded
			// Create database entry
			$description = htmlspecialchars($description);

			$row = new SupportAttachment($this->database);
			$row->bind(array(
				'id' => 0,
				'ticket' => $listdir,
				'filename' => $file['name'],
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
