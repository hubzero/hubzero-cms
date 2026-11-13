<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Hubzero\Config\Registry;
use Components\Courses\Tables;
use Hubzero\Facades\Filesystem;
use Hubzero\Facades\Lang;
use Hubzero\Facades\User;
use Hubzero\Facades\App;
use Hubzero\Facades\Request;

/**
 * Courses model class for a course
 */
class Offering extends Base
{
    protected $membership;
    protected $plugins;

    /**
     * Table class name
     *
     * @var string
     */
    protected $tableClass = '\\Components\\Courses\\Tables\\Offering';

    /**
     * Object scope
     *
     * @var string
     */
    protected $scope = 'offering';

    /**
     * \Components\Courses\Models\Iterator
     *
     * @var object|null
     */
    private $units = null;

    /**
     * \Components\Courses\Models\Unit
     *
     * @var object|null
     */
    private $unit = null;

    /**
     * Page
     *
     * @var object|null
     */
    private $page = null;

    /**
     * Iterator
     *
     * @var array|null
     */
    private $pages = null;

    /**
     * Iterator
     *
     * @var object|null
     */
    private $announcements = null;

    /**
     * Iterator
     *
     * @var array|null
     */
    private $roles = null;

    /**
     * \Components\Courses\Models\Iterator
     *
     * @var array|null
     */
    private $members = null;

    /**
     * \Components\Courses\Models\Iterator
     *
     * @var object|null
     */
    private $member = null;

    /**
     * \Components\Courses\Models\Member
     *
     * @var object|null
     */
    private $manager = null;

    /**
     * \Components\Courses\Models\Iterator
     *
     * @var array|null
     */
    private $managers = null;

    /**
     * \Components\Courses\Models\Member
     *
     * @var object|null
     */
    private $student = null;

    /**
     * \Components\Courses\Models\Gradebook
     *
     * @var object|null
     */
    private $gradebook = null;

    /**
     * \Components\Courses\Models\Iterator
     *
     * @var object|null
     */
    private $sections = null;

    /**
     * \Components\Courses\Models\Section
     *
     * @var object|null
     */
    private $section = null;

    /**
     * \Components\Courses\Models\Iterator
     *
     * @var object
     **/
    private $assets = null;

    /**
     * \Components\Courses\Models\Permissions
     *
     * @var object|null
     */
    private $permissions = null;

    /**
     * URL to this object
     *
     * @var string|null
     */
    private $link = null;

    /**
     * Registry
     *
     * @var object
     */
    private $params = null;

    /**
     * Constructor
     *
     * @return     void
     */
    public function __construct($oid, $course_id = null)
    {
        $section = '!!default!!';

        $this->db = App::get('db');

        $this->table = new Tables\Offering($this->db);

        if ($oid) {
            if (is_numeric($oid) || is_string($oid)) {
                if (strstr($oid, ':')) {
                    $dot = strpos($oid, ':');
                    $section = substr($oid, $dot + 1);
                    $oid = substr($oid, 0, $dot);
                }
                $this->table->loadForCourse($oid, $course_id);
            } elseif (is_object($oid) || is_array($oid)) {
                $this->bind($oid);
            }
        }

        if ($this->exists()) { // && $section)
            $this->section($section);
        }
    }

