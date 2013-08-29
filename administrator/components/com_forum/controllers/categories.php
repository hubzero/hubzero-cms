<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Controller class for forum categories
 */
class ForumControllerCategories extends Hubzero_Controller
{
	/**
	 * Display all categories in a section
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Get Joomla configuration
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();

		// Filters
		$this->view->filters = array();
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit', 
			'limit', 
			$config->getValue('config.list_limit'), 
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart', 
			'limitstart', 
			0, 
			'int'
		);
		$this->view->filters['group']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.group', 
			'group', 
			-1,
			'int'
		);
		$this->view->filters['section_id'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.section_id', 
			'section_id', 
			-1,
			'int'
		));
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort', 
			'filter_order', 
			'id'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir', 
			'filter_order_Dir', 
			'DESC'
		));
		$this->view->filters['scopeinfo']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.scopeinfo', 
			'scopeinfo', 
			''
		));
		if (strstr($this->view->filters['scopeinfo'], ':'))
		{
			$bits = explode(':', $this->view->filters['scopeinfo']);
			$this->view->filters['scope'] = $bits[0];
			$this->view->filters['scope_id'] = intval(end($bits));
		}

		$this->view->filters['admin'] = true;

		// Load the current section
		$this->view->section = new ForumSection($this->database);
		if (!$this->view->filters['section_id'] || $this->view->filters['section_id'] <= 0)
		{
			// No section? Load a default blank section
			$this->view->section->loadDefault();
		}
		else 
		{
			$this->view->section->load($this->view->filters['section_id']);
		}

		// Set the group ID from the secton, if a section is selected
		/*if ($this->view->filters['section_id'] && $this->view->section->id)
		{
			$this->view->filters['scope'] = $this->view->section->scope;
			$this->view->filters['scope_id'] = $this->view->section->scope_id;
		}*/

		// Get the sections for this group
		$this->view->sections = array();

		if ($this->view->filters['scopeinfo'])
		{
			$this->view->sections = $this->view->section->getRecords(array(
				'scope' => $this->view->filters['scope'],
				'scope_id' => $this->view->filters['scope_id'],
				'sort' => 'title',
				'sort_Dir' => 'ASC'
			));
		}

		
		/*if ($sections)
		{
			ximport('Hubzero_Group');
			ximport('Hubzero_View_Helper_Html');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');

			foreach ($sections as $s)
			{
				switch ($s->scope)
				{
					case 'group':
						$group = Hubzero_Group::getInstance($s->scope_id);
						$ky = $s->scope;
						if ($group)
						{
							$ky .= ' (' . Hubzero_View_Helper_Html::shortenText($group->get('cn'), 50, 0) . ')';
						}
						else
						{
							$ky .= ' (' . $s->scope_id . ')';
						}
					break;
					case 'course':
						$offering = CoursesModelOffering::getInstance($s->scope_id);
						$course = CoursesModelCourse::getInstance($offering->get('course_id'));
						$ky = $s->scope . ' (' . Hubzero_View_Helper_Html::shortenText($course->get('alias'), 50, 0) . ': ' . Hubzero_View_Helper_Html::shortenText($offering->get('alias'), 50, 0) . ')';
					break;
					case 'site':
					default:
						$ky = '[ site ]'; //$ky = $s->scope . ($s->scope_id ? ' (' . $s->scope_id . ')' : '');
					break;
				}

				if (!isset($this->view->sections[$ky]))
				{
					$this->view->sections[$ky] = array();
				}
				$this->view->sections[$ky][] = $s;
				asort($this->view->sections[$ky]);
			}
		}
		else 
		{
			$default = new ForumSection($this->database);
			$default->loadDefault($this->view->section->scope, $this->view->section->scope_id);

			$this->view->sections[] = $default;
		}
		asort($this->view->sections);*/

		$model = new ForumCategory($this->database);

		// Get a record count
		$this->view->total = $model->getCount($this->view->filters);

		// Get records
		$this->view->results = $model->getRecords($this->view->filters);

		// initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}

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
	 * @return	void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		// Incoming
		$section = JRequest::getInt('section_id', 0);
		$id = 0;
		$ids = JRequest::getVar('id', array());
		if (is_array($ids) && !empty($ids)) 
		{
			$id = intval($ids[0]);
		}

		$this->view->section = new ForumSection($this->database);
		$this->view->section->load($section);

		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else 
		{
			$this->view->row = new ForumCategory($this->database);
			$this->view->row->load($id);
		}

