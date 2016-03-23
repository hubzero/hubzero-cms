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

namespace Components\Forum\Site\Controllers;

use Hubzero\Component\SiteController;
use Hubzero\Utility\String;
use Components\Forum\Models\Manager;
use Components\Forum\Models\Category;
use Components\Forum\Tables;
use Exception;
use Pathway;
use Request;
use Notify;
use Route;
use User;
use Lang;
use App;

/**
 * Controller class for forum categories
 */
class Categories extends SiteController
{
	/**
	 * Determine task and execute
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->model = new Manager('site', 0);

		parent::execute();
	}

	/**
	 * Method to set the document path
	 *
	 * @return  void
	 */
	protected function _buildPathway()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if (isset($this->view->section))
		{
			Pathway::append(
				String::truncate(stripslashes($this->view->section->get('title')), 100, array('exact' => true)),
				'index.php?option=' . $this->_option . '&section=' . $this->view->section->get('alias')
			);
		}
		if (isset($this->view->category))
		{
			Pathway::append(
				String::truncate(stripslashes($this->view->category->get('title')), 100, array('exact' => true)),
				'index.php?option=' . $this->_option . '&section=' . $this->view->section->get('alias') . '&category=' . $this->view->category->get('alias')
			);
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @return	void
	 */
	protected function _buildTitle()
	{
		$this->_title = Lang::txt(strtoupper($this->_option));
		if (isset($this->view->section))
		{
			$this->_title .= ': ' . String::truncate(stripslashes($this->view->section->get('title')), 100, array('exact' => true));
		}
		if (isset($this->view->category))
		{
			$this->_title .= ': ' . String::truncate(stripslashes($this->view->category->get('title')), 100, array('exact' => true));
		}

		App::get('document')->setTitle($this->_title);
	}

	/**
	 * Display a list of threads for a category
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->view->title = Lang::txt('COM_FORUM');

		// Incoming
		$this->view->filters = array(
			'authorized' => 1,
			'limit'      => Request::getInt('limit', 25),
			'start'      => Request::getInt('limitstart', 0),
			'section'    => Request::getVar('section', ''),
			'category'   => Request::getCmd('category', ''),
			'search'     => Request::getVar('q', ''),
			'scope'      => $this->model->get('scope'),
			'scope_id'   => $this->model->get('scope_id'),
			'state'      => 1,
			'parent'     => 0,
			// Show based on if logged in or not
			'access'     => (User::isGuest() ? 0 : array(0, 1))
		);

		$this->view->filters['sortby'] = Request::getWord('sortby', 'activity');
		switch ($this->view->filters['sortby'])
		{
			case 'title':
				$this->view->filters['sort'] = 'c.sticky DESC, c.title';
				$this->view->filters['sort_Dir'] = strtoupper(Request::getVar('sortdir', 'ASC'));
			break;

			case 'replies':
				$this->view->filters['sort'] = 'c.sticky DESC, replies';
				$this->view->filters['sort_Dir'] = strtoupper(Request::getVar('sortdir', 'DESC'));
			break;

			case 'created':
				$this->view->filters['sort'] = 'c.sticky DESC, c.created';
				$this->view->filters['sort_Dir'] = strtoupper(Request::getVar('sortdir', 'DESC'));
			break;

			case 'activity':
			default:
				$this->view->filters['sort'] = 'c.sticky DESC, activity';
				$this->view->filters['sort_Dir'] = strtoupper(Request::getVar('sortdir', 'DESC'));
			break;
		}

		$this->view->section  = $this->model->section($this->view->filters['section'], $this->model->get('scope'), $this->model->get('scope_id'));
		if (!$this->view->section->exists())
		{
			throw new Exception(Lang::txt('COM_FORUM_SECTION_NOT_FOUND'), 404);
		}

		$this->view->category = $this->view->section->category($this->view->filters['category']);
		if (!$this->view->category->exists())
		{
			throw new Exception(Lang::txt('COM_FORUM_CATEGORY_NOT_FOUND'), 404);
		}

		//get authorization
		$this->_authorize('category');
		$this->_authorize('thread');

		// Check logged in status
		if ($this->view->category->get('access') > 0 && User::isGuest())
		{
			$return = base64_encode(Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section=' . $this->view->filters['section'] . '&category=' . $this->view->filters['category'], false, true));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return)
			);
			return;
		}

		$this->view->config = $this->config;

		$this->view->model = $this->model;

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		$this->view->notifications = Notify::messages('forum');

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Search threads and display a list of results
	 *
	 * @return     void
	 */
	public function searchTask()
	{
		$this->view->title = Lang::txt('COM_FORUM');

		// Incoming
		$this->view->filters = array(
			'authorized' => 1,
			'limit'      => Request::getInt('limit', 25),
			'start'      => Request::getInt('limitstart', 0),
			'search'     => Request::getVar('q', ''),
			'scope'      => $this->model->get('scope'),
			'scope_id'   => $this->model->get('scope_id'),
			'state'      => 1,
			// Show based on if logged in or not
			'access'     => (User::isGuest() ? 0 : array(0, 1))
		);

		$this->view->section = $this->model->section(0);
		$this->view->section->set('scope', $this->model->get('scope'));
		$this->view->section->set('title', Lang::txt('COM_FORUM_POSTS'));
		$this->view->section->set('alias', str_replace(' ', '-', $this->view->section->get('title')));
		$this->view->section->set('alias', preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($this->view->section->get('title'))));

		// Get all sections
		$sections = $this->model->sections();
		$s = array();
		foreach ($sections as $section)
		{
			$s[$section->get('id')] = $section;
		}
		$this->view->sections = $s;

		$this->view->category = $this->view->section->category(0);
		$this->view->category->set('scope', $this->model->get('scope'));
		$this->view->category->set('title', Lang::txt('COM_FORUM_SEARCH'));
		$this->view->category->set('alias', str_replace(' ', '-', $this->view->category->get('title')));
		$this->view->category->set('alias', preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($this->view->category->get('title'))));

