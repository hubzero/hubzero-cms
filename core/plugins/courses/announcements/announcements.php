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
 * Plugin class for course announcements
 */
class plgCoursesAnnouncements extends \Hubzero\Plugin\Plugin
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
			->set('icon', 'f095');

		if ($describe)
		{
			return $response;
		}

		if (!($active = Request::getVar('active')))
		{
			Request::setVar('active', ($active = $this->_name));
		}

		// Get a student count
		$response->set('meta_count', $offering->announcements(array('count' => true)));

		// Check if our area is in the array of areas we want to return results for
		if ($response->get('name') == $active)
		{
			// Set some variables so other functions have access
			$this->option   = Request::getCmd('option', 'com_courses');
			$this->course   = $course;
			$this->offering = $offering;

			// Set the page title
			Document::setTitle(Document::getTitle() . ': ' . Lang::txt('PLG_COURSES_ANNOUNCEMENTS'));

			Pathway::append(
				Lang::txt('PLG_COURSES_' . strtoupper($this->_name)),
				$this->offering->link() . '&active=' . $this->_name
			);

			require_once(Component::path('com_courses') . DS . 'models' . DS . 'announcement.php');

			$action = Request::getWord('action', '');

			switch (strtolower($action))
			{
				case 'save':   $response->set('html', $this->_save());   break;
				case 'new':    $response->set('html', $this->_edit());   break;
				case 'edit':   $response->set('html', $this->_edit());   break;
				case 'delete': $response->set('html', $this->_delete()); break;
				default:       $response->set('html', $this->_list());   break;
			}
		}

		// Return the output
		return $response;
	}

	/**
	 * Set redirect and message
	 *
	 * @param   object  $url  URL to redirect to
	 * @param   object  $msg  Message to send
	 * @return  void
	 */
	public function onCourseBeforeOutline($course, $offering)
	{
		return $this->view('default', 'latest')
					->set('course', $course)
					->set('offering', $offering)
					->set('params', $this->params)
					->loadTemplate();
	}

	/**
	 * Administrative dashboard info
	 *
	 * @param   object  $course    \Components\Courses\Models\Course
	 * @param   object  $offering  \Components\Courses\Models\Offering
	 * @return  string
	 */
	public function onCourseDashboard($course, $offering)
	{
		$view = with($this->view('dashboard', 'browse'))
			->set('course', $course)
			->set('offering', $offering)
			->set('option', Request::getCmd('option', 'com_courses'))
			->set('config', $course->config())
			->set('params', $this->params);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Display a list of all entries
	 *
	 * @return  string  HTML
	 */
	private function _list()
	{
		$view = with($this->view('default', 'browse'))
			->set('course', $this->course)
			->set('offering', $this->offering)
			->set('option', $this->option)
			->set('config', $this->course->config())
			->set('params', $this->params)
			->set('no_html', Request::getInt('no_html', 0));

		// Get filters for the entries list
		$filters = array(
			'search' => Request::getVar('q', ''),
			'limit'  => Request::getInt('limit', Config::get('list_limit', 25)),
			'start'  => Request::getInt('limitstart', 0)
		);
		$filters['start'] = ($filters['limit'] == 0 ? 0 : $filters['start']);

		$view->set('filters', $filters);

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Display a form for editing or creating an entry
	 *
	 * @return  string  HTML
	 */
	private function _edit($model=null)
	{
		// Permissions check
		if (!$this->offering->access('manage', 'section'))
		{
			return $this->_list();
		}

		$view = with($this->view('default', 'edit'))
			->set('course', $this->course)
			->set('offering', $this->offering)
			->set('option', $this->option)
			->set('params', $this->params);

		if (!($model instanceof \Components\Courses\Models\Announcement))
		{
			$model = \Components\Courses\Models\Announcement::getInstance(Request::getInt('entry', 0));
		}

		$view->set('model', $model);

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		// Display edit form
		return $view->loadTemplate();
	}

	/**
	 * Save an entry
	 *
	 * @return  string  HTML
	 */
	private function _save()
	{
		// Permissions check
		if (!$this->offering->access('manage', 'section'))
		{
			return $this->_list();
		}

		// Check for request forgeries
		Request::checkToken();

		$no_html = Request::getInt('no_html', 0);

		$response = new stdClass;
		$response->code = 0;

		// Incoming
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);
		$fields = array_map('trim', $fields);

		// Get the model and bind the data
		$model = new \Components\Courses\Models\Announcement(0);
		if (!$model->bind($fields))
		{
			$this->setError($model->getError());
			return $this->_edit($model);
		}

		// Incoming dates are in local time. We need to convert to UTC
		if ($model->get('publish_up') && $model->get('publish_up') != '0000-00-00 00:00:00')
		{
			$model->set('publish_up', Date::of($model->get('publish_up'), Config::get('offset'))->toSql());
		}

		// Incoming dates are in local time. We need to convert to UTC
		if ($model->get('publish_down') && $model->get('publish_down') != '0000-00-00 00:00:00')
		{
			$model->set('publish_down', Date::of($model->get('publish_down'), Config::get('offset'))->toSql());
		}

		if (!isset($fields['priority']) || !$fields['priority'])
		{
			$model->set('priority', 0);
		}

		// Store content
		if (!$model->store(true))
		{
			$this->setError($model->getError());
			if (!$no_html)
			{
				return $this->_edit($model);
			}
		}

		if ($no_html)
		{
			if ($this->getError())
			{
				$response->code   = 1;
				$response->errors = $this->getErrors();
				$response->data   = $fields;
			}
			ob_clean();
			header('Content-type: text/plain');
			echo json_encode($response);
			exit();
		}

		// Display listing
		return $this->_list();
	}

	/**
	 * Mark an entry as deleted
	 *
	 * @return  string  HTML
	 */
	private function _delete()
	{
		// Permissions check
		if (!$this->offering->access('manage', 'section'))
		{
			return $this->_list();
		}

		// Incoming
		$id = Request::getInt('entry', 0);

		// Get the model and set the state to "deleted"
		$model = \Components\Courses\Models\Announcement::getInstance($id);
		$model->set('state', 2);

		// Store content
		if (!$model->store())
		{
			$this->setError($model->getError());
		}

		// Display listing
		return $this->_list();
	}
}

