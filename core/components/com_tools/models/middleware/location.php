<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Models\Middleware;

require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'zonelocations.php';
require_once __DIR__ . DS . 'base.php';

/**
 * Middleware zone location model
 */
class Location extends Base
{
    /**
     * Table class name
     *
     * @var string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_tbl_name = '\\Components\\Tools\\Tables\\ZoneLocations';

    /**
     * Returns a reference to a zone location model
     *
     * @param      mixed $oid Location ID or array or object
     * @return     object
     */
    public static function &getInstance($oid = null)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = array();
        }

        if (!isset($instances[$oid])) {
            $instances[$oid] = new self($oid);
        }

        return $instances[$oid];
    }
}
