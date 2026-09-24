<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Site\Controllers;

use Components\Wiki\Models\Book;
use Components\Wiki\Models\Page;
use Components\Wiki\Models\Comment;
use Hubzero\Component\SiteController;
use Exception;
use Document;
use Pathway;
use Request;
use User;
use Lang;
use Date;
use App;
use Route;
use Event;

/**
 * Wiki controller class for comments
 */
class Comments extends SiteController
{
	/**
	 * Book model
	 *
	 * @var  object
	 */
	public $book = null;

	/**
	 * Sub component?
	 *
	 * @var  bool
	 */
	public $sub = false;

	/**
	 * Constructor
	 *
	 * @param   array  $config  Optional configurations
	 * @return  void
	 */
	public function __construct($config=array())
	{
		$this->_base_path = dirname(__DIR__);

		if (isset($config['base_path']))
		{
			$this->_base_path = $config['base_path'];
		}

		if (!isset($config['scope']))
		{
			$config['scope'] = 'site';
		}

		if (!isset($config['scope_id']))
		{
			$config['scope_id'] = 0;
		}

		if (isset($config['sub']))
		{
			$this->sub = $config['sub'];
		}

		$this->book = new Book($config['scope'], $config['scope_id']);

		if ($config['scope'] != 'site')
		{
			Request::setVar('task', Request::getWord('action'));
		}

		parent::__construct($config);
	}

	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		/*if (!$this->book->pages('count'))
		{
			if ($result = $this->book->scribe($this->_option))
			{
				$this->setError($result);
			}

			App::get('config')->get('debug') || App::get('config')->get('profile') ? App::get('profiler')->mark('afterWikiSetup') : null;
		}*/

		$this->page = $this->book->page();

