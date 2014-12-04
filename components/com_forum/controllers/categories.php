<?php
/**
 * @package		HUBzero                                  CMS
 * @author		Shawn                                     Rice <zooley@purdue.edu>
 * @copyright	Copyright                               2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Controller class for forum categories
 */
class ForumControllerCategories extends \Hubzero\Component\SiteController
{
	/**
	 * Determine task and execute
	 *
	 * @return     void
	 */
	public function execute()
	{
		$this->model = new ForumModel('site', 0);

		parent::execute();
	}

	/**
	 * Method to set the document path
	 *
	 * @return	void
	 */
	protected function _buildPathway()
	{
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();

		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if (isset($this->view->section))
		{
			$pathway->addItem(
				\Hubzero\Utility\String::truncate(stripslashes($this->view->section->get('title')), 100, array('exact' => true)),
				'index.php?option=' . $this->_option . '&section=' . $this->view->section->get('alias')
			);
		}
		if (isset($this->view->category))
		{
			$pathway->addItem(
				\Hubzero\Utility\String::truncate(stripslashes($this->view->category->get('title')), 100, array('exact' => true)),
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
		$this->_title = JText::_(strtoupper($this->_option));
		if (isset($this->view->section))
		{
			$this->_title .= ': ' . \Hubzero\Utility\String::truncate(stripslashes($this->view->section->get('title')), 100, array('exact' => true));
		}
		if (isset($this->view->category))
		{
			$this->_title .= ': ' . \Hubzero\Utility\String::truncate(stripslashes($this->view->category->get('title')), 100, array('exact' => true));
		}
		$document = JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Display a list of threads for a category
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->title = JText::_('COM_FORUM');

		// Incoming
		$this->view->filters = array(
			'authorized' => 1,
			'limit'      => JRequest::getInt('limit', 25),
			'start'      => JRequest::getInt('limitstart', 0),
			'section'    => JRequest::getVar('section', ''),
			'category'   => JRequest::getCmd('category', ''),
			'search'     => JRequest::getVar('q', ''),
			'scope'      => $this->model->get('scope'),
			'scope_id'   => $this->model->get('scope_id'),
			'state'      => 1,
			'parent'     => 0,
			// Show based on if logged in or not
			'access'     => ($this->juser->get('guest') ? 0 : array(0, 1))
		);

		$this->view->filters['sortby']   = JRequest::getWord('sortby', 'activity');
		switch ($this->view->filters['sortby'])
		{
			case 'title':
				$this->view->filters['sort'] = 'c.sticky DESC, c.title';
				$this->view->filters['sort_Dir'] = strtoupper(JRequest::getVar('sortdir', 'ASC'));
			break;

			case 'replies':
				$this->view->filters['sort'] = 'c.sticky DESC, replies';
				$this->view->filters['sort_Dir'] = strtoupper(JRequest::getVar('sortdir', 'DESC'));
			break;

			case 'created':
				$this->view->filters['sort'] = 'c.sticky DESC, c.created';
				$this->view->filters['sort_Dir'] = strtoupper(JRequest::getVar('sortdir', 'DESC'));
			break;

			case 'activity':
			default:
				$this->view->filters['sort'] = 'c.sticky DESC, activity';
				$this->view->filters['sort_Dir'] = strtoupper(JRequest::getVar('sortdir', 'DESC'));
			break;
		}

		$this->view->section  = $this->model->section($this->view->filters['section'], $this->model->get('scope'), $this->model->get('scope_id'));
		if (!$this->view->section->exists())
		{
			JError::raiseError(404, JText::_('COM_FORUM_SECTION_NOT_FOUND'));
			return;
		}

		$this->view->category = $this->view->section->category($this->view->filters['category']);
		if (!$this->view->category->exists())
		{
			JError::raiseError(404, JText::_('COM_FORUM_CATEGORY_NOT_FOUND'));
			return;
		}

		//get authorization
		$this->_authorize('category');
		$this->_authorize('thread');

		$this->view->config = $this->config;

		$this->view->model = $this->model;

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		$this->view->notifications = $this->getComponentMessage();

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
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
		$this->view->title = JText::_('COM_FORUM');

		// Incoming
		$this->view->filters = array(
			'authorized' => 1,
			'limit'      => JRequest::getInt('limit', 25),
			'start'      => JRequest::getInt('limitstart', 0),
			'search'     => JRequest::getVar('q', ''),
			'scope'      => $this->model->get('scope'),
			'scope_id'   => $this->model->get('scope_id'),
			'state'      => 1,
			// Show based on if logged in or not
			'access'     => ($this->juser->get('guest') ? 0 : array(0, 1))
		);

		$this->view->section = $this->model->section(0);
		$this->view->section->set('scope', $this->model->get('scope'));
		$this->view->section->set('title', JText::_('COM_FORUM_POSTS'));
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
		$this->view->category->set('title', JText::_('COM_FORUM_SEARCH'));
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

		$this->view->notifications = $this->getComponentMessage();

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->display();
	}

	/**
	 * Show a form for creating an entry
	 *
	 * @return     void
	 */
	public function newTask()
	{
		$this->editTask();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @return     void
	 */
	public function editTask($model=null)
	{
		$this->view->setLayout('edit');

		if ($this->juser->get('guest'))
		{
			$return = JRoute::_('index.php?option=' . $this->_option, false, true);
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		$this->view->section = $this->model->section(JRequest::getVar('section', ''));

		// Incoming
		if (is_object($model))
		{
			$this->view->category = $model;
		}
		else
		{
			$this->view->category = new ForumModelCategory(
				JRequest::getVar('category', ''),
				$this->view->section->get('id')
			);
		}

		$this->_authorize('category', $this->view->category->get('id'));

		if (!$this->view->category->exists())
		{
			$this->view->category->set('created_by', $this->juser->get('id'));
			$this->view->category->set('section_id', $this->view->section->get('id'));
		}
		elseif ($this->view->category->get('created_by') != $this->juser->get('id') && !$this->config->get('access-create-category'))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
			return;
		}

		$this->view->config = $this->config;
		$this->view->model  = $this->model;

		$this->view->notifications = $this->getComponentMessage();

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->display();
	}

	/**
	 * Save an entry
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$fields = JRequest::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		$model = new ForumModelCategory($fields['id']);
		if (!$model->bind($fields))
		{
			$this->addComponentMessage($model->getError(), 'error');
			$this->editTask($model);
			return;
		}

		$this->_authorize('category', $model->get('id'));

		if (!$this->config->get('access-edit-category'))
		{
			// Set the redirect
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
		}

		$model->set('closed', (isset($fields['closed']) && $fields['closed']) ? 1 : 0);

		// Store new content
		if (!$model->store(true))
		{
			$this->addComponentMessage($model->getError(), 'error');
			$this->editTask($model);
			return;
		}

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option)
		);
	}

	/**
	 * Delete a category
	 *
	 * @return     void
	 */
	public function deleteTask()
	{
		// Is the user logged in?
		if ($this->juser->get('guest'))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_('index.php?option=' . $this->_option, false, true))),
				JText::_('COM_FORUM_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Load the section
		$section = $this->model->section(JRequest::getVar('section', ''));

		// Load the category
		$category = $section->category(JRequest::getVar('category', ''));

		// Make the sure the category exist
		if (!$category->exists())
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option),
				JText::_('COM_FORUM_MISSING_ID'),
				'error'
			);
			return;
		}

