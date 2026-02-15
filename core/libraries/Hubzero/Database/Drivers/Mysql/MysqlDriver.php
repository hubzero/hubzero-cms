<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Mysql;

use Hubzero\Database\Drivers\Base\BaseSqlDriver;
use Hubzero\Database\Exception\ConnectionFailedException;
use Hubzero\Database\Exception\QueryFailedException;
use Hubzero\Database\Exception\UnsupportedEngineException;

/**
 * MySQL (PDO) database driver
 *
 * MySQL is the world's most popular open source relational database management
 * system. This driver provides MySQL-specific functionality including:
 *
 * Key MySQL-specific features:
 * - InnoDB storage engine (ACID-compliant, supports transactions)
 * - MyISAM storage engine (fast reads, full-text search)
 * - NDB Cluster storage engine (for MySQL Cluster)
 * - MEMORY storage engine (in-memory tables)
 * - MySQL Replication (master/slave replication status)
 * - Performance Schema (query analysis and optimization)
 * - Generated columns (virtual and stored, MySQL 5.7+)
 * - JSON functions (MySQL 5.7+)
 * - Window functions (MySQL 8.0+)
 * - CHECK constraints (actually enforced in MySQL 8.0.16+)
 * - Invisible columns (MySQL 8.0.23+)
 *
 * INHERITANCE:
 * This class extends Sql (the universal SQL base) which extends Pdo
 * (the connection layer). MariaDB and Percona drivers extend this class.
 *
 */
class MysqlDriver extends BaseSqlDriver
{
    /**
     * The name of the database driver
     *
     * @var string
     */
    protected $name = 'mysql';

    /**
     * MySQL uses backtick quoting for identifiers
     *
     * @var string
     */
    protected $wrapper = '`%s`';

    /**
     * The current transaction depth (for savepoint support)
     *
     * @var int
     */
    protected $transactionDepth = 0;

    /**
     * Whether the _sequences emulation table has been verified/created
     *
     * @var bool
     */
    private $sequenceTableReady = false;

    /**
     * Constructs a new database object based on the given params
     *
     * @param   array  $options  The database connection params
     * @return  void
     * @throws  ConnectionFailedException  If the DSN is invalid or connection fails
     */
    public function __construct($options)
    {
        if (!isset($options['extras'])) {
            $options['extras'] = [];
        }

        if (isset($options['ssl_ca']) && $options['ssl_ca'] && ($options['host'] ?? '') != 'localhost') {
            $options['extras'][\PDO::MYSQL_ATTR_SSL_CA] = $options['ssl_ca'];
        }

        if (!isset($options['dsn'])) {
            $options['dsn']  = "mysql:host={$options['host']};charset=utf8";
            if (isset($options['port'])) {
                $options['dsn'] .= ";port={$options['port']}";
            }
            $options['dsn'] .= (isset($options['database']) &&
                $options['database']) ? ";dbname={$options['database']}" : '';
        }

        if (substr($options['dsn'], 0, 6) != 'mysql:') {
            throw new ConnectionFailedException('MySQL DSN for PDO connection does not appear to be valid.', 500);
        }

        parent::__construct($options);
    }

