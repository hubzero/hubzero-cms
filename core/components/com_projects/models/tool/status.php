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
 * Project Tool Status model
 */
class Status extends Model
{
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $statuses;

    /**
     * Table class name
     *
     * @var  string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $tableClass = '\\Components\\Projects\\Tables\\ToolStatus';

    /**
     * Registry
     *
     * @var  object
     */
    public $config = null;

    /**
     * Constructor
     *
     * @param   integer  $oid
     * @return  void
     */
    public function __construct($oid = null)
    {
        $this->db = App::get('db');

        if (!isset($this->table)) {
            $this->table = new Tables\ToolStatus($this->db);
        }
        if (!isset($this->statuses)) {
            $this->statuses = [];
            $statuses = $this->table->getItems();
            foreach ($statuses as $status) {
                $this->statuses[$status->id] = $status;
            }
        }

        if (is_numeric($oid)) {
            if (isset($this->statuses[$oid])) {
                $this->table->bind($this->statuses[$oid]);
            } else {
                $this->table->load($oid);
            }
        }
    }

    /**
     * Returns a reference to the model
     *
     * @param   mixed  $oid  status ID
     * @return  void
     */
    public function getStatus($oid = null)
    {
        if (isset($this->statuses[$oid])) {
            $this->table->bind($this->statuses[$oid]);
        }
    }

    /**
     * Returns a reference to the model
     *
     * @param   mixed  $oid  status ID
     * @return  object
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
}
