<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Informix;

use Hubzero\Database\ConnectionInterface;
use Hubzero\Database\Drivers\Base\BaseSqlDriver;
use Hubzero\Database\Exception\ConnectionFailedException;
use Hubzero\Database\Exception\QueryFailedException;

/**
 * Informix database driver
 *
 * IBM Informix is a product family within IBM's Information Management division.
 * This driver extends the PDO driver with Informix-specific functionality.
 *
 * Informix-specific features:
 * - SERIAL, SERIAL8, BIGSERIAL for auto-increment
 * - FIRST/SKIP pagination syntax
 * - System catalog tables (systables, syscolumns)
 * - EXTEND function for type conversion
 * - Informix-specific error handling
 *
 * Informix restrictions and quirks:
 * - SERIAL columns cannot be updated (error -232), enforced at SQL PREPARE stage
 *   See Syntax\Informix::buildSet() for workaround that filters SERIAL columns from UPDATE
 * - Column names returned UPPERCASE by default, requires PDO::ATTR_CASE = PDO::CASE_LOWER
 * - Identifiers are case-insensitive and don't support double-quote quoting by default
 *
 * TODO: ORM should only include dirty attributes in UPDATE statements (like Laravel Eloquent),
 * which would eliminate the need for SERIAL column filtering. Current implementation in
 * Relational::modify() uses getTableColumnsOnly() which returns ALL attributes.
 * See: Syntax\Informix::buildSet() for detailed explanation.
 *
 * Connection requirements:
 * - PDO_INFORMIX extension must be installed
 * - DSN format: informix:host=hostname;service=service_name;database=database_name;server=server_name
 * - Alternative: informix:DSN=odbc_dsn_name
 */
class InformixDriver extends BaseSqlDriver
{
    /**
     * The database driver name
     *
     * @var  string
     */
    protected $name = 'informix';

    /**
     * Track if we just executed a DDL statement
     *
     * @var  bool
     */
    protected $lastWasDdl = false;

    /**
     * The character(s) used to quote SQL statement names such as tables or columns
     *
     * Informix does not use quoted identifiers — identifiers are case-insensitive.
     *
     * @var  string
     */
    protected $wrapper = '%s';

    /**
     * Map of abstract column types to Informix-specific SQL types
     *
     * @var  array
     */

    /**
     * SQL expression for current timestamp
     *
     * @var  string
     */
    protected string $nowExpression = 'CURRENT';

    /**
     * SQL expression for random number/ordering
     *
     * @var  string
     */
    protected string $randExpression = 'RANDOM()';

    /**
     * SQL function name for string length
     *
     * @var  string
     */
    protected string $lengthFunction = 'LENGTH';

    /**
     * SQL function name for NULL coalescing
     *
     * @var  string
     */
    protected string $ifNullFunction = 'NVL';

    /**
     * Transaction nesting depth
     *
     * @var  int
     */
    protected $transactionDepth = 0;
    /**
     * Whether SQL transactions (BEGIN/COMMIT/ROLLBACK WORK) are supported.
     *
     * Null means unknown and will be detected lazily.
     *
     * @var bool|null
     */
    protected $transactionSqlSupported = null;
    /**
     * Whether we are emulating transactions by buffering write SQL.
     *
     * @var bool
     */
    protected $transactionEmulationActive = false;
    /**
     * Buffered SQL statements for emulated transactions.
     *
     * @var array<int, string>
     */
    protected $transactionBuffer = [];
    /**
     * Keep LOB streams alive until statement execution completes.
     *
     * @var array<int, resource>
     */
    protected $lobStreams = [];

    /**
     * Whether Informix trace logging is enabled.
     *
     * @return bool
     */
    protected function isTraceEnabled(): bool
    {
        $value = getenv('IFX_TRACE');
        if ($value === false) {
            return false;
        }
        $value = strtolower(trim((string) $value));
        return !in_array($value, ['', '0', 'false', 'off', 'no'], true);
    }

    /**
     * Write one trace line for Informix debugging.
     *
     * @param string $message
     * @return void
     */
    protected function trace(string $message): void
    {
        if (!$this->isTraceEnabled()) {
            return;
        }

        $file = getenv('IFX_TRACE_FILE') ?: '/tmp/ifx_trace.log';
        $line = sprintf(
            "[%s] [pid:%d] %s\n",
            date('Y-m-d H:i:s'),
            getmypid(),
            $message
        );
        @file_put_contents($file, $line, FILE_APPEND);
    }


    /**
     * Constructs a new Informix database object
     *
     * @param   array  $options  The database connection options
     * @return  void
     * @throws  ConnectionFailedException
     */
    public function __construct($options)
    {
        // Check if PDO Informix extension is available
        if (!class_exists('PDO') || !in_array('informix', \PDO::getAvailableDrivers())) {
            throw new ConnectionFailedException(
                'PDO Informix extension is not available. Please install the PDO_INFORMIX extension.',
                500
            );
        }

        // Build DSN if not provided
        if (empty($options['dsn'])) {
            $options['dsn'] = $this->buildDsn($options);
        }

        // Set Informix-specific PDO attributes
        if (!isset($options['driver_options'])) {
            $options['driver_options'] = [];
        }

        // Force lowercase column names for ORM compatibility
        // Informix returns UPPERCASE by default, but the ORM expects lowercase
        $options['driver_options'][\PDO::ATTR_CASE] = \PDO::CASE_LOWER;

        // Call parent constructor
        parent::__construct($options);

        // Set Informix-specific connection options
        $this->setInformixOptions();
    }

    /**
     * Builds a DSN string for Informix connection
     *
     * @param   array  $options  Connection options
     * @return  string
     */
    protected function buildDsn(array $options)
    {
        $parts = ['informix:'];

        // Host and service
        if (!empty($options['host'])) {
            $parts[] = 'host=' . $options['host'];
        }

        if (!empty($options['service']) || !empty($options['port'])) {
            $service = $options['service'] ?? $options['port'] ?? '9088';
            $parts[] = 'service=' . $service;
        }

        // Database name
        if (!empty($options['database'])) {
            $parts[] = 'database=' . $options['database'];
        }

        // Server name (Informix server instance)
        if (!empty($options['server'])) {
            $parts[] = 'server=' . $options['server'];
        }

        // Protocol
        if (!empty($options['protocol'])) {
            $parts[] = 'protocol=' . $options['protocol'];
        }

        return implode(';', $parts);
    }

    /**
     * Sets Informix-specific connection options
     *
     * @return  void
     */
    protected function setInformixOptions()
    {
        // Force lowercase column names for ORM compatibility
        // Informix returns UPPERCASE by default, but the ORM expects lowercase
        try {
            $this->connection->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER);
            $this->trace('setAttribute ATTR_CASE=CASE_LOWER');
        } catch (\PDOException $e) {
            $this->trace('setAttribute ATTR_CASE failed: ' . $e->getMessage());
            // Ignore if not supported
        }

        // Prefer emulated prepares for Informix CLOB stability.
        // Native ODBC prepared execution can fail with smart-LOB locator errors
        // (e.g. SQLSTATE HY000 / -12014) under repeated CRUD in test flows.
        try {
            $this->connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
            $this->trace('setAttribute ATTR_EMULATE_PREPARES=true');
        } catch (\PDOException $e) {
            $this->trace('setAttribute ATTR_EMULATE_PREPARES failed: ' . $e->getMessage());
            // Ignore if not supported
        }

