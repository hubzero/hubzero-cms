<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Tool;

use Hubzero\Base\Model;
use Components\Projects\Tables;
use Hubzero\Facades\App;

/**
 * Project Tool View model
 */
class View extends Model
{
    /**
     * Table class name
     *
     * @var  class-string<\Components\Projects\Tables\ToolView>
     */
    protected $tableClass = '\\Components\\Projects\\Tables\\ToolView';

    /**
     * Table, an instance of $tableClass
     *
     * @var \Components\Projects\Tables\ToolView
     */
    protected $table;

    /**
     * Registry
     *
     * @var  object
     */
    public $config;

    /**
     * Constructor
     *
     * @param   mixed  $oid  view ID
     * @return  void
     */
    public function __construct($oid = null)
    {
        $this->db = App::get('db');

        $this->table = new Tables\ToolView($this->db);

        if (is_numeric($oid)) {
            $this->table->load($oid);
        }
    }

    /**
     * Returns a reference to the model
     *
     * @param   int|string|\stdClass|array<string,mixed>|null  $oid  view ID
     * @return  self
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
     * Check if page was viewed recently
     *
     * @param   integer  $toolid  Project tool id
     * @param   integer  $userid  User id
     * @return  mixed    Return string or NULL
     */
    public function lastView($toolid = 0, $userid = 0)
    {
        return $this->table->checkView($toolid, $userid);
    }
}
