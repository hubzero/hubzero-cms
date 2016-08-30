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

namespace Components\Forum\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Forum\Models\Manager;
use Components\Forum\Models\Section;
use Components\Forum\Models\Category;
use Components\Forum\Models\Post;
use Components\Forum\Models\Attachment;
use Components\Forum\Models\Tags;
use Components\Forum\Admin\Models\AdminThread;
use Filesystem;
use Exception;
use Request;
use Notify;
use Route;
use Lang;
use User;
use App;

/**
 * Controller class for forum threads
 */
class Threads extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->banking = \Component::params('com_members')->get('bankAccounts');

		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');

		parent::execute();
	}

	/**
	 * Display all threads in a category
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Filters
		$filters = array(
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				'-1',
				'int'
			),
			'access' => Request::getState(
				$this->_option . '.' . $this->_controller . '.access',
				'access',
				'-1',
				'int'
			),
			'section_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.section_id',
				'section_id',
				-1,
				'int'
			),
			'category_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.category_id',
				'category_id',
				-1,
				'int'
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'id'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			),
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
			'scopeinfo' => Request::getState(
				$this->_option . '.' . $this->_controller . '.scopeinfo',
				'scopeinfo',
				''
			)
		);
		if (strstr($filters['scopeinfo'], ':'))
		{
			$bits = explode(':', $filters['scopeinfo']);
			$filters['scope']    = $bits[0];
			$filters['scope_id'] = intval(end($bits));
		}
		else
		{
			$filters['scope']    = '';
			$filters['scope_id'] = -1;
		}

		// Get the category
		$category = Category::oneOrNew($filters['category_id']);

		// Get the category
		if ($category->get('id'))
		{
			$filters['scope']      = $category->get('scope');
			$filters['scope_id']   = $category->get('scope_id');
			$filters['scopeinfo']  = $filters['scope'] . ':' . $filters['scope_id'];
			$filters['section_id'] = $category->get('section_id');
		}

		// Get the section
		$section = Section::oneOrNew($filters['section_id']);
		if ($section->get('id') && !$filters['scopeinfo'])
		{
			$filters['scope']     = $section->get('scope');
			$filters['scope_id']  = $section->get('scope_id');
			$filters['scopeinfo'] = $filters['scope'] . ':' . $filters['scope_id'];
		}

		// Get sections
		$sections = array();
		if ($filters['scopeinfo'])
		{
			$sections = Section::all()
				->whereEquals('scope', $filters['scope'])
				->whereEquals('scope_id', $filters['scope_id'])
				->ordered('title', 'ASC')
				->rows();
		}

		// Get categories
		$categories = array();
		if ($filters['section_id'])
		{
			$categories = Category::all()
				->whereEquals('section_id', $filters['section_id'])
				->rows();

			if (!$filters['category_id'] || $filters['category_id'] <= 0)
			{
				$filters['category_id'] = array();
				foreach ($categories as $cat)
				{
					$filters['category_id'][] = $cat->id;
				}
			}
		}

		// Get threads
		$entries = Post::all()
			->whereEquals('parent', 0);

		if ($filters['search'])
		{
			$entries->whereLike('comment', strtolower((string)$filters['search']));
		}

		if ($filters['scope'])
		{
			$entries->whereEquals('scope', $filters['scope']);
		}

		if ($filters['scope_id'] >= 0)
		{
			$entries->whereEquals('scope_id', (int)$filters['scope_id']);
		}

		if ($filters['state'] >= 0)
		{
			$entries->whereEquals('state', (int)$filters['state']);
		}

		if ($filters['access'] >= 0)
		{
			$entries->whereEquals('access', (int)$filters['access']);
		}

		if (is_array($filters['category_id']))
		{
			if (!empty($filters['category_id']))
			{
				$entries->whereIn('category_id', $filters['category_id']);
			}
			$filters['category_id'] = -1;
		}
		elseif ($filters['category_id'] > 0)
		{
			$entries->whereEquals('category_id', (int)$filters['category_id']);
		}

		$rows = $entries
			->ordered('filter_order', 'filter_order_Dir')
			->paginated('limitstart', 'limit')
			->rows();

		$forum = new Manager($filters['scope'], $filters['scope_id']);

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->set('section', $section)
			->set('category', $category)
			->set('sections', $sections)
			->set('categories', $categories)
			->set('scopes', $forum->scopes())
			->display();
	}

	/**
	 * Display all posts in a thread
	 *
	 * @return	void
	 */
	public function threadTask()
	{
		// Filters
		$filters = array(
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				-1,
				'int'
			),
			'access' => Request::getState(
				$this->_option . '.' . $this->_controller . '.access',
				'access',
				-1,
				'int'
			),
			'section_id' => Request::getState(
				$this->_option . '.thread.section_id',
				'section_id',
				-1,
				'int'
			),
			'category_id' => Request::getState(
				$this->_option . '.thread.category_id',
				'category_id',
				-1,
				'int'
			),
			'thread' => Request::getState(
				$this->_option . '.thread.thread',
				'thread',
				0,
				'int'
			),
			'sort' => Request::getState(
				$this->_option . '.thread.sort',
				'filter_order',
				'c.id'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.thread.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		// Get the categories
		$categories = array();
		foreach (Category::all()->rows() as $c)
		{
			if (!isset($cateories[$c->section_id]))
			{
				$cateories[$c->section_id] = array();
			}
			$cateories[$c->section_id][] = $c;
			asort($cateories[$c->section_id]);
		}

		// Get the sections
		$sections = array();
		foreach (Section::all()->rows() as $s)
		{
			$ky = $s->scope . ' (' . $s->scope_id . ')';
			if ($s->scope == 'site')
			{
				$ky = '[ site ]';
			}
			if (!isset($sections[$ky]))
			{
				$sections[$ky] = array();
			}
			$s->categories = (isset($cateories[$s->id])) ? $cateories[$s->id] : array();
			$sections[$ky][] = $s;
			asort($sections[$ky]);
		}

		// Get records
		$entries = Post::oneOrFail($filters['thread']);

		if ($filters['search'])
		{
			$entries->whereLike('comment', strtolower((string)$filters['search']));
		}

		if ($filters['state'] >= 0)
		{
			$entries->whereEquals('state', (int)$filters['state']);
		}

		if ($filters['access'] >= 0)
		{
			$entries->whereEquals('access', (int)$filters['access']);
		}

		if ($filters['thread'])
		{
			$entries->whereEquals('thread', (int)$filters['thread']);
		}

		if ($filters['category_id'])
		{
			if (is_array($filters['category_id']))
			{
				$entries->whereIn('category_id', (int)$filters['category_id']);
				$filters['category_id'] = -1;
			}
			else if ($filters['category_id'] >= 0)
			{
				$entries->whereEquals('category_id', (int)$filters['category_id']);
			}
		}

		// Get records
		$rows = $entries
			->ordered('filter_order', 'filter_order_Dir')
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->set('sections', $sections)
			->set('thread', $entries)
			->display();
	}

	/**
	 * Displays a question response for editing
	 *
	 * @param   mixed  $post
	 * @return  void
	 */
	public function editTask($post=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$parent = Request::getInt('parent', 0);

		if (!is_object($post))
		{
			$id = Request::getVar('id', array(0));
			if (is_array($id))
			{
				$id = intval($id[0]);
			}

			$post = Post::oneOrNew($id);
		}

		if ($post->isNew())
		{
			$post->set('parent', $parent);
			$post->set('created_by', User::get('id'));
		}

		if ($post->get('parent'))
		{
			$threads = Post::all()
				->whereEquals('category_id', $post->get('category_id'))
				->whereEquals('parent', 0)
				->ordered()
				->rows();
		}

		// Get the category
		$category = Category::oneOrNew($post->get('category_id'));

		$categories = array();
		foreach (Category::all()->rows() as $c)
		{
			if (!isset($categories[$c->section_id]))
			{
				$categories[$c->section_id] = array();
			}
			$categories[$c->section_id][] = $c;
			asort($categories[$c->section_id]);
		}

		// Get the section
		$section = Section::oneOrNew($category->get('section_id'));

		// Get the sections for this group
		$sections = array();
		foreach (Section::all()->rows() as $s)
		{
			$ky = $s->scope . ' (' . $s->scope_id . ')';
			if ($s->scope == 'site')
			{
				$ky = '[ site ]';
			}
			if (!isset($sections[$ky]))
			{
				$sections[$ky] = array();
			}
			$s->categories = (isset($categories[$s->id])) ? $categories[$s->id] : array();
			$sections[$ky][] = $s;
			asort($sections[$ky]);
		}

		User::setState('com_forum.edit.thread.data', array(
			'id'       => $post->get('id'),
			'asset_id' => $post->get('asset_id')
		));
		$m = new AdminThread();
		$form = $m->getForm();

		// Get tags on this article
		$this->view
			->set('row', $post)
			->set('sections', $sections)
			->set('categories', $categories)
			->set('form', $form)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a post and redirects to listing
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		User::setState('com_forum.edit.thread.data', null);

		// Incoming
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);
		$fields = array_map('trim', $fields);

		$fields['sticky']    = (isset($fields['sticky']))    ? $fields['sticky']    : 0;
		$fields['closed']    = (isset($fields['closed']))    ? $fields['closed']    : 0;
		$fields['anonymous'] = (isset($fields['anonymous'])) ? $fields['anonymous'] : 0;

		// Initiate extended database class
		$post = Post::oneOrNew(intval($fields['id']))->set($fields);

		// Bind the rules.
		$data = Request::getVar('jform', array(), 'post');
		if (isset($data['rules']) && is_array($data['rules']))
		{
			$model = new AdminThread();
			$form      = $model->getForm($data, false);
			$validData = $model->validate($form, $data);

			$post->assetRules = $validData['rules'];
		}

		// Store new content
		if (!$post->save())
		{
			Notify::error($post->getError());
			return $this->editTask($post);
		}

		// Handle attachments
		if (!$this->uploadTask($post->get('thread', $post->get('id')), $post->get('id')))
		{
			Notify::error($this->getError());
			return $this->editTask($post);
		}

		// Process tags
		$post->tag(trim(Request::getVar('tags', '')));

		Notify::success(Lang::txt('COM_FORUM_POST_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($post);
		}

		// Redirect
		$p = '';
		if ($thread = Request::getInt('thread', 0))
		{
			$p = '&task=thread&thread=' . $thread;
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . $p, false)
		);
	}

	/**
	 * Uploads a file to a given directory and returns an attachment string
	 * that is appended to report/comment bodies
	 *
	 * @param   integer  $thread_id  Directory to upload files to
	 * @param   integer  $post_id    Post ID
	 * @return  boolean
	 */
	public function uploadTask($thread_id, $post_id)
	{
		if (!$thread_id)
		{
			$this->setError(Lang::txt('COM_FORUM_NO_UPLOAD_DIRECTORY'));
			return false;
		}

		// Instantiate an attachment record
		$attachment = Attachment::oneOrNew(Request::getInt('attachment', 0));
		$attachment->set('description', trim(Request::getVar('description', '')));
		$attachment->set('parent', $thread_id);
		$attachment->set('post_id', $post_id);
		if ($attachment->isNew())
		{
			$attachment->set('state', 1);
		}

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file || !isset($file['name']) || !$file['name'])
		{
			if ($attachment->get('id'))
			{
				// Only updating the description
				if (!$attachment->save())
				{
					$this->setError($attachment->getError());
					return false;
				}
			}
			return true;
		}

		// Upload file
		if (!$attachment->upload($file['name'], $file['tmp_name']))
		{
			$this->setError($attachment->getError());
		}

		// Save entry
		if (!$attachment->save())
		{
			$this->setError($attachment->getError());
		}

		return true;
	}

	/**
	 * Deletes one or more records and redirects to listing
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Loop through each ID
		$i = 0;

		foreach ($ids as $id)
		{
			$post = Post::oneOrFail(intval($id));

			if (!$post->destroy())
			{
				Notify::error($post->getError());
				continue;
			}

			$i++;
		}

		if ($i)
		{
			Notify::success(Lang::txt('COM_FORUM_POSTS_DELETED'));
		}

		$this->cancelTask();
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$state = $this->getTask() == 'publish' ? Post::STATE_PUBLISHED : Post::STATE_UNPUBLISHED;

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Loop through each record
		$i = 0;

		foreach ($ids as $id)
		{
			// Update record(s)
			$post = Post::oneOrFail(intval($id));
			$post->set('state', $state);

			if (!$post->save())
			{
				Notify::error($post->getError());
				continue;
			}

			$i++;
		}

		// Set message
		if ($i)
		{
			if ($state == Post::STATE_PUBLISHED)
			{
				$message = Lang::txt('COM_FORUM_ITEMS_PUBLISHED', $i);
			}
			else
			{
				$message = Lang::txt('COM_FORUM_ITEMS_UNPUBLISHED', $i);
			}

			Notify::success($message);
		}

		$this->cancelTask();
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @return  void
	 */
	public function stickyTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$sticky = Request::getInt('sticky', 0);

		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Loop through each record
		$i = 0;

		foreach ($ids as $id)
		{
			// Update record(s)
			$post = Post::oneOrFail(intval($id));
			$post->set('stick', $sticky);

			if (!$post->save())
			{
				Notify::error($post->getError());
				continue;
			}

			$i++;
		}

		// Set message
		if ($i)
		{
			if ($sticky == 1)
			{
				$message = Lang::txt('COM_FORUM_ITEMS_STUCK', $i);
			}
			else
			{
				$message = Lang::txt('COM_FORUM_ITEMS_UNSTUCK', $i);
			}

			Notify::success($message);
		}

		$this->cancelTask();
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @return  void
	 */
	public function accessTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$access = Request::getInt('access', 0);

		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Loop through each record
		$i = 0;

		foreach ($ids as $id)
		{
			// Update record(s)
			$post = Post::oneOrFail(intval($id));
			$post->set('access', $access);

			if (!$post->save())
			{
				Notify::error($post->getError());
				continue;
			}

			$i++;
		}

		// Set message
		if ($i)
		{
			Notify::success(Lang::txt('COM_FORUM_ITEMS_ACCESS_CHANGED', $i));
		}

		$this->cancelTask();
	}

	/**
	 * Cancels a task and redirects to listing
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		$fields = Request::getVar('fields', array());
		$thread = 0;
		if (isset($fields['thread']) && $fields['thread'])
		{
			$thread = $fields['thread'];
		}
		else if (isset($fields['id']))
		{
			$thread = $fields['id'];
		}
		$thread = $thread ?: Request::getInt('thread', 0);

		if (!isset($fields['category_id']))
		{
			$fields['category_id'] = Request::getInt('category_id', 0);
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&category_id=' . $fields['category_id'] . ($thread ? '&task=thread&thread=' . $thread : ''), false)
		);
	}
}
