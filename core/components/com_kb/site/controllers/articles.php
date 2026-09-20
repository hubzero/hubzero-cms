<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
use App;
use Route;

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
		$categoryAlias = Request::getString('categoryAlias', '');

		// Make sure we have an alias for the category
		if (!$categoryAlias)
		{
			return $this->displayTask();
		}

		// Get the category
		$this->view->catid = 0;

		if ($categoryAlias == 'all')
		{
			$category = new Category;
			$category->set('alias', 'all');
			$category->set('title', Lang::txt('COM_KB_ALL_ARTICLES'));
			$category->set('id', 0);
			$category->set('published', 1);
		}
		else
		{
			$category = Category::oneByAlias($categoryAlias);

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

		// The pager falls back to the offset it last saved in the session, and
		// that one saved offset is shared by every category. Without a start
		// in the URL, page 2 of a long category would open a short one past
		// its last article, so a link with no start means the first page.
		Request::setVar('start', Request::getInt('start', 0));

		// Get configuration
		$this->view->filters = array(
			'sort'     => Request::getWord('sort', 'recent'),
			'category' => $category->get('id'),
			'search'   => Request::getString('search', '')
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
		$articleAlias = Request::getString('articleAlias', '');
		$categoryId   = Request::getInt('categoryId', 0);

		// Load the article
		$article = Article::all()
			->whereEquals('alias', $articleAlias)
			->whereEquals('category', $categoryId)
			->row();

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
			$this->view->vote = strtolower(Request::getString('vote', ''));
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
			$return = Request::getString('REQUEST_URI', Route::url('index.php?option=' . $this->_option), 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		Request::checkToken(['get', 'post']);

		// Incoming
		$type = strtolower(Request::getString('type', ''));
		$vote = strtolower(Request::getString('vote', ''));
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
				$this->_requireReadableArticle($row);
			break;
			case 'comment':
				$row = Comment::oneOrFail($id);

				// A comment's visibility is its article's, plus its own state:
				// oneOrFail() resolves any row, so without this a vote could be
				// cast on a deleted comment, or on a comment belonging to an
				// article the caller cannot see -- and the difference between a
				// counted vote and a 404 tells them which hidden ids exist.
				if (!in_array((int) $row->get('state'), array(Comment::STATE_PUBLISHED, Comment::STATE_FLAGGED), true))
				{
					throw new Exception(Lang::txt('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
				}

				$this->_requireReadableArticle(Article::oneOrNew($row->get('entry_id')));
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
			$return = Request::getString('REQUEST_URI', Route::url('index.php?option=' . $this->_option), 'server');
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
		$comment = Request::getArray('comment', array(), 'post');

		// The form always posts this one, but a crafted request need not, and
		// reading a missing key is a warning this hub turns into a 500.
		$cid = isset($comment['id']) ? (int) $comment['id'] : 0;

		// Instantiate the comment object
		$row = Comment::oneOrNew($cid);

		// For an existing comment, require ownership
		if (!$row->isNew()
		 && $row->get('created_by') != User::get('id')
		 && !User::authorise('core.manage', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$isNew = $row->isNew();

		// Who wrote the comment, what it hangs off and whether it has been
		// reported are not the submitter's to set. set() copies every key of the
		// request array onto a real column of #__kb_comments, and the ownership
		// test above governs WHICH row is written, not WHAT is written to it --
		// so without pinning these, an edit of one's own comment could
		// re-attribute it to another member, move it to another article, or
		// clear a state of 3 (reported as abusive) back to published.
		$keep = array(
			'entry_id'   => $row->get('entry_id'),
			'parent'     => $row->get('parent'),
			'created'    => $row->get('created'),
			'created_by' => $row->get('created_by'),
			'state'      => $row->get('state')
		);

		$row->set($comment);

		if ($isNew)
		{
			$row->set('created', \Date::toSql());
			$row->set('created_by', User::get('id'));
			$row->set('state', Comment::STATE_PUBLISHED);

			// entry_id is a form field too, so confirm it names an article that
			// is actually readable before hanging a comment off it.
			$target = Article::oneOrNew((int) $row->get('entry_id'));

			if (!$target->get('id') || $target->get('state') != Article::STATE_PUBLISHED)
			{
				throw new Exception(Lang::txt('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
			}
		}
		else
		{
			foreach ($keep as $__k => $__v)
			{
				$row->set($__k, $__v);
			}
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
				'action'      => ($cid ? 'updated' : 'created'),
				'scope'       => 'kb.article.comment',
				'scope_id'    => $row->get('id'),
				'anonymous'   => $row->get('anonymous', 0),
				'description' => Lang::txt('COM_KB_ACTIVITY_COMMENT_' . ($cid ? 'UPDATED' : 'CREATED'), $row->get('id'), '<a href="' . Route::url($article->link() . '#c' . $row->get('id')) . '">' . htmlspecialchars((string) ($article->get('title')), ENT_QUOTES, 'UTF-8') . '</a>'),
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
		$alias = Request::getString('alias', '');
		$id    = Request::getInt('id', 0);

		// Load the article
		$article = ($alias ? Article::oneByAlias($alias) : Article::oneOrFail($id));
		if (!$article->get('id'))
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
		}

		// This feed carries the same comments the article page shows, so it must
		// not serve an article the page itself refuses.
		$this->_requireReadableArticle($article);

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

		// Start outputing results if any found.
		//
		// comments() hands back a relation, not rows: it is not Traversable, so
		// the foreach in _feedItem() iterated nothing and this feed has always
		// been empty whatever the article carried. Resolve the rows here, with
		// the same state filter the article view applies, and take only the
		// top-level ones -- the relation returns replies too, and _feedItem()
		// recurses into those itself.
		$comments = $article->comments()
			->whereEquals('parent', 0)
			->whereIn('state', array(Comment::STATE_PUBLISHED, Comment::STATE_FLAGGED))
			->rows();

		$this->_feedItem($comments, Route::url($article->link()));
	}

	/**
	 * Refuse an article the article page itself would refuse.
	 *
	 * articleTask() requires a published state and a published category before
	 * rendering, but the feed and the vote handler both resolved a row by id or
	 * alias and went straight on -- oneOrFail()/oneByAlias() apply no state
	 * filter. That let the comment bodies, author names and timestamps of an
	 * unpublished or trashed article be read by anyone who knew its alias, and
	 * let votes be cast on hidden rows.
	 *
	 * The state test is on STATE_PUBLISHED rather than on truthiness:
	 * articleTask()'s !state lets a trashed article (STATE_DELETED) through, and
	 * nothing links a feed or a vote control for one.
	 *
	 * @param   object  $article
	 * @return  void
	 */
	protected function _requireReadableArticle($article)
	{
		if (!$article->get('id') || $article->get('state') != Article::STATE_PUBLISHED)
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
		}

		// The category comes from the ARTICLE, never from the request: the
		// router's 'category' value was never checked against the article it
		// named.
		$category = Category::oneOrNew($article->get('category'));

		if (!$category->get('id') || !$category->get('published'))
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
		}
	}

	/**
	 * Recursive function to append comments to a feed
	 *
	 * @param   object  $comments  Resolved comment rows, not a relation
	 * @param   string  $link      Link to the article the comments hang off
	 * @return  void
	 */
	protected function _feedItem($comments, $link)
	{
		foreach ($comments as $comment)
		{
			// Load individual item creator class
			$item = new \Hubzero\Document\Type\Feed\Item();

			// creator() takes no arguments and hands back the relation, not the
			// name -- passing it a field and a fallback put a BelongsToOne object
			// into the title below, which Lang::txt() then tried to convert to a
			// string. The property resolves the related user.
			$item->author = Lang::txt('JANONYMOUS');
			if (!$comment->get('anonymous'))
			{
				$name = $comment->creator->get('name');

				if ($name)
				{
					$item->author = $name;
				}
			}

			// Prepare the title
			$item->title = Lang::txt('COM_KB_COMMENTS_RSS_COMMENT_TITLE', $item->author) . ' @ ' . $comment->created('time') . ' on ' . $comment->created('date');

			// URL link to article. This read $feed->link, and $feed has never
			// existed in this scope -- the article's link is the caller's to
			// supply, which is what commentsTask() sets on the document.
			$item->link = $link . '#c' . $comment->get('id');

			// Strip html from feed item description text
			if ($comment->isReported())
			{
				$item->description = Lang::txt('COM_KB_COMMENT_REPORTED_AS_ABUSIVE');
			}
			else
			{
				// Decode BEFORE stripping, not after: stripping first and then
				// decoding turns a comment whose text is "&lt;img onerror=...&gt;"
				// back into live markup in the feed, which a reader that renders
				// the description as HTML would run. This order strips whatever
				// the decode produces, and still un-escapes ordinary entities.
				$item->description = \Hubzero\Utility\Sanitize::stripAll(
					html_entity_decode($comment->content('clean'))
				);
			}

			$item->date = $comment->created();
			$item->category = '';

			// Loads item info into rss array
			Document::addItem($item);

			// replies() hands back a query builder, so this needs resolving too
			$replies = $comment->replies()
				->whereIn('state', array(Comment::STATE_PUBLISHED, Comment::STATE_FLAGGED))
				->rows();

			if ($replies->count())
			{
				$this->_feedItem($replies, $link);
			}
		}
	}
}
