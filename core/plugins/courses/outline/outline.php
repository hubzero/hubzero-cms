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

// No direct access
defined('_HZEXEC_') or die();

/**
 * Courses Plugin class for the outline
 */
class plgCoursesOutline extends \Hubzero\Plugin\Plugin
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
			->set('description', Lang::txt('PLG_COURSES_' . strtoupper($this->_name) . '_BLURB'))
			->set('default_access', $this->params->get('plugin_access', 'members'))
			->set('display_menu_tab', true)
			->set('icon', 'f0ae');

		if ($describe)
		{
			return $response;
		}

		if (!($active = Request::getVar('active')))
		{
			Request::setVar('active', ($active = $this->_name));
		}

		// Check to see if user is member and plugin access requires members
		$sparams = new \Hubzero\Config\Registry($course->offering()->section()->get('params'));
		if (!$course->offering()->section()->access('view') && !$sparams->get('preview', 0))
		{
			$response->set('html', '<p class="info">' . Lang::txt('COURSES_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>');
			return $response;
		}

		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($response->get('name') == $active)
		{
			$this->css();

			// Course and action
			$this->course = $course;
			$action = strtolower(Request::getWord('action', ''));

			$this->view = $this->view('default', 'outline');
			$this->view->option     = Request::getCmd('option', 'com_courses');
			$this->view->controller = Request::getWord('controller', 'course');
			$this->view->course     = $course;
			$this->view->offering   = $offering;
			$this->view->config     = $course->config();

			switch ($action)
			{
				case 'build':
					$this->_build();
				break;

				default:
					$this->js();

					$this->_display();
				break;
			}

			$response->set('html', $this->view->loadTemplate());
		}

		// Return the output
		return $response;
	}

	/**
	 * Set the layout to the default outline view
	 *
	 * @return  void
	 */
	private function _display()
	{
		if (($unit = Request::getVar('unit', '')))
		{
			$this->view->setLayout('unit');
		}
		if (($group = Request::getVar('group', '')))
		{
			$this->view->setLayout('lecture');
		}

		if (isset($unit))
		{
			$this->view->unit = $unit;
		}
		if (isset($group))
		{
			$this->view->group = $group;
		}
	}

	/**
	 * Show the builder interface
	 *
	 * @return  string
	 */
	private function _build()
	{
		if (!$this->course->access('manage'))
		{
			App::abort(401, Lang::txt('Not Authorized'));
			return;
		}

		// If we have a scope set, we're loading a specific outline piece (ex: a unit)
		if ($scope = Request::getWord('scope', false))
		{
			// Setup view
			$this->view->setLayout("edit{$scope}");

			$this->css('build.css');
			$this->css($scope . '.css');
			$this->js($scope);

			// Add file uploader JS
			$this->js('jquery.iframe-transport', 'system');
			$this->js('jquery.fileupload', 'system');

			$this->view->title         = "Edit {$scope}";
			$this->view->scope         = $scope;
			$this->view->scope_id      = Request::getInt('scope_id');

			return;
		}

		$this->css('jquery.ui.css', 'system');

		// Add outline builder style and script
		$this->css('build.css');
		$this->js('build');

		// Add Content box plugin
		$this->js('contentbox', 'system');
		$this->css('contentbox.css', 'system');

		// Add underscore
		$this->js('underscore-min', 'system');
		$this->js('jquery.hoverIntent', 'system');

		// Add 'uniform' js and css
		$this->css('uniform.css', 'system');
		$this->js('jquery.uniform', 'system');

		// Add file uploader JS
		$this->js('jquery.iframe-transport', 'system');
		$this->js('jquery.fileupload', 'system');

		// Use datetime picker, rather than just datepicker
		$this->js('jquery.timepicker', 'system');

		// Setup view
		$this->view->setLayout('build');

		$this->view->title = 'Build Outline';
	}

	/**
	 * Set redirect and message
	 *
	 * @param   string  $url   URL to redirect to
	 * @param   string  $msg   Message to send
	 * @param   string  $type  Message type (message, error, warning, info)
	 * @return  void
	 */
	public function setRedirect($url, $msg=null, $type='message')
	{
		if ($msg !== null)
		{
			$this->addPluginMessage($msg, $type);
		}

		App::redirect($url);
	}
}
