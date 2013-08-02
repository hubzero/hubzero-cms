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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Blog controller class for entries
 */
class BlogControllerEntries extends Hubzero_Controller
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		$this->model = new BlogModel('site', 0);

		$this->_authorize();
		$this->_authorize('entry');

		$this->registerTask('comments.rss', 'comments');
		$this->registerTask('commentsrss', 'comments');

		$this->registerTask('feed.rss', 'feed');
		$this->registerTask('feedrss', 'feed');

		$this->registerTask('archive', 'display');

		parent::execute();
	}

	/**
	 * Method to set the document path
	 *
	 * @return	void
	 */
	protected function _buildPathway()
	{
		$pathway =& JFactory::getApplication()->getPathway();

		$title = ($this->config->get('title')) ? $this->config->get('title') : JText::_(strtoupper($this->_option));

		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				$title,
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task && $this->_task != 'display') 
		{
			if ($this->_task != 'entry' && $this->_task != 'savecomment' && $this->_task != 'deletecomment') 
			{
				$pathway->addItem(
					JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
					'index.php?option=' . $this->_option . '&task=' . $this->_task
				);
			}
			$year = JRequest::getInt('year', 0);
			if ($year) 
			{
				$pathway->addItem(
					$year,
					'index.php?option=' . $this->_option . '&year=' . $year
				);
			}
			$month = JRequest::getInt('month', 0);
			if ($month) 
			{
				$pathway->addItem(
					sprintf("%02d",$month),
					'index.php?option=' . $this->_option . '&year=' . $year . '&month=' . sprintf("%02d", $month)
				);
			}
			if (isset($this->view->row)) 
			{
				$pathway->addItem(
					stripslashes($this->view->row->get('title')),
					'index.php?option=' . $this->_option . '&year=' . $year . '&month=' . sprintf("%02d", $month) . '&alias=' . $this->view->row->get('alias')
				);
			}
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @return	void
	 */
	protected function _buildTitle()
	{
		$this->_title = ($this->config->get('title')) ? $this->config->get('title') : JText::_(strtoupper($this->_option));
		if ($this->_task && $this->_task != 'display') 
		{
			if ($this->_task != 'entry' && $this->_task != 'savecomment' && $this->_task != 'deletecomment') 
			{
				$this->_title .= ': ' . JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task));
			}
			$year = JRequest::getInt('year', 0);
			if ($year) 
			{
				$this->_title .= ': ' . $year;
			}
			$month = JRequest::getInt('month', 0);
			if ($month) 
			{
				$this->_title .= ': ' . sprintf("%02d", $month);
			}
			if (isset($this->view->row)) 
			{
				$this->_title .= ': ' . stripslashes($this->view->row->get('title'));
			}
		}
		$document =& JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Display a list of entries
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->config = $this->config;

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Filters for returning results
		$this->view->filters = array();
		$this->view->filters['limit']    = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$this->view->filters['start']    = JRequest::getInt('limitstart', 0);
		$this->view->filters['year']     = JRequest::getInt('year', 0);
		$this->view->filters['month']    = JRequest::getInt('month', 0);
		$this->view->filters['scope']    = 'site';
		$this->view->filters['group_id'] = 0;
		$this->view->filters['search']   = JRequest::getVar('search', '');
		$this->view->filters['authorized'] = false;

		$this->view->filters['state'] = 'public';
		if (!$this->juser->get('guest')) 
		{
			$this->view->filters['state'] = 'registered';

			if ($this->view->config->get('access-manage-component')) 
			{
				$this->view->filters['state'] = 'all';
				$this->view->filters['authorized'] = true;
			}
		}

		$this->view->model = $this->model;

		$this->view->year  = $this->view->filters['year'];
		$this->view->month = $this->view->filters['month'];

		$this->_buildTitle();
		$this->_buildPathway();
		$this->_getStyles();
		$this->_getScripts();

		$this->view->title = ($this->config->get('title')) ? $this->config->get('title') : JText::_(strtoupper($this->_option));

		// Get any errors for display
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output HTML
		$this->view->display();
	}

	/**
	 * Highlight a string in a body of text
	 * 
	 * @param      string $searchquery String to highlight
	 * @param      array  $results     Content o highlight string in
	 * @return     array 
	 */
	private function _highlight($searchquery, $results)
	{
		$toks = array($searchquery);

		$resultback = 60;
		$resultlen  = 300;

		// Loop through all results
		for ($i = 0, $n = count($results); $i < $n; $i++)
		{
			$row =& $results[$i];

			// Clean the text up a bit first
			$lowerrow = strtolower($row->content);

			// Find first occurrence of a search word
			$pos = 0;
			foreach ($toks as $tok)
			{
				$pos = strpos($lowerrow, $tok);
				if ($pos !== false) break;
			}

			if ($pos > $resultback) 
			{
				$row->content = substr($row->content, ($pos - $resultback), $resultlen);
			} 
			else 
			{
				$row->content = substr($row->content, 0, $resultlen);
			}

			// Highlight each word/phrase found
			foreach ($toks as $tok)
			{
				if (($tok == 'class') || ($tok == 'span') || ($tok == 'highlight')) 
				{
					continue;
				}
				$row->content = preg_replace('#' . $tok . '#i', "<span class=\"highlight\">\\0</span>", $row->content);
				$row->title = preg_replace('#' . $tok . '#i', "<span class=\"highlight\">\\0</span>", $row->title);
			}

			$row->content = trim($row->content) . ' &#8230;';
		}

		return $results;
	}

	/**
	 * Display an entry
	 * 
	 * @return     void
	 */
	public function entryTask()
	{
		$this->view->setLayout('entry');

		$this->view->config = $this->config;
		$this->view->model  = $this->model;

		$alias = JRequest::getVar('alias', '');

		if (!$alias) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller)
			);
			return;
		}

		$this->view->row = $this->model->entry($alias);

		if (!$this->view->row->exists()) 
		{
			JError::raiseError(404, JText::_('COM_BLOG_NOT_FOUND'));
			return;
		}

		// Check authorization
		if (($this->view->row->get('state') == 2 && $this->juser->get('guest')) 
		 || ($this->view->row->get('state') == 0 && !$this->view->config->get('access-manage-component'))) 
		{
			JError::raiseError(403, JText::_('COM_BLOG_NOT_AUTH'));
			return;
		}

		// Filters for returning results
		$this->view->filters = array(
			'limit'    => 10,
			'start'    => 0,
			'scope'    => 'site',
			'group_id' => 0
		);

		if ($this->juser->get('guest')) 
		{
			$this->view->filters['state'] = 'public';
		} 
		else 
		{
			if (!$this->view->config->get('access-manage-component')) 
			{
				$this->view->filters['state'] = 'registered';
			}
		}

		// Push some scripts to the template
		$this->_buildTitle();
		$this->_buildPathway();
		$this->_getStyles();
		$this->_getScripts('assets/js/' . $this->_name);

		$this->view->title = ($this->config->get('title')) 
							? $this->config->get('title') 
							: JText::_(strtoupper($this->_option));

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}

	/**
	 * Show a form for creating an entry
	 * 
	 * @return     void
	 */
	public function newTask()
	{
		return $this->editTask();
	}

	/**
	 * Show a form for editing an entry
	 * 
	 * @return     void
	 */
	public function editTask($row = null)
	{
		if ($this->juser->get('guest')) 
		{
			$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option . '&task=' . $this->_task, false, true), 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($rtrn)),
				JText::_('COM_BLOG_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		$this->view->setLayout('edit');

		if (is_object($row))
		{
			$this->view->entry = $row;
		}
		else 
		{
			$this->view->entry = $this->model->entry(JRequest::getInt('entry', 0));
		}

		if (!$this->view->entry->exists()) 
		{
			$this->view->entry->set('allow_comments', 1);
			$this->view->entry->set('state', 1);
			$this->view->entry->set('scope', 'site');
			$this->view->entry->set('created_by', $this->juser->get('id'));
		}

		// Push some scripts to the template
		$this->_buildTitle();
		$this->_buildPathway();
		$this->_getStyles();
		$this->_getScripts('assets/js/jquery-ui-timepicker-addon');
		$this->_getScripts('assets/js/' . $this->_name);

		$this->view->title = ($this->config->get('title')) 
							? $this->config->get('title') 
							: JText::_(strtoupper($this->_option));

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->display();
	}

	/**
	 * Save entry to database
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		if ($this->juser->get('guest')) 
		{
			$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option . '&task=' . $this->_task), 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($rtrn)),
				JText::_('COM_BLOG_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		$entry = JRequest::getVar('entry', array(), 'post', 'none', 2);

		$row = $this->model->entry(0);

		if (!$row->bind($entry)) 
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		// Store new content
		if (!$row->store(true)) 
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		// Process tags
		if (!$row->tag(JRequest::getVar('tags', ''))) 
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		$this->setRedirect(
			JRoute::_($row->link())
		);
	}

	/**
	 * Mark an entry as deleted
	 * 
	 * @return     void
	 */
	public function deleteTask()
	{
		if ($this->juser->get('guest')) 
		{
			$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option, false, true), 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($rtrn)),
				JText::_('COM_BLOG_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		if (!$this->config->get('access-delete-entry')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option),
				JText::_('COM_BLOG_NOT_AUTHORIZED'),
				'error'
			);
			return;
		}

		// Incoming
		$id = JRequest::getInt('entry', 0);
		if (!$id) 
		{
			return $this->displayTask();
		}

		$process    = JRequest::getVar('process', '');
		$confirmdel = JRequest::getVar('confirmdel', '');

		// Initiate a blog entry object
		$entry = $this->model->entry($id);

		// Did they confirm delete?
		if (!$process || !$confirmdel) 
		{
			if ($process && !$confirmdel) 
			{
				$this->setError(JText::_('COM_BLOG_ERROR_CONFIRM_DELETION'));
			}

			// Push some scripts to the template
			$this->_buildTitle();
			$this->_buildPathway();
			$this->_getStyles();
			$this->_getScripts();

			// Output HTML
			$this->view->title  = ($this->config->get('title')) ? $this->config->get('title') : JText::_(strtoupper($this->_option));
			$this->view->entry  = $entry;
			$this->view->config = $this->config;
			if ($this->getError()) 
			{
				foreach ($this->getErrors() as $error)
				{
					$this->view->setError($error);
				}
			}
			$this->view->display();
			return;
		}

		// Delete the entry itself
		$entry->set('state', -1);
		if (!$entry->store()) 
		{
			$this->setError($entry->getError());
		}

		// Return the topics list
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option)
		);
		return;
	}

	/**
	 * Generate an RSS feed of entries
	 * 
	 * @return     string RSS
	 */
	public function feedTask()
	{
		if (!$this->config->get('feeds_enabled')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
			return;
		}

		include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'document' . DS . 'feed' . DS . 'feed.php');

		// Set the mime encoding for the document
		$jdoc =& JFactory::getDocument();
		$jdoc->setMimeEncoding('application/rss+xml');

		// Start a new feed object
		$doc = new JDocumentFeed;
		$doc->link = JRoute::_('index.php?option=' . $this->_option);

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Incoming
		$filters = array(
			'limit'    => JRequest::getInt('limit', $jconfig->getValue('config.list_limit')),
			'start'    => JRequest::getInt('limitstart', 0),
			'year'     => JRequest::getInt('year', 0),
			'month'    => JRequest::getInt('month', 0),
			'scope'    => 'site',
			'group_id' => 0,
			'search'   => JRequest::getVar('search','')
		);

		if ($this->juser->get('guest')) 
		{
			$filters['state'] = 'public';
		} 
		else 
		{
			if (!$this->config->get('access-manage-component')) 
			{
				$filters['state'] = 'registered';
			}
		}

		// Build some basic RSS document information
		$doc->title  = $jconfig->getValue('config.sitename') . ' - ' . JText::_(strtoupper($this->_option));
		$doc->title .= ($filters['year'])  ? ': ' . $year : '';
		$doc->title .= ($filters['month']) ? ': ' . sprintf("%02d", $filters['month']) : '';

		$doc->description = JText::sprintf('COM_BLOG_RSS_DESCRIPTION', $jconfig->getValue('config.sitename'));
		$doc->copyright   = JText::sprintf('COM_BLOG_RSS_COPYRIGHT', date("Y"), $jconfig->getValue('config.sitename'));
		$doc->category    = JText::_('COM_BLOG_RSS_CATEGORY');

		// Get the records
		$rows = $this->model->entries('list', $filters);

		// Start outputing results if any found
		if ($rows->total() > 0) 
		{
			foreach ($rows as $row)
			{
				$item = new JFeedItem();

				// Strip html from feed item description text
				$item->description = $row->content('parsed');
				$item->description = html_entity_decode(Hubzero_View_Helper_Html::purifyText($item->description));
				if ($this->config->get('feed_entries') == 'partial') 
				{
					$item->description = Hubzero_View_Helper_Html::shortenText($item->description, 300, 0);
				}

				// Load individual item creator class
				$item->title       = html_entity_decode(strip_tags($row->get('title')));
				$item->link        = JRoute::_($row->link());
				$item->date        = date('r', strtotime($row->published()));
				$item->category    = '';
				$item->author      = $row->creator('name');

				// Loads item info into rss array
				$doc->addItem($item);
			}
		}

		// Output the feed
		echo $doc->render();
	}

	/**
	 * Save a comment
	 * 
	 * @return     void
	 */
	public function savecommentTask()
	{
		// Ensure the user is logged in
		if ($this->juser->get('guest')) 
		{
			$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option), 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($rtrn)),
				JText::_('COM_BLOG_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Incoming
		$comment = JRequest::getVar('comment', array(), 'post');

		// Instantiate a new comment object and pass it the data
		$row = new BlogModelComment($comment['id']);
		if (!$row->bind($comment)) 
		{
			$this->setError($row->getError());
			return $this->entryTask();
		}

		// Store new content
		if (!$row->store(true)) 
		{
			$this->setError($row->getError());
			return $this->entryTask();
		}

		/*
		if ($row->get('created_by') != $this->member->get('uidNumber)) 
		{
			$this->entry = new BlogEntry($this->database);
			$this->entry->load($row->entry_id);
			
			// Get the site configuration
			$jconfig =& JFactory::getConfig();

			// Build the "from" data for the e-mail
			$from = array();
			$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_('PLG_MEMBERS_BLOG');
			$from['email'] = $jconfig->getValue('config.mailfrom');

			$subject = JText::_('PLG_MEMBERS_BLOG_SUBJECT_COMMENT_POSTED');

			// Build the SEF referenced in the message
			$juri =& JURI::getInstance();
			$sef = JRoute::_('index.php?option='.$this->option.'&id='. $this->member->get('uidNumber').'&active=blog&task='.JHTML::_('date',$this->entry->publish_up, '%Y', 0).'/'.JHTML::_('date',$this->entry->publish_up, '%m', 0).'/'.$this->entry->alias.'#comments);
			if (substr($sef,0,1) == '/') {
				$sef = substr($sef,1,strlen($sef));
			}

			// Message
			$message  = "The following comment has been posted to your blog entry:\r\n\r\n";
			$message .= stripslashes($row->content)."\r\n\r\n";
			$message .= "To view all comments on the blog entry, go to:\r\n";
			$message .= $juri->base().$sef . "\r\n";

			// Send the message
			JPluginHelper::importPlugin('xmessage');
			$dispatcher =& JDispatcher::getInstance();
			if (!$dispatcher->trigger('onSendMessage', array('blog_comment', $subject, $message, $from, array($this->member->get('uidNumber')), $this->option))) {
				$this->setError(JText::_('PLG_MEMBERS_BLOG_ERROR_MSG_MEMBER_FAILED'));
			}
		}
		*/

		return $this->entryTask();
	}

	/**
	 * Delete a comment
	 * 
	 * @return     void
	 */
	public function deletecommentTask()
	{
		// Ensure the user is logged in
		if ($this->juser->get('guest')) 
		{
			$this->setError(JText::_('COM_BLOG_LOGIN_NOTICE'));
			return $this->entryTask();
		}

		// Incoming
		$id = JRequest::getInt('comment', 0);
		if (!$id) 
		{
			return $this->entryTask();
		}

		// Initiate a blog comment object
		$comment = new BlogComment($this->database);
		$comment->load($id);

		if ($this->juser->get('id') != $comment->created_by && !$this->config->get('access-manage-entry')) 
		{
			return $this->entryTask();
		}

		// Mark all comments as deleted
		$comment->setState($id, 2);

		// Delete the entry itself
		if (!$comment->store()) 
		{
			$this->setError($comment->getError());
		}

		// Return the topics list
		//return $this->entryTask();
		$year  = JRequest::getVar('year', '');
		$month = JRequest::getVar('month', '');
		$alias = JRequest::getVar('alias', '');
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&year=' . $year . '&month=' . $month . '&alias=' . $row->alias)
		);
	}

	/**
	 * Display an RSS feed of comments
	 * 
	 * @return     string RSS
	 */
	public function commentsTask()
	{
		if (!$this->config->get('feeds_enabled')) 
		{
			JError::raiseError(404, JText::_('Feed not found.'));
			return;
		}

		include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'document' . DS . 'feed' . DS . 'feed.php');

		// Set the mime encoding for the document
		$jdoc =& JFactory::getDocument();
		$jdoc->setMimeEncoding('application/rss+xml');

		// Start a new feed object
		$doc = new JDocumentFeed;
		$doc->link = JRoute::_('index.php?option=' . $this->_option);

		// Incoming
		$alias = JRequest::getVar('alias', '');
		if (!$alias) 
		{
			JError::raiseError(404, JText::_('Feed not found.'));
			return;
		}

		$this->entry = $this->model->entry($alias);

		if (!$this->entry->isAvailable()) 
		{
			JError::raiseError(404, JText::_('Feed not found.'));
			return;
		}

		$year  = JRequest::getInt('year', date("Y"));
		$month = JRequest::getInt('month', 0);

		// Build some basic RSS document information
		$jconfig =& JFactory::getConfig();
		$doc->title  = $jconfig->getValue('config.sitename') . ' - ' . JText::_(strtoupper($this->_option));
		$doc->title .= ($year) ? ': ' . $year : '';
		$doc->title .= ($month) ? ': ' . sprintf("%02d", $month) : '';
		$doc->title .= stripslashes($this->entry->get('title', ''));
		$doc->title .= ': '.JText::_('Comments');

		$doc->description = JText::sprintf('COM_BLOG_COMMENTS_RSS_DESCRIPTION', $jconfig->getValue('config.sitename'), stripslashes($this->entry->get('title')));
		$doc->copyright   = JText::sprintf('COM_BLOG_RSS_COPYRIGHT', date("Y"), $jconfig->getValue('config.sitename'));

		$rows = $this->entry->comments('list');

		// Start outputing results if any found
		if ($rows->total() <= 0) 
		{
			echo $doc->render();
			return;
		}

		$this->wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => 'blog',
			'pagename' => $this->entry->get('alias'),
			'pageid'   => 0,
			'filepath' => $this->config->get('uploadpath'),
			'domain'   => ''
		);
		ximport('Hubzero_Wiki_Parser');
		$this->p = Hubzero_Wiki_Parser::getInstance();

		foreach ($rows as $row)
		{
			$this->_comment($doc, $row);
		}

		// Output the feed
		echo $doc->render();
	}

	/**
	 * Recursive method to add comments to a flat RSS feed
	 *
	 * @param   object $doc JDocumentFeed
	 * @param   object $row BlogModelComment
	 * @return	void
	 */
	private function _comment(&$doc, $row)
	{
		// Load individual item creator class
		$item = new JFeedItem();
		$item->title = JText::sprintf('Comment #%s', $row->get('id')) . ' @ ' . $row->created('time') . ' on ' . $row->created('date');
		$item->link  = JRoute::_($this->entry->link()  . '#c' . $row->get('id'));

		if ($row->isReported()) 
		{
			$item->description = JText::_('COM_BLOG_COMMENT_REPORTED_AS_ABUSIVE');
		} 
		else 
		{
			$item->description = html_entity_decode(Hubzero_View_Helper_Html::purifyText($this->p->parse($row->get('content'), $this->wikiconfig)));
		}

		if ($row->get('anonymous'))
		{
			$item->author = JText::_('COM_BLOG_ANONYMOUS');
		}
		else
		{
			$item->author = $row->creator('name');
		}
		$item->date     = $row->created();
		$item->category = '';

		$doc->addItem($item);

		if ($row->replies()->total() > 0)
		{
			foreach ($row->replies() as $reply)
			{
				$this->_comment($doc, $reply);
			}
		}
	}

	/**
	 * Method to check admin access permission
	 *
	 * @return	boolean	True on success
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);

		if (!$this->juser->get('guest')) 
		{
			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$asset  = $this->_option;
				if ($assetId)
				{
					$asset .= ($assetType != 'component') ? '.' . $assetType : '';
					$asset .= ($assetId) ? '.' . $assetId : '';
				}

				$at = '';
				if ($assetType != 'component')
				{
					$at .= '.' . $assetType;
				}

				// Admin
				$this->config->set('access-admin-' . $assetType, $this->juser->authorise('core.admin', $asset));
				$this->config->set('access-manage-' . $assetType, $this->juser->authorise('core.manage', $asset));
				// Permissions
				$this->config->set('access-create-' . $assetType, $this->juser->authorise('core.create' . $at, $asset));
				$this->config->set('access-delete-' . $assetType, $this->juser->authorise('core.delete' . $at, $asset));
				$this->config->set('access-edit-' . $assetType, $this->juser->authorise('core.edit' . $at, $asset));
				$this->config->set('access-edit-state-' . $assetType, $this->juser->authorise('core.edit.state' . $at, $asset));
				$this->config->set('access-edit-own-' . $assetType, $this->juser->authorise('core.edit.own' . $at, $asset));
			}
			else 
			{
				if ($this->juser->authorize($this->_option, 'manage'))
				{
					$this->config->set('access-manage-' . $assetType, true);
					$this->config->set('access-admin-' . $assetType, true);
					$this->config->set('access-create-' . $assetType, true);
					$this->config->set('access-delete-' . $assetType, true);
					$this->config->set('access-edit-' . $assetType, true);
				}
			}
		}
	}
}
