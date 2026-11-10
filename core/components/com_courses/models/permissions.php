<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Components\Courses\Tables;
use Hubzero\Base\Obj;
use Hubzero\Facades\Component;
use Hubzero\Facades\Request;
use Hubzero\Facades\User;

/**
 * Courses model class for course permissions
 */
class Permissions extends Obj
{
    protected $db;

    /**
     * Config
     *
     * @var object
     */
    private $config = null;

    /**
     * \Components\Courses\Models\Iterator
     *
     * @var object
     */
    private $managers = null;

    /**
     * \Components\Courses\Models\Member
     *
     * @var object
     */
    private $manager = null;

    /**
     * \Components\Courses\Models\Iterator
     *
     * @var object
     */
    private $students = null;

    /**
     * \Components\Courses\Models\Member
     *
     * @var object
     */
    private $student = null;

    /**
     * \Components\Courses\Models\Iterator
     *
     * @var object
     */
    private $courseId = null;

    /**
     * \Components\Courses\Models\Section
     *
     * @var object
     */
    private $offeringId = null;

    /**
     * \Components\Courses\Models\Section
     *
     * @var object
     */
    private $sectionId = null;

    /**
     * Constructor
     *
     * @param   integer $course_id   Course ID or alias
     * @param   integer $offering_id Course offering ID or alias
     * @param   integer $section_id  Course section ID or alias
     * @return  void
     */
    public function __construct(
        $course_id = null,
        $offering_id = null,
        $section_id = null,
    ) {
        $this->set('course_id', $course_id);
        $this->set('offering_id', $offering_id);
        $this->set('section_id', $section_id);
    }

    /**
     * Returns a reference to a permissions model
     *
     * @param   integer $course_id   Course ID or alias
     * @param   integer $offering_id Course offering ID or alias
     * @param   integer $section_id  Course section ID or alias
     * @return  object \Components\Courses\Models\Permissions
     */
    public static function &getInstance(
        $course_id = null,
        $offering_id = null,
        $section_id = null,
    ) {
        static $instance;

        if (!is_object($instance)) {
            $instance = new Permissions($course_id, $offering_id, $section_id);
        }

        return $instance;
    }

    /**
     * Get the component config
     *
     * @return  object Registry
     */
    public function config()
    {
        if (!isset($this->config)) {
            $this->config = Component::params('com_courses');
        }
        return $this->config;
    }

    /**
     * Returns a property of the object or the default value if not set
     *
     * @param   string $property The name of the property
     * @param   mixed  $default  The default value
     * @return  mixed  The value of the property
     */
    public function get($property, $default = null)
    {
        $property = self::propertyName($property);

        if (isset($this->{$property})) {
            return $this->{$property};
        }
        return $default;
    }

    /**
     * Modifies a property of the object, creating it if it does not already exist.
     *
     * @param   string $property The name of the property
     * @param   mixed  $value The value of the property to set
     * @return  mixed  Previous value of the property
     */
    public function set($property, $value = null)
    {
        $key = self::propertyName($property);
        $previous = isset($this->{$key}) ? $this->{$key} : null;
        $this->{$key} = $value;
        return $previous;
    }

    /**
     * Check if the current user has manager access
     * This is just a shortcut for the access check
     *
     * @return  boolean
     */
    public function isManager()
    {
        return $this->access('manage', 'section');
    }

    /**
     * Check if the current user is enrolled
     *
     * @param   integer $user_ID
     * @return  boolean
     */
    public function manager($user_id = null)
    {
        if (
            !isset($this->manager)
            || ($user_id !== null
                && (int) $this->manager->get('user_id') != $user_id)
        ) {
            $this->manager = null;

            if (isset($this->managers) && isset($this->managers[$user_id])) {
                $this->manager = $this->managers[$user_id];
            }

            if (!$this->manager) {
                $this->manager = Manager::getInstance(
                    $user_id,
                    $this->get('course_id'),
                    $this->get('offering_id'),
                    $this->get('section_id'),
                );
            }
        }

        return $this->manager;
    }

