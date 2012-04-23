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

ximport('Hubzero_Controller');

/**
 * Short description for 'ForumController'
 * 
 * Long description (if any) ...
 */
class ForumControllerCategories extends Hubzero_Controller
{
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
				stripslashes($this->view->section->title), 
				'index.php?option=' . $this->_option . '&section=' . $this->view->section->alias
			);
		}
		if (isset($this->view->category))
		{
			$pathway->addItem(
				stripslashes($this->view->category->title), 
				'index.php?option=' . $this->_option . '&section=' . $this->view->section->alias . '&category=' . $this->view->category->alias
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
			$this->_title .= ': ' . stripslashes($this->view->section->title);
		}
		if (isset($this->view->category))
		{
			$this->_title .= ': ' . stripslashes($this->view->category->title);
		}
		$document = JFactory::getDocument();
		$document->setTitle($this->_title);
	}
	
	/**
	 * Short description for 'topics'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->title = JText::_('Discussion Forum');

		// Incoming
		$this->view->filters = array();
		$this->view->filters['authorized'] = 1;
		$this->view->filters['limit']    = JRequest::getInt('limit', 25);
		$this->view->filters['start']    = JRequest::getInt('limitstart', 0);
		$this->view->filters['section']  = JRequest::getVar('section', '');
		$this->view->filters['category'] = JRequest::getVar('category', '');
		$this->view->filters['search']   = JRequest::getVar('q', '');
		$this->view->filters['group']    = 0;
		$this->view->filters['state']    = 1;
		$this->view->filters['parent']   = 0;
		
		$this->view->section = new ForumSection($this->database);
		$this->view->section->loadByAlias($this->view->filters['section'], 0);
		
		$this->view->category = new ForumCategory($this->database);
		$this->view->category->loadByAlias($this->view->filters['category'], $this->view->section->id, 0);
		$this->view->filters['category_id'] = $this->view->category->id;
		
		if (!$this->view->category->id)
		{
			$this->view->category->title = JText::_('Discussions');
			$this->view->category->alias = str_replace(' ', '-', $this->view->category->title);
			$this->view->category->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($this->view->category->title));
		}
		
		// Initiate a forum object
		$this->view->forum = new ForumPost($this->database);

		// Get record count
		$this->view->total = $this->view->forum->getCount($this->view->filters);

		// Get records
		$this->view->rows = $this->view->forum->getRecords($this->view->filters);
		
		// Get the last post
		$this->view->lastpost = $this->view->forum->getLastActivity(0, $this->view->category->id);

		//get authorization
		$this->_authorize('category');
		$this->_authorize('thread');

		$this->view->config = $this->config;

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		// Push CSS to the tmeplate
		$this->_getStyles();
		
		// Push scripts to the template
		$this->_getScripts();
	
		// Set the page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();

		$this->view->notifications = $this->getComponentMessage();
		
		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}
		
		$this->view->display();
	}
	
	/**
	 * Short description for 'topics'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function searchTask()
	{
		$this->view->title = JText::_('Discussion Forum');

		// Incoming
		$this->view->filters = array();
		$this->view->filters['authorized'] = 1;
		$this->view->filters['limit']  = JRequest::getInt('limit', 25);
		$this->view->filters['start']  = JRequest::getInt('limitstart', 0);
		$this->view->filters['search'] = JRequest::getVar('q', '');
		$this->view->filters['group']  = 0;
		
		$this->view->section = new ForumSection($this->database);
		$this->view->section->title = JText::_('Posts');
		$this->view->section->alias = str_replace(' ', '-', $this->view->section->title);
		$this->view->section->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($this->view->section->title));
		
		// Get all sections
		$sections = $this->view->section->getRecords(array(
			'state' => 1, 
			'group' => $this->view->filters['group']
		));
		$s = array();
		foreach ($sections as $section)
		{
			$s[$section->id] = $section;
		}
		$this->view->sections = $s;
		
		$this->view->category = new ForumCategory($this->database);
		$this->view->category->title = JText::_('Search');
		$this->view->category->alias = str_replace(' ', '-', $this->view->category->title);
		$this->view->category->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($this->view->category->title));
		
		// Get all categories
		$categories = $this->view->category->getRecords(array(
			'state' => 1, 
			'group' => $this->view->filters['group']
		));
		$c = array();
		foreach ($categories as $category)
		{
			$c[$category->id] = $category;
		}
		$this->view->categories = $c;
		
		// Initiate a forum object
		$this->view->forum = new ForumPost($this->database);

		// Get record count
		$this->view->total = $this->view->forum->getCount($this->view->filters);

		// Get records
		$this->view->rows = $this->view->forum->getRecords($this->view->filters);

		//get authorization
		$this->_authorize('category');
		$this->_authorize('thread');

		$this->view->config = $this->config;

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		// Push CSS to the tmeplate
		$this->_getStyles();
		
		// Push scripts to the template
		$this->_getScripts();
	
		// Set the page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();

		$this->view->notifications = $this->getComponentMessage();
		
		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}
		
		$this->view->display();
	}
	
	/**
	 * Short description for 'addTopic'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function newTask()
	{
		$this->view->setLayout('edit');
		$this->editTask();
	}

	/**
	 * Short description for 'editTopic'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function editTask($model=null)
	{
		$category = JRequest::getVar('category', '');
		$section = JRequest::getInt('section', 0);
		
		if ($this->juser->get('guest')) 
		{
			$return = JRoute::_('index.php?option=' . $this->_option);
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($return))
			);
			return;
		}
		
		$this->view->section = new ForumSection($this->database);
		$this->view->section->loadByAlias($section, 0);

		// Incoming
		if (is_object($model))
		{
			$this->view->model = $model;
		}
		else 
		{
			$this->view->model = new ForumCategory($this->database);
			$this->view->model->loadByAlias($category, $this->view->section->id, 0);
		}
		
		$this->_authorize('category', $this->view->model->id);
		
		if (!$this->view->model->id) 
		{
			$this->view->model->created_by = $this->juser->get('id');
			$this->view->model->section_id = $section;
		}
		elseif ($this->view->model->created_by != $this->juser->get('id') && !$this->config->get('access-create-category'))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
			return;
		}
		
		$this->view->sections = $this->view->section->getRecords(array(
			'state' => 1,
			'group' => 0
		));
		if (!$this->view->sections || count($this->view->sections) <= 0)
		{
			$this->view->sections = array();
			
			$default = new ForumSection($this->database);
			$default->id = 0;
			$default->title = JText::_('Categories');
			$default->alias = str_replace(' ', '-', $default->title);
			$default->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($default->title));
			$this->view->sections[] = $default;
		}

		// Push CSS to the template
		$this->_getStyles();
		
		$this->view->config = $this->config;
		$this->view->notifications = $this->getComponentMessage();

		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}
		
		$this->view->display();
	}
	
	/**
	 * Short description for 'saveTopic'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function saveTask()
	{
		$fields = JRequest::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);
		
		$model = new ForumCategory($this->database);
		if (!$model->bind($fields))
		{
			$this->addComponentMessage($model->getError(), 'error');
			$this->view->setLayout('edit');
			$this->editTask($model);
			return;
		}
		
		$this->_authorize('category', $model->id);
		if (!$this->config->get('access-edit-category'))
		{
			// Set the redirect
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
		}

		// Check content
		if (!$model->check()) 
		{
			$this->addComponentMessage($model->getError(), 'error');
			$this->view->setLayout('edit');
			$this->editTask($model);
			return;
		}
		
		// Store new content
		if (!$model->store()) 
		{
			$this->addComponentMessage($model->getError(), 'error');
			$this->view->setLayout('edit');
			$this->editTask($model);
			return;
		}

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option)
		);
	}
	
	/**
	 * Short description for 'deleteTopic'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function deleteTask()
	{
		// Is the user logged in?
		if ($this->juser->get('guest')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode(JRoute::_('index.php?option=' . $this->_option))),
				JText::_('COM_FORUM_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Incoming
		$category = JRequest::getVar('category', '');
		if (!$category) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option),
				JText::_('COM_FORUM_MISSING_ID'),
				'error'
			);
			return;
		}
		
		// Load the section
		$section = JRequest::getVar('section', '');
		$sModel = new ForumSection($this->database);
		$sModel->loadByAlias($section, 0);

		// Load the category
		$model = new ForumCategory($this->database);
		$model->loadByAlias($category, $sModel->id, 0);
		
		// Make the sure the category exist
		if (!$model->id) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option),
				JText::_('COM_FORUM_MISSING_ID'),
				'error'
			);
			return;
		}
		
		// Check if user is authorized to delete entries
		$this->_authorize('category', $model->id);
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
		$tModel = new ForumPost($this->database);
		if (!$tModel->setStateByCategory($model->id, 2))  /* 0 = unpublished, 1 = published, 2 = deleted */
		{
			$this->setError($tModel->getError());
		}
		
		// Set the category to "deleted"
		$model->state = 2;  /* 0 = unpublished, 1 = published, 2 = deleted */
		if (!$model->store()) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option),
				$model->getError(),
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
	 * Short description for '_authorize'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);
		if (!$this->juser->get('guest')) 
		{
			if (version_compare(JVERSION, '1.6', 'ge'))
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
				$this->config->set('access-create-' . $assetType, $this->juser->authorise('core.create' . $at, $asset));
				$this->config->set('access-delete-' . $assetType, $this->juser->authorise('core.delete' . $at, $asset));
				$this->config->set('access-edit-' . $assetType, $this->juser->authorise('core.edit' . $at, $asset));
				$this->config->set('access-edit-state-' . $assetType, $this->juser->authorise('core.edit.state' . $at, $asset));
				$this->config->set('access-edit-own-' . $assetType, $this->juser->authorise('core.edit.own' . $at, $asset));
			}
			else 
			{
				if ($assetType == 'post' || $assetType == 'thread')
				{
					$this->config->set('access-create-' . $assetType, true);
					$this->config->set('access-edit-' . $assetType, true);
					$this->config->set('access-delete-' . $assetType, true);
				}
				if ($this->juser->authorize($this->_option, 'manage'))
				{
					$this->config->set('access-manage-' . $assetType, true);
					$this->config->set('access-admin-' . $assetType, true);
					$this->config->set('access-create-' . $assetType, true);
					$this->config->set('access-delete-' . $assetType, true);
					$this->config->set('access-edit-' . $assetType, true);
				}
			}
		}
	}
}