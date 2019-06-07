<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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

		$response = with(new \Hubzero\Base\Obj)
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
		if (!($active = Request::getString('active')) && !$nonadmin)
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
				->set('option', Request::getCmd('option', 'com_courses'))
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