		if (!$this->view->row->id) 
		{
			$this->view->row->created_by = $this->juser->get('id');
			$this->view->row->section_id = $section;
			$this->view->row->scope      = $this->view->section->scope;
			$this->view->row->scope_id   = $this->view->section->scope_id;
		}

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
				$this->view->sections[$ky][] = $s;
				asort($this->view->sections[$ky]);
			}
		}
		else 
		{
			$default = new ForumSection($this->database);
			$default->loadDefault($this->view->section->scope, $this->view->section->scope_id);

			$this->view->sections[] = $default;
		}
		asort($this->view->sections);

		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$m = new ForumModelCategory();
			$this->view->form = $m->getForm();
		}

		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save a category record and redirects to listing
	 * 
	 * @return     void
	 */
	public function saveTask() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Initiate extended database class
		$model = new ForumCategory($this->database);
		if (!$model->bind($fields))
		{
			$this->addComponentMessage($model->getError(), 'error');
			$this->editTask($model);
			return;
		}

		if (!$model->scope)
		{
			$section = new ForumSection($this->database);
			$section->load($filters['section_id']);
			$model->scope    = $section->scope;
			$model->scope_id = $section->scope_id;
		}

		// Check content
		if (!$model->check()) 
		{
			$this->addComponentMessage($model->getError(), 'error');
			$this->editTask($model);
			return;
		}

		// Store new content
		if (!$model->store()) 
		{
			$this->addComponentMessage($model->getError(), 'error');
			$this->editTask($model);
			return;
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section_id=' . $fields['section_id'],
			JText::_('Category Successfully Saved')
		);
	}

	/**
	 * Deletes one or more records and redirects to listing
	 * 
	 * @return     void
	 */
	public function removeTask() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$section = JRequest::getInt('section_id', 0);
		$ids = JRequest::getVar('id', array());

		// Do we have any IDs?
		if (count($ids) > 0) 
		{
			// Instantiate some objects
			$category = new ForumCategory($this->database);

			// Loop through each ID
			foreach ($ids as $id) 
			{
				$id = intval($id);

				// Remove the posts in this category
				$tModel = new ForumPost($this->database);
				if (!$tModel->deleteByCategory($id)) 
				{
					JError::raiseError(500, $tModel->getError());
					return;
				}

				// Remove this category
				if (!$category->delete($id)) 
				{
					JError::raiseError(500, $category->getError());
					return;
				}
			}
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section_id=' . $section,
			JText::_('Categories Successfully Removed')
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
	 * @param      integer The state to set entries to
	 * @return     void
	 */
	public function stateTask($state=0) 
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$section = JRequest::getInt('section_id', 0);
		$ids = JRequest::getVar('id', array());

		// Check for an ID
		if (count($ids) < 1) 
		{
			$action = ($state == 1) ? JText::_('unpublish') : JText::_('publish');

			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section_id=' . $section,
				JText::_('Select an entry to ' . $action),
				'error'
			);
			return;
		}

		foreach ($ids as $id) 
		{
			// Update record(s)
			$row = new ForumCategory($this->database);
			$row->load(intval($id));
			$row->state = $state;
			if (!$row->store()) 
			{
				JError::raiseError(500, $row->getError());
				return;
			}
		}

		// set message
		if ($state == 1) 
		{
			$message = JText::_(count($ids) . ' Item(s) successfully published');
		} 
		else
		{
			$message = JText::_(count($ids) . ' Item(s) successfully unpublished');
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section_id=' . $section,
			$message
		);
	}

	/**
	 * Sets the state of one or more entries
	 * 
	 * @param      integer The state to set entries to
	 * @return     void
	 */
	public function accessTask() 
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$section = JRequest::getInt('section_id', 0);
		$state = JRequest::getInt('access', 0);
		$ids = JRequest::getVar('id', array());

		// Check for an ID
		if (count($ids) < 1) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section_id=' . $section,
				JText::_('Select an entry to change access'),
				'error'
			);
			return;
		}

		foreach ($ids as $id) 
		{
			// Update record(s)
			$row = new ForumCategory($this->database);
			$row->load(intval($id));
			$row->access = $state;
			if (!$row->store()) 
			{
				JError::raiseError(500, $row->getError());
				return;
			}
		}

		// set message
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section_id=' . $section,
			JText::_(count($ids) . ' Item(s) successfully changed access')
		);
	}

	/**
	 * Cancels a task and redirects to listing
	 * 
	 * @return     void
	 */
	public function cancelTask()
	{
		$fields = JRequest::getVar('fields', array());

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section_id=' . $fields['section_id']
		);
	}
}

