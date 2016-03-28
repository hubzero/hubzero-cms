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

namespace Components\Forum\Site\Controllers;

use Hubzero\Component\SiteController;
use Hubzero\Utility\String;
use Components\Forum\Models\Manager;
use Components\Forum\Models\Section;
use Components\Forum\Models\Category;
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
		$this->forum = new Manager('site', 0);

		parent::execute();
	}

	/**
	 * Method to set the document path
	 *
	 * @param   object  $section
	 * @param   object  $category
	 * @return  void
	 */
	protected function buildPathway($section=null, $category=null)
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if ($section)
		{
			Pathway::append(
				String::truncate(stripslashes($section->get('title')), 100, array('exact' => true)),
				'index.php?option=' . $this->_option . '&section=' . $section->get('alias')
			);
		}
		if ($category)
		{
			Pathway::append(
				String::truncate(stripslashes($category->get('title')), 100, array('exact' => true)),
				'index.php?option=' . $this->_option . '&section=' . $section->get('alias') . '&category=' . $category->get('alias')
			);
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @param   object  $section
	 * @param   object  $category
	 * @return	void
	 */
	protected function buildTitle($section=null, $category=null)
	{
		$this->_title = Lang::txt(strtoupper($this->_option));
		if ($section)
		{
			$this->_title .= ': ' . String::truncate(stripslashes($section->get('title')), 100, array('exact' => true));
		}
		if ($category)
		{
			$this->_title .= ': ' . String::truncate(stripslashes($category->get('title')), 100, array('exact' => true));
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
		// Incoming
		$filters = array(
			'section'    => Request::getVar('section', ''),
			'category'   => Request::getCmd('category', ''),
			'search'     => Request::getVar('q', ''),
			'scope'      => $this->forum->get('scope'),
			'scope_id'   => $this->forum->get('scope_id'),
			'state'      => Category::STATE_PUBLISHED,
			'parent'     => 0,
			'access'     => User::getAuthorisedViewLevels()
		);

		$filters['sortby'] = Request::getWord('sortby', 'activity');
		switch ($filters['sortby'])
		{
			case 'title':
				$filters['sort'] = 'sticky` DESC, `title';
				$filters['sort_Dir'] = strtoupper(Request::getVar('sortdir', 'ASC'));
			break;

			case 'replies':
				$filters['sort'] = 'sticky` DESC, `rgt';
				$filters['sort_Dir'] = strtoupper(Request::getVar('sortdir', 'DESC'));
			break;

			case 'created':
				$filters['sort'] = 'sticky` DESC, `created';
				$filters['sort_Dir'] = strtoupper(Request::getVar('sortdir', 'DESC'));
			break;

			case 'activity':
			default:
				$filters['sort'] = 'sticky` DESC, `activity';
				$filters['sort_Dir'] = strtoupper(Request::getVar('sortdir', 'DESC'));
			break;
		}

		// Section
		$section = Section::all()
			->whereEquals('alias', $filters['section'])
			->whereEquals('scope', $this->forum->get('scope'))
			->whereEquals('scope_id', $this->forum->get('scope_id'))
			->row();
		if (!$section->get('id'))
		{
			App::abort(404, Lang::txt('COM_FORUM_SECTION_NOT_FOUND'));
		}

		// Get the category
		$category = Category::all()
			->whereEquals('alias', $filters['category'])
			->whereEquals('scope', $this->forum->get('scope'))
			->whereEquals('scope_id', $this->forum->get('scope_id'))
			->row();
		if (!$category->get('id'))
		{
			App::abort(404, Lang::txt('COM_FORUM_CATEGORY_NOT_FOUND'));
		}

		// Get authorization
		$this->_authorize('category');
		$this->_authorize('thread');

		// Check logged in status
		if ($category->get('access') > 0 && User::isGuest())
		{
			$return = base64_encode(Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section=' . $filters['section'] . '&category=' . $filters['category'], false, true));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return)
			);
			return;
		}

		$threads = $category->threads()
			->select("*, (CASE WHEN last_activity != '0000-00-00 00:00:00' THEN last_activity ELSE created END)", 'activity')
			->whereEquals('state', $filters['state'])
			->whereIn('access', $filters['access'])
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated()
			->rows();

		// Set the page title
		$this->buildTitle($section, $category);

		// Set the pathway
		$this->buildPathway($section, $category);

		// Output view
		$this->view
			->set('config', $this->config)
			->set('forum', $this->forum)
			->set('section', $section)
			->set('category', $category)
			->set('threads', $threads)
			->set('filters', $filters)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Search threads and display a list of results
	 *
	 * @return  void
	 */
	public function searchTask()
	{
		// Incoming
		$filters = array(
			'scope'      => $this->forum->get('scope'),
			'scope_id'   => $this->forum->get('scope_id'),
			'state'      => Category::STATE_PUBLISHED,
			'access'     => User::getAuthorisedViewLevels()
		);

		$section = Section::blank();
		$section->set('scope', $this->forum->get('scope'));
		$section->set('title', Lang::txt('COM_FORUM_POSTS'));
		$section->set('alias', str_replace(' ', '-', $section->get('title')));
		$section->set('alias', preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($section->get('title'))));

		// Get all sections
		$sections = array();
		foreach ($this->forum->sections($filters)->rows() as $section)
		{
			$sections[$section->get('id')] = $section;
		}

		$category = Category::blank();
		$category->set('scope', $this->forum->get('scope'));
		$category->set('scope_id', $this->forum->get('scope_id'));
		$category->set('title', Lang::txt('COM_FORUM_SEARCH'));
		$category->set('alias', str_replace(' ', '-', $category->get('title')));
		$category->set('alias', preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($category->get('title'))));

		$categories = array();
		foreach ($this->forum->categories($filters)->rows() as $category)
		{
			$categories[$category->get('id')] = $category;
		}

		$filters['search'] = Request::getVar('q', '');

		if (!$filters['search'])
		{
			App::redirect(Route::url('index.php?option=' . $this->_option));
		}

		// Get authorization
		$this->_authorize('category');
		$this->_authorize('thread');

		// Set the page title
		$this->buildTitle($section, $category);

		// Set the pathway
		$this->buildPathway($section, $category);

		$this->view
			->set('config', $this->config)
			->set('forum', $this->forum)
			->set('sections', $sections)
			->set('categories', $categories)
			->set('filters', $filters)
			->display();
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
	 * @param   object  $category
	 * @return  void
	 */
	public function editTask($category=null)
	{
		if (User::isGuest())
		{
			$return = Route::url('index.php?option=' . $this->_option, false, true);
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// Get the section
		$section = Section::all()
			->whereEquals('alias', Request::getVar('section', ''))
			->whereEquals('scope', $this->forum->get('scope'))
			->whereEquals('scope_id', $this->forum->get('scope_id'))
			->row();

		// Incoming
		if (!is_object($category))
		{
			$category = Category::all()
				->whereEquals('alias', Request::getVar('category', ''))
				->whereEquals('scope', $this->forum->get('scope'))
				->whereEquals('scope_id', $this->forum->get('scope_id'))
				->row();
		}

		$this->_authorize('category', $category->get('id'));

		if ($category->isNew())
		{
			$category->set('created_by', User::get('id'));
			$category->set('section_id', $section->get('id'));
		}
		elseif ($category->get('created_by') != User::get('id') && !$this->config->get('access-create-category'))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
			return;
		}

		// Output the view
		$this->view
			->set('config', $this->config)
			->set('forum', $this->forum)
			->set('category', $category)
			->set('section', $section)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		if (User::isGuest())
		{
			$return = Route::url('index.php?option=' . $this->_option, false, true);
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// Check for request forgeries
		Request::checkToken();

		$url = 'index.php?option=' . $this->_option;

		// Incoming
		$fields = Request::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Instantiate a category
		$category = Category::oneOrNew($fields['id'])->set($fields);

		// Double-check that the user is authorized
		$this->_authorize('category', $category->get('id'));

		if (!$this->config->get('access-edit-category'))
		{
			App::redirect(
				Route::url($url)
			);
		}

		$category->set('closed', (isset($fields['closed']) && $fields['closed']) ? 1 : 0);

		// Check for alias duplicates
		if (!$category->isUnique())
		{
			Notify::error(Lang::txt('COM_FORUM_ERROR_CATEGORY_ALREADY_EXISTS'));
			return $this->editTask($category);
		}

		// Store new content
		if (!$category->save())
		{
			Notify::error($category->getError());
			return $this->editTask($category);
		}

		// Log activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($fields['id'] ? 'updated' : 'created'),
				'scope'       => 'forum.category',
				'scope_id'    => $category->get('id'),
				'description' => Lang::txt('COM_FORUM_ACTIVITY_CATEGORY_' . ($fields['id'] ? 'UPDATED' : 'CREATED'), '<a href="' . Route::url($url) . '">' . $category->get('title') . '</a>'),
				'details'     => array(
					'title' => $category->get('title'),
					'url'   => Route::url($url)
				)
			],
			'recipients' => array(
				['forum.site', 1],
				['forum.section', $category->get('section_id')],
				['user', $category->get('created_by')]
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
	 * @return  void
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

		// Load the category
		$category = Category::all()
			->whereEquals('alias', Request::getVar('category', ''))
			->whereEquals('scope', $this->forum->get('scope'))
			->whereEquals('scope_id', $this->forum->get('scope_id'))
			->row();

		// Make the sure the category exist
		if (!$category->get('id'))
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

		// Set the category to "deleted"
		$category->set('state', $category::STATE_DELETED);

		if (!$category->save())
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
				'scope_id'    => $category->get('id'),
				'description' => Lang::txt('COM_FORUM_ACTIVITY_CATEGORY_DELETED', '<a href="' . Route::url($url) . '">' . $category->get('title') . '</a>'),
				'details'     => array(
					'title' => $category->get('title'),
					'url'   => Route::url($url)
				)
			],
			'recipients' => array(
				['forum.site', 1],
				['forum.section', $category->get('section_id')],
				['user', $category->get('created_by')]
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
	 * @param   string   $assetType
	 * @param   integer  $assetId
	 * @return  void
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
