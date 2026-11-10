<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models;

use Hubzero\Base\Model;
use Components\Projects\Tables;
use Hubzero\Base\ItemList;
use Hubzero\Facades\Component;
use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;
use Hubzero\Facades\User;

/**
 * Project Tool model
 */

/** @phpstan-consistent-constructor */
class Tool extends Model
{
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $actor;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $componentConfig;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $creator;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $lastUpdate;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $lastView;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $log;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $status;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $statusChanger;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $version;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $viewLog;

    /**
     * Table class name
     *
     * @var string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $tableClass = '\\Components\\Projects\\Tables\\Tool';

    /**
     * Entry
     *
     * @var object
     */
    private $entry = null;

    /**
     * \Hubzero\Base\ItemList
     *
     * @var object
     */
    private $entries = null;

    /**
     * Registry
     *
     * @var object
     */
    public $config = null;

    /**
     * Constructor
     *
     * @param   mixed    $oid
     * @param   integer  $projectid
     * @param   object   $instance
     * @return  void
     */
    public function __construct($oid = null, $projectid = null, $instance = null)
    {
        $this->db = App::get('db');

        $this->table = new Tables\Tool($this->db);

        if ($oid) {
            if (is_numeric($oid) || is_string($oid)) {
                $this->table->loadTool($oid, $projectid);
            }

            // Load instance
            if ($this->exists()) {
                $this->version($instance, $oid);
            }
        }
    }

