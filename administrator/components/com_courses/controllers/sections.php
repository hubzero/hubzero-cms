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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'section.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'offering.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');

/**
 * Courses controller class for managing membership and course info
 */
class CoursesControllerSections extends Hubzero_Controller
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
		$this->view->filters['offering']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.offering',
			'offering',
			0
		);

		$this->view->offering = CoursesModelOffering::getInstance($this->view->filters['offering']);
		if (!$this->view->offering->exists())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=courses'
			);
			return;
		}
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

		$this->view->total = $this->view->offering->sections($this->view->filters);

		$this->view->filters['count'] = false;

		$this->view->rows = $this->view->offering->sections($this->view->filters);

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

			$this->view->row = CoursesModelSection::getInstance($id);
		}

		if (!$this->view->row->get('offering_id'))
		{
			$this->view->row->set('offering_id', JRequest::getInt('offering', 0));
		}

		$this->view->offering = CoursesModelOffering::getInstance($this->view->row->get('offering_id'));

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
		$model = CoursesModelSection::getInstance($fields['id']);

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

		$dates = JRequest::getVar('dates', array(), 'post');

		//$i=0;
		//$unit_up = '';
		//$unit_down = '';

		foreach ($dates as $i => $dt)
		{
			/*if (!$unit_up && $i == 0)
			{
				$unit_up = $dt['publish_up'];
			}
			if (!$unit_down && $i == 0)
			{
				$unit_down = $dt['publish_down'];
			}*/
			$dt['section_id'] = $model->get('id');

			$dtmodel = CoursesModelSectionDate::getInstance($dt['id']);
			if (!$dtmodel->bind($dt))
			{
				$this->setError($dtmodel->getError());
				continue;
			}

			if (!$dtmodel->store(true))
			{
				$this->setError($dtmodel->getError());
				continue;
			}

			if (isset($dt['asset_group']))
			{
				foreach ($dt['asset_group'] as $j => $ag)
				{
					if (!isset($ag['publish_up']) || !$ag['publish_up'])
					{
						$ag['publish_up'] = $dt['publish_up'];
					}
					/*else if ($ag['publish_up'] != $publishup)
					{
						$publishup = $ag['publish_up'];
					}*/

					if (!isset($ag['publish_down']) || !$ag['publish_down'])
					{
						$ag['publish_down'] = $dt['publish_down'];
					}
					/*else if ($ag['publish_down'] != $publishdown)
					{
						$publishdown = $ag['publish_down'];
					}*/
					$ag['section_id'] = $model->get('id');

					$dtmodel = CoursesModelSectionDate::getInstance($ag['id']);
					if (!$dtmodel->bind($ag))
					{
						$this->setError($dtmodel->getError());
						continue;
					}

					if (!$dtmodel->store(true))
					{
						$this->setError($dtmodel->getError());
						continue;
					}
					
					if (isset($ag['asset_group']))
					{
						foreach ($ag['asset_group'] as $k => $agt)
						{
							if (!isset($agt['publish_up']) || !$agt['publish_up'])
							{
								$agt['publish_up'] = $ag['publish_up'];
							}
							/*else if ($agt['publish_up'] != $publishup)
							{
								$publishup = $agt['publish_up'];
							}*/

							if (!isset($agt['publish_down']) || !$agt['publish_down'])
							{
								$agt['publish_down'] = $ag['publish_down'];
							}
							/*else if ($ag['publish_down'] != $publishdown)
							{
								$publishdown = $ag['publish_down'];
							}*/
							$agt['section_id'] = $model->get('id');

							$dtmodel = CoursesModelSectionDate::getInstance($agt['id']);
							if (!$dtmodel->bind($agt))
							{
								$this->setError($dtmodel->getError());
								continue;
							}

							if (!$dtmodel->store(true))
							{
								$this->setError($dtmodel->getError());
								continue;
							}
							
							if (isset($agt['asset']))
							{
								foreach ($agt['asset'] as $z => $a)
								{
									if (!isset($a['publish_up']) || !$a['publish_up'])
									{
										$a['publish_up'] = $agt['publish_up'];
									}
									/*else if ($agt['publish_up'] != $publishup)
									{
										$publishup = $agt['publish_up'];
									}*/

									if (!isset($a['publish_down']) || !$a['publish_down'])
									{
										$a['publish_down'] = $agt['publish_down'];
									}
									/*else if ($ag['publish_down'] != $publishdown)
									{
										$publishdown = $ag['publish_down'];
									}*/
									$a['section_id'] = $model->get('id');

									$dtmodel = CoursesModelSectionDate::getInstance($a['id']);
									if (!$dtmodel->bind($a))
									{
										$this->setError($dtmodel->getError());
										continue;
									}

									if (!$dtmodel->store(true))
									{
										$this->setError($dtmodel->getError());
										continue;
									}
									//$agt['asset'][$z] = $a;
								}
							}
							//$ag['asset_group'][$k] = $agt;
						}
					}
					
					//print_r($ag);
					//$dt['asset_group'][$j] = $ag;
				}
				//print_r($dt);
				//$dates[$i] = $dt;
			}

			/*if (!$dt['publish_up'])
			{
				$dt['publish_up'] = $publishup;
			}
			else if ($dt['publish_up'] != $publishup)
			{
				$publishup = $dt['publish_up'];
			}

			if (!$dt['publish_down'])
			{
				$dt['publish_down'] = $publishdown;
			}
			else if ($dt['publish_down'] != $publishdown)
			{
				$publishdown = $dt['publish_down'];
			}

			$dt['section_id'] = $model->get('id');

			$i++;

			$dtmodel = CoursesModelSectionDate::getInstance($dt['id']);
			if (!$dtmodel->bind($dt))
			{
				$this->setError($dtmodel->getError());
				continue;
			}

			if (!$dtmodel->store(true))
			{
				$this->setError($dtmodel->getError());
				continue;
			}*/
		}

		if ($this->getError())
		{
			$this->addComponentMessage(implode('<br />', $this->getErrors()), 'error');
			$this->editTask($model);
			return;
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&offering=' . $model->get('offering_id'),
			JText::_('COM_COURSES_SECTION_SAVED')
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
		$offering_id = JRequest::getInt('offering', 0);

		// Get the single ID we're working with
		if (!is_array($ids))
		{
			$ids = array();
		}

		$num = 0;

		// Do we have any IDs?
		if (!empty($ids))
		{
			// Get plugins
			//JPluginHelper::importPlugin('courses');
			//$dispatcher =& JDispatcher::getInstance();

			foreach ($ids as $id)
			{
				// Load the course page
				$model = CoursesModelSection::getInstance($id);

				// Ensure we found the course info
				if (!$model->exists())
				{
					continue;
				}

				// Delete course
				if (!$model->delete())
				{
					JError::raiseError(500, JText::_('Unable to delete section'));
					return;
				}

				// Log the course approval
				$log = new CoursesTableLog($this->database);
				$log->scope_id  = $course->get('id');
				$log->scope     = 'course_section';
				$log->user_id   = $this->juser->get('id');
				$log->timestamp = date('Y-m-d H:i:s', time());
				$log->action    = 'section_deleted';
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
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&offering=' . $offering_id,
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
		$offering_id = JRequest::getInt('offering', 0);

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&offering=' . $offering_id
		);
	}
}
