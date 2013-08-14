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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

// Include required forms models
require_once(JPATH_COMPONENT . DS . 'models' . DS . 'form.php');
require_once(JPATH_COMPONENT . DS . 'models' . DS . 'formRespondent.php');
require_once(JPATH_COMPONENT . DS . 'models' . DS . 'formDeployment.php');

/**
 * Courses form controller class
 */
class CoursesControllerForm extends Hubzero_Controller {
	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		// Get the user
		$this->juser = JFactory::getUser();

		parent::execute();
	}

	/**
	 * Method to set the document path
	 * 
	 * @return     void
	 */
	public function _buildPathway()
	{
		$pathway =& JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}

		$pathway->addItem(
			JText::_('Forms'),
			'index.php?option=' . $this->_option . '&controller=form'
		);

		if($this->_task != 'index')
		{
			$pathway->addItem(
				JText::_(ucfirst($this->_task)),
				'index.php?option=' . $this->_option . '&controller=form&task=' . $this->_task
			);
		}
	}

	/**
	 * Method to build and set the document title
	 * 
	 * @return     void
	 */
	public function _buildTitle($append='')
	{
		// Set the title used in the view
		$this->_title = JText::_('Forms');

		if(!empty($append))
		{
			$this->_title .= ': ' . $append;
		}

		//set title of browser window
		$document =& JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Default index view of all forms
	 * 
	 * @return     void
	 */
	public function indexTask()
	{
		// Check authorization
		// @FIXME: only admins should see ALL exams
		$this->authorize();

		// Add stylesheets and scripts
		$this->_getStyles($this->_option, $this->_controller . '.css');
		$this->_getScripts('assets/js/select');

		// Add tablesorter
		Hubzero_Document::addSystemStylesheet('tablesorter.themes.blue.css');
		Hubzero_Document::addSystemScript('jquery.tablesorter.min');

		// Set the title and pathway
		$this->_buildTitle('Upload a PDF');
		$this->_buildPathway();

		if(!isset($this->view->errors))
		{
			$this->view->errors = array();
		}

		$this->view->title = $this->_title;

		// Display
		$this->view->display();
	}

	/**
	 * Upload a PDF and render images
	 * 
	 * @return     void
	 */
	public function uploadTask()
	{
		// Check authorization
		$this->authorize();
		$pdf = PdfForm::fromPostedFile('pdf');

		// No error, then render the images
		if (!$pdf->hasErrors())
		{
			$pdf->renderPageImages();
		}

		// If there were errors, jump back to the index view and display them
		if ($pdf->hasErrors())
		{
			$this->setView('form', 'index');
			$this->view->errors = $pdf->getErrors();
			$this->indexTask();
		}
		else
		{
			// Just return JSON
			if (JRequest::getInt('no_html', false))
			{
				echo json_encode(array('success'=>true, 'id'=>$pdf->getId()));
				exit();
			}
			else // Otherwise, redirect
			{
				$this->setRedirect(
					JRoute::_('index.php?option=com_courses&controller=form&task=layout&formId=' . $pdf->getId(), false),
					JText::_('PDF upload successfull'),
					'passed'
				);
				return;
			}
		}
	}

	/**
	 * PDF layout view, annotate rendered images
	 * 
	 * @return     void
	 */
	public function layoutTask()
	{
		// Check authorization
		$this->authorize();

		// Add stylesheets and scripts
		$this->_getStyles($this->_option, $this->_controller . '.css');
		$this->_getScripts('assets/js/' . $this->_task);

		// Set the title and pathway
		$this->_buildTitle();
		$this->_buildPathway();

		$this->view->pdf      = new PdfForm($this->assertFormId());
		$this->view->title    = $this->view->pdf->getTitle();
		$this->view->readonly = JRequest::getInt('readonly', false);
		$this->view->display();
	}

	/**
	 * Save layout
	 * 
	 * @return     void
	 */
	public function saveLayoutTask()
	{
		// Check authorization
		$this->authorize();

		$pdf = $this->assertExistentForm(); 
		$pdf->setTitle($_POST['title']);

		if (isset($_POST['pages']))
		{
			$pdf->setPageLayout($_POST['pages']);
		}

		echo json_encode(array('result'=>'success'));
		exit();
	}

	/**
	 * Deploy form view
	 * 
	 * @return     void
	 */
	public function deployTask($dep=NULL)
	{
		// Check authorization
		$this->authorize();

		// Set the title and pathway
		$this->_buildTitle();
		$this->_buildPathway();

		// Add stylesheets and scripts
		$this->_getStyles($this->_option, $this->_controller . '.css');
		$this->_getScripts('assets/js/timepicker');
		$this->_getScripts('assets/js/' . $this->_task);

		// Add tablesorter
		Hubzero_Document::addSystemStylesheet('tablesorter.themes.blue.css');
		Hubzero_Document::addSystemScript('jquery.tablesorter.min');

		$this->view->pdf   = $this->assertExistentForm();
		$this->view->dep   = ($dep) ? $dep : new PdfFormDeployment;
		$this->view->title = $this->view->pdf->getTitle();

		$this->view->display();
	}

	/**
	 * Create deployment
	 * 
	 * @return     void
	 */
	public function createDeploymentTask()
	{
		if(!$deployment = JRequest::getVar('deployment'))
		{
			JError::raiseError(422, 'No deployment provided');
		}

		$pdf = $this->assertExistentForm(); 
		$dep = PdfFormDeployment::fromFormData($pdf->getId(), $deployment);

		if ($dep->hasErrors())
		{
			$this->setView('form', 'deploy');
			$this->view->task = 'deployTask';
			$this->deployTask($dep);
			return;
		}
		else
		{
			if (JRequest::getInt('no_html', false))
			{
				echo json_encode(array('success'=>true, 'id'=>$dep->save(), 'formId'=>$pdf->getId()));
				exit();
			}
			else
			{
				$tmpl = (JRequest::getWord('tmpl', false)) ? '&tmpl=component' : '';
				$this->setRedirect(
					JRoute::_('index.php?option=com_courses&controller=form&task=showDeployment&id='.$dep->save().'&formId='.$pdf->getId().$tmpl, false),
					JText::_('Deployment successfully created'),
					'passed'
				);
				return;
			}
		}
	}

	/**
	 * Update an existing deployment
	 * 
	 * @return     void
	 */
	public function updateDeploymentTask()
	{
		if(!$deployment = JRequest::getVar('deployment'))
		{
			JError::raiseError(422, 'No deployment provided');
		}

		if(!$deploymentId = JRequest::getInt('deploymentId'))
		{
			JError::raiseError(422, 'No deployment ID provided');
		}

		$pdf = $this->assertExistentForm(); 
		$dep = PdfFormDeployment::fromFormData($pdf->getId(), $deployment);

		if ($dep->hasErrors(NULL, TRUE))
		{
			$this->setView('form', 'showDeployment');
			$dep->setId($deploymentId);
			$this->showDeploymentTask($dep);
			return;
		}
		else
		{
			$tmpl = (JRequest::getWord('tmpl', false)) ? '&tmpl=component' : '';
			$this->setRedirect(
				JRoute::_('index.php?option=com_courses&controller=form&task=showDeployment&id='.$dep->save($deploymentId).'&formId='.$pdf->getId().$tmpl, false),
				JText::_('Deployment successfully updated'),
				'passed'
			);
			return;
		}
	}

	/**
	 * Show deployment
	 * 
	 * @return     void
	 */
	public function showDeploymentTask($dep=NULL)
	{
		if(!$id = JRequest::getInt('id', false))
		{
			JError::raiseError(422, 'No form identifier supplied');
		}

		// Set the title and pathway
		$this->_buildTitle();
		$this->_buildPathway();

		// Add styles and scripts
		$this->_getStyles($this->_option, $this->_controller . '.css');
		$this->_getScripts('assets/js/' . $this->_task);
		$this->_getScripts('assets/js/deploy');
		$this->_getScripts('assets/js/timepicker');

		// Add tablesorter
		Hubzero_Document::addSystemStylesheet('tablesorter.themes.blue.css');
		Hubzero_Document::addSystemScript('jquery.tablesorter.min');

		$this->view->pdf   = $this->assertExistentForm();
		$this->view->title = $this->view->pdf->getTitle();
		$this->view->dep   = ($dep) ? $dep : PdfFormDeployment::load($id);

		// Display
		$this->view->display();
	}

	/**
	 * Take a form/exam
	 * 
	 * @return     void
	 */
	public function completeTask()
	{
		if(!$crumb = JRequest::getVar('crumb', false))
		{
			JError::raiseError(422);
		}

		// First, attempt to compute section
		$section = FormHelper::getSection($crumb);

		$dep = PdfFormDeployment::fromCrumb($crumb, $section);
		$dbg = JRequest::getVar('dbg', false);

		switch ($dep->getState())
		{
			case 'pending':
				JError::raiseError(422, 'This deployment is not yet available');
			case 'expired':
				$this->setView('results', $dep->getResultsClosed());

				// Set the title and pathway
				$this->_buildTitle();
				$this->_buildPathway();

				$this->_getStyles($this->_option, $this->_controller . '.css');
				$this->_getScripts('assets/js/' . $this->_task);

				$this->view->dep   = $dep;
				$this->view->pdf   = $dep->getForm();
				$this->view->title = $this->view->pdf->getTitle();
				$this->view->dep   = $dep;

				// Display
				$this->view->display();
			break;
			case 'active':
				$resp = $dep->getRespondent();
				if($resp->getEndTime())
				{
					$this->setView('results', $dep->getResultsOpen());

					// Set the title and pathway
					$this->_buildTitle();
					$this->_buildPathway();

					$this->_getStyles($this->_option, $this->_controller . '.css');
					$this->_getScripts('assets/js/' . $this->_task);

					$this->view->incomplete = array();
					$this->view->pdf = $dep->getForm();
					$this->view->title = $this->view->pdf->getTitle();
					$this->view->dep = $dep;
					$this->view->display();
					return;
				}
				else
				{
					// Set the title and pathway
					$this->_buildTitle();
					$this->_buildPathway();

					$this->_getStyles($this->_option, $this->_controller . '.css');
					$this->_getScripts('assets/js/' . $this->_task);

					$this->view->pdf   = $dep->getForm();
					$this->view->title = $this->view->pdf->getTitle();
					$this->view->dep   = $dep;
					$this->view->incomplete = (isset($this->view->incomplete)) ? $this->view->incomplete : array();
					$this->view->display();
				}
		}
	}

	/**
	 * Mark the start of a time form
	 * 
	 * @return     void
	 */
	public function startWorkTask()
	{
		if(!$crumb = JRequest::getVar('crumb', false))
		{
			JError::raiseError(422);
		}

		PdfFormDeployment::fromCrumb($crumb)->getRespondent()->markStart();

		$tmpl = (JRequest::getWord('tmpl', false)) ? '&tmpl=' . JRequest::getWord('tmpl') : '';

		$this->setRedirect(
			JRoute::_('index.php?option=com_courses&controller=form&task=complete&crumb=' . $_POST['crumb'] . $tmpl, false)
		);
		return;
	}

	/**
	 * Save progress, called via JS ajax
	 * 
	 * @return     void
	 */
	public function saveProgressTask()
	{
		if (!isset($_POST['crumb']) || !isset($_POST['question']) || !isset($_POST['answer']))
		{
			echo "Crumb, question, or answer not provided.";
			exit();
		}

		PdfFormDeployment::fromCrumb($_POST['crumb'])->getRespondent()->saveProgress($_POST['question'], $_POST['answer']);

		echo json_encode(array("result"=>"success"));
		exit();
	}

	/**
	 * Submit and save a form response
	 * 
	 * @return     void
	 */
	public function submitTask()
	{
		if(!$crumb = JRequest::getVar('crumb', false))
		{
			JError::raiseError(422);
		}

		$dep = PdfFormDeployment::fromCrumb($crumb);

		list($complete, $answers) = $dep->getForm()->getQuestionAnswerMap($_POST);

		if ($complete)
		{
			$resp = $dep->getRespondent();
			$resp->saveAnswers($_POST)->markEnd();

			$this->setRedirect(JRoute::_('index.php?option=com_courses&controller=form&task=complete&crumb='.$crumb, false));
			return;
		}
		else
		{
			$this->setView('form', 'complete');
			$this->view->incomplete = array_filter($answers, function($ans) { return is_null($ans[0]); });
			$this->completeTask();
		}
	}

	/**
	 * Check authorization
	 * 
	 * @return     void
	 */
	public function authorize()
	{
		// Make sure they're logged in
		if($this->juser->get('guest'))
		{
			$return = base64_encode(JRoute::_('index.php?option=' . $this->_option . '&controller=form'));
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . $return),
				$message,
				'warning'
			);
			return;
		}

		// Check for super admins
		if($this->juser->usertype == 'Super Administrator')
		{
			// Let them through
		}
		else
		{
			// If they're not a super admin, they can only view this page if they're looking at a form associated with a course that they're authorized on
			if(!$this->authorizeCourse())
			{
				// Otherwise, a course id should be provided, and we need to make sure they are authorized
				JError::raiseError(403, 'Not authorized');
			}
		}
	}

	/**
	 * Get form ID
	 * 
	 * @return     int
	 */
	public function assertFormId()
	{
		if (isset($_POST['formId']))
		{
			return $_POST['formId'];
		}
		if (isset($_GET['formId']))
		{
			return $_GET['formId'];
		}

		JError::raiseError(422, 'No form identifier supplied');
	}

	/**
	 * Check that form ID exists
	 * 
	 * @return     object
	 */
	public function assertExistentForm()
	{
		$pdf = new PdfForm($this->assertFormId());

		if (!$pdf->isStored())
		{
			JError::raiseError(404, 'No form matches identifier');
		}

		return $pdf;
	}

	/**
	 * Check if form is part of a course
	 * 
	 * @return bool
	 */
	public function authorizeCourse() {
		// First, check for a form id
		if(!$fid = JRequest::getInt('formId', false))
		{
			return false;
		}

		$dbh = JFactory::getDBO();

		$dbh->setQuery(
			'SELECT ca.course_id
			FROM `#__courses_assets` AS ca
			LEFT JOIN `#__courses_forms` AS cf ON cf.asset_id = ca.id
			WHERE cf.id = ' . $dbh->Quote($fid)
		);

		if($result = $dbh->loadResult())
		{
			// Get course model
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');
			$course = CoursesModelCourse::getInstance((int) $result);

			return $course->access('manage');
		}
		else
		{
			return false;
		}
	}
}

