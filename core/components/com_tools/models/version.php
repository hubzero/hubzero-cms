<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Models;

use Hubzero\Facades\Log;
use Hubzero\Facades\App;

/**
 * Tool version instance
 *
 * Its private properties are read and written from outside through __get() and
 * __set(); these are the ones other code uses:
 *
 * @property  mixed  $toolid
 * @property  mixed  $vnc_command
 * @property  mixed  $vnc_timeout
 * @property-read   array         $hostreq
 * @property-write  array|string  $hostreq  A single value is kept as a list of one
 * @property  mixed  $mw
 * @property  mixed  $params
 * @property  integer|null  $hardTimeout
 * @property  string|null  $instance
 * @property  mixed  $toolname
 * @property  integer|null  $state
 * @property  mixed  $revision
 * @property  mixed  $codeaccess
 * @property  mixed  $license
 * @property  mixed  $fulltxt
 * @property  mixed  $description
 * @property  mixed  $title
 */
class Version
{
    /**
     * Description for 'id'
     *
     * @var mixed
     */
    private $id = null;              //                     #__tool_version

    /**
     * Description for 'toolname'
     *
     * @var mixed
     */
    private $toolname = null;          //                       #__tool_version

    /**
     * Description for 'instance'
     *
     * @var string|null
     */
    private $instance = null;          // tool                  #__tool_version

    /**
     * Description for 'title'
     *
     * @var mixed
     */
    private $title = null;            // cn                 #__tool_version

    /**
     * Description for 'description'
     *
     * @var mixed
     */
    private $description = null;        // description          #__tool_version

    /**
     * Description for 'fulltxt'
     *
     * @var mixed
     */
    private $fulltxt = null;           //                       #__tool_version

    /**
     * Description for 'version'
     *
     * @var mixed
     */
    private $version = null;            // version              #__tool_version

    /**
     * Description for 'revision'
     *
     * @var mixed
     */
    private $revision = null;          // revision              #__tool_version

    /**
     * Description for 'toolaccess'
     *
     * @var mixed
     */
    private $toolaccess = null;      // public              #__tool_version

    /**
     * Description for 'codeaccess'
     *
     * @var mixed
     */
    private $codeaccess = null;      // sourcePublic            #__tool_version

    /**
     * Description for 'wikiaccess'
     *
     * @var mixed
     */
    private $wikiaccess = null;      // projectPublic       #__tool_version

    /**
     * Description for 'state'
     *
     * @var integer
     */
    private $state = null;            // state              #__tool_version

    /**
     * Description for 'released_by'
     *
     * @var mixed
     */
    private $released_by = null;        //                      #__tool_version

    /**
     * Description for 'released'
     *
     * @var mixed
     */
    private $released = null;          // publishDate           #__tool_version

    /**
     * Description for 'unpublished'
     *
     * @var mixed
     */
    private $unpublished = null;        // unpublishDate        #__tool_version

    /**
     * Description for 'exportControl'
     *
     * @var mixed
     */
    private $exportControl = null;    // exportControl      #__tool_version

    /**
     * Description for 'license'
     *
     * @var mixed
     */
    private $license = null;            // usageAgreementText   #__tool_version

    /**
     * Description for 'vnc_geometry'
     *
     * @var mixed
     */
    private $vnc_geometry = null;      // vncGeometry           #__tool_version

    /**
     * Description for 'vnc_depth'
     *
     * @var mixed
     */
    private $vnc_depth = null;        // vncDepth               #__tool_version

    /**
     * Description for 'vnc_timeout'
     *
     * @var mixed
     */
    private $vnc_timeout = null;        // vncTimeout           #__tool_version

    /**
     * The longest a session of this version may run, in seconds; null for no limit
     *
     * @var integer
     */
    private $hardTimeout = null;        // hardTimeout          #__tool_version

    /**
     * Description for 'vnc_command'
     *
     * @var mixed
     */
    private $vnc_command = null;        // vncCommand           #__tool_version

    /**
     * Description for 'mw'
     *
     * @var mixed
     */
    private $mw = null;              // defaultMiddleware   #__tool_version

    /**
     * Description for 'priority'
     *
     * @var mixed
     */
    private $priority = null;          // priority              #__tool_version

    /**
     * Description for 'toolid'
     *
     * @var mixed
     */
    private $toolid = null;          //                     #__tool_version

