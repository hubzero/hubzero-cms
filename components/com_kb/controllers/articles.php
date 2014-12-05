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

/**
 * Knowledge Base controller
 */
class KbControllerArticles extends \Hubzero\Component\SiteController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		$this->archive = new KbModelArchive();

		parent::execute();
	}

	/**
	 * Displays an overview of categories and articles in the knowledge base
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->setLayout('display');

		// Set the pathway
		$this->_buildPathway(null, null, null);

		// Set the page title
		$this->_buildTitle(null, null, null);

		// Output HTML
		$this->view->database = $this->database;
		$this->view->title    = JText::_('COM_KB');
		$this->view->catid    = 0;
		$this->view->config   = $this->config;
		$this->view->archive  = $this->archive;

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
	 * Displays a list of articles for a given category
	 *
	 * @return     void
	 */
	public function categoryTask()
	{
		$this->view->setLayout('category');

		// Make sure we have an ID
		if (!($alias = JRequest::getVar('alias', '')))
		{
			$this->displayTask();
			return;
		}

		// Get the category
		$sect = -1;
		$cat  = -1;

		$this->view->category = new KbModelCategory($alias);
		$this->view->section  = new KbModelCategory($this->view->category->get('section'));
		if ($alias == 'all')
		{
			$this->view->category->set('alias', 'all');
			$this->view->category->set('title', JText::_('COM_KB_ALL_ARTICLES'));
			$this->view->category->set('id', 0);
			$this->view->category->set('state', 1);
		}
		else
		{
			if ($this->view->category->get('section'))
			{
				//$this->view->section  = new KbModelCategory($this->view->category->get('section'));

				$sect = $this->view->category->get('section');
				$cat  = $this->view->category->get('id');
			}
			else
			{
				$sect = $this->view->category->get('id');
			}
		}

		if (!$this->view->category->isPublished())
		{
			JError::raiseError(404, JText::_('COM_KB_ERROR_CATEGORY_NOT_FOUND'));
			return;
		}

		// Get configuration
		$jconfig = JFactory::getConfig();

		$this->view->filters = array(
			'limit'    => JRequest::getInt('limit', $jconfig->getValue('config.list_limit')),
			'start'    => JRequest::getInt('limitstart', 0),
			'sort'     => JRequest::getWord('sort', 'recent'),
			'section'  => $sect,
			'category' => $cat,
			'search'   => JRequest::getVar('search',''),
			'state'    => 1
		);
		if ($this->juser->get('guest'))
		{
			$this->view->filters['access'] = 0;
		}
		if (!in_array($this->view->filters['sort'], array('recent', 'popularity')))
		{
			$this->view->filters['sort'] = 'recent';
		}
		if (!$this->juser->get('guest'))
		{
			$this->view->filters['user_id'] = $this->juser->get('id');
		}

		// Get a record count
		$this->view->total    = $this->view->category->articles('count', $this->view->filters);

		// Get the records
		$this->view->articles = $this->view->category->articles('list', $this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Get all main categories for menu
		$this->view->categories = $this->archive->categories();

		// Set the pathway
		$this->_buildPathway($this->view->section, $this->view->category, null);

		// Set the page title
		$this->_buildTitle($this->view->section, $this->view->category, null);

		// Output HTML
		$this->view->title  = JText::_('COM_KB');
		$this->view->catid  = $sect;
		$this->view->config = $this->config;
		$this->view->juser  = $this->juser;
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
	 * Displays a knowledge base article
	 *
	 * @return     void
	 */
	public function articleTask()
	{
		$this->view->setLayout('article');

		// Incoming
		$alias = JRequest::getVar('alias', '');
		$id    = JRequest::getInt('id', 0);

		// Load the article
		$this->view->article = new KbModelArticle(($alias ? $alias : $id), JRequest::getVar('category'));

		if (!$this->view->article->exists())
		{
			JError::raiseError(404, JText::_('COM_KB_ERROR_ARTICLE_NOT_FOUND'));
			return;
		}

		if (!$this->view->article->isPublished())
		{
			JError::raiseError(404, JText::_('COM_KB_ERROR_ARTICLE_NOT_FOUND'));
			return;
		}

		// Is the user logged in?
		/*if (!$this->juser->get('guest'))
		{
			// See if this person has already voted
			$h = new KbTableVote($this->database);
			$this->view->vote = $h->getVote(
				$this->view->article->get('id'),
				$this->juser->get('id'),
				JRequest::ip(),
				'entry'
			);
		}
		else
		{*/
			$this->view->vote = strtolower(JRequest::getVar('vote', ''));
		//}

		// Load the category object
		$this->view->section = new KbModelCategory($this->view->article->get('section'));
		if (!$this->view->section->isPublished())
		{
			JError::raiseError(404, JText::_('COM_KB_ERROR_ARTICLE_NOT_FOUND'));
			return;
		}

		// Load the category object
		$this->view->category = $this->view->article->category();
		if ($this->view->category->exists() && !$this->view->category->isPublished())
		{
			JError::raiseError(404, JText::_('COM_KB_ERROR_ARTICLE_NOT_FOUND'));
			return;
		}

		// Get all main categories for menu
		$this->view->categories = $this->archive->categories('list');

		$this->view->subcategories = $this->view->section->children('list');

		$this->view->replyto = new KbModelComment(JRequest::getInt('reply', 0));

		// Set the pathway
		$this->_buildPathway($this->view->section, $this->view->category, $this->view->article);

		// Set the page title
		$this->_buildTitle($this->view->section, $this->view->category, $this->view->article);

		// Output HTML
		$this->view->title   = JText::_('COM_KB');
		$this->view->juser   = $this->juser;
		$this->view->helpful = $this->helpful;
		$this->view->catid   = $this->view->section->get('id');
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
	 * Pushes items to the global breadcrumbs object
	 *
	 * @param      object $section  KbTableCategory
	 * @param      object $category KbTableCategory
	 * @param      object $article  KbTableArticle
	 * @return     void
	 */
	protected function _buildPathway($section=null, $category=null, $article=null)
	{
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();

		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if (is_object($section) && $section->get('alias'))
		{
			$pathway->addItem(
				stripslashes($section->get('title')),
				'index.php?option=' . $this->_option . '&section=' . $section->get('alias')
			);
		}
		if (is_object($category) && $category->get('alias'))
		{
			$lnk  = 'index.php?option=' . $this->_option;
			$lnk .= (is_object($section) && $section->get('alias')) ? '&section=' . $section->get('alias') : '';
			$lnk .= '&category=' . $category->get('alias');

			$pathway->addItem(
				stripslashes($category->get('title')),
				$lnk
			);
		}
		if (is_object($article) && $article->get('alias'))
		{
			$lnk = 'index.php?option=' . $this->_option . '&section=' . $section->get('alias');
			if (is_object($category) && $category->get('alias'))
			{
				$lnk .= '&category=' . $category->get('alias');
			}
			$lnk .= '&alias=' . $article->get('alias');

			$pathway->addItem(
				stripslashes($article->get('title')),
				$lnk
			);
		}
	}

	/**
	 * Builds the document title
	 *
	 * @param      object $section  KbTableCategory
	 * @param      object $category KbTableCategory
	 * @param      object $article  KbTableArticle
	 * @return     void
	 */
	protected function _buildTitle($section=null, $category=null, $article=null)
	{
		$this->_title = JText::_(strtoupper($this->_option));
		if (is_object($section) && $section->get('title') != '')
		{
			$this->_title .= ': ' . stripslashes($section->get('title'));
		}
		if (is_object($category) && $category->get('title') != '')
		{
			$this->_title .= ': ' . stripslashes($category->get('title'));
		}
		if (is_object($article))
		{
			$this->_title .= ': ' . stripslashes($article->get('title'));
		}
		$document = JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Records the vote (like/dislike) of either an article or comment
	 * AJAX call - Displays updated vote links
	 * Standard link - falls through to the article view
	 *
	 * @return     void
	 */
	public function voteTask()
	{
		if ($this->juser->get('guest'))
		{
			$return = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option), 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// Incoming
		$type = strtolower(JRequest::getVar('type', ''));
		$vote = strtolower(JRequest::getVar('vote', ''));
		$id   = JRequest::getInt('id', 0);

		// Did they vote?
		if (!$vote)
		{
			// Already voted
			$this->setError(JText::_('COM_KB_USER_DIDNT_VOTE'));
			$this->articleTask();
			return;
		}

		if (!in_array($type, array('entry', 'comment')))
		{
			// Already voted
			$this->setError(JText::_('COM_KB_WRONG_VOTE_TYPE'));
			$this->displayTask();
			return;
		}

		// Load the article
		switch ($type)
		{
			case 'entry':
				$row = new KbModelArticle($id);
			break;
			case 'comment':
				$row = new KbModelComment($id);
			break;
		}

		if (!$row->vote($vote, $this->juser->get('id')))
		{
			$this->setError($row->getError());
		}

		if (JRequest::getInt('no_html', 0))
		{
			$this->view->setLayout('_vote');

			$this->view->item = $row;
			$this->view->type = $type;
			$this->view->vote = $vote;
			$this->view->id   = ''; //$id;
			if ($this->getError())
			{
				$this->view->setError($this->getError());
			}
			$this->view->display();
		}
		else
		{
			if ($type == 'entry')
			{
				//JRequest::setVar('alias', $row->get('alias'));
				$this->setRedirect(
					JRoute::_($row->link())
				);
				return;
			}
			$this->articleTask();
		}
	}

	/**
	 * Saves a comment to an article
	 * Displays article
	 *
	 * @return     void
	 */
	public function savecommentTask()
	{
		// Ensure the user is logged in
		if ($this->juser->get('guest'))
		{
			$return = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option), 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$comment = JRequest::getVar('comment', array(), 'post', 'none', 2);

		// Instantiate a new comment object and pass it the data
		$row = new KbModelComment($comment['id']);
		if (!$row->bind($comment))
		{
			$this->setError($row->getError());
			$this->articleTask();
			return;
		}

		// Store new content
		if (!$row->store(true))
		{
			$this->setError($row->getError());
			$this->articleTask();
			return;
		}

		$this->setRedirect(
			JRoute::_($row->link() . '#comments')
		);
	}

	/**
	 * Displays an RSS feed of comments for a given article
	 *
	 * @return  void
	 */
	public function commentsTask()
	{
		if (!$this->config->get('feeds_enabled'))
		{
			$this->_task = 'article';
			$this->articleTask();
			return;
		}

		// Incoming
		$alias = JRequest::getVar('alias', '');
		$id    = JRequest::getInt('id', 0);

		// Load the article
		$category = new KbModelCategory(JRequest::getVar('category'));

		$article = new KbModelArticle(($alias ? $alias : $id), $category->get('id'));
		if (!$article->exists())
		{
			throw new JException(JText::_('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
		}

		include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'document' . DS . 'feed' . DS . 'feed.php');

		// Set the mime encoding for the document
		$document = JFactory::getDocument();
		$document->setMimeEncoding('application/rss+xml');

		$jconfig = JFactory::getConfig();

		// Start a new feed object
		$feed = new JDocumentFeed;
		$feed->link = JRoute::_($article->link());

		// Build some basic RSS document information
		$feed->title  = $jconfig->getValue('config.sitename') . ' - ' . JText::_(strtoupper($this->_option));
		$feed->title .= ($article->get('title')) ? ': ' . stripslashes($article->get('title')) : '';
		$feed->title .= ': ' . JText::_('COM_KB_COMMENTS');

		$feed->description = JText::sprintf('COM_KB_COMMENTS_RSS_DESCRIPTION', $jconfig->getValue('config.sitename'), stripslashes($article->get('title')));
		$feed->copyright   = JText::sprintf('COM_KB_COMMENTS_RSS_COPYRIGHT', gmdate("Y"), $jconfig->getValue('config.sitename'));

		// Start outputing results if any found
		$feed = $this->_feedItem($feed, $article->comments('list'));

		// Output the feed
		echo $feed->render();
		die();
	}

	/**
	 * Recursive function to append comments to a feed
	 *
	 * @param   object  $feed
	 * @param   object  $comments
	 * @return  object
	 */
	protected function _feedItem($feed, $comments)
	{
		foreach ($comments as $comment)
		{
			// Load individual item creator class
			$item = new JFeedItem();

			$item->author = JText::_('COM_KB_ANONYMOUS');
			if (!$comment->get('anonymous'))
			{
				$item->author = $comment->creator('name', $item->author);
			}

			// Prepare the title
			$item->title = JText::sprintf('COM_KB_COMMENTS_RSS_COMMENT_TITLE', $item->author) . ' @ ' . $comment->created('time') . ' on ' . $comment->created('date');

			// URL link to article
			$item->link = $feed->link . '#c' . $comment->get('id');

			// Strip html from feed item description text
			if ($comment->isReported())
			{
				$item->description = JText::_('COM_KB_COMMENT_REPORTED_AS_ABUSIVE');
			}
			else
			{
				$item->description = html_entity_decode(\Hubzero\Utility\Sanitize::stripAll($comment->content('clean')));
			}

			$item->date = $comment->created();
			$item->category = '';

			// Loads item info into rss array
			$feed->addItem($item);

			if ($comment->replies()->total())
			{
				$feed = $this->_feedItem($feed, $comment->replies());
			}
		}

		return $feed;
	}
}