    /**
     * Returns a reference to a course offering model
     *
     * This method must be invoked as:
     *     $offering = \Components\Courses\Models\Offering::getInstance($alias);
     *
     * @param   mixed  $oid  ID (int) or alias (string)
     * @return  object
     */
    public static function &getInstance($oid = null, $course_id = null)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = [];
        }

        $key = 0;

        if (is_numeric($oid) || is_string($oid)) {
            $key = $oid;
        } elseif (is_object($oid)) {
            $key = $oid->get('id');
        } elseif (is_array($oid)) {
            $key = $oid['id'];
        }

        if (!isset($instances[$key])) {
            $instances[$key] = new self($oid, $course_id);
        }

        return $instances[$key];
    }

    /**
     * Get a param value
     *
     * @param      string $key     Property to return
     * @param      mixed  $default Default value to return
     * @return     mixed
     */
    public function params($key = '', $default = null)
    {
        if (!($this->params instanceof Registry)) {
            $this->params = new Registry($this->get('params'));
        }
        if ($key) {
            return $this->params->get((string) $key, $default);
        }
        return $this->params;
    }

    /**
     * Method to get/set the current unit
     *
     * @param     mixed $id ID or alias of specific unit
     * @return    object \Components\Courses\Models\Unit
     */
    public function section($id = null)
    {
        if ($id instanceof Section) {
            $this->link = null;
            $this->section = $id;
            return $this->section;
        }

        // The isset test belongs in the mismatch too, not only in the
        // condition below: this is computed before that condition is reached,
        // so without it an offering with no current section is asked for the
        // id of nothing.
        $idMismatch = $id !== null
            && isset($this->section)
            && (int) $this->section->get('id') != $id
            && (string) $this->section->get('alias') != $id;
        if (!isset($this->section) || $idMismatch) {
            $this->section = null;
            $this->link = null; // Clear any potential existing data that may have another (prevous) section's info

            if (isset($this->sections)) {
                foreach ($this->sections() as $section) {
                    if ($id == '!!default!!') {
                        if ($section->get('is_default')) {
                            $this->section = $section;
                            break;
                        }
                    } elseif ((int) $section->get('id') == $id || (string) $section->get('alias') == $id) {
                        $this->section = $section;
                        break;
                    }
                }
            }

            if (!$this->section) {
                $this->section = Section::getInstance($id, $this->get('id'));
            }
        }
        return $this->section;
    }

    /**
     * Get a list of units for an offering
     *   Accepts either a numeric array index or a string [id, name]
     *   If index, it'll return the entry matching that index in the list
     *   If string, it'll return either a list of IDs or names
     *
     * @param      array $filters  Filters; with 'count' set, the sections are counted
     * @param      bool  $clear    Fetch the list again
     * @return     ($filters is array{} ? Iterator : int|string|Iterator|null)  The sections, or how many there are
     */
    public function sections($filters = [], $clear = false)
    {
        if (!isset($filters['offering_id'])) {
            $filters['offering_id'] = (int) $this->get('id');
        }
        if (!isset($filters['available']) && !App::isAdmin()) {
            $filters['available'] = true;
        }

        if (isset($filters['count']) && $filters['count']) {
            $tbl = new Tables\Section($this->db);

            return $tbl->count($filters);
        }

        if (!($this->sections instanceof \Traversable) || $clear) {
            $tbl = new Tables\Section($this->db);

            if (($results = $tbl->find($filters))) {
                foreach ($results as $key => $result) {
                    $results[$key] = new Section($result);
                }
            } else {
                $results = [];
            }

            $this->sections = new Iterator($results);
        }

        return $this->sections;
    }

    /**
     * Method to get/set the current unit
     *
     * @param     mixed $id ID or alias of specific unit
     * @return    object \Components\Courses\Models\Unit
     */
    public function unit($id = null)
    {
        if (
            !isset($this->unit)
            || ($id !== null && (int) $this->unit->get('id') != $id && (string) $this->unit->get('alias') != $id)
        ) {
            $this->unit = null;

            if (isset($this->units)) {
                foreach ($this->units() as $key => $unit) {
                    if ((int) $unit->get('id') == $id || (string) $unit->get('alias') == $id) {
                        $this->unit = $unit;
                        $units = $this->units();
                        $this->unit->siblings($units);
                        break;
                    }
                }
            }

            if (is_null($this->unit)) {
                $this->unit = Unit::getInstance($id, $this->get('id'));
            }
        }
        return $this->unit;
    }

    /**
     * Get a list of units for an offering
     *   Accepts either a numeric array index or a string [id, name]
     *   If index, it'll return the entry matching that index in the list
     *   If string, it'll return either a list of IDs or names
     *
     * @return     object|string|null
     */
    public function units($filters = [], $clear = false)
    {
        if (!isset($filters['offering_id'])) {
            $filters['offering_id'] = (int) $this->get('id');
        }
        if (!isset($filters['section_id'])) {
            $filters['section_id'] = (int) $this->section()->get('id');
        }

        if (isset($filters['count']) && $filters['count']) {
            $tbl = new Tables\Unit($this->db);

            return $tbl->count($filters);
        }

        if (!($this->units instanceof \Traversable) || $clear) {
            $tbl = new Tables\Unit($this->db);

            if (($results = $tbl->find($filters))) {
                foreach ($results as $key => $result) {
                    if (!$result->section_id) {
                        $result->section_id = (int) $this->section()->get('id');
                    }
                    $results[$key] = new Unit($result);
                }
            } else {
                $results = [];
            }

            $this->units = new Iterator($results);
        }

        return $this->units;
    }

    /**
     * Check if the current user has manager access
     * This is just a shortcut for the access check
     *
     * @return     boolean
     */
    public function isManager()
    {
        return $this->access('manage', 'section');
    }

    /**
     * Check if the current user is enrolled
     *
     * @return     boolean
     */
    public function manager($user_id = null)
    {
        if (
            !isset($this->manager)
            || ($user_id !== null && (int) $this->manager->get('user_id') != $user_id)
        ) {
            $this->manager = null;

            if (isset($this->managers) && isset($this->managers[$user_id])) {
                $this->manager = $this->managers[$user_id];
            } else {
                $this->manager = Manager::getInstance(
                    $user_id,
                    $this->get('course_id'),
                    $this->get('id'),
                    $this->section()->get('id'),
                );
            }
        }

        return $this->manager;
    }

    /**
     * Get a list of units for an offering
     *   Accepts either a numeric array index or a string [id, name]
     *   If index, it'll return the entry matching that index in the list
     *   If string, it'll return either a list of IDs or names
     *
     * @param      array   $filters Filters to build query from
     * @param      boolean $clear   Force a new dataset?
     * @return     mixed
     */
    public function managers($filters = [], $clear = false)
    {
        if (!isset($filters['course_id'])) {
            $filters['course_id'] = (int) $this->get('course_id');
        }
        if (!isset($filters['offering_id'])) {
            $filters['offering_id'] = (int) $this->get('id');
        }
        if (!isset($filters['section_id'])) {
            $filters['section_id'] = (int) $this->section()->get('id');
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
                        $results[$result->user_id] = new Manager($result, $this->get('id'));
                    } else {
                        // Course manager takes precedence over offering manager
                        if ($result->course_id && !$result->offering_id && !$result->section_id) {
                            $results[$result->user_id] = new Manager($result, $this->get('id'));
                        } elseif ($result->course_id && $result->offering_id && !$result->section_id) {
                            // Course offering takes precedence over section manager
                            $results[$result->user_id] = new Manager($result, $this->get('id'));
                        }
                    }
                }
            }

            $this->managers = $results;
        }

        return $this->managers;
    }

    /**
     * Check if the current user has manager access
     * This is just a shortcut for the access check
     *
     * @return     boolean
     */
    public function isStudent()
    {
        if (!$this->access('manage', 'section') && $this->access('view', 'section')) {
            return true;
        }
        return false;
    }

    /**
     * Check if the current user is enrolled
     *
     * @return     boolean
     */
    public function student($user_id = null)
    {
        if (
            !isset($this->student)
            || ($user_id !== null && (int) $this->student->get('user_id') != $user_id)
        ) {
            $this->student = null;

            /*if (isset($this->members) && isset($this->members[$user_id]))
            {
                $this->student = $this->members[$user_id];
            }*/
            if (isset($this->members)) { // && isset($this->members[$user_id]))
                foreach ($this->members as $member) {
                    $matchesUser = $member->get('user_id') == $user_id;
                    $matchesSection = $member->get('section_id') == $this->section()->get('id');
                    if ($matchesUser && $matchesSection && $member->get('student')) {
                        $this->student = $member;
                        break;
                    }
                }
            }
        }

        if (!$this->student) {
            $this->student = Student::getInstance(
                $user_id,
                $this->get('course_id'),
                $this->get('id'),
                $this->section()->get('id'),
            );
        }

        return $this->student;
    }

    /**
     * Get offering gradebook
     *
     * @return     object
     */
    public function gradebook($oid = null)
    {
        if (!isset($this->gradebook)) {
            $course = new Course($this->get('course_id'));
            $course->offering($this->get('id'));
            $course->offering()->section($this->section()->get('id'));
            $this->gradebook = new GradeBook($oid, $course);
        }

        return $this->gradebook;
    }

    /**
     * Get a list of assets for an offering
     *
     * Accepts an array of filters to apply to the list of assets
     *
     * @param      array $filters Filters to apply
     * @return     object \Components\Courses\Models\Iterator
     */
    public function assets($filters = [])
    {
        if (!($this->assets instanceof \Traversable)) {
            if (!isset($filters['asset_scope_id'])) {
                $filters['asset_scope_id'] = (int) $this->get('id');
            }
            if (!isset($filters['asset_scope'])) {
                $filters['asset_scope']    = 'offering';
            }

            $tbl = new Tables\Asset($this->db);

            if (($results = $tbl->find(['w' => $filters]))) {
                foreach ($results as $key => $result) {
                    $results[$key] = new Asset($result);
                }
            } else {
                $results = [];
            }

            $this->assets = new Iterator($results);
        }

        return $this->assets;
    }

    /**
     * Check if the current user is enrolled
     *
     * @return     boolean
     */
    public function students($filters = [], $clear = false)
    {
        $filters['student'] = 1;

        return $this->members($filters, $clear);
    }

    /**
     * Check if the current user is enrolled
     *
     * @return     Member  the user's membership, empty if they have none
     */
    public function member($user_id = null)
    {
        if (
            !isset($this->member)
            || ($user_id !== null && (int) $this->member->get('user_id') != $user_id)
        ) {
            $this->member = null;

            if (isset($this->members)) { // && isset($this->members[$user_id]))
                foreach ($this->members as $member) {
                    $matchesUser = $member->get('user_id') == $user_id;
                    $matchesSection = $member->get('section_id') == $this->section()->get('id');
                    if ($matchesUser && $matchesSection) {
                        $this->member = $member;
                        break;
                    }
                }
            }
        }

        if (!$this->member) {
            $this->member = Member::getInstance(
                $user_id,
                $this->get('course_id'),
                $this->get('id'),
                $this->section()->get('id'),
            );
        }

        return $this->member;
    }

    /**
     * Get a list of units for an offering
     *   Accepts either a numeric array index or a string [id, name]
     *   If index, it'll return the entry matching that index in the list
     *   If string, it'll return either a list of IDs or names
     *
     * @param      array   $filters Filters to build query from
     * @param      boolean $clear   Force a new dataset?
     * @return     mixed
     */
    public function members($filters = [], $clear = false)
    {
        if (!isset($filters['course_id'])) {
            $filters['course_id'] = (int) $this->get('course_id');
        }
        if (!isset($filters['offering_id'])) {
            $filters['offering_id'] = [0, (int) $this->get('id')];
        }
        if (!isset($filters['section_id'])) {
            $filters['section_id'] = [0, (int) $this->section()->get('id')];
        }
        if (!isset($filters['sort'])) {
            $filters['sort'] = 'student ASC, section_id ASC, offering_id';
        }
        if (!isset($filters['sort_Dir'])) {
            $filters['sort_Dir'] = 'ASC';
        }

        if (isset($filters['count']) && $filters['count']) {
            $tbl = new Tables\Member($this->db);

            return $tbl->count($filters);
        }

        if (!isset($this->members) || !is_array($this->members) || $clear) {
            $tbl = new Tables\Member($this->db);

            $results = [];

            if (($data = $tbl->find($filters))) {
                $mdl = '\\Components\\Courses\\Models\\Member';
                if (isset($filters['student']) && $filters['student']) {
                    $mdl = '\\Components\\Courses\\Models\\Student';
                } elseif (isset($filters['student']) && !$filters['student']) {
                    $mdl = '\\Components\\Courses\\Models\\Manager';
                }

                foreach ($data as $key => $result) {
                    if (!isset($results[$result->user_id])) {
                        $results[$key] = new $mdl($result);
                    }
                }
            }

            $this->members = $results;
        }

        return $this->members;
    }

    /**
     * Get a list of memerships for a user
     *
     * @param      array|int $user_id Filters to build query from
     * @return     mixed
     */
    public function membership($user_id = 0)
    {
        $filters = [];
        if (!isset($filters['course_id'])) {
            $filters['course_id'] = (int) $this->get('course_id');
        }
        /*if (!isset($filters['offering_id']))
        {
            $filters['offering_id'] = (int) $this->get('id');
        }*/
        if (!$user_id) {
            $user_id = User::get('id');
        }
        $filters['user_id'] = (int) $user_id;
        $filters['sort'] = 'offering_id ASC, student';
        $filters['sort_Dir'] = 'ASC';

        if (isset($filters['count']) && $filters['count']) {
            $tbl = new Tables\Member($this->db);

            return $tbl->count($filters);
        }

        if (!isset($this->membership[$user_id]) || !is_array($this->membership[$user_id])) {
            $tbl = new Tables\Member($this->db);

            $results = [];

            if (($results = $tbl->find($filters))) {
                foreach ($results as $key => $result) {
                    $mdl = '\\Components\\Courses\\Models\\Member';
                    if ($result->student) {
                        $mdl = '\\Components\\Courses\\Models\\Student';
                    } else {
                        $mdl = '\\Components\\Courses\\Models\\Manager';
                    }
                    $results[$key] = new $mdl($result);
                }
            }

            $this->membership[$user_id] = $results;
        }

        return $this->membership[$user_id];
    }

    /**
     * Get a list of units for an offering
     *   Accepts either a numeric array index or a string [id, name]
     *   If index, it'll return the entry matching that index in the list
     *   If string, it'll return either a list of IDs or names
     *
     * @param      array   $filters Filters to build query from
     * @param      boolean $clear   Force a new dataset?
     * @return     mixed
     */
    public function roles($filters = [], $clear = false)
    {
        if (!isset($filters['offering_id'])) {
            $filters['offering_id'] = [0, (int) $this->get('id')];  // 0 = default roles
        }

        if (isset($filters['count']) && $filters['count']) {
            $tbl = new Tables\Role($this->db);

            return $tbl->count($filters);
        }
        if (!isset($this->roles) || !is_array($this->roles) || $clear) {
            $tbl = new Tables\Role($this->db);

            if (!($results = $tbl->find($filters))) {
                $results = [];
            }

            $this->roles = $results;
        }

        return $this->roles;
    }

    /**
     * Check if the current user is enrolled
     *
     * @return     boolean
     */
    public function page($url = null)
    {
        if (
            !isset($this->page)
            || ($url !== null && (int) $this->page['url'] != $url)
        ) {
            $this->page = null;

            //if (isset($this->pages) && is_array($this->pages))
            //{
            foreach ($this->pages() as $page) {
                if ($page->get('url') == $url) {
                    $this->page = $page;
                }
            }
            //}
            if (!$this->page) {
                $this->page = new Page(0);
            }
        }

        return $this->page;
    }

    /**
     * Get a list of units for an offering
     *   Accepts either a numeric array index or a string [id, name]
     *   If index, it'll return the entry matching that index in the list
     *   If string, it'll return either a list of IDs or names
     *
     * @return     array|string|null
     */
    public function pages($filters = [], $reload = false)
    {
        if (!isset($filters['offering_id'])) {
            $filters['offering_id'] = (int) $this->get('id');
        }
        if (!isset($filters['active'])) {
            $filters['active'] = 1;
        }
        if (!isset($filters['sort'])) {
            $filters['sort'] = 'ordering';
        }

        if (isset($filters['count']) && $filters['count']) {
            $tbl = new Tables\Page($this->db);

            return $tbl->count($filters);
        }

        if (!isset($this->pages) || !is_array($this->pages) || $reload) {
            $tbl = new Tables\Page($this->db);

            $results = [];

            if (($data = $tbl->find($filters))) {
                foreach ($data as $key => $result) {
                    $results[] = new Page($result);
                }
            }

            $this->pages = $results;
        }

        return $this->pages;
    }

    /**
     * Get a list of announcements for an offering
     *   Accepts an array of filters to retrieve data by
     *
     * @param      array $filters
     * @return     mixed
     */
    public function announcements($filters = [])
    {
        $defaultFilters = [
            'offering_id' => (int) $this->get('id'),
            'section_id' => (int) $this->section()->get('id'),
            'state' => 1,
        ];
        $filters = array_merge($defaultFilters, $filters);

        if (isset($filters['count']) && $filters['count']) {
            $tbl = new Tables\Announcement($this->db);

            return $tbl->count($filters);
        }

        if (!isset($this->announcements) || !is_array($this->announcements)) {
            $tbl = new Tables\Announcement($this->db);

            $results = [];

            if (($data = $tbl->find($filters))) {
                foreach ($data as $key => $result) {
                    $results[] = new Announcement($result);
                }
            }

            $this->announcements = new Iterator($results);
        }

        return $this->announcements;
    }

    /**
     * Get a list of plugins
     *
     * @param   string  $name
     * @return  mixed
     */
    public function plugins($name = null)
    {
        if (!isset($this->plugins) || !is_array($this->plugins)) {
            $this->importPlugin('courses');

            $course = Course::getInstance($this->get('course_id'));
            $this->plugins = $this->trigger('onCourse', [$course, $this, true]);
        }

        if ($name !== null) {
            foreach ($this->plugins as $plugin) {
                if ($plugin->get('name') == $name) {
                    return $plugin;
                }
            }
        }

        return $this->plugins;
    }

    /**
     * Check a user's authorization
     *
     * @param      string $action Action to check
     * @return     boolean True if authorized, false if not
     */
    public function access($action = 'view', $item = 'offering')
    {
        if (!isset($this->permissions)) {
            $this->permissions = Permissions::getInstance();
            $this->permissions->set('course_id', $this->get('course_id'));
            $this->permissions->set('offering_id', $this->get('id'));
            $this->permissions->set('section_id', $this->section()->get('id'));
        }
        return $this->permissions->access($action, $item);
    }

    /**
     * Add one or more user IDs or usernames to the managers list
     *
     * @return    void
     */
    public function add($data = [], $role_id = 'student')
    {
        if (!is_array($data)) {
            $data = [$data];
        }

        $role = new Tables\Role($this->db);
        $role->load($role_id);
        if (is_string($role_id)) {
            $role_id = $role->get('id');
        }

        foreach ($data as $result) {
            $user_id = (int) $this->userId($result);

            $model = Member::getInstance(
                $user_id,
                $this->get('course_id'),
                $this->get('id'),
                $this->section()->get('id'),
            );
            $model->set('user_id', $user_id);
            $model->set('course_id', $this->get('course_id'));
            $model->set('offering_id', $this->get('id'));
            $model->set('section_id', $this->section()->get('id'));
            $model->set('role_id', $role_id);
            if ($role->get('alias') == 'student') {
                $model->set('student', 1);
            }
            if (!$model->store()) {
                $this->setError($model->getError());
                continue;
            }

            $this->managers[$user_id] = $model;
        }
    }

    /**
     * Remove one or more user IDs or usernames to the managers list
     *
     * @param     array $data List of IDs or usernames
     * @return    void
     */
    public function remove($data = [])
    {
        if (!is_array($data)) {
            $data = [$data];
        }
        if (count($data) > 0) {
            $this->members();

            foreach ($data as $result) {
                $user_id = $this->userId($result);

                if (isset($this->members[$user_id])) {
                    $this->members[$user_id]->delete();
                    unset($this->members[$user_id]);
                }
            }
            $this->managers = null;
        }
    }

    /**
     * Return an ID for a user
     *
     * @param     mixed $user User ID or username
     * @return    integer
     */
    private function userId($user)
    {
        if (is_numeric($user)) {
            return $user;
        }

        $this->db->setQuery("SELECT id FROM `#__users` WHERE username=" . $this->db->quote($user));

        if (($result = $this->db->loadResult())) {
            return $result;
        }

        return 0;
    }

    /**
     * Create a section linked to this offering
     *
     * @return  boolean True on success, False on error
     */
    public function makeSection($alias = '__default', $is_default = 1)
    {
        $section = new Section();
        $section->set('offering_id', $this->get('id'));
        $section->set('alias', $alias);
        $section->set('title', Lang::txt('Default'));
        $section->set('state', 1);
        $section->set('is_default', $is_default);
        $section->set('enrollment', $this->config('default_enrollment', 0));
        $section->set('start_date', $this->get('start_date'));
        $section->set('end_date', $this->get('end_date'));
        $section->set('publish_up', $this->get('publish_up'));
        $section->set('publish_down', $this->get('publish_down'));
        if (!$section->store()) {
            $this->setError($section->getError());
            return false;
        }

        $this->sections = null;

        return true;
    }

    /**
     * Store changes to the database
     *
     * @param   boolean $validate Validate data?
     * @return  boolean True on success, False on error
     */
    public function store($validate = true)
    {
        if (empty($this->db)) {
            return false;
        }

        $isNew = ($this->get('id') ? false : true);

        // Validate data?
        if ($validate) {
            if (!$this->table->check()) {
                $this->setError($this->table->getError());
                return false;
            }
        }

        // Store data
        if (!$this->table->store()) {
            $this->setError($this->table->getError());
            return false;
        }

        // Check for sections
        // An offering MUST have at least one __default section
        if ($validate && $this->sections()->total() <= 0) {
            if (!$this->makeSection('__default', 1)) {
                return false;
            }
        }

        $this
            ->importPlugin('courses')
            ->trigger('onOfferingSave', [$this]);

        if ($isNew) {
            $this->log($this->get('id'), $this->scope, 'create');
        }

        return true;
    }

    /**
     * Delete an entry and associated data
     *
     * @return     boolean True on success, false on error
     */
    public function delete()
    {
        // Remove pages
        foreach ($this->pages(['active' => [0, 1]], true) as $page) {
            if (!$page->delete()) {
                $this->setError($page->getError());
            }
        }

        // Remove announcements
        foreach ($this->announcements(['section_id' => -1, 'state' => -1]) as $announcement) {
            if (!$announcement->delete()) {
                $this->setError($announcement->getError());
            }
        }

        // Remove any units
        foreach ($this->units(['section_id' => -1], true) as $unit) {
            if (!$unit->delete()) {
                $this->setError($unit->getError());
            }
        }

        // Remove sections
        // Each section will also remove any students in that section
        foreach ($this->sections() as $section) {
            if (!$section->delete()) {
                $this->setError($section->getError());
            }
        }

        $value = parent::delete();

        $this
            ->importPlugin('courses')
            ->trigger('onOfferingDelete', [$this]);

        return $value;
    }

    /**
     * Copy an entry and associated data
     *
     * @param   integer $course_id New course to copy to
     * @param   boolean $deep      Copy associated data?
     * @return  boolean True on success, false on error
     */
    public function copy($course_id = null, $deep = true)
    {
        // Get some old info we may need
        //  - Offering ID
        //  - Course ID
        $o_id = $this->get('id');
        $c_id = $this->get('course_id');
        $oldOfferingAssets = $this->assets();

        // Reset the ID. This will force store() to create a new record.
        $this->set('id', 0);
        // Are we copying to a new course?
        if ($course_id) {
            $this->set('course_id', $course_id);
        } else {
            // Copying to the same course so we want to distinguish
            // this offering from the one we copied from
            $this->set('title', $this->get('title') . ' (copy)');
            $this->set('alias', $this->get('alias') . '_copy');
        }
        if (!$this->store()) {
            return false;
        }

        if ($deep) {
            // Copy pages
            foreach ($this->pages(['offering_id' => $o_id, 'active' => [0, 1]], true) as $page) {
                if (!$page->copy($this->get('course_id'), $this->get('id'))) {
                    $this->setError($page->getError());
                }
            }

            // Copy units
            foreach ($this->units(['offering_id' => $o_id, 'section_id' => -1], true) as $unit) {
                if (!$unit->copy($this->get('id'))) {
                    $this->setError($unit->getError());
                }
            }

            // Copy logo
            if ($file = $this->logo('file')) {
                $uploadPath = DS . trim($this->config('uploadpath', '/site/courses'), '/');
                $src  = $uploadPath . DS . $c_id . '/offerings/' . $o_id . DS . $file;
                if (file_exists(PATH_APP . $src)) {
                    $dest = $uploadPath . DS . $this->get('course_id')
                        . '/offerings/' . $this->get('id');

                    if (!is_dir(PATH_APP . $dest)) {
                        if (!Filesystem::makeDirectory(PATH_APP . $dest)) {
                            $this->setError(Lang::txt('UNABLE_TO_CREATE_UPLOAD_PATH'));
                        }
                    }

                    $dest .= DS . $file;

                    if (!Filesystem::copy(PATH_APP . $src, PATH_APP . $dest)) {
                        $this->setError(Lang::txt('Failed to copy offering logo.'));
                    }
                }
            }

            // Copy assets (grab the assets from the original offering)
            if ($oldOfferingAssets) {
                foreach ($oldOfferingAssets as $asset) {
                    $oldAssetId = $asset->get('id');
                    if (!$asset->copy()) {
                        $this->setError($asset->getError());
                    } else {
                        // Copy asset associations
                        $tbl = new Tables\AssetAssociation($this->db);
                        $findParams = [
                            'scope_id' => $o_id,
                            'scope' => 'offering',
                            'asset_id' => $oldAssetId,
                        ];
                        foreach ($tbl->find($findParams) as $aa) {
                            $tbl->bind($aa);
                            $tbl->id = 0;
                            $tbl->scope_id = $this->get('id');
                            $tbl->asset_id = $asset->get('id');
                            if (!$tbl->store()) {
                                $this->setError($tbl->getError());
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Generate and return various links to the entry
     * Link will vary depending upon action desired, such as edit, delete, etc.
     *
     * @param      string $type The type of link to return
     * @return     string
     */
    public function link($type = '')
    {
        if (!isset($this->link)) {
            if (!$this->get('course_alias')) {
                $course = Course::getInstance($this->get('course_id'));
                $this->set('course_alias', $course->get('alias'));
            }
            $this->link = 'index.php?option=com_courses&controller=offering&gid='
                . $this->get('course_alias') . '&offering=' . $this->alias();
        }

        // If it doesn't exist or isn't published
        switch (strtolower($type)) {
            case 'edit':
                $link = $this->link . '&task=edit';
                break;

            case 'delete':
                $link = $this->link . '&task=delete';
                break;

            case 'enroll':
                $this->importPlugin('courses');

                $course = Course::getInstance($this->get('course_id'));

                $data = $this->trigger('onCourseEnrollLink', [
                    $course,
                    $this,
                    $this->section(),
                ]);
                if ($data && count($data) > 0) {
                    $link = implode('', $data);
                } else {
                    $link = $this->link . '&task=enroll';
                }
                break;

            case 'overview':
                $link = 'index.php?option=com_courses&gid=' . $this->get('course_alias');
                break;

            case 'permalink':
            default:
                $link = $this->link;
                break;
        }

        return $link;
    }

    /**
     * Get the offering alias with section alias
     *
     * @return     string
     */
    public function alias()
    {
        $suffix = $this->section()->get('is_default') ? '' : ':' . $this->section()->get('alias');
        return $this->get('alias') . $suffix;
    }

    /**
     * Get the offering logo
     *
     * @param      string $rtrn Property to return
     * @return     string
     */
    public function logo($rtrn = '')
    {
        $rtrn = strtolower(trim($rtrn));

        // Return just the file name
        if ($rtrn == 'file') {
            return $this->params('logo');
        }

        // Build the path
        $uploadPath = '/' . trim($this->config('uploadpath', '/site/courses'), '/');
        $path = $uploadPath . '/' . $this->get('course_id') . '/offerings/' . $this->get('id');

        // Return just the upload path?
        if ($rtrn == 'path') {
            return $path;
        }

        // Do we have a logo set?
        if ($file = $this->params('logo')) {
            // Return the web path to the image
            $path .= '/' . $file;
            if (file_exists(PATH_APP . $path)) {
                $path = str_replace('/administrator', '', Request::base(true)) . $path;
            }
            if ($rtrn == 'url') {
                return $this->link() . '&active=logo';
            }
            return $path;
        }

        return '';
    }
}