    /**
     * Description for 'toolid'
     *
     * @var mixed
     */
    private $params = null;          //                     #__tool_version

    /**
     * Description for 'alias'
     *
     * @var array
     */
    private $alias = [];          // alias [array]     #__tool_aliases

    /**
     * Description for 'middleware'
     *
     * @var array
     */
    private $middleware = [];    // middleware [array] #__tool_middleware

    /**
     * Description for 'hostreq'
     *
     * @var array
     */
    private $hostreq = [];      // vncHostReq [array]  #__tool_hostreq

    /**
     * Description for 'author'
     *
     * @var array
     */
    private $author = [];        // author [array]     #__tool_authors

    /**
     * Description for 'member'
     *
     * @var array
     */
    private $member = [];        // member [array]     #__tool_groups

    /**
     * Description for 'owner'
     *
     * @var array
     */
    private $owner = [];          // owner [array]     #__tool_groups

    /**
     * Description for '_list_keys'
     *
     * @var array
     */
    private $listKeys = ['alias', 'middleware', 'hostreq', 'author', 'member', 'owner'];

    /**
     * Description for '_updateAll'
     *
     * @var boolean
     */
    private $updateAll = false;

    /**
     * Description for '_propertyattrmap'
     *
     * @var array
     */
    public static $propertyattrmap = [
        'title' => 'cn',
        'description' => 'description',
        'version' => 'version',
        'revision' => 'revision',
        'released' => 'publishDate',
        'unpublished' => 'unpublishDate',
        'exportControl' => 'exportControl',
        'vnc_geometry' => 'vncGeometry',
        'vnc_depth' => 'vncDepth',
        'vnc_timeout' => 'vncTimeout',
        'hardTimeout' => 'hardTimeout',
        'vnc_command' => 'vncCommand',
        'mw' => 'defaultMiddleware',
        'priority' => 'priority',
        'alias' => 'alias',
        'hostreq' => 'vncHostReq',
        'member' => 'member',
        'owner' => 'owner',
        'state' => 'state',
        'codeaccess' => 'sourcePublic',
        'toolaccess' => 'public',
        'wikiaccess' => 'projectPublic',
        'author' => 'author',
        'middleware' => 'middleware',
        'params' => 'params',
    ];

    /**
     * Description for '_updatedkeys'
     *
     * @var array
     */
    private $updatedkeys = [];

    /**
     * Constructor
     *
     * @return   void
     */
    private function __construct() {}

    /**
     * Short description for 'clear'
     *
     * Long description (if any) ...
     *
     * @return   void
     */
    public function clear()
    {
        $cvars = get_class_vars(__CLASS__);

        $this->updatedkeys = [];

        foreach ($cvars as $key => $value) {
            if ($key[0] != '_') {
                unset($this->$key);

                if (!in_array($key, $this->listKeys)) {
                    $this->$key = null;
                } else {
                    $this->$key = [];
                }
            }
        }

        $this->updateAll = false;
        $this->updatedkeys = [];
    }

    /**
     * Short description for 'logDebug'
     *
     * Long description (if any) ...
     *
     * @param     mixed $msg Parameter description (if any) ...
     * @return   void
     */
    private function logDebug($msg)
    {
        Log::debug($msg);
    }

    /**
     * Short description for 'getTool_VersionNames'
     *
     * Long description (if any) ...
     *
     * @param     mixed $tool Parameter description (if any) ...
     * @return   mixed Return description (if any) ...
     */
    public function getToolVersionNames($tool = null)
    {
        $db = App::get('db');

        if (is_numeric($tool)) {
            $where_clause = "toolid=" . $db->Quote($tool);
        } elseif (!empty($tool)) {
            $where_clause = "toolname=" . $db->Quote($tool);
        } elseif (empty($this->toolname)) {
            $where_clause = "toolid=" . $db->Quote($this->id);
        } elseif (empty($this->toolid)) {
            $where_clause = "toolname=" . $db->Quote($this->toolname);
        } else {
            $where_clause = "toolname=" . $db->Quote($this->toolname) . " AND toolid=" . $db->Quote($this->toolid);
        }

        $db->setQuery("SELECT instance FROM #__tool_version WHERE $where_clause;");
        $result = $db->loadColumn();

        return $result;
    }

    /**
     * Short description for 'toArray'
     *
     * Long description (if any) ...
     *
     * @return   mixed Return description (if any) ...
     */
    public function toArray()
    {
        $result = [];

        if (true) {
            foreach (self::$propertyattrmap as $key => $value) {
                $current = $this->__get($key);

                $result[$key] = $current;
            }

            return $result;
        }

        return false;
    }

