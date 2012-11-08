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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'courses.php');
//require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');

/**
 * Courses controller class
 */
class CoursesControllerCourses extends Hubzero_Controller
{
	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->_authorize();
		$this->_authorize('course');

		$this->registerTask('__default', 'intro');

		parent::execute();
	}

	/**
	 * Method to set the document path
	 * 
	 * @param      array $course_pages List of roup pages
	 * @return     void
	 */
	public function _buildPathway($course_pages = array())
	{
		$pathway =& JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);

			if ($this->_task == 'new') 
			{
				$pathway->addItem(
					JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
					'index.php?option=' . $this->_option . '&task=' . $this->_task
				);
			}
		}
	}

	/**
	 * Method to build and set the document title
	 * 
	 * @return     void
	 */
	public function _buildTitle()
	{
		//set title used in view
		$this->_title = JText::_(strtoupper($this->_option));

		if ($this->_task && $this->_task != 'intro') 
		{
			$this->_title = JText::_(strtoupper($this->_option . '_' . $this->_task));
		}

		//set title of browser window
		$document =& JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Redirect to login page
	 * 
	 * @return     void
	 */
	public function loginTask($message = '')
	{
		$return = base64_encode(JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . 'task=' . $this->_task));
		$this->setRedirect(
			JRoute::_('index.php?option=com_login&return=' . $return),
			$message,
			'warning'
		);
		return;
	}

	/**
	 * Display component main page
	 * 
	 * @return     void
	 */
	public function introTask()
	{
		//build the title
		$this->_buildTitle();

		//build pathway
		$this->_buildPathway();

		// Push some needed styles to the template
		$this->_getStyles($this->_option, 'intro.css');

		// Push some needed scripts to the template
		//$this->_getScripts();

		//vars
		$mytags = '';
		$mycourses = array();
		$popularcourses = array();
		$interestingcourses = array();

		//get the users profile
		$profile = Hubzero_User_Profile::getInstance($this->juser->get('id'));

		if (is_object($profile))
		{
			//get users tags
			/*include_once(JPATH_ROOT . DS . 'components' . DS . 'com_members' . DS . 'helpers' . DS . 'tags.php');
			$mt = new MembersTags($this->database);
			$mytags = $mt->get_tag_string($profile->get("uidNumber"));

			//get users courses
			$mycourses['members'] = Hubzero_User_Helper::getCourses($profile->get("uidNumber"), 'members', 1);
			$mycourses['invitees'] = Hubzero_User_Helper::getCourses($profile->get("uidNumber"), 'invitees', 1);
			$mycourses['applicants'] = Hubzero_User_Helper::getCourses($profile->get("uidNumber"), 'applicants', 1);
			$mycourses = array_filter($mycourses);

			//get courses user may be interested in
			$interestingcourses = Hubzero_Course_Helper::getCoursesMatchingTagString($mytags, Hubzero_User_Helper::getCourses($profile->get("uidNumber")));*/
		}

		//get the popular courses
		$popularcourses = array(); //Hubzero_Course_Helper::getPopularCourses(3);

		// Output HTML
		//$this->view->option = $this->_option;
		$this->view->config = $this->config;
		$this->view->database = $this->database;
		$this->view->user = $this->juser;
		$this->view->title = $this->_title;
		$this->view->mycourses = $mycourses;
		$this->view->popularcourses = $popularcourses;
		$this->view->interestingcourses = $interestingcourses;
		$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
		$this->view->display();
	}

	/**
	 * Display a list of courses on the site and options for filtering/browsing them
	 * 
	 * @return     void
	 */
	public function browseTask()
	{
		$jconfig = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		//$this->view->filters['type']   = array(1,3);
		//$this->view->filters['authorized'] = "";

		// Filters for getting a result count
		//$this->view->filters['limit']  = 'all';
		//$this->view->filters['fields'] = array('COUNT(*)');
		$this->view->filters['state'] = 1;
		$this->view->filters['search'] = JRequest::getVar('search', '');
		$this->view->filters['sortby'] = strtolower(JRequest::getWord('sortby', 'title'));
		if (!in_array($this->view->filters['sortby'], array('alias', 'title')))
		{
			$this->view->filters['sortby'] = 'title';
		}
		// Filters for returning results
		$this->view->filters['limit']  = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$this->view->filters['limit']  = ($this->view->filters['limit']) ? $this->view->filters['limit'] : 'all';
		$this->view->filters['start']  = JRequest::getInt('limitstart', 0);
		$this->view->filters['policy'] = strtolower(JRequest::getWord('policy', ''));
		if (!in_array($this->view->filters['policy'], array('open', 'restricted', 'invite', 'closed')))
		{
			$this->view->filters['policy'] = '';
		}
		$this->view->filters['index']  = htmlentities(JRequest::getVar('index', ''));

		$model = CoursesModelCourses::getInstance();

		// Get a record count
		$this->view->filters['count'] = true;
		$this->view->total   = $model->courses($this->view->filters);

		// Get records
		$this->view->filters['count'] = false;
		$this->view->courses = $model->courses($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		// Run through the master list of courses and mark the user's status in that course
		//$this->view->authorized = $this->_authorize();
		/*if (!$this->juser->get('guest') && $this->view->courses) 
		{
			$this->view->courses = $this->_getCourses($this->view->courses);
		}*/

		// Push some styles to the template
		$this->_getStyles($this->_option, $this->_task . '.css');

		//build the title
		$this->_buildTitle();

		//build pathway
		$this->_buildPathway();

		// Output HTML
		$this->view->title = $this->_title;
		$this->view->config = $this->config;
		$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
		$this->view->display();
	}

	/**
	 * Short description for 'getCourses'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $courses Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	private function _getCourses($courses)
	{
		if (!$this->juser->get('guest')) 
		{
			$profile = Hubzero_User_Profile::getInstance($this->juser->get('id'));

			$ugs = $profile->getGroups('all');

			for ($i = 0; $i < count($courses); $i++)
			{
				if (!isset($courses[$i]->cn)) 
				{
					$courses[$i]->cn = '';
				}
				$courses[$i]->registered   = 0;
				$courses[$i]->regconfirmed = 0;
				$courses[$i]->manager      = 0;

				if ($ugs && count($ugs) > 0) 
				{
					foreach ($ugs as $ug)
					{
						if (is_object($ug) && $ug->cn == $courses[$i]->cn) 
						{
							$courses[$i]->registered   = $ug->registered;
							$courses[$i]->regconfirmed = $ug->regconfirmed;
							$courses[$i]->manager      = $ug->manager;
						}
					}
				}
			}
		}

		return $courses;
	}

	/**
	 * Set access permissions for a user
	 * 
	 * @return     void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);
		$this->config->set('access-create-' . $assetType, false);
		$this->config->set('access-manage-' . $assetType, false);
		$this->config->set('access-admin-' . $assetType, false);

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
				$this->config->set('access-create-' . $assetType, true);
				if ($this->juser->authorize($this->_option, 'manage')) 
				{
					$this->config->set('access-manage-' . $assetType, true);
					$this->config->set('access-admin-' . $assetType, true);
				}
			}
		}
	}
}

