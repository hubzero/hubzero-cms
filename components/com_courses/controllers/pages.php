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

/**
 * Courses controller class for pages
 */
class CoursesControllerPages extends Hubzero_Controller
{
	/**
	 * Execute a task
	 *
	 * @return	void
	 */
	public function execute()
	{
		// Get the course ID
		$this->gid = JRequest::getVar('gid', '');
		if (!$this->gid) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_ID'));
			return;
		}

		// Load the course page
		$this->course = CoursesModelCourse::getInstance($this->gid);

		// Ensure we found the course info
		if (!$this->course->exists() || !$this->course->isAvailable()) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_FOUND'));
			return;
		}

		$this->offering = $this->course->offering(JRequest::getVar('offering', ''));
		if (!$this->offering->exists()) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_OFFERING_FOUND'));
			return;
		}

		if (!$this->offering->access('manage')) 
		{
			JError::raiseError(403, JText::_('COURSES_NOT_AUTH'));
			return;
		}

		parent::execute();
	}

	/**
	 * Show an interface for managing course pages
	 * 
	 * @return     void
	 */
	public function manageTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to manage a course offering\'s pages.');
			return;
		}

		// Build the page title
		$this->_buildTitle();

		// Build the pathway
		$this->_buildPathway();

		// Push some needed styles to the template
		//$this->_getCourseStyles();

		// Push some needed scripts to the template
		//$this->_getCourseScripts();

		// Import the wiki parser
		$wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => '',
			'pagename' => $this->course->get('cn'),
			'pageid'   => $this->course->get('gidNumber'),
			'filepath' => $this->config->get('uploadpath', '/site/courses'),
			'domain'   => $this->course->get('cn')
		);

		ximport('Hubzero_Wiki_Parser');
		$p =& Hubzero_Wiki_Parser::getInstance();

		// Instantiate course page and module object
		/*$GPage = new CoursePages($this->database);

		// Get the highest page order
		$high_order_pages = $GPage->getHighestPageOrder($this->course->get('gidNumber'));

		// Get a subtask if there is one
		$sub_task = JRequest::getVar('sub_task');
		$page_id  = JRequest::getVar('page');

		// Set the grou for changing state
		//$this->_course = $course;

		// Perform task based on sub task
		switch ($sub_task)
		{
			case 'add_page':        $this->editTask($this->course); return;
			case 'edit_page':       $this->editTask($this->course); return;
			case 'save_page':       $this->saveTask();        return;

			case 'deactivate_page': $this->change_state('page', 'deactivate', $page_id);         break;
			case 'activate_page':   $this->change_state('page', 'activate', $page_id);           break;
			case 'down_page':       $this->reorder('page', 'down', $page_id, $high_order_pages); break;
			case 'up_page':         $this->reorder('page', 'up', $page_id, $high_order_pages);   break;
		}

		// Get the course pages
		$pages = $GPage->getPages($this->course->get('gidNumber'));*/
		
		$this->view->filters['state'] = JRequest::getCmd('state', 'active');

		// Seperate active/inactive pages
		//$active_pages = array();
		//$inactive_pages = array();

		$state = 1;
		if ($this->view->filters['state'] == 'inactive')
		{
			$state = 0;
		}
		$this->view->pages = $this->offering->pages(array('active' => $state));

		/*foreach ($pages as $page)
		{
			if ($page['active'] == $state) 
			{
				array_push($pages, $page);
			} 
		}*/

		// Get the highest page order
		//$high_order_pages = $GPage->getHighestPageOrder($this->course->get('gidNumber'));

		// Output HTML
		$this->view->title  = stripslashes($this->course->get('title')) . ': ' . stripslashes($this->offering->get('title'));
		//$this->view->course = $course;

		//$this->view->active_pages     = $active_pages;
		//$this->view->inactive_pages   = $inactive_pages;
		$this->view->high_order_pages = 0; //$high_order_pages;

		$this->view->parser = $p;
		$this->view->wikiconfig = $wikiconfig;

		$this->view->course = $this->course;
		$this->view->offering = $this->offering;
		$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
		$this->view->display();
	}

	/**
	 * Show a form for editing a course page
	 * 
	 * @param      object $course Hubzero_Course
	 * @return     void
	 */
	public function editTask($page=null)
	{
		if (is_a($page, 'CoursesModelPage'))
		{
			$this->view->page = $page;
		}
		else
		{
			$this->view->page = new CoursesModelPage(
				$this->course->get('id'), 
				$this->offering->get('id'), 
				JRequest::getVar('page', '')
			);
		}

		/*$page = JRequest::getVar('page','','get');

		if ($page) 
		{
			$GPage = new CoursePages($this->database);
			$GPage->load($page);

			$page = array();
			$page['id']      = $GPage->id;
			$page['gid']     = $GPage->gid;
			$page['url']     = $GPage->url;
			$page['title']   = $GPage->title;
			$page['content'] = $GPage->content;
			$page['porder']  = $GPage->porder;
			$page['active']  = $GPage->active;
			$page['privacy'] = $GPage->privacy;
		}

		if ($this->page) 
		{
			$page = $this->page;
		}*/

		$this->view->title  = $this->_title;
		$this->view->course = $this->course;
		$this->view->offering = $this->offering;
		//$this->view->page = $page;
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$this->view->display();
	}

	/**
	 * Save a course page
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Get the page vars being posted
		$page = JRequest::getVar('page', array(), 'post', 'none', 2);

		$page = new CoursesModelPage($this->course->get('id'), $this->offering->get('id'));
		if (!$page->bind($data))
		{
			$this->addComponentMessage($page->getError(), 'error');
			$this->editTask($page);
			return;
		}

		// Check if the page title is set
		/*if ($page['title'] == '') 
		{
			$this->addComponentMessage('You must enter a page title.','error');
			$this->page = $page;
			$this->editTask($course);
			return;
		}

		// Default task
		$task = 'update';

		// Instantiate db and course page objects
		$GPage = new CoursePages($this->database);

		// If new page we must create extra vars
		if ($page['new']) 
		{
			$high = $GPage->getHighestPageOrder($this->course->get('gidNumber'));

			$page['gid'] = $this->course->get('gidNumber');
			$page['active'] = 1;
			$page['porder'] = ($high + 1);

			$task = 'create';
		}

		// Get the course pages
		$pages = $GPage->getPages($this->course->get('gidNumber'));

		//check to see if user supplied url
		if (isset($page['url']) && $page['url'] != '')
		{
			$page['url'] = strtolower(str_replace(' ', '_', trim($page['url'])));
		}
		else
		{
			$page['url'] = strtolower(str_replace(' ', '_', trim($page['title'])));
		}

		$page['url'] = preg_replace("/[^a-zA-Z0-9_]/", '', $page['url']);

		// Get unique page name
		$page['url'] = $this->_uniquePageURL($page['url'],$pages, $course, $page['id']);*/

		// Save the page
		if (!$page->store()) 
		{
			/*$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&gid=' . $this->course->get('cn') . '&task=managepages&offering='),
				JText::sprintf('An error occurred while trying to %s the page.', $task), 
				'error'
			);
			return;*/
			$this->addComponentMessage($page->getError(), 'error');
			$this->editTask($page);
			return;
		}

		// Push success message and redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&gid=' . $this->course->get('cn') . '&task=managepages'),
			JText::sprintf('You have successfully %s the page.', $task . 'd'), 
			'passed'
		);
	}

	/**
	 * Reorder items in a list
	 * 
	 * @param      string  $type       Items being reordered
	 * @param      string  $direction  Direction to move item
	 * @param      string  $id         Item ID
	 * @param      integer $high_order Highest order
	 * @return     void
	 */
	public function reorderTask($type, $direction, $id, $high_order)
	{
		$order_field = substr($type, 0, 1) . 'order';

		// Get the current order of the object trying to reorder
		$sql = "SELECT $order_field FROM #__courses_" . $type . "s WHERE id='" . $id . "'";
		$this->database->setQuery($sql);
		$order = $this->database->loadAssoc();

		// Set the high and low that the order can be
		$lowest_order = 1;
		$highest_order = $high_order;

		// Set the old order
		$old_order = $order[$order_field];

		// Get the new order depending on the direction of reordering
		// Make sure we are with our high and low limits
		if ($direction == 'down') 
		{
			$new_order = $old_order + 1;
			if ($new_order > $highest_order) 
			{
				$new_order = $highest_order;
			}
		} 
		else 
		{
			$new_order = $old_order - 1;
			if ($new_order < $lowest_order) 
			{
				$new_order = $lowest_order;
			}
		}

		// Check to see if another object holds the order we are trying to move to
		$sql = "SELECT *  FROM #__courses_" . $type . "s WHERE $order_field='" . $new_order . "' AND gid='" . $this->_course->get('gidNumber') . "'";
		$this->database->setQuery($sql);
		$new = $this->database->loadAssoc();

		// If there isnt an object there then just update
		if ($new['id'] == '') 
		{
			$sql = "UPDATE #__courses_" . $type . "s SET $order_field='" . $new_order . "' WHERE id='" . $id . "'";
			$this->database->setQuery($sql);

			if (!$this->database->Query()) 
			{
				$this->addComponentMessage('An error occurred while trying to reorder the ' . $type . '. Please try again', 'error');
			} 
			else 
			{
				$this->addComponentMessage('The ' . $type . ' was successfully reordered.', 'passed');
			}
		} 
		else 
		{
			// Otherwise basically switch the two objects orders
			$sql = "UPDATE #__courses_" . $type . "s SET $order_field='" . $new_order . "' WHERE id='" . $id . "'";
			$this->database->setQuery($sql);
			$this->database->Query();

			$sql = "UPDATE #__courses_" . $type . "s SET $order_field='" . $old_order . "' WHERE id='" . $new['id'] . "'";
			$this->database->setQuery($sql);

			if (!$this->database->Query()) 
			{
				$this->addComponentMessage('An error occurred while trying to reorder the ' . $type . '. Please try again','error');
			} 
			else 
			{
				$this->addComponentMessage('The ' . $type . ' was successfully reordered.', 'passed');
			}
		}

		// Redirect back to manage pages area
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&gid=' . $this->_course->get('cn') . '&task=managepages')
		);
	}

	/**
	 * Generate a unique page URL
	 * 
	 * @param      string $current_url Current URL
	 * @param      array  $course_pages List of course pages
	 * @param      object $course       Hubzero_Course
	 * @return     string
	 */
	private function _uniquePageURL($current_url, $course_pages, $course, $current_id = null)
	{
		//remove the current page so we dont check it
		foreach ($course_pages as $k => $v)
		{
			if ($current_id != null && $current_id == $v['id'])
			{
				unset($course_pages[$k]);
			}
		}

		// Get the page urls
		$page_urls = array_keys($course_pages);

		// Get plugin names
		$plugin_names = array_keys(Hubzero_Course_Helper::getPluginAccess($course));

		if (in_array($current_url, $plugin_names)) 
		{
			$current_url = $current_url . '_page';
			return $this->_uniquePageURL($current_url, $course_pages, $course);
		}

		// Check if current url is already taken
		// otherwise return current url
		if (in_array($current_url, $page_urls)) 
		{
			// Split up the current url
			$url_parts = explode('_', $current_url);

			// Get the last part of the split url
			$num = end($url_parts);

			// If last part is numeric we need to remove that part from array and increment number then append back on end of url
			// else append a number to the end of the url
			if (is_numeric($num)) 
			{
				$num++;
				$oldNum = array_pop($url_parts);
				$url  = implode('_', $url_parts);
				$url .= "_{$num}";
			} 
			else 
			{
				$count = 1;
				$url  = implode('_', $url_parts);
				$url .= "_{$count}";
			}

			// Run the function again to see if we now have a unique url
			return $this->_uniquePageURL($url, $course_pages, $course);
		} 
		else 
		{
			return $current_url;
		}
	}
}

