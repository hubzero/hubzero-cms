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
		
		$sections = $sModel->getRecords(array(
			'state' => 1, 
			'group' => $this->view->filters['group']
		));
		//if (!$this->view->sections || count($this->view->sections) <= 0)
		//{
			$default = new ForumSection($this->database);
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
		$this->view->sections = $sections;
		
		$model = new ForumCategory($this->database);
		
		$this->view->stats = new stdClass;
		$this->view->stats->categories = 0;
		$this->view->stats->threads = 0;
		$this->view->stats->posts = 0;
		
		foreach ($this->view->sections as $key => $section)
		{
			$this->view->filters['section_id'] = $section->id;
			
			$this->view->sections[$key]->categories = $model->getRecords($this->view->filters);
			if ($this->view->sections[0]->id == 0 && !$this->view->sections[$key]->categories)
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
			}
			
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
		$this->view->lastpost = $post->getLastActivity();

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
		$id = JRequest::getInt('id', 0);
		if (!$id) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option),
				JText::_('COM_FORUM_MISSING_ID'),
				'error'
			);
			return;
		}
		
		// Check if user is authorized to delete entries
		$this->_authorize('section', $id);
		if (!$this->config->get('access-delete')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option),
				JText::_('COM_FORUM_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// Initiate a forum object
		$model = new ForumSection($this->database);
		
		$category = new ForumCategory($this->database);
		$categories = $category->getRecords(array(
			'section_id' => $id,
			'group'      => 0
		));
		if ($categories)
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option),
				JText::_('COM_FORUM_SECTION_MUST_BE_EMPTY'),
				'warning'
			);
			return;
		}

		// Delete the topic itself
		if (!$model->delete($id)) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option),
				$model->getError(),
				'error'
			);
			return;
		}

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
				$asset  = $this->_option . '.' . $assetType;
				$asset .= ($assetId) ? '.' . $assetId : '';
				
				// Check general edit permission first.
				if ($this->juser->authorise('core.create' . $assetType, $asset)) 
				{
					$this->config->set('access-create-' . $assetType, true);
				}
				if ($this->juser->authorise('core.delete', $asset)) 
				{
					$this->config->set('access-delete-' . $assetType, true);
				}
				if ($this->juser->authorise('core.edit', $asset)) 
				{
					$this->config->set('access-edit-' . $assetType, true);
				}
			}
			else 
			{
				if ($this->juser->authorize($this->_option, 'manage'))
				{
					$this->config->set('access-create-' . $assetType, true);
					$this->config->set('access-delete-' . $assetType, true);
					$this->config->set('access-edit-' . $assetType, true);
				}
			}
		}
	}
}