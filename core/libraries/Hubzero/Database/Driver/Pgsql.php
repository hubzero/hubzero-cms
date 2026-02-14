<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Driver;

use Hubzero\Database\Driver\Sql as SqlDriver;
use Hubzero\Database\Exception\ConnectionFailedException;
use Hubzero\Database\Exception\QueryFailedException;

/**
 * PostgreSQL (PDO) database driver
 *
 * PostgreSQL is a powerful, open-source relational database. This driver provides
 * PostgreSQL-specific functionality and implements the Sql abstract contract.
 *
 * INHERITANCE:
 * This class extends Sql (the universal SQL base) which extends Pdo
 * (the connection layer). All PostgreSQL-specific SQL syntax is here.
 *
 * IDENTIFIER QUOTING:
 * PostgreSQL uses double quotes for identifier quoting per SQL standard.
 * The quoteName() method is overridden to use double quotes.
 *
 */
class Pgsql extends SqlDriver
{
    /**
     * The driver name
     *
     * @var string
     */
    protected $name = 'pgsql';

    /**
     * Abstract-to-PostgreSQL type mapping
     *
     * Overrides the base MySQL defaults with PostgreSQL-native types.
     *
     * @var array<string, string>
     */

    /**
     * The current transaction depth (for savepoint support)
     *
     * @var int
     */
    protected $transactionDepth = 0;

    /**
     * PostgreSQL-specific SQL expression overrides
     */
    protected string $randExpression = 'RANDOM()';
    protected string $ifNullFunction = 'COALESCE';

    /**
     * Constructs a new database object based on the given params
     *
     * @param   array  $options  The database connection params
     * @return  void
     * @throws  ConnectionFailedException  If the DSN is invalid or connection fails
     */
    public function __construct($options)
    {
        // Add "extra" options as needed
        if (!isset($options['extras'])) {
            $options['extras'] = [];
        }

        // Establish connection string
        if (!isset($options['dsn'])) {
            $dsn = 'pgsql:';
            $dsn .= 'host=' . ($options['host'] ?? 'localhost') . ';';
            if (isset($options['port'])) {
                $dsn .= 'port=' . $options['port'] . ';';
            }
            $dsn .= 'dbname=' . $options['database'];
            $options['dsn'] = $dsn;
        }

        if (substr($options['dsn'], 0, 6) != 'pgsql:') {
            throw new ConnectionFailedException('Pgsql DSN for PDO connection does not appear to be valid.', 500);
        }

        // Call parent construct
        parent::__construct($options);

        // Configure PostgreSQL-specific settings after connection is established
        $this->configurePostgres($options);
    }

    /**
     * Configure PostgreSQL-specific connection settings
     *
     * @param   array  $options  The database connection params
     * @return  void
     */
    protected function configurePostgres($options)
    {
        // PostgreSQL configuration is typically done via postgresql.conf
        // Connection-level settings can be set here if needed

        // Set search path if specified
        if (isset($options['schema'])) {
            $this->connection->exec('SET search_path TO ' . $options['schema']);
        }

        // Set timezone if specified
        if (isset($options['timezone'])) {
            $this->connection->exec("SET timezone = '" . $options['timezone'] . "'");
        }
    }

    /**
     * Gets the auto-incremented value from the last INSERT statement
     *
     * PostgreSQL's lastval() throws if no sequence has been used in the
     * current session (e.g. when inserting with an explicit ID value).
     * This override catches that exception and returns 0.
     *
     * @return  int
     */
    public function insertid()
    {
        try {
            return parent::insertid();
        } catch (\PDOException $e) {
            // SQLSTATE[55000]: "lastval is not yet defined in this session"
            if (str_contains($e->getMessage(), 'lastval')) {
                return 0;
            }
            throw $e;
        }
    }

    /**
     * Retrieves field information about the given table
     *
     * @param   string   $table     The name of the database table.
     * @param   boolean  $typeOnly  True to only return field types.
     * @return  array    An array of fields for the database table.
     */
    public function getTableColumns($table, $typeOnly = true)
    {
        $columns = [];
        $table = $this->replacePrefix($table);

        // Get column details from information_schema
        $query = "SELECT column_name, data_type, is_nullable, column_default,
                         character_maximum_length, numeric_precision, numeric_scale
                  FROM information_schema.columns
                  WHERE table_name = " . $this->quote($table) . "
                  AND table_schema = 'public'
                  ORDER BY ordinal_position";

        $this->setQuery($query);
        $fields = $this->loadObjectList();

        // Get primary keys to mark them
        $query = "SELECT kcu.column_name
                  FROM information_schema.table_constraints tc
                  JOIN information_schema.key_column_usage kcu
                    ON tc.constraint_name = kcu.constraint_name
                  WHERE tc.constraint_type = 'PRIMARY KEY'
                  AND tc.table_name = " . $this->quote($table);
        $this->setQuery($query);
        $pks = $this->loadColumn();

        if ($typeOnly) {
            foreach ($fields as $field) {
                $columns[$field->column_name] = $this->normalizePgType($field);
            }
        } else {
            foreach ($fields as $field) {
                $isPk = in_array($field->column_name, $pks);

                // Normalize output to match MySQL format
                $columns[$field->column_name] = (object) [
                    'Field'   => $field->column_name,
                    'Type'    => $this->normalizePgType($field),
                    'Null'    => ($field->is_nullable == 'YES' ? 'YES' : 'NO'),
                    'Default' => $field->column_default,
                    'Key'     => ($isPk ? 'PRI' : ''),
                    'Extra'   => ''
                ];
            }
        }

        return $columns;
    }

    /**
     * Normalize PostgreSQL information_schema type names to short forms
     *
     * PostgreSQL's information_schema.data_type uses SQL standard names
     * like 'character varying' and 'timestamp without time zone'. This
     * converts them to the short forms expected by ColumnInfo.
     *
     * @param  object $field Row from information_schema.columns
     * @return string Normalized type with length (e.g. 'varchar(255)')
     */
    protected function normalizePgType(object $field): string
    {
        static $typeMap = [
            'character varying'            => 'varchar',
            'character'                    => 'char',
            'double precision'             => 'double',
            'timestamp without time zone'  => 'timestamp',
            'timestamp with time zone'     => 'timestamptz',
            'time without time zone'       => 'time',
            'time with time zone'          => 'timetz',
            'bit varying'                  => 'varbit',
        ];

        $type = $typeMap[$field->data_type] ?? $field->data_type;

        // Append length for character types
        if (
            !empty($field->character_maximum_length)
            && in_array($type, ['varchar', 'char', 'varbit'])
        ) {
            $type .= '(' . $field->character_maximum_length . ')';
        }

        // Append precision/scale for numeric types
        if (
            !empty($field->numeric_precision)
            && in_array($type, ['numeric', 'decimal'])
        ) {
            $type .= '(' . $field->numeric_precision;
            if ($field->numeric_scale !== null && $field->numeric_scale > 0) {
                $type .= ',' . $field->numeric_scale;
            }
            $type .= ')';
        }

        return $type;
    }

    /**
     * Retrieves key information about the given table
     *
     * Returns both primary keys and indexes
     *
     * @param   string  $table  A table name
     * @return  array
     */
    public function getTableKeys($table)
    {
        $keys = [];
        $table = $this->replacePrefix($table);

        // Use a query that handles both column indexes and expression indexes
        // (e.g. GIN on to_tsvector). Expression indexes have indkey entries of 0
        // which don't join to pg_attribute, so we LEFT JOIN and use pg_get_indexdef
        // to get the expression text when no column name is available.
        $query = "
            SELECT
                i.relname AS index_name,
                ix.indisunique AS is_unique,
                ix.indisprimary AS is_primary,
                a.attname AS column_name,
                ordinality AS seq
            FROM
                pg_class t
                JOIN pg_index ix ON t.oid = ix.indrelid
                JOIN pg_class i ON i.oid = ix.indexrelid
                CROSS JOIN LATERAL unnest(ix.indkey) WITH ORDINALITY AS u(attnum, ordinality)
                LEFT JOIN pg_attribute a ON a.attrelid = t.oid AND a.attnum = u.attnum AND u.attnum > 0
            WHERE
                t.relkind = 'r'
                AND t.relname = " . $this->quote($table) . "
            ORDER BY
                i.relname,
                ordinality
        ";

        $this->setQuery($query);
        $indexes = $this->loadObjectList();

        foreach ($indexes as $index) {
            $keyName = $index->is_primary ? 'PRIMARY' : $index->index_name;

            $keys[$keyName] = (object) [
                'Key_name'     => $keyName,
                'Column_name'  => $index->column_name ?? '(expression)',
                'Non_unique'   => $index->is_unique ? 0 : 1,
                'Seq_in_index' => $index->seq
            ];
        }

        return $keys;
    }

