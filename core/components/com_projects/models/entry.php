<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models;

use Hubzero\Base\Model;
use Components\Projects\Tables;
use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;
use Hubzero\Facades\User;

/**
 * Model class for a todo entry
 */
class Entry extends Model
{
    /**
     * @var  \Components\Projects\Models\Project|null
     */
    protected $project = null;

    /**
     * Table name
     *
     * @var class-string<\Components\Projects\Tables\Todo>
     */
    protected $tableClass = '\\Components\\Projects\\Tables\\Todo';

    /**
     * Table, an instance of $tableClass
     *
     * @var \Components\Projects\Tables\Todo
     */
    protected $table;

    /**
     * Model context
     *
     * @var string
     */
    protected $context = 'com_projects.todo.details';

    /**
     * \Hubzero\Base\ItemList
     *
     * @var \Hubzero\Base\ItemList|null
     */
    private $comments = null;

    /**
     * Comment count
     *
     * @var integer|null
     */
    private $commentsCount = null;

    /**
     * User
     *
     * @var \Hubzero\User\User|null
     */
    private $creator = null;

    /**
     * Hubzero\User\User
     *
     * @var \Hubzero\User\User|null
     */
    private $owner = null;

    /**
     * Hubzero\User\User
     *
     * @var \Hubzero\User\User|null
     */
    private $closer = null;

    /**
     * Constructor
     *
     * @param   mixed  $oid  ID (int) or alias (string)
     * @return  void
     */
    public function __construct($oid)
    {
        $this->db = App::get('db');

        $this->table = new Tables\Todo($this->db);

        if ($oid) {
            if (is_numeric($oid)) {
                $this->table->load($oid);
            } elseif (is_object($oid) || is_array($oid)) {
                $this->bind($oid);
            }
        }
    }

    /**
     * Returns a reference to a todo entry model
     *
     * @param   int|string|\stdClass|array<string,mixed>|null   $oid   ID (int) or alias (string)
     * @return  self  Entry
     */
    public static function &getInstance($oid = null)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = [];
        }

        if ($oid instanceof \stdClass) {
            $key = $oid->id;
        } elseif (is_array($oid)) {
            $key = $oid['id'];
        } else {
            $key = $oid;
        }

        if (!isset($instances[$key])) {
            $instances[$key] = new self($oid);
        }

        return $instances[$key];
    }

    /**
     * Get the home project of this entry
     *
     * @param   string|null  $get
     * @return  object  Models\Project
     */
    public function project($get = null)
    {
        if (empty($this->project)) {
            $this->project = new \Components\Projects\Models\Project($this->get('projectid'));
        }

        return $get ? $this->project->get($get) : $this->project;
    }

    /**
     * Return a formatted created timestamp
     *
     * @param   string  $as  What data to return
     * @return  string|null
     */
    public function created($as = '')
    {
        return $this->date('created', $as);
    }

    /**
     * Return a formatted modified timestamp
     *
     * @param   string  $as  What data to return
     * @return  string|null
     */
    public function due($as = '')
    {
        return $this->date('duedate', $as);
    }

    /**
     * Return a formatted modified timestamp
     *
     * @param   string  $as  What data to return
     * @return  string|null
     */
    public function closed($as = '')
    {
        return $this->date('closed', $as);
    }

    /**
     * Is item overdue?
     *
     * @return  boolean
     */
    public function isOverdue()
    {
        if (
            $this->get('duedate')
            && $this->get('duedate') != $this->db->getNullDate()
            && $this->get('duedate') < Date::toSql()
        ) {
            return true;
        }
        return false;
    }

    /**
     * Is item complete?
     *
     * @return  boolean
     */
    public function isComplete()
    {
        if ($this->get('state') == 1) {
            return true;
        }
        return false;
    }

    /**
     * Return a formatted timestamp
     *
     * @param   string  $key  Field to return
     * @param   string  $as   What data to return
     * @return  string|null
     */
    protected function date($key, $as = '')
    {
        if ($this->get($key) == $this->db->getNullDate()) {
            return null;
        }
        switch (strtolower($as)) {
            case 'date':
                return Date::of($this->get($key))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));

            case 'time':
                return Date::of($this->get($key))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));

            default:
                return $this->get($key);
        }
    }

    /**
     * Get the creator of this entry
     *
     * Accepts an optional property name. If provided
     * it will return that property value. Otherwise,
     * it returns the entire user object
     *
     * @param   string|null  $property
     * @param   mixed   $default
     * @return  mixed
     */
    public function creator($property = null, $default = null)
    {
        if (!($this->creator instanceof \Hubzero\User\User)) {
            $this->creator = \Hubzero\User\User::oneOrNew($this->get('created_by'));
        }
        if ($property) {
            $property = ($property == 'uidNumber') ? 'id' : $property;
            if ($property == 'picture') {
                return $this->creator->picture();
            }
            return $this->creator->get($property, $default);
        }
        return $this->creator;
    }

    /**
     * Get the owner of this entry
     *
     * Accepts an optional property name. If provided
     * it will return that property value. Otherwise,
     * it returns the entire object
     *
     * @param   string|null  $property  What data to return
     * @param   mixed   $default   Default value
     * @return  mixed
     */
    public function owner($property = null, $default = null)
    {
        if (!($this->owner instanceof \Hubzero\User\User)) {
            $this->owner = User::getInstance($this->get('assigned_to'));
        }
        if ($property) {
            if ($property == 'picture') {
                return $this->owner->picture();
            }
            return $this->owner->get($property, $default);
        }
        return $this->owner;
    }

    /**
     * Get the owner of this entry
     *
     * Accepts an optional property name. If provided
     * it will return that property value. Otherwise,
     * it returns the entire object
     *
     * @param   string|null  $property  What data to return
     * @param   mixed   $default   Default value
     * @return  mixed
     */
    public function closer($property = null, $default = null)
    {
        if (!($this->closer instanceof \Hubzero\User\User)) {
            $this->closer = User::getInstance($this->get('closed_by'));
        }
        if ($property) {
            if ($property == 'picture') {
                return $this->closer->picture();
            }
            return $this->closer->get($property, $default);
        }
        return $this->closer;
    }

    /**
     * Get a list or count of comments
     *
     * @param   string   $rtrn     Data format to return
     * @param   array<mixed>    $filters  Filters to apply to data fetch
     * @param   boolean|mixed  $clear    Clear cached data?
     * @return  mixed
     */
    public function comments($rtrn = 'list', $filters = [], $clear = false)
    {
        $tbl = new \Components\Projects\Tables\Comment($this->db);

        switch (strtolower($rtrn)) {
            case 'count':
                if (!isset($this->commentsCount) || $clear) {
                    $this->commentsCount = 0;

                    if (!$this->comments) {
                        $c = $this->comments('list', $filters);
                    }
                    foreach ($this->comments ?? [] as $com) {
                        $this->commentsCount++;
                    }
                }
                return $this->commentsCount;

            case 'list':
            case 'results':
            default:
                if (!($this->comments instanceof \Hubzero\Base\ItemList) || $clear) {
                    if ($results = $tbl->getComments($this->get('id'), 'todo')) {
                        foreach ($results as $key => $result) {
                            $results[$key] = new Comment($result);
                            $results[$key]->set('option', 'com_projects');
                            $results[$key]->set('scope', '');
                            $results[$key]->set('alias', '');
                            $results[$key]->set('path', '');
                        }
                    } else {
                        $results = [];
                    }
                    $this->comments = new \Hubzero\Base\ItemList($results);
                }
                return $this->comments;
        }
    }
}
