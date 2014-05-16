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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'section.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'offering.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'section' . DS . 'badge.php');

/**
 * Courses controller class for managing membership and course info
 */
class CoursesControllerSections extends \Hubzero\Component\AdminController
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
		// Get sorting variables
		$this->view->filters['sort']         = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort', 
			'filter_order', 
			'title'
		));
		$this->view->filters['sort_Dir']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir', 
			'filter_order_Dir', 
			'ASC'
		));
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
		$this->view->course   = CoursesModelCourse::getInstance($this->view->offering->get('course_id'));
		$this->view->badge    = CoursesModelSectionBadge::loadBySectionId($this->view->row->get('id'));

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
	 * Save a course and fall through to edit view
	 *
	 * @return void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Saves changes to a course or saves a new entry if creating
	 *
	 * @return void
	 */
	public function saveTask($redirect=true)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');

		// Instantiate a Course object
		$model = CoursesModelSection::getInstance($fields['id']);

		if (!$model->bind($fields))
		{
			$this->addComponentMessage($model->getError(), 'error');
			$this->editTask($model);
			return;
		}

		$paramsClass = 'JParameter';
		$mthd = 'bind';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
			$mthd = 'loadArray';
		}

		$p = new $paramsClass('');
		$p->$mthd(JRequest::getVar('params', '', 'post'));

		$paramsClass = 'JParameter';
		$mthd = 'bind';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
			$mthd = 'loadArray';
		}

		// Make sure the logo gets carried over
		$op = new $paramsClass($model->get('params'));
		$p->set('logo', $op->get('logo'));

		$model->set('params', $p->toString());

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

			$dtmodel = new CoursesModelSectionDate($dt['id']);
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

					if (!isset($ag['publish_down']) || !$ag['publish_down'])
					{
						$ag['publish_down'] = $dt['publish_down'];
					}

					$ag['section_id'] = $model->get('id');

					$dtmodel = new CoursesModelSectionDate($ag['id']);
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

							if (!isset($agt['publish_down']) || !$agt['publish_down'])
							{
								$agt['publish_down'] = $ag['publish_down'];
							}

							$agt['section_id'] = $model->get('id');

							$dtmodel = new CoursesModelSectionDate($agt['id']);
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

									if (!isset($a['publish_down']) || !$a['publish_down'])
									{
										$a['publish_down'] = $agt['publish_down'];
									}

									$a['section_id'] = $model->get('id');

									$dtmodel = new CoursesModelSectionDate($a['id']);
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
					if (isset($ag['asset']))
					{
						foreach ($ag['asset'] as $z => $a)
						{
							if (!isset($a['publish_up']) || !$a['publish_up'])
							{
								$a['publish_up'] = $ag['publish_up'];
							}

							if (!isset($a['publish_down']) || !$a['publish_down'])
							{
								$a['publish_down'] = $ag['publish_down'];
							}

							$a['section_id'] = $model->get('id');

							$dtmodel = new CoursesModelSectionDate($a['id']);
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
						}
					}
				}
			}
			if (isset($dt['asset']))
			{
				foreach ($dt['asset'] as $z => $a)
				{
					if (!isset($a['publish_up']) || !$a['publish_up'])
					{
						$a['publish_up'] = $dt['publish_up'];
					}

					if (!isset($a['publish_down']) || !$a['publish_down'])
					{
						$a['publish_down'] = $dt['publish_down'];
					}

					$a['section_id'] = $model->get('id');

					$dtmodel = new CoursesModelSectionDate($a['id']);
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
		}

		// Process badge info
		$badge = JRequest::getVar('badge', array(), 'post', 'array', JREQUEST_ALLOWHTML);
		if (isset($badge['published']) && $badge['published'])
		{
			// Get courses config
			$cconfig = JComponentHelper::getParams('com_courses');

			// Save the basic badge content
			$badge['section_id'] = $model->get('id');
			$badgeObj = new CoursesModelSectionBadge($badge['id']);
			$badgeObj->bind($badge);
			$badgeObj->store();

			// See if we have an image coming in as well
			$badge_image = JRequest::getVar('badge_image', false, 'files', 'array');

			// If so, proceed with saving the image
			if (isset($badge_image['name']) && $badge_image['name'])
			{
				// Get the file extension
				$pathinfo = pathinfo($badge_image['name']);
				$filename = $pathinfo['filename'];
				$ext      = $pathinfo['extension'];

				// Check for square and at least 420 x 420
				$dimensions = getimagesize($badge_image['tmp_name']);

				if ($dimensions[0] != $dimensions[1])
				{
					$this->setError("Image must be square.");
				}
				else if ($dimensions[0] < 450)
				{
					$this->setError("Image should be at least 450px.");
				}
				else
				{
					// Build the upload path if it doesn't exist
					$uploadDirectory  = JPATH_ROOT . DS . trim($cconfig->get('uploadpath', '/site/courses'), DS);
					$uploadDirectory .= DS . 'badges' . DS . $badgeObj->get('id') . DS;

					// Make sure upload directory exists and is writable
					if (!is_dir($uploadDirectory))
					{
						if (!JFolder::create($uploadDirectory))
						{
							$this->setError('Unable to create upload directory');
						}
					}
					if (!is_writable($uploadDirectory))
					{
						$this->setError("Upload directory isn't writable");
					}

					// Get the final file path
					$target_path = $uploadDirectory . 'badge.' . $ext;

					if(!$move = move_uploaded_file($badge_image['tmp_name'], $target_path))
					{
						$this->setError('Move file failed');
					}
					else
					{
						// Move successful, save the image url to the badge entry
						$img_url = DS . 'courses' . DS . 'badge' . DS . $badgeObj->get('id') . DS . 'image';
						$badgeObj->bind(array('img_url'=>$img_url));
						$badgeObj->store();
					}
				}
			}

			// Process criteria text
			if (strcmp($badgeObj->get('criteria_text'), $badge['criteria']))
			{
				$badgeObj->set('criteria_text_new', $badge['criteria']);
				$badgeObj->store();
				$badgeObj->set('criteria_text_new', NULL);
			}

			// If we don't already have a provider badge id set, then we're processing our initial badge creation
			if ($badgeObj->get('provider_name') && !$badgeObj->get('provider_badge_id') && $badgeObj->get('img_url'))
			{
				$request_type   = $cconfig->get('badges_request_type', 'oauth');
				$badgesHandler  = new \Hubzero\Badges\Wallet(strtoupper($badgeObj->get('provider_name')), $request_type);
				$badgesProvider = $badgesHandler->getProvider();

				if (is_object($badgesProvider))
				{
					$credentials->consumer_key    = $cconfig->get($badgeObj->get('provider_name').'_consumer_key', 0);
					$credentials->consumer_secret = $cconfig->get($badgeObj->get('provider_name').'_consumer_secret', 0);
					$credentials->issuerId        = $cconfig->get($badgeObj->get('provider_name').'_issuer_id');;
					$badgesProvider->setCredentials($credentials);

					$offering = CoursesModelOffering::getInstance($model->get('offering_id'));
					$course   = CoursesModelCourse::getInstance($offering->get('course_id'));

					$data                  = array();
					$data['Name']          = $course->get('title');
					$data['Description']   = trim($course->get('title')) . ' Badge';
					$data['CriteriaUrl']   = rtrim(JURI::root(), DS) . DS . 'courses' . DS . 'badge' . DS . $badgeObj->get('id') . DS . 'criteria';
					$data['Version']       = '1';
					$data['BadgeImageUrl'] = rtrim(JURI::root(), DS) . DS . trim($badgeObj->get('img_url'), DS);

					if (!$credentials->consumer_key || !$credentials->consumer_secret)
					{
						$this->setError('You must fill in the courses badge options before attempting to save a badge!');
					}
					else
					{
						try
						{
							$provider_badge_id = $badgesProvider->createBadge($data);
						}
						catch (Exception $e)
						{
							$this->setError($e->getMessage());
						}

						if ($provider_badge_id)
						{
							// We've successfully created a badge, so save that id to the database
							$badgeObj->bind(array('provider_badge_id'=>$provider_badge_id));
							$badgeObj->store();
						}
						else
						{
							$this->setError('Failed to save badge to provider. Please try saving again or make sure your badge parameters are correct.');
						}
					}
				}
			}
		}
		elseif ($badge['id']) // badge exists and is being unpublished
		{
			$badgeObj = new CoursesModelSectionBadge($badge['id']);
			$badgeObj->bind(array('published'=>0));
			$badgeObj->store();
		}

		if ($this->getError())
		{
			$this->addComponentMessage(implode('<br />', $this->getErrors()), 'error');
			$this->editTask($model);
			return;
		}

		if ($redirect)
		{
			// Output messsage and redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&offering=' . $model->get('offering_id'),
				JText::_('COM_COURSES_SECTION_SAVED')
			);
			return;
		}

		$this->editTask($model);
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
	 * Make a section the default one
	 *
	 * @return	void
	 */
	public function makedefaultTask()
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

		$row = CoursesModelSection::getInstance($id);
		$row->makeDefault();

		// Redirect back to the courses page
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&offering=' . JRequest::getInt('offering', 0)
		);
	}

	/**
	 * Publish a course
	 *
	 * @return void
	 */
	public function publishTask()
	{
		$this->stateTask(1);
	}

	/**
	 * Unpublish a course
	 *
	 * @return void
	 */
	public function unpublishTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Set the state of a course
	 *
	 * @return void
	 */
	public function stateTask($state=0)
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		// Get the single ID we're working with
		if (!is_array($ids))
		{
			$ids = array();
		}

		// Do we have any IDs?
		$num = 0;
		if (!empty($ids))
		{
			// foreach course id passed in
			foreach ($ids as $id)
			{
				// Load the course page
				$section = CoursesModelSection::getInstance($id);

				// Ensure we found the course info
				if (!$section->exists())
				{
					continue;
				}

				//set the course to be published and update
				$section->set('state', $state);
				if (!$section->store())
				{
					$this->setError(JText::_('Unable to set state for section #' . $id . '.'));
					continue;
				}

				$num++;
			}
		}

		if ($this->getErrors())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				implode('<br />', $this->getErrors()),
				'error'
			);
		}
		else
		{
			// Output messsage and redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&offering=' . JRequest::getInt('offering', 0),
				($state ? JText::sprintf('%s item(s) published', $num) : JText::sprintf('%s item(s) unpublished', $num))
			);
		}
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&offering=' . JRequest::getInt('offering', 0)
		);
	}
}