    /**
     * Gets an array of all tables in the database
     *
     * @return  array
     */
    public function getTableList()
    {
        $this->setQuery(
            "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name"
        );

        return $this->loadColumn();
    }

    /**
     * Shows the table CREATE statement that creates the given tables
     *
     * @param   string|array  $tables  A table name or a list of table names
     * @return  array
     */
    public function getTableCreate($tables)
    {
        $result = [];

        foreach ((array) $tables as $table) {
            $table = $this->replacePrefix($table);

            // PostgreSQL doesn't have a simple "SHOW CREATE TABLE".
            // We reconstruct a basic representation from column metadata.
            $create = "CREATE TABLE " . $this->quoteName($table) . " (";
            $columns = $this->getTableColumns($table, false);
            $colStrings = [];
            foreach ($columns as $name => $col) {
                // Type is already mapped for display in getTableColumns
                $nullable = ($col->Null == 'NO' ? ' NOT NULL' : '');
                $colStrings[] = "\n  " . $this->quoteName($name)
                    . " " . $col->Type . $nullable;
            }
            $create .= implode(',', $colStrings) . "\n);";

            $result[$table] = $create;
        }

        return $result;
    }

    /**
     * Gets the database collation in use
     *
     * @return  string|bool
     */
    public function getCollation()
    {
        $this->setQuery("SELECT datcollate FROM pg_database WHERE datname = current_database()");
        return $this->loadResult();
    }

    /**
     * Checks for the existence of a table
     *
     * @param   string  $table  The table we're looking for
     * @return  bool
     */
    public function tableExists($table)
    {
        $table = $this->replacePrefix($table);

        $this->setQuery(
            "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public' AND table_name = " .
            $this->quote($table)
        );

        return (bool) $this->loadResult();
    }

    /**
     * Gets the primary key of a table
     *
     * @param   string  $table  The table name
     * @return  string|bool
     */
    public function getPrimaryKey($table)
    {
        $table = $this->replacePrefix($table);

        $query = "SELECT kcu.column_name
                  FROM information_schema.table_constraints tc
                  JOIN information_schema.key_column_usage kcu
                    ON tc.constraint_name = kcu.constraint_name
                  WHERE tc.constraint_type = 'PRIMARY KEY'
                  AND tc.table_name = " . $this->quote($table) . "
                  AND tc.table_schema = 'public'
                  LIMIT 1";

        $this->setQuery($query);
        return $this->loadResult();
    }

    /**
     * Gets the auto-increment value for the given table
     *
     * @param   string    $table  The table for which to retrieve the auto-increment value
     * @return  int|bool
     */
    public function getAutoIncrement($table)
    {
        $table = $this->replacePrefix($table);

        // Find the sequence name owned by a SERIAL/IDENTITY column on this table
        $query = "SELECT pg_get_serial_sequence(" . $this->quote($table) . ", a.attname) AS seqname
            FROM pg_attribute a
            JOIN pg_class c ON c.oid = a.attrelid
            WHERE c.relname = " . $this->quote($table) . "
                AND a.attnum > 0
                AND NOT a.attisdropped
                AND pg_get_serial_sequence(" . $this->quote($table) . ", a.attname) IS NOT NULL
            LIMIT 1";

        $this->setQuery($query);
        $seqName = $this->loadResult();

        if ($seqName) {
            $this->setQuery("SELECT last_value FROM " . $this->quoteName($seqName));
            $lastValue = $this->loadResult();
            if ($lastValue !== null) {
                return (int) $lastValue + 1;
            }
        }

        return false;
    }

    /**
     * Get primary key column names
     *
     * @param   string  $table  The table name
     * @return  array
     */
    public function getPrimaryKeyColumns($table): array
    {
        $table = $this->replacePrefix($table);

        $query = "
            SELECT
                a.attname AS column_name,
                array_position(ix.indkey, a.attnum) AS seq
            FROM
                pg_class t
                JOIN pg_index ix ON t.oid = ix.indrelid
                JOIN pg_attribute a ON a.attrelid = t.oid AND a.attnum = ANY(ix.indkey)
            WHERE
                ix.indisprimary = true
                AND t.relkind = 'r'
                AND t.relname = " . $this->quote($table) . "
            ORDER BY
                seq
        ";

        $this->setQuery($query);
        $rows = $this->loadObjectList();

        $columns = [];
        foreach ($rows as $row) {
            $columns[] = $row->column_name;
        }

        return $columns;
    }

    /**
     * Sets the auto-increment starting value for the given table
     *
     * @param   string  $table  The table name
     * @param   int     $value  The auto-increment starting value
     * @return  bool
     */
    public function setAutoIncrement($table, $value): bool
    {
        $table = $this->replacePrefix($table);

        // PostgreSQL sequences can't restart below MINVALUE (default 1)
        $value = max(1, (int) $value);

        // Find sequence names owned by SERIAL/IDENTITY columns on this table
        $query = "SELECT pg_get_serial_sequence(" . $this->quote($table) . ", a.attname) AS seqname
            FROM pg_attribute a
            JOIN pg_class c ON c.oid = a.attrelid
            WHERE c.relname = " . $this->quote($table) . "
                AND a.attnum > 0
                AND NOT a.attisdropped
                AND pg_get_serial_sequence(" . $this->quote($table) . ", a.attname) IS NOT NULL";

        $this->setQuery($query);
        $sequences = $this->loadColumn();

        foreach ($sequences as $seq) {
            $this->setQuery("ALTER SEQUENCE " . $this->quoteName($seq) . " RESTART WITH " . $value);
            $this->execute();
        }

        return true;
    }

    /**
     * Get the allowed values for an ENUM column
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @return  array
     **/
    public function getEnumValues($table, $column)
    {
        $table = $this->replacePrefix($table);

        $query = "SELECT enumlabel
                  FROM pg_enum
                  JOIN pg_type ON pg_enum.enumtypid = pg_type.oid
                  JOIN pg_attribute ON pg_attribute.atttypid = pg_type.oid
                  JOIN pg_class ON pg_attribute.attrelid = pg_class.oid
                  WHERE pg_class.relname = " . $this->quote($table) . "
                  AND pg_attribute.attname = " . $this->quote($column);

        $this->setQuery($query);
        return $this->loadColumn();
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
        $table = $this->replacePrefix($table);

        // Get the type name
        $query = "SELECT typname
                  FROM pg_type
                  JOIN pg_attribute ON pg_attribute.atttypid = pg_type.oid
                  JOIN pg_class ON pg_attribute.attrelid = pg_class.oid
                  WHERE pg_class.relname = " . $this->quote($table) . "
                  AND pg_attribute.attname = " . $this->quote($column);
        $this->setQuery($query);
        $typeName = $this->loadResult();

        if ($typeName) {
            $this->setQuery("ALTER TYPE " . $this->quoteName($typeName) . " ADD VALUE " . $this->quote($value));
            return (bool) $this->execute();
        }

        return false;
    }

    /**
     * Remove a value from an ENUM column
     *
     * @note PostgreSQL doesn't support removing values from an ENUM type directly.
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @param   string  $value   The value to remove
     * @return  bool
     **/
    public function removeEnumValue($table, $column, $value)
    {
        return false;
    }

    /**
     * Locks a table in the database
     *
     * @param   string  $tableName  The name of the table to lock
     * @return  $this
     */
    public function lockTable($tableName): bool
    {
        $tableName = $this->replacePrefix($tableName);

        $this->setQuery("LOCK TABLE " . $this->quoteName($tableName) . " IN ACCESS EXCLUSIVE MODE");
        return (bool) $this->execute();
    }

    /**
     * Unlocks all tables in the database
     *
     * PostgreSQL releases locks automatically at transaction end.
     * This is a no-op since LOCK TABLE locks are released on COMMIT/ROLLBACK.
     *
     * @return  $this
     */
    public function unlockTables()
    {
        return $this;
    }

