<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

use Hubzero\Database\Driver;

/**
 * Fluent View Builder for CREATE OR REPLACE VIEW operations
 *
 * Provides a fluent interface for view creation that allows specifying
 * MySQL-specific options (algorithm, definer, security) that are ignored
 * on SQLite and PostgreSQL.
 *
 * Usage:
 * ```php
 * // Create view with MySQL-specific options
 * $schema->createView('#__contributors_view')
 *     ->algorithm('UNDEFINED')
 *     ->definer('CURRENT_USER')
 *     ->security('INVOKER')
 *     ->as('SELECT id, name FROM #__users WHERE active = 1');
 *
 * // Create view with defaults (UNDEFINED, CURRENT_USER, INVOKER)
 * $schema->createView('#__active_users')
 *     ->as('SELECT * FROM #__users WHERE active = 1');
 * ```
 *
 */
class ViewBuilder
{
    /**
     * Database driver instance
     *
     * @var Driver
     */
    protected $driver;

    /**
     * View name
     *
     * @var string
     */
    protected $name;

    /**
     * Algorithm option (MySQL-specific)
     * UNDEFINED, MERGE, or TEMPTABLE
     *
     * @var string
     */
    protected $algorithm = 'UNDEFINED';

    /**
     * Definer option (MySQL-specific)
     *
     * @var string
     */
    protected $definer = 'CURRENT_USER';

    /**
     * SQL Security option (MySQL-specific)
     * DEFINER or INVOKER
     *
     * @var string
     */
    protected $security = 'INVOKER';

    /**
     * Create a new view builder
     *
     * @param  Driver  $driver  Database driver
     * @param  string  $name    View name (with or without prefix)
     */
    public function __construct(Driver $driver, string $name)
    {
        $this->driver = $driver;
        $this->name = $name;
    }

    /**
     * Set the view algorithm (MySQL-specific, ignored on SQLite/PostgreSQL)
     *
     * @param   string  $algorithm  UNDEFINED, MERGE, or TEMPTABLE
     * @return  $this
     */
    public function algorithm(string $algorithm): self
    {
        $this->algorithm = strtoupper($algorithm);
        return $this;
    }

    /**
     * Set the view definer (MySQL-specific, ignored on SQLite/PostgreSQL)
     *
     * @param   string  $definer  User who owns the view (e.g., 'CURRENT_USER', 'root@localhost')
     * @return  $this
     */
    public function definer(string $definer): self
    {
        $this->definer = $definer;
        return $this;
    }

    /**
     * Set the SQL security mode (MySQL-specific, ignored on SQLite/PostgreSQL)
     *
     * @param   string  $security  DEFINER or INVOKER
     * @return  $this
     */
    public function security(string $security): self
    {
        $this->security = strtoupper($security);
        return $this;
    }

    /**
     * Set the SELECT statement and create the view
     *
     * @param   string  $selectSql  The SELECT statement for the view
     * @return  bool
     */
    public function as(string $selectSql): bool
    {
        return $this->driver->createOrReplaceView($this->name, $selectSql, [
            'algorithm' => $this->algorithm,
            'definer'   => $this->definer,
            'security'  => $this->security
        ]);
    }
}