		// Check if user is authorized to delete entries
		$this->_authorize('category', $category->get('id'));
		if (!$this->config->get('access-delete-category'))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option),
				JText::_('COM_FORUM_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// Set all the threads/posts in all the categories to "deleted"
		$tModel = new ForumTablePost($this->database);
		if (!$tModel->setStateByCategory($category->get('id'), 2))  /* 0 = unpublished, 1 = published, 2 = deleted */
		{
			$this->setError($tModel->getError());
		}

		// Set the category to "deleted"
		$category->set('state', 2);  /* 0 = unpublished, 1 = published, 2 = deleted */
		if (!$category->store())
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option),
				$category->getError(),
				'error'
			);
			return;
		}

		// Redirect to main listing
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option),
			JText::_('COM_FORUM_CATEGORY_DELETED'),
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
		if (!$this->juser->get('guest'))
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
			$this->config->set('access-admin-' . $assetType, $this->juser->authorise('core.admin', $asset));
			$this->config->set('access-manage-' . $assetType, $this->juser->authorise('core.manage', $asset));
			// Permissions
			if ($assetType == 'post' || $assetType == 'thread')
			{
				$this->config->set('access-create-' . $assetType, true);
				$val = $this->juser->authorise('core.create' . $at, $asset);
				if ($val !== null)
				{
					$this->config->set('access-create-' . $assetType, $val);
				}

				$this->config->set('access-edit-' . $assetType, true);
				$val = $this->juser->authorise('core.edit' . $at, $asset);
				if ($val !== null)
				{
					$this->config->set('access-edit-' . $assetType, $val);
				}

				$this->config->set('access-edit-own-' . $assetType, true);
				$val = $this->juser->authorise('core.edit.own' . $at, $asset);
				if ($val !== null)
				{
					$this->config->set('access-edit-own-' . $assetType, $val);
				}
			}
			else
			{
				$this->config->set('access-create-' . $assetType, $this->juser->authorise('core.create' . $at, $asset));
				$this->config->set('access-edit-' . $assetType, $this->juser->authorise('core.edit' . $at, $asset));
				$this->config->set('access-edit-own-' . $assetType, $this->juser->authorise('core.edit.own' . $at, $asset));
			}

			$this->config->set('access-delete-' . $assetType, $this->juser->authorise('core.delete' . $at, $asset));
			$this->config->set('access-edit-state-' . $assetType, $this->juser->authorise('core.edit.state' . $at, $asset));
		}
	}
}