    /**
     * Short description for 'getInstance'
     *
     * Long description (if any) ...
     *
     * @param     mixed $instance Parameter description (if any) ...
     * @return   \Components\Tools\Models\Version|false  The version, or false if it cannot be found
     */
    public static function getInstance($instance)
    {
        $hztv = new self();

        if ($hztv->read($instance) === false) {
            return false;
        }

        return $hztv;
    }

    /**
     * Short description for 'createInstance'
     *
     * Long description (if any) ...
     *
     * @param     string $toolname Parameter description (if any) ...
     * @param     string $instance Parameter description (if any) ...
     * @return   mixed Return description (if any) ...
     */
    public static function createInstance($toolname, $instance)
    {
        if (empty($toolname)) {
            return false;
        }

        if (empty($instance)) {
            return false;
        }

        $newinstance = new self();
        $newinstance->toolname = $toolname;
        $newinstance->instance = $instance;

        if ($newinstance->create()) {
            return $newinstance;
        }

        return false;
    }

    /**
     * Short description for 'sqlCreate'
     *
     * Long description (if any) ...
     *
     * @return   boolean Return description (if any) ...
     */
    private function sqlCreate()
    {
        $db = App::get('db');

        if (empty($db)) {
            return false;
        } elseif (is_numeric($this->id)) {
            $query = "INSERT INTO #__tool_version (id,toolname,instance) VALUES ( "
                . $db->Quote($this->id) . "," . $db->Quote($this->toolname) . ","
                . $db->Quote($this->instance) . ");";

            $db->setQuery($query);

            $result = $db->query();

            if ($result !== false || $db->getErrorNum() == 1062) {
                return true;
            }
        } else {
            $query = "INSERT INTO #__tool_version (toolname,instance) VALUES ( "
                . $db->Quote($this->toolname) . "," . $db->Quote($this->instance) . ");";

            $db->setQuery($query);

            $result = $db->query();

            if ($result === false && $db->getErrorNum() == 1062) {
                $query = "SELECT id FROM #__tool_version where instance="
                    . $db->Quote($this->instance) . ";";

                $db->setQuery($query);

                $result = $db->loadResult();

                if ($result == null) {
                    return false;
                }

                $this->id = $result;
                return true;
            } elseif ($result !== false) {
                $this->id = $db->insertid();
                return true;
            }
        }

        return false;
    }

    /**
     * Short description for 'create'
     *
     * Long description (if any) ...
     *
     * @return   boolean Return description (if any) ...
     */
    public function create()
    {
        if (true) {
            $result = $this->sqlCreate();

            if ($result === false) {
                $this->error(__FUNCTION__ . ": MySQL create failed", E_USER_WARNING);
            }
        }

        return $result;
    }

    /**
     * Short description for 'sqlRead'
     *
     * Long description (if any) ...
     *
     * @return   boolean Return description (if any) ...
     */
    private function sqlRead()
    {
        $db = App::get('db');
        $lazyloading = false;

        if (empty($db)) {
            return false;
        }

        if (is_numeric($this->instance)) {
            $query = "SELECT * FROM #__tool_version WHERE id=" . $db->Quote($this->instance) . ";";
        } else {
            $query = "SELECT * FROM #__tool_version WHERE instance="
                . $db->Quote($this->instance) . ";";
        }

        $db->setQuery($query);

        $result = $db->loadAssoc();

        if (empty($result)) {
            return false;
        }

        $this->clear();

        foreach ($result as $key => $value) {
            $this->__set($key, $value);
        }

        $this->__unset('alias');
        $this->__unset('middleware');
        $this->__unset('hostreq');
        $this->__unset('author');
        $this->__unset('member');
        $this->__unset('owner');

        if (!$lazyloading) {
            $this->__get('alias');
            $this->__get('middleware');
            $this->__get('hostreq');
            $this->__get('author');
            $this->__get('member');
            $this->__get('owner');
        }

        $this->updatedkeys = [];

        return true;
    }

