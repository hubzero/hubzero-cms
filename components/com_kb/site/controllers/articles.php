<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Kb\Site\Controllers;

use Components\Kb\Models\Archive;
use Components\Kb\Models\Category;
use Components\Kb\Models\Article;
use Components\Kb\Models\Comment;
use Hubzero\Component\SiteController;
use Exception;

/**
 * Knowledge Base controller
 */
class Articles extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->archive = new Archive();

		parent::execute();
	}

	/**
	 * Displays an overview of categories and articles in the knowledge base
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Set the pathway
		$this->_buildPathway(null, null, null);

		// Set the page title
		$this->_buildTitle(null, null, null);

		// Output HTML
		$this->view->database = $this->database;
		$this->view->title    = Lang::txt('COM_KB');
		$this->view->catid    = 0;
		$this->view->config   = $this->config;
		$this->view->archive  = $this->archive;

		$this->view
			->setLayout('display')
			->display();
	}

	/**
	 * Displays a list of articles for a given category
	 *
	 * @return  void
	 */
	public function categoryTask()
	{
		// Make sure we have an ID
		if (!($alias = Request::getVar('alias', '')))
		{
			$this->displayTask();
			return;
		}

		// Get the category
		$sect = -1;
		$cat  = -1;

		$this->view->category = new Category($alias);
		$this->view->section  = new Category($this->view->category->get('section'));
		if ($alias == 'all')
		{
			$this->view->category->set('alias', 'all');
			$this->view->category->set('title', Lang::txt('COM_KB_ALL_ARTICLES'));
			$this->view->category->set('id', 0);
			$this->view->category->set('state', 1);
		}
		else
		{
			if ($this->view->category->get('section'))
			{
				//$this->view->section  = new Category($this->view->category->get('section'));

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
			throw new Exception(Lang::txt('COM_KB_ERROR_CATEGORY_NOT_FOUND'), 404);
		}

		// Get configuration
		$this->view->filters = array(
			'limit'    => Request::getInt('limit', Config::get('list_limit')),
			'start'    => Request::getInt('limitstart', 0),
			'sort'     => Request::getWord('sort', 'recent'),
			'section'  => $sect,
			'category' => $cat,
			'search'   => Request::getVar('search',''),
			'state'    => 1,
			'access'   => User::getAuthorisedViewLevels()
		);

		if (!in_array($this->view->filters['sort'], array('recent', 'popularity')))
		{
			$this->view->filters['sort'] = 'recent';
		}
		if (!User::isGuest())
		{
			$this->view->filters['user_id'] = User::get('id');
		}

		// Get a record count
		$this->view->total    = $this->view->category->articles('count', $this->view->filters);

		// Get the records
		$this->view->articles = $this->view->category->articles('list', $this->view->filters);

		// Get all main categories for menu
		$this->view->categories = $this->archive->categories();

		// Set the pathway
		$this->_buildPathway($this->view->section, $this->view->category, null);

		// Set the page title
		$this->_buildTitle($this->view->section, $this->view->category, null);

		// Output HTML
		$this->view->title  = Lang::txt('COM_KB');
		$this->view->catid  = $sect;
		$this->view->config = $this->config;
		$this->view->juser  = User::getRoot();

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view
			->setLayout('category')
			->display();
	}

	/**
	 * Displays a knowledge base article
	 *
	 * @return  void
	 */
	public function articleTask()
	{
		// Incoming
		$alias = Request::getVar('alias', '');
		$id    = Request::getInt('id', 0);

		// Load the article
		$this->view->article = new Article(($alias ? $alias : $id), Request::getVar('category'));

		if (!$this->view->article->exists())
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
		}

		if (!$this->view->article->isPublished())
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
		}

		// Is the user logged in?
		/*if (!User::isGurst())
		{
			// See if this person has already voted
			$h = new Vote($this->database);
			$this->view->vote = $h->getVote(
				$this->view->article->get('id'),
				User::get('id'),
				Request::ip(),
				'entry'
			);
		}
		else
		{*/
			$this->view->vote = strtolower(Request::getVar('vote', ''));
		//}

		// Load the category object
		$this->view->section = new Category($this->view->article->get('section'));
		if (!$this->view->section->isPublished())
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
		}

		// Load the category object
		$this->view->category = $this->view->article->category();
		if ($this->view->category->exists() && !$this->view->category->isPublished())
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
		}

		// Get all main categories for menu
		$this->view->categories = $this->archive->categories('list');

		$this->view->subcategories = $this->view->section->children('list');

		$this->view->replyto = new Comment(Request::getInt('reply', 0));

		// Set the pathway
		$this->_buildPathway($this->view->section, $this->view->category, $this->view->article);

		// Set the page title
		$this->_buildTitle($this->view->section, $this->view->category, $this->view->article);

		// Output HTML
		$this->view->title   = Lang::txt('COM_KB');
		$this->view->juser   = User::getRoot();
		$this->view->helpful = $this->helpful;
		$this->view->catid   = $this->view->section->get('id');

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view
			->setLayout('article')
			->display();
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
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if (is_object($section) && $section->get('alias'))
		{
			Pathway::append(
				stripslashes($section->get('title')),
				'index.php?option=' . $this->_option . '&section=' . $section->get('alias')
			);
		}
		if (is_object($category) && $category->get('alias'))
		{
			$lnk  = 'index.php?option=' . $this->_option;
			$lnk .= (is_object($section) && $section->get('alias')) ? '&section=' . $section->get('alias') : '';
			$lnk .= '&category=' . $category->get('alias');

			Pathway::append(
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

			Pathway::append(
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
		$this->_title = Lang::txt(strtoupper($this->_option));
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
		$document = \JFactory::getDocument();
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
		if (User::isGuest())
		{
			$return = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option), 'server');
			$this->setRedirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// Incoming
		$type = strtolower(Request::getVar('type', ''));
		$vote = strtolower(Request::getVar('vote', ''));
		$id   = Request::getInt('id', 0);

		// Did they vote?
		if (!$vote)
		{
			// Already voted
			$this->setError(Lang::txt('COM_KB_USER_DIDNT_VOTE'));
			$this->articleTask();
			return;
		}

		if (!in_array($type, array('entry', 'comment')))
		{
			// Already voted
			$this->setError(Lang::txt('COM_KB_WRONG_VOTE_TYPE'));
			$this->displayTask();
			return;
		}

		// Load the article
		switch ($type)
		{
			case 'entry':
				$row = new Article($id);
			break;
			case 'comment':
				$row = new Comment($id);
			break;
		}

		if (!$row->vote($vote, User::get('id')))
		{
			$this->setError($row->getError());
		}

		if (Request::getInt('no_html', 0))
		{
			$this->view->item = $row;
			$this->view->type = $type;
			$this->view->vote = $vote;
			$this->view->id   = ''; //$id;
			if ($this->getError())
			{
				$this->view->setError($this->getError());
			}
			$this->view->setLayout('_vote')->display();
		}
		else
		{
			if ($type == 'entry')
			{
				$this->setRedirect(
					Route::url($row->link())
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
		if (User::isGuest())
		{
			$return = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option), 'server');
			$this->setRedirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$comment = Request::getVar('comment', array(), 'post', 'none', 2);

		// Instantiate a new comment object and pass it the data
		$row = new Comment($comment['id']);
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
			Route::url($row->link() . '#comments')
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
		$alias = Request::getVar('alias', '');
		$id    = Request::getInt('id', 0);

		// Load the article
		$category = new Category(Request::getVar('category'));

		$article = new Article(($alias ? $alias : $id), $category->get('id'));
		if (!$article->exists())
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
		}

		include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'document' . DS . 'feed' . DS . 'feed.php');

		// Set the mime encoding for the document
		$document = \JFactory::getDocument();
		$document->setMimeEncoding('application/rss+xml');

		// Start a new feed object
		$feed = new \JDocumentFeed;
		$feed->link = Route::url($article->link());

		// Build some basic RSS document information
		$feed->title  = Config::get('sitename') . ' - ' . Lang::txt(strtoupper($this->_option));
		$feed->title .= ($article->get('title')) ? ': ' . stripslashes($article->get('title')) : '';
		$feed->title .= ': ' . Lang::txt('COM_KB_COMMENTS');

		$feed->description = Lang::txt('COM_KB_COMMENTS_RSS_DESCRIPTION', Config::get('sitename'), stripslashes($article->get('title')));
		$feed->copyright   = Lang::txt('COM_KB_COMMENTS_RSS_COPYRIGHT', gmdate("Y"), Config::get('sitename'));

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
			$item = new \JFeedItem();

			$item->author = Lang::txt('COM_KB_ANONYMOUS');
			if (!$comment->get('anonymous'))
			{
				$item->author = $comment->creator('name', $item->author);
			}

			// Prepare the title
			$item->title = Lang::txt('COM_KB_COMMENTS_RSS_COMMENT_TITLE', $item->author) . ' @ ' . $comment->created('time') . ' on ' . $comment->created('date');

			// URL link to article
			$item->link = $feed->link . '#c' . $comment->get('id');

			// Strip html from feed item description text
			if ($comment->isReported())
			{
				$item->description = Lang::txt('COM_KB_COMMENT_REPORTED_AS_ABUSIVE');
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

