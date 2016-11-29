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

// No direct access
defined('_HZEXEC_') or die();

/**
 * Courses Plugin class for manager dashboard
 */
class plgCoursesDashboard extends \Hubzero\Plugin\Plugin
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
		if (!$offering->access('manage', 'section'))
		{
			return;
		}

		$response = with(new \Hubzero\Base\Object)
			->set('name', $this->_name)
			->set('title', Lang::txt('PLG_COURSES_' . strtoupper($this->_name)))
			->set('description', Lang::txt('PLG_COURSES_' . strtoupper($this->_name) . '_BLURB'))
			->set('default_access', $this->params->get('plugin_access', 'managers'))
			->set('display_menu_tab', true)
			->set('icon', 'f083');

		if ($describe)
		{
			return $response;
		}

		$nonadmin = Request::getState('com_courses.offering' . $offering->get('id') . '.nonadmin', 0);
		if (!($active = Request::getVar('active')) && !$nonadmin)
		{
			Request::setVar('active', ($active = $this->_name));
		}

		if ($response->get('name') == $active)
		{
			// Set the page title
			Document::setTitle(Document::getTitle() . ': ' . Lang::txt('PLG_COURSES_' . strtoupper($this->_name)));

			Pathway::append(
				Lang::txt('PLG_COURSES_' . strtoupper($this->_name)),
				$offering->link() . '&active=' . $this->_name
			);

			$view = with($this->view('default', 'overview'))
				->set('option', Request::getVar('option', 'com_courses'))
				->set('course', $course)
				->set('offering', $offering)
				->set('params', $this->params);

			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}

			$response->set('html', $view->loadTemplate());
		}

		// Return the output
		return $response;
	}
}

