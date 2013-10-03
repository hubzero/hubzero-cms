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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');

/**
 * Courses controller class for generation and viewing of certificates
 */
class CoursesControllerCertificate extends Hubzero_Controller
{
	/**
	 * Displays a list of courses
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		$course   = CoursesModelCourse::getInstance(JRequest::getVar('course', ''));
		$offering = $course->offering(JRequest::getVar('offering', ''));

		// Ensure the course exists
		if (!$course->exists() || !$offering->exists())
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=courses')
			);
			return;
		}

		// Ensure specified user is enrolled in the course
		$student = $offering->member($this->juser->get('id'));
		if (!$student->exists())
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=courses')
			);
			return;
		}

		//$juser = JUser::getInstance(JRequest::getInt('u', 0));

		// Path and file name
		$dir = JPATH_ROOT . DS . 'site' . DS . 'courses' . DS . 'certificates';
		$file = $dir . DS . 'certificate_' . $course->get('id') . '_' . $offering->get('id') . '_' . $this->juser->get('id') . '.pdf'; 

		// If the file exists and we want to force regenerate it
		if (is_file($file) && JRequest::getInt('regenerate', 0))
		{
			jimport('joomla.filesystem.file');
			if (!JFile::delete($file))
			{
				JError::raiseError(500, JText::_('UNABLE_TO_DELETE_FILE'));
				return;
			}
		}

		// Does the file exist already?
		if (!is_file($file))
		{
			// Create the upload directory if needed
			if (!is_dir($dir))
			{
				jimport('joomla.filesystem.folder');
				if (!JFolder::create($dir, 0755))
				{
					JError::raiseError(500, JText::_('Failed to create folder to store receipts'));
					return;
				}
			}

			// Build the render URL
			$juri =& JURI::getInstance();
			$url  = rtrim(str_replace('http:', 'https:', $juri->base()), DS) . DS;
			$url .= 'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=render&no_html=1';
			// Course / Offering / Student
			$url .= '&course=' . $course->get('id') . '&offering=' . $offering->get('id') . '&u=' . $this->juser->get('id');
			// Validation key (lock on a screen door)
			$url .= '&key='. JUtility::getHash($course->get('id') . $offering->get('id') . $this->juser->get('id'));

			// Script execution
			$cmd = JPATH_ROOT . '/vendor/bin/phantomjs_64 ';
			$rasterizeFile = JPATH_ROOT . DS . 'components' . DS . $this->_option . DS . 'assets' . DS . 'js' . DS . 'rasterize.js';
			$finalCommand = $cmd . ' ' . $rasterizeFile . ' "' . $url . '" ' . $file . ' 11in*8.5in'; //65

			exec($finalCommand, $output);
		}

		// If file exists
		if (is_file($file))
		{
			// Serve up the file
			ximport('Hubzero_Content_Server');

			$xserver = new Hubzero_Content_Server();
			$xserver->filename($file);
			$xserver->serve_inline($file);
			exit;
		}

		// Output failure message
		$this->view->display();
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function renderTask()
	{
		// Get the course
		$this->view->course   = CoursesModelCourse::getInstance(JRequest::getVar('course', ''));
		$this->view->offering = $this->view->course->offering(JRequest::getVar('offering', ''));

		// Ensure the course exists
		if (!$this->view->course->exists() || !$this->view->offering->exists())
		{
			JError::raiseError(404, JText::_('Course does not exist.'));
			return;
		}

		// Ensure specified user is enrolled in the course
		$this->view->student = $this->view->offering->member(JRequest::getInt('u', 0));
		if (!$this->view->student->exists())
		{
			JError::raiseError(404, JText::_('User is not a student of specified course.'));
			return;
		}

		// Load the JUser object for name, etc.
		$this->view->juser = JUser::getInstance(JRequest::getInt('u', 0));

		// Check the hash
		$hash = JUtility::getHash($this->view->course->get('id') . $this->view->offering->get('id') . $this->view->juser->get('id'));
		if ($hash != JRequest::getVar('key'))
		{
			JError::raiseError(403, JText::_('Access denied.'));
			return;
		}

		// Display
		$this->view->display();
	}
}
