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
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		$task = JRequest::getVar('task', '');
		$plugin = JRequest::getVar('plugin', '');
		if ($plugin && $task && $task != 'manage') //!isset($this->_taskMap[$task]))
		{
			JRequest::setVar('action', $task);
			JRequest::setVar('task', 'manage');
		}

		parent::execute();
	}

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

		$this->_getStyles();

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

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		$p = new $paramsClass('');
		$p->bind(JRequest::getVar('params', array(), 'post'));

		$model->set('params', $p->toString());

		if (!$model->store(true))
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
	public function removeTask()
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
				$model = new CoursesModelAssetgroup($id);

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
	 * Set the state of an entry
	 * 
	 * @param      integer $state State to set
	 * @return     void
	 */
	public function stateTask($state=0)
	{
		// Incoming
		$ids = JRequest::getVar('id', array(0));
		if (!is_array($ids)) 
		{
			$ids = array(0);
		}

		// Check for an ID
		if (count($ids) < 1) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&unit=' . JRequest::getInt('unit', 0),
				($state == 1 ? JText::_('COM_COURSES_SELECT_PUBLISH') : JText::_('COM_COURSES_SELECT_UNPUBLISH')),
				'error'
			);
			return;
		}

		// Update record(s)
		foreach ($ids as $id)
		{
			// Updating a category
			$row = new CoursesModelAssetgroup($id);
			$row->set('state', $state);
			$row->store();
		}

		// Set message
		switch ($state)
		{
			case '-1': 
				$message = JText::sprintf('COM_COURSES_ARCHIVED', count($ids));
			break;
			case '1':
				$message = JText::sprintf('COM_COURSES_PUBLISHED', count($ids));
			break;
			case '0':
				$message = JText::sprintf('COM_COURSES_UNPUBLISHED', count($ids));
			break;
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&unit=' . JRequest::getInt('unit', 0),
			$message
		);
	}

	/**
	 * Reorder a record up
	 * 
	 * @return     void
	 */
	public function orderupTask()
	{
		$this->orderTask();
	}

	/**
	 * Reorder a record up
	 * 
	 * @return     void
	 */
	public function orderdownTask()
	{
		$this->orderTask();
	}

	/**
	 * Reorder a plugin
	 * 
	 * @param      integer $access Access level to set
	 * @return     void
	 */
	public function orderTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$id = JRequest::getVar('id', array(0), 'post', 'array');
		JArrayHelper::toInteger($id, array(0));

		$uid = $id[0];
		$inc = ($this->_task == 'orderup' ? -1 : 1);

		$row = new CoursesTableAssetgroup($this->database);
		$row->load($uid);
		$row->move($inc, 'unit_id=' . $this->database->Quote($row->unit_id) . ' AND parent=' . $this->database->Quote($row->parent));
		$row->reorder('unit_id=' . $this->database->Quote($row->unit_id) . ' AND parent=' . $this->database->Quote($row->parent));

		//$unit = CoursesModelUnit::getInstance(JRequest::getInt('unit', 0));
		//$ags = $unit->assetgroups(null, array('parent' => $row->parent));

		if (($ags = $row->find(array('w' => array('parent' => $row->parent, 'unit_id' => $row->unit_id)))))
		{
			foreach ($ags as $ag)
			{
				$a = new CoursesModelAssetgroup($ag);
				$a->store();
			}
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&unit=' . JRequest::getInt('unit', 0)
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