    /**
     * Short description for 'read'
     *
     * Long description (if any) ...
     *
     * @param     mixed $instance Parameter description (if any) ...
     * @return   boolean Return description (if any) ...
     */
    public function read($instance = null)
    {
        if (is_null($instance)) {
            $instance = $this->instance;

            if (!empty($instance) && !is_string($instance) && !is_numeric($instance)) {
                $this->error(
                    __FUNCTION__ . ": invalid tool version instance defined",
                    E_USER_ERROR,
                );
                die();
            }
        }

        if (!empty($instance) && !is_string($instance) && !is_numeric($instance)) {
            $this->error(
                __FUNCTION__ . ": Argument #1 is not a valid string and not numeric",
                E_USER_ERROR,
            );
            die();
        }

        $result = true;

        if (true) {
            $this->clear();
            $this->instance = $instance;

            $result = $this->sqlRead();

            if ($result === false) {
                $this->clear();
            }
        }

        return $result;
    }

    /**
     * Short description for 'sqlUpdate'
     *
     * Long description (if any) ...
     *
     * @param     boolean $all Parameter description (if any) ...
     * @return   boolean Return description (if any) ...
     */
    private function sqlUpdate($all = false)
    {
        $db = App::get('db');

        $query = "UPDATE #__tool_version SET ";

        $classvars = get_class_vars(__CLASS__);

        $first = true;

        foreach ($classvars as $property => $value) {
            if (($property[0] == '_') || in_array($property, $this->listKeys)) {
                continue;
            }

            if (!$all && !in_array($property, $this->updatedkeys)) {
                continue;
            }

            if (!$first) {
                $query .= ',';
            } else {
                $first = false;
            }

            $value = $this->__get($property);

            if ($value === null) {
                $query .= "`$property`=NULL";
            } else {
                $query .= "`$property`=" . $db->Quote($value);
            }
        }

        $query .= " WHERE `id`=" . $db->Quote($this->__get('id')) . ";";

        if ($first == true) {
            $query = '';
        }

        if (!empty($query)) {
            $db->setQuery($query);

            $result = $db->query();

            if ($result === false) {
                return false;
            }
        }

        foreach ($this->listKeys as $property) {
            if (!$all && !in_array($property, $this->updatedkeys)) {
                continue;
            }

            if ($property == 'author') {
                $aux_table = '#__tool_authors';
            } elseif ($property == 'member' || $property == 'owner') {
                $aux_table = '#__tool_groups';
            } else {
                $aux_table = "#__tool_version_" . $property;
            }

            $list = $this->__get($property);

            if (!is_null($list) && !is_array($list)) {
                $list = [$list];
            }

            if (is_array($list) && count($list) > 0) {
                $first = true;

                if ($property == 'author') {
                    $query = "REPLACE INTO $aux_table (toolname,revision,uid,ordering,version_id) VALUES ";
                } elseif ($property == 'member' || $property == 'owner') {
                    $query = "REPLACE INTO $aux_table (cn,toolid,role) VALUES ";
                } else {
                    $query = "REPLACE INTO $aux_table (tool_version_id, " . $property . ") VALUES ";
                }

                $order = 0;

                foreach ($list as $value) {
                    if (!$first) {
                        $query .= ',';
                    }

                    $first = false;

                    if ($property == 'author') {
                        $query .= '(' . $db->Quote($this->toolname) . ','
                            . $db->Quote($this->revision) . ',' . $db->Quote($value) . ','
                            . $db->Quote($order) . ',' . $db->Quote($this->id) . ')';
                    } elseif ($property == 'member') {
                        $query .= '(' . $db->Quote($value) . ',' . $db->Quote($this->toolid) . ','
                            . $db->Quote('0') . ')';
                    } elseif ($property == 'owner') {
                        if ($value == 'apps' || $value == 'contribtooladmin') {
                            $query .= '(' . $db->Quote($value) . ',' . $db->Quote($this->toolid)
                                . ',' . $db->Quote('2') . ')';
                        } else {
                            $query .= '(' . $db->Quote($value) . ',' . $db->Quote($this->toolid)
                                . ',' . $db->Quote('1') . ')';
                        }
                    } else {
                        $query .= '(' . $db->Quote($this->id) . ',' . $db->Quote($value) . ')';
                    }

                    $order++;
                }

                $db->setQuery($query);

                if (!$db->query()) {
                    return false;
                }
            }

            if (!is_array($list) || count($list) == 0) {
                if ($property == 'author') {
                    $query = "DELETE FROM $aux_table WHERE version_id=" . $db->Quote($this->id) . ";";
                } elseif ($property == 'member') {
                    $query = "DELETE FROM $aux_table WHERE toolid=" . $db->Quote($this->toolid) . " AND role='0';";
                } elseif ($property == 'owner') {
                    $query = "DELETE FROM $aux_table WHERE toolid=" . $db->Quote($this->toolid) . " AND role='1';";
                } else {
                    $query = "DELETE FROM $aux_table WHERE tool_version_id=" . $db->Quote($this->id) . ";";
                }
            } else {
                foreach ($list as $key => $value) {
                    $list[$key] = $db->Quote($value);
                }

                $valuelist = implode(",", $list);

                if ($property == 'author') {
                    $query = "DELETE FROM $aux_table WHERE version_id=" . $db->Quote($this->id)
                        . " AND uid NOT IN ($valuelist);";
                } elseif ($property == 'member') {
                    $query = "DELETE FROM $aux_table WHERE role='0' AND toolid="
                        . $db->Quote($this->toolid) . " AND cn NOT IN ($valuelist);";
                } elseif ($property == 'owner') {
                    $query = "DELETE FROM $aux_table WHERE (role='1' OR role='2') AND toolid="
                        . $db->Quote($this->toolid) . " AND cn NOT IN ($valuelist);";
                } else {
                    $query = "DELETE FROM $aux_table WHERE tool_version_id="
                        . $db->Quote($this->id) . " AND $property NOT IN ($valuelist);";
                }
            }

            $db->setQuery($query);

            if (!$db->query()) {
                Log::debug('_sql_update_failed');
                return false;
            }
        }

        return true;
    }

