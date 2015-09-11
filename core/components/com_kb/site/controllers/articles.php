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

namespace Components\Kb\Site\Controllers;

use Components\Kb\Models\Archive;
use Components\Kb\Models\Category;
use Components\Kb\Models\Article;
use Components\Kb\Models\Comment;
use Hubzero\Component\SiteController;
use Exception;
use Document;
use Pathway;
use Request;
use Config;
use Lang;
use User;

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

		Document::setTitle($this->_title);
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
			App::redirect(
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
				App::redirect(
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
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// Check for request forgeries
		Request::checkToken();

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

		App::redirect(
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

		include_once(PATH_CORE . DS . 'libraries' . DS . 'joomla' . DS . 'document' . DS . 'feed' . DS . 'feed.php');

		// Set the mime encoding for the document
		Document::setType('feed');

		// Start a new feed object
		Document::setLink(Route::url($article->link()));

		// Build some basic RSS document information
		$title  = Config::get('sitename') . ' - ' . Lang::txt(strtoupper($this->_option));
		$title .= ($article->get('title')) ? ': ' . stripslashes($article->get('title')) : '';
		$title .= ': ' . Lang::txt('COM_KB_COMMENTS');

		Document::setTitle($title);

		Document::instance()->description = Lang::txt('COM_KB_COMMENTS_RSS_DESCRIPTION', Config::get('sitename'), stripslashes($article->get('title')));
		Document::instance()->copyright   = Lang::txt('COM_KB_COMMENTS_RSS_COPYRIGHT', gmdate("Y"), Config::get('sitename'));

		// Start outputing results if any found
		$this->_feedItem($article->comments('list'));
	}

	/**
	 * Recursive function to append comments to a feed
	 *
	 * @param   object  $feed
	 * @param   object  $comments
	 * @return  object
	 */
	protected function _feedItem($comments)
	{
		foreach ($comments as $comment)
		{
			// Load individual item creator class
			$item = new \Hubzero\Document\Type\Feed\Item();

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
			Document::addItem($item);

			if ($comment->replies()->total())
			{
				$this->_feedItem($comment->replies());
			}
		}
	}
}

