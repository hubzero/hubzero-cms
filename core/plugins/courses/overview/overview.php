<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */
/**
 * Courses Plugin class for the course overview page
 */
namespace Plugins\Courses\Overview;

use Hubzero\Plugin\Plugin;

class Overview extends Plugin
{
    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var    boolean
     */
// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_autoloadLanguage = true;

    /**
     * Return data on a course view (this will be some form of HTML)
     *
     * @param      object  $course Current course
     * @param      string  $active Current active area
     * @return     object
     */
    public function onCourseView($course, $active = null)
    {
        $response = with(new \Hubzero\Base\Obj())
            ->set('name', $this->_name)
            ->set('title', Lang::txt('PLG_COURSES_' . strtoupper($this->_name)));

        // Check if our area is in the array of areas we want to return results for
        if ($response->get('name') == $active) {
            $view = $this->view('default', 'overview');
            $view->set('option', Request::getCmd('option', 'com_courses'))
                ->set('controller', Request::getWord('controller', 'course'))
                ->set('course', $course);

            $response->set('html', $view->loadTemplate());
        }

        // Return the output
        return $response;
    }
}