		$this->view->thread = $this->view->category->thread(0);

		// Get all categories
		$categories = $this->view->section->categories('list', array('section_id' => -1));
		$c = array();
		foreach ($categories as $category)
		{
			$c[$category->get('id')] = $category;
		}
		$this->view->categories = $c;

		//get authorization
		$this->_authorize('category');
		$this->_authorize('thread');

		$this->view->config = $this->config;
		$this->view->model  = $this->model;

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		$this->view->notifications = Notify::messages('forum');

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Show a form for creating an entry
	 *
	 * @return  void
	 */
	public function newTask()
	{
		$this->editTask();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @return  void
	 */
	public function editTask($model=null)
	{
		if (User::isGuest())
		{
			$return = Route::url('index.php?option=' . $this->_option, false, true);
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		$this->view->section = $this->model->section(Request::getVar('section', ''));

		// Incoming
		if (is_object($model))
		{
			$this->view->category = $model;
		}
		else
		{
			$this->view->category = new Category(
				Request::getVar('category', ''),
				$this->view->section->get('id')
			);
		}

		$this->_authorize('category', $this->view->category->get('id'));

		if (!$this->view->category->exists())
		{
			$this->view->category->set('created_by', User::get('id'));
			$this->view->category->set('section_id', $this->view->section->get('id'));
		}
		elseif ($this->view->category->get('created_by') != User::get('id') && !$this->config->get('access-create-category'))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
			return;
		}

		$this->view->config = $this->config;
		$this->view->model  = $this->model;

		$this->view->notifications = Notify::messages('forum');

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$fields = Request::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		$model = new Category($fields['id']);
		if (!$model->bind($fields))
		{
			Notify::error($model->getError());
			$this->editTask($model);
			return;
		}

		$this->_authorize('category', $model->get('id'));

		if (!$this->config->get('access-edit-category'))
		{
			// Set the redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
		}

		$model->set('closed', (isset($fields['closed']) && $fields['closed']) ? 1 : 0);

		// Store new content
		if (!$model->store(true))
		{
			Notify::error($model->getError());
			$this->editTask($model);
			return;
		}

		$url = 'index.php?option=' . $this->_option;

		// Log activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($fields['id'] ? 'updated' : 'created'),
				'scope'       => 'forum.category',
				'scope_id'    => $model->get('id'),
				'description' => Lang::txt('COM_FORUM_ACTIVITY_CATEGORY_' . ($fields['id'] ? 'UPDATED' : 'CREATED'), '<a href="' . Route::url($url) . '">' . $model->get('title') . '</a>'),
				'details'     => array(
					'title' => $model->get('title'),
					'url'   => Route::url($url)
				)
			],
			'recipients' => array(
				['forum.site', 1],
				['user', $model->get('created_by')]
			)
		]);

		// Set the redirect
		App::redirect(
			Route::url($url)
		);
	}

	/**
	 * Delete a category
	 *
	 * @return     void
	 */
	public function deleteTask()
	{
		$url = 'index.php?option=' . $this->_option;

		// Is the user logged in?
		if (User::isGuest())
		{
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($url, false, true))),
				Lang::txt('COM_FORUM_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Load the section
		$section = $this->model->section(Request::getVar('section', ''));

		// Load the category
		$category = $section->category(Request::getVar('category', ''));

		// Make the sure the category exist
		if (!$category->exists())
		{
			App::redirect(
				Route::url($url),
				Lang::txt('COM_FORUM_MISSING_ID'),
				'error'
			);
			return;
		}

		// Check if user is authorized to delete entries
		$this->_authorize('category', $category->get('id'));
		if (!$this->config->get('access-delete-category'))
		{
			App::redirect(
				Route::url($url),
				Lang::txt('COM_FORUM_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// Set all the threads/posts in all the categories to "deleted"
		$tModel = new Tables\Post($this->database);
		if (!$tModel->setStateByCategory($category->get('id'), 2))  /* 0 = unpublished, 1 = published, 2 = deleted */
		{
			$this->setError($tModel->getError());
		}

		// Set the category to "deleted"
		$category->set('state', 2);  /* 0 = unpublished, 1 = published, 2 = deleted */
		if (!$category->store())
		{
			App::redirect(
				Route::url($url),
				$category->getError(),
				'error'
			);
			return;
		}

		// Log activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'forum.category',
				'scope_id'    => $model->get('id'),
				'description' => Lang::txt('COM_FORUM_ACTIVITY_CATEGORY_DELETED', '<a href="' . Route::url($url) . '">' . $model->get('title') . '</a>'),
				'details'     => array(
					'title' => $model->get('title'),
					'url'   => Route::url($url)
				)
			],
			'recipients' => array(
				['forum.site', 1],
				['user', $model->get('created_by')]
			)
		]);

		// Redirect to main listing
		App::redirect(
			Route::url($url),
			Lang::txt('COM_FORUM_CATEGORY_DELETED'),
			'message'
		);
	}

	/**
	 * Set the authorization level for the user
	 *
	 * @return     void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);
		if (!User::isGuest())
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
			$this->config->set('access-admin-' . $assetType, User::authorise('core.admin', $asset));
			$this->config->set('access-manage-' . $assetType, User::authorise('core.manage', $asset));
			// Permissions
			if ($assetType == 'post' || $assetType == 'thread')
			{
				$this->config->set('access-create-' . $assetType, true);
				$val = User::authorise('core.create' . $at, $asset);
				if ($val !== null)
				{
					$this->config->set('access-create-' . $assetType, $val);
				}

				$this->config->set('access-edit-' . $assetType, true);
				$val = User::authorise('core.edit' . $at, $asset);
				if ($val !== null)
				{
					$this->config->set('access-edit-' . $assetType, $val);
				}

				$this->config->set('access-edit-own-' . $assetType, true);
				$val = User::authorise('core.edit.own' . $at, $asset);
				if ($val !== null)
				{
					$this->config->set('access-edit-own-' . $assetType, $val);
				}

				$this->config->set('access-delete-' . $assetType, true);
				$val = User::authorise('core.delete' . $at, $asset);
				if ($val !== null)
				{
					$this->config->set('access-delete-' . $assetType, $val);
				}
			}
			else
			{
				$this->config->set('access-create-' . $assetType, User::authorise('core.create' . $at, $asset));
				$this->config->set('access-edit-' . $assetType, User::authorise('core.edit' . $at, $asset));
				$this->config->set('access-edit-own-' . $assetType, User::authorise('core.edit.own' . $at, $asset));
				$this->config->set('access-delete-' . $assetType, User::authorise('core.delete' . $at, $asset));
			}

			//$this->config->set('access-delete-' . $assetType, User::authorise('core.delete' . $at, $asset));
			$this->config->set('access-edit-state-' . $assetType, User::authorise('core.edit.state' . $at, $asset));
		}
	}
}