        // Force LOB-capable values to be fetched as strings instead of streams.
        // This mirrors known working practice in Doctrine's pdo_informix driver.
        try {
            $this->connection->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, true);
            $this->trace('setAttribute ATTR_STRINGIFY_FETCHES=true');
        } catch (\PDOException $e) {
            $this->trace('setAttribute ATTR_STRINGIFY_FETCHES failed: ' . $e->getMessage());
            // Ignore if not supported
        }

        // Enable ANSI mode for better SQL standard compliance
        try {
            $this->connection->exec('SET LOCK MODE TO WAIT');
            $this->trace('exec SET LOCK MODE TO WAIT');
        } catch (\PDOException $e) {
            $this->trace('exec SET LOCK MODE TO WAIT failed: ' . $e->getMessage());
            // Ignore if not supported
        }
    }

    /**
     * Gets the auto-incremented value from the last INSERT statement
     *
     * Informix uses SERIAL, SERIAL8, or BIGSERIAL for auto-increment.
     * The SQLCA.SQLERRD[1] contains the last SERIAL value.
     *
     * @return  int
     */
    public function insertid()
    {
        // PDO_INFORMIX supports lastInsertId() for SERIAL columns
        return (int) $this->connection->lastInsertId();
    }

    /**
     * Determines if the connection to the server is active
     *
     * @return  bool
     */
    public function connected()
    {
        if (!$this->connection) {
            return false;
        }

        // Use ConnectionInterface method if available
        if ($this->connection instanceof \Hubzero\Database\ConnectionInterface) {
            return $this->connection->isConnected();
        }

        return false;
    }

    /**
     * Execute the SQL statement
     *
     * Wraps parent with DDL auto-commit. Informix does not auto-commit
     * DDL statements, so we must explicitly commit after CREATE, ALTER,
     * DROP, TRUNCATE, and RENAME when outside an explicit transaction.
     *
     * @return  $this|int
     * @throws  QueryFailedException
     */
    public function execute()
    {
        $sql = $this->statement->queryString ?? '';
        $firstWord = strtoupper(strtok(ltrim($sql), " \t\n"));
        $isDdl = in_array($firstWord, ['CREATE', 'ALTER', 'DROP', 'TRUNCATE', 'RENAME'], true);
        if ($sql !== '') {
            $this->trace('execute SQL: ' . preg_replace('/\s+/', ' ', trim($sql)));
        }

        // If transactions are being emulated, buffer mutating SQL until commit.
        if (
            $this->transactionEmulationActive
            && $this->transactionDepth > 0
            && $this->shouldBufferTransactionStatement($sql)
        ) {
            $this->transactionBuffer[] = $this->interpolateBindingsIntoSql($sql, $this->bindings ?? []);
            $this->lobStreams = [];
            return 1;
        }

        try {
            $result = parent::execute();
        } catch (QueryFailedException $e) {
            // Some PDO_INFORMIX builds raise a false -11031 cursor-state
            // error after successful no-result DDL execution.
            if ($isDdl && $this->isIgnorableDdlCursorStateFailure($e)) {
                $this->trace('ignoring DDL cursor-state warning: ' . $e->getMessage());
                $result = 1;
            } else {
                throw $e;
            }
        } finally {
            // Release any temporary LOB streams used by bind().
            foreach ($this->lobStreams as $stream) {
                if (is_resource($stream)) {
                    @fclose($stream);
                }
            }
            $this->lobStreams = [];
        }

        // Auto-commit DDL when outside explicit transactions
        if ($isDdl && $this->transactionDepth === 0) {
            $this->lastWasDdl = true;
            try {
                if ($this->connection->inTransaction()) {
                    $this->connection->commit();
                }
            } catch (\PDOException $e) {
                // Ignore
            }
        }

        return $result;
    }

    /**
     * Executes a raw SQL statement directly without prepared statements.
     *
     * During emulated transactions, mutating statements are buffered and
     * replayed on commit to preserve commit/rollback behavior.
     *
     * @param   string  $statement  The SQL statement to execute
     * @return  int
     */
    public function exec($statement)
    {
        $firstWord = strtoupper(strtok(ltrim((string) $statement), " \t\n"));
        $isDdl = in_array($firstWord, ['CREATE', 'ALTER', 'DROP', 'TRUNCATE', 'RENAME'], true);

        if (
            $this->transactionEmulationActive
            && $this->transactionDepth > 0
            && $this->shouldBufferTransactionStatement((string) $statement)
        ) {
            $this->transactionBuffer[] = $this->replacePrefix((string) $statement);
            return 1;
        }

        try {
            return parent::exec($statement);
        } catch (QueryFailedException $e) {
            if ($isDdl && $this->isIgnorableDdlCursorStateFailure($e)) {
                $this->trace('ignoring DDL cursor-state warning in exec(): ' . $e->getMessage());
                return 1;
            }
            throw $e;
        }
    }

    /**
     * Detect known false DDL cursor-state failures from PDO_INFORMIX.
     *
     * @param   \Throwable  $e
     * @return  bool
     */
    protected function isIgnorableDdlCursorStateFailure(\Throwable $e): bool
    {
        $message = strtolower($e->getMessage());

        return strpos($message, '-11031') !== false
            || strpos($message, 'invalid cursor state') !== false;
    }

    /**
     * Interpolate positional bindings into SQL using driver quoting.
     *
     * @param string $sql
     * @param array $bindings
     * @return string
     */
    protected function interpolateBindingsIntoSql(string $sql, array $bindings): string
    {
        $parts = explode('?', $sql);
        if (count($parts) <= 1) {
            return $sql;
        }
        $out = $parts[0];
        $count = count($parts) - 1;
        for ($i = 0; $i < $count; $i++) {
            $out .= $this->quoteLiteralBinding($bindings[$i] ?? null) . $parts[$i + 1];
        }
        return $out;
    }

    /**
     * Quote one binding value as SQL literal.
     *
     * @param mixed $value
     * @return string
     */
    protected function quoteLiteralBinding($value): string
    {
        if ($value === null) {
            return 'NULL';
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }
        $value = (string) $value;

        // Informix limits individual quoted literals (~32768 bytes). Split
        // large payloads into concatenated chunks so CLOB writes can still use
        // literal fallback safely.
        if (strlen($value) > 16000) {
            $chunks = str_split($value, 8000);
            $quoted = [];
            foreach ($chunks as $chunk) {
                $quoted[] = $this->quote($chunk);
            }
            return '(' . implode(' || ', $quoted) . ')';
        }

        return $this->quote($value);
    }

    /**
     * Binds the given bindings to the prepared statement
     *
     * WORKAROUND for PDO_INFORMIX bug: bindValue() with PDO::PARAM_NULL
     * corrupts the parameter count, causing "Wrong number of parameters"
     * (-11012) on execute(). Use PDO::PARAM_STR for null values instead.
     *
     * @param   array  $bindings  The param bindings
     * @param   array  $type      The param types
     * @return  $this
     */
    public function bind($bindings, $type = [])
    {
        $idx = 1;

        $this->bindings = $bindings;
        $this->lobStreams = [];

        foreach ($bindings as $binding) {
            if (is_null($binding)) {
                // PDO_INFORMIX bug: PARAM_NULL breaks parameter count
                // Use PARAM_STR which correctly inserts NULL
                $pdoType = \PDO::PARAM_STR;
            } elseif (is_bool($binding)) {
                $pdoType = \PDO::PARAM_BOOL;
            } elseif (is_int($binding)) {
                $pdoType = \PDO::PARAM_INT;
            } else {
                $pdoType = \PDO::PARAM_STR;
            }

            if (isset($type[$idx])) {
                $pdoType = constant('\PDO::PARAM_' . strtoupper($type[$idx]));
            }

            $kind = gettype($binding);
            $hash = '';
            if (is_string($binding)) {
                $kind .= '(' . strlen($binding) . ')';
                if (strlen($binding) >= 1024) {
                    $hash = ' md5=' . md5($binding);
                }
            } elseif (is_resource($binding)) {
                $kind .= '(' . get_resource_type($binding) . ')';
            }
            $this->trace(sprintf('bind #%d %s pdoType=%d%s', $idx, $kind, $pdoType, $hash));

            // Keep TEXT payloads on plain string binding.
            // In this PDO_INFORMIX/ODBC stack, upgrading large strings to
            // PARAM_LOB stream introduced deterministic corruption:
            // payload length +1 with an embedded NUL near 8KB boundaries.
            $this->statement->bindValue($idx, $binding, $pdoType);
            $idx++;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTableExistsQuery(string $table): string
    {
        return "SELECT COUNT(*) FROM systables WHERE tabname = "
            . $this->quote(strtolower($table)) . " AND tabtype = 'T'";
    }

    public function getTableList()
    {
        $this->setQuery("
            SELECT tabname
            FROM systables
            WHERE tabtype = 'T'
              AND tabid > 99
            ORDER BY tabname
        ");

        $tables = [];
        foreach ($this->loadObjectList() as $table) {
            $tables[] = trim($table->tabname);
        }

        return $tables;
    }

    public function getTableColumns($table, $typeOnly = true)
    {
        $table = strtolower($this->replacePrefix($table));

        $this->setQuery("
            SELECT
                c.colname AS field_name,
                c.coltype AS field_type,
                c.collength AS field_length,
                CASE WHEN c.coltype >= 256 THEN 'NO' ELSE 'YES' END AS nullable,
                c.colno AS position
            FROM syscolumns c
            JOIN systables t ON c.tabid = t.tabid
            WHERE t.tabname = " . $this->quote($table) . "
              AND t.tabtype = 'T'
            ORDER BY c.colno
        ");

        $fields = $this->loadObjectList();
        $columns = [];

        if ($typeOnly) {
            foreach ($fields as $field) {
                $name = trim($field->field_name);
                $columns[$name] = $this->convertFieldType($field->field_type, $field->field_length);
            }
        } else {
            foreach ($fields as $field) {
                $name = trim($field->field_name);
                $isNullable = (strtoupper($field->nullable) === 'YES');
                $typeName = $this->convertFieldType($field->field_type, $field->field_length);
                $columns[$name] = [
                    'name'      => $name,
                    'type'      => $typeName,
                    'Type'      => $typeName,
                    'raw_type'  => $field->field_type,
                    'length'    => $field->field_length,
                    'allownull' => $isNullable,
                    'Null'      => $isNullable ? 'YES' : 'NO',
                    'default'   => null,
                    'pk'        => false
                ];
            }
        }

        return $columns;
    }

    /**
     * Convert Informix type code to generic type name
     *
     * @param   int  $typeCode  Informix type code
     * @param   int  $length    Field length
     * @return  string
     */
    protected function convertFieldType($typeCode, $length = 0)
    {
        // Remove nullable flag (types 256+ are NOT NULL variants)
        $baseType = $typeCode % 256;

        // Informix type codes from syscolumns.coltype
        $types = [
            0   => 'char',
            1   => 'smallint',
            2   => 'int',
            3   => 'float',
            4   => 'smallfloat',
            5   => 'decimal',
            6   => 'serial',
            7   => 'date',
            8   => 'money',
            9   => 'null',
            10  => 'datetime',
            11  => 'byte',
            12  => 'text',
            13  => 'varchar',
            14  => 'interval',
            15  => 'nchar',
            16  => 'nvarchar',
            17  => 'int8',
            18  => 'serial8',
            19  => 'set',
            20  => 'multiset',
            21  => 'list',
            22  => 'row',
            40  => 'lvarchar',
            41  => 'text',
            43  => 'bigint',
            44  => 'bigserial',
            52  => 'bigint',
            53  => 'bigserial',
        ];

        return $types[$baseType] ?? 'unknown';
    }

    /**
     * Begins a transaction
     *
     * Supports nested transactions via savepoints.
     *
     * @return  void
     */
    public function transactionStart()
    {
        $this->hasConnectionOrFail();

        if ($this->transactionDepth == 0) {
            if ($this->supportsTransactionSql()) {
                $this->connection->exec('BEGIN WORK');
                $this->transactionEmulationActive = false;
            } else {
                // Non-logging Informix databases cannot run transactions.
                // Emulate by buffering writes and applying them on commit.
                $this->transactionEmulationActive = true;
                $this->transactionBuffer = [];
            }
        } elseif (!$this->transactionEmulationActive) {
            $this->setQuery('SAVEPOINT SP_' . $this->transactionDepth)->execute();
        }

        $this->transactionDepth++;
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
        if ($this->transactionDepth <= 0) {
            return;
        }

        $this->transactionDepth--;

        if ($this->transactionDepth == 0) {
            if ($this->transactionEmulationActive) {
                try {
                    foreach ($this->transactionBuffer as $sql) {
                        parent::exec($sql);
                    }
                } finally {
                    $this->transactionBuffer = [];
                    $this->transactionEmulationActive = false;
                }
                return;
            }

            $this->connection->exec('COMMIT WORK');
        } elseif (!$this->transactionEmulationActive) {
            $this->setQuery('RELEASE SAVEPOINT SP_' . $this->transactionDepth)->execute();
        }
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
        if ($this->transactionDepth <= 0) {
            return;
        }

        $this->transactionDepth--;

        if ($this->transactionDepth == 0) {
            if ($this->transactionEmulationActive) {
                $this->transactionBuffer = [];
                $this->transactionEmulationActive = false;
                return;
            }

            $this->connection->exec('ROLLBACK WORK');
        } elseif (!$this->transactionEmulationActive) {
            $this->setQuery('ROLLBACK TO SAVEPOINT SP_' . $this->transactionDepth)->execute();
        }
    }

    /**
     * Determine if a statement should be buffered in emulated transactions.
     *
     * @param  string  $sql
     * @return bool
     */
    protected function shouldBufferTransactionStatement(string $sql): bool
    {
        $firstWord = strtoupper((string) strtok(ltrim($sql), " \t\n"));
        $writeWords = [
            'INSERT', 'UPDATE', 'DELETE', 'MERGE', 'REPLACE',
            'CREATE', 'ALTER', 'DROP', 'TRUNCATE', 'RENAME',
        ];
        return in_array($firstWord, $writeWords, true);
    }

    /**
     * Detect whether SQL transaction commands are available.
     *
     * @return bool
     */
    protected function supportsTransactionSql(): bool
    {
        if ($this->transactionSqlSupported !== null) {
            return $this->transactionSqlSupported;
        }

        try {
            $this->connection->exec('BEGIN WORK');
            $this->connection->exec('ROLLBACK WORK');
            $this->transactionSqlSupported = true;
            return true;
        } catch (\Throwable $e) {
            if ($this->isTransactionUnavailableError($e)) {
                $this->transactionSqlSupported = false;
                return false;
            }
            // Be conservative on unknown failures and avoid hard-failing startup.
            $this->transactionSqlSupported = false;
            return false;
        }
    }

    /**
     * Detect Informix "transaction not available" failures.
     *
     * @param  \Throwable  $e
     * @return bool
     */
    protected function isTransactionUnavailableError(\Throwable $e): bool
    {
        $message = strtolower($e->getMessage());

        if (str_contains($message, 'transaction not available')) {
            return true;
        }

        if ($e instanceof \PDOException) {
            $state = (string) ($e->errorInfo[0] ?? '');
            $code = (string) ($e->errorInfo[1] ?? '');

            if ($state === 'HY000' && $code === '-256') {
                return true;
            }
        }

        return false;
    }

    /**
     * Test to see if the Informix PDO connector is available
     *
     * @return  bool
     */
    public static function test()
    {
        return class_exists('PDO') && in_array('informix', \PDO::getAvailableDrivers());
    }

    /**
     * Quotes a string for use in a query
     *
     * Informix uses single quotes for string literals
     *
     * @param   string  $text   The string to quote
     * @param   bool    $escape Whether to escape special characters
     * @return  string
     */
    public function quote($text, $escape = true)
    {
        if (is_array($text)) {
            foreach ($text as $k => $v) {
                $text[$k] = $this->quote($v, $escape);
            }

            return $text;
        }

        // Use PDO's quote method
        return $this->connection->quote($text);
    }

    /**
     * Quotes a database identifier (table, column, etc.)
     *
     * Informix identifiers are case-insensitive and stored in lowercase.
     * Double-quoted identifiers would require DELIMIDENT=Y and make
     * identifiers case-sensitive, which goes against Informix conventions.
     * Returns identifiers unquoted.
     *
     * @param   mixed  $name  The identifier name
     * @param   mixed  $as    The alias (optional)
     * @return  string
     */
    public function quoteName($name, $as = null)
    {
        if (is_array($name)) {
            $quotedArray = [];

            foreach ($name as $k => $v) {
                $quotedArray[$k] = $this->quoteName($v);
            }

            return $quotedArray;
        }

        // Informix identifiers are case-insensitive and don't need quoting
        // Just return the name as-is (Informix will store it in lowercase)
        // Handle dot notation for table.column - keep as-is
        // Handle * wildcard - keep as-is

        // Add alias if provided
        if ($as !== null) {
            $name .= ' AS ' . $as;
        }

        return $name;
    }

    // =========================================================================
    // Schema Introspection Methods
    // =========================================================================

    /**
     * Get the details list of keys for a table
     *
     * @param   string  $table  The name of the table
     * @return  array|false   An array of the key specification for the table
     */
    public function getTableKeys($table)
    {
        $table = strtolower($this->replacePrefix($table));

        // Get table ID
        $this->setQuery(
            "SELECT tabid FROM systables WHERE tabname = " . $this->quote($table) . " AND tabtype = 'T'"
        );
        $tabid = $this->loadResult();

        if (!$tabid) {
            return [];
        }

        // Get column names by number for this table
        $this->setQuery(
            "SELECT colno, colname FROM syscolumns WHERE tabid = " . (int) $tabid
        );
        $colRows = $this->loadObjectList();
        $colMap = [];
        foreach ($colRows as $row) {
            $colMap[(int) $row->colno] = trim($row->colname);
        }

        // Find the primary key index name (via sysconstraints)
        $this->setQuery(
            "SELECT c.idxname FROM sysconstraints c " .
            "JOIN systables t ON c.tabid = t.tabid " .
            "WHERE t.tabname = " . $this->quote($table) . " AND c.constrtype = 'P'"
        );
        $pkIndexName = trim($this->loadResult() ?? '');

        // Get indexes with up to 16 parts
        $this->setQuery(
            "SELECT idxname, idxtype, " .
            "part1, part2, part3, part4, part5, part6, part7, part8, " .
            "part9, part10, part11, part12, part13, part14, part15, part16 " .
            "FROM sysindexes WHERE tabid = " . (int) $tabid
        );
        $indexes = $this->loadObjectList();

        $keys = [];
        foreach ($indexes as $index) {
            $name = trim($index->idxname);

            // Determine if this is the primary key index
            $isPrimary = ($pkIndexName !== '' && $pkIndexName === $name);

            $isUnique = in_array($index->idxtype, ['U', 'u']);

            // Use 'PRIMARY' as the key name for the PK (MySQL convention)
            $keyName = $isPrimary ? 'PRIMARY' : $name;

            // Resolve column numbers to names
            for ($i = 1; $i <= 16; $i++) {
                $partCol = 'part' . $i;
                $partNum = (int) ($index->$partCol ?? 0);
                if ($partNum === 0) {
                    break;
                }
                $colNo = abs($partNum); // negative = DESC
                $colName = $colMap[$colNo] ?? '';

                if ($colName) {
                    $keys[$keyName][] = (object) [
                        'Column_name' => $colName,
                        'Non_unique' => ($isPrimary || $isUnique) ? 0 : 1,
                        'Index_type' => 'BTREE',
                        'isPrimary' => $isPrimary,
                        'isUnique' => $isUnique,
                    ];
                }
            }
        }

        return $keys;
    }

    /**
     * Get the indexes for a table
     *
     * @param   string  $table  The table name
     * @return  array
     */
    public function getIndexes($table)
    {
        return $this->getTableKeys($table);
    }

    /**
     * Shows the table CREATE statement that creates the given tables
     *
     * @param   string|array  $tables  A table name or a list of table names
     * @return  array
     */
    public function getTableCreate($tables)
    {
        // Informix doesn't have SHOW CREATE TABLE, so reconstruct from getTableDdl()
        $result = [];
        $tables = (array) $tables;

        foreach ($tables as $table) {
            $ddl = $this->getTableDdl($this->replacePrefix($table));
            if ($ddl) {
                $result[$table] = $ddl[0]; // First element is the CREATE TABLE statement
            }
        }

        return $result;
    }

    // =========================================================================
    // Table Operations
    // =========================================================================

    public function lockTable($table)
    {
        $this->hasConnectionOrFail();

        $sql = 'LOCK TABLE ' . $this->quoteName($table) . ' IN EXCLUSIVE MODE';

        return $this->exec($sql);
    }

    public function unlockTables()
    {
        // Informix releases locks automatically at transaction end
        return true;
    }

    public function renameTable($oldTable, $newTable, $backup = null, $prefix = null)
    {
        $oldTable = $this->replacePrefix($oldTable);
        $newTable = $this->replacePrefix($newTable);

        $this->setQuery('RENAME TABLE ' . $this->quoteName($oldTable) . ' TO ' . $this->quoteName($newTable))
             ->execute();
        return true;
    }

    public function dropTable($table, $ifExists = true)
    {
        $table = $this->replacePrefix($table);

        // Informix does not support DROP TABLE IF EXISTS
        if ($ifExists && !$this->tableExists($table)) {
            return $this;
        }

        $this->setQuery($this->getSchemaGrammar()->compileDrop($table, false))
             ->execute();
        return $this;
    }

    /**
     * Truncates a table (removes all rows and resets SERIAL counter)
     *
     * Informix SERIAL counters have a one-way high-water mark that
     * cannot be reset below their current value via ALTER TABLE.
     * When $nextId is at or above the high-water mark, a simple
     * DELETE + ALTER is sufficient. Only when lowering below the
     * high-water mark is the expensive DROP + CREATE needed.
     *
     * @param   string  $table   The table to truncate
     * @param   int     $nextId  The next auto-increment value (default 1)
     * @return  $this
     */
    public function truncateTable($table, int $nextId = 1)
    {
        $table = $this->replacePrefix($table);

        // Check current high-water mark before clearing rows
        $currentNextId = $this->getAutoIncrement($table);

        if ($currentNextId !== false && $nextId < $currentNextId) {
            // Need to lower the counter — DROP + CREATE is required
            // because Informix SERIAL can only go forward
            $ddlStatements = $this->getTableDdl($table);
            if ($ddlStatements) {
                $this->setQuery(
                    'DROP TABLE ' . $this->quoteName($table)
                )->execute();
                foreach ($ddlStatements as $stmt) {
                    $this->setQuery($stmt)->execute();
                }
                if ($nextId > 1) {
                    $this->setAutoIncrement($table, $nextId);
                }
            } else {
                // Can't reconstruct DDL — just truncate without reset
                $this->setQuery(
                    'TRUNCATE TABLE ' . $this->quoteName($table)
                )->execute();
            }
        } else {
            // nextId >= current counter — TRUNCATE + ALTER is sufficient
            $this->setQuery(
                'TRUNCATE TABLE ' . $this->quoteName($table)
            )->execute();
            $this->setAutoIncrement($table, $nextId);
        }

        return $this;
    }

    /**
     * Reconstruct CREATE TABLE DDL from Informix system catalogs
     *
     * Reads syscolumns/sysconstraints/sysindexes to build a CREATE TABLE
     * statement plus CREATE INDEX statements for unique and regular indexes.
     *
     * @param   string  $table  Table name (without quotes)
     * @return  array|null  Array of SQL statements [CREATE TABLE, CREATE INDEX...] or null on failure
     */
    protected function getTableDdl(string $table): ?array
    {
        $rawTable = strtolower(str_replace('"', '', $table));

        // Get tabid
        $this->setQuery(
            "SELECT tabid FROM systables WHERE tabname = " . $this->quote($rawTable)
        );
        $tabid = $this->loadResult();
        if (!$tabid) {
            return null;
        }

        // Get columns
        $this->setQuery(
            "SELECT colname, coltype, collength, extended_id FROM syscolumns"
            . " WHERE tabid = " . (int)$tabid . " ORDER BY colno"
        );
        $columns = $this->loadObjectList();
        if (empty($columns)) {
            return null;
        }

        // Get primary key constraint
        $this->setQuery(
            "SELECT c.constrname, i.part1, i.part2, i.part3, i.part4, i.part5,"
            . " i.part6, i.part7, i.part8, i.part9, i.part10, i.part11, i.part12,"
            . " i.part13, i.part14, i.part15, i.part16"
            . " FROM sysconstraints c"
            . " JOIN sysindexes i ON c.idxname = i.idxname"
            . " WHERE c.tabid = " . (int)$tabid
            . " AND c.constrtype = 'P'"
        );
        $pkInfo = $this->loadObject();

        // Map PK column numbers to names
        $pkColumns = [];
        if ($pkInfo) {
            for ($i = 1; $i <= 16; $i++) {
                $partKey = "part{$i}";
                $partVal = $pkInfo->$partKey ?? 0;
                if ($partVal == 0) {
                    break;
                }
                $colNum = abs($partVal);
                if (isset($columns[$colNum - 1])) {
                    $pkColumns[] = $columns[$colNum - 1]->colname;
                }
            }
        }

        // Get unique constraints (type 'U') for inline UNIQUE definitions
        $this->setQuery(
            "SELECT c.constrname, c.constrtype, i.idxname,"
            . " i.part1, i.part2, i.part3, i.part4, i.part5,"
            . " i.part6, i.part7, i.part8, i.part9, i.part10, i.part11, i.part12,"
            . " i.part13, i.part14, i.part15, i.part16"
            . " FROM sysconstraints c"
            . " JOIN sysindexes i ON c.idxname = i.idxname"
            . " WHERE c.tabid = " . (int)$tabid
            . " AND c.constrtype = 'U'"
        );
        $uniqueConstraints = $this->loadObjectList();

        // Get non-constraint indexes (regular indexes not tied to PK/UNIQUE constraints)
        // These are indexes in sysindexes that don't appear in sysconstraints
        $this->setQuery(
            "SELECT TRIM(i.idxname) AS idxname, i.idxtype,"
            . " i.part1, i.part2, i.part3, i.part4, i.part5,"
            . " i.part6, i.part7, i.part8, i.part9, i.part10, i.part11, i.part12,"
            . " i.part13, i.part14, i.part15, i.part16"
            . " FROM sysindexes i"
            . " WHERE i.tabid = " . (int)$tabid
            . " AND NOT EXISTS ("
            . "   SELECT 1 FROM sysconstraints c WHERE c.idxname = i.idxname AND c.tabid = i.tabid"
            . " )"
            . " AND TRIM(i.idxname) NOT LIKE ' %'"
        );
        $regularIndexes = $this->loadObjectList();

        // Build column definitions
        $colDefs = [];
        foreach ($columns as $col) {
            $coltype = (int)$col->coltype;
            $collength = (int)$col->collength;
            $name = trim($col->colname);

            $notNull = ($coltype >= 256);
            $baseType = $coltype % 256;

            $sqlType = $this->coltypeToSql($baseType, $collength);

            $def = $this->quoteName($name) . ' ' . $sqlType;

            // SERIAL columns include NOT NULL and PRIMARY KEY implicitly
            if ($baseType === 6 || $baseType === 53) {
                $colDefs[] = $def . ' PRIMARY KEY';
                $pkColumns = array_diff($pkColumns, [$name]);
                continue;
            }

            if ($notNull) {
                $def .= ' NOT NULL';
            }

            $colDefs[] = $def;
        }

        // Add composite PK if not already inline
        if (!empty($pkColumns)) {
            $quotedPkCols = array_map([$this, 'quoteName'], $pkColumns);
            $colDefs[] = 'PRIMARY KEY (' . implode(', ', $quotedPkCols) . ')';
        }

        $statements = [];
        $statements[] = 'CREATE TABLE ' . $this->quoteName($rawTable)
            . ' (' . implode(', ', $colDefs) . ')';

        // Helper to resolve index part columns to names
        $resolveIndexCols = function ($indexObj) use ($columns) {
            $cols = [];
            for ($i = 1; $i <= 16; $i++) {
                $partKey = "part{$i}";
                $partVal = $indexObj->$partKey ?? 0;
                if ($partVal == 0) {
                    break;
                }
                $colNum = abs($partVal);
                if (isset($columns[$colNum - 1])) {
                    $cols[] = trim($columns[$colNum - 1]->colname);
                }
            }
            return $cols;
        };

        // Add UNIQUE index statements from constraints
        // Use constrname (user-defined name) not idxname (system-generated like "1234_5678")
        foreach ($uniqueConstraints as $uc) {
            $idxCols = $resolveIndexCols($uc);
            if (!empty($idxCols)) {
                $idxName = trim($uc->constrname);
                $colList = implode(', ', array_map([$this, 'quoteName'], $idxCols));
                $statements[] = "CREATE UNIQUE INDEX {$idxName} ON {$rawTable}({$colList})";
            }
        }

        // Add regular index statements
        foreach ($regularIndexes as $idx) {
            $idxCols = $resolveIndexCols($idx);
            if (!empty($idxCols)) {
                $idxName = trim($idx->idxname);
                $isUnique = (trim($idx->idxtype ?? '') === 'U');
                $uniqueKw = $isUnique ? 'UNIQUE ' : '';
                $colList = implode(', ', array_map([$this, 'quoteName'], $idxCols));
                $statements[] = "CREATE {$uniqueKw}INDEX {$idxName} ON {$rawTable}({$colList})";
            }
        }

        return $statements;
    }

    /**
     * Convert Informix coltype code to SQL type string
     *
     * @param   int  $baseType   Base column type (coltype % 256)
     * @param   int  $collength  Column length from syscolumns
     * @return  string  SQL type
     */
    protected function coltypeToSql(int $baseType, int $collength): string
    {
        switch ($baseType) {
            case 0:
                return 'CHAR(' . $collength . ')';
            case 1:
                return 'SMALLINT';
            case 2:
                return 'INTEGER';
            case 3:
                return 'FLOAT';
            case 4:
                return 'SMALLFLOAT';
            case 5:
                // DECIMAL: precision in high byte, scale in low byte
                $precision = ($collength >> 8) & 0xFF;
                $scale = $collength & 0xFF;
                return "DECIMAL({$precision},{$scale})";
            case 6:
                return 'SERIAL';
            case 7:
                return 'DATE';
            case 8:
                return 'MONEY(' . (($collength >> 8) & 0xFF) . ',' . ($collength & 0xFF) . ')';
            case 10:
                return 'DATETIME YEAR TO SECOND';
            case 12:
                return 'TEXT';
            case 13:
                // VARCHAR
                return 'VARCHAR(' . ($collength & 0xFF) . ')';
            case 14:
                return 'INTERVAL';
            case 15:
                return 'NCHAR(' . $collength . ')';
            case 16:
                return 'NVARCHAR(' . ($collength & 0xFF) . ')';
            case 17:
                return 'INT8';
            case 18:
                return 'SERIAL8';
            case 19:
                return 'SET';
            case 20:
                return 'MULTISET';
            case 21:
                return 'LIST';
            case 40:
                // LVARCHAR
                return 'LVARCHAR(' . $collength . ')';
            case 41:
                // Smart large object text (CLOB) in modern Informix catalogs.
                return 'CLOB';
            case 43:
                return 'LVARCHAR(' . $collength . ')';
            case 45:
                return 'BOOLEAN';
            case 52:
                return 'BIGINT';
            case 53:
                return 'BIGSERIAL';
            default:
                return 'VARCHAR(255)';
        }
    }

    // =========================================================================
    // Column Operations
    // =========================================================================

    protected function buildAddColumnSql(
        string $table,
        string $column,
        string $definition,
        string $comment
    ): string {
        return 'ALTER TABLE ' . $this->quoteName($table)
            . ' ADD ' . $this->quoteName($column) . ' ' . $definition;
    }

    protected function buildDropColumnSql(string $table, string $column): string
    {
        return 'ALTER TABLE ' . $this->quoteName($table) . ' DROP ' . $this->quoteName($column);
    }

    protected function buildModifyColumnSql(
        string $table,
        string $column,
        string $definition,
        string $comment
    ): string {
        return 'ALTER TABLE ' . $this->quoteName($table)
            . ' MODIFY ' . $this->quoteName($column)
            . ' ' . $definition;
    }

    /**
     * Change/rename a column in a table
     *
     * Informix uses RENAME COLUMN for renaming and MODIFY for type changes.
     *
     * @param   string  $table       The table name
     * @param   string  $oldColumn   The current column name
     * @param   string  $newColumn   The new column name
     * @param   string  $definition  The column definition
     * @param   string  $comment     Column comment (ignored in Informix)
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

        // If renaming is needed
        if ($oldColumn !== $newColumn) {
            $renameSql = 'RENAME COLUMN ' . $this->quoteName($table) . '.' . $this->quoteName($oldColumn)
                       . ' TO ' . $this->quoteName($newColumn);
            $this->setQuery($renameSql)->execute();
        }

        // Modify the column definition if provided
        if (!empty($definition)) {
            $modifySql = 'ALTER TABLE ' . $this->quoteName($table)
                . ' MODIFY ' . $this->quoteName($newColumn)
                . ' ' . $definition;
            $this->setQuery($modifySql)->execute();
        }

        return true;
    }

    /**
     * Alias for modifyColumn
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @param   string  $type    The new column type
     * @return  $this
     */
    public function alterColumn($table, $column, $type)
    {
        return $this->modifyColumn($table, $column, $type);
    }

    // =========================================================================
    // Index Operations
    // =========================================================================

    /**
     * Add an index to a table
     *
     * @param   string        $table    The table name
     * @param   string        $name     The index name
     * @param   string|array  $columns  The columns to index
     * @param   bool          $unique   Whether the index is unique
     * @return  bool
     */
    public function addIndex(string $table, string $name, $columns, bool $unique = false): bool
    {
        $table = $this->replacePrefix($table);
        $uniqueStr = $unique ? 'UNIQUE ' : '';

        $columnList = implode(', ', array_map([$this, 'quoteName'], (array) $columns));

        // Informix index names are database-wide. Drop any stale index with the same name first.
        $this->setQuery(
            "SELECT COUNT(*) FROM sysindexes WHERE idxname = " . $this->quote($name)
        );
        if ((int) $this->loadResult() > 0) {
            $this->setQuery('DROP INDEX ' . $this->quoteName($name))->execute();
        }

        $this->setQuery(
            'CREATE ' . $uniqueStr . 'INDEX ' . $this->quoteName($name)
            . ' ON ' . $this->quoteName($table)
            . ' (' . $columnList . ')'
        )->execute();
        return true;
    }

    /**
     * Drop an index from a table
     *
     * @param   string  $table  The table name
     * @param   string  $name   The index name
     * @return  bool
     */
    public function dropIndex(string $table, string $name): bool
    {
        $this->setQuery('DROP INDEX ' . $this->quoteName($name))
             ->execute();
        return true;
    }

    // =========================================================================
    // Auto-Increment Operations
    // =========================================================================

    /**
     * Gets the auto-increment value for the given table
     *
     * @param   string    $table  The table for which to retrieve the auto-increment value
     * @return  int|bool
     */
    public function getAutoIncrement($table)
    {
        $table = strtolower($this->replacePrefix($table));

        // Find the SERIAL column for this table
        $pk = $this->getPrimaryKey($table);
        if (!$pk) {
            return false;
        }

        // Get the max value of the SERIAL column + 1
        $this->setQuery("SELECT MAX($pk) FROM $table");
        $max = $this->loadResult();

        if ($max === null || $max === false) {
            return 1;
        }

        return (int) $max + 1;
    }

    /**
     * Sets the auto-increment value for the given table
     *
     * @param   string  $table  The table for which to set the auto-increment value
     * @param   int     $value  The new auto-increment value
     * @return  bool
     */
    public function setAutoIncrement($table, $value)
    {
        $table = $this->replacePrefix($table);
        $value = max(1, (int) $value);

        // Informix SERIAL can only advance forward, never backward.
        // To reliably set ANY value, we DROP+CREATE the table (resets SERIAL to 1),
        // reinsert existing data, then advance SERIAL to the target value.
        $ddlStatements = $this->getTableDdl($table);
        if (!$ddlStatements) {
            return false;
        }

        // Save existing data
        $this->setQuery("SELECT * FROM $table");
        $rows = $this->loadObjectList();

        // Drop and recreate (resets SERIAL to 1)
        $this->setQuery("DROP TABLE $table")->execute();
        foreach ($ddlStatements as $stmt) {
            $this->setQuery($stmt)->execute();
        }

        // Reinsert saved data with explicit IDs
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $rowArray = (array) $row;
                $cols = array_keys($rowArray);
                $colList = implode(', ', $cols);
                $placeholders = implode(', ', array_fill(0, count($cols), '?'));
                $this->prepare("INSERT INTO $table ($colList) VALUES ($placeholders)")
                    ->bind(array_values($rowArray))
                    ->execute();
            }
        }

        // Advance SERIAL counter to target value (if > 1, beyond current max)
        if ($value > 1) {
            $pk = $this->getPrimaryKey($table);
            if ($pk) {
                try {
                    $this->setQuery("ALTER TABLE $table MODIFY ($pk SERIAL($value))")->execute();
                } catch (\Exception $e) {
                    // Ignore — value may already be at or past target
                }
            }
        }

        return true;
    }

    // =========================================================================
    // Schema / ALTER TABLE Support
    // =========================================================================


    /**
     * Build column definition for ALTER TABLE
     *
     * @param   string  $name        Column name
     * @param   array   $definition  Column definition array
     * @return  string  Column definition SQL fragment
     */
    public function buildAlterColumnDefinition(string $name, array $definition): string
    {
        // Map the type to Informix-compatible type
        $type = $this->mapTypeToInformix($definition['type']);

        $def = "$name $type";

        // Handle auto increment (SERIAL) - must come before nullable check
        if (isset($definition['autoIncrement']) && $definition['autoIncrement']) {
            // For Informix, replace type with SERIAL
            if (stripos($type, 'BIGINT') !== false || stripos($type, 'INT8') !== false) {
                $def = "$name SERIAL8";
            } else {
                $def = "$name SERIAL";
            }
            return $def; // SERIAL columns don't need NOT NULL or DEFAULT
        }

        // Translate zero-date default to NULL
        if (
            isset($definition['default'])
            && self::isZeroDate($definition['default'])
        ) {
            $definition['nullable'] = true;
            $definition['default'] = null;
        }

        // Handle nullable
        if (isset($definition['nullable']) && !$definition['nullable']) {
            $def .= ' NOT NULL';
        }

        // Handle default value
        if (isset($definition['default'])) {
            if ($definition['default'] === null) {
                $def .= ' DEFAULT NULL';
            } elseif ($definition['default'] === 'CURRENT_TIMESTAMP') {
                $def .= ' DEFAULT CURRENT YEAR TO SECOND';
            } elseif (is_string($definition['default'])) {
                $def .= " DEFAULT '" . str_replace("'", "''", $definition['default']) . "'";
            } elseif (is_bool($definition['default'])) {
                $def .= ' DEFAULT ' . ($definition['default'] ? "'t'" : "'f'");
            } else {
                $def .= ' DEFAULT ' . $definition['default'];
            }
        }

        // Informix doesn't support FIRST/AFTER positioning in ALTER TABLE ADD
        // These would need to be handled with table recreation if really needed

        return $def;
    }

    /**
     * Map generic SQL types to Informix types
     *
     * @param   string  $type  Generic SQL type
     * @return  string  Informix-compatible type
     */
    protected function mapTypeToInformix(string $type): string
    {
        // First try abstract type mapping (camelCase types from schema builder)
        $abstractTypes = [
            'tinyInteger', 'smallInteger', 'mediumInteger', 'integer', 'bigInteger',
            'boolean', 'string', 'char', 'tinyText', 'text', 'mediumText', 'longText',
            'float', 'double', 'decimal', 'date', 'time', 'datetime', 'timestamp',
            'timestampTz', 'year', 'binary', 'json', 'uuid', 'ulid', 'ipAddress', 'macAddress',
        ];
        if (in_array($type, $abstractTypes)) {
            return $this->normalizeColumnType($type);
        }

        // Normalize to uppercase for matching
        $upperType = strtoupper(trim($type));

        // Common type mappings
        $typeMap = [
            // Integer types
            'TINYINT'    => 'SMALLINT',
            'MEDIUMINT'  => 'INTEGER',
            'INT'        => 'INTEGER',
            'BIGINT'     => 'INT8',

            // String types
            'TINYTEXT'   => 'TEXT',
            'MEDIUMTEXT' => 'TEXT',
            'LONGTEXT'   => 'TEXT',
            'TEXT'       => 'TEXT',
            'BLOB'       => 'BYTE',
            'MEDIUMBLOB' => 'BYTE',
            'LONGBLOB'   => 'BYTE',

            // Date/Time types
            'TIMESTAMP'  => 'DATETIME YEAR TO SECOND',
            'DATETIME'   => 'DATETIME YEAR TO SECOND',
            'DATE'       => 'DATE',
            'TIME'       => 'DATETIME HOUR TO SECOND',
            'YEAR'       => 'SMALLINT',

            // Other types
            'DOUBLE'     => 'FLOAT',
            'FLOAT'      => 'SMALLFLOAT',
            'BOOLEAN'    => 'SMALLINT',
            'BOOL'       => 'SMALLINT',
        ];

        // Check for direct match
        foreach ($typeMap as $generic => $informix) {
            if (strpos($upperType, $generic) === 0) {
                // For sized types like VARCHAR(255), preserve the size
                if (preg_match('/^' . preg_quote($generic, '/') . '\s*\((\d+)\)/i', $type, $matches)) {
                    $size = (int)$matches[1];

                    // Informix VARCHAR max is 255 without LVARCHAR
                    if ($generic === 'VARCHAR' && $size > 255) {
                        return "LVARCHAR($size)";
                    }

                    // For other types, preserve size
                    if (in_array($generic, ['VARCHAR', 'CHAR', 'DECIMAL', 'NUMERIC'])) {
                        return $type; // Keep original with size
                    }
                }

                return $informix;
            }
        }

        // Handle VARCHAR(n) > 255 → LVARCHAR(n)
        if (preg_match('/^VARCHAR\s*\((\d+)\)/i', $type, $matches)) {
            $size = (int) $matches[1];
            if ($size > 255) {
                return "LVARCHAR($size)";
            }
        }

        // If no mapping found, return original type
        // This handles VARCHAR(n), CHAR(n), DECIMAL(m,n), etc.
        return $type;
    }

    // =========================================================================
    // Server Information
    // =========================================================================

    /**
     * Get database server version information
     *
     * @return  array
     */
    public function getServerInfo()
    {
        try {
            // PDO_INFORMIX doesn't support ATTR_SERVER_VERSION
            $this->setQuery("SELECT DBINFO('version','full') AS v FROM sysmaster:sysdual");
            $version = $this->loadResult();
            if (!$version) {
                $version = 'Unknown';
            }
            $driverVersion = 'Unknown';
            try {
                $driverVersion = $this->connection->getAttribute(\PDO::ATTR_CLIENT_VERSION);
            } catch (\PDOException $e) {
                // Ignore
            }

            return [
                'version' => $version,
                'driver_version' => $driverVersion,
                'comment' => 'Informix'
            ];
        } catch (\Exception $e) {
            return [
                'version' => 'Unknown',
                'driver_version' => 'Unknown',
                'comment' => 'Informix'
            ];
        }
    }

    /**
     * Get the database version
     *
     * @return  string
     */
    public function getVersion()
    {
        $info = $this->getServerInfo();
        return $info['version'];
    }

    /**
     * Gets the database collation in use
     *
     * @return  string|bool
     */
    public function getCollation()
    {
        // Informix uses locales instead of collations
        try {
            $this->setQuery("SELECT DBINFO('dblocale') AS locale FROM sysmaster:sysdual");
            return $this->loadResult();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Gets the database character set of the given table
     *
     * @param   string       $table  The table for which to retrieve the character set
     * @param   string       $field  The field to check (optional)
     * @return  string|bool
     */
    public function getCharacterSet($table, $field = null)
    {
        // Informix uses code sets
        try {
            $this->setQuery("SELECT DBINFO('dbcodeset') AS codeset FROM sysmaster:sysdual");
            return $this->loadResult();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Set the database character set
     *
     * @param   string  $charset  The character set to set
     * @return  bool
     */
    public function setCharacterSet($charset)
    {
        // Informix character sets are set at database creation
        // Cannot be changed dynamically
        return false;
    }

    /**
     * Set the database collation
     *
     * @param   string  $collation  The collation to set
     * @return  bool
     */
    public function setCollation($collation)
    {
        // Informix locales are set at database creation
        // Cannot be changed dynamically
        return false;
    }

    // =========================================================================
    // Table Maintenance
    // =========================================================================

    /**
     * Optimize a table
     *
     * @param   string  $table  The table to optimize
     * @return  bool
     */
    public function optimizeTable($table)
    {
        // Informix doesn't have OPTIMIZE TABLE
        // Use UPDATE STATISTICS instead
        try {
            $table = $this->replacePrefix($table);
            $this->setQuery('UPDATE STATISTICS FOR TABLE ' . $this->quoteName($table));
            $this->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Analyze a table
     *
     * @param   string  $table  The table to analyze
     * @return  bool
     */
    public function analyzeTable($table)
    {
        return $this->optimizeTable($table);
    }

    /**
     * Repair a table (not supported in Informix)
     *
     * @param   string  $table  The table to repair
     * @return  bool
     */
    public function repairTable($table)
    {
        // Informix doesn't have REPAIR TABLE
        return false;
    }

    /**
     * Check a table (not supported in Informix)
     *
     * @param   string  $table  The table to check
     * @return  bool
     */
    public function checkTable($table)
    {
        // Informix doesn't have CHECK TABLE
        // Use oncheck utility instead (command-line tool)
        return false;
    }

    // =========================================================================
    // Database Operations
    // =========================================================================

    /**
     * Get the current database name
     *
     * @return  string
     */
    public function getDatabase()
    {
        try {
            $this->setQuery("SELECT DBINFO('dbname') AS dbname FROM sysmaster:sysdual");
            return $this->loadResult();
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Get list of all databases
     *
     * @return  array
     */
    public function getDatabaseList()
    {
        try {
            $this->setQuery("SELECT name FROM sysdatabases ORDER BY name");
            return $this->loadColumn();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Create a database (requires DBA privileges)
     *
     * @param   string  $name  The database name
     * @return  bool
     */
    public function createDatabase($name)
    {
        try {
            $this->setQuery('CREATE DATABASE ' . $this->quoteName($name));
            $this->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Drop a database (requires DBA privileges)
     *
     * @param   string  $name  The database name
     * @return  bool
     */
    public function dropDatabase($name)
    {
        try {
            $this->setQuery('DROP DATABASE ' . $this->quoteName($name));
            $this->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Select a database for use (not supported in Informix)
     *
     * @param   string  $database  The name of the database to select for use
     * @return  bool
     */
    public function select($database)
    {
        // Informix requires reconnection to switch databases
        return false;
    }

    /**
     * Gets the database engine of the given table (not applicable for Informix)
     *
     * @param   string       $table  The table for which to retrieve the engine type
     * @return  string|bool
     */
    public function getEngine($table)
    {
        // Informix doesn't have storage engines like MySQL
        return 'informix';
    }


    // =========================================================================
    // Foreign Key Operations
    // =========================================================================

    /**
     * Gets foreign key constraints for a table
     *
     * @param   string  $table  The table name
     * @return  array   Array of foreign key constraint objects
     */
    public function getForeignKeys($table)
    {
        $table = strtolower($this->replacePrefix($table));

        // Get table ID
        $query = $this->getQuery()
            ->select('tabid')
            ->from('systables')
            ->whereEquals('tabname', $table);

        $this->setQuery($query->toString());
        $tabid = $this->loadResult();

        if (!$tabid) {
            return [];
        }

        try {
            // Get FK constraints with local index, referenced index, and referenced table
            $sql = "SELECT
                        c.constrname,
                        c.idxname,
                        r.ptabid,
                        pc.idxname as pidxname,
                        r.delrule
                    FROM sysconstraints c
                    JOIN sysreferences r ON c.constrid = r.constrid
                    JOIN sysconstraints pc ON r.primary = pc.constrid
                    WHERE c.tabid = " . (int) $tabid . "
                    AND c.constrtype = 'R'";

            $this->setQuery($sql);
            $constraints = $this->loadObjectList();

            if (empty($constraints)) {
                return [];
            }

            $foreignKeys = [];
            foreach ($constraints as $fk) {
                // Get local columns from the FK index
                $localCols = $this->getIndexColumns((int) $tabid, trim($fk->idxname));
                // Get referenced table name
                $refTableName = $this->getTableNameById((int) $fk->ptabid);
                // Get referenced columns from the primary index
                $refCols = $this->getIndexColumns((int) $fk->ptabid, trim($fk->pidxname));

                // Map delrule code to action string
                $deleteAction = 'RESTRICT';
                $delrule = trim($fk->delrule ?? '');
                if ($delrule === 'C') {
                    $deleteAction = 'CASCADE';
                } elseif ($delrule === 'N') {
                    $deleteAction = 'SET NULL';
                }

                $foreignKeys[] = (object) [
                    'name'            => trim($fk->constrname),
                    'columns'         => $localCols,
                    'foreign_table'   => $refTableName,
                    'foreign_columns' => $refCols,
                    'on_update'       => 'RESTRICT',  // Informix doesn't support ON UPDATE
                    'on_delete'       => $deleteAction,
                ];
            }

            return $foreignKeys;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get column names for an index by looking up sysindexes
     *
     * @param   int     $tabid    Table ID
     * @param   string  $idxname  Index name
     * @return  array   Column names
     */
    protected function getIndexColumns(int $tabid, string $idxname): array
    {
        $idxname = trim($idxname);
        $sql = "SELECT part1, part2, part3, part4, part5, part6, part7, part8
                FROM sysindexes
                WHERE tabid = {$tabid} AND TRIM(idxname) = '{$idxname}'";

        $this->setQuery($sql);
        $idx = $this->loadObject();

        if (!$idx) {
            return [];
        }

        $columns = [];
        for ($i = 1; $i <= 8; $i++) {
            $partKey = 'part' . $i;
            $colno = (int) ($idx->$partKey ?? 0);
            if ($colno === 0) {
                break;
            }
            // Negative colno means descending order
            $colno = abs($colno);

            $sql = "SELECT colname FROM syscolumns WHERE tabid = {$tabid} AND colno = {$colno}";
            $this->setQuery($sql);
            $colname = $this->loadResult();
            if ($colname) {
                $columns[] = trim($colname);
            }
        }

        return $columns;
    }

    /**
     * Get table name by table ID
     *
     * @param   int     $tabid  Table ID
     * @return  string  Table name
     */
    protected function getTableNameById(int $tabid): string
    {
        $sql = "SELECT tabname FROM systables WHERE tabid = {$tabid}";
        $this->setQuery($sql);
        return trim($this->loadResult() ?? '');
    }

    /**
     * Add a foreign key constraint
     *
     * @param   string  $table           The table name
     * @param   string  $constraint      The constraint name
     * @param   array   $columns         Local columns
     * @param   string  $foreignTable    Referenced table
     * @param   array   $foreignColumns  Referenced columns
     * @param   string  $onUpdate        ON UPDATE action
     * @param   string  $onDelete        ON DELETE action
     * @return  bool
     */
    public function addForeignKey(
        $table,
        $constraint,
        $columns,
        $foreignTable,
        $foreignColumns,
        $onUpdate = 'RESTRICT',
        $onDelete = 'RESTRICT'
    ) {
        $table = $this->replacePrefix($table);
        $foreignTable = $this->replacePrefix($foreignTable);

        $columnList = implode(', ', array_map([$this, 'quoteName'], (array) $columns));
        $foreignColumnList = implode(', ', array_map([$this, 'quoteName'], (array) $foreignColumns));

        // Informix syntax: constraint name goes at the end
        $sql = 'ALTER TABLE ' . $this->quoteName($table)
             . ' ADD CONSTRAINT FOREIGN KEY (' . $columnList . ')'
             . ' REFERENCES ' . $this->quoteName($foreignTable) . ' (' . $foreignColumnList . ')';

        // Informix syntax for referential actions
        if ($onDelete !== 'RESTRICT') {
            $sql .= ' ON DELETE ' . $onDelete;
        }

        $sql .= ' CONSTRAINT ' . $this->quoteName($constraint);

        try {
            $this->setQuery($sql);
            $this->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function dropForeignKey($table, $constraint)
    {
        $table = $this->replacePrefix($table);

        try {
            $this->setQuery(
                'ALTER TABLE ' . $this->quoteName($table)
                . ' DROP CONSTRAINT ' . $this->quoteName($constraint)
            );
            $this->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // =========================================================================
    // Primary Key Operations
    // =========================================================================

    public function getPrimaryKey($table)
    {
        $columns = $this->getPrimaryKeyColumns($table);

        if (empty($columns)) {
            return false;
        }

        // Return first PK column (matches MySQL behavior for single-column PKs)
        return $columns[0];
    }

    /**
     * Get primary key column names
     *
     * @param   string  $table  The table name
     * @return  array   Array of column names in the primary key
     */
    public function getPrimaryKeyColumns($table): array
    {
        $table = strtolower($this->replacePrefix($table));

        // Get table ID
        $query = $this->getQuery()
            ->select('tabid')
            ->from('systables')
            ->whereEquals('tabname', $table);

        $this->setQuery($query->toString());
        $tabid = $this->loadResult();

        if (!$tabid) {
            return [];
        }

        // Get primary key index (idxtype = 'U' means unique/primary)
        $query = $this->getQuery()
            ->select(['part1', 'part2', 'part3', 'part4', 'part5', 'part6', 'part7', 'part8'])
            ->from('sysindexes')
            ->whereEquals('tabid', $tabid)
            ->whereEquals('idxtype', 'U');

        $this->setQuery($query->toString());
        $index = $this->loadObject();

        if (!$index) {
            return [];
        }

        // Get column names for the index parts
        $columns = [];
        for ($i = 1; $i <= 8; $i++) {
            $partField = "part{$i}";
            $colno = $index->$partField ?? 0;

            if ($colno > 0) {
                // Query syscolumns for column name
                $query = $this->getQuery()
                    ->select('colname')
                    ->from('syscolumns')
                    ->whereEquals('tabid', $tabid)
                    ->whereEquals('colno', $colno);

                $this->setQuery($query->toString());
                $colname = $this->loadResult();

                if ($colname) {
                    $columns[] = $colname;
                }
            }
        }

        return $columns;
    }

    /**
     * Get primary key constraint name for a table
     *
     * Informix stores constraint names in sysconstraints.
     * This method looks up the PK constraint name for DROP CONSTRAINT operations.
     *
     * Uses raw PDO to avoid interfering with the current query state (important
     * when called during ALTER TABLE compilation).
     *
     * @param   string  $table  The table name
     * @return  string|null  Constraint name or null if no PK exists
     */
    public function getPrimaryKeyConstraintName(string $table): ?string
    {
        $tableName = strtolower(str_replace('"', '', $this->replacePrefix($table)));

        $sql = "SELECT c.constrname FROM sysconstraints c"
            . " JOIN systables t ON c.tabid = t.tabid"
            . " WHERE t.tabname = ? AND c.constrtype = 'P'";

        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$tableName]);
            $pkName = $stmt->fetchColumn();
            $stmt->closeCursor();
            unset($stmt);

            return $pkName ? trim($pkName) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Add a primary key constraint
     *
     * @param   string        $table    The table name
     * @param   string|array  $columns  The columns for the primary key
     * @return  bool
     */
    public function addPrimaryKey(string $table, $columns): bool
    {
        $table = $this->replacePrefix($table);
        $columnList = implode(', ', array_map([$this, 'quoteName'], (array) $columns));

        // Informix requires ADD CONSTRAINT ... CONSTRAINT name syntax
        $constraintName = 'pk_' . strtolower(str_replace('"', '', $table));

        try {
            $this->setQuery(
                'ALTER TABLE ' . $this->quoteName($table)
                . ' ADD CONSTRAINT PRIMARY KEY (' . $columnList . ')'
                . ' CONSTRAINT ' . $this->quoteName($constraintName)
            );
            $this->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Drop a primary key constraint
     *
     * @param   string  $table  The table name
     * @return  bool
     */
    public function dropPrimaryKey(string $table): bool
    {
        $table = $this->replacePrefix($table);

        // Informix requires DROP CONSTRAINT by name.
        // Look up the primary key constraint name from sysconstraints.
        $this->setQuery(
            "SELECT c.constrname FROM sysconstraints c"
            . " JOIN systables t ON c.tabid = t.tabid"
            . " WHERE t.tabname = " . $this->quote(strtolower($table))
            . " AND c.constrtype = 'P'"
        );
        $constraintName = $this->loadResult();

        if (!$constraintName) {
            return false;
        }

        try {
            $this->setQuery(
                'ALTER TABLE ' . $this->quoteName($table)
                . ' DROP CONSTRAINT '
                . $this->quoteName(trim($constraintName))
            );
            $this->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // =========================================================================
    // View Operations
    // =========================================================================

    /**
     * Creates or replaces a database view
     *
     * @param   string  $name       The view name
     * @param   string  $selectSql  The SELECT statement
     * @param   array   $options    Additional options (unused in Informix)
     * @return  bool
     */
    public function createOrReplaceView($name, $selectSql, array $options = [])
    {
        $viewName = $this->replacePrefix($name);
        $selectSql = str_replace('#__', $this->tablePrefix, $selectSql);

        // Informix doesn't have CREATE OR REPLACE VIEW
        // Drop first if exists, then create
        if ($this->viewExists($name)) {
            $this->dropView($name, false);
        }

        try {
            $this->setQuery('CREATE VIEW ' . $this->quoteName($viewName) . ' AS ' . $selectSql);
            $this->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Drops a database view
     *
     * @param   string  $name      The view name
     * @param   bool    $ifExists  Whether to check existence first
     * @return  bool
     */
    public function dropView($name, $ifExists = true)
    {
        $viewName = $this->replacePrefix($name);

        if ($ifExists && !$this->viewExists($name)) {
            return true;
        }

        try {
            $this->setQuery('DROP VIEW ' . $this->quoteName($viewName));
            $this->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Checks if a view exists
     *
     * @param   string  $name  The view name
     * @return  bool
     */
    public function viewExists($name)
    {
        $viewName = strtolower($this->replacePrefix($name));

        try {
            $query = $this->getQuery()
                ->select('COUNT(*)')
                ->from('systables')
                ->whereEquals('tabname', $viewName)
                ->whereEquals('tabtype', 'V');

            $this->setQuery($query->toString());
            return ((int) $this->loadResult() > 0);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Returns a list of all views in the current database
     *
     * @return  array  Array of view names
     */
    public function getViews()
    {
        try {
            $query = $this->getQuery()
                ->select('tabname')
                ->from('systables')
                ->whereEquals('tabtype', 'V')
                ->where('tabid', '>', 99)  // Exclude system views
                ->order('tabname', 'asc');

            $this->setQuery($query->toString());
            return $this->loadColumn() ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    // =========================================================================
    // Sequence Operations (Informix supports sequences)
    // =========================================================================

    /**
     * Returns a list of all sequences in the current database
     *
     * @return  array  Array of sequence names
     */
    public function getSequences()
    {
        try {
            $query = $this->getQuery()
                ->select('tabname')
                ->from('systables')
                ->whereEquals('tabtype', 'Q')  // Q = Sequence
                ->order('tabname', 'asc');

            $this->setQuery($query->toString());
            return $this->loadColumn() ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Creates a new sequence
     *
     * @param   string  $name       The sequence name
     * @param   int     $start      Starting value
     * @param   int     $increment  Increment value
     * @param   array   $options    Additional options
     * @return  bool
     */
    public function createSequence($name, $start = 1, $increment = 1, array $options = [])
    {
        $name = $this->replacePrefix($name);

        try {
            $sql = 'CREATE SEQUENCE ' . $this->quoteName($name)
                 . ' START WITH ' . (int) $start
                 . ' INCREMENT BY ' . (int) $increment;

            if (isset($options['minvalue'])) {
                $sql .= ' MINVALUE ' . (int) $options['minvalue'];
            }

            if (isset($options['maxvalue'])) {
                $sql .= ' MAXVALUE ' . (int) $options['maxvalue'];
            }

            if (isset($options['cycle']) && $options['cycle']) {
                $sql .= ' CYCLE';
            }

            $this->setQuery($sql);
            $this->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Drops a sequence
     *
     * @param   string  $name      The sequence name
     * @param   bool    $ifExists  Whether to check existence first
     * @return  bool
     */
    public function dropSequence($name, $ifExists = true)
    {
        $name = $this->replacePrefix($name);

        if ($ifExists && !$this->sequenceExists($name)) {
            return true;
        }

        try {
            $this->setQuery('DROP SEQUENCE ' . $this->quoteName($name));
            $this->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Checks if a sequence exists
     *
     * @param   string  $name  The sequence name
     * @return  bool
     */
    public function sequenceExists($name)
    {
        $name = strtolower($this->replacePrefix($name));

        try {
            $query = $this->getQuery()
                ->select('COUNT(*)')
                ->from('systables')
                ->whereEquals('tabname', $name)
                ->whereEquals('tabtype', 'Q');

            $this->setQuery($query->toString());
            return ((int) $this->loadResult() > 0);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Gets the next value from a sequence
     *
     * @param   string  $name  The sequence name
     * @return  int
     */
    public function nextSequenceValue($name)
    {
        $name = $this->replacePrefix($name);

        try {
            $this->setQuery('SELECT ' . $this->quoteName($name) . '.NEXTVAL FROM sysmaster:sysdual');
            return (int) $this->loadResult();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Gets the current value from a sequence
     *
     * @param   string  $name  The sequence name
     * @return  int
     */
    public function currentSequenceValue($name)
    {
        $name = $this->replacePrefix($name);

        try {
            $this->setQuery('SELECT ' . $this->quoteName($name) . '.CURRVAL FROM sysmaster:sysdual');
            return (int) $this->loadResult();
        } catch (\Exception $e) {
            return 0;
        }
    }

    // =========================================================================
    // Additional Utility Methods
    // =========================================================================


    /**
     * Get server status variables (limited support)
     *
     * @return  array
     */
    public function getServerStatus()
    {
        // Informix doesn't have a direct equivalent to MySQL's SHOW STATUS
        // Return basic information
        return [
            'version' => $this->getVersion(),
            'database' => $this->getDatabase(),
        ];
    }

    /**
     * Get server variables (limited support)
     *
     * @return  array
     */
    public function getServerVariables()
    {
        // Informix doesn't have a direct equivalent to MySQL's SHOW VARIABLES
        // Return basic information
        return [
            'version' => $this->getVersion(),
            'dblocale' => $this->getCollation(),
            'dbcodeset' => $this->getCharacterSet(null),
        ];
    }

    // =========================================================================
    // String Functions - Informix SQL Syntax
    // =========================================================================

    /**
     * Returns the SQL for concatenating strings
     *
     * Informix uses the || operator for string concatenation.
     *
     * @param   array  $strings  Array of column names or quoted strings to concatenate
     * @return  string
     */
    public function sqlConcat(array $strings): string
    {
        return '(' . implode(' || ', $strings) . ')';
    }

    /**
     * Returns the SQL for concatenating strings with a separator
     *
     * Informix doesn't have CONCAT_WS, so we manually insert separators.
     *
     * @param   string  $separator  The separator string
     * @param   array   $strings    Array of column names or quoted strings to concatenate
     * @return  string
     */
    public function sqlConcatWs(string $separator, array $strings): string
    {
        if (empty($strings)) {
            return "''";
        }

        $quotedSep = $this->quote($separator);
        return '(' . implode(' || ' . $quotedSep . ' || ', $strings) . ')';
    }

    /**
     * Returns the SQL for extracting a substring based on a delimiter
     *
     * Informix doesn't have SUBSTRING_INDEX. This is a simplified implementation.
     *
     * @param   string  $str    The string expression (column or literal)
     * @param   string  $delim  The delimiter to search for
     * @param   int     $count  The occurrence count (positive = from left, negative = from right)
     * @return  string
     */
    public function sqlSubstringIndex(string $str, string $delim, int $count): string
    {
        // Simplified: just return the string before first delimiter for count=1
        // Full implementation would require complex string manipulation
        if ($count === 1) {
            $quotedDelim = $this->quote($delim);
            return 'SUBSTR(' . $str . ', 1, INSTR(' . $str . ', ' . $quotedDelim . ') - 1)';
        }

        // For other cases, return the original string (limitation)
        return $str;
    }

    /**
     * Returns the SQL keyword for REPLACE (upsert)
     *
     * Informix doesn't have REPLACE INTO. Use MERGE instead.
     *
     * @return  string
     */
    public function sqlReplace(): string
    {
        // Informix doesn't support REPLACE INTO
        // Caller should use MERGE or INSERT with ON DUPLICATE KEY logic
        throw new \RuntimeException('REPLACE INTO is not supported in Informix. Use MERGE instead.');
    }

    /**
     * Returns the SQL for REGEXP matching
     *
     * Informix uses MATCHES for pattern matching (similar to LIKE with wildcards).
     *
     * @param   string  $str      The string expression
     * @param   string  $pattern  The pattern to match
     * @return  string
     */
    public function sqlRegexp(string $column, string $pattern, bool $not = false): string
    {
        $op = $not ? ' NOT MATCHES ' : ' MATCHES ';
        return $column . $op . $this->quote($pattern);
    }

    /**
     * Returns the SQL for formatting dates
     *
     * Informix uses TO_CHAR() for date formatting.
     *
     * @param   string  $date    The date column or expression
     * @param   string  $format  The format string (MySQL style, will be converted)
     * @return  string
     */
    public function sqlDateFormat(string $date, string $format): string
    {
        // Convert MySQL format to Informix format
        // This is a simplified conversion
        $informixFormat = str_replace(
            ['%Y', '%m', '%d', '%H', '%i', '%s'],
            ['%Y', '%m', '%d', '%H', '%M', '%S'],
            $format
        );

        return 'TO_CHAR(' . $date . ', ' . $this->quote($informixFormat) . ')';
    }

    /**
     * Returns the SQL for adding an interval to a date
     *
     * @param   string  $date      The date column or expression
     * @param   int     $interval  The interval value
     * @param   string  $unit      The interval unit (DAY, MONTH, YEAR, etc.)
     * @return  string
     */
    public function sqlDateAdd(string $date, int $value, string $unit = 'DAY'): string
    {
        $unit = strtoupper($unit);
        return '(' . $date . ' + INTERVAL(' . $value . ') ' . $unit . ' TO ' . $unit . ')';
    }

    /**
     * Returns the SQL for subtracting an interval from a date
     *
     * @param   string  $date      The date column or expression
     * @param   int     $interval  The interval value
     * @param   string  $unit      The interval unit (DAY, MONTH, YEAR, etc.)
     * @return  string
     */
    public function sqlDateSub(string $date, int $value, string $unit = 'DAY'): string
    {
        $unit = strtoupper($unit);
        return '(' . $date . ' + INTERVAL(' . $value . ') ' . $unit . ' TO ' . $unit . ')';
    }

    /**
     * Returns the SQL for extracting year from a date
     *
     * @param   string  $date  The date column or expression
     * @return  string
     */
    public function sqlYear(string $date): string
    {
        return 'YEAR(' . $date . ')';
    }

    /**
     * Returns the SQL for extracting month from a date
     *
     * @param   string  $date  The date column or expression
     * @return  string
     */
    public function sqlMonth(string $date): string
    {
        return 'MONTH(' . $date . ')';
    }

    /**
     * Format a boolean value as a SQL literal
     *
     * Uses SMALLINT 0/1 for cross-database consistency with MySQL/SQLite.
     *
     * @param   bool  $value  The boolean value
     * @return  string  The SQL literal
     */
    public function formatBooleanLiteral(bool $value): string
    {
        return $value ? '1' : '0';
    }

    /**
     * Returns the SQL for converting a date to Unix timestamp
     *
     * Informix doesn't have a direct equivalent. This is an approximation.
     *
     * @param   string  $date  The date column or expression
     * @return  string
     */
    public function sqlUnixTimestamp(string $date): string
    {
        // Calculate seconds since epoch (1970-01-01)
        return "(" . $date . " - DATETIME(1970-01-01) YEAR TO SECOND)::INTERVAL SECOND(10) TO SECOND";
    }

    /**
     * Returns the SQL for INSERT IGNORE syntax
     *
     * Informix doesn't have INSERT IGNORE. Return standard INSERT.
     *
     * @return  string
     */
    public function sqlInsertIgnore(): string
    {
        // Informix doesn't support INSERT IGNORE
        // Caller should handle duplicates differently
        return 'INSERT';
    }

    /**
     * Returns the SQL for JSON table functions
     *
     * Informix has limited JSON support. This is a placeholder.
     *
     * @return  string
     */
    public function sqlJsonTable(): string
    {
        throw new \RuntimeException('JSON table functions are not fully supported in Informix.');
    }

    // =========================================================================
    // Feature Detection Methods
    // =========================================================================

    /**
     * Check if this database supports sequences
     *
     * @return  bool  True - Informix supports sequences
     */
    public function supportsSequences(): bool
    {
        return true;
    }

    /**
     * Check if CHECK constraints are supported
     *
     * @return  bool  True - Informix supports CHECK constraints
     */
    public function supportsCheckConstraints(): bool
    {
        return true;
    }

    /**
     * Check if window functions are supported
     *
     * @return  bool  True - Informix supports window functions (since 12.10)
     */
    public function supportsWindowFunctions(): bool
    {
        return true;
    }

    /**
     * Check if Common Table Expressions (CTE) are supported
     *
     * @return  bool  True - Informix supports CTEs via WITH clause
     */
    public function supportsCTE(): bool
    {
        return true;
    }

    // =========================================================================
    // Utility and Helper Methods
    // =========================================================================

    /**
     * Quote a single identifier
     *
     * @param   string  $identifier  The identifier to quote
     * @return  string
     */
    public function quoteIdentifier(string $identifier): string
    {
        // Informix does not use quoted identifiers
        return $identifier;
    }

    /**
     * Informix uses bare identifiers by default.
     *
     * @return  bool
     */
    public function usesQuotedIdentifiers(): bool
    {
        return false;
    }

    /**
     * Get server uptime (limited support)
     *
     * @return  int|null
     */
    public function getUptime()
    {
        // Informix doesn't expose uptime easily
        return null;
    }

    /**
     * Get slow query count (not supported)
     *
     * @return  int
     */
    public function getSlowQueries()
    {
        return 0;
    }

    /**
     * Get global status variables (limited)
     *
     * @return  array
     */
    public function getGlobalStatus()
    {
        return $this->getServerStatus();
    }

    /**
     * Get global configuration variables (limited)
     *
     * @return  array
     */
    public function getGlobalVariables()
    {
        return $this->getServerVariables();
    }

    /**
     * Get connection information
     *
     * @return  array
     */
    public function getConnectionInfo()
    {
        return [
            'driver' => 'informix',
            'version' => $this->getVersion(),
            'database' => $this->getDatabase(),
        ];
    }

    /**
     * Get connection statistics (not supported)
     *
     * @return  array
     */
    public function getConnectionStats()
    {
        return [];
    }

    /**
     * Get table I/O statistics (not supported)
     *
     * @return  array
     */
    public function getTableIoStats()
    {
        return [];
    }

    /**
     * Populate sequential values in a column (utility method)
     *
     * @param   string       $table    Table name
     * @param   string       $column   Column name
     * @param   string|null  $orderBy  Order by clause
     * @return  bool
     */
    public function populateSequentialValues(string $table, string $column, ?string $orderBy = null): bool
    {
        // Not implemented for Informix
        return false;
    }

    // =========================================================================
    // Schema Building Methods
    // =========================================================================

    /**
     * Apply length/precision modifiers for Informix column types
     *
     * Overrides the base class to handle Informix's VARCHAR 255-byte limit:
     * when a string/char length exceeds 255, LVARCHAR is used instead.
     *
     * @param   string  $abstractType  The original abstract type name
     * @param   string  $nativeType    The mapped database type
     * @param   array   $modifiers     Column modifiers
     * @return  string  The type with modifiers applied
     */
    protected function applyColumnModifiers(
        string $abstractType,
        string $nativeType,
        array $modifiers
    ): string {
        switch ($abstractType) {
            case 'string':
            case 'char':
                $length = $modifiers['length'] ?? 255;
                // Informix VARCHAR max is 255; use LVARCHAR for larger
                if ($length > 255) {
                    return "LVARCHAR({$length})";
                }
                return "{$nativeType}({$length})";

            default:
                return parent::applyColumnModifiers(
                    $abstractType,
                    $nativeType,
                    $modifiers
                );
        }
    }

    /**
     * Map abstract column types to Informix-specific SQL types
     *
     * Delegates abstract types to the parent (which uses $this->typeMap
     * and applyColumnModifiers()), then handles MySQL-style concrete
     * type fallthrough for migration compatibility.
     *
     * @param   string  $type       Abstract type name (e.g. 'string', 'integer')
     * @param   array   $modifiers  Column modifiers (length, precision, scale)
     * @return  string  Informix SQL type
     */
    public function normalizeColumnType(string $type, array $modifiers = []): string
    {
        if ($this->getSchemaGrammar()->getTypeMapping($type) !== null) {
            return parent::normalizeColumnType($type, $modifiers);
        }

        // Handle MySQL SET() and ENUM() types
        if (
            preg_match('/^SET\s*\(/i', $type)
            || preg_match('/^ENUM\s*\(/i', $type)
        ) {
            return 'VARCHAR(255)';
        }

        // Handle concrete MySQL-style types with display widths
        $cleaned = trim(str_ireplace(' UNSIGNED', '', $type));

        if (preg_match('/^BIGINT(\(\d+\))?$/i', $cleaned)) {
            return 'BIGINT';
        }
        if (preg_match('/^INT(EGER)?(\(\d+\))?$/i', $cleaned)) {
            return 'INTEGER';
        }
        if (preg_match('/^MEDIUMINT(\(\d+\))?$/i', $cleaned)) {
            return 'INTEGER';
        }
        if (preg_match('/^SMALLINT(\(\d+\))?$/i', $cleaned)) {
            return 'SMALLINT';
        }
        if (preg_match('/^TINYINT(\(\d+\))?$/i', $cleaned)) {
            return 'SMALLINT';
        }
        if (strcasecmp($cleaned, 'DATETIME') === 0) {
            return 'DATETIME YEAR TO SECOND';
        }
        if (preg_match('/^(TINY|MEDIUM|LONG)?BLOB$/i', $cleaned)) {
            return 'BYTE';
        }
        if (preg_match('/^(TINY|MEDIUM|LONG)?TEXT$/i', $cleaned)) {
            return 'TEXT';
        }

        // Already a concrete Informix type
        return $type;
    }

    /**
     * Informix uses CURRENT YEAR TO SECOND instead of CURRENT_TIMESTAMP
     *
     * Must match the precision of the DATETIME column type used by the grammar.
     */
    public function currentTimestampDefault(): string
    {
        return 'CURRENT YEAR TO SECOND';
    }

    /**
     * Informix does not support IF NOT EXISTS for CREATE TABLE
     */
    public function supportsIfNotExists(): bool
    {
        return false;
    }

    /**
     * Informix does not support IF NOT EXISTS for CREATE INDEX
     */
    public function supportsIfNotExistsForIndex(): bool
    {
        return false;
    }

    /**
     * Build auto-increment column definition
     *
     * Informix uses SERIAL for auto-increment, which implicitly includes PRIMARY KEY.
     *
     * @param   string  $quotedName  Quoted column name
     * @param   string  $type        Column type (ignored — SERIAL is always INTEGER)
     * @return  string
     */
    public function buildAutoIncrementColumn(string $quotedName, string $type): string
    {
        return "$quotedName SERIAL PRIMARY KEY";
    }

    /**
     * SERIAL implicitly includes PRIMARY KEY in Informix
     */
    public function autoIncrementIncludesPrimaryKey(): bool
    {
        return true;
    }

    /**
     * Build index definition for inline CREATE TABLE
     *
     * Informix doesn't support inline INDEX in CREATE TABLE,
     * so return null to force separate CREATE INDEX statements.
     *
     * @param   string  $quotedName  Quoted index name
     * @param   string  $columnList  Column list
     * @return  string|null  Always null for Informix
     */
    public function buildIndexDefinition(string $quotedName, string $columnList): ?string
    {
        return null;
    }

    /**
     * Build fulltext index definition (not supported in Informix)
     *
     * @param   string  $quotedName  Quoted index name
     * @param   string  $columnList  Column list
     * @return  string|null  Always null
     */
    public function buildFulltextIndexDefinition(string $quotedName, string $columnList): ?string
    {
        return null;
    }

    /**
     * Build unique constraint definition for inline CREATE TABLE
     *
     * @param   string  $quotedName  Quoted constraint name
     * @param   string  $columnList  Column list
     * @return  string
     */
    public function buildUniqueConstraint(string $quotedName, string $columnList): string
    {
        return "UNIQUE ($columnList) CONSTRAINT $quotedName";
    }

    /**
     * Build foreign key constraint definition for inline CREATE TABLE
     *
     * Informix places the constraint name at the end of the definition.
     *
     * @param   array   $fk           Foreign key definition array
     * @param   string  $tableName    The table being created
     * @return  string
     */
    public function buildForeignKeyDefinition(array $fk, string $tableName): string
    {
        $refTable = $this->replacePrefix($fk['referencedTable']);
        $fkName = !empty($fk['name']) ? $fk['name'] : "fk_{$tableName}_{$fk['column']}";
        $fkColumn = $this->quoteName($fk['column']);
        $refTableQuoted = $this->quoteName($refTable);
        $refColumn = $this->quoteName($fk['referencedColumn']);

        // Informix syntax: FOREIGN KEY (...) REFERENCES ... ON DELETE ... CONSTRAINT name
        $sql = "FOREIGN KEY ($fkColumn) REFERENCES $refTableQuoted ($refColumn)";

        $onDelete = strtoupper($fk['onDelete'] ?? 'RESTRICT');
        if ($onDelete === 'CASCADE') {
            $sql .= ' ON DELETE CASCADE';
        }

        $sql .= ' CONSTRAINT ' . $this->quoteName($fkName);

        return $sql;
    }

    /**
     * Informix doesn't have MySQL-style table options (ENGINE, CHARSET, etc.)
     */
    public function buildTableOptions(
        ?string $engine = null,
        ?string $charset = null,
        ?string $collation = null
    ): string {
        return '';
    }

    /**
     * Get schema grammar object
     *
     * @return  \Hubzero\Database\Drivers\Informix\InformixGrammar
     */
    public function getSchemaGrammar()
    {
        return $this->makeSchemaGrammarFromRegistry();
    }

    // =========================================================================
    // Column Positioning Methods (Not Supported in Informix)
    // =========================================================================

    public function addColumnFirst(string $table, string $column, string $definition, string $comment = ''): bool
    {
        throw new \RuntimeException('Column positioning (FIRST) is not supported in Informix.');
    }

    /**
     * Add column at last position (same as regular add)
     *
     * @param   string  $table   Table name
     * @param   string  $column  Column name
     * @param   string  $type    Column type
     * @return  bool
     */
    public function addColumnLast(string $table, string $column, string $definition, string $comment = ''): bool
    {
        return $this->addColumn($table, $column, $definition, $comment);
    }

    /**
     * Add column after another column (not supported in Informix)
     *
     * @param   string  $table        Table name
     * @param   string  $column       Column name
     * @param   string  $definition   Column definition
     * @param   string  $afterColumn  Column to add after
     * @param   string  $comment      Column comment (ignored)
     * @return  bool
     */
    public function addColumnAfter(
        string $table,
        string $column,
        string $definition,
        string $afterColumn,
        string $comment = ''
    ): bool {
        throw new \RuntimeException('Column positioning (AFTER) is not supported in Informix.');
    }

    public function addColumnBefore(
        string $table,
        string $column,
        string $definition,
        string $beforeColumn,
        string $comment = ''
    ): bool {
        throw new \RuntimeException('Column positioning (BEFORE) is not supported in Informix.');
    }

    public function modifyColumnFirst(string $table, string $column, string $definition, string $comment = ''): bool
    {
        throw new \RuntimeException('Column positioning (FIRST) is not supported in Informix.');
    }

    public function modifyColumnAfter(
        string $table,
        string $column,
        string $definition,
        string $afterColumn,
        string $comment = ''
    ): bool {
        throw new \RuntimeException('Column positioning (AFTER) is not supported in Informix.');
    }

    public function modifyColumnBefore(
        string $table,
        string $column,
        string $definition,
        string $beforeColumn,
        string $comment = ''
    ): bool {
        throw new \RuntimeException('Column positioning (BEFORE) is not supported in Informix.');
    }

    // =========================================================================
    // Special Column Methods (Not Supported in Informix)
    // =========================================================================

    /**
     * Add auto-increment primary key (uses SERIAL)
     *
     * @param   string  $table   Table name
     * @param   string  $column  Column name (default: 'id')
     * @return  bool
     */
    public function addAutoIncrementPrimaryKey(
        string $table,
        string $column = 'id',
        bool $first = false,
        bool $useBigInt = true
    ): bool {
        $table = $this->replacePrefix($table);

        // Check if table exists
        if (!$this->tableExists($table)) {
            return false;
        }

        // Idempotent: if column already exists, return true
        if ($this->tableHasField($table, $column)) {
            return true;
        }

        // Check if table has data — Informix cannot add SERIAL to a populated table
        $this->setQuery("SELECT COUNT(*) FROM $table");
        $count = (int) $this->loadResult();

        if ($count === 0) {
            // Empty table: simple ALTER TABLE ADD SERIAL
            $this->setQuery("ALTER TABLE $table ADD $column SERIAL PRIMARY KEY");
            $this->execute();
            return true;
        }

        // Populated table: multi-step approach
        // 1. Add nullable INTEGER column
        $this->setQuery("ALTER TABLE $table ADD $column INTEGER")->execute();

        // 2. Populate with sequential IDs using ROWID
        $this->setQuery("SELECT ROWID FROM $table ORDER BY ROWID");
        $rowids = $this->loadColumn();
        $i = 1;
        foreach ($rowids as $rowid) {
            $this->setQuery("UPDATE $table SET $column = $i WHERE ROWID = $rowid")->execute();
            $i++;
        }

        // 3. Modify to SERIAL NOT NULL
        $this->setQuery("ALTER TABLE $table MODIFY ($column SERIAL NOT NULL)")->execute();

        // 4. Add PRIMARY KEY
        $this->setQuery("ALTER TABLE $table ADD CONSTRAINT PRIMARY KEY ($column)")->execute();

        return true;
    }

    /**
     * Add stored (computed) column (not supported)
     *
     * @param   string  $table       Table name
     * @param   string  $column      Column name
     * @param   string  $expression  SQL expression
     * @return  bool
     */
    public function addStoredColumn(string $table, string $column, string $expression, ?string $type = null): bool
    {
        throw new \RuntimeException('Stored generated columns are not supported in Informix.');
    }

    /**
     * Add virtual (computed) column (not supported)
     *
     * @param   string       $table       Table name
     * @param   string       $column      Column name
     * @param   string       $expression  SQL expression
     * @param   string|null  $type        Column type (required by MySQL so standardized here)
     * @return  bool
     */
    public function addVirtualColumn(string $table, string $column, string $expression, ?string $type = null): bool
    {
        throw new \RuntimeException('Virtual generated columns are not supported in Informix.');
    }

    /**
     * Make column visible (not supported)
     *
     * @param   string  $table   Table name
     * @param   string  $column  Column name
     * @return  bool
     */
    public function makeColumnVisible(string $table, string $column): bool
    {
        throw new \RuntimeException('Invisible columns are not supported in Informix.');
    }

    /**
     * Make column invisible (not supported)
     *
     * @param   string  $table   Table name
     * @param   string  $column  Column name
     * @return  bool
     */
    public function makeColumnInvisible(string $table, string $column): bool
    {
        throw new \RuntimeException('Invisible columns are not supported in Informix.');
    }

    // =========================================================================
    // Index Methods
    // =========================================================================

    /**
     * Add fulltext index to a table
     *
     * Informix doesn't support FULLTEXT indexes natively.
     * Creates a regular index as a fallback.
     *
     * @param   string        $table    Table name
     * @param   string        $name     Index name
     * @param   string|array  $columns  Column(s)
     * @return  bool
     */
    public function addFulltextIndex(string $table, string $name, $columns): bool
    {
        $table = $this->replacePrefix($table);
        $columns = is_array($columns) ? array_values($columns) : [$columns];
        $statements = $this->buildDeferredFulltextIndexStatements($table, $name, $columns);

        foreach ($statements as $sql) {
            $this->setQuery($sql);
            if (!$this->execute()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Build deferred fulltext index statements for Informix using BTS.
     *
     * @param   string  $table    Table name
     * @param   string  $name     Index name
     * @param   array   $columns  Indexed columns
     * @return  array
     */
    public function buildDeferredFulltextIndexStatements(string $table, string $name, array $columns): array
    {
        $table = $this->replacePrefix($table);
        $quotedTable = $this->quoteName($table);
        $columnTypeMap = $this->getTableColumns($table, true);
        $statements = [];
        $multiple = count($columns) > 1;

        foreach ($columns as $column) {
            $columnName = trim((string) $column);
            $quotedColumn = $this->quoteName($columnName);
            $type = strtolower((string) ($columnTypeMap[$columnName] ?? 'varchar'));
            $opclass = $this->resolveBtsOpClassForColumnType($type);

            $indexName = $multiple ? $this->buildInformixBtsIndexName($name, $columnName) : $name;
            $quotedName = $this->quoteName($indexName);
            $statements[] = 'CREATE INDEX ' . $quotedName
                . ' ON ' . $quotedTable
                . ' (' . $quotedColumn . ' ' . $opclass . ') USING bts';
        }

        return $statements;
    }

    /**
     * Resolve BTS operator class name for an Informix column type.
     *
     * @param   string  $type
     * @return  string
     */
    protected function resolveBtsOpClassForColumnType(string $type): string
    {
        $type = strtolower($type);

        if ($type === 'text' || $type === 'clob') {
            return 'bts_clob_ops';
        }
        if ($type === 'lvarchar') {
            return 'bts_lvarchar_ops';
        }
        if ($type === 'longlvarchar') {
            return 'bts_longlvarchar_ops';
        }
        if ($type === 'char') {
            return 'bts_char_ops';
        }
        if ($type === 'nchar') {
            return 'bts_nchar_ops';
        }
        if ($type === 'nvarchar') {
            return 'bts_nvarchar_ops';
        }
        if ($type === 'varchar') {
            return 'bts_varchar_ops';
        }

        // Conservative fallback for string-like unknowns.
        return 'bts_varchar_ops';
    }

    /**
     * Build a deterministic Informix BTS index name for multi-column fulltext.
     *
     * @param   string  $baseName
     * @param   string  $columnName
     * @return  string
     */
    protected function buildInformixBtsIndexName(string $baseName, string $columnName): string
    {
        $suffix = preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($columnName));
        $name = $baseName . '_' . $suffix;

        // Keep comfortably under Informix identifier limits.
        if (strlen($name) > 120) {
            $name = substr($name, 0, 120);
        }

        return $name;
    }

    // =========================================================================
    // Table Operations
    // =========================================================================

    /**
     * Set table storage engine (not applicable in Informix)
     *
     * @param   string  $table   Table name
     * @param   string  $engine  Engine name
     * @return  bool
     */
    public function setTableEngine(string $table, string $engine = 'MYISAM'): bool
    {
        // Informix doesn't have storage engines
        return false;
    }

    /**
     * Set table character set (not applicable in Informix)
     *
     * @param   string  $table      Table name
     * @param   string  $charset    Character set
     * @param   string  $collation  Collation
     * @return  bool
     */
    public function setTableCharset(
        string $table,
        string $charset = 'utf8',
        string $collation = 'utf8_general_ci'
    ): bool {
        // Informix uses database-level locale
        return false;
    }

    /**
     * Check if table has specific engine (not applicable)
     *
     * @param   string  $table   Table name
     * @param   string  $engine  Engine name
     * @return  bool
     */
    public function hasEngine($table, $engine)
    {
        return false;
    }

    /**
     * Sets the connection to use UTF-8 character encoding
     *
     * @return  bool
     */
    public function setUTF()
    {
        return true;
    }

    // =========================================================================
    // Charset Conversion (Not supported in Informix)
    // =========================================================================

    /**
     * Convert a table to a specific character set
     *
     * @param   string       $table    Table name
     * @param   string       $charset  Character set
     * @param   string|null  $collate  Collation (optional)
     * @return  bool
     */
    public function convertToCharset($table, $charset, $collate = null)
    {
        // Informix uses database-level locale settings
        return false;
    }

    // =========================================================================
    // ENUM Support (Not supported in Informix)
    // =========================================================================

    /**
     * Get ENUM values for a column
     *
     * @param   string  $table   Table name
     * @param   string  $column  Column name
     * @return  array
     */
    public function getEnumValues($table, $column)
    {
        // Informix doesn't have native ENUM type
        return [];
    }

    /**
     * Add a value to an ENUM column
     *
     * @param   string  $table   Table name
     * @param   string  $column  Column name
     * @param   string  $value   Value to add
     * @return  bool
     */
    public function addEnumValue($table, $column, $value)
    {
        // Informix doesn't have native ENUM type
        return false;
    }

    /**
     * Set ENUM values for a column
     *
     * @param   string  $table   Table name
     * @param   string  $column  Column name
     * @param   array   $values  ENUM values
     * @return  bool
     */
    public function setEnum($table, $column, $values)
    {
        // Informix doesn't have native ENUM type
        return false;
    }

    /**
     * Remove a value from an ENUM column
     *
     * @param   string  $table   Table name
     * @param   string  $column  Column name
     * @param   string  $value   Value to remove
     * @return  bool
     */
    public function removeEnumValue($table, $column, $value)
    {
        // Informix doesn't have native ENUM type
        return false;
    }

    // =========================================================================
    // LOB Stream -> String Conversion
    // =========================================================================

    /**
     * Convert LOB stream resources to strings in a fetched row.
     *
     * Some PDO_INFORMIX LOB-capable types can be returned as PHP stream
     * resources. Normalize them to strings for cross-driver consistency.
     *
     * @param   mixed  $row
     * @return  mixed
     */
    protected function convertLobs($row)
    {
        if ($row === false || $row === null) {
            return $row;
        }

        $cache = [];

        if (is_array($row)) {
            foreach ($row as &$value) {
                if (is_resource($value)) {
                    $rid = function_exists('get_resource_id') ? get_resource_id($value) : 0;
                    $this->trace('convertLobs array resource id=' . $rid);
                    if (!array_key_exists($rid, $cache)) {
                        $cache[$rid] = $this->readLobResource($value, 'array#' . $rid);
                    }
                    $value = $cache[$rid];
                }
            }
            unset($value);
        } elseif (is_object($row)) {
            foreach (get_object_vars($row) as $prop => $value) {
                if (is_resource($value)) {
                    $rid = function_exists('get_resource_id') ? get_resource_id($value) : 0;
                    $this->trace('convertLobs object ' . $prop . ' resource id=' . $rid);
                    if (!array_key_exists($rid, $cache)) {
                        $cache[$rid] = $this->readLobResource($value, 'object.' . $prop . '#' . $rid);
                    }
                    $row->$prop = $cache[$rid];
                }
            }
        }

        return $row;
    }

    /**
     * Read a LOB stream resource with optional safety cap for diagnostics.
     *
     * @param resource $resource
     * @param string $label
     * @return string
     */
    protected function readLobResource($resource, string $label): string
    {
        $limitEnv = getenv('IFX_LOB_READ_LIMIT');
        $limit = ($limitEnv === false) ? 32768 : (int) $limitEnv;
        @rewind($resource);
        $data = $limit > 0
            ? (string) stream_get_contents($resource, $limit)
            : (string) stream_get_contents($resource);

        $len = strlen($data);
        $preview = substr($data, 0, 80);
        $nulCount = substr_count($data, "\0");
        $dashCount = substr_count($data, '-');
        $underCount = substr_count($data, '_');
        $eqCount = substr_count($data, '=');
        $plusCount = substr_count($data, '+');
        $headHex = bin2hex(substr($data, 0, 16));
        $tailHex = bin2hex(substr($data, -16));
        $this->trace(
            sprintf(
                'readLobResource %s len=%d limit=%d md5=%s nul=%d -=%d _=%d ==%d +=%d head=%s tail=%s preview=%s',
                $label,
                $len,
                $limit,
                md5($data),
                $nulCount,
                $dashCount,
                $underCount,
                $eqCount,
                $plusCount,
                $headHex,
                $tailHex,
                preg_replace('/\s+/', ' ', $preview)
            )
        );

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function fetchObject($class = 'stdClass')
    {
        return $this->convertLobs(parent::fetchObject($class));
    }

    /**
     * {@inheritdoc}
     */
    protected function fetchArray()
    {
        return $this->convertLobs(parent::fetchArray());
    }

    /**
     * {@inheritdoc}
     */
    protected function fetchAssoc()
    {
        return $this->convertLobs(parent::fetchAssoc());
    }

    /**
     * Override freeResult to add explicit statement cleanup for PDO_INFORMIX
     *
     * PDO_INFORMIX has statement handle cleanup bugs. After fetching results,
     * we need to explicitly unset the PDOStatement to force immediate cleanup
     * at the C level and prevent connection corruption.
     *
     * @return  $this
     */
    public function freeResult()
    {
        parent::freeResult();

        if (isset($this->statement)) {
            $this->statement = null;
        }

        return $this;
    }
}