    /**
     * Get a list of managers
     *
     * @param   array   $filters Filters to build query from
     * @param   boolean $clear   Force a new dataset?
     * @return  mixed
     */
    public function managers($filters = [], $clear = false)
    {
        if (!isset($filters['course_id'])) {
            $filters['course_id'] = (int) $this->get('course_id');
        }
        if (!isset($filters['offering_id']) && $this->get('offering_id') !== null) {
            $filters['offering_id'] = (int) $this->get('offering_id');
        }
        if (!isset($filters['section_id']) && $this->get('section_id') !== null) {
            $filters['section_id'] = (int) $this->get('section_id');
        }
        $filters['student'] = 0;

        if (isset($filters['count']) && $filters['count']) {
            $tbl = new Tables\Member($this->db);

            return $tbl->count($filters);
        }

        if (!isset($this->managers) || !is_array($this->managers) || $clear) {
            $tbl = new Tables\Member($this->db);

            $results = [];

            if (($data = $tbl->find($filters))) {
                foreach ($data as $key => $result) {
                    if (!isset($results[$result->user_id])) {
                        $results[$result->user_id] = new Manager(
                            $result,
                            $this->get('id'),
                        );
                    } else {
                        // Course manager takes precedence over offering manager
                        $isCourse = $result->course_id
                            && !$result->offering_id
                            && !$result->section_id;
                        $isOffering = $result->course_id
                            && $result->offering_id
                            && !$result->section_id;
                        if ($isCourse) {
                            $results[$result->user_id] = new Manager(
                                $result,
                                $this->get('id'),
                            );
                        } elseif ($isOffering) {
                            // Course offering takes precedence over section manager
                            $results[$result->user_id] = new Manager(
                                $result,
                                $this->get('id'),
                            );
                        }
                    }
                }
            }

            $this->managers = $results;
        }

        return $this->managers;
    }

    /**
     * Check if the current user is a student
     * This is just a shortcut for the access check
     *
     * @return  boolean
     */
    public function isStudent()
    {
        return $this->config()->get('is-student');
        /*
        $noManage = !$this->access('manage', 'section');
        $canView = $this->access('view', 'section');
        if ($noManage && $canView) {
            return true;
        }
        return false;
        */
    }

    /**
     * Check if the current user is enrolled
     *
     * @param   integer $user_id
     * @return  boolean
     */
    public function student($user_id = null)
    {
        if (
            !isset($this->student)
            || ($user_id !== null
                && (int) $this->student->get('user_id') != $user_id)
        ) {
            $this->student = null;

            if (isset($this->students) && isset($this->students[$user_id])) {
                $this->student = $this->students[$user_id];
            }
        }

        if (!$this->student) {
            $this->student = \Components\Courses\Models\Student::getInstance(
                $user_id,
                $this->get('course_id'),
                null,
                $this->get('section_id'),
            );
        }

        return $this->student;
    }

    /**
     * Get a list of students
     *
     * @param   array   $filters Filters to build query from
     * @param   boolean $clear   Force a new dataset?
     * @return  mixed
     */
    public function students($filters = [], $clear = false)
    {
        if (!isset($filters['course_id'])) {
            $filters['course_id'] = (int) $this->get('course_id');
        }
        if (!isset($filters['offering_id'])) {
            $filters['offering_id'] = (int) $this->get('offering_id');
        }
        if (!isset($filters['section_id'])) {
            $filters['section_id'] = (int) $this->get('section_id');
        }
        $filters['student'] = 1;

        if (isset($filters['count']) && $filters['count']) {
            $tbl = new Tables\Member($this->db);

            return $tbl->count($filters);
        }

        if (!isset($this->students) || !is_array($this->students) || $clear) {
            $tbl = new Tables\Member($this->db);

            $results = [];

            if (($data = $tbl->find($filters))) {
                foreach ($data as $key => $result) {
                    $results[$result->user_id] = new Student(
                        $result,
                        $this->get('id'),
                    );
                }
            }

            // Previously: new \Components\Courses\Models\Iterator($results)
            $this->students = $results;
        }

        return $this->students;
    }

