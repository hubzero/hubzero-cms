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

		/*if (is_object($profile))
		{
			//get users tags
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_members' . DS . 'helpers' . DS . 'tags.php');
			$mt = new MembersTags($this->database);
			$mytags = $mt->get_tag_string($profile->get("uidNumber"));

			//get users courses
			$mycourses['members'] = Hubzero_User_Helper::getCourses($profile->get("uidNumber"), 'members', 1);
			$mycourses['invitees'] = Hubzero_User_Helper::getCourses($profile->get("uidNumber"), 'invitees', 1);
			$mycourses['applicants'] = Hubzero_User_Helper::getCourses($profile->get("uidNumber"), 'applicants', 1);
			$mycourses = array_filter($mycourses);

			//get courses user may be interested in
			$interestingcourses = Hubzero_Course_Helper::getCoursesMatchingTagString($mytags, Hubzero_User_Helper::getCourses($profile->get("uidNumber")));
		}*/

		//get the popular courses
		$popularcourses = array(); //Hubzero_Course_Helper::getPopularCourses(3);

		// Output HTML
		//$this->view->option = $this->_option;
		$this->view->config   = $this->config;
		$this->view->database = $this->database;
		$this->view->user     = $this->juser;
		$this->view->title    = $this->_title;

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
		//$jconfig = JFactory::getConfig();

		// Filters 
		$this->view->filters = array();
		$this->view->filters['state']  = 1;
		$this->view->filters['search'] = JRequest::getVar('search', '');
		$this->view->filters['sortby'] = strtolower(JRequest::getWord('sortby', 'title'));
		if (!in_array($this->view->filters['sortby'], array('alias', 'title')))
		{
			$this->view->filters['sortby'] = 'title';
		}
		// Filters for returning results
		$this->view->filters['limit']  = JRequest::getInt('limit', JFactory::getConfig()->getValue('config.list_limit'));
		$this->view->filters['limit']  = ($this->view->filters['limit']) ? $this->view->filters['limit'] : 'all';
		$this->view->filters['start']  = JRequest::getInt('limitstart', 0);
		$this->view->filters['index']  = JRequest::getWord('index', '');

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

		// Push some styles to the template
		$this->_getStyles($this->_option, $this->_task . '.css');

		//build the title
		$this->_buildTitle();

		//build pathway
		$this->_buildPathway();

		// Output HTML
		$this->view->title  = $this->_title;
		$this->view->config = $this->config;
		$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
		$this->view->display();
	}
}

