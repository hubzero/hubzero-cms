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
 * @author    David Benham <dbenham@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Courses plugin class for member options
 */
class plgCoursesMemberOptions extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return data on a course view (this will be some form of HTML)
	 *
	 * @param   object   $course    Current course
	 * @param   object   $offering  Name of the component
	 * @param   boolean  $describe  Return plugin description only?
	 * @return  object
	 */
	public function onCourse($course, $offering, $describe=false)
	{
		$response = with(new \Hubzero\Base\Object)
			->set('name', $this->_name)
			->set('title', Lang::txt('PLG_COURSES_' . strtoupper($this->_name)))
			->set('default_access', 'registered')
			->set('display_menu_tab', false);

		if ($describe)
		{
			return $response;
		}

		if (!($active = Request::getVar('active')))
		{
			Request::setVar('active', ($active = $this->_name));
		}

		if ($response->get('name') == $active)
		{
			// Things we need from the form
			$recvEmailOptionID    = Request::getInt('memberoptionid', 0);
			$recvEmailOptionValue = Request::getInt('recvpostemail', 0);

			include_once(__DIR__ . DS . 'memberoption.class.php');

			switch ($action)
			{
				case 'editmemberoptions':
					$response->set('html', $this->edit($course, $user, $recvEmailOptionID, $recvEmailOptionValue));
				break;
				case 'savememberoptions':
					$response->set('html', $this->save($course, $user, $recvEmailOptionID, $recvEmailOptionValue));
				break;
				default:
					$response->set('html', $this->edit($course, $user, $recvEmailOptionID, $recvEmailOptionValue));
				break;
			}
		}

		// Return the output
		return $response;
	}

	/**
	 * Show an edit form
	 *
	 * @param   object  $course
	 * @param   object  $user
	 * @param   integer $recvEmailOptionID
	 * @param   unknown $recvEmailOptionValue
	 * @return  string
	 */
	protected function edit($course, $user, $recvEmailOptionID, $recvEmailOptionValue)
	{
		// Instantiate a view
		$view = $this->view('default', 'browse');

		// Load the options
		$database = App::get('db');
		$recvEmailOption = new courses_MemberOption($database);
		$recvEmailOption->loadRecord($course->get('gidNumber'), $user->get('id'), COURSES_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION);

		if ($recvEmailOption->id)
		{
			$view->recvEmailOptionID    = $recvEmailOption->id;
			$view->recvEmailOptionValue = $recvEmailOption->optionvalue;
		}
		else
		{
			$view->recvEmailOptionID    = 0;
			$view->recvEmailOptionValue = 0;
		}

		// Pass the view some info
		$view->option = $this->option;
		$view->course = $this->course;

		// Return the output
		return $view->loadTemplate();

	}

	/**
	 * Save setting
	 *
	 * @param   object  $course
	 * @param   object  $user
	 * @param   integer $recvEmailOptionID
	 * @param   unknown $recvEmailOptionValue
	 * @return  mixed
	 */
	protected function save($course, $user, $recvEmailOptionID, $recvEmailOptionValue)
	{
		$postSaveRedirect = Request::getVar('postsaveredirect', '');

		// Instantaite database object
		$database = App::get('db');

		// Save the COURSES_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION setting
		$row = new courses_MemberOption($database);

		//bind the data
		$rowdata = array(
			'id'          => $recvEmailOptionID,
			'userid'      => $user->get('id'),
			'gidNumber'   => $course->get('gidNumber'),
			'optionname'  => COURSES_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION,
			'optionvalue' => $recvEmailOptionValue
		);

		$row->bind($rowdata);

		// Check content
		if (!$row->check())
		{
			$this->setError($row->getError());
			return;
		}

		// Store content
		if (!$row->store())
		{
			$this->setError($row->getError());
			return $this->edit();
		}

		$msg = 'You have successfully updated your email settings';

		if (!$postSaveRedirect)
		{
			App::redirect(Route::url($this->course->link() . '&active=' . $this->_name . '&task=edit'), $msg);
		}
		else
		{
			App::redirect($postSaveRedirect, $msg);
		}
	}
}

