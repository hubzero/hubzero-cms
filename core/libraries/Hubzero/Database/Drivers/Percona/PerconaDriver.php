<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Percona;

use Hubzero\Database\Exception\UnsupportedEngineException;

/**
 * Percona (Pdo) database driver
 *
 * Percona Server for MySQL is a free, fully compatible, enhanced, and open source
 * drop-in replacement for any MySQL database. It provides superior performance,
 * scalability, and instrumentation.
 *
 * Key Percona-specific features:
 * - XtraDB storage engine (enhanced InnoDB)
 * - Additional performance monitoring capabilities
 * - Enhanced query response time statistics
 * - TokuDB storage engine support (deprecated in 8.0)
 *
 */
class PerconaDriver extends \Hubzero\Database\Drivers\Mysql\MysqlDriver
{
    /**
     * The name of the database driver
     *
     * @var string
     */
    protected $name = 'percona';

    /**
     * Set the database engine of the given table
     *
     * Percona supports all standard MySQL engines plus XtraDB (which is
     * Percona's enhanced version of InnoDB and is the default).
     *
     * Note: In Percona Server 8.0+, XtraDB is effectively merged with InnoDB,
     * so specifying 'xtradb' will use InnoDB with Percona enhancements.
     *
     * @param   string  $table   The table for which to retrieve the engine type
     * @param   string  $engine  The engine type to set
     * @return  bool
     * @throws  UnsupportedEngineException  If the specified engine is not supported
     **/
    public function setEngine($table, $engine)
    {
        $supported = [
            // Standard MySQL engines
            'innodb',
            'myisam',
            'archive',
            'merge',
            'memory',
            'csv',
            'federated',
            // Percona-specific engines
            'xtradb',   // Enhanced InnoDB (maps to InnoDB in 8.0+)
            'tokudb',   // Fractal tree indexing (deprecated in 8.0)
            'rocksdb',  // LSM tree storage engine
            'myrocks'   // Alias for RocksDB
        ];

        $engineLower = strtolower($engine);

        if (!in_array($engineLower, $supported)) {
            throw new UnsupportedEngineException(sprintf(
                'Unsupported engine type of "%s" specified. Engine type must be one of: %s',
                $engine,
                implode(', ', $supported)
            ));
        }

        // XtraDB maps to InnoDB in Percona Server 8.0+
        // The XtraDB enhancements are built into InnoDB
        if ($engineLower === 'xtradb') {
            $engine = 'InnoDB';
        }

        // MyRocks is an alias for RocksDB
        if ($engineLower === 'myrocks') {
            $engine = 'RocksDB';
        }

        $table = $this->replacePrefix($table);

        $this->setQuery("ALTER TABLE `$table` ENGINE = $engine");
        $this->execute();

        return true;
    }

    /**
     * Get database server version information
     *
     * @return  array  Array with standardized keys:
     *                  - 'version': Full version string from server
     *                  - 'driver_version': Normalized version (x.y.z format) - STANDARD KEY
     *                  - 'percona_version': Version comment (deprecated, use driver_version)
     *                  - 'comment': Version comment/description
     */
    public function getServerInfo()
    {
        $this->setQuery("SHOW VARIABLES LIKE '%version%'");
        $rows = $this->loadObjectList('Variable_name');

        $version = $rows['version']->Value ?? null;
        $comment = $rows['version_comment']->Value ?? null;

        // Extract version from version string (e.g., "8.0.32-24")
        $driverVersion = null;
        if ($version && preg_match('/^(\d+\.\d+\.\d+)/', $version, $matches)) {
            $driverVersion = $matches[1];
        }

        return [
            'version'         => $version,
            'driver_version'  => $driverVersion,  // Standard key for all drivers
            'percona_version' => $driverVersion,  // Deprecated alias for backwards compatibility
            'comment'         => $comment,
        ];
    }

    /**
     * Get the major version number of the database server
     *
     * @return  float|null  Major version (e.g., 8.0) or null if unknown
     */
    public function getMajorVersion()
    {
        $info = $this->getServerInfo();
        $version = $info['version'] ?? null;

        if ($version && preg_match('/^(\d+\.\d+)/', $version, $matches)) {
            return (float) $matches[1];
        }

        return null;
    }

    /**
     * Percona tests use INTEGER for CAST expressions.
     *
     * @return  string
     */
    public function getIntegerCastKeyword(): string
    {
        return 'INTEGER';
    }

    /**
     * Get Percona XtraDB cluster status (if running in cluster mode)
     *
     * @return  array|null  Cluster status or null if not in cluster mode
     */
    public function getClusterStatus()
    {
        try {
            $this->setQuery("SHOW STATUS LIKE 'wsrep_%'");
            $rows = $this->loadObjectList('Variable_name');

            if (empty($rows)) {
                return null;
            }

            return [
                'cluster_size'   => $rows['wsrep_cluster_size']->Value ?? null,
                'cluster_status' => $rows['wsrep_cluster_status']->Value ?? null,
                'node_status'    => $rows['wsrep_local_state_comment']->Value ?? null,
                'connected'      => ($rows['wsrep_connected']->Value ?? 'OFF') === 'ON',
                'ready'          => ($rows['wsrep_ready']->Value ?? 'OFF') === 'ON',
            ];
        } catch (\Exception $e) {
            // Not running in cluster mode
            return null;
        }
    }

    /**
     * Check if a specific storage engine is available
     *
     * @param   string  $engine  Engine name to check
     * @return  bool
     */
    public function hasEngine($engine)
    {
        $this->setQuery("SHOW ENGINES");
        $engines = $this->loadObjectList('Engine');

        $engine = strtoupper($engine);

        // XtraDB shows up as InnoDB in engine list
        if ($engine === 'XTRADB') {
            $engine = 'INNODB';
        }

        if (!isset($engines[$engine])) {
            return false;
        }

        $support = strtoupper($engines[$engine]->Support);
        return in_array($support, ['DEFAULT', 'YES']);
    }

    /**
     * Get query response time statistics (Percona-specific feature)
     *
     * Requires the QUERY_RESPONSE_TIME plugin to be installed.
     *
     * @return  array|null  Response time statistics or null if not available
     */
    public function getQueryResponseTimeStats()
    {
        try {
            $this->setQuery("SELECT * FROM INFORMATION_SCHEMA.QUERY_RESPONSE_TIME");
            return $this->loadObjectList();
        } catch (\Exception $e) {
            // Plugin not installed
            return null;
        }
    }

    /**
     * Get the schema grammar instance for this driver
     *
     * @return  \Hubzero\Database\Drivers\Base\BaseSchemaGrammar
     */
    public function getSchemaGrammar()
    {
        return $this->makeSchemaGrammarFromRegistry();
    }
}
