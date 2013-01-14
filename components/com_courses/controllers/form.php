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
 * Courses controller class
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
			'index.php?option=' . $this->_option . '&controller=forms&task=index'
		);

		if($this->_task != 'index')
		{
			$pathway->addItem(
				JText::_($this->_task),
				'index.php?option=' . $this->_option . '&controller=forms&task=' . $this->_task
			);
		}
	}

	/**
	 * Method to build and set the document title
	 * 
	 * @return     void
	 */
	public function _buildTitle()
	{
		// Set the title used in the view
		$this->_title = JText::_('Forms');

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
		$this->_buildTitle();
		$this->_buildPathway();

		if(!isset($this->view->errors))
		{
			$this->view->errors = array();
		}

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
			// @FIXME: get these errors back to the index view
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

		$this->view->pdf = new PdfForm($this->assertFormId());

		$this->view->title = $this->view->pdf->getTitle();
		//$path->addItem(($title ? htmlentities($title) : 'Layout: new form'), $_SERVER['REQUEST_URI']);

		$this->view->display();
	}

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

	public function deployTask()
	{
		// Check authorization
		$this->authorize();

		// Add stylesheets and scripts
		$this->_getStyles($this->_option, $this->_controller . '.css');
		$this->_getScripts('assets/js/timepicker');
		$this->_getScripts('assets/js/' . $this->_task);

		$this->view->pdf = $this->assertExistentForm();
		$this->view->dep = new PdfFormDeployment;

		$this->view->title = $this->view->pdf->getTitle();

		//$path->addItem('Deploy: '.htmlentities($title), $_SERVER['REQUEST_URI']);

		$this->view->display();
	}

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
			$this->deployTask();
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
			$this->showDeployment();
		}
		else
		{
			$this->setRedirect(
				JRoute::_('index.php?option=com_courses&controller=form&task=showDeployment&id='.$dep->save($deploymentId).'&formId='.$pdf->getId(), false),
				JText::_('Deployment successfully updated'),
				'passed'
			);
			return;
		}
	}

	public function showDeploymentTask()
	{
		if(!$id = JRequest::getInt('id', false))
		{
			JError::raiseError(422, 'No form identifier supplied');
		}

		$this->_getStyles($this->_option, $this->_controller . '.css');
		$this->_getScripts('assets/js/' . $this->_task);
		$this->_getScripts('assets/js/timepicker');

		// Add tablesorter
		Hubzero_Document::addSystemStylesheet('tablesorter.themes.blue.css');
		Hubzero_Document::addSystemScript('jquery.tablesorter.min');

		$this->view->pdf = $this->assertExistentForm();

		$this->view->title = 'Deployment: '.htmlentities($this->view->pdf->getTitle());
		//$doc->setTitle($title);
		//$path->addItem($title, $_SERVER['REQUEST_URI']);

		$this->view->dep = PdfFormDeployment::load($id);

		$this->view->display();
	}

	public function completeTask()
	{
		if(!$crumb = JRequest::getVar('crumb', false))
		{
			JError::raiseError(422);
		}

		$this->view->dep = PdfFormDeployment::fromCrumb($crumb);
		$dbg = JRequest::getVar('dbg', false);

		switch ($this->view->dep->getState())
		{
			case 'pending': 
				JError::raiseError(422, 'This deployment is not yet available');
			case 'expired': 
				require '../views/results/'.$this->view->dep->getResultsClosed().'.php'; 
			break;
			case 'active':
				$this->view->incomplete = array();
				$resp = $this->view->dep->getRespondent();
				if($resp->getEndTime())
				{
					'views/results/'.$this->view->dep->getResultsOpen().'.php';
				}
				else
				{
					$this->_getStyles($this->_option, $this->_controller . '.css');
					$this->_getScripts('assets/js/' . $this->_task);

					//$title = $pdf->getTitle();
					//$doc->setTitle($title);
					//$path->addItem(htmlentities($title), $_SERVER['REQUEST_URI']);
					$this->view->display();
				}
		}
	}

	public function startWorkTask()
	{
		if(!$crumb = JRequest::getVar('crumb', false))
		{
			JError::raiseError(422);
		}

		PdfFormDeployment::fromCrumb($crumb)->getRespondent()->markStart();

		header('Location: /courses?controller=form&task=complete&crumb='.$_POST['crumb'].(isset($_POST['tmpl']) ? '&tmpl='.$_POST['tmpl'] : ''));
		exit();
	}

	public function saveProgressTask()
	{
		if (!isset($_POST['crumb']) || !isset($_POST['question']) || !isset($_POST['answer'])) {
			throw new UnprocessableEntityError();
		}
		PdfFormDeployment::fromCrumb($_POST['crumb'])->getRespondent()->saveProgress($_POST['question'], $_POST['answer']);
		header('Content-type: application/json');
		echo '{"result":"success"}';
		exit();
	}

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

			$this->setRedirect(JRoute::_('index.php?option=com_courses&controller=form&task=complete&crumb='.$crumb));
			return;
		}
		else
		{
			$incomplete = array_filter($answers, function($ans) { return is_null($ans[0]); });
			require '../views/complete.php';
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
			// Otherwise, a course id should be provided, and we need to make sure they are authorized
			JError::raiseError(403, 'Not authorized');
		}
	}

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

	public function assertExistentForm()
	{
		$pdf = new PdfForm($this->assertFormId());

		if (!$pdf->isStored())
		{
			JError::raiseError(404, 'No form matches identifier');
		}

		return $pdf;
	}
}