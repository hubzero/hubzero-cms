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
		$this->_authorize();
		$this->_authorize('entry');

		$this->dateFormat = '%d %b %Y';
		$this->timeFormat = '%I:%M %p';
		$this->yearFormat  = "%Y";
		$this->monthFormat = "%m";
		$this->dayFormat   = "%d";
		$this->tz = 0;
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$this->dateFormat = 'd M Y';
			$this->timeFormat = 'H:i p';
			$this->yearFormat  = "Y";
			$this->monthFormat = "m";
			$this->dayFormat   = "d";
			$this->tz = true;
		}

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
			if ($this->row) 
			{
				$pathway->addItem(
					stripslashes($this->row->title),
					'index.php?option=' . $this->_option . '&year=' . $year . '&month=' . sprintf("%02d", $month) . '&alias=' . $this->row->alias
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
			if ($this->row) 
			{
				$this->_title .= ': ' . stripslashes($this->row->title);
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

		$this->view->filters['state'] = 'public';
		if (!$this->juser->get('guest')) 
		{
			$this->view->filters['state'] = 'registered';

			if ($this->view->config->get('access-manage-component')) 
			{
				$this->view->filters['state'] = 'all';
			}
		}

		// Instantiate the BlogEntry object
		$be = new BlogEntry($this->database);

		// Get a record count
		$this->view->total = $be->getCount($this->view->filters);

		// Get the records
		$this->view->rows = $be->getRecords($this->view->filters);

		// Highlight search results
		/*if ($this->view->filters['search']) 
		{
			$this->view->rows = $this->_highlight($this->view->filters['search'], $this->view->rows);
		}*/

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		$this->view->firstentry = $be->getDateOfFirstEntry($this->view->filters);

		$this->view->year  = $this->view->filters['year'];
		$this->view->month = $this->view->filters['month'];

		$this->_buildTitle();
		$this->_buildPathway();
		$this->_getStyles();
		$this->_getScripts();

		$this->view->popular = $be->getPopularEntries($this->view->filters);
		$this->view->recent  = $be->getRecentEntries($this->view->filters);

		$this->view->title = ($this->config->get('title')) ? $this->config->get('title') : JText::_(strtoupper($this->_option));

		$this->view->dateFormat  = $this->dateFormat;
		$this->view->timeFormat  = $this->timeFormat;
		$this->view->yearFormat  = $this->yearFormat;
		$this->view->monthFormat = $this->monthFormat;
		$this->view->dayFormat   = $this->dayFormat;
		$this->view->tz          = $this->tz;

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

		$alias = JRequest::getVar('alias', '');

		if (!$alias) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller)
			);
			return;
		}

		$this->view->row = new BlogEntry($this->database);
		$this->view->row->loadAlias($alias, 'site');

		if (!$this->view->row->id) 
		{
			JError::raiseError(404, JText::_('COM_BLOG_NOT_FOUND'));
			return;
		}
		$this->row = $this->view->row;

		// Check authorization
		if (($this->view->row->state == 2 && $this->juser->get('guest')) 
		 || ($this->view->row->state == 0 && !$this->view->config->get('access-manage-component'))) 
		{
			JError::raiseError(403, JText::_('COM_BLOG_NOT_AUTH'));
			return;
		}

		if ($this->juser->get('id') != $this->view->row->created_by) 
		{
			$this->view->row->hit();
		}

		if ($this->view->row->content) 
		{
			$wikiconfig = array(
				'option'   => $this->_option,
				'scope'    => '',
				'pagename' => JHTML::_('date', $this->view->row->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date', $this->view->row->publish_up, $this->monthFormat, $this->tz) . '/' . $this->view->row->alias,
				'pageid'   => 0,
				'filepath' => $this->config->get('uploadpath', '/site/blog'),
				'domain'   => ''
			);
			ximport('Hubzero_Wiki_Parser');
			$p =& Hubzero_Wiki_Parser::getInstance();
			$this->view->row->content = $p->parse(stripslashes($this->view->row->content), $wikiconfig);
		}

		$bc = new BlogComment($this->database);
		$this->view->comments = $bc->getAllComments($this->view->row->id);

		$this->view->comment_total = 0;
		if ($this->view->comments) 
		{
			foreach ($this->view->comments as $com)
			{
				$this->view->comment_total++;
				if ($com->replies) 
				{
					foreach ($com->replies as $rep)
					{
						$this->view->comment_total++;
						if ($rep->replies) 
						{
							$this->view->comment_total = $this->view->comment_total + count($rep->replies);
						}
					}
				}
			}
		}

		$r = JRequest::getInt('reply', 0);
		$this->view->replyto = new BlogComment($this->database);
		$this->view->replyto->load($r);

		$bt = new BlogTags($this->database);
		$this->view->tags = $bt->get_tag_cloud(0,0,$this->view->row->id);

		// Filters for returning results
		$filters = array();
		$filters['limit']    = 10;
		$filters['start']    = 0;
		$filters['scope']    = 'site';
		$filters['group_id'] = 0;

		if ($this->juser->get('guest')) 
		{
			$filters['state'] = 'public';
		} 
		else 
		{
			if (!$this->view->config->get('access-manage-component')) 
			{
				$filters['state'] = 'registered';
			}
		}
		$this->view->popular    = $this->view->row->getPopularEntries($filters);
		$this->view->recent     = $this->view->row->getRecentEntries($filters);
		$this->view->firstentry = $this->view->row->getDateOfFirstEntry($filters);

		// Push some scripts to the template
		$this->_buildTitle();
		$this->_buildPathway();
		$this->_getStyles();
		$this->_getScripts();

		$this->view->title = ($this->config->get('title')) ? $this->config->get('title') : JText::_(strtoupper($this->_option));

		$this->view->dateFormat  = $this->dateFormat;
		$this->view->timeFormat  = $this->timeFormat;
		$this->view->yearFormat  = $this->yearFormat;
		$this->view->monthFormat = $this->monthFormat;
		$this->view->dayFormat   = $this->dayFormat;
		$this->view->tz          = $this->tz;

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
			$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option . '&task=' . $this->_task), 'server');
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
			$id = JRequest::getInt('entry', 0);

			$this->view->entry = new BlogEntry($this->database);
			$this->view->entry->load($id);
		}

		if (!$this->view->entry->id) 
		{
			$this->view->entry->allow_comments = 1;
			$this->view->entry->state = 1;
			$this->view->entry->scope = 'site';
			$this->view->entry->created_by = $this->juser->get('id');
		}

		$bt = new BlogTags($this->database);
		$this->view->tags = $bt->get_tag_string($this->view->entry->id);

		// Push some scripts to the template
		$this->_buildTitle();
		$this->_buildPathway();
		$this->_getStyles();
		$this->_getScripts();

		$this->view->title = ($this->config->get('title')) ? $this->config->get('title') : JText::_(strtoupper($this->_option));

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

		$row = new BlogEntry($this->database);
		if (!$row->bind($entry)) 
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		// Check content
		if (!$row->check()) 
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		// Process tags
		$bt = new BlogTags($this->database);
		$bt->tag_object(
			$this->juser->get('id'), 
			$row->id, 
			trim(JRequest::getVar('tags', '')), 
			1, 
			1
		);

		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&task=' . JHTML::_('date', $row->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date', $row->publish_up, $this->monthFormat, $this->tz) . '/' . $row->alias)
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
			$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option), 'server');
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
		$entry = new BlogEntry($this->database);
		$entry->load($id);

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
			$this->view->title = ($this->config->get('title')) ? $this->config->get('title') : JText::_(strtoupper($this->_option));
			$this->view->entry = $entry;
			$this->view->authorized = $this->_authorize();
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
		$entry->state = -1;
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
		$filters = array();
		$filters['limit'] = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$filters['start'] = JRequest::getInt('limitstart', 0);
		$filters['year'] = JRequest::getInt('year', 0);
		$filters['month'] = JRequest::getInt('month', 0);
		$filters['scope'] = 'site';
		$filters['group_id'] = 0;
		$filters['search'] = JRequest::getVar('search','');

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

		// Instantiate the BlogEntry object
		$be = new BlogEntry($this->database);

		// Get the records
		$rows = $be->getRecords($filters);

		// Build some basic RSS document information
		$jconfig =& JFactory::getConfig();
		$doc->title  = $jconfig->getValue('config.sitename') . ' - ' . JText::_(strtoupper($this->_option));
		$doc->title .= ($filters['year'])  ? ': ' . $year : '';
		$doc->title .= ($filters['month']) ? ': ' . sprintf("%02d", $filters['month']) : '';

		$doc->description = JText::sprintf('COM_BLOG_RSS_DESCRIPTION',$jconfig->getValue('config.sitename'));
		$doc->copyright   = JText::sprintf('COM_BLOG_RSS_COPYRIGHT', date("Y"), $jconfig->getValue('config.sitename'));
		$doc->category    = JText::_('COM_BLOG_RSS_CATEGORY');

		// Start outputing results if any found
		if (count($rows) > 0) 
		{
			$wikiconfig = array(
				'option'   => $this->_option,
				'scope'    => 'blog',
				'pagename' => '',
				'pageid'   => 0,
				'filepath' => $this->config->get('uploadpath'),
				'domain'   => ''
			);
			ximport('Hubzero_Wiki_Parser');
			$p =& Hubzero_Wiki_Parser::getInstance();

			foreach ($rows as $row)
			{
				// Prepare the title
				$title = strip_tags($row->title);
				$title = html_entity_decode($title);

				// URL link to article
				$link = JRoute::_('index.php?option=' . $this->_option . '&task=' . JHTML::_('date', $row->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date', $row->publish_up, $this->monthFormat, $this->tz) . '/' . $row->alias);

				$cuser =& JUser::getInstance($row->created_by);
				$author = $cuser->get('name');

				// Strip html from feed item description text
				$description = $p->parse(stripslashes($row->content), $wikiconfig);
				$description = html_entity_decode(Hubzero_View_Helper_Html::purifyText($description));
				if ($this->config->get('feed_entries') == 'partial') 
				{
					$description = Hubzero_View_Helper_Html::shortenText($description, 300, 0);
				}

				@$date = ($row->publish_up ? date('r', strtotime($row->publish_up)) : '');

				// Load individual item creator class
				$item = new JFeedItem();
				$item->title       = $title;
				$item->link        = $link;
				$item->description = $description;
				$item->date        = $date;
				$item->category    = '';
				$item->author      = $author;

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
		$row = new BlogComment($this->database);
		if (!$row->bind($comment)) 
		{
			$this->setError($row->getError());
			return $this->entryTask();
		}

		// Check content
		if (!$row->check()) 
		{
			$this->setError($row->getError());
			return $this->entryTask();
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->setError($row->getError());
			return $this->entryTask();
		}

		/*
		if ($row->created_by != $this->member->get('uidNumber)) 
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
	public function commentfeedTask()
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
		//$params =& $mainframe->getParams();
		$app =& JFactory::getApplication();
		$params =& $app->getParams();
		$doc->link = JRoute::_('index.php?option=' . $this->_option);

		// Incoming
		$alias = JRequest::getVar('alias', '');

		if (!$alias) 
		{
			JError::raiseError(404, JText::_('Feed not found.'));
			return;
		}

		$entry = new BlogEntry($this->database);
		$entry->loadAlias($alias, 'site');

		if (!$entry->id) 
		{
			JError::raiseError(404, JText::_('Feed not found.'));
			return;
		}

		$bc = new BlogComment($this->database);
		$rows = $bc->getAllComments($entry->id);

		$year = JRequest::getInt('year', date("Y"));
		$month = JRequest::getInt('month', 0);

		// Build some basic RSS document information
		$jconfig =& JFactory::getConfig();
		$doc->title  = $jconfig->getValue('config.sitename') . ' - ' . JText::_(strtoupper($this->_option));
		$doc->title .= ($year) ? ': ' . $year : '';
		$doc->title .= ($month) ? ': ' . sprintf("%02d",$month) : '';
		$doc->title .= ($entry->title) ? ': ' . stripslashes($entry->title) : '';
		$doc->title .= ': '.JText::_('Comments');

		$doc->description = JText::sprintf('COM_BLOG_COMMENTS_RSS_DESCRIPTION', $jconfig->getValue('config.sitename'), stripslashes($entry->title));
		$doc->copyright = JText::sprintf('COM_BLOG_RSS_COPYRIGHT', date("Y"), $jconfig->getValue('config.sitename'));
		//$doc->category = JText::_('COM_BLOG_RSS_CATEGORY');

		// Start outputing results if any found
		if (count($rows) <= 0) 
		{
			echo $doc->render();
			return;
		}

		$wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => 'blog',
			'pagename' => $entry->alias,
			'pageid'   => 0,
			'filepath' => $this->config->get('uploadpath'),
			'domain'   => ''
		);
		ximport('Hubzero_Wiki_Parser');
		$p =& Hubzero_Wiki_Parser::getInstance();

		foreach ($rows as $row)
		{
			// URL link to article
			$link = JRoute::_('index.php?option=' . $this->_option . '&task=' . JHTML::_('date', $entry->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date', $entry->publish_up, $this->monthFormat, $this->tz) . '/' . $entry->alias . '#c' . $row->id);

			$author = JText::_('COM_BLOG_ANONYMOUS');
			if (!$row->anonymous) 
			{
				$cuser =& JUser::getInstance($row->created_by);
				$author = stripslashes($cuser->get('name'));
			}

			// Prepare the title
			$title = JText::sprintf('Comment by %s', $author) . ' @ ' . JHTML::_('date', $row->created, $this->timeFormat, $this->tz) . ' on ' . JHTML::_('date', $row->created, $this->dateFormat, $this->tz);

			// Strip html from feed item description text
			if ($row->reports) 
			{
				$description = JText::_('COM_BLOG_COMMENT_REPORTED_AS_ABUSIVE');
			} 
			else 
			{
				$description = $p->parse(stripslashes($row->content), $wikiconfig);
			}
			$description = html_entity_decode(Hubzero_View_Helper_Html::purifyText($description));

			@$date = ($row->created ? date('r', strtotime($row->created)) : '');

			// Load individual item creator class
			$item = new JFeedItem();
			$item->title       = $title;
			$item->link        = $link;
			$item->description = $description;
			$item->date        = $date;
			$item->category    = '';
			$item->author      = $author;

			// Loads item info into rss array
			$doc->addItem($item);

			// Check for any replies
			if ($row->replies) 
			{
				foreach ($row->replies as $reply)
				{
					// URL link to article
					$link = JRoute::_('index.php?option=' . $this->_option . '&task=' . JHTML::_('date', $entry->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date',$entry->publish_up, $this->monthFormat, $this->tz) . '/' . $entry->alias . '#c' . $reply->id);

					$author = JText::_('COM_BLOG_ANONYMOUS');
					if (!$reply->anonymous) 
					{
						$cuser =& JUser::getInstance($reply->created_by);
						$author = $cuser->get('name');
					}

					// Prepare the title
					$title = JText::sprintf('Reply to comment #%s by %s', $row->id, $author) . ' @ ' . JHTML::_('date', $reply->created, $this->timeFormat, $this->tz) . ' on ' . JHTML::_('date', $reply->created, $this->dateFormat, $this->tz);

					// Strip html from feed item description text
					if ($reply->reports) 
					{
						$description = JText::_('COM_BLOG_COMMENT_REPORTED_AS_ABUSIVE');
					} 
					else 
					{
						$description = (is_object($p)) ? $p->parse(stripslashes($reply->content)) : nl2br(stripslashes($reply->content));
					}
					$description = html_entity_decode(Hubzero_View_Helper_Html::purifyText($description));

					@$date = ($reply->created ? date('r', strtotime($reply->created)) : '');

					// Load individual item creator class
					$item = new JFeedItem();
					$item->title       = $title;
					$item->link        = $link;
					$item->description = $description;
					$item->date        = $date;
					$item->category    = '';
					$item->author      = $author;

					// Loads item info into rss array
					$doc->addItem($item);

					if ($reply->replies) 
					{
						foreach ($reply->replies as $response)
						{
							// URL link to article
							$link = JRoute::_('index.php?option=' . $this->_option . '&task=' . JHTML::_('date', $entry->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date', $entry->publish_up, $this->monthFormat, $this->tz) . '/' . $entry->alias . '#c' . $response->id);

							$author = JText::_('COM_BLOG_ANONYMOUS');
							if (!$response->anonymous) 
							{
								$cuser =& JUser::getInstance($response->created_by);
								$author = $cuser->get('name');
							}

							// Prepare the title
							$title = JText::sprintf('Reply to comment #%s by %s', $reply->id, $author) . ' @ ' . JHTML::_('date', $response->created, $this->timeFormat, $this->tz) . ' on ' . JHTML::_('date', $response->created, $this->dateFormat, $this->tz);

							// Strip html from feed item description text
							if ($response->reports) 
							{
								$description = JText::_('COM_BLOG_COMMENT_REPORTED_AS_ABUSIVE');
							} 
							else 
							{
								$description = (is_object($p)) ? $p->parse(stripslashes($response->content)) : nl2br(stripslashes($response->content));
							}
							$description = html_entity_decode(Hubzero_View_Helper_Html::purifyText($description));

							@$date = ($response->created ? date('r', strtotime($response->created)) : '');

							// Load individual item creator class
							$item = new JFeedItem();
							$item->title       = $title;
							$item->link        = $link;
							$item->description = $description;
							$item->date        = $date;
							$item->category    = '';
							$item->author      = $author;

							// Loads item info into rss array
							$doc->addItem($item);
						}
					}
				}
			}
		}

		// Output the feed
		echo $doc->render();
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