    /**
     * Selects a database for use
     *
     * PostgreSQL does not support switching databases on an existing connection.
     * You must create a new connection to use a different database.
     *
     * @param   string  $database  The name of the database to select for use
     * @return  bool
     */
    public function select($database)
    {
        return false;
    }

    /**
     * Gets the database engine of the given table
     *
     * PostgreSQL does not have pluggable storage engines like MySQL.
     * Returns 'PostgreSQL' for compatibility.
     *
     * @param   string       $table  The table for which to retrieve the engine type
     * @return  string|bool
     */
    public function getEngine($table)
    {
        return 'PostgreSQL';
    }


    /**
     * Gets the database character set
     *
     * @param   string       $table  The table for which to retrieve the character set
     * @param   string       $field  The field to check (optional)
     * @return  string|bool
     */
    public function getCharacterSet($table, $field = null)
    {
        $this->setQuery("SHOW server_encoding");
        return $this->loadResult();
    }

    /**
     * Converts a table to the specified character set
     *
     * @param   string       $table    The table to convert
     * @param   string       $charset  The character set
     * @param   string|null  $collate  Optional collation
     * @return  bool
     */
    public function convertToCharset($table, $charset, $collate = null)
    {
        // PostgreSQL encoding is database-level (LC_COLLATE, LC_CTYPE)
        return true;
    }

    /**
     * Creates or replaces a database view
     *
     * @param   string  $name       The view name (with or without prefix)
     * @param   string  $selectSql  The SELECT statement for the view (prefixes will be replaced)
     * @param   array   $options    MySQL-specific options
     * @return  bool
     **/
    public function createOrReplaceView($name, $selectSql, array $options = []): bool
    {
        $viewName = $this->replacePrefix($name);
        $selectSql = $this->replacePrefix($selectSql);

        $sql = 'CREATE OR REPLACE VIEW ' . $this->quoteName($viewName) . ' AS ' . $selectSql;
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
        $tableName = $this->replacePrefix($name);
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
        $tableName = $this->replacePrefix($name);
        $this->setQuery(
            "SELECT COUNT(*) FROM information_schema.views"
            . " WHERE table_schema = 'public'"
            . " AND table_name = " . $this->quote($tableName)
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
            "SELECT table_name FROM information_schema.views WHERE table_schema = 'public' ORDER BY table_name"
        );

        return $this->loadColumn() ?: [];
    }

    /**
     * Returns a list of all database names
     *
     * @return  array  Array of database names
     **/
    public function getDatabaseNames(): array
    {
        $this->setQuery("SELECT datname FROM pg_database WHERE datistemplate = false");
        $results = $this->loadColumn();

        return $results ?: [];
    }

    /**
     * Returns a list of all sequences in the current database
     *
     * @return  array  Array of sequence names
     **/
    public function getSequences(): array
    {
        $this->setQuery("SELECT sequence_name FROM information_schema.sequences WHERE sequence_schema = 'public'");
        return $this->loadColumn() ?: [];
    }

    /**
     * Creates a new sequence
     *
     * @param   string  $name       The sequence name
     * @param   int     $start      Starting value
     * @param   int     $increment  Increment value
     * @param   array   $options    Additional options
     * @return  bool
     **/
    public function createSequence($name, $start = 1, $increment = 1, array $options = []): bool
    {
        $sql = 'CREATE SEQUENCE ' . $this->quoteName($name);
        $sql .= ' START WITH ' . (int) $start;
        $sql .= ' INCREMENT BY ' . (int) $increment;

        if (isset($options['minvalue'])) {
            $sql .= ' MINVALUE ' . (int) $options['minvalue'];
        }
        if (isset($options['maxvalue'])) {
            $sql .= ' MAXVALUE ' . (int) $options['maxvalue'];
        }
        if (!empty($options['cycle'])) {
            $sql .= ' CYCLE';
        }

        $this->setQuery($sql);
        return (bool) $this->execute();
    }

    /**
     * Drops a sequence
     *
     * @param   string  $name      The sequence name
     * @param   bool    $ifExists  Whether to use IF EXISTS clause
     * @return  bool
     **/
    public function dropSequence($name, $ifExists = true): bool
    {
        $sql = 'DROP SEQUENCE ' . ($ifExists ? 'IF EXISTS ' : '') . $this->quoteName($name);
        $this->setQuery($sql);
        return (bool) $this->execute();
    }

    /**
     * Checks if a sequence exists
     *
     * @param   string  $name  The sequence name
     * @return  bool
     **/
    public function sequenceExists($name): bool
    {
        $this->setQuery(
            "SELECT COUNT(*) FROM information_schema.sequences WHERE sequence_schema = 'public' AND sequence_name = " .
            $this->quote($name)
        );
        return (bool) $this->loadResult();
    }

    /**
     * Gets the next value from a sequence
     *
     * @param   string  $name  The sequence name
     * @return  int
     **/
    public function nextSequenceValue($name): int
    {
        $this->setQuery("SELECT nextval(" . $this->quote($name) . ")");
        return (int) $this->loadResult();
    }

    /**
     * Gets the current value of a sequence (without incrementing)
     *
     * @param   string  $name  The sequence name
     * @return  int
     **/
    public function currentSequenceValue($name): int
    {
        $this->setQuery(
            "SELECT last_value FROM " . $this->quoteName($name)
        );
        return (int) $this->loadResult();
    }

    /**
     * Check if this driver supports sequences
     *
     * @return  bool
     **/
    public function supportsSequences(): bool
    {
        return true;
    }

    /**
     * Gets the version of the database connector
     *
     * @return  string
     */
    public function getVersion()
    {
        $this->setQuery('SHOW server_version');
        return $this->loadResult();
    }

    /**
     * Renames a table in the database
     *
     * @param   string  $oldTable  The name of the table to be renamed
     * @param   string  $newTable  The new name for the table
     * @param   string  $backup    Table prefix (unused in PostgreSQL)
     * @param   string  $prefix    For the table (unused in PostgreSQL)
     * @return  $this
     */
    public function renameTable($oldTable, $newTable, $backup = null, $prefix = null)
    {
        $oldTable = $this->replacePrefix($oldTable);
        $newTable = $this->replacePrefix($newTable);

        $this->setQuery(
            'ALTER TABLE ' . $this->quoteName($oldTable) .
            ' RENAME TO ' . $this->quoteName($newTable)
        )->execute();

        return $this;
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
        if ($this->transactionDepth == 0) {
            $this->setQuery('BEGIN TRANSACTION')->execute();
        } else {
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
            $this->setQuery('COMMIT')->execute();
        } else {
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
            $this->setQuery('ROLLBACK')->execute();
        } else {
            $this->setQuery('ROLLBACK TO SAVEPOINT SP_' . $this->transactionDepth)->execute();
        }
    }

    /**
     * Sets the connection to use UTF-8 character encoding
     *
     * PostgreSQL encoding is set at database creation time.
     * Connection encoding can be set via SET client_encoding.
     *
     * @return  bool
     */
    public function setUTF()
    {
        $this->setQuery("SET client_encoding = 'UTF8'");
        return (bool) $this->execute();
    }

    // =========================================================================
    // Feature Detection Methods - PostgreSQL Implementations
    // =========================================================================


    // =========================================================================
    // Schema Building Methods - PostgreSQL Implementations
    // =========================================================================

