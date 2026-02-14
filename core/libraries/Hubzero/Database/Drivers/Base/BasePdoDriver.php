<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Base;

use Hubzero\Database\Driver;
use Hubzero\Database\Connection\PdoConnection;
use Hubzero\Database\Exception\ConnectionFailedException;

/**
 * PDO database driver - Backward compatibility layer
 *
 * @deprecated  This class exists for backward compatibility only. Connection
 *              mechanics have been absorbed into Driver. New code should use
 *              the concrete dialect drivers (Mysql, Pgsql, etc.) directly.
 *
 * Previously this class provided PDO connection mechanics between Driver and
 * Sql. Those methods now live in Driver itself, making this class a thin
 * constructor-only wrapper.
 *
 * The inheritance chain is preserved so that `instanceof Pdo` checks still
 * work for any external code that uses them.
 */
abstract class BasePdoDriver extends Driver
{
    /**
     * Constructs a new database object based on the given params
     *
     * Creates a lazy PdoConnection from the provided options. The actual
     * database connection is not opened until first use.
     *
     * @param   array  $options  The database connection params
     * @return  void
     * @throws  ConnectionFailedException  If PDO is not installed or DSN is not set
     */
    public function __construct($options)
    {
        // Make sure the DSN is set
        if (!isset($options['dsn']) || !$options['dsn']) {
            throw new ConnectionFailedException('DSN for PDO connection not set.', 500);
        }

        // Make sure extra PDO options array is set
        if (!isset($options['extras'])) {
            $options['extras'] = [];
        }

        // Create lazy PDO connection via ConnectionInterface
        // PdoConnection stores config and connects on first use
        $this->setConnection(new PdoConnection(
            (string)$options['dsn'],
            (string)($options['user'] ?? ''),
            (string)($options['password'] ?? ''),
            (array)$options['extras']
        ));

        // Call parent construct
        parent::__construct($options);
    }

    /**
     * Test to see if the PDO connector is available.
     *
     * @return  bool
     */
    public static function test()
    {
        return PdoConnection::isAvailable();
    }
}