/**
 * Form Helper class
 */
class FormHelper {
	/**
	 * Time remaining (in human readable language)
	 * 
	 * @return     string
	 */
	public function timeDiff($secs)
	{
		$seconds = array(1, 'second');
		$minutes = array(60 * $seconds[0], 'minute');
		$hours   = array(60 * $minutes[0], 'hour');
		$days    = array(24 * $hours[0],   'day');
		$weeks   = array(7  * $days[0],    'week');
		$rv      = array();

		foreach (array($weeks, $days, $hours, $minutes, $seconds) as $step)
		{
			list($sec, $unit) = $step;
			$times = floor($secs / $sec);

			if ($times > 0)
			{
				$secs -= $sec * $times;
				$rv[] = $times . ' ' . $unit . ($times == 1 ? '' : 's');

				if (count($rv) == 2)
				{
					break;
				}
			}
			else if (count($rv))
			{
				break;
			}
		}

		return join(', ', $rv);
	}

	/**
	 * Get section for current student and form
	 * 
	 * @return     string
	 */
	public function getSection($crumb)
	{
		$db = JFactory::getDBO();
		$query = "SELECT cm.section_id FROM `#__courses_form_deployments` cfd
					JOIN `#__courses_assets` ca ON cfd.crumb = substring(ca.url, 30)
					JOIN `#__courses_offering_section_dates` cosd ON ca.id = cosd.scope_id
					JOIN `#__courses_members` cm ON cosd.section_id = cm.section_id 
					WHERE cosd.scope = 'asset'
					AND cm.user_id = " . $db->quote(JFactory::getUser()->get('id')) . "
					AND cfd.crumb = " . $db->quote($crumb);

		$db->setQuery($query);
		$result = $db->loadResult();

		return ($result) ? $result : NULL;
	}
}