<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Components\Courses\Tables;
use Hubzero\Config\Registry;
use Hubzero\Facades\Filesystem;
use Hubzero\Facades\Lang;
use Hubzero\Facades\User;

/**
 * Courses model class for a course
 */
class Course extends Base
{
    /**
     * Table class name
     *
     * @var string
     */
    protected $tableClass = '\\Components\\Courses\\Tables\\Course';

    /**
     * Object scope
     *
     * @var string
     */
    protected $scope = 'course';

    /**
     * \Components\Courses\Models\Offering
     *
     * @var object|null
     */
    private $offering = null;

    /**
     * \Components\Courses\Models\Iterator
     *
     * @var object|null
     */
    private $offerings = null;

    /**
     * \Components\Courses\Models\Permissions
     *
     * @var object|null
     */
    private $permissions = null;

    /**
     * \Components\Courses\Models\Offering
     *
     * @var object|null
     */
    private $manager = null;

    /**
     * List of managers
     *
     * @var array|null
     */
    private $managers = null;

    /**
     * List of students
     *
     * @var array|null
     */
    private $students = null;

    /**
     * \Components\Courses\Models\Offering
     *
     * @var object|null
     */
    private $page = null;

    /**
     * \Components\Courses\Models\Iterator
     *
     * @var array|null
     */
    private $pages = null;

    /**
     * List of plugins available for a given event
     *
     * @var array
     */
    private $plugins = [];

    /**
     * URL to this object
     *
     * @var string|null
     */
    private $base = null;

    /**
     * Certificate
     *
     * @var object|null
     */
    private $certificate = null;

    /**
     * Constructor
     *
     * @param      integer $oid Course ID or alias
     * @return     void
     */
    public function __construct($oid)
    {
        parent::__construct($oid);

        $this->config()->merge(new Registry($this->get('params')));

        if (!isset($this->permissions)) {
            $this->permissions = Permissions::getInstance();
            $this->permissions->set('course_id', $this->get('id'));
        }
    }

