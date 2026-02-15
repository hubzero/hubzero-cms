<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Mock;

/**
 * Mock database driver (in-memory SQLite)
 *
 * A zero-config database driver for testing. Inherits all functionality
 * from the SQLite driver but always uses an in-memory database. Any
 * configuration passed to the constructor is ignored.
 *
 * Each instance gets its own isolated in-memory database that persists
 * for the lifetime of the connection. Disconnecting destroys all data.
 */
class MockDriver extends \Hubzero\Database\Drivers\Sqlite\SqliteDriver
{
    /**
     * The driver name
     *
     * @var string
     */
    protected $name = 'mock';

    /**
     * Constructs a new in-memory SQLite database
     *
     * All passed options are ignored. The driver always uses :memory:
     * with no table prefix.
     *
     * @param   array  $options  Ignored
     * @return  void
     */
    public function __construct($options = [])
    {
        parent::__construct([
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
