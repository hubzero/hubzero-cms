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
 * Courses Plugin class for intro guide
 */
class plgCoursesGuide extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Event call after course outline
	 *
	 * @param   object  $course    Current course
	 * @param   object  $offering  Current offering
	 * @return  void
	 */
	public function onCourseAfterOutline($course, $offering)
	{
		$member = $offering->member(User::get('id'));
		if ($member->get('first_visit') && $member->get('first_visit') != '0000-00-00 00:00:00')
		{
			return;
		}
		elseif (!$member->get('id')
			&& is_object(\Hubzero\Utility\Cookie::eat('plugin.courses.guide'))
			&& isset(\Hubzero\Utility\Cookie::eat('plugin.courses.guide')->first_visit))
		{
			return;
		}

		$this->view = with($this->view('overlay', $this->_name))
			->set('option', Request::getCmd('option', 'com_courses'))
			->set('controller', Request::getWord('controller', 'course'))
			->set('course', $course)
			->set('offering', $offering)
			->set('plugin', $this->_name);

		return $this->view->loadTemplate();
	}

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
		$response = with(new \Hubzero\Base\Obj)
			->set('name', $this->_name)
			->set('title', Lang::txt('PLG_COURSES_' . strtoupper($this->_name)))
			->set('default_access', $this->params->get('plugin_access', 'members'))
			->set('display_menu_tab', true)
			->set('icon', 'f059');

		if ($describe)
		{
			return $response;
		}

		$tmpl = Request::getWord('tmpl', null);
		if (!isset($tmpl) || $tmpl != 'component')
		{
			$this->css()
			     ->js('jquery.fancybox', 'system')
			     ->js('guide.overlay');
		}

		if (!($active = Request::getString('active')))
		{
			Request::setVar('active', ($active = $this->_name));
		}

		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($response->get('name') == $active)
		{
			$active = strtolower(Request::getWord('unit', ''));

			$action = '';
			if ($active == 'mark')
			{
				$action = 'mark';
			}
			if ($act = strtolower(Request::getWord('action', '')))
			{
				$action = $act;
			}

			$this->view = $this->view('default', $this->_name);
			$this->view->option     = Request::getCmd('option', 'com_courses');
			$this->view->controller = Request::getWord('controller', 'course');
			$this->view->course     = $course;
			$this->view->offering   = $offering;
			$this->view->config     = $this->params;
			$this->view->plugin     = $this->_name;

			switch ($action)
			{
				case 'mark':
					$this->_mark();
					break;

				default:
					$this->_default();
					break;
			}

			if (Request::getInt('no_html', 0))
			{
				ob_clean();
				header('Content-type: text/plain');
				echo $this->view->loadTemplate();
				exit();
			}
			$response->set('html', $this->view->loadTemplate());
		}

		// Return the output
		return $response;
	}

	/**
	 * Set default layout
	 *
	 * @return  void
	 */
	public function _default()
	{
		$this->view->setLayout('overlay');
	}

	/**
	 * Mark the overlay as having been viewed
	 *
	 * @return  void
	 */
	public function _mark()
	{
		$this->view->setLayout('mark');

		$member = $this->view->offering->member(User::get('id'));
		if ($member->get('first_visit') && $member->get('first_visit') != '0000-00-00 00:00:00')
		{
			return;
		}
		elseif (!$member->get('id'))
		{
			$cookie = \Hubzero\Utility\Cookie::eat('plugin.courses.guide');
			if (!is_object($cookie) || !isset($cookie->first_visit))
			{
				// Drop cookie
				$lifetime = time() + 365*24*60*60;

				\Hubzero\Utility\Cookie::bake('plugin.courses.guide', $lifetime, array(
					'first_visit' => Date::toSql()
				));
			}
		}
		$member->set('first_visit', Date::toSql());
		$member->store();
	}
}
