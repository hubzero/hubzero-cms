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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Short description for 'SupportControllerTickets'
 * 
 * Long description (if any) ...
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
		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . $this->_name . '.css');

		// Get filters
		$this->view->filters = SupportUtilities::getFilters();

		$obj = new SupportTicket($this->database);

		// Record count
		$this->view->total = $obj->getTicketsCount($this->view->filters, true);

		// Fetch results
		$this->view->rows = $obj->getTickets($this->view->filters, true);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination($this->view->total, $this->view->filters['start'], $this->view->filters['limit']);

		// Set any errors
		if ($this->getError()) {
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new ticket
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->view->setLayout('edit');
		$this->editTask();
	}

	/**
	 * Displays a ticket and comments
	 *
	 * @return	void
	 */
	public function editTask()
	{
		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . $this->_name . '.css');
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
			$webpath = str_replace('/administrator/', '/', $juri->base() . $this->config->get('webpath') . DS . $id);
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
		$row->summary = html_entity_decode(stripslashes($row->summary), ENT_COMPAT, 'UTF-8');
		$row->summary = str_replace('&quote;','&quot;',$row->summary);
		$row->summary = htmlentities($row->summary, ENT_COMPAT, 'UTF-8');

		$row->report  = html_entity_decode(stripslashes($row->report), ENT_COMPAT, 'UTF-8');
		$row->report  = str_replace('&quote;','&quot;',$row->report);
		$row->report  = str_replace("<br />","",$row->report);
		$row->report  = htmlentities($row->report, ENT_COMPAT, 'UTF-8');
		$row->report  = nl2br($row->report);
		$row->report  = str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$row->report);
		$row->report  = str_replace("    ",'&nbsp;&nbsp;&nbsp;&nbsp;',$row->report);

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

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Saves changes to a ticket, adds a new comment/changelog,
	 * notifies any relevant parties
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getInt('id', 0);

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
		$_POST = array_map('trim',$_POST);

		// Initiate class and bind posted items to database fields
		$row = new SupportTicket($this->database);
		if (!$row->bind($_POST))
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		if (!$row->id && !trim($row->summary))
		{
			$row->summary = substr($row->report, 0, 70);
			if (strlen($row->summary) >=70)
			{
				$row->summary .= '...';
			}
		}
		if (!$row->id
		 && (!$row->created || $row->created == '0000-00-00 00:00:00'))
		{
			$row->created = date("Y-m-d H:i:s");
		}

		//$bits = explode(':',$row->category);
		//$row->category = end($bits);
		//$row->section = $bits[0];

		// Set the status of the ticket
		if ($row->resolved)
		{
			if ($row->resolved == 1)
			{
				// "waiting user response"
				$row->status = 1;
			}
			else
			{
				// If there's a resolution, close the ticket
				$row->status = 2;
			}
		}
		else
		{
			$row->status = 0;
		}

		// Set the status to just "open" if no owner and no resolution
		if (!$row->owner && !$row->resolved)
		{
			$row->status = 0;
		}

		// If status is "open" or "waiting", ensure the resolution is empty
		if ($row->status == 0 || $row->status == 1)
		{
			$row->resolved = '';
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
			$comment = JRequest::getVar('comment', '');
			$comment = Hubzero_Filter::cleanXss($comment);
			if ($comment)
			{
				// If a comment was posted to a closed ticket, re-open it.
				if ($old->status == 2 && $row->status == 2)
				{
					$row->status = 0;
					$row->resolved = '';
					$row->store();
				}
				// If a comment was posted by the ticket submitter to a "waiting user response" ticket, change status.
				$ccreated_by = JRequest::getVar('username', '');
				if ($row->status == 1 && $ccreated_by == $row->login)
				{
					$row->status = 0;
					$row->resolved = '';
					$row->store();
				}
			}

			// Compare fields to find out what has changed for this ticket
			// and build a changelog
			$changelog = array();

			/*if ($row->section != $old->section) 
			{
				$changelog[] = '<li><strong>'.JText::_('TICKET_FIELD_SECTION').'</strong> '.JText::_('TICKET_CHANGED_FROM').' <em>'.$old->section.'</em> to <em>'.$row->section.'</em></li>';
			}
			if ($row->category != $old->category) 
			{
				$changelog[] = '<li><strong>'.JText::_('TICKET_FIELD_CATEGORY').'</strong> '.JText::_('TICKET_CHANGED_FROM').' <em>'.$old->category.'</em> to <em>'.$row->category.'</em></li>';
			}*/
			// Did the tags change?
			if ($tags != $oldtags)
			{
				$oldtags = (trim($oldtags) == '') ? JText::_('BLANK') : $oldtags;
				$changelog[] = '<li><strong>' . JText::_('TICKET_FIELD_TAGS').'</strong> ' . JText::_('TICKET_CHANGED_FROM').' <em>'.$oldtags.'</em> to <em>'.$tags.'</em></li>';
			}
			// Did group change?
			if ($row->group != $old->group)
			{
				$changelog[] = '<li><strong>' . JText::_('TICKET_FIELD_GROUP').'</strong> ' . JText::_('TICKET_CHANGED_FROM').' <em>'.$old->group.'</em> to <em>'.$row->group.'</em></li>';
			}
			// Did severity change?
			if ($row->severity != $old->severity)
			{
				$changelog[] = '<li><strong>'.JText::_('TICKET_FIELD_SEVERITY').'</strong> '.JText::_('TICKET_CHANGED_FROM').' <em>'.$old->severity.'</em> to <em>'.$row->severity.'</em></li>';
			}
			// Did owner change?
			if ($row->owner != $old->owner)
			{
				if ($old->owner == '')
				{
					$changelog[] = '<li><strong>'.JText::_('TICKET_FIELD_OWNER').'</strong> '.JText::_('TICKET_SET_TO').' <em>'.$row->owner.'</em></li>';
				}
				else
				{
					$changelog[] = '<li><strong>'.JText::_('TICKET_FIELD_OWNER').'</strong> '.JText::_('TICKET_CHANGED_FROM').' <em>'.$old->owner.'</em> to <em>'.$row->owner.'</em></li>';
				}
			}
			// Did the resolution change?
			if ($row->resolved != $old->resolved)
			{
				if ($old->resolved == '')
				{
					$changelog[] = '<li><strong>'.JText::_('TICKET_FIELD_RESOLUTION').'</strong> '.JText::_('TICKET_SET_TO').' <em>'.$row->resolved.'</em></li>';
				}
				else
				{
					// This will happen if someone is reopening a closed ticket
					$row->resolved = ($row->resolved) ? $row->resolved : '[unresolved]';
					$changelog[] = '<li><strong>'.JText::_('TICKET_FIELD_RESOLUTION').'</strong> '.JText::_('TICKET_CHANGED_FROM').' <em>'.$old->resolved.'</em> to <em>'.$row->resolved.'</em></li>';
				}
			}
			// Did the status change?
			if ($row->status != $old->status)
			{
				$changelog[] = '<li><strong>'.JText::_('TICKET_FIELD_STATUS').'</strong> '.JText::_('TICKET_CHANGED_FROM').' <em>'.SupportHtml::getStatus($old->status).'</em> to <em>'.SupportHtml::getStatus($row->status).'</em></li>';
			}

			// Were there any changes?
			$log = implode("\n", $changelog);
			if ($log != '')
			{
				$log = '<ul class="changelog">' . "\n" . $log . '</ul>'."\n";
			}

			$attachment = $this->upload($row->id);
			$comment .= ($attachment) ? "\n\n".$attachment : '';

			// Create a new support comment object and populate it
			$rowc = new SupportComment($this->database);
			$rowc->ticket     = $id;
			$rowc->comment    = nl2br($comment);
			$rowc->comment    = str_replace('<br>', '<br />', $rowc->comment);
			$rowc->created    = date('Y-m-d H:i:s', time());
			$rowc->created_by = JRequest::getVar('username', '');
			$rowc->changelog  = $log;
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

					$subject = ucfirst($this->_name) . ', Ticket #' . $row->id . ' comment ' . md5($row->id);

					$from = array();
					$from['name']  = $jconfig->getValue('config.sitename') . ' ' . ucfirst($this->_name);
					$from['email'] = $jconfig->getValue('config.mailfrom');

					$message  = '----------------------------'."\r\n";
					$message .= strtoupper(JText::_('TICKET')).': '.$row->id."\r\n";
					$message .= strtoupper(JText::_('TICKET_DETAILS_SUMMARY')).': '.stripslashes($row->summary)."\r\n";
					$message .= strtoupper(JText::_('TICKET_DETAILS_CREATED')).': '.$row->created."\r\n";
					$message .= strtoupper(JText::_('TICKET_DETAILS_CREATED_BY')).': '.$row->name;
					$message .= ($row->login) ? ' ('.$row->login.')'."\r\n" : "\r\n";
					$message .= '----------------------------'."\r\n\r\n";
					$message .= JText::sprintf('TICKET_EMAIL_COMMENT_POSTED',$row->id).': '.$rowc->created_by."\r\n";
					$message .= JText::_('TICKET_EMAIL_COMMENT_CREATED').': '.$rowc->created."\r\n\r\n";
					if ($row->owner != $old->owner)
					{
						if ($old->owner == '')
						{
							$message .= JText::_('TICKET_FIELD_OWNER').' '.JText::_('TICKET_SET_TO').' "'.$row->owner.'"'."\r\n\r\n";
						}
						else
						{
							$message .= JText::_('TICKET_FIELD_OWNER').' '.JText::_('TICKET_CHANGED_FROM').' "'.$old->owner.'" to "'.$row->owner.'"'."\r\n\r\n";
						}
					}
					$message .= $attach->parse($comment) . "\r\n\r\n";

					// Build the link to the ticket
					//   NOTE: We don't use JRoute as it will have no affect on the back-end
					//   and it would return only the script name and querystring (index.php?option=...)
					//   We need nice URLs that can be clicked.
					$sef = $this->_name . '/ticket/' . $row->id;
					if (substr($sef, 0, 1) == '/')
					{
						$sef = substr($sef, 1, strlen($sef));
					}
					$message .= $base . $sef . "\r\n";

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
							$zuser =& JUser::getInstance($row->login);
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

								if (!$dispatcher->trigger('onSendMessage', array($type, $subject, $message, $from, array($zuser->get('id')), $this->_option)))
								{
									$this->setError(JText::_('Failed to message ticket submitter.'));
								}
								else
								{
									$emaillog[] = '<li>'.JText::_('TICKET_EMAILED_SUBMITTER').' - '.$row->email.'</li>';
								}
							}
							else if ($row->email && SupportUtilities::checkValidEmail($row->email))
							{
								$emails[] = $row->email;
								$emaillog[] = '<li>'.JText::_('TICKET_EMAILED_SUBMITTER').' - '.$row->email.'</li>';
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

							if (!$dispatcher->trigger('onSendMessage', array('support_reply_assigned', $subject, $message, $from, array($juser->get('id')), $this->_option)))
							{
								$this->setError(JText::_('Failed to message ticket owner.'));
							}
							else
							{
								$emaillog[] = '<li>'.JText::_('TICKET_EMAILED_OWNER').' - '.$juser->get('email').'</li>';
							}
						}
					}

					// Add any CCs to the e-mail list
					$cc = JRequest::getVar('cc', '');
					if (trim($cc))
					{
						$cc = explode(',', $cc);
						foreach ($cc as $acc)
						{
							$acc = trim($acc);

							// Is this a username or email address?
							if (!strstr($acc, '@'))
							{
								// Username or user ID - load the user
                                $acc = (is_string($acc)) ? strtolower($acc) : $acc;
                                $juser =& JUser::getInstance($acc);
								// Did we find an account?
								if (is_object($juser))
								{
									// Get the user's email address
									//$acc = $juser->get('email');
									//if (!XMessageHelper::sendMessage('support_reply_assigned', $subject, $message, $from, array($juser->get('id')))) {
									if (!$dispatcher->trigger('onSendMessage', array('support_reply_assigned', $subject, $message, $from, array($juser->get('id')), $this->_option)))
									{
										$this->setError(JText::_('Failed to message ticket owner.'));
									}
									$emaillog[] = '<li>'.JText::_('TICKET_EMAILED_CC').' - '.$acc.'</li>';
								}
								else
								{
									// Move on - nothing else we can do here
									continue;
								}
							// Make sure it's a valid e-mail address
							}
							elseif (SupportUtilities::checkValidEmail($acc))
							{
								$emails[] = $acc;
								$emaillog[] = '<li>' . JText::_('TICKET_EMAILED_CC') . ' - ' . $acc . '</li>';
							}
						}
					}

					// Send an e-mail to each address
					foreach ($emails as $email)
					{
						SupportUtilities::sendEmail($email, $subject, $message, $from);
					}

					// Were there any changes?
					$elog = implode("\n", $emaillog);
					if ($elog != '')
					{
						$rowc->changelog .= '<ul class="emaillog">' . "\n" . $elog . '</ul>' . "\n";

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

		$filters = JRequest::getVar('filters', '');
		$filters = str_replace('&amp;','&',$filters);

		// output messsage and redirect
		$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&' . $filters;
		$this->_message = JText::sprintf('TICKET_SUCCESSFULLY_SAVED', $row->id);
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
			$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
			$this->_message = JText::_('SUPPORT_ERROR_SELECT_TICKET_TO_DELETE');
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

			// Delete ticket
			$ticket = new SupportTicket($this->database);
			$ticket->delete($id);
		}

		// Output messsage and redirect
		$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
		$this->_message = JText::sprintf('TICKET_SUCCESSFULLY_DELETED', count($ids));
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
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
		$query = "SELECT a.username AS value, a.name AS text, g.name AS groupname"
			. "\n FROM #__users AS a"
			. "\n INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id"	// map user to aro
			. "\n INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id"	// map aro to group
			. "\n INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id"
			. "\n WHERE a.block = '0' AND g.id=25"
			. "\n ORDER BY ". $order;

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
		$basePath = $this->config->get('webpath');
		if ($basePath)
		{
			// Make sure the path doesn't end with a slash
			if (substr($basePath, -1) == DS)
			{
				$basePath = substr($basePath, 0, strlen($basePath) - 1);
			}
			// Ensure the path starts with a slash
			if (substr($basePath, 0, 1) != DS)
			{
				$basePath = DS . $basePath;
			}
		}
		$basePath .= DS . $attach->ticket;

		// Does the path start with a slash?
		if (substr($file, 0, 1) != DS)
		{
			$file = DS . $file;
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
