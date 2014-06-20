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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');

/**
 * Courses controller class for managing membership and course info
 */
class CoursesControllerCodes extends \Hubzero\Component\AdminController
{
	/**
	 * Displays a list of courses
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Get configuration
		$app = JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['section']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.section',
			'section',
			0
		);

		$this->view->section = CoursesModelSection::getInstance($this->view->filters['section']);
		if (!$this->view->section->exists())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=courses'
			);
			return;
		}
		$this->view->offering = CoursesModelOffering::getInstance($this->view->section->get('offering_id'));
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
		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		$this->view->filters['count'] = true;

		$this->view->total = $this->view->section->codes($this->view->filters);

		$this->view->filters['count'] = false;

		$this->view->rows = $this->view->section->codes($this->view->filters);

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
				$id = (!empty($ids)) ? $ids[0] : 0;
			}
			else
			{
				$id = 0;
			}

			$this->view->row = new CoursesModelSectionCode($id);
		}

		if (!$this->view->row->get('offering_id'))
		{
			$this->view->row->set('offering_id', JRequest::getInt('offering', 0));
		}

		$this->view->section = CoursesModelSection::getInstance($this->view->row->get('section_id'));

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

		// Instantiate a Course object
		$model = new CoursesModelSectionCode($fields['id']);

		if (!$model->bind($fields))
		{
			$this->addComponentMessage($model->getError(), 'error');
			$this->editTask($model);
			return;
		}

		if (!$model->store(true))
		{
			$this->addComponentMessage($model->getError(), 'error');
			$this->editTask($model);
			return;
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section=' . $model->get('section_id'),
			JText::_('COM_COURSES_CODE_SAVED')
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
				// Load the code
				$model = new CoursesModelSectionCode($id);

				// Ensure we found a record
				if (!$model->exists())
				{
					continue;
				}

				// Delete record
				if (!$model->delete())
				{
					JError::raiseError(500, JText::_('COM_COURSES_ERROR_UNABLE_TO_REMOVE_ENTRY'));
					return;
				}

				$num++;
			}
		}

		// Redirect back to the courses page
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section=' . JRequest::getInt('section', 0),
			JText::sprintf('COM_COURSES_ITEMS_REMOVED', $num)
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function generateTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$section = JRequest::getInt('section', 0);
		$num = JRequest::getInt('num', 1);

		$expires = JRequest::getVar('expires', array());
		$expires = implode('-', $expires) . ' 12:00:00';

		if ($num > 0)
		{
			$codes = array();
			for ($i = 0; $i < $num; $i++)
			{
				$model = new CoursesModelSectionCode(0);
				$model->set('code', $this->_generateCode());
				$model->set('section_id', $section);
				$model->set('expires', $expires);
				if (!$model->store(true))
				{
					$this->setError($model->getError());
				}
			}
		}

		if ($this->getError())
		{
			echo implode('<br />', $this->getErrors());
			die();
		}

		if (!JRequest::getInt('no_html', 0))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section=' . $section
			);
		}
	}

	/**
	 * Generate QRcode
	 *
	 * @return	void
	 */
	public function qrcodeTask()
	{
		include_once(JPATH_ROOT . DS . 'libraries' . DS . 'phpqrcode' . DS . 'qrlib.php');

		$no_html = JRequest::getInt('no_html', 0);
		$code = JRequest::getVar('code');

		if (!$code)
		{
			JError::raiseError(500, JText::_('No code provided'));
			return;
		}

		/*$section  = CoursesModelSection::getInstance($code->get('section_id'));
		if (!$section->exists())
		{
			JError::raiseError(500, JText::_('Section not found'));
			return;
		}
		$offering = CoursesModelOffering::getInstance($section->get('offering_id'));
		$course   = CoursesModelCourse::getInstance($offering->get('course_id'));*/
		//$juri = JURI::getInstance();

		$url = rtrim(JURI::base(), '/') . '/' . ltrim(JRoute::_('index.php?option=' . $this->_option . '&controller=courses&task=redeem&code=' . $code), '/');

		if ($no_html)
		{
			echo QRcode::png($url);
			return;
		}

		echo QRcode::text($url);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function optionsTask()
	{
		$section = JRequest::getInt('section', 0);

		$this->view->section = CoursesModelSection::getInstance($section);

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
	 * Generate a coupon code
	 *
	 * @return    string
	 */
	private function _generateCode()
	{
		$chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$res = '';
		for ($i = 0; $i < 10; $i++)
		{
			$res .= $chars[mt_rand(0, strlen($chars)-1)];
		}
		return $res;
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section=' . JRequest::getInt('section', 0)
		);
	}
}
