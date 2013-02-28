<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'assetgroup.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'unit.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'offering.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');

/**
 * Courses controller class for managing membership and course info
 */
class CoursesControllerAssetgroups extends Hubzero_Controller
{
	/**
	 * Displays a list of courses
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['unit']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.unit',
			'unit',
			0
		);

		$this->view->unit = CoursesModelUnit::getInstance($this->view->filters['unit']);
		if (!$this->view->unit->exists())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=courses'
			);
			return;
		}
		$this->view->offering = CoursesModelOffering::getInstance($this->view->unit->get('offering_id'));
		$this->view->course = CoursesModelCourse::getInstance($this->view->offering->get('course_id'));

		$this->view->filters['search']  = urldecode(trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search',
			'search',
			''
		)));
		// Filters for returning results
		$this->view->filters['limit']  = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']  = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);

		/*$this->view->filters['count'] = true;

		$this->view->total = $this->view->unit->assetgroups(null, $this->view->filters);

		$this->view->filters['count'] = false;

		$this->view->rows = $this->view->unit->assetgroups(null, $this->view->filters);*/
		$rows = $this->view->unit->assetgroups(null, $this->view->filters);

		// establish the hierarchy of the menu
		$children = array(
			0 => array()
		);

		$levellimit = ($this->view->filters['limit'] == 0) ? 500 : $this->view->filters['limit'];

		// first pass - collect children
		foreach ($rows as $v)
		{
			$children[0][] = $v;
			$children[$v->get('id')] = $v->children();
			
			//$v->set('name', '');
			/*$pt      = $v->get('parent');
			$list    = @$children[$pt] ? $children[$pt] : array();
			array_push($list, $v);
			$children[$pt] = $list;*/
		}

		// second pass - get an indent list of the items
		$list = $this->treeRecurse(0, '', array(), $children, max(0, $levellimit-1));

		$this->view->total = count($list);

		$this->view->rows = array_slice($list, $this->view->filters['start'], $this->view->filters['limit']);

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
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Recursive function to build tree
	 * 
	 * @param      integer $id       Parent ID
	 * @param      string  $indent   Indent text
	 * @param      array   $list     List of records
	 * @param      array   $children Container for parent/children mapping
	 * @param      integer $maxlevel Maximum levels to descend
	 * @param      integer $level    Indention level
	 * @param      integer $type     Indention type
	 * @return     void
	 */
	public function treeRecurse($id, $indent, $list, $children, $maxlevel=9999, $level=0, $type=1)
	{
		if (@$children[$id] && $level <= $maxlevel)
		{
			foreach ($children[$id] as $v)
			{
				$id = $v->get('id');

				if ($type) 
				{
					$pre    = '<span class="treenode">&#8970;</span>&nbsp;';
					$spacer = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				} 
				else 
				{
					$pre    = '- ';
					$spacer = '&nbsp;&nbsp;';
				}

				/*if (!is_a($v, 'stdClass'))
				{
					$data = $v->toArray();
				}
				else 
				{
					foreach (get_object_vars($v) as $key => $val) 
					{
						if (substr($key, 0, 1) != '_') 
						{
							$data[$key] = $val;
						}
					}
				}

				$k = new stdClass;
				foreach ($data as $key => $val)
				{
					$k->$key = $val;
				}*/

				if ($v->get('parent') == 0) 
				{
					$txt = '';
				} 
				else 
				{
					$txt = $pre;
				}
				$pt = $v->get('parent');

				$list[$id] = $v;
				$list[$id]->treename = "$indent$txt";
				$list[$id]->children = count(@$children[$id]);
				$list = $this->treeRecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level+1, $type);
			}
		}
		return $list;
	}

	/**
	 * Create a new course
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Displays an edit form
	 *
	 * @return	void
	 */
	public function editTask($model=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		if (is_object($model))
		{
			$this->view->row = $model;
		}
		else
		{
			// Incoming
			$ids = JRequest::getVar('id', array());

			// Get the single ID we're working with
			if (is_array($ids))
			{
				$id = (!empty($ids)) ? $ids[0] : '';
			}
			else
			{
				$id = '';
			}

			$this->view->row = new CoursesModelAssetgroup($id);
		}

		if (!$this->view->row->get('unit_id'))
		{
			$this->view->row->set('unit_id', JRequest::getInt('unit', 0));
		}

		$this->view->unit = CoursesModelUnit::getInstance($this->view->row->get('unit_id'));

		$this->view->offering = CoursesModelOffering::getInstance($this->view->unit->get('offering_id'));

		$rows = $this->view->unit->assetgroups();

		// establish the hierarchy of the menu
		$children = array(
			0 => array()
		);

		$levellimit = 500;

		// first pass - collect children
		foreach ($rows as $v)
		{
			$children[0][] = $v;
			$children[$v->get('id')] = $v->children();
		}

		// second pass - get an indent list of the items
		$this->view->assetgroups = $this->treeRecurse(0, '', array(), $children, max(0, $levellimit-1));

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Saves changes to a course or saves a new entry if creating
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');

		// Instantiate an Hubzero_Course object
		$model = new CoursesModelAssetgroup($fields['id']);

		if (!$model->bind($fields))
		{
			$this->setError('failed bind');
			$this->addComponentMessage($model->getError(), 'error');
			$this->editTask($model);
			return;
		}

		if (!$model->store())
		{
			$this->setError('failed store' . $model->getError());
			$this->addComponentMessage($model->getError(), 'error');
			$this->editTask($model);
			return;
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&unit=' . $model->get('unit_id'),
			JText::_('COM_COURSES_ASSETGROUP_SAVED')
		);
	}

	/**
	 * Removes a course and all associated information
	 *
	 * @return	void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		// Get the single ID we're working with
		if (!is_array($ids))
		{
			$ids = array();
		}

		$num = 0;

		// Do we have any IDs?
		if (!empty($ids))
		{
			foreach ($ids as $id)
			{
				// Load the course page
				$model = CoursesModelAssetgroup::getInstance($id);

				// Ensure we found the course info
				if (!$model->exists())
				{
					continue;
				}

				// Delete course
				if (!$model->delete())
				{
					JError::raiseError(500, JText::_('Unable to delete asset group'));
					return;
				}

				// Log the course approval
				$log = new CoursesTableLog($this->database);
				$log->scope_id  = $course->get('id');
				$log->scope     = 'course_assetgroup';
				$log->user_id   = $this->juser->get('id');
				$log->timestamp = date('Y-m-d H:i:s', time());
				$log->action    = 'assetgroup_deleted';
				$log->actor_id  = $this->juser->get('id');
				if (!$log->store())
				{
					$this->setError($log->getError());
				}

				$num++;
			}
		}

		// Redirect back to the courses page
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&unit=' . JRequest::getInt('unit', 0),
			JText::sprintf('%s Item(s) removed.', $num)
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&unit=' . JRequest::getInt('unit', 0)
		);
	}
}
