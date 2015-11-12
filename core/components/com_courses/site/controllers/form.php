<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Site\Controllers;

use Components\Courses\Models\Course;
use Components\Courses\Models\PdfForm;
use Components\Courses\Models\PdfFormDeployment;
use Hubzero\Component\SiteController;
use Request;
use Pathway;
use Route;
use User;
use Lang;
use Date;
use App;

// Include required forms models
require_once(dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'form.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'form.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'formRespondent.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'formDeployment.php');

/**
 * Courses form controller class
 */
class Form extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->getCourseInfo();

		// Get the courses member
		$this->member = $this->course->offering()->section()->member(User::get('id'))->get('id');
		if (!$this->member || !is_numeric($this->member))
		{
			$this->member = $this->course->offering()->member(User::get('id'))->get('id');
			if (!$this->member || !is_numeric($this->member))
			{
				App::abort(422, Lang::txt('No user found'));
			}
		}

		// Set the base path
		$this->base = "index.php?option=com_courses&controller=form&gid={$this->course->get('alias')}&offering={$this->course->offering()->alias()}";

		parent::execute();
	}

	/**
	 * Method to set the document path
	 *
	 * @return  void
	 */
	public function _buildPathway()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}

		Pathway::append(
			Lang::txt(ucfirst($this->course->get('title'))),
			'index.php?option=com_courses&controller=form&gid=' . $this->course->get('alias')
		);

		Pathway::append(
			Lang::txt(ucfirst($this->course->offering()->get('title'))),
			$this->base
		);

		if ($this->_task != 'index')
		{
			Pathway::append(
				Lang::txt(ucfirst($this->_task)),
				'index.php?option=' . $this->_option . '&controller=form&task=' . $this->_task
			);
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @return  void
	 */
	public function _buildTitle($append='')
	{
		// Set the title used in the view
		$this->_title = Lang::txt('COM_COURSES_FORMS');

		if (!empty($append))
		{
			$this->_title .= ': ' . $append;
		}

		//set title of browser window
		\Document::setTitle($this->_title);
	}

	/**
	 * Default index view of all forms
	 *
	 * @return  void
	 */
	public function indexTask()
	{
		// Check authorization
		// @FIXME: only admins should see ALL exams
		$this->authorize();

		// Set the title and pathway
		$this->_buildTitle('Upload a PDF');
		$this->_buildPathway();

		if (!isset($this->view->errors))
		{
			$this->view->errors = array();
		}

		$this->view->title = $this->_title;
		$this->view->base  = $this->base;
		$this->view->course = $this->course;

		// Display
		$this->view->display();
	}

	/**
	 * Upload a PDF and render images
	 *
	 * @return  void
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
			if (Request::getInt('no_html', false))
			{
				echo json_encode(array(
					'success' => true,
					'id'      => $pdf->getId()
				));
				exit();
			}
			else // Otherwise, redirect
			{
				App::redirect(
					Route::url('index.php?option=com_courses&controller=form&task=layout&formId=' . $pdf->getId(), false),
					Lang::txt('COM_COURSES_PDF_UPLOAD_SUCCESSFUL'),
					'passed'
				);
				return;
			}
		}
	}

	/**
	 * PDF layout view, annotate rendered images
	 *
	 * @return  void
	 */
	public function layoutTask()
	{
		// Check authorization
		$this->authorize();

		// Set the title and pathway
		$this->_buildTitle();
		$this->_buildPathway();

		$this->view->pdf      = new PdfForm($this->assertFormId());
		$this->view->title    = $this->view->pdf->getTitle();
		$this->view->readonly = Request::getInt('readonly', false);
		$this->view->base     = $this->base;
		$this->view->course   = $this->course;
		$this->view->display();
	}

	/**
	 * Save layout
	 *
	 * @return  void
	 */
	public function saveLayoutTask()
	{
		// Check authorization
		$this->authorize();

		$pdf = $this->assertExistentForm();

		if (Request::getVar('title', false))
		{
			$pdf->setTitle($_POST['title']);
		}

		if (isset($_POST['pages']))
		{
			$pdf->setPageLayout($_POST['pages']);
		}

		if (isset($_FILES['pdf']))
		{
			$pdf->setFname((is_array($_FILES['pdf']['tmp_name'])) ? $_FILES['pdf']['tmp_name'][0] : $_FILES['pdf']['tmp_name']);
			$pdf->renderPageImages();
		}

		echo json_encode(array('result' => 'success'));
		exit();
	}

	/**
	 * Deploy form view
	 *
	 * @return  void
	 */
	public function deployTask($dep=NULL)
	{
		// Check authorization
		$this->authorize();

		// Set the title and pathway
		$this->_buildTitle();
		$this->_buildPathway();

		$this->view->pdf   = $this->assertExistentForm();
		$this->view->dep   = ($dep) ? $dep : new PdfFormDeployment;
		$this->view->title = $this->view->pdf->getTitle();
		$this->view->base  = $this->base;
		$this->view->course = $this->course;

		$this->view->display();
	}

	/**
	 * Create deployment
	 *
	 * @return  void
	 */
	public function createDeploymentTask()
	{
		if (!$deployment = Request::getVar('deployment'))
		{
			App::abort(422, Lang::txt('COM_COURSES_ERROR_MISSING_DEPLOYMENT'));
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
			if (Request::getInt('no_html', false))
			{
				echo json_encode(array(
					'success' => true,
					'id'      => $dep->save(),
					'formId'  => $pdf->getId()
				));
				exit();
			}
			else
			{
				$tmpl = (Request::getWord('tmpl', false)) ? '&tmpl=component' : '';
				App::redirect(
					Route::url($this->base . '&task=form.showDeployment&id=' . $dep->save() . '&formId=' . $pdf->getId() . $tmpl, false),
					Lang::txt('COM_COURSES_DEPLOYMENT_CREATED'),
					'passed'
				);
				return;
			}
		}
	}

	/**
	 * Update an existing deployment
	 *
	 * @return  void
	 */
	public function updateDeploymentTask()
	{
		if (!$deployment = Request::getVar('deployment'))
		{
			App::abort(422, Lang::txt('COM_COURSES_ERROR_MISSING_DEPLOYMENT'));
		}

		if (!$deploymentId = Request::getInt('deploymentId'))
		{
			App::abort(422, Lang::txt('COM_COURSES_ERROR_MISSING_DEPLOYMENT_ID'));
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
			$tmpl = (Request::getWord('tmpl', false)) ? '&tmpl=component' : '';
			App::redirect(
				Route::url($this->base . '&task=form.showDeployment&id=' . $dep->save($deploymentId) . '&formId=' . $pdf->getId() . $tmpl, false),
				Lang::txt('COM_COURSES_DEPLOYMENT_UPDATED'),
				'passed'
			);
			return;
		}
	}

	/**
	 * Show deployment
	 *
	 * @return  void
	 */
	public function showDeploymentTask($dep=NULL)
	{
		if (!$id = Request::getInt('id', false))
		{
			App::abort(422, Lang::txt('COM_COURSES_ERROR_MISSING_IDENTIFIER'));
		}

		// Set the title and pathway
		$this->_buildTitle();
		$this->_buildPathway();

		$this->view->pdf   = $this->assertExistentForm();
		$this->view->title = $this->view->pdf->getTitle();
		$this->view->dep   = ($dep) ? $dep : PdfFormDeployment::load($id);
		$this->view->base  = $this->base;
		$this->view->course = $this->course;

		// Display
		$this->view->display();
	}

	/**
	 * Take a form/exam
	 *
	 * @return  void
	 */
	public function completeTask()
	{
		if (!$crumb = Request::getVar('crumb', false))
		{
			App::abort(422);
		}

		$dep = PdfFormDeployment::fromCrumb($crumb, $this->course->offering()->section()->get('id'));
		$dbg = Request::getVar('dbg', false);

		switch ($dep->getState())
		{
			case 'pending':
				App::abort(422, Lang::txt('COM_COURSES_DEPLOYMENT_UNAVAILABLE'));
			break;

			case 'expired':
				$attempt = Request::getInt('attempt', 1);

				// Make sure they're not trying to take the form too many times
				if ($attempt > $dep->getAllowedAttempts())
				{
					App::abort(403, Lang::txt('COM_COURSES_WARNING_EXCEEDED_ATTEMPTS'));
				}

				$this->setView('results', $dep->getResultsClosed());

				// Set the title and pathway
				$this->_buildTitle();
				$this->_buildPathway();

				$this->view->dep   = $dep;
				$this->view->resp  = $dep->getRespondent($this->member, $attempt);
				$this->view->pdf   = $dep->getForm();
				$this->view->title = $this->view->pdf->getTitle();
				$this->view->base  = $this->base;
				$this->view->dep   = $dep;
				$this->view->course = $this->course;

				// Display
				$this->view->display();
			break;

			case 'active':
				$attempt = Request::getInt('attempt', 1);

				// Make sure they're not trying to take the form too many times
				if ($attempt > $dep->getAllowedAttempts())
				{
					App::abort(403, Lang::txt('COM_COURSES_WARNING_EXCEEDED_ATTEMPTS'));
				}

				$resp = $dep->getRespondent($this->member, $attempt);

				if ($resp->getEndTime())
				{
					$this->setView('results', $dep->getResultsOpen());

					// Set the title and pathway
					$this->_buildTitle();
					$this->_buildPathway();

					$this->view->incomplete = array();
					$this->view->pdf        = $dep->getForm();
					$this->view->title      = $this->view->pdf->getTitle();
					$this->view->base       = $this->base;
					$this->view->dep        = $dep;
					$this->view->resp       = $resp;
					$this->view->course     = $this->course;
					$this->view->display();
					return;
				}
				else
				{
					// Set the title and pathway
					$this->_buildTitle();
					$this->_buildPathway();

					$this->view->pdf        = $dep->getForm();
					$this->view->title      = $this->view->pdf->getTitle();
					$this->view->base       = $this->base;
					$this->view->dep        = $dep;
					$this->view->resp       = $resp;
					$this->view->incomplete = (isset($this->view->incomplete)) ? $this->view->incomplete : array();
					$this->view->course     = $this->course;
					$this->view->display();
				}
			break;
		}
	}

	/**
	 * Mark the start of a time form
	 *
	 * @return  void
	 */
	public function startWorkTask()
	{
		if (!$crumb = Request::getVar('crumb', false))
		{
			App::abort(422);
		}

		$attempt = Request::getInt('attempt', 1);

		PdfFormDeployment::fromCrumb($crumb)->getRespondent($this->member, $attempt)->markStart();

		$tmpl = (Request::getWord('tmpl', false)) ? '&tmpl=' . Request::getWord('tmpl') : '';
		$att  = ($attempt > 1) ? '&attempt='.$attempt : '';

		App::redirect(
			Route::url($this->base . '&task=form.complete&crumb=' . $crumb . $att . $tmpl, false)
		);
		return;
	}

	/**
	 * Save progress, called via JS ajax
	 *
	 * @return  void
	 */
	public function saveProgressTask()
	{
		if (!isset($_POST['crumb']) || !isset($_POST['question']) || !isset($_POST['answer']))
		{
			echo Lang::txt('COM_COURSES_ERROR_MISSING_CRUMB_QUESTION_OR_ANSWER');
			exit();
		}

		$attempt = Request::getInt('attempt', 1);

		$resp = PdfFormDeployment::fromCrumb($_POST['crumb'])->getRespondent($this->member, $attempt);

		if (!$resp->getEndTime())
		{
			$resp->saveProgress($_POST['question'], $_POST['answer']);
		}

		echo json_encode(array("result"=>"success"));
		exit();
	}

	/**
	 * Submit and save a form response
	 *
	 * @return  void
	 */
	public function submitTask()
	{
		if (!$crumb = Request::getVar('crumb', false))
		{
			App::abort(422);
		}

		$attempt = Request::getInt('attempt', 1);
		$att     = ($attempt > 1) ? '&attempt='.$attempt : '';
		$dep     = PdfFormDeployment::fromCrumb($crumb);
		$ended   = false;

		// Make sure they're not trying to take the form too many times
		if ($attempt > $dep->getAllowedAttempts())
		{
			App::abort(403, Lang::txt('COM_COURSES_WARNING_EXCEEDED_ATTEMPTS'));
		}

		// Check to see if the time limit has been reached
		if ($limit = $dep->getTimeLimit())
		{
			$resp = $dep->getRespondent($this->member, $attempt);

			$now   = strtotime(Date::of('now'));
			$start = strtotime($resp->getStartTime());
			$end   = strtotime($dep->getEndTime());
			$dur   = $limit * 60;

			if ($now > ($start + $dur) || ($dep->getEndTime() && $end < $now))
			{
				$ended = true;
			}
		}

		list($complete, $answers) = $dep->getForm()->getQuestionAnswerMap($_POST, $ended);

		if ($complete)
		{
			$resp = $dep->getRespondent($this->member, $attempt);
			if (!$resp->getEndTime())
			{
				$resp->saveAnswers($_POST)->markEnd();
			}

			App::redirect(Route::url($this->base . '&task=form.complete&crumb=' . $crumb . $att, false));
			return;
		}
		else
		{
			$this->setView('form', 'complete');
			$this->_task = 'complete';
			$this->view->incomplete = array_filter($answers, function($ans) { return is_null($ans[0]); });
			$this->completeTask();
		}
	}

	/**
	 * Check authorization
	 *
	 * @return  void
	 */
	public function authorize()
	{
		// Make sure they're logged in
		if (User::isGuest())
		{
			$return = base64_encode(Route::url('index.php?option=' . $this->_option . '&controller=form'));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return),
				$message,
				'warning'
			);
			return;
		}

		// Check for super admins
		if (User::get('usertype') == 'Super Administrator')
		{
			// Let them through
		}
		else
		{
			// If they're not a super admin, they can only view this page if they're looking at a form associated with a course that they're authorized on
			if (!$this->authorizeCourse())
			{
				// Otherwise, a course id should be provided, and we need to make sure they are authorized
				App::abort(403, Lang::txt('COM_COURSES_NOT_AUTH'));
			}
		}
	}

	/**
	 * Get form ID
	 *
	 * @return  integer
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

		App::abort(422, Lang::txt('COM_COURSES_ERROR_MISSING_IDENTIFIER'));
	}

	/**
	 * Check that form ID exists
	 *
	 * @return  object
	 */
	public function assertExistentForm()
	{
		$pdf = new PdfForm($this->assertFormId());

		if (!$pdf->isStored())
		{
			App::abort(404, Lang::txt('COM_COURSES_ERROR_UNKNOWN_IDENTIFIER'));
		}

		return $pdf;
	}

	/**
	 * Get course info from route
	 *
	 * @return  bool
	 */
	public function getCourseInfo()
	{
		$gid      = Request::getVar('gid');
		$offering = Request::getVar('offering');
		$section  = Request::getVar('section');

		$this->course = new Course($gid);
		$this->course->offering($offering);
		$this->course->offering()->section($section);
	}

	/**
	 * Check if form is part of a course
	 *
	 * @return  bool
	 */
	public function authorizeCourse()
	{
		return $this->course->access('manage');
	}
}