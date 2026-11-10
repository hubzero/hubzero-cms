<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models;

use Hubzero\Base\Model;
use Hubzero\Facades\App;
use Hubzero\Facades\Component;

/**
 * Project Todo model
 */
class Todo extends Model
{
    /**
     * Table class name
     *
     * @var string
     */
    protected $tableClass = '\\Components\\Projects\\Tables\\Todo';

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
     * @return  void
     */
    public function __construct()
    {
        $this->db = App::get('db');

        $this->table = new \Components\Projects\Tables\Todo($this->db);

        $this->config = Component::params('com_projects');
    }

    /**
     * Returns a reference to a todo model
     *
     * @param   mixed   $oid  TODO ID
     * @return  object  Todo
     */
    public static function &getInstance($oid = null)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = [];
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
     * Get list name
     *
     * @param   integer  $projectid
     * @param   mixed    $filters
     * @return  array
     */
    public function getListName($projectid = null, $filters = [])
    {
        $color = is_array($filters) ? $filters['todolist'] : $filters;
        return $this->table->getListName($projectid, $color);
    }

    /**
     * Get lists
     *
     * @param   integer  $projectid
     * @return  array
     */
    public function getLists($projectid = null)
    {
        return $this->table->getTodoLists($projectid);
    }

    /**
     * Set and get a specific offering
     *
     * @param   integer  $id
     * @return  object
     */
    public function entry($id = null)
    {
        if (
            !isset($this->entry)
            || ($id !== null && (int) $this->entry->get('id') != $id)
        ) {
            $this->entry = Entry::getInstance($id);
        }
        return $this->entry;
    }

    /**
     * Get a list of todo items
     *   Accepts either a numeric array index or a string [id, name]
     *   If index, it'll return the entry matching that index in the list
     *   If string, it'll return either a list of IDs or names
     *
     * @param   string  $rtrn
     * @param   array   $filters
     * @return  array
     */
    public function entries($rtrn = 'list', $filters = [])
    {
        $results = [];

        switch (strtolower($rtrn)) {
            case 'count':
                $filters['count'] = 1;
                return (int) $this->table->getTodos(null, $filters);
                break;

            case 'list':
            case 'results':
            default:
                if ($results = $this->table->getTodos(null, $filters)) {
                    foreach ($results as $key => $result) {
                        $results[$key] = new Entry($result);
                    }
                } else {
                    $results = [];
                }
                return new \Hubzero\Base\ItemList($results);
                break;
        }
        return null;
    }

    /**
     * Store changes to this database entry
     *
     * @param   boolean  $check  Perform data validation check?
     * @return  boolean  False if error, True on success
     */
    public function store($check = true)
    {
        // Do nothing here yet.
        return true;
    }
}
