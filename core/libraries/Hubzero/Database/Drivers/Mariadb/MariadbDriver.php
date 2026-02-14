<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Mariadb;

use Hubzero\Database\Exception\UnsupportedEngineException;

/**
 * MariaDB (Pdo) database driver
 *
 * MariaDB is a community-developed, commercially supported fork of MySQL,
 * intended to remain free and open-source. It has diverged from MySQL over time
 * and includes many unique features and storage engines.
 *
 * Key MariaDB-specific features:
 * - Aria storage engine (crash-safe MyISAM replacement)
 * - ColumnStore engine (columnar storage for analytics)
 * - Spider engine (for sharding/federation)
 * - S3 engine (store tables on S3-compatible storage)
 * - Mroonga engine (full-text search with CJK support)
 * - Sequences (CREATE SEQUENCE)
 * - System-versioned tables (temporal tables)
 * - CHECK constraints (actually enforced, unlike MySQL)
 * - Galera Cluster support for synchronous replication
 *
 */
class MariadbDriver extends \Hubzero\Database\Drivers\Mysql\MysqlDriver
{
    /**
     * The name of the database driver
     *
     * @var string
     */
    protected $name = 'mariadb';

    /**
     * Set the database engine of the given table
     *
     * MariaDB supports all standard MySQL engines plus several unique engines:
     * - Aria: Crash-safe replacement for MyISAM
     * - ColumnStore: Columnar storage for analytics workloads
     * - Spider: For sharding and federation across servers
     * - S3: Store tables on S3-compatible object storage
     * - Mroonga: Full-text search with CJK language support
     * - Connect: Access external data sources (CSV, XML, ODBC, etc.)
     * - Sequence: Engine for generating sequences
     * - RocksDB: LSM-tree storage engine
     *
     * @param   string  $table   The table for which to set the engine type
     * @param   string  $engine  The engine type to set
     * @return  bool
     * @throws  UnsupportedEngineException  If the specified engine is not supported
     **/
    public function setEngine($table, $engine)
    {
        $supported = [
            // Standard MySQL-compatible engines
            'innodb',
            'myisam',
            'archive',
            'merge',
            'memory',
            'csv',
            'federated',
            // MariaDB-specific engines
            'aria',         // Crash-safe MyISAM replacement (default for system tables)
            'columnstore',  // Columnar storage for analytics (formerly InfiniDB)
            'spider',       // Sharding engine for table partitioning across servers
            's3',           // Store tables on S3-compatible storage (10.5+)
            'mroonga',      // Full-text search with CJK support
            'connect',      // Access external data sources
            'sequence',     // Engine for sequence objects
            'rocksdb',      // LSM-tree storage engine
            'tokudb',       // Fractal tree indexing (deprecated)
            'oqgraph',      // Open Query Graph engine for hierarchical data
            'sphinx',       // SphinxSE for Sphinx full-text search integration
            'blackhole',    // Accepts data but stores nothing (for replication)
        ];

        $engineLower = strtolower($engine);

        if (!in_array($engineLower, $supported)) {
            throw new UnsupportedEngineException(sprintf(
                'Unsupported engine type of "%s" specified. Engine type must be one of: %s',
                $engine,
                implode(', ', $supported)
            ));
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
     *                  - 'mariadb_version': Alias for driver_version (deprecated, use driver_version)
     *                  - 'comment': Version comment/description
     */
    public function getServerInfo()
    {
        $this->setQuery("SHOW VARIABLES LIKE '%version%'");
        $rows = $this->loadObjectList('Variable_name');

        $version = $rows['version']->Value ?? null;
        $comment = $rows['version_comment']->Value ?? null;

        // Extract MariaDB version from version string (e.g., "10.5.12-MariaDB")
        $driverVersion = null;
        if ($version && preg_match('/^(\d+\.\d+\.\d+)/', $version, $matches)) {
            $driverVersion = $matches[1];
        }

        return [
            'version'         => $version,
            'driver_version'  => $driverVersion,  // Standard key for all drivers
            'mariadb_version' => $driverVersion,  // Deprecated alias for backwards compatibility
            'comment'         => $comment,
        ];
    }

    /**
     * Get the MariaDB major version number
     *
     * @return  float|null  Major.minor version (e.g., 10.5) or null if unknown
     */
    public function getMajorVersion()
    {
        $info = $this->getServerInfo();
        $version = $info['mariadb_version'] ?? null;

        if ($version && preg_match('/^(\d+\.\d+)/', $version, $matches)) {
            return (float) $matches[1];
        }

        return null;
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

        // Engine names in MariaDB are case-insensitive but returned with specific casing
        // Check using case-insensitive comparison
        foreach ($engines as $eng) {
            if (strcasecmp($eng->Engine, $engine) === 0) {
                $support = strtoupper($eng->Support);
                return in_array($support, ['DEFAULT', 'YES']);
            }
        }

        return false;
    }

    /**
     * Get Galera Cluster status (if running in cluster mode)
     *
     * MariaDB Galera Cluster provides synchronous multi-master replication.
     *
     * @return  array|null  Cluster status or null if not in cluster mode
     */
    public function getGaleraStatus()
    {
        try {
            $this->setQuery("SHOW STATUS LIKE 'wsrep_%'");
            $rows = $this->loadObjectList('Variable_name');

            if (empty($rows) || !isset($rows['wsrep_cluster_size'])) {
                return null;
            }

            return [
                'cluster_size'        => (int) ($rows['wsrep_cluster_size']->Value ?? 0),
                'cluster_status'      => $rows['wsrep_cluster_status']->Value ?? null,
                'cluster_state_uuid'  => $rows['wsrep_cluster_state_uuid']->Value ?? null,
                'node_status'         => $rows['wsrep_local_state_comment']->Value ?? null,
                'node_name'           => $rows['wsrep_node_name']->Value ?? null,
                'connected'           => ($rows['wsrep_connected']->Value ?? 'OFF') === 'ON',
                'ready'               => ($rows['wsrep_ready']->Value ?? 'OFF') === 'ON',
                'flow_control_paused' => (float) ($rows['wsrep_flow_control_paused']->Value ?? 0),
            ];
        } catch (\Exception $e) {
            // Not running in Galera Cluster mode
            return null;
        }
    }

    /**
     * Check if Galera Cluster is enabled
     *
     * @return  bool
     */
    public function isGaleraCluster()
    {
        return $this->getGaleraStatus() !== null;
    }

    // =========================================================================
    // Sequence Support (MariaDB 10.3+)
    // =========================================================================

    /**
     * Check if sequences are supported (MariaDB 10.3+)
     *
     * @return  bool
     */
    public function supportsSequences(): bool
    {
        $version = $this->getMajorVersion();
        return $version !== null && $version >= 10.3;
    }

    /**
     * MariaDB supports INTEGER in CAST expressions.
     *
     * @return  string
     */
    public function getIntegerCastKeyword(): string
    {
        return 'INTEGER';
    }

    /**
     * List all sequences in the current database
     *
     * MariaDB 10.3+ stores sequences as TABLE_TYPE = 'SEQUENCE'
     * in INFORMATION_SCHEMA.TABLES. Falls back to emulated
     * sequences from parent Mysql for older versions.
     *
     * @return  array
     */
    public function getSequences(): array
    {
        if (!$this->supportsSequences()) {
            return parent::getSequences();
        }

        $this->setQuery(
            "SELECT TABLE_NAME"
            . " FROM INFORMATION_SCHEMA.TABLES"
            . " WHERE TABLE_TYPE = 'SEQUENCE'"
            . " AND TABLE_SCHEMA = DATABASE()"
            . " ORDER BY TABLE_NAME"
        );

        return $this->loadColumn() ?: [];
    }

    /**
     * Create a sequence
     *
     * Sequences provide a way to generate sequential integer values
     * without the overhead of table locking that AUTO_INCREMENT requires.
     *
     * @param   string  $name       Sequence name
     * @param   int     $start      Starting value (default: 1)
     * @param   int     $increment  Increment value (default: 1)
     * @param   array   $options    Additional options:
     *                              - minValue: Minimum value (default: 1)
     *                              - maxValue: Maximum value (default: 9223372036854775806)
     *                              - cycle: Whether to cycle when max is reached (default: false)
     *                              - cache: Number of values to cache (default: 1000)
     * @return  bool
     */
    public function createSequence($name, $start = 1, $increment = 1, array $options = []): bool
    {
        if (!$this->supportsSequences()) {
            return false;
        }

        // Extract options with defaults
        $minValue = $options['minValue'] ?? 1;
        $maxValue = $options['maxValue'] ?? 9223372036854775806;
        $cycle = $options['cycle'] ?? false;
        $cache = $options['cache'] ?? 1000;

        $name = $this->replacePrefix($name);
        $cycleStr = $cycle ? 'CYCLE' : 'NOCYCLE';

        $sql = "CREATE SEQUENCE IF NOT EXISTS `$name` " .
               "START WITH $start " .
               "INCREMENT BY $increment " .
               "MINVALUE $minValue " .
               "MAXVALUE $maxValue " .
               "$cycleStr " .
               "CACHE $cache";

        $this->setQuery($sql);
        $this->execute();

        return true;
    }

    /**
     * Drop a sequence
     *
     * @param   string  $name      Sequence name
     * @param   bool    $ifExists  Only drop if exists (default: true)
     * @return  bool
     */
    public function dropSequence($name, $ifExists = true): bool
    {
        if (!$this->supportsSequences()) {
            return false;
        }

        $name = $this->replacePrefix($name);
        $ifExistsStr = $ifExists ? 'IF EXISTS' : '';

        $this->setQuery("DROP SEQUENCE $ifExistsStr `$name`");
        $this->execute();

        return true;
    }

    /**
     * Get the next value from a sequence
     *
     * @param   string  $name  Sequence name
     * @return  int
     */
    public function nextSequenceValue($name): int
    {
        if (!$this->supportsSequences()) {
            return 0;
        }

        $name = $this->replacePrefix($name);

        $this->setQuery("SELECT NEXTVAL(`$name`)");
        $result = $this->loadResult();

        return $result !== null ? (int) $result : 0;
    }

    /**
     * Get the current value from a sequence (without incrementing)
     *
     * @param   string  $name  Sequence name
     * @return  int
     */
    public function currentSequenceValue($name): int
    {
        if (!$this->supportsSequences()) {
            return 0;
        }

        $name = $this->replacePrefix($name);

        // LASTVAL returns the most recent value generated by NEXTVAL
        // in the current session, or error if NEXTVAL not yet called
        try {
            $this->setQuery("SELECT LASTVAL(`$name`)");
            $result = $this->loadResult();
            return $result !== null ? (int) $result : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Set the next value for a sequence
     *
     * @param   string  $name   Sequence name
     * @param   int     $value  Value to set
     * @return  bool
     */
    public function setSequenceValue($name, $value): bool
    {
        if (!$this->supportsSequences()) {
            return false;
        }

        $name = $this->replacePrefix($name);
        $value = (int) $value;

        $this->setQuery("SELECT SETVAL(`$name`, $value)");
        $this->execute();

        return true;
    }

    /**
     * Check if a sequence exists
     *
     * @param   string  $name  Sequence name
     * @return  bool
     */
    public function sequenceExists($name): bool
    {
        if (!$this->supportsSequences()) {
            return false;
        }

        $name = $this->replacePrefix($name);

        // Sequences are stored in the INFORMATION_SCHEMA.TABLES as 'SEQUENCE' type
        $this->setQuery(
            "SELECT COUNT(*) FROM information_schema.TABLES " .
            "WHERE TABLE_SCHEMA = DATABASE() " .
            "AND TABLE_NAME = " . $this->quote($name) . " " .
            "AND TABLE_TYPE = 'SEQUENCE'"
        );

        return (int) $this->loadResult() > 0;
    }

    // =========================================================================
    // System-Versioned Tables (Temporal Tables, MariaDB 10.3.4+)
    // =========================================================================

    /**
     * Check if system versioning is supported (MariaDB 10.3.4+)
     *
     * @return  bool
     */
    public function supportsSystemVersioning()
    {
        $version = $this->getMajorVersion();
        return $version !== null && $version >= 10.3;
    }

    /**
     * Add system versioning to an existing table
     *
     * This enables temporal table functionality, allowing you to query
     * historical data using AS OF, BETWEEN, etc.
     *
     * @param   string  $table  Table name
     * @return  bool
     */
    public function addSystemVersioning(string $table): bool
    {
        if (!$this->supportsSystemVersioning()) {
            return false;
        }

        $table = $this->replacePrefix($table);

        $this->setQuery("ALTER TABLE `$table` ADD SYSTEM VERSIONING");
        $this->execute();

        return true;
    }

    /**
     * Remove system versioning from a table
     *
     * @param   string  $table  Table name
     * @return  bool
     */
    public function dropSystemVersioning(string $table): bool
    {
        if (!$this->supportsSystemVersioning()) {
            return false;
        }

        $table = $this->replacePrefix($table);

        $this->setQuery("ALTER TABLE `$table` DROP SYSTEM VERSIONING");
        $this->execute();

        return true;
    }

    /**
     * Check if a table has system versioning enabled
     *
     * @param   string  $table  Table name
     * @return  bool
     */
    public function hasSystemVersioning(string $table): bool
    {
        if (!$this->supportsSystemVersioning()) {
            return false;
        }

        $table = $this->replacePrefix($table);

        // Check for row_start and row_end columns which indicate system versioning
        $columns = $this->getTableColumns($table, false);

        foreach ($columns as $column) {
            if (isset($column->Extra) && stripos($column->Extra, 'GENERATED ALWAYS AS ROW') !== false) {
                return true;
            }
        }

        return false;
    }

    // =========================================================================
    // CHECK Constraints (Actually enforced in MariaDB, unlike MySQL)
    // =========================================================================

    /**
     * Add a CHECK constraint to a table
     *
     * Unlike MySQL, MariaDB actually enforces CHECK constraints.
     *
     * @param   string  $table       Table name
     * @param   string  $name        Constraint name
     * @param   string  $expression  Check expression (e.g., "age >= 0")
     * @return  bool
     */
    public function addCheckConstraint(string $table, string $name, string $expression): bool
    {
        $table = $this->replacePrefix($table);

        $this->setQuery("ALTER TABLE `$table` ADD CONSTRAINT `$name` CHECK ($expression)");
        $this->execute();

        return true;
    }

    /**
     * Drop a CHECK constraint from a table
     *
     * @param   string  $table  Table name
     * @param   string  $name   Constraint name
     * @return  bool
     */
    public function dropCheckConstraint(string $table, string $name): bool
    {
        $table = $this->replacePrefix($table);

        $this->setQuery("ALTER TABLE `$table` DROP CONSTRAINT `$name`");
        $this->execute();

        return true;
    }

    /**
     * Get all CHECK constraints for a table
     *
     * @param   string  $table  Table name
     * @return  array  Array of constraint objects with 'name' and 'expression' properties
     */
    public function getCheckConstraints(string $table): array
    {
        $table = $this->replacePrefix($table);

        $this->setQuery(
            "SELECT CONSTRAINT_NAME as name, CHECK_CLAUSE as expression " .
            "FROM information_schema.CHECK_CONSTRAINTS " .
            "WHERE CONSTRAINT_SCHEMA = DATABASE() " .
            "AND TABLE_NAME = " . $this->quote($table)
        );

        return $this->loadObjectList() ?: [];
    }

    // =========================================================================
    // Invisible Columns (MariaDB 10.3.3+)
    // =========================================================================

    /**
     * Check if invisible columns are supported (MariaDB 10.3.3+)
     *
     * @return  bool
     */
    public function supportsInvisibleColumns(): bool
    {
        $version = $this->getMajorVersion();
        return $version !== null && $version >= 10.3;
    }

    /**
     * Make a column invisible (excluded from SELECT *)
     *
     * @param   string  $table   Table name
     * @param   string  $column  Column name
     * @return  bool
     */
    public function makeColumnInvisible(string $table, string $column): bool
    {
        if (!$this->supportsInvisibleColumns()) {
            return false;
        }

        $table = $this->replacePrefix($table);

        // Get the current column definition
        $columns = $this->getTableColumns($table, false);
        if (!isset($columns[$column])) {
            return false;
        }

        $col = $columns[$column];
        $definition = $col->Type;

        if ($col->Null === 'NO') {
            $definition .= ' NOT NULL';
        }

        if ($col->Default !== null) {
            $definition .= ' DEFAULT ' . $this->quote($col->Default);
        }

        $definition .= ' INVISIBLE';

        $this->setQuery("ALTER TABLE `$table` MODIFY COLUMN `$column` $definition");
        $this->execute();

        return true;
    }

    /**
     * Make a column visible again
     *
     * @param   string  $table   Table name
     * @param   string  $column  Column name
     * @return  bool
     */
    public function makeColumnVisible(string $table, string $column): bool
    {
        if (!$this->supportsInvisibleColumns()) {
            return false;
        }

        $table = $this->replacePrefix($table);

        // Get the current column definition
        $columns = $this->getTableColumns($table, false);
        if (!isset($columns[$column])) {
            return false;
        }

        $col = $columns[$column];
        $definition = $col->Type;

        if ($col->Null === 'NO') {
            $definition .= ' NOT NULL';
        }

        if ($col->Default !== null) {
            $definition .= ' DEFAULT ' . $this->quote($col->Default);
        }

        // Explicitly set VISIBLE to remove INVISIBLE
        $definition .= ' VISIBLE';

        $this->setQuery("ALTER TABLE `$table` MODIFY COLUMN `$column` $definition");
        $this->execute();

        return true;
    }

    // =========================================================================
    // JSON Functions (MariaDB-specific variants)
    // =========================================================================

    /**
     * Get SQL for JSON_TABLE function (MariaDB 10.6+)
     *
     * Converts JSON data to a relational table format.
     *
     * @param   string  $jsonColumn  Column containing JSON data
     * @param   string  $path        JSON path expression
     * @param   array   $columns     Column definitions for output
     * @return  string  SQL for JSON_TABLE
     */
    public function sqlJsonTable(string $jsonColumn, string $path, array $columns): string
    {
        $columnDefs = [];
        foreach ($columns as $name => $definition) {
            $columnDefs[] = "`$name` $definition";
        }

        return "JSON_TABLE($jsonColumn, '$path' COLUMNS (" . implode(', ', $columnDefs) . "))";
    }

    // =========================================================================
    // Plugin Management
    // =========================================================================

    /**
     * Get list of installed plugins
     *
     * @return  array
     */
    public function getPlugins(): array
    {
        $this->setQuery("SHOW PLUGINS");
        return $this->loadObjectList() ?: [];
    }

    /**
     * Check if a plugin is installed and active
     *
     * @param   string  $name  Plugin name
     * @return  bool
     */
    public function hasPlugin(string $name): bool
    {
        $this->setQuery("SHOW PLUGINS");
        $plugins = $this->loadObjectList('Name');

        $name = strtoupper($name);

        if (!isset($plugins[$name])) {
            // Try lowercase
            $name = strtolower($name);
            if (!isset($plugins[$name])) {
                return false;
            }
        }

        return strtoupper($plugins[$name]->Status ?? '') === 'ACTIVE';
    }

    /**
     * Install a plugin using INSTALL SONAME
     *
     * MariaDB uses INSTALL SONAME which automatically registers all plugins
     * from the shared library. The $name parameter is ignored because MariaDB
     * determines plugin names from the library itself.
     *
     * @param   string  $name    Plugin name (ignored, kept for MySQL compatibility)
     * @param   string  $soname  Shared library name (e.g., 'ha_spider.so')
     * @return  bool
     */
    public function installPlugin(string $name, string $soname): bool
    {
        try {
            // MariaDB's INSTALL SONAME doesn't use the plugin name
            $this->setQuery("INSTALL SONAME " . $this->quote($soname));
            $this->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Install plugins from a shared library using INSTALL SONAME
     *
     * This is a MariaDB-specific method that installs all plugins from a
     * shared library without specifying individual plugin names.
     *
     * @param   string  $soname  Shared library name (e.g., 'ha_spider.so')
     * @return  bool
     */
    public function installSoname(string $soname): bool
    {
        try {
            $this->setQuery("INSTALL SONAME " . $this->quote($soname));
            $this->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Uninstall a plugin
     *
     * @param   string  $name  Plugin name
     * @return  bool
     */
    public function uninstallPlugin(string $name): bool
    {
        try {
            $this->setQuery("UNINSTALL PLUGIN " . $this->quoteName($name));
            $this->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Uninstall all plugins from a shared library using UNINSTALL SONAME
     *
     * This is a MariaDB-specific method that uninstalls all plugins from a
     * shared library at once.
     *
     * @param   string  $soname  Shared library name
     * @return  bool
     */
    public function uninstallSoname(string $soname): bool
    {
        try {
            $this->setQuery("UNINSTALL SONAME " . $this->quote($soname));
            $this->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // =========================================================================
    // MaxScale and Load Balancing Support
    // =========================================================================

    /**
     * Check if connected through MaxScale proxy
     *
     * MaxScale is MariaDB's intelligent database proxy for load balancing
     * and high availability.
     *
     * @return  bool
     */
    public function isMaxScaleConnection(): bool
    {
        try {
            // MaxScale sets specific session variables
            $this->setQuery("SELECT @@maxscale_version");
            $result = $this->loadResult();
            return !empty($result);
        } catch (\Exception $e) {
            return false;
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