    /**
     * Returns a reference to a course model
     *
     * This method must be invoked as:
     *     $offering = \Components\Courses\Models\Course::getInstance($alias);
     *
     * @param      mixed $oid Course ID (int) or alias (string)
     * @return     object \Components\Courses\Models\Course
     */
    public static function &getInstance($oid = 0)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = [];
        }

        if (!isset($instances[$oid])) {
            $instances[$oid] = new self($oid);
        }

        return $instances[$oid];
    }

    /**
     * Check if the current user has manager access
     * This is just a shortcut for the access check
     *
     * @return     boolean
     */
    public function isManager($user_id = 0)
    {
        if ((int) $user_id) {
            $this->permissions->isManager($user_id);
        }
        return $this->permissions->isManager();
    }

    /**
     * Check if the current user has manager access
     * This is just a shortcut for the access check
     *
     * @return     boolean
     */
    public function isStudent($user_id = 0)
    {
        if ((int) $user_id) {
            $this->permissions->isStudent($user_id);
        }
        return $this->permissions->isStudent();
    }

    /**
     * Set and get a specific offering
     *
     * @return     \Components\Courses\Models\Offering
     */
    public function offering($id = null)
    {
        // If the current offering isn't set
        //    OR the ID passed doesn't equal the current offering's ID or alias
        //
        // The isset test belongs in the mismatch too, not only in the
        // condition below: this is computed before that condition is reached,
        // so without it a course with no current offering is asked for the id
        // of nothing.
        $idMismatch = $id !== null
            && isset($this->offering)
            && (int) $this->offering->get('id') != $id
            && (string) $this->offering->get('alias') != $id;
        if (!isset($this->offering) || $idMismatch) {
            // Reset current offering
            $this->offering = null;

            // If the list of all offerings is available ...
            if (isset($this->offerings)) {
                // Find an offering in the list that matches the ID passed
                foreach ($this->offerings() as $key => $offering) {
                    if ((int) $offering->get('id') == $id || (string) $offering->get('alias') == $id) {
                        // Set current offering
                        $this->offering = $offering;
                        break;
                    }
                }
            }

            if (is_null($this->offering)) {
                // Get current offering
                $this->offering = Offering::getInstance($id, $this->get('id'));
            }
        }
        // Return current offering
        return $this->offering;
    }

    /**
     * Get a list of offerings for a course
     *   Accepts an array of filters to build query from
     *
     * @param      array $filters Filters to build query from
     * @return     mixed
     */
    public function offerings($filters = [], $clear = false)
    {
        if (!isset($filters['course_id'])) {
            $filters['course_id'] = (int) $this->get('id');
        }

        // Perform a record count?
        if (isset($filters['count']) && $filters['count']) {
            $tbl = new Tables\Offering($this->db);

            return $tbl->count($filters);
        }

        // Is the data is not set OR is it not the right type?
        if (!($this->offerings instanceof \Traversable) || $clear) {
            $tbl = new Tables\Offering($this->db);

            // Attempt to get database results
            if (($results = $tbl->find($filters))) {
                // Loop through each result and turn into a model object
                foreach ($results as $key => $result) {
                    $results[$key] = new Offering($result);
                }
            } else {
                // No results found
                // We need an empty array for the Iterator model
                $results = [];
            }

            // Set the results
            $this->offerings = new Iterator($results);
        }

        // Return results
        return $this->offerings;
    }

    /**
     * Check a user's authorization
     *
     * @param      string $action Action to check
     * @return     boolean True if authorized, false if not
     */
    public function access($action = 'view', $item = 'course')
    {
        return $this->permissions->access($action, $item);
    }

    /**
     * Retrieve a specific manager record by user ID
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
            }

            if (!$this->manager) {
                $this->manager = \Components\Courses\Models\Manager::getInstance($user_id, $this->get('id'), 0, 0);
            }
        }

        return $this->manager;
    }

    /**
     * Get a list of managers for a course
     *   If a manager has multiple entries, it will set
     *   the entry int he array with the record that has
     *   the highest permission levels
     *
     * @param      array   $filters Filters to build query from
     * @param      boolean $clear   Force a new dataset?
     * @return     mixed
     */
    public function managers($filters = [], $clear = false)
    {
        if (!isset($filters['course_id'])) {
            $filters['course_id'] = (int) $this->get('id');
        }
        if (!isset($filters['offering_id'])) {
            $filters['offering_id'] = 0;
        }
        if (!isset($filters['section_id'])) {
            $filters['section_id'] = 0;
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
     * Get a list of students for a course
     *
     * @param      array   $filters Filters to build query from
     * @param      boolean $clear   Force a new dataset?
     * @return     mixed
     */
    public function students($filters = [], $clear = false)
    {
        if (!isset($filters['course_id'])) {
            $filters['course_id'] = (int) $this->get('id');
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
                    $results[$key] = new Student($result, $this->get('id'));
                }
            }

            $this->students = $results;
        }

        return $this->students;
    }

    /**
     * Get a list of instructors for a course
     *
     * @param      array   $filters Filters to build query from
     * @param      boolean $clear   Force a new dataset?
     * @return     mixed
     */
    public function instructors($filters = [], $clear = true)
    {
        $filters['role'] = 'instructor';
        return $this->managers($filters, $clear);
    }

    /**
     * Add one or more user IDs or usernames to the managers list
     *
     * @return    void
     */
    public function add($data = null, $role_id = 0)
    {
        $user_ids = $this->userIds($data);

        $tbl = new Tables\Member($this->db);

        $filters = [
            'course_id' => (int) $this->get('id'),
        ];

        foreach ($user_ids as $user_id) {
            $filters['user_id'] = $user_id;

            if (($data = $tbl->find($filters))) {
                $this->managers[$user_id] = new Manager(array_shift($data), $this->get('id'));

                if (count($data) > 0) {
                    foreach ($data as $key => $result) {
                        $tbl->delete($result->id);
                        //$data[$key] = new Manager($result, $this->get('id'));
                        //$data[$key]->delete();
                    }
                }
            }

            if (!isset($this->managers[$user_id])) {
                $this->managers[$user_id] = new Manager($user_id, $this->get('id'));
            }
            $this->managers[$user_id]->set('user_id', $user_id);
            $this->managers[$user_id]->set('course_id', $this->get('id'));
            $this->managers[$user_id]->set('role_id', $role_id);
            $this->managers[$user_id]->set('section_id', 0);
            $this->managers[$user_id]->set('student', 0);
            $this->managers[$user_id]->set('offering_id', 0);
            $this->managers[$user_id]->store();
        }
    }

    /**
     * Remove one or more user IDs or usernames to the managers list
     *
     * @param     array $data List of IDs or usernames
     * @return    void
     */
    public function remove($data = null)
    {
        $user_ids = $this->userIds($data);

        if (count($user_ids) > 0) {
            $this->managers();

            foreach ($user_ids as $user_id) {
                if (isset($this->managers[$user_id])) {
                    $this->managers[$user_id]->delete();
                    unset($this->managers[$user_id]);
                }
            }
        }
    }

    /**
     * Return a list of user IDs for a list of usernames
     *
     * @param     array $users List of user IDs or usernames
     * @return    array|false List of user IDs
     */
    private function userIds($users)
    {
        if (empty($this->db)) {
            return false;
        }

        $usernames = [];
        $userids = [];

        if (!is_array($users)) {
            $users = [$users];
        }

        foreach ($users as $u) {
            if (is_numeric($u)) {
                $userids[] = $u;
            } else {
                $usernames[] = $this->db->quote($u);
            }
        }

        if (empty($usernames)) {
            return $userids;
        }

        $this->db->setQuery("SELECT id FROM `#__users` WHERE username IN (" . implode(",", $usernames) . ");");

        if (!($result = $this->db->loadColumn())) {
            $result = [];
        }

        return array_merge($result, $userids);
    }

    /**
     * Run just validation check to ensure no errors occur
     *
     *
     * @return boolean
     */
    public function check()
    {
        if (!$this->table->check()) {
            $this->setError($this->table->getError());
            return false;
        }
        return true;
    }

    /**
     * Short title for 'update'
     * Long title (if any) ...
     *
     * @return boolean Return title (if any) ...
     */
    public function store($check = true)
    {
        if (empty($this->db)) {
            return false;
        }

        $isNew = ($this->get('id') ? false : true);

        $affected = 0;

        if ($check) {
            if (!$this->check()) {
                return false;
            }
        }

        $this->importPlugin('content')->trigger('onContentBeforeSave', [
            'com_courses.course.description',
            &$this,
            $this->exists(),
        ]);

        if (!$this->table->store()) {
            $this->setError($this->table->getError());
            return false;
        }

        $affected = $this->db->getAffectedRows();

        // After SQL is done and has no errors, fire off onCourseUserEnrolledEvents
        // for every user added to this course
        $this
            ->importPlugin('courses')
            ->trigger('onCourseSave', [$this]);

        if ($affected > 0) {
            $this
                ->importPlugin('user')
                ->trigger('onAfterStoreCourse', [$this]);
        }

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
        $this
            ->importPlugin('courses')
            ->trigger('onCourseDelete', [$this]);

        return parent::delete();
    }

    /**
     * Check if the current user is enrolled
     *
     * @return     object
     */
    public function page($url = null)
    {
        if (
            !isset($this->page)
            || ($url !== null && (string) $this->page->get('url') != $url)
        ) {
            $this->page = null;

            if (isset($this->pages) && is_array($this->pages) && isset($this->pages[$url])) {
                $this->page = $this->pages[$url];
            }

            if (!$this->page) {
                $this->page = new Page(0);
            }
        }

        return $this->page;
    }

    /**
     * Get a list of pages for a course
     *
     * @param      array $filters Filters to apply
     * @return     array|string|null
     */
    public function pages($filters = [])
    {
        if (!isset($filters['course_id'])) {
            $filters['course_id'] = (int) $this->get('id');
        }
        if (!isset($filters['offering_id'])) {
            $filters['offering_id'] = 0;
        }
        if (!isset($filters['sort'])) {
            $filters['sort'] = 'ordering';
        }

        if (isset($filters['count']) && $filters['count']) {
            $tbl = new Tables\Page($this->db);

            return $tbl->count($filters);
        }

        if (!isset($this->pages) || !is_array($this->pages)) {
            $tbl = new Tables\Page($this->db);

            $results = [];

            if (($data = $tbl->find($filters))) {
                foreach ($data as $key => $result) {
                    $results[$result->url] = new Page($result);
                }
            }

            $this->pages = $results;
        }

        return $this->pages;
    }

    /**
     * Check if the current user is enrolled
     *
     * @return     array|string
     */
    public function tags($as = 'cloud', $admin = 0)
    {
        if (!$this->exists()) {
            switch (strtolower($as)) {
                case 'array':
                    return [];

                case 'string':
                case 'cloud':
                case 'html':
                default:
                    return '';
            }
        }

        $cloud = new Tags($this->get('id'));

        return $cloud->render($as, ['admin' => $admin]);
    }

    /**
     * Tag the entry
     *
     * @return  boolean
     */
    public function tag($tags = null, $user_id = 0, $admin = 0)
    {
        $cloud = new Tags($this->get('id'));

        return $cloud->setTags($tags, $user_id, $admin);
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
        if (!isset($this->base)) {
            $this->base  = 'index.php?option=com_courses&gid=' . $this->get('alias');
        }

        // If it doesn't exist or isn't published
        switch (strtolower($type)) {
            case 'edit':
                $link = $this->base . '&task=edit';
                break;

            case 'delete':
                $link = $this->base . '&task=delete';
                break;

            case 'permalink':
            default:
                $link = $this->base;
                break;
        }

        return $link;
    }

    /**
     * Get a course logo
     *
     * @param   string $rtrn Data type to return
     * @return  mixed
     */
    public function logo($rtrn = '')
    {
        $rtrn = strtolower(trim($rtrn));

        // Return just the file name
        if ($rtrn == 'file') {
            return $this->get('logo');
        }

        $path = '';
        $size = [
            'width'  => 0,
            'height' => 0,
        ];

        if ($file = $this->get('logo')) {
            $uploadPath = '/' . trim($this->config('uploadpath', '/site/courses'), '/');
            $path = $uploadPath . '/' . $this->get('id') . '/' . $file;
            if (file_exists(PATH_APP . $path)) {
                list($width, $height) = getimagesize(PATH_APP . $path);
                $size['width']  = $width;
                $size['height'] = $height;
                //$path = \Request::base(true) . substr(PATH_APP, strlen(PATH_ROOT)) . $path;

                if ($rtrn == 'url') {
                    return $this->link() . '&active=logo';
                }
            } else {
                return null;
            }
        }

        // Return just the upload path?
        if ($rtrn == 'size') {
            return $size;
        }

        return $path;
    }

    /**
     * Get the content of the entry
     *
     * @param      string  $as      Format to return state in [text, number]
     * @param      integer $shorten Number of characters to shorten text to
     * @return     string
     */
    public function description($as = 'parsed', $shorten = 0)
    {
        $as = strtolower($as);
        $options = [];

        switch ($as) {
            case 'parsed':
                $content = $this->get('description.parsed', null);
                if ($content === null) {
                    $config = [
                        'option'   => 'com_courses',
                        'scope'    => '',
                        'pagename' => $this->get('alias'),
                        'pageid'   => 0, //$this->get('id'),
                        'filepath' => DS . ltrim($this->config()->get('uploadpath', '/site/courses'), DS),
                        'domain'   => $this->get('alias'),
                    ];

                    $content = (string) $this->get('description', '');
                    $this->importPlugin('content')->trigger('onContentPrepare', [
                        'com_courses.course.description',
                        &$this,
                        &$config,
                    ]);

                    $this->set('description.parsed', (string) $this->get('description', ''));
                    $this->set('description', $content);

                    return $this->description($as, $shorten);
                }
                $options['html'] = true;
                break;

            case 'clean':
                $content = strip_tags($this->description('parsed'));
                break;

            case 'raw':
            default:
                $content = $this->get('description');
                $content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content == null ? '' : $content);
                //$content = html_entity_decode($content);
                //$content = str_replace("\xC2\xA0", ' ', $content);
                $content = str_replace(['&lt;', '&gt;', '&amp;'], ['<', '>', '&'], $content);
                break;
        }

        if ($shorten) {
            $content = \Hubzero\Utility\Str::truncate($content, $shorten, $options);
        }
        return $content;
    }

    /**
     * Copy an entry and associated data
     *
     * @param   boolean  $deep  Copy associated data?
     * @param   string   $title
     * @param   string   $alias
     * @return  boolean  True on success, false on error
     */
    public function copy($deep = true, $title = '', $alias = '')
    {
        // Get some old info we may need
        //  - Course ID
        $c_id = $this->get('id');

        // Reset the ID. This will force store() to create a new record.
        $this->set('id', 0);
        // We want to distinguish this course from the one we copied from
        $title = $title ? $title : $this->get('title') . ' (copy)';
        $alias = $alias ? $alias : $this->get('alias') . '_copy';

        $this->set('title', $title);
        $this->set('alias', $alias);

        if (!$this->store()) {
            return false;
        }

        if ($deep) {
            // Copy pages
            foreach ($this->pages(['course_id' => $c_id, 'active' => [0, 1]]) as $page) {
                if (!$page->copy($this->get('course_id'))) {
                    $this->setError($page->getError());
                }
            }

            // Copy units
            foreach ($this->offerings(['course_id' => $c_id], true) as $offering) {
                if (!$offering->copy($this->get('id'))) {
                    $this->setError($offering->getError());
                }
            }

            // Copy managers
            foreach ($this->managers(['course_id' => $c_id], true) as $manager) {
                $manager->set('id', 0);
                $manager->set('course_id', $this->get('id'));
                if (!$manager->store()) {
                    $this->setError($manager->getError());
                }
            }

            // Copy logo
            if ($file = $this->get('logo')) {
                $src  = '/' . trim($this->config('uploadpath', '/site/courses'), '/') . '/' . $c_id . '/' . $file;
                if (file_exists(PATH_APP . $src)) {
                    $dest = '/' . trim($this->config('uploadpath', '/site/courses'), '/') . '/' . $this->get('id');

                    if (!is_dir(PATH_APP . $dest)) {
                        if (!Filesystem::makeDirectory(PATH_APP . $dest)) {
                            $this->setError(Lang::txt('UNABLE_TO_CREATE_UPLOAD_PATH'));
                        }
                    }

                    $dest .= '/' . $file;

                    if (!Filesystem::copy(PATH_APP . $src, PATH_APP . $dest)) {
                        $this->setError(Lang::txt('Failed to copy course logo.'));
                    }
                }
            }

            // Copy tags
            $tagger = new Tags($c_id);
            $this->tag($tagger->render('string', ['admin' => 1]), User::get('id'), 1);
        }

        return true;
    }

    /**
     * Get a course logo
     *
     * @return     object
     */
    public function certificate()
    {
        if (!$this->certificate) {
            $this->certificate = Certificate::getInstance(0, $this->get('id'));
        }

        return $this->certificate;
    }
}
