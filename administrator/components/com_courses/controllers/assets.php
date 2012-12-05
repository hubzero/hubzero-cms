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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.association.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.php');

/**
 * Courses controller class for managing course pages
 */
class CoursesControllerAssets extends Hubzero_Controller
{
	/**
	 * Manage course pages
	 *
	 * @return void
	 */
	public function displayTask()
	{
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['tmpl']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.tmpl',
			'tmpl',
			''
		);
		$this->view->filters['asset_scope']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.scope',
			'scope',
			'asset_group'
		);
		$this->view->filters['asset_scope_id']  = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.scope_id',
			'scope_id',
			0,
			'int'
		);
		$this->view->filters['course_id']  = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.course_id',
			'course_id',
			0,
			'int'
		);

		/*$this->view->unit = CoursesModelUnit::getInstance($this->view->filters['unit']);
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
		)));*/
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

		$tbl = new CoursesTableAsset($this->database);

		//$this->view->rows = $tbl->find(array('w' => $filters))
		$this->view->rows = $tbl->find(array(
			'w' => $this->view->filters
		));

		$this->view->assets = $tbl->find(array(
			'w' => array('course_id' => $this->view->filters['course_id'])
		));
		/*if ($this->view->rows)
		{
			$this->view->assets = $tbl->find(array(
				'w' => array('course_id' => $this->view->rows[0]->course_id)
			));
		}
		else
		{
			$unit = CoursesModelUnit::getInstance($this->view->filters['unit']);
			

			$this->view->assets = $tbl->find(array(
				'w' => array('course_id' => $this->view->offering->get('course_id'))
			));
		}*/

		$this->view->total = count($this->view->rows);

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
	 * Create a course page
	 *
	 * @return void
	 */
	public function linkTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$asset_id  = JRequest::getInt('asset', 0);
		$tmpl      = JRequest::getVar('tmpl', '');
		$scope     = JRequest::getVar('scope', 'asset_group');
		$scope_id  = JRequest::getInt('scope_id', 0);
		$course_id = JRequest::getInt('course_id', 0);

		// Get the element moving down - item 1
		$tbl = new CoursesTableAssetAssociation($this->database);
		$tbl->scope    = $scope;
		$tbl->scope_id = $scope_id;
		$tbl->asset_id = $asset_id;
		if (!$tbl->check())
		{
			$this->setError($tbl->getError());
		}
		if (!$tbl->store())
		{
			$this->setError($tbl->getError());
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&tmpl=' . $tmpl . '&scope=' . $scope . '&scope_id=' . $scope_id . '&course_id=' . $course_id
		);
	}

	/**
	 * Create a course page
	 *
	 * @return void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a course page
	 *
	 * @return void
	 */
	public function editTask($model = null)
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
				$id = (!empty($ids)) ? $ids[0] : 0;
			}
			else
			{
				$id = 0;
			}

			$this->view->row = new CoursesTableAsset($this->database);
			$this->view->row->load($id);
		}

		/*if (!$this->view->row->get('offering_id'))
		{
			$this->view->row->set('offering_id', JRequest::getInt('offering', 0));
		}

		$this->view->offering = CoursesModelOffering::getInstance($this->view->row->get('offering_id'));*/
		$this->view->tmpl      = JRequest::getVar('tmpl', '');
		$this->view->scope     = JRequest::getVar('scope', 'asset_group');
		$this->view->scope_id  = JRequest::getInt('scope_id', 0);
		$this->view->course_id = JRequest::getInt('course_id', 0);

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
	 * Save a course page
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// load the request vars
		$fields = JRequest::getVar('fields', array(), 'post');
		$tmpl   = JRequest::getVar('tmpl', '');

		// instatiate course page object for saving
		$row = new CoursesTableAsset($this->database);

		if (!$row->bind($fields))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if (!$row->check())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if (!$row->store())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		$fields['asset_id'] = $row->get('id');

		$row2 = new CoursesTableAssetAssociation($this->database);

		if (!$row2->bind($fields))
		{
			$this->addComponentMessage($row2->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if (!$row2->check())
		{
			$this->addComponentMessage($row2->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if (!$row2->store())
		{
			$this->addComponentMessage($row2->getError(), 'error');
			$this->editTask($row);
			return;
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&tmpl=' . $tmpl . '&scope=' . $fields['scope'] . '&scope_id=' . $fields['scope_id'] . '&course_id=' . $fields['course_id']
		);
	}

	/**
	 * Cancel a course page task
	 *
	 * @return void
	 */
	public function orderdownTask()
	{
		$this->reorderTask(-1);
	}

	/**
	 * Cancel a course page task
	 *
	 * @return void
	 */
	public function orderupTask()
	{
		$this->reorderTask(1);
	}

	/**
	 * Cancel a course page task
	 *
	 * @return void
	 */
	public function reorderTask($move=1)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getVar('id', array());
		$id = $id[0];

		$tmpl      = JRequest::getVar('tmpl', '');
		$scope     = JRequest::getVar('scope', 'asset_group');
		$scope_id  = JRequest::getInt('scope_id', 0);
		$course_id = JRequest::getInt('course_id', 0);

		// Get the element moving down - item 1
		$tbl = new CoursesTableAsset($this->database);
		$tbl->move($move, "id=" . $id);

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&tmpl=' . $tmpl . '&scope=' . $scope . '&scope_id=' . $scope_id . '&course_id=' . $course_id
		);
	}

	/**
	 * Cancel a course page task
	 *
	 * @return void
	 */
	public function cancelTask()
	{
		$tmpl      = JRequest::getVar('tmpl', '');
		$scope     = JRequest::getVar('scope', 'asset_group');
		$scope_id  = JRequest::getInt('scope_id', 0);
		$course_id = JRequest::getInt('course_id', 0);

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&tmpl=' . $tmpl . '&scope=' . $scope . '&scope_id=' . $scope_id . '&course_id=' . $course_id
		);
	}
}