    /**
     * Returns a reference to a tool model
     *
     * @param   mixed    $oid
     * @param   integer  $projectid
     * @param   object   $instance
     * @return  object
     */
    public static function &getInstance($oid = null, $projectid = null, $instance = null)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = [];
        }

        if (!isset($instances[$oid])) {
            $instances[$oid] = new static($oid, $projectid, $instance);
        }

        return $instances[$oid];
    }

    /**
     * Returns a reference to a tool model
     *
     * @param   object  $result
     * @return  object
     */
    public static function mapInstance($result)
    {
        if (!is_object($result)) {
            return false;
        }

        $oid = $result->id;
        static $instances;

        if (!isset($instances)) {
            $instances = [];
        }

        if (!isset($instances[$oid])) {
            $instances[$oid] = new static();
            foreach ($result as $key => $value) {
                $instances[$oid]->set($key, $value);
            }
        }

        return $instances[$oid];
    }

    /**
     * Load a specific instance
     *
     * @param   integer  $id
     * @param   integer  $parent
     * @return  object
     */
    public function version($id = null, $parent = null)
    {
        if (
            !isset($this->version)
            || ($id !== null && (int) $this->version->get('id') != $id)
        ) {
            $this->version = Tool\Instance::getInstance($id, $parent);
        }

        return $this->version;
    }

    /**
     * Get specific instance property
     *
     * @param   string  $property
     * @return  mixed
     */
    public function instance($property)
    {
        if ($property) {
            return $this->version->get($property);
        }
        return $this->version;
    }

    /**
     * Get a log model
     *
     * @param   integer  $parent_id
     * @param   string   $parent_name
     * @return  object
     */
    public function log($parent_id = null, $parent_name = null)
    {
        if (!isset($this->log)) {
            $this->log = new Tool\Log();
            if ($parent_id) {
                $this->log->set('parent_id', $parent_id);
            }
            if ($parent_name) {
                $this->log->set('parent_name', $parent_name);
            }
        }
        return $this->log;
    }

    /**
     * Get last status change
     *
     * @param   string   $property
     * @param   string   $as
     * @return  boolean
     */
    public function lastStatusChange($property = null, $as = null)
    {
        return $this->lastUpdate($property, $as, true);
    }

    /**
     * Get history
     *
     * @param   array  $filters
     * @return  array
     */
    public function history($filters = [])
    {
        // Get log model
        $log = $this->log($this->get('id'), $this->get('name'));

        return $log->getHistory($filters);
    }

    /**
     * Get last update
     *
     * @param   string   $property
     * @param   string   $as
     * @param   boolean  $statusChange
     * @return  boolean
     */
    public function lastUpdate($property = null, $as = null, $statusChange = false)
    {
        // Get log model
        $log = $this->log($this->get('id'), $this->get('name'));

        if (!isset($this->lastUpdate)) {
            $this->lastUpdate = $log->getLastUpdate($statusChange);
        }
        if ($property) {
            if ($property == 'recorded') {
                $this->set('recorded', $this->lastUpdate->recorded);
                return $this->date('recorded', $as);
            }
            if ($property == 'actor') {
                if (!isset($this->actor) || !($this->actor instanceof \Hubzero\User\User)) {
                    $this->actor = User::getInstance($this->lastUpdate->actor);
                }
                if ($as) {
                    return $this->actor->get($as);
                }
            }
            return isset($this->lastUpdate->$property) ? $this->lastUpdate->$property : null;
        }
        return $this->lastUpdate;
    }

    /**
     * Get a status model
     *
     * @param   string   $property
     * @param   string   $statusModel
     * @return  object
     */
    public function status($property = null, $statusModel = null)
    {
        if (!empty($statusModel) && ($statusModel instanceof Tool\Status)) {
            $statusModel->getStatus($this->get('status'));
            $this->status = $statusModel;
        }
        if (!isset($this->status)) {
            $this->status = new Tool\Status($this->get('status'));
            $options = new \Hubzero\Config\Registry($this->status->get('options'));
            $this->status->set('options', $options);
        }
        if ($property) {
            return $this->status->get($property);
        }
        return $this->status;
    }

    /**
     * Get status css
     *
     * @param   boolean  $admin
     * @return  string
     */
    public function getStatusCss($admin = false)
    {
        $next_actor = $this->status('next_actor');
        if (!$admin) {
            $class = $next_actor == 1 ? 'wait' : 'donext';
            $class = $next_actor == 2 ? 'followup' : $class;
        } else {
            $class = $next_actor == 1 ? 'donext' : 'wait';
            $class = $next_actor == 2 ? 'followup' : $class;
        }
        return $class;
    }

    /**
     * Return a formatted created timestamp
     *
     * @param   string  $as  What data to return
     * @return  string
     */
    public function created($as = '')
    {
        return $this->date('created', $as);
    }

    /**
     * Return a formatted created timestamp
     *
     * @param   string  $as  What data to return
     * @return  string
     */
    public function statusChanged($as = '')
    {
        return $this->date('status_changed', $as);
    }

    /**
     * Return a formatted created timestamp
     *
     * @param   string  $as  What data to return
     * @return  string
     */
    public function lastView($as = '')
    {
        if (!isset($this->lastView)) {
            if (!isset($this->viewLog)) {
                $this->viewLog = new Tool\View();
            }
            $this->lastView = $this->viewLog->lastView($this->get('id'), User::get('id'));
        }
        return $this->lastView;
    }

    /**
     * Return a formatted timestamp
     *
     * @param   string  $key  Field to return
     * @param   string  $as   What data to return
     * @return  string
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function date($key, $as = '')
    {
        if ($this->get($key) == '0000-00-00 00:00:00') {
            return null;
        }
        switch (strtolower($as)) {
            case 'date':
                return Date::of($this->get($key))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                break;

            case 'time':
                return Date::of($this->get($key))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
                break;

            case 'datetime':
                return $this->date($key, 'date') . ' &#64; ' . $this->date($key, 'time');
                break;

            case 'timeago':
                return \Components\Projects\Helpers\Html::timeAgo($this->get($key));
                break;

            default:
                return $this->get($key);
                break;
        }
    }

    /**
     * Get a list of tools
     *   Accepts either a numeric array index or a string [id, name]
     *   If index, it'll return the entry matching that index in the list
     *   If string, it'll return either a list of IDs or names
     *
     * @param   string   $rtrn
     * @param   array    $filters
     * @param   boolean  $admin
     * @return  array
     */
    public function entries($rtrn = 'list', $filters = [], $admin = false)
    {
        $results = [];

        switch (strtolower($rtrn)) {
            case 'count':
                $filters['count'] = 1;
                return (int) $this->table->getRecordCount($filters, $admin);
                break;

            case 'results':
                return $this->table->getRecords($filters, $admin);
                break;

            case 'list':
            default:
                if ($results = $this->table->getRecords($filters, $admin)) {
                    foreach ($results as $key => $result) {
                        $results[$key] = self::mapInstance($result);
                    }
                } else {
                    $results = [];
                }

                return new ItemList($results);
                break;
        }
        return null;
    }

    /**
     * Get a configuration value
     * If no key is passed, it returns the configuration object
     *
     * @param   string  $key  Config property to retrieve
     * @return  mixed
     */
    public function config($key = null)
    {
        if (!isset($this->componentConfig)) {
            $this->componentConfig = Component::params('com_tools');
        }
        if ($key) {
            return $this->componentConfig->get($key);
        }
        return $this->componentConfig;
    }

    /**
     * Verify data before saving
     *
     * @return  boolean  False if error, True on success
     */
    public function verify()
    {
        if (!$this->check()) {
            // Name check
            return false;
        }
        if (trim($this->get('title')) == '') {
            // Title check
            $this->setError(Lang::txt('PLG_PROJECTS_TOOLS_ERROR_MISSING_TITLE'));
            return false;
        }

        // Clean title
        return false;
    }

    /**
     * Check tool name
     *
     * @param   string   $name  Alias name
     * @param   integer  $ajax
     * @return  boolean  False if error, True on success
     */
    public function check($name = '', $ajax = 0)
    {
        $name = $name ? $name : $this->get('name');

        // Load config
        $this->config();

        // Set name length
        $minLength = $this->componentConfig->get('min_name_length', 3);
        $maxLength = $this->componentConfig->get('max_name_length', 30);

        // Array of reserved names (task names and default dirs)
        $reserved = explode(',', $this->componentConfig->get('reserved_names'));
        $tasks    = ['temp', 'toolname', 'register'];

        if ($name) {
            $name = preg_replace('/ /', '', $name);
            $name = strtolower($name);
            $this->set('name', $name);
        }

        // Perform checks
        if (!$name) {
            // Cannot be empty
            $this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_EMPTY'));
        } elseif (strlen($name) < intval($minLength)) {
            // Check for length
            $this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_TOO_SHORT'));
        } elseif (strlen($name) > intval($maxLength)) {
            $this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_TOO_LONG'));
        } elseif (preg_match('/[^a-z0-9]/', $name)) {
            // Check for illegal characters
            $this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_INVALID'));
        } elseif (is_numeric($name)) {
            // Check for all numeric (not allowed)
            $this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_INVALID_NUMERIC'));
        } else {
            // Verify name uniqueness
            if (
                $this->table->checkUniqueName($name, $this->get('id'))
                || ($reserved
                && in_array($name, $reserved))
                || in_array($name, $tasks)
            ) {
                $this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_NOT_UNIQUE'));
            }
        }
        if ($this->getError()) {
            return false;
        }

        return true;
    }

    /**
     * Get the creator of this entry
     *
     * Accepts an optional property name. If provided
     * it will return that property value. Otherwise,
     * it returns the entire User object
     *
     * @param   string  $property
     * @return  mixed
     */
    public function creator($property = null)
    {
        if (!isset($this->creator) || !($this->creator instanceof \Hubzero\User\User)) {
            $this->creator = User::getInstance($this->get('created_by'));
        }
        if ($property) {
            return $this->creator->get($property);
        }
        return $this->creator;
    }

    /**
     * Get the use who changed status of this entry
     *
     * Accepts an optional property name. If provided
     * it will return that property value. Otherwise,
     * it returns the entire User object
     *
     * @param   string  $property
     * @return  mixed
     */
    public function statusChanger($property = null)
    {
        if (!isset($this->statusChanger) || !($this->statusChanger instanceof \Hubzero\User\User)) {
            $this->statusChanger = User::getInstance($this->get('status_changed_by'));
        }
        if ($property) {
            return is_object($this->statusChanger) ? $this->statusChanger->get($property) : null;
        }
        return $this->statusChanger;
    }

    /**
     * Is sandbox open?
     *
     * @return  bboolean
     */
    public function isOpenDev()
    {
        if ($this->get('opendev') == 1) {
            return true;
        }
        return false;
    }

    /**
     * Is source open?
     *
     * @return  boolean
     */
    public function isOpenSource()
    {
        if ($this->get('opensource') == 1) {
            return true;
        }
        return false;
    }

    /**
     * Is published?
     *
     * @return  boolean
     */
    public function isPublished()
    {
        if ($this->get('published') == 1) {
            return true;
        }
        return false;
    }

    /**
     * Tool repo exists
     *
     * @return  boolean
     */
    public function repoExists()
    {
        if ($this->get('status') > 1) {
            return true;
        }
        return false;
    }

    /**
     * Tool publication
     *
     * @return  boolean
     */
    public function publication()
    {
        return false;
    }

    /**
     * Is tool in particular status
     *
     * @param   string  $status
     * @return  boolean
     */
    public function inStatus($status = null)
    {
        switch ($status) {
            case 'created':
                return $this->status('status') == 'created' ? true : false;
                break;

            case 'uploaded':
                return $this->status('status') == 'uploaded' ? true : false;
                break;

            case 'installed':
                return $this->status('status') == 'installed' ? true : false;
                break;

            case 'draft':
                return $this->get('status') < 5 ? true : false;
                break;
        }
        return false;
    }

    /**
     * Is action required?
     *
     * @param   string  $type
     * @return  boolean
     */
    public function actionRequired($type = 'user')
    {
        $next_actor = $this->status('next_actor');
        if ($type == 'user') {
            $required = $next_actor == 1 ? false : true;
        } else {
            $required = $next_actor == 1 ? true : false;
        }

        return $required;
    }
}
