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

namespace Components\Wiki\Site\Controllers;

use Components\Wiki\Models\Book;
use Components\Wiki\Models\Page;
use Components\Wiki\Models\Comment;
use Hubzero\Component\SiteController;
use Document;
use Pathway;
use Request;
use Event;
use User;
use Lang;
use Date;
use App;

/**
 * Wiki controller class for comments
 */
class Comments extends SiteController
{
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

		$this->_sub = false;
		if (isset($config['sub']))
		{
			$this->_sub = $config['sub'];
		}

		$this->_group = false;
		if (isset($config['group']))
		{
			$this->_group = $config['group'];
		}

		if ($this->_sub)
		{
			Request::setVar('task', Request::getWord('action'));
		}

		$this->book = new Book(($this->_group ? $this->_group : '__site__'));

		parent::__construct($config);
	}

	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		if (!$this->book->pages('count'))
		{
			if ($result = $this->book->scribe($this->_option))
			{
				$this->setError($result);
			}

			App::get('config')->get('debug') || App::get('config')->get('profile') ? App::get('profiler')->mark('afterWikiSetup') : null;
		}

		$this->page = $this->book->page();

		if (in_array($this->page->get('namespace'), array('image', 'file')))
		{
			App::redirect(
				'index.php?option=' . $this->_option . '&controller=media&scope=' . $this->page->get('scope') . '&pagename=' . $this->page->get('pagename') . '&task=download'
			);
			return;
		}

		if (!$this->page->exists())
		{
			App::abort(404, Lang::txt('COM_WIKI_WARNING_NOT_FOUND'));
		}

		$this->registerTask('addcomment', 'new');
		$this->registerTask('editcomment', 'edit');
		$this->registerTask('savecomment', 'save');
		$this->registerTask('removecomment', 'remove');
		$this->registerTask('reportcomment', 'report');

		parent::execute();
	}

	/**
	 * Display comments for a wiki page
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->view->page      = $this->page;
		$this->view->config    = $this->config;
		$this->view->base_path = $this->_base_path;
		$this->view->sub       = $this->_sub;

		// Viewing comments for a specific version?
		$this->view->v = Request::getInt('version', 0);

		if (!isset($this->view->mycomment) && !User::isGuest())
		{
			$this->view->mycomment = new Comment(0);
			// No ID, so we're creating a new comment
			// In that case, we'll need to set some data...
			$revision = $this->page->revision('current');

			$this->view->mycomment->set('pageid', $revision->get('pageid'));
			$this->view->mycomment->set('version', $revision->get('version'));
			$this->view->mycomment->set('parent', Request::getInt('parent', 0));
			$this->view->mycomment->set('created_by', User::get('id'));
		}

		// Prep the pagename for display
		// e.g. "MainPage" becomes "Main Page"
		$this->view->title = $this->page->get('title');

		// Set the page's <title> tag
		Document::setTitle(Lang::txt(strtoupper($this->_option)) . ': ' . $this->view->title . ': ' . Lang::txt(strtoupper($this->_option . '_' . $this->_task)));

		// Set the pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}

		$parents = array();
		if ($scope = $this->page->get('scope'))
		{
			$s = array();
			if ($cn = $this->page->get('group_cn'))
			{
				$scope = substr($scope, strlen($cn . '/wiki'));
				$s[] = $cn;
				$s[] = 'wiki';
			}
			$scope = trim($scope, '/');
			if ($scope)
			{
				$bits = explode('/', $scope);
				foreach ($bits as $bit)
				{
					$bit = trim($bit);
					if ($bit != '/' && $bit != '')
					{
						$p = Page::getInstance($bit, implode('/', $s));
						if ($p->exists())
						{
							Pathway::append(
								$p->get('title'),
								$p->link()
							);
							$parents[] = $p;
						}
						$s[] = $bit;
					}
				}
			}
		}

		Pathway::append(
			$this->view->title,
			$this->page->link()
		);
		Pathway::append(
			Lang::txt(strtoupper($this->_option . '_' . $this->_task)),
			$this->page->link('comments')
		);

		// Output content
		$this->view->message = $this->_message;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view
			->set('parents', $parents)
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
			$url = Request::getVar('REQUEST_URI', '', 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url))
			);
			return;
		}

		// Retrieve a comment ID if we're editing
		$id = Request::getInt('comment', 0);

		// Add the comment object to our controller's registry
		// This is how comments() knows if it needs to display a form or not
		$this->view->mycomment = new Comment($id);

		if (!$id)
		{
			// No ID, so we're creating a new comment
			// In that case, we'll need to set some data...
			$revision = $this->page->revision('current');

			$this->view->mycomment->set('pageid', $revision->get('pageid'));
			$this->view->mycomment->set('version', $revision->get('version'));
			$this->view->mycomment->set('parent', Request::getInt('parent', 0));
			$this->view->mycomment->set('created_by', User::get('id'));
		}

		$this->displayTask();
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

		$fields = Request::getVar('comment', array(), 'post');

		// Bind the form data to our object
		$comment = new Comment($fields['id']);
		if (!$comment->bind($fields))
		{
			$this->setError($comment->getError());
			return $this->displayTask();
		}

		// Parse the wikitext and set some values
		$comment->set('chtml', NULL);
		$comment->set('chtml', $comment->content('parsed'));
		$comment->set('anonymous', ($comment->get('anonymous') ? 1 : 0));
		$comment->set('created', ($comment->get('created') ? $comment->get('created') : Date::toSql()));

		// Save the data
		if (!$comment->store(true))
		{
			$this->setError($comment->getError());
			return $this->displayTask();
		}

		// Did they rate the page?
		// If so, update the page with the new average rating
		if ($comment->get('rating'))
		{
			$this->page->calculateRating();
			if (!$this->page->store())
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
			$parent = new Comment($comment->get('parent'));
			$recipients[] = ['user', $parent->get('created_by')];
		}
		if ($this->page->get('group_cn'))
		{
			$group = \Hubzero\User\Group::getInstance($this->page->get('group_cn'));
			$recipients[]  = ['group', $group->get('gidNumber')];
			$recipients[0] = ['wiki.group', $group->get('gidNumber')];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($fields['id'] ? 'updated' : 'created'),
				'scope'       => 'wiki.comment',
				'scope_id'    => $this->page->get('id'),
				'description' => Lang::txt('COM_WIKI_ACTIVITY_COMMENT_' . ($fields['id'] ? 'UPDATED' : 'CREATED'), $comment->get('id'), '<a href="' . Route::url($this->page->link('comments')) . '">' . $this->page->get('title') . '</a>'),
				'details'     => array(
					'title'    => $this->page->get('title'),
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
		$msg = null;
		$cls = 'message';

		// Make sure we have a comment to delete
		if ($id = Request::getInt('comment', 0))
		{
			// Make sure they're authorized to delete (must be an author)
			if ($this->page->access('delete', 'comment'))
			{
				$comment = new Comment($id);
				$comment->set('status', 2);
				if ($comment->store(false))
				{
					$msg = Lang::txt('COM_WIKI_COMMENT_DELETED');
				}

				// Log activity
				$recipients = array(
					['wiki.site', 1],
					['user', $this->page->get('created_by')],
					['user', $comment->get('created_by')]
				);
				if ($this->page->get('group_cn'))
				{
					$group = \Hubzero\User\Group::getInstance($this->page->get('group_cn'));
					$recipients[]  = ['group', $group->get('gidNumber')];
					$recipients[0] = ['wiki.group', $group->get('gidNumber')];
				}

				Event::trigger('system.logActivity', [
					'activity' => [
						'action'      => 'deleted',
						'scope'       => 'wiki.comment',
						'scope_id'    => $this->page->get('id'),
						'description' => Lang::txt('COM_WIKI_ACTIVITY_COMMENT_DELETED', $comment->get('id'), '<a href="' . Route::url($this->page->link('comments')) . '">' . $this->page->get('title') . '</a>'),
						'details'     => array(
							'title'    => $this->page->get('title'),
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
