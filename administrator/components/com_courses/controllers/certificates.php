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

//require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'section.php');
//require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'offering.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');

/**
 * Courses controller class for managing membership and course info
 */
class CoursesControllerCertificates extends Hubzero_Controller
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
		$this->view->filters['course']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.course',
			'course',
			0
		);

		$this->view->course = CoursesModelCourse::getInstance($this->view->filters['course']);
		if (!$this->view->course->exists())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=courses'
			);
			return;
		}

		$this->view->filters['offering']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.offering',
			'offering',
			0
		);
		$this->view->offering = CoursesModelOffering::getInstance($this->view->filters['offering']);
		/*$this->view->course = CoursesModelCourse::getInstance($this->view->offering->get('course_id'));

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
		}*/

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new course
	 *
	 * @return	void
	 */
	/*public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Displays an edit form
	 *
	 * @return	void
	 */
	/*public function editTask($model=null)
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
	/*public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');

		// Instantiate an Hubzero_Course object
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
	/*public function deleteTask()
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
					JError::raiseError(500, JText::_('Unable to delete code'));
					return;
				}

				$num++;
			}
		}

		// Redirect back to the courses page
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section=' . JRequest::getInt('section', 0),
			JText::sprintf('%s Item(s) removed.', $num)
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function previewTask()
	{
		$this->view->course = CoursesModelCourse::getInstance(JRequest::getInt('course', 0));
		if (!$this->view->course->exists())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=courses'
			);
			return;
		}
		//$this->view->offering = $this->view->course->offering(JRequest::getInt('offering', 0));

		/*$jconfig =& JFactory::getConfig();

		// Build the link displayed
		$juri =& JURI::getInstance();
		$sef = JRoute::_('index.php?option=' . $this->_option);
		if (substr($sef, 0, 1) == '/')
		{
			$sef = substr($sef, 1, strlen($sef));
		}
		$webpath = str_replace('/administrator/', '/', $juri->base() . $sef);
		$webpath = str_replace('//', '/', $webpath);
		if (isset($_SERVER['HTTPS']))
		{
			$webpath = str_replace('http:', 'https:', $webpath);
		}
		if (!strstr($webpath, '://'))
		{
			$webpath = str_replace(':/', '://', $webpath);
		}
		
		//require_once(JPATH_ROOT . DS . 'libraries/tcpdf/tcpdf.php');
		$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); //PDF_PAGE_ORIENTATION == 'P'

		// set default header data
		$pdf->SetHeaderData(NULL, 0, stripslashes($this->view->course->get('title')), NULL, array(84, 94, 124), array(146, 152, 169));
		$pdf->setFooterData(array(255, 255, 255), array(255, 255, 255));

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(10);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// Set font
		$pdf->SetFont('dejavusans', '', 11, '', true);

		$pdf->AddPage();

		$this->view->student = $this->juser;

		$html = $this->view->loadTemplate();

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');

		// ---------------------------------------------------------

		$dir = JPATH_ROOT . DS . 'site' . DS . 'courses' . DS . 'certificates';
		$tempFile = $dir . DS . 'certificate_' . $this->view->course->get('id') . '_' . $this->juser->get('id') . '.pdf'; 

		if (!is_dir($dir))
		{
			if (!JFolder::create($dir, 0755))
			{
				jimport('joomla.filesystem.folder');
				JError::raiseError(500, 'Failed to create folder to store receipts');
				return;
			}
		}

		// Close and output PDF document
		$pdf->Output($tempFile, 'F');

		if (is_file($tempFile))
		{
			// Get some needed libraries
			ximport('Hubzero_Content_Server');
			
			$xserver = new Hubzero_Content_Server();
			$xserver->filename($tempFile);		
			$xserver->serve_inline($tempFile);
			exit;
		}
		else
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('There was an error creating the certificate'), 'error'
			);
			return;
		}*/
		//build url to newsletter with no html
		//$url = 'https://' . $_SERVER['HTTP_HOST'] . DS . 'administrator' . DS . 'index.php?option=com_courses&controller=certificates&task=render&no_html=1&course=' . $this->view->course->get('id');

		$url = 'https://' . $_SERVER['HTTP_HOST'] . DS . 'index.php?option=com_courses&controller=certificate&task=render&no_html=1&course=' . $this->view->course->get('id') . '&offering=' . $this->view->offering->get('id') . '&u=' . $this->juser->get('id');

		//path to newsletter file
		$dir = JPATH_ROOT . DS . 'site' . DS . 'courses' . DS . 'certificates';
		$tempFile = $dir . DS . 'certificate_' . $this->view->course->get('id') . '_' . $this->juser->get('id') . '.pdf'; 

		if (!is_dir($dir))
		{
			if (!JFolder::create($dir, 0755))
			{
				jimport('joomla.filesystem.folder');
				JError::raiseError(500, 'Failed to create folder to store receipts');
				return;
			}
		}

		if (is_file($tempFile))
		{
			jimport('joomla.filesystem.file');
			if (!JFile::delete($tempFile))
			{
				$this->setError(JText::_('UNABLE_TO_DELETE_FILE'));
			}
		}

		$cmd = JPATH_ROOT . '/vendor/bin/phantomjs_64 ';
		$rasterizeFile = JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'assets' . DS . 'js' . DS . 'rasterize.js';
		$finalCommand = $cmd . ' ' . $rasterizeFile . ' "' . $url . '" ' . $tempFile . ' 11in*8.5in'; //65
		//echo $finalCommand;
		exec($finalCommand, $output);
//var_dump($output);
		//output as attachment
		header("Content-type: application/pdf");
		header("Content-Disposition: attachment; filename=" . 'certificate_' . $this->view->course->get('id') . '_' . $this->juser->get('id') . ".pdf");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo file_get_contents($tempFile);
		exit();
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function renderTask()
	{
		$this->view = new JView(array(
			'base_path' => JPATH_ROOT . DS . 'components' . DS . $this->_option,
			'name'      => 'certificate',
			'layout'    => 'render'
		));
		$this->view->option = $this->_option;

		$this->view->course = CoursesModelCourse::getInstance(JRequest::getInt('course', 0));
		if (!$this->view->course->exists())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=courses'
			);
			return;
		}

		$this->view->student = CoursesModelMember::getInstance($this->juser->get('id'), $this->view->course->get('id'));
		$this->view->juser = $this->juser;
		$this->view->display();
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