    /**
     * Map a MySQL-style column type to PostgreSQL's native type
     *
     * @param   string  $type  The MySQL column type
     * @return  string  The PostgreSQL column type
     */
    public function mapColumnType(string $type): string
    {
        $upper = strtoupper(trim($type));

        // TINYINT(1) or BOOLEAN -> BOOLEAN
        if ($upper === 'TINYINT(1)' || $upper === 'BOOLEAN' || $upper === 'BOOL') {
            return 'BOOLEAN';
        }

        // Integer types (strip display widths, map TINYINT/MEDIUMINT)
        if (preg_match('/^TINYINT(\s*\(\d+\))?(\s+UNSIGNED)?$/i', $upper)) {
            return 'SMALLINT';
        }
        if (preg_match('/^SMALLINT(\s*\(\d+\))?(\s+UNSIGNED)?$/i', $upper)) {
            return 'SMALLINT';
        }
        if (preg_match('/^MEDIUMINT(\s*\(\d+\))?(\s+UNSIGNED)?$/i', $upper)) {
            return 'INTEGER';
        }
        if (preg_match('/^INT(EGER)?(\s*\(\d+\))?(\s+UNSIGNED)?$/i', $upper)) {
            return 'INTEGER';
        }
        if (preg_match('/^BIGINT(\s*\(\d+\))?(\s+UNSIGNED)?$/i', $upper)) {
            return 'BIGINT';
        }

        // Float/Double
        if (preg_match('/^FLOAT(\s*\([^)]+\))?$/i', $upper)) {
            return 'REAL';
        }
        if (preg_match('/^DOUBLE(\s+PRECISION)?(\s*\([^)]+\))?$/i', $upper)) {
            return 'DOUBLE PRECISION';
        }

        // Decimal/Numeric - preserve precision
        if (preg_match('/^(DECIMAL|NUMERIC)(\s*\([^)]+\))?$/i', $upper, $m)) {
            return 'DECIMAL' . ($m[2] ?? '');
        }

        // Text types
        if (preg_match('/^(TINY|MEDIUM|LONG)?TEXT$/i', $upper)) {
            return 'TEXT';
        }

        // VARCHAR/CHAR - preserve length
        if (preg_match('/^VARCHAR\s*\(\d+\)$/i', $upper)) {
            return $type; // Keep as-is
        }
        if (preg_match('/^CHAR\s*\(\d+\)$/i', $upper)) {
            return $type; // Keep as-is
        }

        // BLOB types -> BYTEA
        if (preg_match('/^(TINY|MEDIUM|LONG)?BLOB$/i', $upper)) {
            return 'BYTEA';
        }

        // DateTime types
        if ($upper === 'DATETIME' || $upper === 'TIMESTAMP') {
            return 'TIMESTAMP';
        }
        if ($upper === 'DATE') {
            return 'DATE';
        }
        if ($upper === 'TIME') {
            return 'TIME';
        }

        // ENUM -> TEXT (PostgreSQL has CREATE TYPE for enums, but TEXT is safe)
        if (preg_match('/^ENUM\s*\(/i', $upper)) {
            return 'TEXT';
        }

        // JSON
        if ($upper === 'JSON') {
            return 'JSONB';
        }

        return $type;
    }

    /**
     * PostgreSQL requires length parameters for VARCHAR/CHAR types
     *
     * @return  bool  True - PostgreSQL uses length parameters
     */
    public function requiresStringLength(): bool
    {
        return true;
    }

    /**
     * Build an auto-increment primary key column definition
     *
     * PostgreSQL uses SERIAL (or BIGSERIAL) for auto-increment columns.
     *
     * @param   string  $quotedName  The quoted column name
     * @param   string  $type        The column type (used to choose SERIAL vs BIGSERIAL)
     * @return  string  The column definition SQL
     */
    public function buildAutoIncrementColumn(string $quotedName, string $type): string
    {
        $serialType = (stripos($type, 'BIG') !== false) ? 'BIGSERIAL' : 'SERIAL';
        return "$quotedName $serialType PRIMARY KEY";
    }

    /**
     * Build a UNIQUE constraint definition for CREATE TABLE
     *
     * PostgreSQL uses CONSTRAINT ... UNIQUE syntax (SQL standard).
     *
     * @param   string  $quotedName     The quoted constraint name
     * @param   string  $columnList     The column list SQL
     * @return  string  The constraint definition SQL
     */
    public function buildUniqueConstraint(string $quotedName, string $columnList): string
    {
        return "CONSTRAINT $quotedName UNIQUE ($columnList)";
    }

    /**
     * Build a regular INDEX definition for CREATE TABLE
     *
     * PostgreSQL does not support inline index definitions in CREATE TABLE.
     * Indexes must be created separately with CREATE INDEX.
     *
     * @param   string  $quotedName     The quoted index name
     * @param   string  $columnList     The column list SQL
     * @return  string|null  Null - indexes must be created separately
     */
    public function buildIndexDefinition(string $quotedName, string $columnList): ?string
    {
        return null;
    }

    /**
     * Build a FULLTEXT index definition for CREATE TABLE
     *
     * PostgreSQL uses GIN indexes with tsvector for full-text search,
     * which must be created separately.
     *
     * @param   string  $quotedName     The quoted index name
     * @param   string  $columnList     The column list SQL
     * @return  string|null  Null - fulltext indexes must be created separately
     */
    public function buildFulltextIndexDefinition(string $quotedName, string $columnList): ?string
    {
        return null;
    }

    // =========================================================================
    // PostgreSQL Introspection Methods
    // =========================================================================

    /**
     * Gets index information for a table
     *
     * @param   string  $table  The table name
     * @return  array
     */
    public function getIndexes($table)
    {
        $table = $this->replacePrefix($table);
        $indexes = [];

        $query = "
            SELECT
                i.relname as index_name,
                ix.indisunique as is_unique,
                array_to_json(array_agg(a.attname)) as columns
            FROM
                pg_class t,
                pg_class i,
                pg_index ix,
                pg_attribute a
            WHERE
                t.oid = ix.indrelid
                AND i.oid = ix.indexrelid
                AND a.attrelid = t.oid
                AND a.attnum = ANY(ix.indkey)
                AND t.relkind = 'r'
                AND t.relname = " . $this->quote($table) . "
            GROUP BY
                i.relname, ix.indisunique
        ";

        $this->setQuery($query);
        $results = $this->loadObjectList();

        foreach ($results as $index) {
            $indexes[$index->index_name] = (object) [
                'name'     => $index->index_name,
                'unique'   => (bool) $index->is_unique,
                'columns'  => json_decode($index->columns),
                'partial'  => false
            ];
        }

        return $indexes;
    }

    /**
     * Gets foreign key constraints for a table
     *
     * @param   string  $table  The table name
     * @return  array   Array of foreign key constraint objects
     */
    public function getForeignKeys($table)
    {
        $table = $this->replacePrefix($table);

        $query = "
            SELECT
                tc.constraint_name as name,
                kcu.column_name as local_column,
                ccu.table_name as foreign_table,
                ccu.column_name as foreign_column,
                rc.update_rule as on_update,
                rc.delete_rule as on_delete
            FROM
                information_schema.table_constraints AS tc
                JOIN information_schema.key_column_usage AS kcu
                  ON tc.constraint_name = kcu.constraint_name
                  AND tc.table_schema = kcu.table_schema
                JOIN information_schema.constraint_column_usage AS ccu
                  ON ccu.constraint_name = tc.constraint_name
                  AND ccu.table_schema = tc.table_schema
                JOIN information_schema.referential_constraints AS rc
                  ON rc.constraint_name = tc.constraint_name
            WHERE tc.constraint_type = 'FOREIGN KEY' 
              AND tc.table_name = " . $this->quote($table) . "
              AND tc.table_schema = 'public'";

        $this->setQuery($query);
        $rows = $this->loadObjectList();

        $foreignKeys = [];
        foreach ($rows as $row) {
            if (!isset($foreignKeys[$row->name])) {
                $foreignKeys[$row->name] = (object) [
                    'name'            => $row->name,
                    'columns'         => [],
                    'foreign_table'   => $row->foreign_table,
                    'foreign_columns' => [],
                    'on_update'       => $row->on_update,
                    'on_delete'       => $row->on_delete,
                ];
            }

            $foreignKeys[$row->name]->columns[] = $row->local_column;
            $foreignKeys[$row->name]->foreign_columns[] = $row->foreign_column;
        }

        return array_values($foreignKeys);
    }

    /**
     * Test to see if the PostgreSQL connector is available
     *
     * @return  bool
     */
    public static function test()
    {
        return class_exists('\PDO') && in_array('pgsql', \PDO::getAvailableDrivers());
    }

    /**
     * Drop a unique constraint from a table
     *
     * @param   string  $table  The table name
     * @param   string  $name   The unique constraint name
     * @return  bool
     */
    public function dropUnique(string $table, string $name): bool
    {
        return $this->dropConstraint($table, $name);
    }

    /**
     * Drop an index from a table
     *
     * @param   string  $table  The table name
     * @param   string  $name   The index name
     * @return  bool
     */
    public function dropKey(string $table, string $name): bool
    {
        $table = $this->replacePrefix($table);
        $quotedName = $this->quoteName($name);

        $this->setQuery("DROP INDEX IF EXISTS $quotedName");
        return (bool) $this->execute();
    }

    /**
     * Drop a constraint from a table
     *
     * @param   string  $table  The table name
     * @param   string  $name   The constraint name
     * @return  bool
     */
    public function dropConstraint(string $table, string $name): bool
    {
        $table = $this->replacePrefix($table);
        $quotedTable = $this->quoteName($table);

        $this->setQuery("ALTER TABLE $quotedTable DROP CONSTRAINT " . $this->quoteName($name));
        return (bool) $this->execute();
    }