		if (in_array($this->page->getNamespace(), array('image', 'file')))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=media&scope=' . $this->page->get('scope') . '&pagename=' . $this->page->get('pagename') . '&task=download')
			);
		}

		if (!$this->page->exists())
		{
			App::abort(404, Lang::txt('COM_WIKI_WARNING_NOT_FOUND'));
		}

		// Same view check as the page itself
		if (!$this->page->access('view', 'page'))
		{
			App::abort(403, Lang::txt('COM_WIKI_WARNING_NOT_AUTH'));
		}

		if (!$this->page->config('comments', 1))
		{
			App::redirect(
				Route::url($this->page->link())
			);
		}

		if (is_null($this->sub))
		{
			$this->sub = ($this->page->get('scope') != 'site');
		}

		$this->registerTask('addcomment', 'edit');
		$this->registerTask('editcomment', 'edit');
		$this->registerTask('savecomment', 'save');
		$this->registerTask('removecomment', 'remove');
		$this->registerTask('reportcomment', 'report');

		parent::execute();
	}

	/**
	 * Display comments for a wiki page
	 *
	 * @param   object  $mycomment
	 * @return  void
	 */
	public function displayTask($mycomment = null)
	{
		// Viewing comments for a specific version?
		$version = Request::getInt('version', 0);

		if (!$mycomment && !User::isGuest())
		{
			$mycomment = Comment::blank();
			// No ID, so we're creating a new comment
			// In that case, we'll need to set some data...
			// version() is the relationship, not the row: its get('version')
			// is empty and the posted comment[version]='' 500s on strict SQL
			$revision = $this->page->version;

			$mycomment->set('page_id', $revision->get('page_id'));
			$mycomment->set('version', $revision->get('version'));
			$mycomment->set('parent', Request::getInt('parent', 0));
			$mycomment->set('created_by', User::get('id'));
		}

		// Set the page's <title> tag
		Document::setTitle(
			Lang::txt(strtoupper($this->_option)) . ': ' .
			$this->page->title . ': ' .
			Lang::txt(strtoupper($this->_option . '_' . $this->_task))
		);

		// Set the pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}

		$parents = array();

		if ($this->page->get('parent'))
		{
			$parents = $this->page->ancestors();

			foreach ($parents as $p)
			{
				Pathway::append(
					$p->get('title'),
					$p->link()
				);
			}
		}

		Pathway::append(
			$this->page->title,
			$this->page->link()
		);
		Pathway::append(
			Lang::txt(strtoupper($this->_option . '_' . $this->_task)),
			$this->page->link('comments')
		);

		// Output content
		$this->view
			->set('parents', $parents)
			->set('page', $this->page)
			->set('sub', $this->sub)
			->set('mycomment', $mycomment)
			->set('version', $version)
			->set('config', $this->config)
			->setErrors($this->getErrors())
			->setLayout('display')
			->display();
	}

	/**
	 * Create a comment
	 *
	 * @return  void
	 */
	public function newTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a comment
	 *
	 * @return  void
	 */
	public function editTask()
	{
		// Is the user logged in?
		// If not, then we need to stop everything else and display a login form
		if (User::isGuest())
		{
			$url = Request::getString('REQUEST_URI', '', 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url), false)
			);
		}

		// Retrieve a comment ID if we're editing
		$id = Request::getInt('comment', 0);

		// Add the comment object to our controller's registry
		// This is how comments() knows if it needs to display a form or not
		$mycomment = Comment::oneOrNew($id);

		// Reading is bound the same way saveTask's write is: oneOrNew() resolves
		// any row of #__wiki_comments, and this renders the body into the editor
		// plus created_by into a hidden field -- so without this, any logged-in
		// user could read any comment on the hub, including the author of one
		// marked anonymous.
		if (!$mycomment->isNew())
		{
			if ((int) $mycomment->get('page_id') !== (int) $this->page->get('id')
			 || ($mycomment->get('created_by') != User::get('id')
			  && !$this->page->access('manage')))
			{
				App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
			}
		}

		if (!$id)
		{
			// No ID, so we're creating a new comment
			// In that case, we'll need to set some data...
			// version() is the relationship, not the row: its get('version')
			// is empty and the posted comment[version]='' 500s on strict SQL
			$revision = $this->page->version;

			$mycomment->set('page_id', $revision->get('page_id'));
			$mycomment->set('version', $revision->get('version'));
			$mycomment->set('parent', Request::getInt('parent', 0));
			$mycomment->set('created_by', User::get('id'));
		}

		$this->displayTask($mycomment);
	}

	/**
	 * Save a comment
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$fields = Request::getArray('comment', array(), 'post');

		// Bind the form data to our object
		// The form always posts this, but a crafted request need not, and reading
		// a missing key is a warning this hub turns into a 500.
		$__cid   = isset($fields['id']) ? (int) $fields['id'] : 0;
		$comment = Comment::oneOrNew($__cid);

		// The comment has to belong to the page this request resolved.
		// $this->page comes from the request's scope/pagename, and
		// Comment::oneOrNew() resolves any row of #__wiki_comments -- so
		// without this, access('manage') on any one page admitted editing any
		// comment on the hub.
		if (!$comment->isNew()
		 && (int) $comment->get('page_id') !== (int) $this->page->get('id'))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// For an existing comment, require ownership or page-manage rights
		if (!$comment->isNew()
		 && $comment->get('created_by') != User::get('id')
		 && !$this->page->access('manage'))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// created_by and page_id decide whose comment this is and where it
		// lives, and BOTH are hidden inputs on the form
		// (views/comments/tmpl/display.php), so pin them on every path. Pinning
		// page_id only for an existing row left the create path able to plant a
		// comment on any page id the caller named -- including a private or
		// comments-disabled page -- because $this->page was never used for the
		// write.
		$__isNew  = $comment->isNew();

		// A new comment needs a logged-in user the page lets comment
		// (knol pages can turn comments off)
		if ($__isNew && (User::isGuest() || !$this->page->access('create', 'comment')))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$__owner  = $comment->get('created_by');
		$__page   = $comment->get('page_id');
		$__state  = $comment->get('state');
		$__parent = $comment->get('parent');

		$comment->set($fields);

		// comment[version] is a hidden field; an empty value is not an integer
		// on strict SQL (the column is NOT NULL int)
		$comment->set('version', (int) $comment->get('version', 0) ?: (int) $this->page->version->get('version'));

		$comment->set('created_by', $__isNew ? User::get('id') : $__owner);
		$comment->set('page_id',    $__isNew ? $this->page->get('id') : $__page);

		// state and parent are hidden inputs too. state is the one that matters:
		// it carries the reported-as-abusive flag, so leaving it bound let an
		// author clear the report on their own comment. parent decides where the
		// comment sits in the thread.
		if (!$__isNew)
		{
			$comment->set('state', $__state);
			$comment->set('parent', $__parent);
		}

		// Parse the wikitext and set some values
		$comment->set('chtml', null);
		$comment->set('chtml', $comment->content('parsed'));
		$comment->set('anonymous', ($comment->get('anonymous') ? 1 : 0));
		$comment->set('created', $comment->get('created', Date::toSql()));

		// Save the data
		if (!$comment->save())
		{
			$this->setError($comment->getError());
			return $this->displayTask($comment);
		}

		// Did they rate the page?
		// If so, update the page with the new average rating
		if ($comment->get('rating'))
		{
			$this->page->calculateRating();

			if (!$this->page->save())
			{
				$this->setError($this->page->getError());
			}
		}

		// Log activity
		$recipients = array(
			['wiki.site', 1],
			['user', $this->page->get('created_by')],
			['user', $comment->get('created_by')]
		);

		if ($comment->get('parent'))
		{
			$parent = Comment::oneOrFail($comment->get('parent'));
			$recipients[] = ['user', $parent->get('created_by')];
		}
		if ($this->page->get('scope') != 'site')
		{
			$recipients[]  = [$this->page->get('scope'), $this->page->get('scope_id')];
			$recipients[0] = ['wiki.' . $this->page->get('scope'), $this->page->get('scope_id')];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($__cid ? 'updated' : 'created'),
				'scope'       => 'wiki.comment',
				'scope_id'    => $this->page->get('id'),
				'anonymous'   => $comment->get('anonymous', 0),
				'description' => Lang::txt('COM_WIKI_ACTIVITY_COMMENT_' . ($__cid ? 'UPDATED' : 'CREATED'), $comment->get('id'), '<a href="' . Route::url($this->page->link('comments')) . '">' . htmlspecialchars((string) ($this->page->title), ENT_QUOTES, 'UTF-8') . '</a>'),
				'details'     => array(
					'title'    => $this->page->title,
					'url'      => Route::url($this->page->link('comments')),
					'name'     => $this->page->get('pagename'),
					'comment'  => $comment->get('id')
				)
			],
			'recipients' => $recipients
		]);

		// Redirect to Comments page
		App::redirect(
			Route::url($this->page->link('comments'))
		);
	}

	/**
	 * Remove a comment
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// The delete button is a form that posts a token
		Request::checkToken();

		$msg = null;
		$cls = 'message';

		// Make sure we have a comment to delete
		if ($id = Request::getInt('comment', 0))
		{
			// Make sure they're authorized to delete (must be an author)
			if ($this->page->access('delete', 'comment'))
			{
				$comment = Comment::oneOrFail($id);

				// ...on THIS page. As savecomment above: oneOrFail() resolves
				// any comment row, so delete rights on one page would otherwise
				// reach every comment on the hub.
				if ((int) $comment->get('page_id') !== (int) $this->page->get('id'))
				{
					App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
				}

				$comment->set('state', Comment::STATE_DELETED);
				if ($comment->save())
				{
					$msg = Lang::txt('COM_WIKI_COMMENT_DELETED');
				}

				// Log activity
				$recipients = array(
					['wiki.site', 1],
					['user', $this->page->get('created_by')],
					['user', $comment->get('created_by')]
				);
				if ($this->page->get('scope') != 'site')
				{
					$recipients[]  = [$this->page->get('scope'), $this->page->get('scope_id')];
					$recipients[0] = ['wiki.' . $this->page->get('scope'), $this->page->get('scope_id')];
				}

				Event::trigger('system.logActivity', [
					'activity' => [
						'action'      => 'deleted',
						'scope'       => 'wiki.comment',
						'scope_id'    => $this->page->get('id'),
						'description' => Lang::txt('COM_WIKI_ACTIVITY_COMMENT_DELETED', $comment->get('id'), '<a href="' . Route::url($this->page->link('comments')) . '">' . htmlspecialchars((string) ($this->page->title), ENT_QUOTES, 'UTF-8') . '</a>'),
						'details'     => array(
							'title'    => $this->page->title,
							'url'      => Route::url($this->page->link('comments')),
							'name'     => $this->page->get('pagename'),
							'comment'  => $comment->get('id')
						)
					],
					'recipients' => $recipients
				]);
			}
			else
			{
				$msg = Lang::txt('COM_WIKI_ERROR_NOTAUTH');
				$cls = 'error';
			}
		}

		// Redirect to Comments page
		App::redirect(
			Route::url($this->page->link('comments')),
			$msg,
			$cls
		);
	}
}
