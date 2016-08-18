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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Kb\Site\Controllers;

use Components\Kb\Models\Archive;
use Components\Kb\Models\Category;
use Components\Kb\Models\Article;
use Components\Kb\Models\Comment;
use Components\Kb\Models\Vote;
use Hubzero\Component\SiteController;
use Exception;
use Document;
use Pathway;
use Request;
use Config;
use Event;
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
	 * @return  void
	 */
	public function displayTask()
	{
		$this->view
			->set('archive', $this->archive)
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
		$alias = Request::getVar('alias', '');
		$id    = Request::getInt('id', 0);

		// Make sure we have an ID
		if (!$alias && !$id)
		{
			return $this->displayTask();
		}

		// Get the category
		$this->view->catid = 0;

		if ($alias == 'all')
		{
			$category = new Category;
			$category->set('alias', 'all');
			$category->set('title', Lang::txt('COM_KB_ALL_ARTICLES'));
			$category->set('id', 0);
			$category->set('published', 1);
		}
		else
		{
			$category = ($alias ? Category::oneByAlias($alias) : Category::oneOrFail($id));

			$this->view->catid = $category->get('id');
			if ($category->get('parent_id') > 1)
			{
				$this->view->catid = $category->get('parent_id');
			}
		}

		if (!$category->get('published'))
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_CATEGORY_NOT_FOUND'), 404);
		}

		// Get configuration
		$this->view->filters = array(
			'sort'     => Request::getWord('sort', 'recent'),
			'category' => $category->get('id'),
			'search'   => Request::getVar('search','')
		);

		if (!in_array($this->view->filters['sort'], array('recent', 'popularity')))
		{
			$this->view->filters['sort'] = 'recent';
		}
		if (!User::isGuest())
		{
			$this->view->filters['user_id'] = User::get('id');
		}

		$this->view->archive = $this->archive;
		$this->view->category = $category;

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
		$article = ($alias ? Article::oneByAlias($alias) : Article::oneOrFail($id));

		if (!$article->get('id'))
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
		}

		if (!$article->get('state'))
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
		}

		// Is the user logged in?
		if (!User::isGuest())
		{
			// See if this person has already voted
			$vote = Vote::blank()->find(
				$article->get('id'),
				User::get('id'),
				Request::ip(),
				'article'
			);
			$this->view->vote = $vote->get('vote');
		}
		else
		{
			$this->view->vote = strtolower(Request::getVar('vote', ''));
		}

		// Load the category object
		$category = Category::oneOrFail($article->get('category'));

		if (!$category->get('published'))
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
		}

		$this->view->catid = $category->get('id');
		if ($category->get('parent_id') > 1)
		{
			$this->view->catid = $category->get('parent_id');
		}

		$this->view
			->set('article', $article)
			->set('category', $category)
			->set('archive', $this->archive)
			->setLayout('article')
			->display();
	}

	/**
	 * Records the vote (like/dislike) of either an article or comment
	 * AJAX call - Displays updated vote links
	 * Standard link - falls through to the article view
	 *
	 * @return  void
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
			$this->setError(Lang::txt('COM_KB_USER_DIDNT_VOTE'));
			return $this->articleTask();
		}

		if (!in_array($type, array('article', 'comment')))
		{
			App::abort(404, Lang::txt('COM_KB_WRONG_VOTE_TYPE'));
		}

		// Load the article
		switch ($type)
		{
			case 'article':
				$row = Article::oneOrFail($id);
			break;
			case 'comment':
				$row = Comment::oneOrFail($id);
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
			if ($type == 'article')
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
	 * @return  void
	 */
	public function savecommentTask()
	{
		// Ensure the user is logged in
		if (User::isGuest())
		{
			$return = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option), 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return)),
				Lang::txt('COM_KB_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$comment = Request::getVar('comment', array(), 'post', 'none', 2);

		// Instantiate a new comment object and pass it the data
		$row = Comment::oneOrNew($comment['id'])->set($comment);
		if ($row->isNew())
		{
			$row->set('created', \Date::toSql());
		}

		// Store new content
		if (!$row->save())
		{
			$this->setError($row->getError());
			return $this->articleTask();
		}

		// Log the activity
		$article = Article::oneOrFail($row->get('entry_id'));

		$recipients = array($row->get('created_by'));
		if ($row->get('parent'))
		{
			$recipients[] = $row->parent()->get('created_by');
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($comment['id'] ? 'updated' : 'created'),
				'scope'       => 'kb.article.comment',
				'scope_id'    => $row->get('id'),
				'anonymous'   => $row->get('anonymous', 0),
				'description' => Lang::txt('COM_KB_ACTIVITY_COMMENT_' . ($comment['id'] ? 'UPDATED' : 'CREATED'), $row->get('id'), '<a href="' . Route::url($article->link() . '#c' . $row->get('id')) . '">' . $article->get('title') . '</a>'),
				'details'     => array(
					'title'    => $article->get('title'),
					'entry_id' => $article->get('id'),
					'url'      => $article->link()
				)
			],
			'recipients' => $recipients
		]);

		App::redirect(
			Route::url($article->link() . '#comments')
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
			throw new Exception(Lang::txt('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
		}

		// Incoming
		$alias = Request::getVar('alias', '');
		$id    = Request::getInt('id', 0);

		// Load the article
		$category = Category::oneByAlias(Request::getVar('category'));

		$article = ($alias ? Article::oneByAlias($alias) : Article::oneOrFail($id));
		if (!$article->get('id'))
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
		}

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
	 * @param   object  $comments
	 * @return  void
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

