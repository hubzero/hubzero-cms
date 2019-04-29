<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Courses Plugin class for course offerings
 */
class plgCoursesOfferings extends \Hubzero\Plugin\Plugin
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
	 * @param   object  $course  Current course
	 * @param   string  $active  Current active area
	 * @return  array
	 */
	public function onCourseView($course, $active=null)
	{
		// Check that there are any offerings to show
		if ($course->offerings(array('state' => 1, 'sort_Dir' => 'ASC'), true)->total() <= 0)
		{
			return;
		}

		// Can this plugin respond, based on the current access settings?
		$respond = false;
		switch ($this->params->get('plugin_access', 'anyone'))
		{
			case 'managers':
				$memberships = $course->offering()->membership();

				if (count($memberships) > 0)
				{
					foreach ($memberships as $membership)
					{
						if (!$membership->get('student'))
						{
							$respond = true;
							break;
						}
					}
				}
			break;

			case 'members':
				if (count($course->offering()->membership()) > 0)
				{
					$respond = true;
				}
			break;

			case 'registered':
				if (!User::isGuest())
				{
					$respond = true;
				}
			break;

			case 'anyone':
			default:
				$respond = true;
			break;
		}

		if (!$respond)
		{
			return;
		}

		// Prepare response
		$response = with(new \Hubzero\Base\Obj)
			->set('name', $this->_name)
			->set('title', Lang::txt('PLG_COURSES_' . strtoupper($this->_name)));

		// Check if our area is in the array of areas we want to return results for
		if ($response->get('name') == $active)
		{
			$view = $this->view('default', 'overview');
			$view->set('option', Request::getCmd('option', 'com_courses'))
			     ->set('controller', Request::getWord('controller', 'course'))
			     ->set('course', $course)
			     ->set('name', $this->_name);

			$response->set('html', $view->loadTemplate());
		}

		// Return the output
		return $response;
	}
}
