<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\Courses\Models\Course;
use Hubzero\Facades\Request;
use Hubzero\Facades\App;

/**
 * API controller for the courses component
 */
class Base extends ApiController
{
    /**
     * @var  int
     */
    protected $course_id;
    /**
     * @var  string
     */
    protected $offering_alias;
    /**
     * @var  mixed
     */
    protected $section_id = null;
    /**
     * @var  \Components\Courses\Models\Course
     */
    protected $course;

    /**
     * Checks to ensure appropriate authorization
     *
     * @param   string  $action
     * @return  bool
     * @throws  \Exception
     */
    protected function authorizeOrFail($action = 'manage')
    {
        // Make sure we have a valid user
        $this->requiresAuthentication();

        // Get the course id
        $this->course_id      = Request::getInt('course_id', 0);
        $this->offering_alias = Request::getCmd('offering', '');
        $this->section_id     = Request::getInt('section_id', '');

        // Load the course page
        $course   = Course::getInstance($this->course_id);
        $offering = $course->offering($this->offering_alias);
        $section  = $course->offering()->section($this->section_id);

        if (!$course->access($action)) {
            App::abort(401, 'Unauthorized');
        }

        // Set the course for reuse later
        $this->course = $course;

        return true;
    }
}
