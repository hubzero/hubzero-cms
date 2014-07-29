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

/**
 * Courses controller class for generation and viewing of certificates
 */
class CoursesControllerCertificate extends \Hubzero\Component\SiteController
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
				JRoute::_('index.php?option=' . $this->_option . '&controller=courses'),
				JText::_('COM_COURSES_ERROR_COURSE_OR_OFFERING_NOT_FOUND'),
				'error'
			);
			return;
		}

		// Ensure specified user is enrolled in the course
		//$student = $offering->member($this->juser->get('id'));
		$student = CoursesModelMember::getInstance($this->juser->get('id'), $course->get('id'), $offering->get('id'), null, 1);
		if (!$student->exists())
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=courses'),
				JText::_('COM_COURSES_ERROR_STUDENT_RECORD_NOT_FOUND'),
				'error'
			);
			return;
		}

		$certificate = $course->certificate();
		if (!$certificate->exists() || !$certificate->hasFile())
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=courses'),
				JText::_('COM_COURSES_ERROR_NO_CERTIFICATE_FOR_COURSE'),
				'error'
			);
			return;
		}

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
				if (!JFolder::create($dir))
				{
					JError::raiseError(500, JText::_('COM_COURSES_ERROR_FAILED_TO_CREATE_DIRECTORY'));
					return;
				}
			}

			$certificate->render($this->juser, $file);
		}

		// If file exists
		if (is_file($file))
		{
			$student->token();

			// Serve up the file
			$xserver = new \Hubzero\Content\Server();
			$xserver->filename($file);
			$xserver->serve_attachment($file); // Firefox and Chrome fail if served inline
			exit;
		}

		// Output failure message
		$this->view->display();
	}
}
