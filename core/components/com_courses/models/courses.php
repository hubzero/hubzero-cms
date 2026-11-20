<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Components\Courses\Tables;
use Hubzero\Base\Obj;
use Hubzero\Facades\App;

/**
 * Courses model class for a course
 */
class Courses extends Obj
{
    /**
     * Tables\Course
     *
     * @var \Components\Courses\Tables\Course
     */
    private $tbl;

    /**
     * Database
     *
     * @var \Hubzero\Database\Driver
     */
    private $db = null;

    /**
     * \Components\Courses\Models\Iterator
     *
     * @var \Components\Courses\Models\Iterator|null
     */
    private $courses = null;

    /**
     * \Components\Courses\Models\Course
     *
     * @var Course|null
     */
    private $course = null;

    /**
     * Constructor
     *
     * @return     void
     */
    public function __construct()
    {
        $this->db = App::get('db');

        $this->tbl = new Tables\Course($this->db);
    }

    /**
     * Returns a reference to a course model
     *
     * This method must be invoked as:
     *     $offering = \Components\Courses\Models\Course::getInstance($alias);
     *
     * @param      mixed $oid Course ID (int) or alias (string)
     * @return     self
     */
    public static function &getInstance($oid = 0)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = [];
        }

        if (!isset($instances[$oid])) {
            $instances[$oid] = new self();
        }

        return $instances[$oid];
    }

    /**
     * Method to get/set the current unit
     *
     * @param     mixed $id ID or alias of specific unit
     * @return    Course|null
     */
    public function course($id = null)
    {
        if (
            !isset($this->course)
            || ($id !== null && (int) $this->course->get('id') != $id && (string) $this->course->get('alias') != $id)
        ) {
            $this->course = null;

            if (isset($this->courses)) {
                foreach ($this->courses() as $key => $course) {
                    if ((int) $course->get('id') == $id || (string) $course->get('alias') == $id) {
                        $this->course = $course;
                        break;
                    }
                }
            } else {
                $this->course = Course::getInstance($id);
            }
        }
        return $this->course;
    }

    /**
     * Get a list of courses
     *   Accepts an array of filters to build query from
     *
     * @param      array<mixed>   $filters Filters to build query from
     * @param      boolean $clear   Clear cached results?
     * @return     mixed
     */
    public function courses($filters = [], $clear = false)
    {
        if (isset($filters['count']) && $filters['count']) {
            return $this->tbl->getCount($filters);
        }

        if (!($this->courses instanceof \Traversable) || $clear) {
            if (($results = $this->tbl->getRecords($filters))) {
                foreach ($results as $key => $result) {
                    $results[$key] = new Course($result);
                }
            } else {
                $results = [];
            }

            $this->courses = new Iterator($results);
        }

        return $this->courses;
    }

    /**
     * Get a list of courses
     *   Accepts an array of filters to build query from
     *
     * @param   mixed  $uid
     * @param   string  $type
     * @param   mixed  $limit
     * @param   int  $start
     * @return     mixed
     */
    public function userCourses($uid, $type = 'all', $limit = null, $start = 0)
    {
        if (($results = $this->tbl->getUserCourses($uid, $type, $limit, $start))) {
            foreach ($results as $key => $result) {
                $results[$key] = new Course($result);
            }
        } else {
            $results = [];
        }

        return new Iterator($results);
    }

    /**
     * Get a list of courses
     *   Accepts an array of filters to build query from
     *
     * @param   string  $what
     * @param      array<mixed> $filters Filters to build query from
     * @param   bool  $clear
     * @return     mixed
     */
    public function tags($what = 'cloud', $filters = [], $clear = false)
    {
        $ct = new Tags();

        return $ct->render($what, $filters, $clear);
    }

    /**
     * Turn a string of tags to an array
     *
     * @param      string $tag Tag string
     * @param   string  $remove
     * @return     mixed
     */
    public function parseTags($tag, $remove = '')
    {
        $ct = new Tags();
        return $ct->parseTags($tag, $remove);
    }
}