    /**
     * Calculate permissions
     *
     * @return  void
     */
    private function calculate()
    {
        // List of actions
        $actions = [
            'admin',
            'manage',
            'create',
            'delete',
            'edit',
            'edit-state',
            'edit-own',
            'view',
        ];

        // Are they an admin?
        if (
            $this->config()->get('access-admin-course')
            || $this->config()->get('access-manage-course')
        ) {
            // Admin - no need to go any further
            return;
        }

        /*if (!$this->config()->get('access-checked-course'))
        {

        }*/
        // If no course Id found
        if (!$this->get('course_id')) {
            // Try to get the course from request
            $course = Course::getInstance(Request::getString('gid', ''));
            if ($course->exists()) {
                $this->set('course_id', $course->get('id'));
            }
        }

        // Still no course? Can't do anything from here
        if ($this->get('course_id') === null) {
            return;
        }

        // Get the course
        if (!isset($course)) {
            $course = Course::getInstance($this->get('course_id'));
        }

        // Make sure the course exists
        if ($course->exists() && $course->isPublished()) {
            $this->config()->set('access-view-course', true);
        }

        // If they're logged in
        if (!User::isGuest()) {
            $manager = $this->manager(User::get('id'));
            // If they're NOT an admin
            if ($manager->exists()) {
                // If no specific permissions set
                if ($manager->get('course_id') == $this->get('course_id')) {
                    $noOffering = !$manager->get('offering_id');
                    $noSection = !$manager->get('section_id');
                    if ($noOffering && $noSection) {
                        if (!$manager->get('permissions')) {
                            foreach ($actions as $action) {
                                $key = 'access-' . $action;
                                $this->config()->set($key . '-section', true);
                                $this->config()->set($key . '-offering', true);
                                $this->config()->set($key . '-course', true);
                            }
                        } else {
                            // Merge permissions
                            $this->config()->merge($manager->get('permissions'));
                        }
                    } else {
                        $this->config()->set('access-view-section', true);
                        $this->config()->set('access-view-offering', true);
                        $this->config()->set('access-view-course', true);
                    }

                    $this->config()->set('is-manager', true);
                    $this->config()->set('access-checked-offering', true);
                    $this->config()->set('access-checked-section', true);
                }
            } elseif (($student = $this->student(User::get('id')))) {
                // If they're a student
                if ($student->exists()) {
                    // Allow them to view content
                    $this->config()->set('is-student', true);
                    $this->config()->set('access-view-course', true);
                }
            }
        }

        // Passed course level checks
        $this->config()->set('access-checked-course', true);

        // If no offering ID is found
        if (!$this->get('offering_id')) {
            $oid = Request::getString('offering', '');

            //$course = \Components\Courses\Models\Course::getInstance($this->get('course_id'));
            $offering = $course->offering($oid);
            if ($offering->exists()) {
                $this->set('offering_id', $offering->get('id'));
                $this->set('section_id', $offering->section()->get('id'));
            }
        }

        // Ensure we have an offering to work with
        if ($this->get('offering_id') === null) {
            return;
        }

        if (!isset($offering)) {
            $course = Course::getInstance($this->get('course_id'));
            $offering = $course->offering($this->get('offering_id'));
        }

        // Offering isn't available
        if (!$offering->exists() || !$offering->isPublished()) {
            return;
        }

        // If they're logged in
        if (!User::isGuest()) {
            $manager = $this->manager(User::get('id'));
            if ($manager->exists()) {
                // If no specific permissions set
                $courseMatch = $manager->get('course_id')
                    == $this->get('course_id');
                $offeringMatch = $manager->get('offering_id')
                    == $this->get('offering_id');
                if ($courseMatch && $offeringMatch) {
                    if (!$manager->get('permissions')) {
                        // If section_id is set, then they're a section manager
                        $sectionMatch = $manager->get('section_id')
                            == $this->get('section_id');
                        if ($sectionMatch) {
                            foreach ($actions as $action) {
                                $key = 'access-' . $action . '-section';
                                $this->config()->set($key, true);
                            }
                        } else {
                            foreach ($actions as $action) {
                                $keySection = 'access-' . $action . '-section';
                                $keyOffering = 'access-' . $action . '-offering';
                                $this->config()->set($keySection, true);
                                $this->config()->set($keyOffering, true);
                            }
                        }
                    } else {
                        // Merge permissions
                        $this->config()->merge($manager->get('permissions'));
                    }
                    $this->config()->set('access-view-offering', true);
                    $this->config()->set('access-view-section', true);
                }
            } elseif (($student = $this->student(User::get('id')))) {
                // If they're a student
                if ($student->exists()) {
                    // Allow them to view content
                    $this->config()->set('access-view-offering', true);

                    // Give section view privileges if in identified section
                    if ($student->get('section_id') == $this->get('section_id')) {
                        $this->config()->set('access-view-section', true);
                    }
                }
            }
        }

        $this->config()->set('access-checked-offering', true);
        $this->config()->set('access-checked-section', true);
    }

    /**
     * Check a user's authorization
     *
     * @param   string  $action Action to check
     * @param   string  $item   Item type to check action against
     * @return  boolean True if authorized, false if not
     */
    public function access($action = 'view', $item = 'course')
    {
        if (!$this->config()->get('access-checked-' . $item)) {
            $this->calculate();
        }

        if (User::authorise('core.admin', 'com_courses')) {
            return true;
        }

        return $this->config()->get('access-' . strtolower($action) . '-' . $item);
    }

    /**
     * The property holding a value callers name in snake case, as the
     * properties were once spelled: object_type for $objectType
     *
     * @param   string  $name
     * @return  string
     */
    private static function propertyName($name)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', (string) $name))));
    }
}
