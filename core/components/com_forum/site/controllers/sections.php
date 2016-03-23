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
use Components\Forum\Models\Manager;
use Components\Forum\Models\Section;
use Pathway;
use Request;
use Route;
use User;
use Lang;
use App;

/**
 * Controller class for forum sections
 */
class Sections extends SiteController
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
	 * Display a list of sections and their categories
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get authorization
		$this->_authorize('section');
		$this->_authorize('category');

		$this->view->config = $this->config;

		$this->view->title = Lang::txt('COM_FORUM');

		// Incoming
		$this->view->filters = array(
			//'authorized' => 1,
			'scope'      => $this->model->get('scope'),
			'scope_id'   => $this->model->get('scope_id'),
			'search'     => Request::getVar('q', ''),
			//'section_id' => 0,
			'state'      => 1,
			// Show based on if logged in or not
			'access'     => (User::isGuest() ? 0 : array(0, 1))
		);

		// Flag to indicate if a section is being put into edit mode
		$this->view->edit = null;
		if (($section = Request::getVar('section', '')) && $this->_task == 'edit')
		{
			$this->view->edit = $section;
		}
		$this->view->sections = $this->model->sections('list', array('state' => 1));

		$this->view->model = $this->model;

		if (!$this->view->sections->total()
		 && $this->config->get('access-create-section')
		 && Request::getWord('action') == 'populate')
		{
			if (!$this->model->setup())
			{
				$this->setError($this->model->getError());
			}
			$this->view->sections = $this->model->sections('list', array('state' => 1));
		}

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		$this->view->notifications = \Notify::messages('forum');

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Saves a section and redirects to main page afterward
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming posted data
		$fields = Request::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Instantiate a new table row and bind the incoming data
		$section = new Section($fields['id']);
		if (!$section->bind($fields))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option),
				$section->getError(),
				'error'
			);
			return;
		}

		// Store new content
		if (!$section->store(true))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option),
				$section->getError(),
				'error'
			);
			return;
		}

		$url = 'index.php?option=' . $this->_option;

		// Log activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($fields['id'] ? 'updated' : 'created'),
				'scope'       => 'forum.section',
				'scope_id'    => $section->get('id'),
				'description' => Lang::txt('COM_FORUM_ACTIVITY_SECTION_' . ($fields['id'] ? 'UPDATED' : 'CREATED'), '<a href="' . Route::url($url) . '">' . $section->get('title') . '</a>'),
				'details'     => array(
					'title' => $section->get('title'),
					'url'   => Route::url($url)
				)
			],
			'recipients' => array(
				['forum.site', 1],
				['user', $section->get('created_by')]
			)
		]);

		// Set the redirect
		App::redirect(
			Route::url($url)
		);
	}

	/**
	 * Deletes a section and redirects to main page afterwards
	 *
	 * @return     void
	 */
	public function deleteTask()
	{
		// Is the user logged in?
		if (User::isGuest())
		{
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url('index.php?option=' . $this->_option, false, true))),
				Lang::txt('COM_FORUM_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Load the section
		$section = $this->model->section(Request::getVar('section', ''));

		// Make the sure the section exist
		if (!$section->exists())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option),
				Lang::txt('COM_FORUM_MISSING_ID'),
				'error'
			);
			return;
		}

		// Check if user is authorized to delete entries
		$this->_authorize('section', $section->get('id'));

		if (!$this->config->get('access-delete-section'))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option),
				Lang::txt('COM_FORUM_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// Set the section to "deleted"
		$section->set('state', 2);  /* 0 = unpublished, 1 = published, 2 = deleted */

		if (!$section->store())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option),
				$model->getError(),
				'error'
			);
			return;
		}

		$url = 'index.php?option=' . $this->_option;

		// Log activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'forum.section',
				'scope_id'    => $section->get('id'),
				'description' => Lang::txt('COM_FORUM_ACTIVITY_SECTION_DELETED', '<a href="' . Route::url($url) . '">' . $section->get('title') . '</a>'),
				'details'     => array(
					'title' => $section->get('title'),
					'url'   => Route::url($url)
				)
			],
			'recipients' => array(
				['forum.site', 1],
				['user', $section->get('created_by')]
			)
		]);

		// Redirect to main listing
		App::redirect(
			Route::url($url),
			Lang::txt('COM_FORUM_SECTION_DELETED'),
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
			}
			else
			{
				$this->config->set('access-create-' . $assetType, User::authorise('core.create' . $at, $asset));
				$this->config->set('access-edit-' . $assetType, User::authorise('core.edit' . $at, $asset));
				$this->config->set('access-edit-own-' . $assetType, User::authorise('core.edit.own' . $at, $asset));
			}

			$this->config->set('access-delete-' . $assetType, User::authorise('core.delete' . $at, $asset));
			$this->config->set('access-edit-state-' . $assetType, User::authorise('core.edit.state' . $at, $asset));
		}
	}

	/**
	 * Method to set the document path
	 *
	 * @return	void
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
		if ($this->_task)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
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
		if ($this->_task)
		{
			$this->_title .= ': ' . Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		}

		App::get('document')->setTitle($this->_title);
	}
}