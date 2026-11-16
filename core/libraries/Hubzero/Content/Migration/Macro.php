<?php

/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration;

use Hubzero\Database\Driver;

/**
 * Abstract migration macro class
 */
abstract class Macro
{
    /**
     * Migration instance
     *
     * @var  \Hubzero\Content\Migration\Base|null
     */
    protected $migration = null;

    /**
     * Database connection
     *
     * @var  \Hubzero\Database\Driver|null
     */
    protected $db = null;

    /**
     * Set the database connection
     *
     * @param   \Hubzero\Database\Driver $database
     * @return  $this
     */
    public function setDatabase(Driver $database)
    {
        $this->db = $database;

        return $this;
    }

    /**
     * Set the migration object
     *
     * @param   \Hubzero\Content\Migration\Base $migration
     * @return  $this
     */
    public function setMigration(Base $migration)
    {
        $this->migration = $migration;

        return $this;
    }

    /**
     * Get the migration object
     *
     * @return  \Hubzero\Content\Migration\Base
     */
    public function getMigration()
    {
        return $this->migration;
    }

    /**
     * Log a message
     *
     * @param   string  $message
     * @param   string  $type
     * @return  void
     */
    public function log($message, $type = 'info')
    {
        $this->getMigration()->log($message, $type);
    }
}
