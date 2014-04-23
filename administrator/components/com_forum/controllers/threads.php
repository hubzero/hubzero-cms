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

/**
 * Controller class for forum threads
 */
class ForumControllerThreads extends \Hubzero\Component\AdminController
{
	/**
	 * Display all threads in a category
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
		$this->view->filters['category_id'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.category_id', 
			'category_id', 
			-1,
			'int'
		));
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort', 
			'filter_order', 
			'c.id'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir', 
			'filter_order_Dir', 
			'DESC'
		));
		$this->view->filters['sticky'] = false;
		$this->view->filters['parent'] = 0;

		// Get the section
		$this->view->section = new ForumTableSection($this->database);
		if (!$this->view->section->id || $this->view->filters['section_id'] <= 0)
		{
			// No section? Load a default blank section
			$this->view->section->loadDefault();
		}
		else 
		{
			$this->view->section->load($this->view->filters['section_id']);
		}

		// Get the category
		$this->view->category = new ForumTableCategory($this->database);
		if (!$this->view->category->id || $this->view->filters['category_id'] <= 0)
		{
			// No category? Load a default blank catgory
			$this->view->category->loadDefault();
		}
		else 
		{
			$this->view->category->load($this->view->filters['category_id']);
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
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');

			foreach ($sections as $s)
			{
				$ky = $s->scope . ' (' . $s->scope_id . ')';

				switch ($s->scope)
				{
					case 'group':
						$group = \Hubzero\User\Group::getInstance($s->scope_id);
						$ky = $s->scope;
						if ($group)
						{
							$ky .= ' (' . \Hubzero\Utility\String::truncate($group->get('cn'), 50) . ')';
						}
						else
						{
							$ky .= ' (' . $s->scope_id . ')';
						}
					break;
					case 'course':
						$offering = CoursesModelOffering::getInstance($s->scope_id);
						$course = CoursesModelCourse::getInstance($offering->get('course_id'));
						$ky = $s->scope . ' (' . \Hubzero\Utility\String::truncate($course->get('alias'), 50) . ': ' . \Hubzero\Utility\String::truncate($offering->get('alias'), 50) . ')';
					break;
					case 'site':
					default:
						$ky = '[ site ]'; //$ky = $s->scope . ($s->scope_id ? ' (' . $s->scope_id . ')' : '');
					break;
				}

				/*if ($s->scope == 'site')
				{
					$ky = '[ site ]';
				}*/
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
			$default = new ForumTableSection($this->database);
			$default->loadDefault($this->view->section->scope, $this->view->section->scope_id);