    /**
     * Short description for 'sync'
     *
     * Long description (if any) ...
     *
     * @return   mixed Return description (if any) ...
     */
    public function sync()
    {
        $this->updateAll = true;
        return $this->update();
    }

    /**
     * Short description for 'update'
     *
     * Long description (if any) ...
     *
     * @return   boolean Return description (if any) ...
     */
    public function update()
    {
        Log::debug("update");

        $result = true;

        if (true) {
            $result = $this->sqlUpdate($this->updateAll);

            if ($result === false) {
                $this->error(__FUNCTION__ . ": MySQL update failed", E_USER_WARNING);
            }
        }

        $this->updateAll = false;
        return $result;
    }

    /**
     * Short description for 'sqlDelete'
     *
     * Long description (if any) ...
     *
     * @return   boolean Return description (if any) ...
     */
    private function sqlDelete()
    {
        if (!isset($this->instance) && !isset($this->id)) {
            return false;
        }

        $db = App::get('db');

        if (empty($db)) {
            return false;
        }

        if (!isset($this->id)) {
            $db->setQuery("SELECT id FROM #__tool_version WHERE instance=" . $db->Quote($this->instance) . ";");

            $this->id = $db->loadResult();
        }

        if (empty($this->id)) {
            return false;
        }

        $db->setQuery("DELETE FROM #__tool_version WHERE id=" . $db->Quote($this->id) . ";");

        if (!$db->query()) {
            return false;
        }

        $db->setQuery("DELETE FROM #__tool_version_alias WHERE tool_version_id=" . $db->Quote($this->id) . ";");
        $db->query();

        $db->setQuery("DELETE FROM #__tool_version_hostreq WHERE tool_version_id=" . $db->Quote($this->id) . ";");
        $db->query();

        $db->setQuery("DELETE FROM #__tool_version_middleware WHERE tool_version_id=" . $db->Quote($this->id) . ";");
        $db->query();

        if ($this->state == 3) {
            $db->setQuery(
                "DELETE FROM #__trac_user_action as a,#__trac as t WHERE a.trac_id=t.id AND t.scope='tool' AND t.name="
                    . $db->Quote($this->toolname),
            );
            $db->query();

            $db->setQuery(
                "DELETE FROM #__trac_group_action as a,#__trac as t WHERE a.trac_id=t.id AND t.scope='tool' AND t.name="
                    . $db->Quote($this->toolname),
            );
            $db->query();

            $db->setQuery("DELETE FROM #__trac as t WHERE t.scope='tool' AND t.name=" . $db->Quote($this->toolname));
            $db->query();
        }

        return true;
    }

    /**
     * Short description for 'delete'
     *
     * Long description (if any) ...
     *
     * @return   boolean Return description (if any) ...
     */
    public function delete()
    {
        $result = true;

        if (true) {
            $result = $this->sqlDelete();

            if ($result === false) {
                $this->error(__FUNCTION__ . ": MySQL deletion failed", E_USER_WARNING);
            }
        }

        return $result;
    }