    /**
     * Set the database engine of the given table
     *
     * MySQL supports several storage engines, each with different characteristics:
     * - InnoDB: ACID-compliant, supports transactions and foreign keys (default)
     * - MyISAM: Fast reads, full-text search, no transactions
     * - MEMORY: In-memory storage for temporary tables
     * - ARCHIVE: Compressed storage for historical data
     * - CSV: Stores data in comma-separated values format
     * - MERGE: Collection of identical MyISAM tables
     * - FEDERATED: Access remote MySQL tables
     * - NDB: For MySQL Cluster deployments
     * - BLACKHOLE: Accepts data but stores nothing (for replication filtering)
     *
     * @param   string  $table   The table for which to set the engine type
     * @param   string  $engine  The engine type to set
     * @return  bool
     * @throws  UnsupportedEngineException  If the specified engine is not supported
     **/
    public function setEngine($table, $engine)
    {
        $supported = [
            // Standard MySQL engines
            'innodb',       // Default, ACID-compliant, transactions, foreign keys
            'myisam',       // Fast reads, full-text search, no transactions
            'memory',       // In-memory tables, temporary data
            'archive',      // Compressed, append-only storage
            'csv',          // Comma-separated values storage
            'merge',        // Collection of identical MyISAM tables
            'federated',    // Access remote MySQL tables
            'blackhole',    // Accepts data but stores nothing
            // MySQL Cluster engine
            'ndb',          // Network Database for MySQL Cluster
            'ndbcluster',   // Alias for NDB
        ];

        $engineLower = strtolower($engine);

        // Map ndbcluster to ndb
        if ($engineLower === 'ndbcluster') {
            $engineLower = 'ndb';
            $engine = 'NDB';
        }

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
     *                  - 'mysql_version': Alias for driver_version (deprecated, use driver_version)
     *                  - 'comment': Version comment/description
     */
    public function getServerInfo()
    {
        $this->setQuery("SHOW VARIABLES LIKE '%version%'");
        $rows = $this->loadObjectList('Variable_name');

        $version = $rows['version']->Value ?? null;
        $comment = $rows['version_comment']->Value ?? null;
        $driverVersion = $this->extractDriverVersionFromString($version, true);

        return [
            'version'        => $version,
            'driver_version' => $driverVersion,  // Standard key for all drivers
            'mysql_version'  => $driverVersion,  // Deprecated alias for backwards compatibility
            'comment'        => $comment,
        ];
    }

    /**
     * Format a boolean value as a SQL literal
     *
     * MySQL has no native boolean type; uses TINYINT(1) with 1/0 values.
     *
     * @param   bool  $value  The boolean value
     * @return  string  The SQL literal
     */
    public function formatBooleanLiteral(bool $value): string
    {
        return $value ? '1' : '0';
    }

    /**
     * Apply column modifiers for MySQL
     *
     * Extends the base to support integer display widths (e.g., INT(11)).
     *
     * @param   string  $abstractType  The abstract type name
     * @param   string  $nativeType    The mapped MySQL type
     * @param   array   $modifiers     Column modifiers
     * @return  string  The type with modifiers applied
     */
    protected function applyColumnModifiers(
        string $abstractType,
        string $nativeType,
        array $modifiers
    ): string {
        // MySQL supports display width on integer types
        $integerTypes = [
            'tinyInteger', 'smallInteger',
            'mediumInteger', 'integer', 'bigInteger',
        ];
        if (
            isset($modifiers['length'])
            && in_array($abstractType, $integerTypes)
        ) {
            return "{$nativeType}({$modifiers['length']})";
        }

        return parent::applyColumnModifiers(
            $abstractType,
            $nativeType,
            $modifiers
        );
    }

    /**
     * Build a MySQL-specific column definition for ALTER TABLE
     *
     * MySQL supports additional features beyond standard SQL:
     * - UNSIGNED modifier for numeric types
     * - AUTO_INCREMENT for auto-incrementing columns
     * - COMMENT for column documentation
     * - AFTER and FIRST for column positioning
     *
     * @param   string  $name        The column name
     * @param   array   $definition  The column definition
     * @return  string  The SQL column definition string
     */
    public function buildAlterColumnDefinition(string $name, array $definition): string
    {
        $type = $definition['type'];
        $modifiers = $definition['modifiers'] ?? [];

        // Normalize abstract types (like 'string') to MySQL types (like 'VARCHAR(255)')
        $type = $this->normalizeColumnType($type, $modifiers);

        // Detect and normalize AUTO_INCREMENT
        $hasAutoIncrement = !empty($modifiers['autoIncrement']) || stripos($type, 'AUTO_INCREMENT') !== false;
        if ($hasAutoIncrement) {
            $type = preg_replace('/\s*AUTO_INCREMENT\s*/i', ' ', $type);
            $type = trim($type);
        }

        // Handle UNSIGNED modifier
        $hasUnsigned = !empty($modifiers['unsigned']) || stripos($type, 'UNSIGNED') !== false;
        if ($hasUnsigned && stripos($type, 'UNSIGNED') === false) {
            $type .= ' UNSIGNED';
        }

        $parts = ['`' . $name . '`', $type];

        // Translate zero-date default to NULL
        if (
            array_key_exists('default', $modifiers)
            && self::isZeroDate($modifiers['default'])
        ) {
            $modifiers['nullable'] = true;
            $modifiers['default'] = null;
        }

        // NULL / NOT NULL
        if (isset($modifiers['nullable'])) {
            $parts[] = $modifiers['nullable'] ? 'NULL' : 'NOT NULL';
        }

        // AUTO_INCREMENT
        if ($hasAutoIncrement) {
            $parts[] = 'AUTO_INCREMENT';
        }

        // DEFAULT value
        if (array_key_exists('default', $modifiers)) {
            $default = $modifiers['default'];
            if ($default === null) {
                $parts[] = 'DEFAULT NULL';
            } elseif (is_bool($default)) {
                $parts[] = 'DEFAULT ' . ($default ? '1' : '0');
            } elseif (is_numeric($default)) {
                $parts[] = 'DEFAULT ' . $default;
            } elseif ($default === 'CURRENT_TIMESTAMP') {
                $parts[] = 'DEFAULT CURRENT_TIMESTAMP';
            } else {
                $parts[] = "DEFAULT '" . addslashes($default) . "'";
            }
        }

        // COMMENT (MySQL-specific)
        if (isset($modifiers['comment'])) {
            $parts[] = "COMMENT '" . addslashes($modifiers['comment']) . "'";
        }

        // AFTER (MySQL-specific positioning)
        if (isset($modifiers['after'])) {
            $parts[] = "AFTER `{$modifiers['after']}`";
        }

        // FIRST (MySQL-specific positioning)
        if (!empty($modifiers['first'])) {
            $parts[] = 'FIRST';
        }

        return implode(' ', $parts);
    }

    /**
     * Check if this database supports length parameters for integer types
     *
     * @return  bool
     */
    public function supportsIntegerLength(): bool
    {
        return true;
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

        // Engine names in MySQL are case-insensitive but returned with specific casing
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
     * Resolve a requested storage engine to a supported one
     *
     * If the requested engine is not supported, return the default engine
     * (or the first supported engine) to avoid errors.
     *
     * @param   string|null  $engine  Requested engine
     * @return  string|null  Supported engine or null if none available
     */
    protected function resolveEngine(?string $engine): ?string
    {
        if ($engine === null || trim($engine) === '') {
            return null;
        }

        $this->setQuery("SHOW ENGINES");
        $engines = $this->loadObjectList('Engine');

        if (!is_array($engines) || empty($engines)) {
            return null;
        }

        $requested = strtoupper(trim($engine));
        if (isset($engines[$requested])) {
            $support = strtoupper($engines[$requested]->Support ?? '');
            if (in_array($support, ['DEFAULT', 'YES'], true)) {
                return $engines[$requested]->Engine;
            }
        }

        foreach ($engines as $row) {
            $support = strtoupper($row->Support ?? '');
            if ($support === 'DEFAULT') {
                return $row->Engine;
            }
        }

        foreach ($engines as $row) {
            $support = strtoupper($row->Support ?? '');
            if ($support === 'YES') {
                return $row->Engine;
            }
        }

        return null;
    }

    // =========================================================================
    // MySQL Replication Support
    // =========================================================================

    /**
     * Get MySQL replication status
     *
     * Returns information about the replication status when running
     * as a master or slave in a replication topology.
     *
     * @return  array|null  Replication status or null if not replicating
     */
    public function getReplicationStatus()
    {
        try {
            // Check slave status first
            $this->setQuery("SHOW SLAVE STATUS");
            $slaveStatus = $this->loadAssoc();

            if ($slaveStatus) {
                return [
                    'role'                => 'slave',
                    'master_host'         => $slaveStatus['Master_Host'] ?? null,
                    'master_port'         => $slaveStatus['Master_Port'] ?? null,
                    'slave_io_running'    => ($slaveStatus['Slave_IO_Running'] ?? '') === 'Yes',
                    'slave_sql_running'   => ($slaveStatus['Slave_SQL_Running'] ?? '') === 'Yes',
                    'seconds_behind'      => isset($slaveStatus['Seconds_Behind_Master'])
                        ? (int) $slaveStatus['Seconds_Behind_Master'] : null,
                    'last_error'          => $slaveStatus['Last_Error'] ?? null,
                    'relay_log_file'      => $slaveStatus['Relay_Log_File'] ?? null,
                    'relay_log_pos'       => $slaveStatus['Relay_Log_Pos'] ?? null,
                ];
            }

            // Check master status
            $this->setQuery("SHOW MASTER STATUS");
            $masterStatus = $this->loadAssoc();

            if ($masterStatus) {
                return [
                    'role'            => 'master',
                    'file'            => $masterStatus['File'] ?? null,
                    'position'        => $masterStatus['Position'] ?? null,
                    'binlog_do_db'    => $masterStatus['Binlog_Do_DB'] ?? null,
                    'binlog_ignore_db' => $masterStatus['Binlog_Ignore_DB'] ?? null,
                ];
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check if the server is a replication master
     *
     * @return  bool
     */
    public function isMasterServer()
    {
        $status = $this->getReplicationStatus();
        return $status !== null && ($status['role'] ?? '') === 'master';
    }

    /**
     * Check if the server is a replication slave
     *
     * @return  bool
     */
    public function isSlaveServer()
    {
        $status = $this->getReplicationStatus();
        return $status !== null && ($status['role'] ?? '') === 'slave';
    }

    /**
     * Get list of connected slave servers (when running as master)
     *
     * @return  array
     */
    public function getSlaveHosts()
    {
        try {
            $this->setQuery("SHOW SLAVE HOSTS");
            return $this->loadObjectList() ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    // =========================================================================
    // InnoDB-Specific Features
    // =========================================================================

    /**
     * Get InnoDB engine status information
     *
     * Returns detailed information about InnoDB internals including
     * buffer pool, transactions, file I/O, and more.
     *
     * @return  string|null  InnoDB status text or null if unavailable
     */
    public function getInnodbStatus()
    {
        try {
            $this->setQuery("SHOW ENGINE INNODB STATUS");
            $result = $this->loadAssoc();
            return $result['Status'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get InnoDB buffer pool statistics
     *
     * The buffer pool is InnoDB's main memory cache for data and indexes.
     *
     * @return  array
     */
    public function getInnodbBufferPoolStats()
    {
        try {
            $this->setQuery("SHOW STATUS LIKE 'Innodb_buffer_pool%'");
            $rows = $this->loadObjectList('Variable_name');

            return [
                'size'              => (int) ($rows['Innodb_buffer_pool_pages_total']->Value ?? 0),
                'free'              => (int) ($rows['Innodb_buffer_pool_pages_free']->Value ?? 0),
                'dirty'             => (int) ($rows['Innodb_buffer_pool_pages_dirty']->Value ?? 0),
                'data'              => (int) ($rows['Innodb_buffer_pool_pages_data']->Value ?? 0),
                'read_requests'     => (int) ($rows['Innodb_buffer_pool_read_requests']->Value ?? 0),
                'reads'             => (int) ($rows['Innodb_buffer_pool_reads']->Value ?? 0),
                'write_requests'    => (int) ($rows['Innodb_buffer_pool_write_requests']->Value ?? 0),
                'bytes_data'        => (int) ($rows['Innodb_buffer_pool_bytes_data']->Value ?? 0),
                'bytes_dirty'       => (int) ($rows['Innodb_buffer_pool_bytes_dirty']->Value ?? 0),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Calculate InnoDB buffer pool hit ratio
     *
     * A healthy database should have a hit ratio above 99%.
     *
     * @return  float|null  Hit ratio as percentage (0-100) or null if unavailable
     */
    public function getInnodbBufferPoolHitRatio()
    {
        $stats = $this->getInnodbBufferPoolStats();

        $requests = $stats['read_requests'] ?? 0;
        $reads = $stats['reads'] ?? 0;

        if ($requests === 0) {
            return null;
        }

        return (($requests - $reads) / $requests) * 100;
    }

    // =========================================================================
    // Performance Schema
    // =========================================================================

    /**
     * Check if Performance Schema is enabled
     *
     * @return  bool
     */
    public function isPerformanceSchemaEnabled()
    {
        try {
            $this->setQuery("SHOW VARIABLES LIKE 'performance_schema'");
            $result = $this->loadObject();
            return $result && strtoupper($result->Value ?? '') === 'ON';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get slow queries from Performance Schema
     *
     * Requires Performance Schema to be enabled.
     *
     * @param   int  $limit  Maximum number of queries to return
     * @return  array
     */
    public function getSlowQueries($limit = 10)
    {
        if (!$this->isPerformanceSchemaEnabled()) {
            return [];
        }

        try {
            $this->setQuery(
                "SELECT DIGEST_TEXT as query, " .
                "COUNT_STAR as exec_count, " .
                "SUM_TIMER_WAIT/1000000000000 as total_time_sec, " .
                "AVG_TIMER_WAIT/1000000000000 as avg_time_sec, " .
                "SUM_ROWS_EXAMINED as rows_examined, " .
                "SUM_ROWS_SENT as rows_sent " .
                "FROM performance_schema.events_statements_summary_by_digest " .
                "ORDER BY SUM_TIMER_WAIT DESC " .
                "LIMIT " . (int) $limit
            );
            return $this->loadObjectList() ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get table I/O statistics from Performance Schema
     *
     * @param   string  $table  Table name (optional, returns all if not specified)
     * @return  array
     */
    public function getTableIoStats($table = null)
    {
        if (!$this->isPerformanceSchemaEnabled()) {
            return [];
        }

        try {
            $sql = "SELECT OBJECT_SCHEMA as db, OBJECT_NAME as table_name, " .
                   "COUNT_READ as reads, COUNT_WRITE as writes, " .
                   "SUM_TIMER_READ/1000000000000 as read_time_sec, " .
                   "SUM_TIMER_WRITE/1000000000000 as write_time_sec " .
                   "FROM performance_schema.table_io_waits_summary_by_table " .
                   "WHERE OBJECT_SCHEMA NOT IN ('mysql', 'performance_schema', 'information_schema', 'sys')";

            if ($table) {
                $table = $this->replacePrefix($table);
                $sql .= " AND OBJECT_NAME = " . $this->quote($table);
            }

            $sql .= " ORDER BY SUM_TIMER_WAIT DESC";

            $this->setQuery($sql);
            return $this->loadObjectList() ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    // =========================================================================
    // Generated Columns (MySQL 5.7+)
    // =========================================================================

    /**
     * Check if generated columns are supported (MySQL 5.7+)
     *
     * @return  bool
     */
    public function supportsGeneratedColumns(): bool
    {
        $version = $this->getMajorVersion();
        return $version !== null && $version >= 5.7;
    }

    /**
     * Add a virtual generated column to a table
     *
     * Virtual columns are computed on-the-fly when read.
     * They don't consume storage but can't be indexed (except in MySQL 8.0.13+).
     *
     * @param   string       $table       Table name
     * @param   string       $column      Column name
     * @param   string       $expression  SQL expression for the generated value
     * @param   string|null  $type        Column data type (e.g., 'INT', 'VARCHAR(255)')
     * @return  bool
     */
    public function addVirtualColumn(string $table, string $column, string $expression, ?string $type = null): bool
    {
        if (!$this->supportsGeneratedColumns()) {
            return false;
        }

        // MySQL requires a type for generated columns
        if (empty($type)) {
            throw new \InvalidArgumentException('MySQL generated columns require a data type');
        }

        $table = $this->replacePrefix($table);

        $this->setQuery(
            "ALTER TABLE `$table` ADD COLUMN `$column` $type " .
            "GENERATED ALWAYS AS ($expression) VIRTUAL"
        );
        $this->execute();

        return true;
    }

    /**
     * Add a stored generated column to a table
     *
     * Stored columns are computed when data is inserted/updated.
     * They consume storage but can be indexed.
     *
     * @param   string       $table       Table name
     * @param   string       $column      Column name
     * @param   string       $expression  SQL expression for the generated value
     * @param   string|null  $type        Column data type (e.g., 'INT', 'VARCHAR(255)')
     * @return  bool
     */
    public function addStoredColumn(string $table, string $column, string $expression, ?string $type = null): bool
    {
        if (!$this->supportsGeneratedColumns()) {
            return false;
        }

        // MySQL requires a type for generated columns
        if (empty($type)) {
            // For stability, we might defaulting or throwing - strictly throwing is safer
            // But to comply with signature, we accept null, but then fail logic
            throw new \InvalidArgumentException('MySQL generated columns require a data type');
        }

        $table = $this->replacePrefix($table);

        $this->setQuery(
            "ALTER TABLE `$table` ADD COLUMN `$column` $type " .
            "GENERATED ALWAYS AS ($expression) STORED"
        );
        $this->execute();

        return true;
    }

    // =========================================================================
    // CHECK Constraints (MySQL 8.0.16+)
    // =========================================================================

    /**
     * Check if CHECK constraints are supported and enforced (MySQL 8.0.16+)
     *
     * Prior to MySQL 8.0.16, CHECK constraints were parsed but ignored.
     *
     * @return  bool
     */
    public function supportsCheckConstraints()
    {
        $version = $this->getMajorVersion();
        if ($version === null || $version < 8.0) {
            return false;
        }

        // Need to check patch version for 8.0.16+
        $info = $this->getServerInfo();
        $fullVersion = $info['mysql_version'] ?? '';

        if (preg_match('/^8\.0\.(\d+)/', $fullVersion, $matches)) {
            return (int) $matches[1] >= 16;
        }

        // MySQL 8.1+ definitely supports it
        return $version >= 8.1;
    }

    /**
     * Add a CHECK constraint to a table
     *
     * Only works on MySQL 8.0.16+. On earlier versions, returns false.
     *
     * @param   string  $table       Table name
     * @param   string  $name        Constraint name
     * @param   string  $expression  Check expression (e.g., "age >= 0")
     * @return  bool
     */
    public function addCheckConstraint(string $table, string $name, string $expression): bool
    {
        if (!$this->supportsCheckConstraints()) {
            return false;
        }

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
        if (!$this->supportsCheckConstraints()) {
            return false;
        }

        $table = $this->replacePrefix($table);

        $this->setQuery("ALTER TABLE `$table` DROP CHECK `$name`");
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
        if (!$this->supportsCheckConstraints()) {
            return [];
        }

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
    // Invisible Columns (MySQL 8.0.23+)
    // =========================================================================

    /**
     * Check if invisible columns are supported (MySQL 8.0.23+)
     *
     * @return  bool
     */
    public function supportsInvisibleColumns(): bool
    {
        $version = $this->getMajorVersion();
        if ($version === null || $version < 8.0) {
            return false;
        }

        // Need to check patch version for 8.0.23+
        $info = $this->getServerInfo();
        $fullVersion = $info['mysql_version'] ?? '';

        if (preg_match('/^8\.0\.(\d+)/', $fullVersion, $matches)) {
            return (int) $matches[1] >= 23;
        }

        // MySQL 8.1+ definitely supports it
        return $version >= 8.1;
    }

    /**
     * Make a column invisible (excluded from SELECT *)
     *
     * Invisible columns are not included in SELECT * queries but can
     * still be explicitly selected.
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
    // JSON Functions (MySQL 5.7+)
    // =========================================================================

    /**
     * Check if JSON functions are supported (MySQL 5.7+)
     *
     * @return  bool
     */
    public function supportsJson(): bool
    {
        $version = $this->getMajorVersion();
        return $version !== null && $version >= 5.7;
    }

    /**
     * Get SQL for JSON_TABLE function (MySQL 8.0+)
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
    // Window Functions (MySQL 8.0+)
    // =========================================================================

    /**
     * Check if window functions are supported (MySQL 8.0+)
     *
     * @return  bool
     */
    public function supportsWindowFunctions(): bool
    {
        $version = $this->getMajorVersion();
        return $version !== null && $version >= 8.0;
    }

    /**
     * Check if Common Table Expressions (CTEs) are supported (MySQL 8.0+)
     *
     * @return  bool
     */
    public function supportsCTE(): bool
    {
        $version = $this->getMajorVersion();
        return $version !== null && $version >= 8.0;
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

        if (!isset($plugins[$name])) {
            return false;
        }

        return strtoupper($plugins[$name]->Status ?? '') === 'ACTIVE';
    }

    /**
     * Install a plugin
     *
     * @param   string  $name    Plugin name
     * @param   string  $soname  Shared library name (e.g., 'auth_socket.so')
     * @return  bool
     */
    public function installPlugin(string $name, string $soname): bool
    {
        try {
            $this->setQuery("INSTALL PLUGIN " . $this->quoteName($name) . " SONAME " . $this->quote($soname));
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

    // =========================================================================
    // Global Status and Variables
    // =========================================================================

    /**
     * Get MySQL global status variables
     *
     * @param   string  $like  Optional LIKE pattern to filter variables
     * @return  array
     */
    public function getGlobalStatus($like = null): array
    {
        $sql = "SHOW GLOBAL STATUS";
        if ($like) {
            $sql .= " LIKE " . $this->quote($like);
        }

        $this->setQuery($sql);
        return $this->loadObjectList('Variable_name') ?: [];
    }

    /**
     * Get MySQL global variables
     *
     * @param   string  $like  Optional LIKE pattern to filter variables
     * @return  array
     */
    public function getGlobalVariables($like = null): array
    {
        $sql = "SHOW GLOBAL VARIABLES";
        if ($like) {
            $sql .= " LIKE " . $this->quote($like);
        }

        $this->setQuery($sql);
        return $this->loadObjectList('Variable_name') ?: [];
    }

    /**
     * Get current connection information
     *
     * @return  array
     */
    public function getConnectionInfo(): array
    {
        $this->setQuery("SELECT CONNECTION_ID() as id, USER() as user, DATABASE() as db");
        $result = $this->loadAssoc();

        return [
            'connection_id' => $result['id'] ?? null,
            'user'          => $result['user'] ?? null,
            'database'      => $result['db'] ?? null,
        ];
    }

    /**
     * Get MySQL uptime in seconds
     *
     * @return  int
     */
    public function getUptime(): int
    {
        $this->setQuery("SHOW GLOBAL STATUS LIKE 'Uptime'");
        $result = $this->loadObject();
        return (int) ($result->Value ?? 0);
    }

    /**
     * Get current number of connections
     *
     * @return  array  Array with 'current', 'max', and 'max_used' keys
     */
    public function getConnectionStats(): array
    {
        $status = $this->getGlobalStatus('Threads_connected');
        $maxUsed = $this->getGlobalStatus('Max_used_connections');
        $variables = $this->getGlobalVariables('max_connections');

        return [
            'current'  => (int) ($status['Threads_connected']->Value ?? 0),
            'max'      => (int) ($variables['max_connections']->Value ?? 0),
            'max_used' => (int) ($maxUsed['Max_used_connections']->Value ?? 0),
        ];
    }

    // =========================================================================
    // SQL Abstract Method Implementations
    // These implement the Sql base class contract
    // =========================================================================


    /**
     * Returns the SQL keyword for INSERT with ignore duplicates
     *
     * @return  string
     */
    public function sqlInsertIgnore(): string
    {
        return 'INSERT IGNORE INTO';
    }

    /**
     * Returns the SQL keyword for REPLACE (upsert)
     *
     * @return  string
     */
    public function sqlReplace(): string
    {
        return 'REPLACE INTO';
    }

    // =========================================================================
    // Schema Introspection Methods (MySQL implementation)
    // =========================================================================

    /**
     * Gets the database collation in use
     *
     * @return  string|bool
     */
    public function getCollation()
    {
        return $this->getCollationFromShowVariables();
    }

    /**
     * Shows the table CREATE statement that creates the given tables
     *
     * @param   string|array  $tables  A table name or a list of table names
     * @return  array
     */
    public function getTableCreate($tables)
    {
        return $this->getTableCreateFromShowCreate($tables, true, 'table');
    }

    /**
     * Retrieves field information about the given table
     *
     * @param   string  $table     The name of the database table
     * @param   bool    $typeOnly  True (default) to only return field types
     * @return  array
     */
    public function getTableColumns($table, $typeOnly = true)
    {
        return $this->getTableColumnsFromShowQuery(
            $this->buildShowColumnsQueryForDriver((string) $table, true, true, 'COLUMNS'),
            (bool) $typeOnly
        );
    }

    /**
     * Retrieves key information about the given tables
     *
     * @param   string|array  $tables  A table name or a list of table names
     * @return  array
     */
    public function getTableKeys($table)
    {
        return $this->getTableKeysFromShowKeys((string) $table, 'Key_name');
    }

    /**
     * Gets index information for a table
     *
     * This is an alias for getTableKeys() with a normalized return format.
     * Provided for consistency with PostgreSQL and SQLite drivers.
     *
     * @param   string  $table  The table name
     * @return  array   Array of index objects with 'name', 'columns', 'unique' keys
     */
    public function getIndexes($table)
    {
        return $this->normalizeIndexesFromTableKeys(
            $this->getTableKeys($table)
        );
    }

    /**
     * Gets foreign key constraints for a table
     *
     * Returns an array of foreign key constraint objects, each containing:
     * - name: The constraint name
     * - columns: Array of local column names
     * - foreign_table: The referenced table name
     * - foreign_columns: Array of referenced column names
     * - on_update: The ON UPDATE action
     * - on_delete: The ON DELETE action
     *
     * @param   string  $table  The table name
     * @return  array   Array of foreign key constraint objects
     */
    public function getForeignKeys($table)
    {
        $table = $this->replacePrefix($table);
        $database = $this->getDatabase();

        $sql = "SELECT
                    kcu.CONSTRAINT_NAME,
                    kcu.COLUMN_NAME,
                    kcu.REFERENCED_TABLE_NAME,
                    kcu.REFERENCED_COLUMN_NAME,
                    rc.UPDATE_RULE,
                    rc.DELETE_RULE
                FROM information_schema.KEY_COLUMN_USAGE kcu
                JOIN information_schema.REFERENTIAL_CONSTRAINTS rc
                    ON kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
                    AND kcu.CONSTRAINT_SCHEMA = rc.CONSTRAINT_SCHEMA
                WHERE kcu.TABLE_SCHEMA = " . $this->quote($database) . "
                    AND kcu.TABLE_NAME = " . $this->quote($table) . "
                    AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
                ORDER BY kcu.CONSTRAINT_NAME, kcu.ORDINAL_POSITION";

        $this->setQuery($sql);

        return $this->groupForeignKeyRows($this->loadObjectList(), [
            'constraint_name' => 'CONSTRAINT_NAME',
            'column_name'     => 'COLUMN_NAME',
            'foreign_table'   => 'REFERENCED_TABLE_NAME',
            'foreign_column'  => 'REFERENCED_COLUMN_NAME',
            'on_update'       => 'UPDATE_RULE',
            'on_delete'       => 'DELETE_RULE',
        ]);
    }

    /**
     * Gets an array of all tables in the database
     *
     * @return  array
     */
    public function getTableList()
    {
        return $this->getTableListFromShowTables();
    }

    /**
     * Locks a table in the database
     *
     * @param   string  $tableName  The name of the table to lock
     * @return  $this
     */
    public function lockTable($table)
    {
        $this->lockTableForWrite((string) $table);

        return $this;
    }

    /**
     * Renames a table in the database
     *
     * @param   string  $oldTable  The name of the table to be renamed
     * @param   string  $newTable  The new name for the table
     * @param   string  $backup    Table prefix
     * @param   string  $prefix    For the table - used to rename constraints in non-mysql databases
     * @return  $this
     */
    public function renameTable($oldTable, $newTable, $backup = null, $prefix = null)
    {
        $this->renameTableSimple((string) $oldTable, (string) $newTable);

        return $this;
    }

    /**
     * Commits a transaction
     *
     * Supports nested transactions via savepoints.
     *
     * @return  void
     */
    public function transactionCommit()
    {
        $this->transactionCommitWithSavepoints(true);
    }

    /**
     * Rolls back a transaction
     *
     * Supports nested transactions via savepoints.
     *
     * @return  void
     */
    public function transactionRollback()
    {
        $this->transactionRollbackWithSavepoints();
    }

    /**
     * Initializes a transaction
     *
     * Supports nested transactions via savepoints.
     *
     * @return  void
     */
    public function transactionStart()
    {
        $this->transactionStartWithSavepoints();
    }

    /**
     * Unlocks all tables in the database
     *
     * @return  $this
     */
    public function unlockTables()
    {
        $this->unlockAllTables();

        return $this;
    }

    /**
     * Checks for the existance of a table
     *
     * @param   string  $table  The table we're looking for
     * @return  bool
     */
    public function tableExists($table)
    {
        return $this->tableExistsFromShowTablesLike((string) $table);
    }

    /**
     * Returns whether or not the given table has a given field
     *
     * @param   string  $table  A table name
     * @param   string  $field  A field name
     * @return  bool
     */
    public function tableHasField($table, $field)
    {
        return $this->tableHasFieldFromShowQuery(
            $this->buildShowColumnsQueryForDriver((string) $table, false, true, 'FIELDS'),
            (string) $field
        );
    }

    /**
     * Gets the primary key of a table
     *
     * @return  string
     **/
    public function getPrimaryKey($table)
    {
        return $this->getPrimaryKeyFromTableKeys(
            $this->getTableKeys($table),
            'PRIMARY'
        );
    }

    /**
     * Get primary key column names
     *
     * @param   string  $table  The table name
     * @return  array
     */
    public function getPrimaryKeyColumns($table): array
    {
        $this->setQuery(
            'SHOW KEYS FROM ' . $this->quoteName($table) .
            " WHERE Key_name = 'PRIMARY'"
        );
        return $this->extractPrimaryKeyColumnsFromTableKeyRows($this->loadObjectList());
    }

    /**
     * Gets the database engine of the given table
     *
     * @param   string       $table  The table for which to retrieve the engine type
     * @return  string|bool
     **/
    public function getEngine($table)
    {
        return $this->getEngineFromShowTableStatus($table);
    }

    /**
     * Gets the database character set of the given table
     *
     * @param   string       $table  The table for which to retrieve the character set
     * @param   string       $field  The field to check (optional)
     * @return  string|bool
     **/
    public function getCharacterSet($table, $field = null)
    {
        return $this->parseCharacterSetFromCreate(
            $this->getTableCreate($table),
            (string) $table,
            $field !== null ? (string) $field : null
        );
    }

    /**
     * Converts a table to the specified character set
     *
     * This converts all text columns in the table to the new character set.
     *
     * @param   string       $table    The table to convert
     * @param   string       $charset  The character set (e.g., 'utf8', 'utf8mb4')
     * @param   string|null  $collate  Optional collation (e.g., 'utf8mb4_unicode_ci')
     * @return  bool
     **/
    public function convertToCharset($table, $charset, $collate = null)
    {
        return $this->convertToCharsetUsingAlter(
            (string) $table,
            (string) $charset,
            $collate !== null ? (string) $collate : null
        );
    }

    /**
     * Gets the auto-increment value for the given table
     *
     * @param   string    $table  The table for which to retrieve the character set
     * @return  int|bool
     **/
    public function getAutoIncrement($table)
    {
        $create = $this->getTableCreate($table);

        preg_match('/AUTO_INCREMENT=([0-9]*)/', $create[$table], $matches);

        return (isset($matches[1])) ? $matches[1] : false;
    }

    /**
     * Sets the auto-increment starting value for the given table
     *
     * On InnoDB (MySQL 8+ default), ALTER TABLE AUTO_INCREMENT = N is silently
     * ignored when N is less than the internal counter. The counter persists in
     * the redo log even after DELETE FROM, so a simple ALTER TABLE cannot reset
     * it. On MyISAM, ALTER TABLE always works.
     *
     * To normalize behavior across storage engines: when the table is empty,
     * TRUNCATE TABLE is used instead — it reliably resets the InnoDB counter.
     * FK checks are temporarily disabled since TRUNCATE cannot operate on
     * tables referenced by foreign key constraints.
     *
     * @param   string  $table  The table name
     * @param   int     $value  The auto-increment starting value
     * @return  bool
     **/
    public function setAutoIncrement($table, $value): bool
    {
        $table = $this->replacePrefix($table);
        $value = max(1, (int) $value); // MySQL treats 0 as 1

        // Check if table is empty
        $this->setQuery("SELECT COUNT(*) FROM `$table`");
        $count = (int) $this->loadResult();

        if ($count === 0) {
            // TRUNCATE reliably resets the InnoDB auto-increment counter
            $this->setQuery("SET FOREIGN_KEY_CHECKS = 0")->execute();
            $this->setQuery("TRUNCATE TABLE `$table`")->execute();
            $this->setQuery("SET FOREIGN_KEY_CHECKS = 1")->execute();

            if ($value > 1) {
                $this->setQuery("ALTER TABLE `$table` AUTO_INCREMENT = $value");
                $this->execute();
            }
        } else {
            // Table has data — ALTER TABLE works when value > MAX(id)
            $this->setQuery("ALTER TABLE `$table` AUTO_INCREMENT = $value");
            $this->execute();
        }

        return true;
    }

    /**
     * Get the allowed values for an ENUM column
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @return  array   Array of allowed values, empty array if not an ENUM
     **/
    public function getEnumValues($table, $column)
    {
        $table = $this->replacePrefix($table);

        $this->setQuery("SHOW COLUMNS FROM `$table` WHERE Field = " . $this->quote($column));
        $result = $this->loadAssoc();

        if (!$result || empty($result['Type'])) {
            return [];
        }

        // Parse ENUM('val1','val2',...) format
        if (preg_match("/^enum\('(.*)'\)$/i", $result['Type'], $matches)) {
            return explode("','", $matches[1]);
        }

        return [];
    }

    /**
     * Add a value to an ENUM column
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @param   string  $value   The value to add
     * @return  bool
     **/
    public function addEnumValue($table, $column, $value)
    {
        $currentValues = $this->getEnumValues($table, $column);

        // Not an ENUM column or already has the value
        if (empty($currentValues) || in_array($value, $currentValues)) {
            return true;
        }

        $currentValues[] = $value;
        $enumDef = "ENUM('" . implode("','", $currentValues) . "')";

        return $this->modifyColumn($table, $column, $enumDef);
    }

    /**
     * Remove a value from an ENUM column
     *
     * Warning: Existing rows with this value will become invalid.
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @param   string  $value   The value to remove
     * @return  bool
     **/
    public function removeEnumValue($table, $column, $value)
    {
        $currentValues = $this->getEnumValues($table, $column);

        // Not an ENUM column or doesn't have the value
        if (empty($currentValues) || !in_array($value, $currentValues)) {
            return true;
        }

        $currentValues = array_filter($currentValues, function ($v) use ($value) {
            return $v !== $value;
        });

        if (empty($currentValues)) {
            return false; // Can't have an empty ENUM
        }

        $enumDef = "ENUM('" . implode("','", array_values($currentValues)) . "')";

        return $this->modifyColumn($table, $column, $enumDef);
    }

    /**
     * Creates or replaces a database view
     *
     * @param   string  $name       The view name (with or without prefix)
     * @param   string  $selectSql  The SELECT statement for the view (prefixes will be replaced)
     * @param   array   $options    MySQL-specific view options:
     *                              - algorithm: UNDEFINED, MERGE, or TEMPTABLE (default: UNDEFINED)
     *                              - definer: User who owns the view (default: CURRENT_USER)
     *                              - security: DEFINER or INVOKER (default: INVOKER)
     * @return  bool
     **/
    public function createOrReplaceView($name, $selectSql, array $options = []): bool
    {
        $viewName = str_replace('#__', $this->tablePrefix, $name);
        $selectSql = str_replace('#__', $this->tablePrefix, $selectSql);

        // Apply MySQL-specific options with defaults
        $algorithm = strtoupper($options['algorithm'] ?? 'UNDEFINED');
        $definer = $options['definer'] ?? 'CURRENT_USER';
        $security = strtoupper($options['security'] ?? 'INVOKER');

        $sql = 'CREATE OR REPLACE ALGORITHM=' . $algorithm
             . ' DEFINER=' . $definer
             . ' SQL SECURITY ' . $security
             . ' VIEW ' . $this->quoteName($viewName) . ' AS ' . $selectSql;
        $this->setQuery($sql);
        $this->execute();

        return true;
    }

    /**
     * Drops a database view
     *
     * @param   string  $name      The view name (without prefix)
     * @param   bool    $ifExists  Whether to use IF EXISTS clause
     * @return  bool
     **/
    public function dropView($name, $ifExists = true): bool
    {
        $tableName = str_replace('#__', $this->tablePrefix, $name);
        $sql = 'DROP VIEW ' . ($ifExists ? 'IF EXISTS ' : '') . $this->quoteName($tableName);
        $this->setQuery($sql);
        $this->execute();

        return true;
    }

    /**
     * Checks if a view exists in the database
     *
     * @param   string  $name  The view name (without prefix)
     * @return  bool
     **/
    public function viewExists($name): bool
    {
        $tableName = str_replace('#__', $this->tablePrefix, $name);
        $this->setQuery(
            'SELECT COUNT(*) FROM information_schema.VIEWS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '
            . $this->quote($tableName)
        );

        return (bool) $this->loadResult();
    }

    /**
     * Returns a list of all views in the current database
     *
     * @return  array  Array of view names
     **/
    public function getViews(): array
    {
        $this->setQuery(
            'SELECT TABLE_NAME FROM information_schema.VIEWS WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME'
        );

        return $this->loadColumn() ?: [];
    }

    /**
     * Returns a list of all database names on the server
     *
     * @return  array  Array of database names
     **/
    public function getDatabaseNames(): array
    {
        $this->setQuery('SHOW DATABASES');

        return $this->loadColumn() ?: [];
    }

    // =========================================================================
    // Sequence Emulation (table-based)
    //
    // MySQL does not have native sequence objects. This implementation
    // provides API-compatible sequence emulation via a `_sequences` table.
    // The emulation uses MySQL's LAST_INSERT_ID(expr) trick for atomic
    // increment without explicit row-level locking.
    // =========================================================================

    /**
     * Ensures the _sequences emulation table exists
     *
     * Creates the table on first use (lazy init). The table is:
     *   _sequences(name VARCHAR(255) PK, current_value BIGINT, increment_value INT)
     *
     * @return  void
     */
    protected function ensureSequenceTable(): void
    {
        if ($this->sequenceTableReady) {
            return;
        }

        if (!$this->tableExists('_sequences')) {
            $this->setQuery(
                'CREATE TABLE `_sequences` ('
                . '`name` VARCHAR(255) NOT NULL PRIMARY KEY, '
                . '`current_value` BIGINT NOT NULL DEFAULT 0, '
                . '`increment_value` INT NOT NULL DEFAULT 1, '
                . '`table_name` VARCHAR(255) NULL'
                . ') ENGINE=InnoDB'
            );
            $this->execute();
        } elseif (!$this->tableHasField('_sequences', 'table_name')) {
            $this->setQuery(
                'ALTER TABLE `_sequences` ADD `table_name` VARCHAR(255) NULL'
            );
            $this->execute();
        }

        $this->sequenceTableReady = true;
    }

    /**
     * Returns a list of all emulated sequences
     *
     * @return  array  Array of SequenceInfo objects
     **/
    public function getSequences(): array
    {
        $this->ensureSequenceTable();
        $this->setQuery('SELECT * FROM `_sequences` ORDER BY `name`');
        $rows = $this->loadObjectList();

        return array_map(function ($row) {
            return new \Hubzero\Database\Schema\SequenceInfo([
                'name'          => $row->name,
                'current_value' => (int) $row->current_value,
                'increment'     => (int) $row->increment_value,
            ]);
        }, $rows);
    }

    /**
     * Creates a new emulated sequence
     *
     * Seeds current_value as start - increment so the first
     * nextSequenceValue() call returns $start.
     *
     * @param   string  $name       The sequence name
     * @param   int     $start      Starting value (default: 1)
     * @param   int     $increment  Increment value (default: 1)
     * @param   array   $options    Additional options (ignored)
     * @return  bool
     **/
    public function createSequence(
        $name,
        $start = 1,
        $increment = 1,
        array $options = []
    ): bool {
        $this->ensureSequenceTable();
        $name = $this->replacePrefix($name);
        $seedValue = (int) $start - (int) $increment;
        $tableName = $options['table'] ?? null;
        if ($tableName) {
            $tableName = $this->replacePrefix($tableName);
        }

        $columns = '`name`, `current_value`, `increment_value`, `table_name`';
        $values = $this->quote($name) . ', '
            . $seedValue . ', '
            . (int) $increment . ', '
            . ($tableName ? $this->quote($tableName) : 'NULL');

        $this->setQuery(
            "INSERT INTO `_sequences` ({$columns}) VALUES ({$values})"
        );
        $this->execute();

        return true;
    }

    /**
     * Drops an emulated sequence
     *
     * @param   string  $name      The sequence name
     * @param   bool    $ifExists  Whether to silently ignore missing sequences
     * @return  bool
     **/
    public function dropSequence($name, $ifExists = true): bool
    {
        $this->ensureSequenceTable();
        $name = $this->replacePrefix($name);

        $this->setQuery(
            'DELETE FROM `_sequences` WHERE `name` = '
            . $this->quote($name)
        );
        $this->execute();

        return true;
    }

    protected function cleanupSequencesForTable(string $tableName): void
    {
        if ($this->sequenceTableReady || $this->tableExists('_sequences')) {
            try {
                $this->setQuery(
                    'DELETE FROM `_sequences` WHERE `table_name` = '
                    . $this->quote($tableName)
                );
                $this->execute();
            } catch (\Exception $e) {
                // Backward compatibility: older `_sequences` schemas may not
                // include `table_name`. In that case, skip table-scoped cleanup.
                if (stripos($e->getMessage(), 'table_name') === false) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Checks if an emulated sequence exists
     *
     * @param   string  $name  The sequence name
     * @return  bool
     **/
    public function sequenceExists($name): bool
    {
        $this->ensureSequenceTable();
        $name = $this->replacePrefix($name);

        $this->setQuery(
            'SELECT COUNT(*) FROM `_sequences` WHERE `name` = '
            . $this->quote($name)
        );

        return (int) $this->loadResult() > 0;
    }

    /**
     * Gets the next value from an emulated sequence
     *
     * Uses MySQL's LAST_INSERT_ID(expr) trick for atomic increment.
     * The UPDATE implicitly row-locks, so no FOR UPDATE is needed.
     *
     * @param   string  $name  The sequence name
     * @return  int
     **/
    public function nextSequenceValue($name): int
    {
        $this->ensureSequenceTable();
        $name = $this->replacePrefix($name);

        $this->setQuery(
            'UPDATE `_sequences` SET `current_value` = '
            . 'LAST_INSERT_ID(`current_value` + `increment_value`) '
            . 'WHERE `name` = ' . $this->quote($name)
        );
        $this->execute();

        $this->setQuery('SELECT LAST_INSERT_ID()');

        return (int) $this->loadResult();
    }

    /**
     * Gets the current value of an emulated sequence (without incrementing)
     *
     * @param   string  $name  The sequence name
     * @return  int
     **/
    public function currentSequenceValue($name): int
    {
        $this->ensureSequenceTable();
        $name = $this->replacePrefix($name);

        $this->setQuery(
            'SELECT `current_value` FROM `_sequences` WHERE `name` = '
            . $this->quote($name)
        );
        $result = $this->loadResult();

        return $result !== null ? (int) $result : 0;
    }

    /**
     * Check if this driver supports sequences
     *
     * MySQL provides table-based sequence emulation via a _sequences table.
     *
     * @return  bool
     **/
    public function supportsSequences(): bool
    {
        return true;
    }

    /**
     * MySQL implements sequences via `_sequences` table emulation.
     *
     * @return  bool
     */
    public function usesSequenceEmulation(): bool
    {
        return true;
    }

    /**
     * MySQL CAST uses SIGNED/UNSIGNED rather than INTEGER.
     *
     * @return  string
     */
    public function getIntegerCastKeyword(): string
    {
        return 'SIGNED';
    }

    /**
     * Selects a database for use
     *
     * @param   string  $database  The name of the database to select for use
     * @return  bool
     */
    public function select($database)
    {
        if (empty($database)) {
            return false;
        }

        $this->connection->exec('USE ' . $this->quoteName($database));

        $this->database = $database;

        return true;
    }

    /**
     * Sets the connection to use UTF-8 character encoding
     *
     * This is already happening in the initial database connection for PDO.
     *
     * @return  bool
     */
    public function setUTF()
    {
        return $this->setNamesCharset('utf8mb4');
    }

    // =========================================================================
    // Feature Detection Methods - MySQL Implementations
    // =========================================================================

    /**
     * Check if this database supports column positioning (AFTER/BEFORE/FIRST)
     *
     * MySQL fully supports column positioning in ALTER TABLE statements.
     *
     * @return  bool  True - MySQL supports column positioning
     */
    public function supportsColumnPositioning(): bool
    {
        return true;
    }

    /**
     * Check if this database supports storage engines
     *
     * MySQL supports multiple storage engines (InnoDB, MyISAM, etc.)
     *
     * @return  bool  True - MySQL supports storage engines
     */
    public function supportsEngine(): bool
    {
        return true;
    }

    /**
     * Check if this database supports ENUM column types
     *
     * MySQL supports native ENUM types.
     *
     * @return  bool  True - MySQL supports ENUM
     */
    public function supportsEnum(): bool
    {
        return true;
    }

    /**
     * Check if this database supports table-level character sets
     *
     * MySQL supports per-table and per-column character set and collation.
     *
     * @return  bool  True - MySQL supports table charsets
     */
    public function supportsTableCharset(): bool
    {
        return true;
    }

    /**
     * Check if this database supports UNSIGNED integer types
     *
     * MySQL supports UNSIGNED on integer types.
     *
     * @return  bool  True - MySQL supports UNSIGNED
     */
    public function supportsUnsigned(): bool
    {
        return true;
    }

    /**
     * Check if this database supports column comments in DDL
     *
     * MySQL supports COMMENT 'text' on column definitions.
     *
     * @return  bool  True - MySQL supports column comments
     */
    public function supportsColumnComments(): bool
    {
        return true;
    }

    /**
     * Check if this database supports prefix lengths on indexes
     *
     * MySQL supports INDEX idx_name (column(length)) for partial indexes.
     *
     * @return  bool  True - MySQL supports index prefix lengths
     */
    public function supportsIndexPrefixLength(): bool
    {
        return true;
    }

    // =========================================================================
    // Schema Building Methods - MySQL Implementations
    // =========================================================================

    /**
     * Quote a single identifier using MySQL backticks
     *
     * @param   string  $identifier  The identifier to quote
     * @return  string  The quoted identifier
     */
    public function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    /**
     * MySQL allows AUTO_INCREMENT separately from PRIMARY KEY
     *
     * @return  bool  False - MySQL does not require PRIMARY KEY in AI definition
     */
    public function autoIncrementIncludesPrimaryKey(): bool
    {
        return false;
    }

    /**
     * Build a UNIQUE constraint definition for CREATE TABLE
     *
     * MySQL uses UNIQUE KEY syntax.
     *
     * @param   string  $quotedName     The quoted constraint name
     * @param   string  $columnList     The column list SQL
     * @return  string  The constraint definition SQL
     */
    public function buildUniqueConstraint(string $quotedName, string $columnList): string
    {
        return "UNIQUE KEY $quotedName ($columnList)";
    }

    /**
     * Build the table options string for CREATE TABLE
     *
     * MySQL supports ENGINE, CHARSET, and COLLATE options.
     *
     * @param   string|null  $engine     The storage engine
     * @param   string|null  $charset    The character set
     * @param   string|null  $collation  The collation
     * @return  string  The table options SQL
     */
    public function buildTableOptions(
        ?string $engine = null,
        ?string $charset = null,
        ?string $collation = null
    ): string {
        $parts = [];

        if ($engine !== null) {
            $resolved = $this->resolveEngine($engine);
            if ($resolved !== null) {
                $parts[] = "ENGINE=$resolved";
            }
        }

        if ($charset !== null) {
            $parts[] = "DEFAULT CHARSET=$charset";
        }

        if ($collation !== null) {
            $parts[] = "COLLATE=$collation";
        }

        return implode(' ', $parts);
    }

    // =========================================================================
    // DDL Helper Methods - MySQL Implementations
    // =========================================================================

    /**
     * Modify a column definition
     *
     * @param   string  $table       Table name (with or without prefix)
     * @param   string  $column      Column name
     * @param   string  $definition  New column definition (e.g., "VARCHAR(255) NOT NULL DEFAULT ''")
     * @param   string  $comment     Optional column comment
     * @return  bool
     */
    protected function buildModifyColumnSql(
        string $table,
        string $column,
        string $definition,
        string $comment
    ): string {
        $query = "ALTER TABLE `$table` MODIFY COLUMN `$column` $definition";

        if ($comment) {
            $query .= " COMMENT " . $this->quote($comment);
        }

        return $query;
    }

    /**
     * Modify a column definition and move it after a specific column
     *
     * @param   string  $table        Table name (with or without prefix)
     * @param   string  $column       Column name
     * @param   string  $definition   New column definition
     * @param   string  $afterColumn  Column to position after
     * @param   string  $comment      Optional column comment
     * @return  bool
     */
    public function modifyColumnAfter(
        string $table,
        string $column,
        string $definition,
        string $afterColumn,
        string $comment = ''
    ): bool {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table)) {
            return false;
        }

        if (!$this->tableHasField($table, $column)) {
            return false;
        }

        $query = "ALTER TABLE `$table` MODIFY COLUMN `$column` $definition";

        if ($comment) {
            $query .= " COMMENT " . $this->quote($comment);
        }

        $query .= " AFTER `$afterColumn`";

        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Modify a column definition and move it before a specific column
     *
     * MySQL doesn't have a native BEFORE syntax, so we find the preceding column
     * and use AFTER, or FIRST if the target column is the first column.
     *
     * @param   string  $table         Table name (with or without prefix)
     * @param   string  $column        Column name
     * @param   string  $definition    New column definition
     * @param   string  $beforeColumn  Column to position before
     * @param   string  $comment       Optional column comment
     * @return  bool
     */
    public function modifyColumnBefore(
        string $table,
        string $column,
        string $definition,
        string $beforeColumn,
        string $comment = ''
    ): bool {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table)) {
            return false;
        }

        if (!$this->tableHasField($table, $column)) {
            return false;
        }

        // Get column list to find the column before $beforeColumn
        $columns = array_keys($this->getTableColumns($table));

        $beforeIndex = array_search($beforeColumn, $columns);
        if ($beforeIndex === false) {
            // beforeColumn not found, just modify without repositioning
            return $this->modifyColumn($table, $column, $definition, $comment);
        }

        if ($beforeIndex === 0) {
            // beforeColumn is the first column, use FIRST
            return $this->modifyColumnFirst($table, $column, $definition, $comment);
        }

        // Use AFTER with the column that precedes beforeColumn
        $afterColumn = $columns[$beforeIndex - 1];

        // If the column we're modifying is the one before beforeColumn, skip repositioning
        if ($afterColumn === $column) {
            return $this->modifyColumn($table, $column, $definition, $comment);
        }

        return $this->modifyColumnAfter($table, $column, $definition, $afterColumn, $comment);
    }

    /**
     * Modify a column definition and move it to the first position
     *
     * @param   string  $table       Table name (with or without prefix)
     * @param   string  $column      Column name
     * @param   string  $definition  New column definition
     * @param   string  $comment     Optional column comment
     * @return  bool
     */
    public function modifyColumnFirst(string $table, string $column, string $definition, string $comment = ''): bool
    {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table)) {
            return false;
        }

        if (!$this->tableHasField($table, $column)) {
            return false;
        }

        $query = "ALTER TABLE `$table` MODIFY COLUMN `$column` $definition";

        if ($comment) {
            $query .= " COMMENT " . $this->quote($comment);
        }

        $query .= " FIRST";

        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Change a column name and/or definition
     *
     * @param   string  $table       Table name (with or without prefix)
     * @param   string  $oldColumn   Current column name
     * @param   string  $newColumn   New column name
     * @param   string  $definition  New column definition
     * @param   string  $comment     Optional column comment
     * @return  bool
     */
    public function changeColumn(
        string $table,
        string $oldColumn,
        string $newColumn,
        string $definition,
        string $comment = ''
    ): bool {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table)) {
            return false;
        }

        if (!$this->tableHasField($table, $oldColumn)) {
            return false;
        }

        $query = "ALTER TABLE `$table` CHANGE COLUMN `$oldColumn` `$newColumn` $definition";

        if ($comment) {
            $query .= " COMMENT " . $this->quote($comment);
        }

        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Add a column to a table
     *
     * @param   string  $table       Table name (with or without prefix)
     * @param   string  $column      Column name
     * @param   string  $definition  Column definition
     * @param   string  $comment     Optional column comment
     * @return  bool
     */
    protected function buildAddColumnSql(
        string $table,
        string $column,
        string $definition,
        string $comment
    ): string {
        $query = "ALTER TABLE `$table` ADD COLUMN `$column` $definition";

        if ($comment) {
            $query .= " COMMENT " . $this->quote($comment);
        }

        return $query;
    }

    /**
     * Add a column after a specific column
     *
     * @param   string  $table        Table name (with or without prefix)
     * @param   string  $column       Column name
     * @param   string  $definition   Column definition
     * @param   string  $afterColumn  Column to add after
     * @param   string  $comment      Optional column comment
     * @return  bool
     */
    public function addColumnAfter(
        string $table,
        string $column,
        string $definition,
        string $afterColumn,
        string $comment = ''
    ): bool {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table)) {
            return false;
        }

        if ($this->tableHasField($table, $column)) {
            return true; // Already exists
        }

        $query = "ALTER TABLE `$table` ADD COLUMN `$column` $definition";

        if ($comment) {
            $query .= " COMMENT " . $this->quote($comment);
        }

        $query .= " AFTER `$afterColumn`";

        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Add a column before a specific column
     *
     * MySQL doesn't have a native BEFORE syntax, so we find the preceding column
     * and use AFTER, or FIRST if the target column is the first column.
     *
     * @param   string  $table         Table name (with or without prefix)
     * @param   string  $column        Column name
     * @param   string  $definition    Column definition
     * @param   string  $beforeColumn  Column to add before
     * @param   string  $comment       Optional column comment
     * @return  bool
     */
    public function addColumnBefore(
        string $table,
        string $column,
        string $definition,
        string $beforeColumn,
        string $comment = ''
    ): bool {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table)) {
            return false;
        }

        if ($this->tableHasField($table, $column)) {
            return true; // Already exists
        }

        // Get column list to find the column before $beforeColumn
        $columns = array_keys($this->getTableColumns($table));

        $beforeIndex = array_search($beforeColumn, $columns);
        if ($beforeIndex === false) {
            // beforeColumn not found, just add at end
            return $this->addColumn($table, $column, $definition, $comment);
        }

        if ($beforeIndex === 0) {
            // beforeColumn is the first column, use FIRST
            return $this->addColumnFirst($table, $column, $definition, $comment);
        }

        // Use AFTER with the column that precedes beforeColumn
        $afterColumn = $columns[$beforeIndex - 1];
        return $this->addColumnAfter($table, $column, $definition, $afterColumn, $comment);
    }

    /**
     * Add a column at the beginning of a table
     *
     * @param   string  $table       Table name (with or without prefix)
     * @param   string  $column      Column name
     * @param   string  $definition  Column definition
     * @param   string  $comment     Optional column comment
     * @return  bool
     */
    public function addColumnFirst(string $table, string $column, string $definition, string $comment = ''): bool
    {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table)) {
            return false;
        }

        if ($this->tableHasField($table, $column)) {
            return true; // Already exists
        }

        $query = "ALTER TABLE `$table` ADD COLUMN `$column` $definition";

        if ($comment) {
            $query .= " COMMENT " . $this->quote($comment);
        }

        $query .= " FIRST";

        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Add a column at the end of a table
     *
     * @param   string  $table       Table name (with or without prefix)
     * @param   string  $column      Column name
     * @param   string  $definition  Column definition
     * @param   string  $comment     Optional column comment
     * @return  bool
     */
    public function addColumnLast(string $table, string $column, string $definition, string $comment = ''): bool
    {
        return $this->addColumn($table, $column, $definition, $comment);
    }

    /**
     * Set the storage engine for a table
     *
     * @param   string  $table   Table name (with or without prefix)
     * @param   string  $engine  Engine type (e.g., 'MYISAM', 'InnoDB')
     * @return  bool
     */
    public function setTableEngine(string $table, string $engine = 'MYISAM'): bool
    {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table)) {
            return false;
        }

        $resolved = $this->resolveEngine($engine);
        if ($resolved === null) {
            // No supported engines available; no-op for compatibility
            return true;
        }

        $query = "ALTER TABLE `$table` ENGINE = $resolved";
        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Set the character set and collation for a table
     *
     * @param   string  $table      Table name (with or without prefix)
     * @param   string  $charset    Character set (e.g., 'utf8')
     * @param   string  $collation  Collation (e.g., 'utf8_general_ci')
     * @return  bool
     */
    public function setTableCharset(
        string $table,
        string $charset = 'utf8',
        string $collation = 'utf8_general_ci'
    ): bool {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table)) {
            return false;
        }

        $query = "ALTER TABLE `$table` CONVERT TO CHARACTER SET $charset COLLATE $collation";
        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Drop a column from a table
     *
     * @param   string  $table   Table name (with or without prefix)
     * @param   string  $column  Column name
     * @return  bool
     */
    protected function buildDropColumnSql(string $table, string $column): string
    {
        return "ALTER TABLE `$table` DROP COLUMN `$column`";
    }

    /**
     * Add a FULLTEXT index to a table
     *
     * @param   string        $table    Table name (with or without prefix)
     * @param   string        $name     Index name
     * @param   string|array  $columns  Column name(s) to index
     * @return  bool
     */
    public function addFulltextIndex(string $table, string $name, $columns): bool
    {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table)) {
            return false;
        }

        if ($this->tableHasKey($table, $name)) {
            return true; // Index already exists
        }

        if (is_string($columns)) {
            $columns = [$columns];
        }

        $columnList = '`' . implode('`, `', $columns) . '`';

        $query = "ALTER TABLE `$table` ADD FULLTEXT INDEX `$name` ($columnList)";
        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Drop the primary key from a table
     *
     * @param   string  $table  Table name (with or without prefix)
     * @return  bool
     */
    public function dropPrimaryKey(string $table): bool
    {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table)) {
            return false;
        }

        if (!$this->tableHasKey($table, 'PRIMARY')) {
            return true; // No primary key to drop
        }

        $query = "ALTER TABLE `$table` DROP PRIMARY KEY";
        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Add a primary key to a table
     *
     * @param   string        $table    Table name (with or without prefix)
     * @param   string|array  $columns  Column name(s) for the primary key
     * @return  bool
     */
    public function addPrimaryKey(string $table, $columns): bool
    {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table)) {
            return false;
        }

        if ($this->tableHasKey($table, 'PRIMARY')) {
            return true; // Primary key already exists
        }

        $columns = is_array($columns) ? $columns : [$columns];
        $columnList = '`' . implode('`, `', $columns) . '`';

        $query = "ALTER TABLE `$table` ADD PRIMARY KEY ($columnList)";
        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Add an auto-increment primary key column to a table
     *
     * On MySQL, uses SERIAL (BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE) or
     * INT AUTO_INCREMENT depending on the $useBigInt parameter.
     *
     * @param   string  $table      Table name (with or without prefix)
     * @param   string  $column     Column name (usually 'id')
     * @param   bool    $first      Add as first column
     * @param   bool    $useBigInt  Use BIGINT/SERIAL (true) or INT (false)
     * @return  bool
     */
    public function addAutoIncrementPrimaryKey(
        string $table,
        string $column = 'id',
        bool $first = false,
        bool $useBigInt = true
    ): bool {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table)) {
            return false;
        }

        if ($this->tableHasField($table, $column)) {
            return true; // Column already exists
        }

        if ($useBigInt) {
            $query = "ALTER TABLE `$table` ADD COLUMN `$column` SERIAL NOT NULL PRIMARY KEY";
        } else {
            $query = "ALTER TABLE `$table` ADD COLUMN `$column` INT NOT NULL AUTO_INCREMENT PRIMARY KEY";
        }

        if ($first) {
            $query .= " FIRST";
        }

        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Populate a column with sequential integer values for existing rows
     *
     * Uses MySQL user variables to assign sequential values.
     *
     * @param   string       $table    Table name (with or without prefix)
     * @param   string       $column   Column name to populate
     * @param   string|null  $orderBy  Optional column to order by when assigning sequence
     * @return  bool
     */
    public function populateSequentialValues(string $table, string $column, ?string $orderBy = null): bool
    {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table) || !$this->tableHasField($table, $column)) {
            return false;
        }

        // Initialize user variable
        $this->setQuery("SET @row_num = 0");
        if (!$this->execute()) {
            return false;
        }

        // Build UPDATE query with user variable increment
        $query = "UPDATE `$table` SET `$column` = (@row_num := @row_num + 1)";

        if ($orderBy) {
            $query .= " ORDER BY `$orderBy`";
        }

        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Add an index to a table
     *
     * @param   string        $table    Table name (with or without prefix)
     * @param   string        $name     Index name
     * @param   string|array  $columns  Column name(s) to index
     * @param   bool          $unique   Whether to create a unique index
     * @return  bool
     */
    protected function buildCreateIndexSql(string $table, string $name, array $columns, bool $unique): string
    {
        $columnList = '`' . implode('`, `', $columns) . '`';
        $uniqueStr = $unique ? 'UNIQUE ' : '';
        return "ALTER TABLE `$table` ADD {$uniqueStr}INDEX `$name` ($columnList)";
    }

    /**
     * Drop an index from a table
     *
     * @param   string  $table  Table name (with or without prefix)
     * @param   string  $name   Index name
     * @return  bool
     */
    public function dropIndex(string $table, string $name): bool
    {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table)) {
            return true; // Table doesn't exist, nothing to drop
        }

        if (!$this->tableHasKey($table, $name)) {
            return true; // Index doesn't exist, nothing to drop
        }

        $query = "ALTER TABLE `$table` DROP INDEX `$name`";
        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Test to see if the MySQL PDO connector is available
     *
     * @return  bool  True if the MySQL PDO extension is available
     */
    public static function test()
    {
        return class_exists('\PDO') && in_array('mysql', \PDO::getAvailableDrivers());
    }

    /**
     * Get the schema grammar instance for MySQL
     *
     * @return  \Hubzero\Database\Drivers\Base\BaseSchemaGrammar
     */
    public function getSchemaGrammar()
    {
        return $this->makeSchemaGrammarFromRegistry();
    }
}
