<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Module;

use Components\Groups\Tables;
use Components\Groups\Models\Module;
use Hubzero\Base\Obj;
use Hubzero\Base\Model\ItemList;
use Hubzero\Facades\App;

/**
 * Group module archive model class
 *
 */
class Archive extends Obj
{
    /**
     * \Hubzero\Base\Model
     *
     * @var \Hubzero\Base\Model\ItemList|null
     */
    private $modules = null;

    /**
     * Database
     *
     * @var object|null
     */
    private $db = null;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct()
    {
        $this->db = App::get('db');
    }

    /**
     * Get Instance of Module Archive
     *
     * @param   string|null $key Instance Key
     * @return  object \Components\Groups\Models\Module\Archive
     */
    public static function &getInstance($key = null)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = [];
        }

        if (!isset($instances[$key])) {
            $instances[$key] = new self();
        }

        return $instances[$key];
    }

    /**
     * Get a list of group modules
     *
     * @param   string  $rtrn    What data to return
     * @param   array<mixed>   $filters Filters to apply to data retrieval
     * @param   boolean $clear Clear cached data?
     * @return  mixed
     */
    public function modules($rtrn = 'list', $filters = [], $clear = false)
    {
        switch (strtolower($rtrn)) {
            case 'unapproved':
                $unapproved = [];
                if ($results = $this->modules('list', $filters, true)) {
                    foreach ($results as $k => $result) {
                        // if module is unapproved return it
                        if ($result->get('approved') == 0) {
                            $unapproved[] = $result;
                        }
                    }
                }
                return new ItemList($unapproved);
            case 'list':
            default:
                $tbl = new Tables\Module($this->db);
                if ($results = $tbl->find($filters)) {
                    foreach ($results as $key => $result) {
                        $results[$key] = new Module($result);
                    }
                }
                $this->modules = new ItemList($results);
                return $this->modules;
        }
    }
}
