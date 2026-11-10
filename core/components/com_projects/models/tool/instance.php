<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Tool;

use Hubzero\Base\Model;
use Components\Projects\Tables;
use Hubzero\Facades\App;

/**
 * Project Tool Instance model
 */
class Instance extends Model
{
    protected $params;

    /**
     * Table class name
     *
     * @var string
     */
    protected $tableClass = '\\Components\\Projects\\Tables\\ToolInstance';

    /**
     * Registry
     *
     * @var object
     */
    public $config = null;

    /**
     * Constructor
     *
     * @return     void
     */
    public function __construct($oid, $parent = null)
    {
        $this->db = App::get('db');

        $this->table = new Tables\ToolInstance($this->db);

        if ($oid && $oid != 'dev') {
            if (is_numeric($oid)) {
                $this->table->load($oid);
            } elseif (is_string($oid)) {
                $this->table->loadFromInstanceName($oid);
            } elseif (is_object($oid)) {
                $this->bind($oid);
            }
        } elseif ($parent) {
            // Load dev instance
            $this->table->loadFromParent($parent, 'dev');
        }

        $this->params = new \Hubzero\Config\Registry($this->table->get('params'));
    }

    /**
     * Returns a reference to the model
     *
     * @param      mixed $oid object ID
     * @return     object Todo
     */
    public static function &getInstance($oid = null, $parent = null)
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
            $instances[$key] = new self($oid, $parent);
        }

        return $instances[$key];
    }

    /**
     * Update Parent Name
     *
     * @param      integer $id
     * @param      string $name
     * @return     boolean
     */
    public function updateParentName($id = null, $name = null)
    {
        if (!$id || !$name) {
            return false;
        }
        return $this->table->updateParentName($id, $name);
    }
}
