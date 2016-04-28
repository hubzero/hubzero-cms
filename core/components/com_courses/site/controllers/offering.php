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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Site\Controllers;

use Components\Courses\Models;
use Hubzero\Component\SiteController;
use Hubzero\Content\Server;
use Hubzero\Config\Registry;
use Exception;
use Pathway;
use Request;
use Config;
use Route;
use Event;
use User;
use Lang;
use App;

/**
 * Courses controller class for an offering
 */
class Offering extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		$this->gid = Request::getVar('gid', '');
		if (!$this->gid)
		{
			App::redirect(
				'index.php?option=' . $this->_option
			);
			return;
		}

		// Load the course page
		$this->course = Models\Course::getInstance($this->gid);

		// Ensure we found the course info
		if (!$this->course->exists() || $this->course->isDeleted() || $this->course->isUnpublished())
		{
			return App::abort(404, Lang::txt('COM_COURSES_NO_COURSE_FOUND'));
		}

		// No offering provided
		if (!($offering = Request::getVar('offering', '')))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=course&gid=' . $this->course->get('alias'))
			);
			return;
		}

		// Ensure we found the course info
		if (!$this->course->offering($offering)->exists()
		 || $this->course->offering($offering)->isDeleted()
		 || (!$this->course->offering($offering)->access('manage', 'section') && $this->course->offering($offering)->isUnpublished()))
		{
			return App::abort(404, Lang::txt('COM_COURSES_NO_OFFERING_FOUND'));
		}

		// Ensure the course has been published or has been approved
		if (!$this->course->offering()->access('manage', 'section') && !$this->course->isAvailable())
		{
			return App::abort(404, Lang::txt('COM_COURSES_NOT_PUBLISHED'));
		}

		parent::execute();
	}

	/**
	 * Method to set the document path
	 *
	 * @param      array $course_pages List of roup pages
	 * @return     void
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

		if ($this->course->exists())
		{
			Pathway::append(
				stripslashes($this->course->get('title')),
				'index.php?option=' . $this->_option . '&gid=' . $this->course->get('alias')
			);

			if ($this->course->offering()->exists())
			{
				Pathway::append(
					stripslashes($this->course->offering()->get('title')),
					'index.php?option=' . $this->_option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias')
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
		$this->_title = Lang::txt(strtoupper($this->_option));

		if ($this->course->exists())
		{
			$this->_title .= ': ' . stripslashes($this->course->get('title'));

			if ($this->course->offering()->exists())
			{
				$this->_title .= ': ' . stripslashes($this->course->offering()->get('title'));
			}
		}

		//set title of browser window
		\Document::setTitle($this->_title);
	}

	/**
	 * Redirect to login page
	 *
	 * @return     void
	 */
	public function loginTask($message = '')
	{
		$rtrn = base64_encode(Route::url($this->course->offering()->link() . '&task=' . $this->_task, false, true));
		$link = str_replace('&amp;', '&', Route::url('index.php?option=com_users&view=login&return=' . $rtrn));
		App::redirect(
			$link,
			Lang::txt($message),
			'warning'
		);
		return;
	}

	/**
	 * View a course
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Check if the offering is available
		if (!$this->course->offering()->access('manage', 'section') && !$this->course->offering()->isAvailable())
		{
			return App::abort(404, Lang::txt('COM_COURSES_NO_OFFERING_FOUND'));
		}

		$tmpl = $this->config->get('tmpl', '');

		if ($tmpl && !Request::getWord('tmpl', false))
		{
			Request::setVar('tmpl', $tmpl);
		}

		// Get the active tab (section)
		$this->view->nonadmin = 0;
		if ($this->course->offering()->access('manage', 'section'))
		{
			$this->view->nonadmin = Request::getInt('nonadmin', User::getState(
				$this->_option . '.offering' . $this->course->offering()->get('id') . '.nonadmin',
				0
			));
			User::setState(
				$this->_option . '.offering' . $this->course->offering()->get('id') . '.nonadmin',
				$this->view->nonadmin
			);
		}

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		// Trigger the functions that return the areas we'll be using
		$plugins = Event::trigger('courses.onCourse', array(
			$this->course,
			$this->course->offering()
		));

		$this->view->course        = $this->course;
		$this->view->config        = $this->config;
		$this->view->plugins       = $plugins;
		$this->view->notifications = \Notify::messages('courses');
		$this->view->display();
	}

	/**
	 * Display an offering asset
	 *
	 * @return     void
	 */
	public function enrollTask()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			$this->loginTask(Lang::txt('COM_COURSES_ENROLLMENT_REQUIRES_LOGIN'));
			return;
		}

		$offering = $this->course->offering();

		// Is the user a manager or student?
		if ($offering->isManager() || $offering->isStudent())
		{
			// Yes! Already enrolled
			// Redirect back to the course page
			App::redirect(
				Route::url($offering->link()),
				Lang::txt('COM_COURSES_ALREADY_ENROLLED')
			);
			return;
		}

		$this->view->course = $this->course;

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		// Can the user enroll?
		if (!$offering->section()->canEnroll())
		{
			$this->view->setLayout('enroll_closed');
			$this->view->display();
			return;
		}

		$enrolled = false;

		// If enrollment is open OR a coupon code was posted
		if (!$offering->section()->get('enrollment') || ($code = Request::getVar('code', '')))
		{
			$section_id = $offering->section()->get('id');

			// If a coupon code was posted
			if (isset($code))
			{
				// Get the coupon
				$coupon = $offering->section()->code($code);
				// Is it a valid code?
				if (!$coupon->exists())
				{
					$this->setError(Lang::txt('COM_COURSES_ERROR_CODE_INVALID', $code));
				}
				// Has it already been redeemed?
				if ($coupon->isRedeemed())
				{
					$this->setError(Lang::txt('COM_COURSES_ERROR_CODE_ALREADY_REDEEMED', $code));
				}
				else
				{
					// Has it expired?
					if ($coupon->isExpired())
					{
						$this->setError(Lang::txt('COM_COURSES_ERROR_CODE_EXPIRED', $code));
					}
				}
				if (!$this->getError())
				{
					// Is this a coupon for a different section?
					if ($offering->section()->get('id') != $coupon->get('section_id'))
					{
						$section = \Components\Courses\Models\Section::getInstance($coupon->get('section_id'));
						if ($section->exists() && $section->get('offering_id') != $offering->get('id'))
						{
							$offering = \Components\Courses\Models\Offering::getInstance($section->get('offering_id'));
							if ($offering->exists() && $offering->get('course_id') != $this->course->get('id'))
							{
								$this->course = \Components\Courses\Models\Course::getInstance($offering->get('course_id'));
							}
						}
						App::redirect(
							Route::url($offering->link() . '&task=enroll&code=' . $code)
						);
						return;
					}
					// Redeem the code
					$coupon->redeem(User::get('id'));// set('redeemed_by', User::get('id'));
					//$coupon->store();
				}
			}

			// If no errors
			if (!$this->getError())
			{
				// Add the user to the course
				$model = new \Components\Courses\Models\Member(0); //::getInstance(User::get('id'), $offering->get('id'));
				$model->set('user_id', User::get('id'));
				$model->set('course_id', $this->course->get('id'));
				$model->set('offering_id', $offering->get('id'));
				$model->set('section_id', $offering->section()->get('id'));
				if ($roles = $offering->roles())
				{
					foreach ($roles as $role)
					{
						if ($role->alias == 'student')
						{
							$model->set('role_id', $role->id);
							break;
						}
					}
				}
				$model->set('student', 1);
				if ($model->store(true))
				{
					$enrolled = true;
				}
				else
				{
					$this->setError($model->getError());
				}
			}
		}

		if ($enrolled)
		{
			$link = $offering->link();

			$data = Event::trigger('courses.onCourseEnrolled', array(
				$this->course, $offering, $offering->section()
			));
			if ($data && count($data) > 0)
			{
				$link = implode('', $data);
			}
			App::redirect(
				Route::url($link)
			);
			return;
		}

		// If enrollment is srestricted and the user isn't enrolled yet
		if ($offering->section()->get('enrollment') == 1 && !$enrolled)
		{
			// Show a form for entering a coupon code
			$this->view->setLayout('enroll_restricted');
		}

		if ($this->getError())
		{
			\Notify::error($this->getError(), 'courses');
		}
		$this->view->notifications = \Notify::messages('courses');
		$this->view->display();
	}

	/**
	 * Show a form for editing a course
	 *
	 * @return     void
	 */
	public function newTask()
	{
		$this->editTask();
	}

	/**
	 * Show a form for editing a course
	 *
	 * @return     void
	 */
	public function editTask()
	{
		$this->view->notifications = \Notify::messages('courses');
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Display an offering asset
	 *
	 * @return     void
	 */
	public function assetTask()
	{
		$sparams    = new Registry($this->course->offering()->section()->get('params'));
		$section_id = $this->course->offering()->section()->get('id');
		$asset      = new Models\Asset(Request::getInt('asset_id', null));
		$asset->set('section_id', $section_id);

		// First, check if current user has access to course
		if (!$this->course->offering()->access('view'))
		{
			// Is a preview available?
			$preview = $sparams->get('preview', 0);

			// If no preview is available or if type  is form (i.e. you can never preview forms)
			if (!$preview || $asset->get('type') == 'form')
			{
				// Check if they're logged in
				if (User::isGuest())
				{
					$this->loginTask(Lang::txt('COM_COURSES_ENROLLMENT_REQUIRED_FOR_ASSET'));
					return;
				}
				else
				{
					// Redirect back to the course outline
					App::redirect(
						Route::url($this->course->offering()->link()),
						Lang::txt('COM_COURSES_ENROLLMENT_REQUIRED_FOR_ASSET'),
						'warning'
					);
					return;
				}
			}
			elseif ($preview == 2) // Preview is only for first unit
			{
				$units = $asset->units();
				if ($units && count($units) > 0)
				{
					foreach ($units as $unit)
					{
						if ($unit->get('ordering') > 1)
						{
							// Check if they're logged in
							if (User::isGuest())
							{
								$this->loginTask(Lang::txt('COM_COURSES_ENROLLMENT_REQUIRED_FOR_ASSET'));
								return;
							}
							else
							{
								// Redirect back to the course outline
								App::redirect(
									Route::url($this->course->offering()->link()),
									Lang::txt('COM_COURSES_ENROLLMENT_REQUIRED_FOR_ASSET'),
									'warning'
								);
								return;
							}
						}
					}
				}
			}
		}

		// If not a manager and either the offering or section is unpublished...
		if (!$this->course->offering()->access('manage') && (!$this->course->offering()->isPublished() || !$this->course->offering()->section()->isPublished()))
		{
			return App::abort(403, Lang::txt('COM_COURSES_ERROR_ASSET_UNAVAILABLE'));
		}

		if (!$this->course->offering()->access('manage') && !$asset->isAvailable())
		{
			// Allow expired forms to pass through (i.e. so students can see their results)
			if (!$asset->get('type') == 'form' || !$asset->ended())
			{
				// Redirect back to the course outline
				App::redirect(
					Route::url($this->course->offering()->link()),
					Lang::txt('COM_COURSES_ERROR_ASSET_UNAVAILABLE'),
					'warning'
				);
				return;
			}
		}

		// Check prerequisites
		$member = $this->course->offering()->section()->member(User::get('id'));
		if (is_null($member->get('section_id')))
		{
			$member->set('section_id', $section_id);
		}
		$prerequisites = $member->prerequisites($this->course->offering()->gradebook());

		if (!$this->course->offering()->access('manage') && !$prerequisites->hasMet('asset', $asset->get('id')))
		{
			$prereqs      = $prerequisites->get('asset', $asset->get('id'));
			$requirements = array();
			foreach ($prereqs as $pre)
			{
				$reqAsset = new Models\Asset($pre['scope_id']);
				$requirements[] = $reqAsset->get('title');
			}

			$requirements = implode(', ', $requirements);

			// Redirect back to the course outline
			App::redirect(
				Route::url($this->course->offering()->link()),
				Lang::txt('COM_COURSES_ERROR_ASSET_HAS_PREREQ', $requirements),
				'warning'
			);
			return;
		}

		// If requesting a file from a wiki type asset, then serve that up directly
		if ($asset->get('subtype') == 'wiki' && Request::getVar('file', false))
		{
			echo $asset->download($this->course);
		}

		echo $asset->render($this->course);
	}

	/**
	 * Save a course
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			$this->loginTask(Lang::txt('COM_COURSES_NOT_LOGGEDIN'));
			return;
		}

		// Redirect back to the course page
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->course->get('alias') . '&task=offerings')
		);
	}

	/**
	 * Delete a course
	 * This method initially displays a form for confirming deletion
	 * then deletes course and associated information upon POST
	 *
	 * @return     void
	 */
	public function deleteTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->course->get('alias') . '&task=offerings')
		);
	}

	/**
	 * Serve up an offering logo
	 *
	 * @return  void
	 */
	public function logoTask()
	{
		if (!($logo = $this->course->offering()->section()->logo()))
		{
			$logo = $this->course->offering()->logo();
		}
		$file = PATH_APP . $logo;

		// Initiate a new content server and serve up the file
		$server = new Server();
		$server->filename($file);
		$server->disposition('inline');
		$server->acceptranges(false);

		if (!$server->serve())
		{
			// Should only get here on error
			throw new Exception(Lang::txt('COM_COURSES_SERVER_ERROR'), 404);
		}
		else
		{
			exit;
		}
	}
}