			$this->view->sections[] = $default;
		}
		asort($this->view->sections);

		$model = new ForumTablePost($this->database);

		// Get a record count
		$this->view->total = $model->getCount($this->view->filters);

		// Get records
		$this->view->results = $model->getRecords($this->view->filters);

		// Initiate paging
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
	 * Display all posts in a thread
	 *
	 * @return	void
	 */
	public function threadTask()
	{
		// Get Joomla configuration
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();

		// Filters
		$this->view->filters = array();
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.thread.limit', 
			'limit', 
			$config->getValue('config.list_limit'), 
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.thread.limitstart', 
			'limitstart', 
			0, 
			'int'
		);
		$this->view->filters['group']    = $app->getUserStateFromRequest(
			$this->_option . '.thread.group', 
			'group', 
			-1,
			'int'
		);
		$this->view->filters['section_id'] = trim($app->getUserStateFromRequest(
			$this->_option . '.thread.section_id', 
			'section_id', 
			-1,
			'int'
		));
		$this->view->filters['category_id'] = trim($app->getUserStateFromRequest(
			$this->_option . '.thread.category_id', 
			'category_id', 
			-1,
			'int'
		));
		$this->view->filters['thread'] = trim($app->getUserStateFromRequest(
			$this->_option . '.thread.thread', 
			'thread', 
			0,
			'int'
		));
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.thread.sort', 
			'filter_order', 
			'c.id'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.thread.sortdir', 
			'filter_order_Dir', 
			'ASC'
		));
		$this->view->filters['sticky'] = false;

		// Get the section
		$this->view->section = new ForumTableSection($this->database);
		$this->view->section->load($this->view->filters['section_id']);
		if (!$this->view->section->id)
		{
			// No section? Load a default blank section
			$this->view->section->loadDefault();
		}

		// Get the category
		$this->view->category = new ForumTableCategory($this->database);
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
			$default = new ForumTableSection($this->database);
			$default->loadDefault($this->view->section->scope, $this->view->section->scope_id);

			$this->view->sections[] = $default;
		}
		asort($this->view->sections);

		$model = new ForumTablePost($this->database);

		// Get a record count
		$this->view->total = $model->getCount($this->view->filters);

		// Get records
		$this->view->results = $model->getRecords($this->view->filters);
		
		$model->load($this->view->filters['thread']);
		$this->view->thread = $model;

		// Initiate paging
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
	public function editTask($post=null) 
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		// Incoming
		$ids = JRequest::getVar('id', array(0));
		$parent = JRequest::getInt('parent', 0);
		$this->view->parent = $parent;
		if (is_array($ids)) 
		{
			$id = intval($ids[0]);
		}
		
		// Incoming
		if (is_object($post))
		{
			$this->view->row = $post;
		}
		else 
		{
			$this->view->row = new ForumTablePost($this->database);
			$this->view->row->load($id);
		}

		if (!$id) 
		{
			$this->view->row->parent = $parent;
			$this->view->row->created_by = $this->juser->get('id');
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
		$this->view->category = new ForumTableCategory($this->database);
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
		$this->view->section = new ForumTableSection($this->database);
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
			$default = new ForumTableSection($this->database);
			$default->loadDefault($this->view->section->scope, $this->view->section->scope_id);

			$this->view->sections[] = $default;
		}
		asort($this->view->sections);

		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$m = new ForumModelAdminThread();
			$this->view->form = $m->getForm();
		}

		// Get tags on this article
		$this->view->tModel = new ForumModelTags($this->view->row->id);
		$this->view->tags = $this->view->tModel->render('string');

		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}

		$this->view->display();
	}

	/**
	 * Save a post and redirects to listing
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

		if ($fields['id'])
		{
			$old = new ForumTablePost($this->database);
			$old->load(intval($fields['id']));
		}

		$fields['sticky']    = (isset($fields['sticky']))    ? $fields['sticky']    : 0;
		$fields['closed']    = (isset($fields['closed']))    ? $fields['closed']    : 0;
		$fields['anonymous'] = (isset($fields['anonymous'])) ? $fields['anonymous'] : 0;

		// Initiate extended database class
		$model = new ForumTablePost($this->database);
		if (!$model->bind($fields))
		{
			$this->addComponentMessage($model->getError(), 'error');
			$this->editTask($model);
			return;
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

		if ($fields['id'])
		{
			if ($old->category_id != $fields['category_id'])
			{
				$model->updateReplies(array('category_id' => $fields['category_id']), $model->id);
			}
		}

		$this->uploadTask(($model->thread ? $model->thread : $model->id), $model->id);

		$msg = JText::_('Thread Successfully Saved');
		$p = '';
		if (($parent = JRequest::getInt('parent', 0)))
		{
			$msg = JText::_('Post Successfully Saved');
			$p = '&task=thread&parent=' . $parent;
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . $p,
			$msg,
			'message'
		);
	}

	/**
	 * Uploads a file to a given directory and returns an attachment string
	 * that is appended to report/comment bodies
	 * 
	 * @param      string $listdir Directory to upload files to
	 * @return     string A string that gets appended to messages
	 */
	public function uploadTask($listdir, $post_id)
	{
		if (!$listdir) 
		{
			$this->setError(JText::_('COM_FORUM_NO_UPLOAD_DIRECTORY'));
			return;
		}

		$row = new ForumTableAttachment($this->database);
		$row->load(JRequest::getInt('attachment', 0));
		$row->description = trim(JRequest::getVar('description', ''));
		$row->post_id = $post_id;
		$row->parent = $listdir;

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
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
		$path = JPATH_ROOT . DS . trim($this->config->get('webpath', '/site/forum'), DS) . DS . $listdir;
		if ($post_id)
		{
			$path .= DS . $post_id;
		}

		// Build the path if it doesn't exist
		if (!is_dir($path)) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path)) 
			{
				$this->setError(JText::_('COM_FORUM_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return;
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		$ext = strtolower(JFile::getExt($file['name']));

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path . DS . $file['name'])) 
		{
			$this->setError(JText::_('COM_FORUM_ERROR_UPLOADING'));
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
	 * @return     void
	 */
	public function removeTask() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$category = JRequest::getInt('category_id', 0);
		$ids = JRequest::getVar('id', array());

		// Do we have any IDs?
		if (count($ids) > 0) 
		{
			$thread = new ForumTablePost($this->database);
			
			// Loop through each ID
			foreach ($ids as $id) 
			{
				$id = intval($id);
				
				if (!$thread->delete($id)) 
				{
					JError::raiseError(500, $thread->getError());
					return;
				}
			}
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&category_id=' . $category,
			JText::_('Entries Successfully Removed')
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
		$category = JRequest::getInt('category_id', 0);
		$ids = JRequest::getVar('id', array());

		// Check for an ID
		if (count($ids) < 1) 
		{
			$action = ($state == 1) ? JText::_('unpublish') : JText::_('publish');

			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&category_id=' . $category,
				JText::_('Select an entry to ' . $action),
				'error'
			);
			return;
		}

		foreach ($ids as $id) 
		{
			// Update record(s)
			$row = new ForumTablePost($this->database);
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
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&category_id=' . $category,
			$message
		);
	}

	/**
	 * Sets the state of one or more entries
	 * 
	 * @param      integer The state to set entries to
	 * @return     void
	 */
	public function stickyTask() 
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');
		
		// Incoming
		$category = JRequest::getInt('category_id', 0);
		$state = JRequest::getInt('sticky', 0);
		$ids = JRequest::getVar('id', array());

		// Check for an ID
		if (count($ids) < 1) 
		{
			$action = ($state == 1) ? JText::_('unstick') : JText::_('make stichy');

			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&category_id=' . $category,
				JText::_('Select an entry to ' . $action),
				'error'
			);
			return;
		}

		foreach ($ids as $id) 
		{
			// Update record(s)
			$row = new ForumTablePost($this->database);
			$row->load(intval($id));
			$row->sticky = $state;
			if (!$row->store()) 
			{
				JError::raiseError(500, $row->getError());
				return;
			}
		}

		// set message
		if ($state == 1) 
		{
			$message = JText::_(count($ids) . ' Item(s) successfully made sticky');
		} 
		else
		{
			$message = JText::_(count($ids) . ' Item(s) successfully unstuck');
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&category_id=' . $category,
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
		$category = JRequest::getInt('category_id', 0);
		$state = JRequest::getInt('access', 0);
		$ids = JRequest::getVar('id', array());

		// Check for an ID
		if (count($ids) < 1) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&category_id=' . $category,
				JText::_('Select an entry to change access'),
				'error'
			);
			return;
		}

		foreach ($ids as $id) 
		{
			// Update record(s)
			$row = new ForumTablePost($this->database);
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
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&category_id=' . $category,
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
		$parent = ($fields['parent']) ? $fields['parent'] : $fields['id'];

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&category_id=' . $fields['category_id'] . '&task=thread&parent=' . $parent
		);
	}
}
