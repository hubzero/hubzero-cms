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

namespace Components\Forum\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Forum\Tables\Section;
use Components\Forum\Tables\Category;
use Components\Forum\Tables\Post;
use Components\Forum\Admin\Models\AdminThread;
use Components\Forum\Models\Tags;
use Components\Forum\Models\Manager;
use Filesystem;
use Exception;
use Request;
use Notify;
use Config;
use Route;
use Lang;
use App;

/**
 * Controller class for forum threads
 */
class Threads extends AdminController
{
	/**
	 * Display all threads in a category
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Filters
		$this->view->filters = array(
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			'group' => Request::getState(
				$this->_option . '.' . $this->_controller . '.group',
				'group',
				-1,
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
				'c.id'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			),
			'scopeinfo' => Request::getState(
				$this->_option . '.' . $this->_controller . '.scopeinfo',
				'scopeinfo',
				''
			),
			'sticky' => false,
			'parent' => 0,
			'admin' => true
		);
		if (strstr($this->view->filters['scopeinfo'], ':'))
		{
			$bits = explode(':', $this->view->filters['scopeinfo']);
			$this->view->filters['scope'] = $bits[0];
			$this->view->filters['scope_id'] = intval(end($bits));
		}
		else
		{
			$this->view->filters['scope'] = '';
			$this->view->filters['scope_id'] = -1;
		}

		// Get the category
		$this->view->category = new Category($this->database);
		if (!$this->view->filters['category_id'] || $this->view->filters['category_id'] <= 0)
		{
			// No category? Load a default blank catgory
			$this->view->category->loadDefault();
		}
		else
		{
			$this->view->category->load($this->view->filters['category_id']);

			$this->view->filters['scope'] = $this->view->category->scope;
			$this->view->filters['scope_id'] = $this->view->category->scope_id;
			$this->view->filters['scopeinfo'] = $this->view->filters['scope'] . ':' . $this->view->filters['scope_id'];
			$this->view->filters['section_id'] = $this->view->category->section_id;
		}

		// Get the section
		$this->view->section = new Section($this->database);
		if (!$this->view->filters['section_id'] || $this->view->filters['section_id'] <= 0)
		{
			// No section? Load a default blank section
			$this->view->section->loadDefault();
		}
		else
		{
			$this->view->section->load($this->view->filters['section_id']);

			if (!$this->view->filters['scopeinfo'])
			{
				$this->view->filters['scope'] = $this->view->section->scope;
				$this->view->filters['scope_id'] = $this->view->section->scope_id;
				$this->view->filters['scopeinfo'] = $this->view->filters['scope'] . ':' . $this->view->filters['scope_id'];
			}
		}

		$this->view->sections = array();
		if ($this->view->filters['scopeinfo'])
		{
			$this->view->sections = $this->view->section->getRecords(array(
				'scope'    => $this->view->filters['scope'],
				'scope_id' => $this->view->filters['scope_id'],
				'sort'     => 'title',
				'sort_Dir' => 'ASC'
			));
		}

		$this->view->categories = array();
		if ($this->view->filters['section_id'])
		{
			$this->view->categories = $this->view->category->getRecords(array(
				'section_id'    => $this->view->filters['section_id']
			));
			if (!$this->view->filters['category_id'] || $this->view->filters['category_id'] <= 0)
			{
				$this->view->filters['category_id'] = array();
				foreach ($this->view->categories as $cat)
				{
					$this->view->filters['category_id'][] = $cat->id;
				}
			}
		}

		$model = new Post($this->database);

		// Get a record count
		$this->view->total = $model->getCount($this->view->filters);

		// Get records
		$this->view->results = $model->getRecords($this->view->filters);

		$this->view->forum = new Manager($this->view->filters['scope'], $this->view->filters['scope_id']);

		$this->view->filters['category_id'] = is_array($this->view->filters['category_id']) ? -1 : $this->view->filters['category_id'];

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Display all posts in a thread
	 *
	 * @return	void
	 */
	public function threadTask()
	{
		// Filters
		$this->view->filters = array(
			'limit' => Request::getState(
				$this->_option . '.thread.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.thread.limitstart',
				'limitstart',
				0,
				'int'
			),
			'group' => Request::getState(
				$this->_option . '.thread.group',
				'group',
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
			),
			'sticky' => false
		);

		// Get the section
		$this->view->section = new Section($this->database);
		$this->view->section->load($this->view->filters['section_id']);
		if (!$this->view->section->id)
		{
			// No section? Load a default blank section
			$this->view->section->loadDefault();
		}

		// Get the category
		$this->view->category = new Category($this->database);
		$this->view->category->load($this->view->filters['category_id']);
		if (!$this->view->category->id)
		{
			// No category? Load a default blank catgory
			$this->view->category->loadDefault();
		}

		$this->view->cateories = array();
		$categories = $this->view->category->getRecords();
		if ($categories)
		{
			foreach ($categories as $c)
			{
				if (!isset($this->view->cateories[$c->section_id]))
				{
					$this->view->cateories[$c->section_id] = array();
				}
				$this->view->cateories[$c->section_id][] = $c;
				asort($this->view->cateories[$c->section_id]);
			}
		}

		// Get the sections for this group
		$this->view->sections = array();
		$sections = $this->view->section->getRecords();
		if ($sections)
		{
			foreach ($sections as $s)
			{
				$ky = $s->scope . ' (' . $s->scope_id . ')';
				if ($s->scope == 'site')
				{
					$ky = '[ site ]';
				}
				if (!isset($this->view->sections[$ky]))
				{
					$this->view->sections[$ky] = array();
				}
				$s->categories = (isset($this->view->cateories[$s->id])) ? $this->view->cateories[$s->id] : array(); //$this->view->category->getRecords(array('section_id'=>$s->id));
				$this->view->sections[$ky][] = $s;
				asort($this->view->sections[$ky]);
			}
		}
		else
		{
			$default = new Section($this->database);
			$default->loadDefault($this->view->section->scope, $this->view->section->scope_id);

			$this->view->sections[] = $default;
		}
		asort($this->view->sections);

		$model = new Post($this->database);

		// Get a record count
		$this->view->total = $model->getCount($this->view->filters);

		// Get records
		$this->view->results = $model->getRecords($this->view->filters);

		$model->load($this->view->filters['thread']);
		$this->view->thread = $model;

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new ticket
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->editTask();
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

		// Incoming
		$id = Request::getVar('id', array(0));
		$parent = Request::getInt('parent', 0);
		$this->view->parent = $parent;
		if (is_array($id))
		{
			$id = intval($id[0]);
		}

		// Incoming
		if (!is_object($post))
		{
			$post = new Post($this->database);
			$post->load($id);
		}

		$this->view->row = $post;

		if (!$id)
		{
			$this->view->row->parent = $parent;
			$this->view->row->created_by = User::get('id');
		}

		if ($this->view->row->parent)
		{
			$filters = array(
				'category_id' => $this->view->row->category_id,
				'sort'        => 'title',
				'sort_Dir'    => 'ASC',
				'limit'       => 100,
				'start'       => 0,
				'parent'      => 0
			);

			$this->view->threads = $this->view->row->getRecords($filters);
		}

		// Get the category
		$this->view->category = new Category($this->database);
		$this->view->category->load($this->view->row->category_id);
		if (!$this->view->category->id)
		{
			// No category? Load a default blank catgory
			$this->view->category->loadDefault();
		}

		$this->view->cateories = array();
		$categories = $this->view->category->getRecords();
		if ($categories)
		{
			foreach ($categories as $c)
			{
				if (!isset($this->view->cateories[$c->section_id]))
				{
					$this->view->cateories[$c->section_id] = array();
				}
				$this->view->cateories[$c->section_id][] = $c;
				asort($this->view->cateories[$c->section_id]);
			}
		}

		// Get the section
		$this->view->section = new Section($this->database);
		$this->view->section->load($this->view->category->section_id);
		if (!$this->view->section->id)
		{
			// No section? Load a default blank section
			$this->view->section->loadDefault();
		}

		// Get the sections for this group
		$this->view->sections = array();
		$sections = $this->view->section->getRecords();
		if ($sections)
		{
			foreach ($sections as $s)
			{
				$ky = $s->scope . ' (' . $s->scope_id . ')';
				if ($s->scope == 'site')
				{
					$ky = '[ site ]';
				}
				if (!isset($this->view->sections[$ky]))
				{
					$this->view->sections[$ky] = array();
				}
				$s->categories = (isset($this->view->cateories[$s->id])) ? $this->view->cateories[$s->id] : array(); //$this->view->category->getRecords(array('section_id'=>$s->id));
				$this->view->sections[$ky][] = $s;
				asort($this->view->sections[$ky]);
			}
		}
		else
		{
			$default = new Section($this->database);
			$default->loadDefault($this->view->section->scope, $this->view->section->scope_id);

			$this->view->sections[] = $default;
		}
		asort($this->view->sections);

		$m = new AdminThread();
		$this->view->form = $m->getForm();

		// Get tags on this article
		$this->view->tModel = new Tags($this->view->row->id);
		$this->view->tags = $this->view->tModel->render('string');

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a post and redirects to listing
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);
		$fields = array_map('trim', $fields);

