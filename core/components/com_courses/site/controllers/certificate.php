<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Site\Controllers;

use Components\Courses\Models\Course;
use Components\Courses\Models\Member;
use Hubzero\Component\SiteController;
use Hubzero\Content\Server;
use Exception;
use Filesystem;
use Request;
use Route;
use User;
use Lang;
use App;

/**
 * Courses controller class for generation and viewing of certificates
 */
class Certificate extends SiteController
{
	/**
	 * Displays a list of courses
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$course   = Course::getInstance(Request::getString('course', ''));
		$offering = $course->offering(Request::getString('offering', ''));

		// Ensure the course exists
		if (!$course->exists() || !$offering->exists())
		{
			Notify::error(Lang::txt('COM_COURSES_ERROR_COURSE_OR_OFFERING_NOT_FOUND'));
			return $this->cancelTask();
		}

		// Ensure specified user is enrolled in the course
		$student = Member::getInstance(User::get('id'), $course->get('id'), $offering->get('id'), null, 1);
		if (!$student->exists())
		{
			Notify::error(Lang::txt('COM_COURSES_ERROR_STUDENT_RECORD_NOT_FOUND'));
			return $this->cancelTask();
		}

		$certificate = $course->certificate();
		if (!$certificate->exists() || !$certificate->hasFile())
		{
			Notify::error(Lang::txt('COM_COURSES_ERROR_NO_CERTIFICATE_FOR_COURSE'));
			return $this->cancelTask();
		}

		// Path and file name
		$dir = PATH_APP . DS . 'site' . DS . 'courses' . DS . 'certificates';
		$file = $dir . DS . 'certificate_' . $course->get('id') . '_' . $offering->get('id') . '_' . User::get('id') . '.pdf';

		// If the file exists and we want to force regenerate it
		if (is_file($file) && Request::getInt('regenerate', 0))
		{
			if (!Filesystem::delete($file))
			{
				throw new Exception(Lang::txt('UNABLE_TO_DELETE_FILE'), 500);
			}
		}

		// Does the file exist already?
		if (!is_file($file))
		{
			// Create the upload directory if needed
			if (!is_dir($dir))
			{
				if (!Filesystem::makeDirectory($dir))
				{
					throw new Exception(Lang::txt('COM_COURSES_ERROR_FAILED_TO_CREATE_DIRECTORY'), 500);
				}
			}

			$certificate->render(User::getInstance(), $file);
		}

		// If file exists
		if (is_file($file))
		{
			$student->token();

			// Serve up the file
			$xserver = new Server();
			$xserver->filename($file);
			$xserver->serve_attachment($file); // Firefox and Chrome fail if served inline
			exit;
		}

		// Output failure message
		$this->view->display();
	}

	/**
	 * Redirect to main page
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=courses')
		);
	}
}
