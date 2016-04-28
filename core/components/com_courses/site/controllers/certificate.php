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
		$course   = Course::getInstance(Request::getVar('course', ''));
		$offering = $course->offering(Request::getVar('offering', ''));

		// Ensure the course exists
		if (!$course->exists() || !$offering->exists())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=courses'),
				Lang::txt('COM_COURSES_ERROR_COURSE_OR_OFFERING_NOT_FOUND'),
				'error'
			);
			return;
		}

		// Ensure specified user is enrolled in the course
		//$student = $offering->member(User::get('id'));
		$student = Member::getInstance(User::get('id'), $course->get('id'), $offering->get('id'), null, 1);
		if (!$student->exists())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=courses'),
				Lang::txt('COM_COURSES_ERROR_STUDENT_RECORD_NOT_FOUND'),
				'error'
			);
			return;
		}

		$certificate = $course->certificate();
		if (!$certificate->exists() || !$certificate->hasFile())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=courses'),
				Lang::txt('COM_COURSES_ERROR_NO_CERTIFICATE_FOR_COURSE'),
				'error'
			);
			return;
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
}