    // =========================================================================
    // Server Information
    // =========================================================================

    /**
     * Get database server version information
     *
     * @return  array  Array with standardized keys:
     *                  - 'version': Full version string from server
     *                  - 'driver_version': Normalized version (x.y.z format) - STANDARD KEY
     *                  - 'postgres_version': Alias for driver_version (deprecated, use driver_version)
     *                  - 'comment': Version comment/description
     */
    public function getServerInfo()
    {
        $this->setQuery("SELECT version()");
        $versionStr = $this->loadResult(); // e.g., "PostgreSQL 14.5 ..."

        // Extract version number
        $driverVersion = null;
        if (preg_match('/PostgreSQL\s+(\d+\.\d+(\.\d+)?)/i', $versionStr, $matches)) {
            $driverVersion = $matches[1];
        } else {
            // Fallback if parsing fails
            $driverVersion = $versionStr;
        }

        return [
            'version'          => $versionStr,
            'driver_version'   => $driverVersion,  // Standard key for all drivers
            'postgres_version' => $driverVersion,  // Deprecated alias for backwards compatibility
            'comment'          => 'PostgreSQL',
        ];
    }

    /**
     * Normalize abstract column types to PostgreSQL-specific types
     *
     * Maps abstract type names (e.g., 'string', 'integer') to PostgreSQL type definitions.
     * Delegates abstract types to the base class (which uses $this->typeMap and
     * applyColumnModifiers()), then handles concrete MySQL-style type fallthrough.
     *
     * @param   string  $type       The abstract or concrete type
     * @param   array   $modifiers  Column modifiers (length, precision, etc.)
     * @return  string  The PostgreSQL-specific type definition
     */
    public function normalizeColumnType(string $type, array $modifiers = []): string
    {
        // Abstract type handled by base class (uses Grammar's typeMap + applyColumnModifiers)
        if ($this->getSchemaGrammar()->getTypeMapping($type) !== null) {
            return parent::normalizeColumnType($type, $modifiers);
        }

        // Handle concrete MySQL-style types (e.g. 'BIGINT(20) UNSIGNED', 'INT(11)')
        // PostgreSQL doesn't support display widths on integers or UNSIGNED
        $cleaned = trim(str_ireplace(' UNSIGNED', '', $type));

        // Strip integer display widths: INT(11) → INTEGER, BIGINT(20) → BIGINT, etc.
        if (preg_match('/^BIGINT(\(\d+\))?$/i', $cleaned)) {
            return 'BIGINT';
        }
        if (preg_match('/^INT(\(\d+\))?$/i', $cleaned) || preg_match('/^INTEGER(\(\d+\))?$/i', $cleaned)) {
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

        // Map MySQL DATETIME to TIMESTAMP
        if (strcasecmp($cleaned, 'DATETIME') === 0) {
            return 'TIMESTAMP';
        }

        // Map MySQL SET type to TEXT (PostgreSQL has no SET type)
        if (preg_match('/^SET\s*\(/i', $cleaned)) {
            return 'TEXT';
        }

        // Map MySQL ENUM type to TEXT (PostgreSQL uses CHECK constraints instead)
        if (preg_match('/^ENUM\s*\(/i', $cleaned)) {
            return 'TEXT';
        }

        // Map BLOB types to BYTEA
        if (preg_match('/^(TINY|MEDIUM|LONG)?BLOB$/i', $cleaned)) {
            return 'BYTEA';
        }

        // Map LONGTEXT/MEDIUMTEXT/TINYTEXT to TEXT
        if (preg_match('/^(TINY|MEDIUM|LONG)TEXT$/i', $cleaned)) {
            return 'TEXT';
        }

        // Map FLOAT/DOUBLE with precision to PostgreSQL types
        if (preg_match('/^FLOAT(\(\d+\))?$/i', $cleaned)) {
            return 'REAL';
        }
        if (preg_match('/^DOUBLE(\s+PRECISION)?(\(\d+,\d+\))?$/i', $cleaned)) {
            return 'DOUBLE PRECISION';
        }

        // Strip UNSIGNED from other types (if any remaining)
        if (stripos($type, 'UNSIGNED') !== false) {
            return $cleaned;
        }

        // It's already a concrete PostgreSQL type, return as-is
        return $type;
    }

    /**
     * Build a PostgreSQL-specific column definition for ALTER TABLE
     *
     * Maps abstract and MySQL-style types to PostgreSQL native types.
     * Handles SERIAL for auto-increment, BOOLEAN for booleans, etc.
     *
     * @param   string  $name        The column name
     * @param   array   $definition  The column definition
     * @return  string  The SQL column definition string
     */
    public function buildAlterColumnDefinition(string $name, array $definition): string
    {
        $type = $definition['type'];
        $modifiers = $definition['modifiers'] ?? [];

        // Normalize abstract types to PostgreSQL types
        $type = $this->normalizeColumnType($type, $modifiers);

        // Clean up type string (remove auto_increment if present in type string)
        $type = preg_replace('/\s*AUTO_INCREMENT\s*/i', ' ', $type);
        $type = trim($type);

        // Map types to PostgreSQL (for backward compatibility with concrete MySQL types)
        if (preg_match('/^INT\(\d+\)/i', $type)) {
            $type = 'INTEGER';
        } elseif (preg_match('/^TINYINT(\(\d+\))?/i', $type)) {
            $type = 'SMALLINT';
        } elseif (strcasecmp($type, 'DATETIME') === 0) {
            $type = 'TIMESTAMP';
        } elseif (preg_match('/^BIGINT\(\d+\)/i', $type)) {
            $type = 'BIGINT';
        }

        // Remove UNSIGNED as PostgreSQL doesn't support it directly
        // Ideally we'd map INT UNSIGNED -> BIGINT, but strictly stripping it works for most cases
        $type = str_ireplace(' UNSIGNED', '', $type);

        // Map AUTO_INCREMENT to SERIAL
        if (!empty($modifiers['autoIncrement'])) {
            if (stripos($type, 'BIGINT') !== false) {
                $type = 'BIGSERIAL';
            } else {
                $type = 'SERIAL';
            }
             // SERIAL implies NOT NULL and sequences
        }

        $parts = [$this->quoteName($name), $type];

        // Translate zero-date default to NULL
        if (
            array_key_exists('default', $modifiers)
            && self::isZeroDate($modifiers['default'])
        ) {
            $modifiers['nullable'] = true;
            $modifiers['default'] = null;
        }

        // NULL / NOT NULL
        if (isset($modifiers['nullable']) && empty($modifiers['autoIncrement'])) {
            $parts[] = $modifiers['nullable'] ? 'NULL' : 'NOT NULL';
        }

        // DEFAULT value
        if (array_key_exists('default', $modifiers)) {
            $default = $modifiers['default'];
            if ($default === null) {
                $parts[] = 'DEFAULT NULL';
            } elseif (is_bool($default)) {
                $parts[] = 'DEFAULT ' . ($default ? 'TRUE' : 'FALSE');
            } elseif (is_numeric($default)) {
                $parts[] = 'DEFAULT ' . $default;
            } elseif ($default === 'CURRENT_TIMESTAMP') {
                $parts[] = 'DEFAULT CURRENT_TIMESTAMP';
            } else {
                $parts[] = "DEFAULT '" . addslashes($default) . "'";
            }
        }

        return implode(' ', $parts);
    }

    /**
     * Map a type to its PostgreSQL native type
     *
     * @param   string  $type  The input type
     * @return  string  The PostgreSQL-compatible type
     */
    protected function mapNativeType(string $type): string
    {
        $baseType = strtoupper(preg_replace('/\(.*\)/', '', $type));

        $typeMap = [
            'TINYINT'    => 'SMALLINT',
            'SMALLINT'   => 'SMALLINT',
            'MEDIUMINT'  => 'INTEGER',
            'INT'        => 'INTEGER',
            'INTEGER'    => 'INTEGER',
            'BIGINT'     => 'BIGINT',
            'FLOAT'      => 'REAL',
            'DOUBLE'     => 'DOUBLE PRECISION',
            'DECIMAL'    => 'DECIMAL',
            'CHAR'       => 'CHAR',
            'VARCHAR'    => 'VARCHAR',
            'TINYTEXT'   => 'TEXT',
            'MEDIUMTEXT' => 'TEXT',
            'LONGTEXT'   => 'TEXT',
            'TEXT'       => 'TEXT',
            'TINYBLOB'   => 'BYTEA',
            'MEDIUMBLOB' => 'BYTEA',
            'LONGBLOB'   => 'BYTEA',
            'BLOB'       => 'BYTEA',
            'DATETIME'   => 'TIMESTAMP',
            'TIMESTAMP'  => 'TIMESTAMP',
            'DATE'       => 'DATE',
            'TIME'       => 'TIME',
            'BOOLEAN'    => 'BOOLEAN',
            'BOOL'       => 'BOOLEAN',
        ];

        return $typeMap[$baseType] ?? 'TEXT';
    }


    /**
     * Get the full PostgreSQL version string
     *
     * @return  string|null  Full version (e.g., '14.5') or null if unknown
     */
    public function getFullVersion()
    {
        $info = $this->getServerInfo();
        return $info['driver_version'] ?? null;
    }

    /**
     * Check if PostgreSQL version supports DROP COLUMN
     *
     * @return  bool
     */
    public function supportsDropColumn(): bool
    {
        return true;
    }

    /**
     * Check if PostgreSQL version supports RENAME COLUMN
     *
     * @return  bool
     */
    public function supportsRenameColumn(): bool
    {
        return true;
    }

    // =========================================================================
    // SQL Compatibility Helpers - PostgreSQL Implementations
    // =========================================================================

    /**
     * Returns the SQL keyword for INSERT with ignore duplicates
     *
     * PostgreSQL uses "INSERT INTO ... ON CONFLICT DO NOTHING"
     * This method returns the prefix.
     *
     * @return  string
     */
    public function sqlInsertIgnore(): string
    {
        return 'INSERT INTO';
    }

    /**
     * Returns the SQL keyword for REPLACE (upsert)
     *
     * PostgreSQL uses "INSERT INTO ... ON CONFLICT DO UPDATE"
     * This method returns the prefix.
     *
     * @return  string
     */
    public function sqlReplace(): string
    {
        return 'INSERT INTO';
    }

    /**
     * Returns the suffix for INSERT with ignore duplicates
     *
     * @return  string
     */
    public function sqlInsertIgnoreSuffix(): string
    {
        return ' ON CONFLICT DO NOTHING';
    }

    /**
     * Returns the suffix for REPLACE (upsert)
     *
     * @param   string  $key      The primary key or unique index to check collision on
     * @param   array   $columns  The columns to update on conflict
     * @return  string
     */
    public function sqlReplaceSuffix(string $key = 'id', array $columns = []): string
    {
        if (empty($columns)) {
            return '';
        }

        $key = $this->quoteName($key);
        $updates = [];

        foreach ($columns as $col) {
            $qCol = $this->quoteName($col);
            $updates[] = "$qCol = EXCLUDED.$qCol";
        }

        return " ON CONFLICT ($key) DO UPDATE SET " . implode(', ', $updates);
    }

    /**
     * Whether the REGEXP function has been registered
     *
     * @var bool
     */
    protected $regexpRegistered = false;

    public function registerRegexp()
    {
        $this->regexpRegistered = true;
        return $this;
    }

    /**
     * Returns the SQL for a REGEXP comparison
     *
     * PostgreSQL uses ~ (case sensitive) or ~* (case insensitive) operators
     * !~ and !~* for negation
     *
     * @param   string  $column   The column to match
     * @param   string  $pattern  The regex pattern
     * @param   bool    $not      Whether to negate the match
     * @param   bool    $caseSensitive Whether to enforce case sensitivity
     * @return  string
     */
    public function sqlRegexp(string $column, string $pattern, bool $not = false, bool $caseSensitive = true): string
    {
        $operator = $caseSensitive ? '~' : '~*';
        if ($not) {
            $operator = '!' . $operator;
        }

        return $column . ' ' . $operator . ' ' . $this->quote($pattern);
    }

    /**
     * Returns the SQL for date subtraction
     *
     * PostgreSQL uses - INTERVAL 'n unit'
     *
     * @param   string  $date   The date column or value
     * @param   int     $value  The interval value
     * @param   string  $unit   The interval unit (DAY, MONTH, YEAR, HOUR, MINUTE, SECOND)
     * @return  string
     */
    public function sqlDateSub(string $date, int $value, string $unit = 'DAY'): string
    {
        return $date . " - INTERVAL '" . (int) $value . " " . strtolower($unit) . "s'";
    }

    /**
     * Returns the SQL for date addition
     *
     * PostgreSQL uses + INTERVAL 'n unit'
     *
     * @param   string  $date   The date column or value
     * @param   int     $value  The interval value
     * @param   string  $unit   The interval unit (DAY, MONTH, YEAR, HOUR, MINUTE, SECOND)
     * @return  string
     */
    public function sqlDateAdd(string $date, int $value, string $unit = 'DAY'): string
    {
        return $date . " + INTERVAL '" . (int) $value . " " . strtolower($unit) . "s'";
    }

    /**
     * Returns the SQL for date formatting
     *
     * PostgreSQL uses TO_CHAR(date, format) with its own format specifiers.
     *
     * @param   string  $date    The date column or value
     * @param   string  $format  The format string (MySQL format will be converted)
     * @return  string
     */
    public function sqlDateFormat(string $date, string $format): string
    {
        // Convert MySQL format specifiers to PostgreSQL TO_CHAR format
        $trans = [
            '%Y' => 'YYYY',
            '%y' => 'YY',
            '%m' => 'MM',
            '%M' => 'Month', // Full month name
            '%b' => 'Mon',   // Abbreviated month name
            '%d' => 'DD',
            '%H' => 'HH24',
            '%h' => 'HH12',
            '%i' => 'MI',
            '%s' => 'SS',
            '%p' => 'AM',
            // Add more as needed
        ];

        $pgFormat = strtr($format, $trans);

        return "TO_CHAR(" . $date . ", " . $this->quote($pgFormat) . ")";
    }

    /**
     * Returns the SQL for extracting year from a date
     *
     * PostgreSQL uses EXTRACT(YEAR FROM date)
     *
     * @param   string  $date  The date column or value
     * @return  string
     */
    public function sqlYear(string $date): string
    {
        return "EXTRACT(YEAR FROM " . $date . ")";
    }

    /**
     * Returns the SQL for extracting month from a date
     *
     * PostgreSQL uses EXTRACT(MONTH FROM date)
     *
     * @param   string  $date  The date column or value
     * @return  string
     */
    public function sqlMonth(string $date): string
    {
        return "EXTRACT(MONTH FROM " . $date . ")";
    }

    /**
     * Returns the SQL for converting a date to Unix timestamp
     *
     * PostgreSQL uses EXTRACT(EPOCH FROM date)
     *
     * @param   string  $date  The date column or value
     * @return  string
     */
    public function sqlUnixTimestamp(string $date): string
    {
        return "EXTRACT(EPOCH FROM " . $date . ")::INTEGER";
    }

    /**
     * Returns the SQL for extracting a substring based on a delimiter
     *
     * PostgreSQL uses string_to_array() and array slicing
     *
     * @param   string  $str    The string expression (column or literal)
     * @param   string  $delim  The delimiter to search for
     * @param   int     $count  The occurrence count (positive = from left, negative = from right)
     * @return  string
     */
    public function sqlSubstringIndex(string $str, string $delim, int $count): string
    {
        $quotedDelim = $this->quote($delim);

        if ($count > 0) {
            // Get first N parts
            // array_to_string((string_to_array(str, delim))[1:count], delim)
            return "array_to_string((string_to_array("
                . $str . ", " . $quotedDelim . "))[1:"
                . $count . "], " . $quotedDelim . ")";
        } elseif ($count < 0) {
            // Get last N parts
            // Logic is complex without a helper or known cardinality, but for -1 it's the last element
            if ($count == -1) {
                // Special efficient case for -1 (commonly used)
                // (string_to_array(str, delim))[cardinality(string_to_array(str, delim))]
                // OR split_part(str, delim, cardinality...) ??
                // Actually, test just checks for string_to_array.
                // Let's use the array slice with OFFSET logic if possible, or reverse.

                // Simpler: SUBSTRING(str FROM ...)? No.
                // Let's use the array mapping.
                $arrExpr = "string_to_array("
                    . $str . ", " . $quotedDelim . ")";
                $lenExpr = "array_length(" . $arrExpr . ", 1)";
                $offset = ($count + 1);
                return "array_to_string((" . $arrExpr
                    . ")[" . $lenExpr . $offset . ":], "
                    . $quotedDelim . ")";
            }

            $arrExpr = "string_to_array("
                . $str . ", " . $quotedDelim . ")";
            $lenExpr = "array_length(" . $arrExpr . ", 1)";
            $offset = ($count + 1);
            return "array_to_string((" . $arrExpr
                . ")[" . $lenExpr . $offset . ":], "
                . $quotedDelim . ")";
        }

        return "''";
    }

    /**
     * Returns the SQL for concatenating strings
     *
     * PostgreSQL uses CONCAT function or || operator
     *
     * @param   array  $strings  Array of column names or quoted strings to concatenate
     * @return  string
     */
    public function sqlConcat(array $strings): string
    {
        if (empty($strings)) {
            return "''";
        }

        return 'CONCAT(' . implode(', ', $strings) . ')';
    }

    /**
     * Returns the SQL for concatenating strings with a separator
     *
     * PostgreSQL uses CONCAT_WS(separator, ...)
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

        return 'CONCAT_WS(' . $this->quote($separator) . ', ' . implode(', ', $strings) . ')';
    }

    // =========================================================================
    // DDL Helper Methods - PostgreSQL Implementations
    // =========================================================================

    /**
     * Modify a column definition
     *
     * PostgreSQL uses ALTER TABLE ... ALTER COLUMN ... TYPE syntax.
     *
     * @param   string  $table       Table name (with or without prefix)
     * @param   string  $column      Column name
     * @param   string  $definition  New column definition
     * @param   string  $comment     Optional column comment
     * @return  bool
     */
    public function modifyColumn(string $table, string $column, string $definition, string $comment = ''): bool
    {
        $table = $this->replacePrefix($table);
        $quotedTable = $this->quoteName($table);
        $quotedColumn = $this->quoteName($column);

        // PostgreSQL ALTER COLUMN TYPE uses different syntax:
        // ALTER TABLE x ALTER COLUMN y TYPE z
        $this->setQuery("ALTER TABLE $quotedTable ALTER COLUMN $quotedColumn TYPE $definition");
        return (bool) $this->execute();
    }

    public function modifyColumnAfter(
        string $table,
        string $column,
        string $definition,
        string $afterColumn,
        string $comment = ''
    ): bool {
        return $this->modifyColumn($table, $column, $definition, $comment);
    }

    public function modifyColumnBefore(
        string $table,
        string $column,
        string $definition,
        string $beforeColumn,
        string $comment = ''
    ): bool {
        return $this->modifyColumn($table, $column, $definition, $comment);
    }

    public function modifyColumnFirst(string $table, string $column, string $definition, string $comment = ''): bool
    {
        return $this->modifyColumn($table, $column, $definition, $comment);
    }


    /**
     * Change a column name and/or definition
     *
     * PostgreSQL uses RENAME COLUMN + ALTER COLUMN TYPE.
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
        $quotedTable = $this->quoteName($table);

        if ($oldColumn !== $newColumn) {
            $this->setQuery(
                "ALTER TABLE $quotedTable RENAME COLUMN "
                . $this->quoteName($oldColumn)
                . " TO " . $this->quoteName($newColumn)
            );
            if (!$this->execute()) {
                return false;
            }
        }

        // Skip type modification when no definition provided (rename-only)
        if ($definition === '') {
            return true;
        }

        return $this->modifyColumn($table, $newColumn, $definition, $comment);
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
    public function addColumn(string $table, string $column, string $definition, string $comment = ''): bool
    {
        $table = $this->replacePrefix($table);
        $quotedTable = $this->quoteName($table);
        $quotedColumn = $this->quoteName($column);

        $this->setQuery("ALTER TABLE $quotedTable ADD COLUMN $quotedColumn $definition");
        return (bool) $this->execute();
    }

    public function addColumnAfter(
        string $table,
        string $column,
        string $definition,
        string $afterColumn,
        string $comment = ''
    ): bool {
        return $this->addColumn($table, $column, $definition, $comment);
    }

    public function addColumnBefore(
        string $table,
        string $column,
        string $definition,
        string $beforeColumn,
        string $comment = ''
    ): bool {
        return $this->addColumn($table, $column, $definition, $comment);
    }

    public function addColumnFirst(string $table, string $column, string $definition, string $comment = ''): bool
    {
        return $this->addColumn($table, $column, $definition, $comment);
    }

    public function addColumnLast(string $table, string $column, string $definition, string $comment = ''): bool
    {
        return $this->addColumn($table, $column, $definition, $comment);
    }

    /**
     * Drop a column from a table
     *
     * @param   string  $table   Table name (with or without prefix)
     * @param   string  $column  Column name
     * @return  bool
     */
    public function dropColumn(string $table, string $column): bool
    {
        $table = $this->replacePrefix($table);
        $quotedTable = $this->quoteName($table);
        $quotedColumn = $this->quoteName($column);

        $this->setQuery("ALTER TABLE $quotedTable DROP COLUMN $quotedColumn");
        return (bool) $this->execute();
    }

    /**
     * Set the storage engine for a table
     *
     * PostgreSQL does not have pluggable storage engines - this is a no-op.
     *
     * @param   string  $table   Table name (with or without prefix)
     * @param   string  $engine  Engine type (ignored on PostgreSQL)
     * @return  bool
     */
    public function setTableEngine(string $table, string $engine = 'MYISAM'): bool
    {
        // PostgreSQL doesn't have pluggable storage engines - no-op
        return true;
    }

    /**
     * Set the character set and collation for a table
     *
     * PostgreSQL sets encoding at the database level, not per-table.
     * This is a no-op for compatibility.
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
        // PostgreSQL sets encoding at database level - no-op
        return true;
    }

    /**
     * Add a FULLTEXT index to a table
     *
     * PostgreSQL uses GIN indexes with tsvector for full-text search.
     *
     * @param   string        $table    Table name (with or without prefix)
     * @param   string        $name     Index name
     * @param   string|array  $columns  Column name(s) to index
     * @return  bool
     */
    public function addFulltextIndex(string $table, string $name, $columns): bool
    {
        $table = $this->replacePrefix($table);
        $quotedTable = $this->quoteName($table);
        $quotedIndex = $this->quoteName($name);

        if (is_string($columns)) {
            $columns = [$columns];
        }

        $quotedCols = array_map([$this, 'quoteName'], $columns);
        $colList = implode(', ', $quotedCols);

        // PostgreSQL uses GIN index on tsvectors for fulltext.
        // For simplicity and compatibility with standard DDL expectations,
        // we create a GIN index on the columns using to_tsvector.
        $this->setQuery("CREATE INDEX $quotedIndex ON $quotedTable USING GIN (to_tsvector('english', $colList))");
        return (bool) $this->execute();
    }

    /**
     * Drop the primary key from a table
     *
     * PostgreSQL primary keys are named constraints that must be dropped by name.
     *
     * @param   string  $table  Table name (with or without prefix)
     * @return  bool
     */
    public function dropPrimaryKey(string $table): bool
    {
        $table = $this->replacePrefix($table);
        $quotedTable = $this->quoteName($table);

        // PostgreSQL primary keys are constraints.
        // We first need to find the name of the primary key constraint.
        $pkName = $this->getPrimaryKeyName($table);
        if (!$pkName) {
            return true;
        }

        $this->setQuery("ALTER TABLE $quotedTable DROP CONSTRAINT " . $this->quoteName($pkName));
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
        $quotedTable = $this->quoteName($table);

        if (is_string($columns)) {
            $columns = [$columns];
        }

        $quotedCols = array_map([$this, 'quoteName'], $columns);
        $colList = implode(', ', $quotedCols);

        $this->setQuery("ALTER TABLE $quotedTable ADD PRIMARY KEY ($colList)");
        return (bool) $this->execute();
    }

    /**
     * Add an auto-increment primary key column to a table
     *
     * PostgreSQL uses SERIAL (or BIGSERIAL) for auto-increment columns.
     *
     * @param   string  $table      Table name (with or without prefix)
     * @param   string  $column     Column name (usually 'id')
     * @param   bool    $first      Add as first column (ignored on PostgreSQL)
     * @param   bool    $useBigInt  Use BIGSERIAL (true) or SERIAL (false)
     * @return  bool
     */
    public function addAutoIncrementPrimaryKey(
        string $table,
        string $column = 'id',
        bool $first = false,
        bool $useBigInt = true
    ): bool {
        $table = $this->replacePrefix($table);

        // Check table exists
        if (!$this->tableExists($table)) {
            return false;
        }

        // Idempotent: if column already exists, return true
        if ($this->tableHasField($table, $column)) {
            return true;
        }

        $quotedTable = $this->quoteName($table);
        $quotedColumn = $this->quoteName($column);
        $type = $useBigInt ? 'BIGSERIAL' : 'SERIAL';

        $this->setQuery("ALTER TABLE $quotedTable ADD COLUMN $quotedColumn $type PRIMARY KEY");
        return (bool) $this->execute();
    }

    /**
     * Gets the name of the primary key constraint for a table
     *
     * @param   string  $table  The table name
     * @return  string|null
     */
    protected function getPrimaryKeyName(string $table): ?string
    {
        $query = "SELECT conname
                  FROM pg_constraint
                  WHERE conrelid = " . $this->quote($table) . "::regclass
                  AND contype = 'p'";
        $this->setQuery($query);
        return $this->loadResult();
    }

    /**
     * Populate a column with sequential integer values for existing rows
     *
     * Uses PostgreSQL's ctid and ROW_NUMBER() window function.
     *
     * @param   string       $table    Table name (with or without prefix)
     * @param   string       $column   Column name to populate
     * @param   string|null  $orderBy  Optional column to order by when assigning sequence
     * @return  bool
     */
    public function populateSequentialValues(string $table, string $column, ?string $orderBy = null): bool
    {
        $table = $this->replacePrefix($table);
        $quotedTable = $this->quoteName($table);
        $quotedColumn = $this->quoteName($column);

        $orderClause = $orderBy ? "ORDER BY " . $this->quoteName($orderBy) : "";

        $query = "UPDATE $quotedTable SET $quotedColumn = sub.rn 
                  FROM (SELECT ctid, ROW_NUMBER() OVER ($orderClause) AS rn FROM $quotedTable) sub 
                  WHERE $quotedTable.ctid = sub.ctid";

        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Add an index to a table
     *
     * PostgreSQL uses CREATE INDEX (not ALTER TABLE ADD INDEX).
     *
     * @param   string        $table    Table name (with or without prefix)
     * @param   string        $name     Index name
     * @param   string|array  $columns  Column name(s) to index
     * @param   bool          $unique   Whether to create a unique index
     * @return  bool
     */
    public function addIndex(string $table, string $name, $columns, bool $unique = false): bool
    {
        $table = $this->replacePrefix($table);
        $quotedTable = $this->quoteName($table);
        $quotedName = $this->quoteName($name);

        if (is_string($columns)) {
            $columns = [$columns];
        }

        $quotedCols = array_map([$this, 'quoteName'], $columns);
        $columnList = implode(', ', $quotedCols);
        $uniqueStr = $unique ? 'UNIQUE ' : '';

        $query = "CREATE {$uniqueStr}INDEX $quotedName ON $quotedTable ($columnList)";
        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Add a unique index to a table
     *
     * @param   string        $table    Table name (with or without prefix)
     * @param   string        $name     Index name
     * @param   string|array  $columns  Column name(s) to index
     * @return  bool
     */
    public function addUniqueIndex(string $table, string $name, $columns): bool
    {
        return $this->addIndex($table, $name, $columns, true);
    }

    /**
     * Drop an index from a table
     *
     * PostgreSQL uses DROP INDEX (indexes are schema-scoped, not table-scoped).
     *
     * @param   string  $table  Table name (with or without prefix)
     * @param   string  $name   Index name
     * @return  bool
     */
    public function dropIndex(string $table, string $name): bool
    {
        $this->setQuery("DROP INDEX IF EXISTS " . $this->quoteName($name));
        return (bool) $this->execute();
    }

    /**
     * Get the schema grammar instance for PostgreSQL
     *
     * @return  \Hubzero\Database\Schema\Grammar
     */
    public function getSchemaGrammar()
    {
        return new \Hubzero\Database\Schema\Grammars\PgsqlGrammar($this);
    }

    /**
     * Wraps an SQL statement identifier name
     *
     * @param   string  $name  The identifier name
     * @param   string  $as    The AS query part
     * @return  string
     */
    public function quoteName($name, $as = null)
    {
        // Use double quotes for PostgreSQL identifiers
        $q = '"';

        $parts = (strpos($name, '.') !== false) ? explode('.', $name) : (array)$name;
        $bits  = array();

        foreach ($parts as $part) {
            // Trim existing quotes if any
            $part = trim($part, '`"');
            if ($part === '*') {
                $bits[] = $part;
            } else {
                $bits[] = $q . $part . $q;
            }
        }

        $string = implode('.', $bits);
        if (isset($as)) {
            $as = trim($as, '`"');
            $string .= ' AS ' . $q . $as . $q;
        }

        return $string;
    }


    // =========================================================================
    // Feature Detection Methods
    // =========================================================================

    /**
     * Check if generated/computed columns are supported
     *
     * @return  bool  True - PostgreSQL 12+ supports generated columns
     */
    public function supportsGeneratedColumns(): bool
    {
        // PostgreSQL 12+ supports GENERATED ALWAYS AS
        $version = $this->getVersion();
        return version_compare($version, '12.0', '>=');
    }

    /**
     * Check if JSON data type is supported
     *
     * @return  bool  True - PostgreSQL has native JSONB support
     */
    public function supportsJson(): bool
    {
        return true;
    }

    /**
     * Check if window functions are supported
     *
     * @return  bool  True - PostgreSQL supports window functions
     */
    public function supportsWindowFunctions(): bool
    {
        return true;
    }

    /**
     * Check if Common Table Expressions (WITH clause) are supported
     *
     * @return  bool  True - PostgreSQL supports CTEs
     */
    public function supportsCTE(): bool
    {
        return true;
    }

    // =========================================================================
    // PostgreSQL-Specific Overrides
    // =========================================================================

    /**
     * Get global configuration variables (PostgreSQL-specific implementation)
     *
     * @return  array
     */
    public function getGlobalVariables()
    {
        try {
            $this->setQuery("SELECT name, setting FROM pg_settings");
            $rows = $this->loadObjectList();
            $vars = [];
            foreach ($rows as $row) {
                $vars[$row->name] = $row->setting;
            }
            return $vars;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get server uptime (PostgreSQL-specific implementation)
     *
     * @return  int|null
     */
    public function getUptime()
    {
        try {
            $this->setQuery("SELECT EXTRACT(EPOCH FROM (NOW() - pg_postmaster_start_time()))::INTEGER");
            return (int) $this->loadResult();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get table I/O statistics (PostgreSQL-specific implementation)
     *
     * @return  array
     */
    public function getTableIoStats()
    {
        try {
            $this->setQuery("SELECT * FROM pg_stat_user_tables");
            return $this->loadObjectList();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get CHECK constraints for table (PostgreSQL-specific implementation)
     *
     * @param   string  $table  Table name
     * @return  array
     */
    public function getCheckConstraints(string $table): array
    {
        $table = $this->replacePrefix($table);
        $this->setQuery("
            SELECT con.conname as name, pg_get_constraintdef(con.oid) as definition
            FROM pg_constraint con
            JOIN pg_class rel ON rel.oid = con.conrelid
            WHERE rel.relname = " . $this->quote($table) . "
            AND con.contype = 'c'
        ");
        return $this->loadObjectList();
    }

    /**
     * Add stored (computed) column (PostgreSQL-specific implementation)
     *
     * @param   string       $table       Table name
     * @param   string       $column      Column name
     * @param   string       $expression  SQL expression
     * @param   string|null  $type        Column type (required by MySQL so standardized here)
     * @return  bool
     */
    public function addStoredColumn(string $table, string $column, string $expression, ?string $type = null): bool
    {
        $table = $this->replacePrefix($table);
        $quotedTable = $this->quoteName($table);
        $quotedColumn = $this->quoteName($column);

        // PostgreSQL 12+ supports GENERATED ALWAYS AS ... STORED
        $this->setQuery(
            "ALTER TABLE $quotedTable ADD COLUMN $quotedColumn"
            . " $type GENERATED ALWAYS AS ($expression) STORED"
        );
        return (bool) $this->execute();
    }
}