    /**
     * Short description for '__get'
     *
     * Long description (if any) ...
     *
     * @param     string $property Parameter description (if any) ...
     * @return   string|null Return description (if any) ...
     */
    public function __get($property = null)
    {
        if (!property_exists(__CLASS__, $property) || $property[0] == '_') {
            if (empty($property)) {
                $property = '(null)';
            }

            $this->error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
            die();
        }
        if (in_array($property, $this->listKeys)) {
            if (!array_key_exists($property, get_object_vars($this))) {
                $db = App::get('db');

                if (is_object($db)) {
                    if (in_array($property, ['alias', 'middleware', 'hostreq'])) {
                        $aux_table = "#__tool_version_" . $property;

                        $query = "SELECT $property FROM $aux_table AS aux WHERE "
                            . " aux.tool_version_id=" . $db->Quote($this->id)
                            . " ORDER BY $property" . " ASC;";
                    } elseif ($property == 'author') {
                        $query = "SELECT uid FROM #__tool_authors WHERE version_id="
                            . $db->Quote($this->id) . " ORDER BY ordering ASC;";
                    } elseif ($property == 'member') {
                        $query = "SELECT cn FROM #__tool_groups WHERE role='0' AND toolid="
                            . $db->Quote($this->toolid) . " ORDER BY cn ASC;";
                    } elseif ($property == 'owner') {
                        $query = "SELECT cn FROM #__tool_groups WHERE (role='1' OR role='2') AND "
                            . " toolid=" . $db->Quote($this->toolid) . " ORDER BY cn ASC;";
                    } else {
                        $query = null;
                    }

                    $db->setQuery($query);

                    $result = $db->loadColumn();

                    if ($result !== false) {
                        $this->$property = (isset($result[0])) ? $result : [];
                        $this->updatedkeys = array_diff($this->updatedkeys, [$property]);
                    }
                }
            }
        }

        if (isset($this->$property)) {
            return $this->$property;
        }

        if (array_key_exists($property, get_object_vars($this))) {
            return null;
        }

        $this->error("Undefined property " . __CLASS__ . "::$" . $property, E_USER_NOTICE);

        return null;
    }

    /**
     * Short description for '__set'
     *
     * Long description (if any) ...
     *
     * @param     string $property Parameter description (if any) ...
     * @param     array $value Parameter description (if any) ...
     */
    public function __set($property = null, $value = null)
    {
        if (!property_exists(__CLASS__, $property) || $property[0] == '_') {
            if (empty($property)) {
                $property = '(null)';
            }

            $this->error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
            die();
        }

        if ($property == 'owner' || $property == 'member') {
            $this->$property = array_map(
                "strtolower",
                array_values(array_unique(array_diff((array) $value, ['']))),
            );
        } elseif (in_array($property, $this->listKeys)) {
            $this->$property = array_values(array_unique(array_diff((array) $value, [''])));
        } else {
            $this->$property = $value;
        }

        if (!in_array($property, $this->updatedkeys)) {
            $this->updatedkeys[] = $property;
        }
    }

    /**
     * Short description for '__isset'
     *
     * Long description (if any) ...
     *
     * @param     string $property Parameter description (if any) ...
     * @return   string Return description (if any) ...
     */
    public function __isset($property = null)
    {
        if (!property_exists(__CLASS__, $property) || $property[0] == '_') {
            if (empty($property)) {
                $property = '(null)';
            }

            $this->error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
            die();
        }

        return isset($this->$property);
    }

    /**
     * Short description for '__unset'
     *
     * Long description (if any) ...
     *
     * @param     string $property Parameter description (if any) ...
     * @return   void
     */
    public function __unset($property = null)
    {
        if (!property_exists(__CLASS__, $property) || $property[0] == '_') {
            if (empty($property)) {
                $property = '(null)';
            }

            $this->error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
            die();
        }

        $this->updatedkeys = array_diff($this->updatedkeys, [$property]);

        unset($this->$property);
    }

