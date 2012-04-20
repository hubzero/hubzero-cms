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
 * Sections controller for forums
 */
class ForumControllerSections extends Hubzero_Controller
{
	public function displayTask()
	{
		$this->view->title = JText::_('Discussion Forum');
		
		// Incoming
		$this->view->filters = array();
		$this->view->filters['authorized'] = 1;
		$this->view->filters['group'] = 0;
		$this->view->filters['search'] = JRequest::getVar('q', '');
		$this->view->filters['section_id'] = 0;
		$this->view->filters['state'] = 1;
		
		// Flag to indicate if a section is being put into edit mode
		$this->view->edit = JRequest::getVar('section', '');
		
		$sModel = new ForumSection($this->database);
		$this->view->sections = $sModel->getRecords(array(
			'state' => 1, 
			'group' => $this->view->filters['group']
		));
		
		$model = new ForumCategory($this->database);
		
		// Check if there are uncategorized posts
		// This should mean legacy data
		if (($posts = $model->getPostCount(0, 0)) || !$this->view->sections || !count($this->view->sections))
		{
			// Create a default section
			$dSection = new ForumSection($this->database);
			$dSection->title = JText::_('Default Section');
			$dSection->alias = str_replace(' ', '-', $dSection->title);
			$dSection->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($dSection->title));
			$dSection->group_id = 0;
			if ($dSection->check())
			{
				$dSection->store();
			}
			
			// Create a default category
			$dCategory = new ForumCategory($this->database);
			$dCategory->title = JText::_('Discussions');
			$dCategory->description = JText::_('Default category for all discussions in this forum.');
			$dCategory->alias = str_replace(' ', '-', $dCategory->title);
			$dCategory->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($dCategory->title));
			$dCategory->section_id = $dSection->id;
			$dCategory->group_id = 0;
			if ($dCategory->check())
			{
				$dCategory->store();
			}
			
			if ($posts)
			{
				// Update all the uncategorized posts to the new default
				$tModel = new ForumPost($this->database);
				$tModel->updateCategory(0, $dCategory->id, 0);
			}
			
			$this->view->sections = $sModel->getRecords(array(
				'state' => 1, 
				'group' => $this->view->filters['group']
			));
		}
		
		//if (!$this->view->sections || count($this->view->sections) <= 0)
		//{
			/*$default = new ForumSection($this->database);
			$default->id = 0;
			$default->title = JText::_('Categories');
			$default->alias = str_replace(' ', '-', $default->title);
			$default->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($default->title));
		//}
		if (is_array($sections))
		{
			array_push($sections, $default);
			$this->view->sections = $sections;
		}
		else 
		{
			$sections = array($default);
		}
		$this->view->sections = $sections;*/
		
		//$model = new ForumCategory($this->database);
		
		$this->view->stats = new stdClass;
		$this->view->stats->categories = 0;
		$this->view->stats->threads = 0;
		$this->view->stats->posts = 0;
		
		foreach ($this->view->sections as $key => $section)
		{
			$this->view->filters['section_id'] = $section->id;
			
			$this->view->sections[$key]->categories = $model->getRecords($this->view->filters);
			/*if ($this->view->sections[0]->id == 0 && !$this->view->sections[$key]->categories)
			{
				$default = new ForumCategory($this->database);
				$default->id = 0;
				$default->title = JText::_('Discussions');
				$default->description = JText::_('Default category for all discussions in this forum.');
				$default->alias = str_replace(' ', '-', $default->title);
				$default->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($default->title));
				$default->section_id = 0;
				$default->created_by = 0;
				$default->threads = $model->getThreadCount(0, $this->view->filters['group']);
				$default->posts = $model->getPostCount(0, $this->view->filters['group']);
				
				$this->view->sections[0]->categories = array($default);
			}*/
			
			$this->view->stats->categories += count($this->view->sections[$key]->categories);
			if ($this->view->sections[$key]->categories)
			{
				foreach ($this->view->sections[$key]->categories as $c)
				{
					$this->view->stats->threads += $c->threads;
					$this->view->stats->posts += $c->posts;
				}
			}
		}

		// Get the last post
		$post = new ForumPost($this->database);
		$this->view->lastpost = $post->getLastActivity(0);

		// Get authorization
		$this->_authorize('section');
		$this->_authorize('category');

		$this->view->config = $this->config;
		
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
	 * Saves a section and redirects to main page afterward
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Incoming posted data
		$fields = JRequest::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);
		
		// Instantiate a new table row and bind the incoming data
		$model = new ForumSection($this->database);
		if (!$model->bind($fields))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
			return;
		}
		
		// Check content
		if ($model->check()) 
		{
			// Store new content
			$model->store();
		}

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option)
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
		$alias = JRequest::getVar('section', '');
		
		// Load the section
		$model = new ForumSection($this->database);
		$model->loadByAlias($alias, 0);
		
		// Make the sure the section exist
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
		$this->_authorize('section', $model->id);
		if (!$this->config->get('access-delete-section')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option),
				JText::_('COM_FORUM_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// Get all the categories in this section
		$cModel = new ForumCategory($this->database);
		$categories = $cModel->getRecords(array(
			'section_id' => $model->id,
			'group'      => 0
		));
		if ($categories)
		{
			// Build an array of category IDs
			$cats = array();
			foreach ($categories as $category)
			{
				$cats[] = $category->id;
			}
			
			// Set all the threads/posts in all the categories to "deleted"
			$tModel = new ForumPost($this->database);
			if (!$tModel->setStateByCategory($cats, 2))  /* 0 = unpublished, 1 = published, 2 = deleted */
			{
				$this->setError($tModel->getError());
			}
			
			// Set all the categories to "deleted"
			if (!$cModel->setStateBySection($model->id, 2))  /* 0 = unpublished, 1 = published, 2 = deleted */
			{
				$this->setError($cModel->getError());
			}
		}

		// Set the section to "deleted"
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
			JText::_('COM_FORUM_SECTION_DELETED'),
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
					$this->config->set('access-delete-' . $assetType, true);
					$this->config->set('access-edit-' . $assetType, true);
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