		if ($fields['id'])
		{
			$old = new Post($this->database);
			$old->load(intval($fields['id']));
		}

		$fields['sticky']    = (isset($fields['sticky']))    ? $fields['sticky']    : 0;
		$fields['closed']    = (isset($fields['closed']))    ? $fields['closed']    : 0;
		$fields['anonymous'] = (isset($fields['anonymous'])) ? $fields['anonymous'] : 0;

		// Initiate extended database class
		$model = new Post($this->database);
		if (!$model->bind($fields))
		{
			Notify::error($model->getError());
			return $this->editTask($model);
		}

		// Check content
		if (!$model->check())
		{
			Notify::error($model->getError());
			return $this->editTask($model);
		}

		// Store new content
		if (!$model->store())
		{
			Notify::error($model->getError());
			return $this->editTask($model);
		}

		if ($fields['id'])
		{
			if ($old->category_id != $fields['category_id'])
			{
				$model->updateReplies(array('category_id' => $fields['category_id']), $model->id);
			}
		}

		$this->uploadTask(($model->thread ? $model->thread : $model->id), $model->id);

		$msg = Lang::txt('COM_FORUM_THREAD_SAVED');
		$p = '';
		if (($parent = Request::getInt('parent', 0)))
		{
			$msg = Lang::txt('COM_FORUM_POST_SAVED');
			$p = '&task=thread&parent=' . $parent;
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . $p, false),
			$msg,
			'message'
		);
	}

	/**
	 * Uploads a file to a given directory and returns an attachment string
	 * that is appended to report/comment bodies
	 *
	 * @param   string   $listdir  Directory to upload files to
	 * @param   integer  $post_id  Post ID
	 * @return  string   A string that gets appended to messages
	 */
	public function uploadTask($listdir, $post_id)
	{
		if (!$listdir)
		{
			$this->setError(Lang::txt('COM_FORUM_NO_UPLOAD_DIRECTORY'));
			return;
		}

		$row = new Attachment($this->database);
		$row->load(Request::getInt('attachment', 0));
		$row->description = trim(Request::getVar('description', ''));
		$row->post_id = $post_id;
		$row->parent = $listdir;

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			if ($row->id)
			{
				if (!$row->check())
				{
					$this->setError($row->getError());
				}
				if (!$row->store())
				{
					$this->setError($row->getError());
				}
			}
			return;
		}

		// Construct our file path
		$path = PATH_APP . DS . trim($this->config->get('webpath', '/site/forum'), DS) . DS . $listdir;
		if ($post_id)
		{
			$path .= DS . $post_id;
		}

		// Build the path if it doesn't exist
		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_FORUM_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return;
			}
		}

		// Make the filename safe
		$file['name'] = Filesystem::clean($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		$ext = strtolower(Filesystem::extension($file['name']));

		// Perform the upload
		if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('COM_FORUM_ERROR_UPLOADING'));
			return;
		}
		else
		{
			// File was uploaded
			// Create database entry
			$row->filename = $file['name'];

			if (!$row->check())
			{
				$this->setError($row->getError());
			}
			if (!$row->store())
			{
				$this->setError($row->getError());
			}
		}
	}

	/**
	 * Deletes one or more records and redirects to listing
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$category = Request::getInt('category_id', 0);

		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		if (count($ids) > 0)
		{
			$thread = new Post($this->database);

			// Loop through each ID
			foreach ($ids as $id)
			{
				$id = intval($id);

				// deletes attachments
				$this->markForDelete($id);

				if (!$thread->delete($id))
				{
					throw new Exception($thread->getError(), 500);
				}
			}
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&category_id=' . $category, false),
			Lang::txt('COM_FORUM_POSTS_DELETED')
		);
	}

	/**
	 * Calls stateTask to publish entries
	 *
	 * @return     void
	 */
	public function publishTask()
	{
		$this->stateTask(1);
	}

	/**
	 * Calls stateTask to unpublish entries
	 *
	 * @return     void
	 */
	public function unpublishTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @param   integer  $state  The state to set entries to
	 * @return  void
	 */
	public function stateTask($state=0)
	{
		// Check for request forgeries
		Request::checkToken('get') or Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$category = Request::getInt('category_id', 0);

		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);
		//attachment state
		$attachment_state = 0;

		// Check for an ID
		if (count($ids) < 1)
		{
			$action = ($state == 1) ? Lang::txt('COM_FORUM_UNPUBLISH') : Lang::txt('COM_FORUM_PUBLISH');

			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&category_id=' . $category, false),
				Lang::txt('COM_FORUM_SELECT_ENTRY_TO', $action),
				'error'
			);
			return;
		}

		foreach ($ids as $id)
		{
			// Update record(s)
			$row = new Post($this->database);
			$row->load(intval($id));
			$row->state = $state;
			if (!$row->store())
			{
				throw new Exception($row->getError(), 500);
			}

			if ($state == 1)
			{
				$this->markForPublish($id);
			}
			elseif ($state == 0)
			{
				$this->markForDelete($id);
			}
		}

		// set message
		if ($state == 1)
		{
			$message = Lang::txt('COM_FORUM_ITEMS_PUBLISHED', count($ids));
		}
		else
		{
			$message = Lang::txt('COM_FORUM_ITEMS_UNPUBLISHED', count($ids));
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&category_id=' . $category, false),
			$message
		);
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @return  void
	 */
	public function stickyTask()
	{
		// Check for request forgeries
		Request::checkToken('get') or Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$category = Request::getInt('category_id', 0);
		$state    = Request::getInt('sticky', 0);

		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			$action = ($state == 1) ? Lang::txt('COM_FORUM_MAKE_NOT_STICKY') : Lang::txt('COM_FORUM_MAKE_STICKY');

			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&category_id=' . $category, false),
				Lang::txt('COM_FORUM_SELECT_ENTRY_TO', $action),
				'error'
			);
			return;
		}

		foreach ($ids as $id)
		{
			// Update record(s)
			$row = new Post($this->database);
			$row->load(intval($id));
			$row->sticky = $state;
			if (!$row->store())
			{
				throw new Exception($row->getError(), 500);
			}
		}

		// set message
		if ($state == 1)
		{
			$message = Lang::txt('COM_FORUM_ITEMS_STUCK', count($ids));
		}
		else
		{
			$message = Lang::txt('COM_FORUM_ITEMS_UNSTUCK', count($ids));
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&category_id=' . $category, false),
			$message
		);
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @return  void
	 */
	public function accessTask()
	{
		// Check for request forgeries
		Request::checkToken('get') or Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$category = Request::getInt('category_id', 0);
		$state    = Request::getInt('access', 0);

		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&category_id=' . $category, false),
				Lang::txt('COM_FORUM_SELECT_ENTRY_TO_CHANGE_ACCESS'),
				'error'
			);
			return;
		}

		foreach ($ids as $id)
		{
			// Update record(s)
			$row = new Post($this->database);
			$row->load(intval($id));
			$row->access = $state;
			if (!$row->store())
			{
				throw new Exception($row->getError(), 500);
			}
		}

		// set message
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&category_id=' . $category, false),
			Lang::txt('COM_FORUM_ITEMS_ACCESS_CHANGED', count($ids))
		);
	}

	/**
	 * Cancels a task and redirects to listing
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		$fields = Request::getVar('fields', array());
		$parent = ($fields['parent']) ? $fields['parent'] : $fields['id'];

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&category_id=' . $fields['category_id'] . '&task=thread&parent=' . $parent, false)
		);
	}

	/**
	 * Marks a file for deletion
	 *
	 * @param   integer  $post_id  The ID of the post which is associated with the attachment
	 * @return  void
	 */
	public function markForDelete($post_id)
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			return;
		}

		// Load attachment object
		$row = new Attachment($this->database);
		$row->loadByPost($post_id);

		//mark for deletion
		$row->set('status', 2);

		if (!$row->store())
		{
			$this->setError($row->getError());
		}
	}

	/**
	 * Marks a file for deletion
	 *
	 * @param   integer  $post_id  The ID of the post which is associated with the attachment
	 * @return  void
	 */
	public function markForPublish($post_id)
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			return;
		}

		// Load attachment object
		$row = new Attachment($this->database);
		$row->loadByPost($post_id);

		//mark for deletion
		$row->set('status', 0);

		if (!$row->store())
		{
			$this->setError($row->getError());
		}
	}
}
