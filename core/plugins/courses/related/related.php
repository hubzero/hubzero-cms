<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Courses Plugin class for related course
 */
namespace Plugins\Courses\Related;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\Request;
use Hubzero\Facades\App;

class Related extends Plugin
{
    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var  boolean
     */
// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_autoloadLanguage = true;

    /**
     * Return data on a course view (this will be some form of HTML)
     *
     * @param      object  $course Current course
     * @return     array
     */
    public function onCourseViewAfter($course)
    {
        $instructors = $course->instructors();
        if (count($instructors) <= 0) {
            return '';
        }

        $ids = array();
        foreach ($instructors as $instructor) {
            $ids[] = (int) $instructor->get('user_id');
        }

        $database = App::get('db');

        $query = "SELECT DISTINCT c.*
                    FROM `#__courses` AS c
                    JOIN `#__courses_members` AS m ON m.course_id=c.id AND m.student=0
                    LEFT JOIN `#__courses_roles` AS r ON r.id=m.role_id
                    WHERE r.alias='instructor'
                    AND m.user_id IN (" . implode(",", $ids) . ")
                    AND m.student=0
                    AND c.state=1
                    AND c.id !=" . $database->Quote($course->get('id')) . "
                    LIMIT " . (int) $this->params->get('display_limit', 3);

        $database->setQuery($query);
        if ($courses = $database->loadObjectList()) {
            $view = $this->view('default', 'overview');
            $view->option = Request::getCmd('option', 'com_courses');
            $view->controller = Request::getWord('controller', 'course');
            $view->course = $course;
            $view->name = $this->_name;
            $view->courses = $courses;
            $view->ids = $ids;

            // Return the output
            return $view->loadTemplate();
        }

        return '';
    }
}