    /**
     * Short description for 'error'
     *
     * Long description (if any) ...
     *
     * @param     string $message Parameter description (if any) ...
     * @param     integer $level Parameter description (if any) ...
     * @return   void
     */
    private function error($message, $level = E_USER_NOTICE)
    {
        $backtrace = debug_backtrace();

        $caller = next($backtrace);

        switch ($level) {
            case E_USER_NOTICE:
                echo "Notice: ";
                break;
            case E_USER_ERROR:
                echo "Fatal error: ";
                break;
            default:
                echo "Unknown error: ";
                break;
        }

        echo $message . ' in ' . $caller['file'] . ' on line ' . $caller['line'] . "\n";
    }

    /**
     * Short description for 'get'
     *
     * Long description (if any) ...
     *
     * @param     mixed $key Parameter description (if any) ...
     * @return   mixed Return description (if any) ...
     */
    public function get($key)
    {
        return $this->__get($key);
    }

    /**
     * Short description for 'set'
     *
     * Long description (if any) ...
     *
     * @param     mixed $key Parameter description (if any) ...
     * @param     mixed $value Parameter description (if any) ...
     * @return   mixed Return description (if any) ...
     */
    public function set($key, $value)
    {
        return $this->__set($key, $value);
    }

    /**
     * Short description for 'add'
     *
     * Long description (if any) ...
     *
     * @param     mixed $key Parameter description (if any) ...
     * @param     array $value Parameter description (if any) ...
     * @return   void
     */
    public function add($key = null, $value = [])
    {
        $this->__set($key, array_merge($this->__get($key), (array) $value));
    }

    /**
     * Short description for 'remove'
     *
     * Long description (if any) ...
     *
     * @param     mixed $key Parameter description (if any) ...
     * @param     array $value Parameter description (if any) ...
     * @return   void
     */
    public function remove($key = null, $value = [])
    {
        $this->__set($key, array_diff($this->__get($key), (array) $value));
    }

    /**
     * Short description for 'getDevelopmentGroup'
     *
     * Long description (if any) ...
     *
     * @param     boolean $byid Parameter description (if any) ...
     * @return   mixed Return description (if any) ...
     */
    public function getDevelopmentGroup($byid = false)
    {
        $db = App::get('db');

        if ($byid == false) {
            $query = "SELECT cn FROM #__tool_groups AS tg WHERE tg.role='1' AND tg.toolid="
                . $db->Quote($this->toolid)
                . " LIMIT 1";
        } else {
            $query = "SELECT gidNumber FROM #__xgroups AS xg, #_tool_groups AS tg WHERE tg.cn=xg.cn AND tg.role='1' "
                . "AND tg.toolid="
                . $db->Quote($this->toolid)
                . " LIMIT 1";
        }
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }

    /**
     * Short description for 'getVersionInfo'
     *
     * Long description (if any) ...
     *
     * @param     string $id Parameter description (if any) ...
     * @param     string $version Parameter description (if any) ...
     * @param     string $toolname Parameter description (if any) ...
     * @param     string $instance Parameter description (if any) ...
     * @return   object Return description (if any) ...
     */
    public static function getVersionInfo($id, $version = null, $toolname = null, $instance = null)
    {
        $db = App::get('db');
        // data comes from db
        $query  = "SELECT v.*, d.doi_label as doi ";
        $query .= "FROM #__tool_version as v LEFT JOIN #__doi_mapping as d ON d.alias = v.toolname AND "
            . "d.local_revision=v.revision ";
        if ($id) {
            $query .= "WHERE v.id = " . $db->quote($id) . " ";
        } elseif ($version && $toolname) {
            if (is_array($toolname)) {
                $query .= "LEFT JOIN #__tool_version AS v2 ON v2.revision < v.revision AND v2.toolname=v.toolname ";
                $query .= "WHERE v.toolname IN (" . implode(',', array_map([$db, 'quote'], $toolname)) . ") ";
            } else {
                $query .= "WHERE v.toolname=" . $db->quote($toolname) . " ";
            }
            switch ($version) {
                case 'current':
                    $query .= "AND v.state=1 ORDER BY v.revision DESC";
                    if (!is_array($toolname)) {
                        $query .= " LIMIT 1";
                    }
                    break;
                case 'dev':
                    $query .= "AND v.state=3";
                    if (!is_array($toolname)) {
                        $query .= " LIMIT 1";
                    }
                    break;
                default:
                    $query .= "AND v.version = " . $db->quote($version) . " ";
                    break;
            }
        } elseif ($instance) {
            $query .= "WHERE v.instance=" . $db->quote($instance) . " ";
        }
        $db->setQuery($query);
        return $db->loadObjectList();
    }
}
