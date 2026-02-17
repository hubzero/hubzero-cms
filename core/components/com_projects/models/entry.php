<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models;

use Hubzero\Base\Model;
use Components\Projects\Tables;
use Date;
use Lang;

/**
 * Model class for a todo entry
 */
class Entry extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_tbl_name = '\\Components\\Projects\\Tables\\Todo';

    /**
     * Model context
     *
     * @var string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_context = 'com_projects.todo.details';

    /**
     * Comment
     *
     * @var object
     */
    private $comment = null;

    /**
     * \Hubzero\Base\ItemList
     *
     * @var object
     */
    private $comments = null;

    /**
     * Comment count
     *
     * @var integer
     */
    private $commentsCount = null;

    /**
     * User
     *
     * @var object
     */
    private $creator = null;

    /**
     * Hubzero\User\User
     *
     * @var object
     */
    private $owner = null;

    /**
     * Hubzero\User\User
     *
     * @var object
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
        $this->_db = \App::get('db');

        $this->_tbl = new Tables\Todo($this->_db);

        if ($oid) {
            if (is_numeric($oid)) {
                $this->_tbl->load($oid);
            } elseif (is_object($oid) || is_array($oid)) {
                $this->bind($oid);
            }
        }
    }

    /**
     * Returns a reference to a todo entry model
     *
     * @param   mixed   $oid   ID (int) or alias (string)
     * @return  object  Entry
     */
    public static function &getInstance($oid = null)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = array();
        }

        if (is_object($oid)) {
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
     * @param   string  $get
     * @return  object  Models\Project
     */
    public function project($get = null)
    {
        if (empty($this->_project)) {
            $this->_project = new \Components\Projects\Models\Project($this->get('projectid'));
            $this->_project->_params = new \Hubzero\Config\Registry($this->_project->params);
        }

        return $get ? $this->_project->get($get) : $this->_project;
    }

    /**
     * Return a formatted created timestamp
     *
     * @param   string  $as  What data to return
     * @return  string
     */
    public function created($as = '')
    {
        return $this->_date('created', $as);
    }

    /**
     * Return a formatted modified timestamp
     *
     * @param   string  $as  What data to return
     * @return  string
     */
    public function due($as = '')
    {
        return $this->_date('duedate', $as);
    }

    /**
     * Return a formatted modified timestamp
     *
     * @param   string  $as  What data to return
     * @return  string
     */
    public function closed($as = '')
    {
        return $this->_date('closed', $as);
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
            && $this->get('duedate') != $this->_db->getNullDate()
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
     * @return  string
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _date($key, $as = '')
    {
        if ($this->get($key) == $this->_db->getNullDate()) {
            return null;
        }
        switch (strtolower($as)) {
            case 'date':
                return Date::of($this->get($key))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
            break;

            case 'time':
                return Date::of($this->get($key))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
            break;

            default:
                return $this->get($key);
            break;
        }
    }

    /**
     * Get the creator of this entry
     *
     * Accepts an optional property name. If provided
     * it will return that property value. Otherwise,
     * it returns the entire user object
     *
     * @param   string  $property
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
                return $this->creator->pcture();
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
     * @param   string  $property  What data to return
     * @param   mixed   $default   Default value
     * @return  mixed
     */
    public function owner($property = null, $default = null)
    {
        if (!($this->owner instanceof \Hubzero\User\User)) {
            $this->owner = \User::getInstance($this->get('assigned_to'));
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
     * @param   string  $property  What data to return
     * @param   mixed   $default   Default value
     * @return  mixed
     */
    public function closer($property = null, $default = null)
    {
        if (!($this->closer instanceof \Hubzero\User\User)) {
            $this->closer = \User::getInstance($this->get('closed_by'));
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
     * @param   array    $filters  Filters to apply to data fetch
     * @param   boolean  $clear    Clear cached data?
     * @return  mixed
     */
    public function comments($rtrn = 'list', $filters = array(), $clear = false)
    {
        $tbl = new \Components\Projects\Tables\Comment($this->_db);

        switch (strtolower($rtrn)) {
            case 'count':
                if (!isset($this->commentsCount) || !is_numeric($this->commentsCount) || $clear) {
                    $this->commentsCount = 0;

                    if (!$this->comments) {
                        $c = $this->comments('list', $filters);
                    }
                    foreach ($this->comments as $com) {
                        $this->commentsCount++;
                    }
                }
                return $this->commentsCount;
            break;

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
                        $results = array();
                    }
                    $this->comments = new \Hubzero\Base\ItemList($results);
                }
                return $this->comments;
            break;
        }
    }
}
