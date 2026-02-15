<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Cubrid;

use Hubzero\Database\ConnectionInterface;
use Hubzero\Database\Exception\ConnectionFailedException;

/**
 * CUBRID (PDO) database driver
 *
 * CUBRID is an open-source relational database management system optimized
 * for web applications and implemented on top of the shared SQL driver contract.
 *
 * Key CUBRID-specific features:
 * - Broad MySQL-style SQL compatibility
 * - Multi-version concurrency control (MVCC)
 * - Native support for Java stored procedures
 * - Built-in high-availability and load balancing
 * - Object-relational features (collections, inheritance)
 * - Full-text search
 * - Spatial data support (R-tree indexing)
 * - Database sharding
 *
 * PDO_CUBRID Extension:
 * Requires the PDO_CUBRID extension (PHP 8.3 compatible version available).
 * See docs/pdo-cubrid-php83-port.md for build instructions.
 */
class CubridDriver extends \Hubzero\Database\Drivers\Base\BaseSqlDriver
{
    /**
     * The name of the database driver
     *
     * @var string
     */
    protected $name = 'cubrid';

    /**
     * CUBRID uses MySQL-style backtick quoting for identifiers.
     *
     * @var string
     */
    protected $wrapper = '`%s`';

    /**
     * Current transaction nesting depth (savepoint support).
     *
     * @var int
     */
    protected $transactionDepth = 0;

    /**
     * Whether the _sequences emulation table has been verified/created.
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
        // Build CUBRID DSN/options before parent initialization.
        if (!isset($options['extras'])) {
            $options['extras'] = [];
        }

        if (!isset($options['dsn'])) {
            // CUBRID DSN format: cubrid:host=hostname;port=33300;dbname=database;charset=utf8
            $options['dsn']  = "cubrid:host={$options['host']}";
            if (isset($options['port'])) {
                $options['dsn'] .= ";port={$options['port']}";
            } else {
                // Default CUBRID port
                $options['dsn'] .= ";port=33300";
            }
            $options['dsn'] .= (isset($options['database']) &&
                $options['database']) ? ";dbname={$options['database']}" : '';
            // Add charset to DSN for REGEXP and other charset-sensitive operations
            $options['dsn'] .= ";charset=utf8";
        }

        if (substr($options['dsn'], 0, 7) != 'cubrid:') {
            throw new ConnectionFailedException('CUBRID DSN for PDO connection does not appear to be valid.', 500);
        }

        parent::__construct($options);

        // Set UTF-8 charset after connection to avoid "incompatible code sets" errors
        // CUBRID is strict about charset compatibility in REGEXP and other operations
        $this->setUTF();
    }

    /**
     * Set the connection to use UTF-8
     *
     * CUBRID uses 'utf8' (not 'utf8mb4' like MySQL). This override ensures
     * compatibility with CUBRID's charset naming.
     *
     * @return  bool  True on success
     */
    public function setUTF()
    {
        // CUBRID uses 'utf8' not 'utf8mb4'
        return $this->setNamesCharset('utf8');
    }

    /**
     * Get database server version information
     *
     * CUBRID version query via SELECT version().
     *
     * @return  array  Array with standardized keys:
     *                  - 'version': Full version string from server
     *                  - 'driver_version': Normalized version (x.y.z format) - STANDARD KEY
     *                  - 'comment': Version comment/description
     */
    public function getServerInfo()
    {
        // CUBRID exposes version info via SELECT version().
        $this->setQuery("SELECT version()");
        $version = $this->loadResult();
        $driverVersion = $this->extractDriverVersionFromString($version);

        return [
            'version'        => $version,
            'driver_version' => $driverVersion,  // Standard key for all drivers
            'comment'        => 'CUBRID Database',
        ];
    }

    /**
     * Retrieves field information about the given table
     *
     * CUBRID uses SHOW COLUMNS (not SHOW FULL COLUMNS) and doesn't support
     * the full metadata shape returned by SHOW FULL COLUMNS. Use CUBRID-compatible syntax.
     *
     * @param   string  $table     The name of the database table
     * @param   bool    $typeOnly  True (default) to only return field types
     * @return  array
     */
    public function getTableColumns($table, $typeOnly = true)
    {
        // CUBRID uses SHOW COLUMNS (not SHOW FULL COLUMNS)
        // Note: Don't use escape() on table names, only quoteName()
        return $this->getTableColumnsFromShowQuery(
            $this->buildShowColumnsQuery($table),
            (bool) $typeOnly
        );
    }

    /**
     * Returns whether or not the given table has a given field
     *
     * CUBRID uses SHOW COLUMNS (not SHOW FIELDS).
     *
     * @param   string  $table  A table name
     * @param   string  $field  A field name
     * @return  bool
     */
    public function tableHasField($table, $field)
    {
        // CUBRID uses SHOW COLUMNS instead of SHOW FIELDS
        return $this->tableHasFieldFromShowQuery(
            $this->buildShowColumnsQuery($table),
            (string) $field
        );
    }

    /**
     * Gets the primary key of a table
     *
     * CUBRID's SHOW COLUMNS includes a 'Key' field that indicates 'PRI' for primary keys.
     *
     * @param   string  $table  The table name
     * @return  string|false
     */
    public function getPrimaryKey($table)
    {
        return $this->getPrimaryKeyFromShowColumns(
            $this->getShowColumnsRows($table),
            'PRI'
        );
    }

    /**
     * Retrieves key information about the given table
     *
     * CUBRID names primary keys with generated identifiers (for example,
     * `pk_tablename_id`) instead of the canonical `PRIMARY` key name. This
     * method normalizes that key name to `PRIMARY` for framework consistency.
     *
     * @param   string|array  $table  A table name
     * @return  array
     */
    public function getTableKeys($table)
    {
        $allKeys = $this->getShowKeysRows($table);

        if (empty($allKeys)) {
            return [];
        }

        // Group keys by Key_name for easier processing
        $groupedKeys = [];
        foreach ($allKeys as $keyInfo) {
            $groupedKeys[$keyInfo->Key_name][] = $keyInfo;
        }

        // Find primary key (looks for key starting with 'pk_' that has Non_unique=0)
        $primaryKeyName = null;
        foreach ($groupedKeys as $keyName => $keyParts) {
            if ($keyParts[0]->Non_unique == 0 && strpos($keyName, 'pk_') === 0) {
                $primaryKeyName = $keyName;
                break;
            }
        }

        // Normalize the detected primary key entries to the canonical name.
        if ($primaryKeyName !== null) {
            $groupedKeys['PRIMARY'] = $groupedKeys[$primaryKeyName];
            foreach ($groupedKeys['PRIMARY'] as $keyPart) {
                $keyPart->Key_name = 'PRIMARY';
            }
            unset($groupedKeys[$primaryKeyName]);
        }

        // Return keyed by Key_name, using the first row for each key.
        $result = [];
        foreach ($groupedKeys as $keyName => $keyParts) {
            $result[$keyName] = $keyParts[0];
        }

        return $result;
    }

    /**
     * Gets index information for a table.
     *
     * @param   string  $table
     * @return  array
     */
    public function getIndexes($table)
    {
        return $this->normalizeIndexesFromTableKeys(
            $this->getTableKeys($table)
        );
    }

    /**
     * Get list of tables.
     *
     * @return  array
     */
    public function getTableList()
    {
        return $this->getTableListFromShowTables();
    }

    /**
     * Show CREATE TABLE output for one or more tables.
     *
     * @param   string|array  $tables
     * @return  array
     */
    public function getTableCreate($tables)
    {
        return $this->getTableCreateFromShowCreate($tables, false, 'TABLE');
    }

    /**
     * Get database collation.
     *
     * @return  string|bool
     */
    public function getCollation()
    {
        return $this->getCollationFromShowVariables();
    }

    /**
     * Get table/database charset.
     *
     * @param   string       $table
     * @param   string|null  $field
     * @return  string|bool
     */
    public function getCharacterSet($table, $field = null)
    {
        return $this->parseCharacterSetFromCreate(
            $this->getTableCreate($table),
            (string) $table,
            $field !== null ? (string) $field : null
        );
    }

    /**
     * Convert table charset/collation.
     *
     * @param   string       $table
     * @param   string       $charset
     * @param   string|null  $collate
     * @return  bool
     */
    public function convertToCharset($table, $charset, $collate = null)
    {
        return $this->convertToCharsetUsingAlter(
            (string) $table,
            (string) $charset,
            $collate !== null ? (string) $collate : null
        );
    }

    /**
     * Lock table for writes.
     *
     * @param   string  $table
     * @return  $this
     */
    public function lockTable($table)
    {
        $this->lockTableForWrite((string) $table);
        return $this;
    }

    /**
     * Unlock all tables.
     *
     * @return  $this
     */
    public function unlockTables()
    {
        $this->unlockAllTables();
        return $this;
    }

    /**
     * Rename table.
     *
     * @param   string  $oldTable
     * @param   string  $newTable
     * @param   string  $backup
     * @param   string  $prefix
     * @return  $this
     */
    public function renameTable($oldTable, $newTable, $backup = null, $prefix = null)
    {
        $this->renameTableSimple((string) $oldTable, (string) $newTable);
        return $this;
    }

    /**
     * Check for table existence.
     *
     * @param   string  $table
     * @return  bool
     */
    public function tableExists($table)
    {
        return $this->tableExistsFromShowTablesLike((string) $table);
    }

    /**
     * Select active database.
     *
     * @param   string  $database
     * @return  bool
     */
    public function select($database)
    {
        if (empty($database)) {
            return false;
        }

        $this->database = $database;
        return true;
    }

    /**
     * SQL helper: INSERT IGNORE.
     *
     * @return  string
     */
    public function sqlInsertIgnore(): string
    {
        return 'INSERT IGNORE INTO';
    }

    /**
     * SQL helper: REPLACE.
     *
     * @return  string
     */
    public function sqlReplace(): string
    {
        return 'REPLACE INTO';
    }

    /**
     * SQL helper: REGEXP predicate.
     *
     * @param   string  $column
     * @param   string  $pattern
     * @param   bool    $not
     * @return  string
     */
    public function sqlRegexp(string $column, string $pattern, bool $not = false): string
    {
        return $this->buildRegexpPredicateSql($column, $pattern, $not);
    }

    /**
     * SQL helper: date subtraction.
     *
     * @param   string  $date
     * @param   int     $value
     * @param   string  $unit
     * @return  string
     */
    public function sqlDateSub(string $date, int $value, string $unit = 'DAY'): string
    {
        return $this->buildDateSubExpression($date, $value, $unit);
    }

    /**
     * SQL helper: date addition.
     *
     * @param   string  $date
     * @param   int     $value
     * @param   string  $unit
     * @return  string
     */
    public function sqlDateAdd(string $date, int $value, string $unit = 'DAY'): string
    {
        return $this->buildDateAddExpression($date, $value, $unit);
    }

    /**
     * SQL helper: date format.
     *
     * @param   string  $date
     * @param   string  $format
     * @return  string
     */
    public function sqlDateFormat(string $date, string $format): string
    {
        return $this->buildDateFormatExpression($date, $format);
    }

    /**
     * SQL helper: YEAR().
     *
     * @param   string  $date
     * @return  string
     */
    public function sqlYear(string $date): string
    {
        return $this->buildYearExpression($date);
    }

    /**
     * SQL helper: MONTH().
     *
     * @param   string  $date
     * @return  string
     */
    public function sqlMonth(string $date): string
    {
        return $this->buildMonthExpression($date);
    }

    /**
     * SQL helper: UNIX_TIMESTAMP().
     *
     * @param   string  $date
     * @return  string
     */
    public function sqlUnixTimestamp(string $date): string
    {
        return $this->buildUnixTimestampExpression($date);
    }

    /**
     * SQL helper: SUBSTRING_INDEX().
     *
     * @param   string  $str
     * @param   string  $delim
     * @param   int     $count
     * @return  string
     */
    public function sqlSubstringIndex(string $str, string $delim, int $count): string
    {
        return $this->buildSubstringIndexExpression($str, $delim, $count);
    }

    /**
     * SQL helper: CONCAT().
     *
     * @param   array  $strings
     * @return  string
     */
    public function sqlConcat(array $strings): string
    {
        return $this->buildConcatExpression($strings);
    }

    /**
     * SQL helper: CONCAT_WS().
     *
     * @param   string  $separator
     * @param   array   $strings
     * @return  string
     */
    public function sqlConcatWs(string $separator, array $strings): string
    {
        return $this->buildConcatWithSeparatorExpression($separator, $strings);
    }

    /**
     * Modify a column definition.
     *
     * @param   string  $table
     * @param   string  $column
     * @param   string  $definition
     * @param   string  $comment
     * @return  bool
     */
    protected function buildModifyColumnSql(
        string $table,
        string $column,
        string $definition,
        string $comment
    ): string {
        return 'ALTER TABLE ' . $this->quoteName($table)
            . ' MODIFY COLUMN ' . $this->quoteName($column) . ' ' . $definition;
    }

    /**
     * Modify a column and move after another column.
     *
     * @param   string  $table
     * @param   string  $column
     * @param   string  $definition
     * @param   string  $afterColumn
     * @param   string  $comment
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
        if (!$this->tableAndFieldExist($table, $column)) {
            return false;
        }

        return $this->executeAlterTableStatement(
            $table,
            'MODIFY COLUMN ' . $this->quoteName($column) . ' ' . $definition
            . ' AFTER ' . $this->quoteName($afterColumn)
        );
    }

    /**
     * Modify a column and move before another column.
     *
     * @param   string  $table
     * @param   string  $column
     * @param   string  $definition
     * @param   string  $beforeColumn
     * @param   string  $comment
     * @return  bool
     */
    public function modifyColumnBefore(
        string $table,
        string $column,
        string $definition,
        string $beforeColumn,
        string $comment = ''
    ): bool {
        $columns = array_keys($this->getTableColumns($table));
        $beforeIndex = array_search($beforeColumn, $columns);
        if ($beforeIndex === false) {
            return $this->modifyColumn($table, $column, $definition, $comment);
        }
        if ($beforeIndex === 0) {
            return $this->modifyColumnFirst($table, $column, $definition, $comment);
        }

        return $this->modifyColumnAfter($table, $column, $definition, $columns[$beforeIndex - 1], $comment);
    }

    /**
     * Modify a column and move it first.
     *
     * @param   string  $table
     * @param   string  $column
     * @param   string  $definition
     * @param   string  $comment
     * @return  bool
     */
    public function modifyColumnFirst(string $table, string $column, string $definition, string $comment = ''): bool
    {
        $table = $this->replacePrefix($table);
        if (!$this->tableAndFieldExist($table, $column)) {
            return false;
        }

        return $this->executeAlterTableStatement(
            $table,
            'MODIFY COLUMN ' . $this->quoteName($column) . ' ' . $definition . ' FIRST'
        );
    }

    /**
     * Rename/change a column.
     *
     * @param   string  $table
     * @param   string  $oldColumn
     * @param   string  $newColumn
     * @param   string  $definition
     * @param   string  $comment
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
        if (!$this->tableAndFieldExist($table, $oldColumn)) {
            return false;
        }

        return $this->executeAlterTableStatement(
            $table,
            'CHANGE COLUMN ' . $this->quoteName($oldColumn)
            . ' ' . $this->quoteName($newColumn)
            . ' ' . $definition
        );
    }

    /**
     * Add a column.
     *
     * @param   string  $table
     * @param   string  $column
     * @param   string  $definition
     * @param   string  $comment
     * @return  bool
     */
    protected function buildAddColumnSql(
        string $table,
        string $column,
        string $definition,
        string $comment
    ): string {
        return 'ALTER TABLE ' . $this->quoteName($table)
            . ' ADD COLUMN ' . $this->quoteName($column) . ' ' . $definition;
    }

    /**
     * Add a column after another column.
     *
     * @param   string  $table
     * @param   string  $column
     * @param   string  $definition
     * @param   string  $afterColumn
     * @param   string  $comment
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
        $precondition = $this->resolveAddColumnPrecondition($table, $column);
        if ($precondition !== null) {
            return $precondition;
        }

        return $this->executeAlterTableStatement(
            $table,
            'ADD COLUMN ' . $this->quoteName($column) . ' ' . $definition
            . ' AFTER ' . $this->quoteName($afterColumn)
        );
    }

    /**
     * Add a column before another column.
     *
     * @param   string  $table
     * @param   string  $column
     * @param   string  $definition
     * @param   string  $beforeColumn
     * @param   string  $comment
     * @return  bool
     */
    public function addColumnBefore(
        string $table,
        string $column,
        string $definition,
        string $beforeColumn,
        string $comment = ''
    ): bool {
        $columns = array_keys($this->getTableColumns($table));
        $beforeIndex = array_search($beforeColumn, $columns);
        if ($beforeIndex === false) {
            return $this->addColumn($table, $column, $definition, $comment);
        }
        if ($beforeIndex === 0) {
            return $this->addColumnFirst($table, $column, $definition, $comment);
        }

        return $this->addColumnAfter($table, $column, $definition, $columns[$beforeIndex - 1], $comment);
    }

    /**
     * Add a column in first position.
     *
     * @param   string  $table
     * @param   string  $column
     * @param   string  $definition
     * @param   string  $comment
     * @return  bool
     */
    public function addColumnFirst(string $table, string $column, string $definition, string $comment = ''): bool
    {
        $table = $this->replacePrefix($table);
        $precondition = $this->resolveAddColumnPrecondition($table, $column);
        if ($precondition !== null) {
            return $precondition;
        }

        return $this->executeAlterTableStatement(
            $table,
            'ADD COLUMN ' . $this->quoteName($column) . ' ' . $definition . ' FIRST'
        );
    }

    /**
     * Add a column in last position.
     *
     * @param   string  $table
     * @param   string  $column
     * @param   string  $definition
     * @param   string  $comment
     * @return  bool
     */
    public function addColumnLast(string $table, string $column, string $definition, string $comment = ''): bool
    {
        return $this->addColumn($table, $column, $definition, $comment);
    }

    /**
     * Set table storage engine.
     *
     * @param   string  $table
     * @param   string  $engine
     * @return  bool
     */
    public function setTableEngine(string $table, string $engine = 'MYISAM'): bool
    {
        // CUBRID uses a single storage engine.
        return true;
    }

    /**
     * Set table charset/collation.
     *
     * @param   string  $table
     * @param   string  $charset
     * @param   string  $collation
     * @return  bool
     */
    public function setTableCharset(
        string $table,
        string $charset = 'utf8',
        string $collation = 'utf8_general_ci'
    ): bool {
        return $this->convertToCharset($table, $charset, $collation);
    }

    /**
     * Drop a column.
     *
     * @param   string  $table
     * @param   string  $column
     * @return  bool
     */
    protected function buildDropColumnSql(string $table, string $column): string
    {
        return 'ALTER TABLE ' . $this->quoteName($table)
            . ' DROP COLUMN ' . $this->quoteName($column);
    }

    /**
     * Add a fulltext index.
     *
     * @param   string        $table
     * @param   string        $name
     * @param   string|array  $columns
     * @return  bool
     */
    public function addFulltextIndex(string $table, string $name, $columns): bool
    {
        // CUBRID does not support MySQL FULLTEXT syntax consistently; degrade to regular index.
        return $this->addIndex($table, $name, $columns, false);
    }

    /**
     * Drop table primary key.
     *
     * @param   string  $table
     * @return  bool
     */
    public function dropPrimaryKey(string $table): bool
    {
        $table = $this->replacePrefix($table);
        $precondition = $this->resolveDropKeyPrecondition($table, 'PRIMARY');
        if ($precondition !== null) {
            return $precondition;
        }

        return $this->executeAlterTableStatement($table, 'DROP PRIMARY KEY');
    }

    /**
     * Add table primary key.
     *
     * @param   string        $table
     * @param   string|array  $columns
     * @return  bool
     */
    public function addPrimaryKey(string $table, $columns): bool
    {
        $table = $this->replacePrefix($table);
        $precondition = $this->resolveAddKeyPrecondition($table, 'PRIMARY');
        if ($precondition !== null) {
            return $precondition;
        }

        $columns = is_array($columns) ? $columns : [$columns];
        $columnList = $this->buildQuotedIdentifierList($columns);

        return $this->executeAlterTableStatement(
            $table,
            'ADD PRIMARY KEY (' . $columnList . ')'
        );
    }

    /**
     * Populate a column with sequential integer values.
     *
     * @param   string       $table
     * @param   string       $column
     * @param   string|null  $orderBy
     * @return  bool
     */
    public function populateSequentialValues(string $table, string $column, ?string $orderBy = null): bool
    {
        $table = $this->replacePrefix($table);
        if (!$this->tableAndFieldExist($table, $column)) {
            return false;
        }

        $pk = $this->getPrimaryKey($table);
        if (!$pk) {
            return false;
        }

        $order = $orderBy ? $this->quoteName($orderBy) : $this->quoteName($pk);
        $this->setQuery(
            'SELECT ' . $this->quoteName($pk)
            . ' FROM ' . $this->quoteName($table)
            . ' ORDER BY ' . $order
        );
        $rows = $this->loadColumn();
        if (!is_array($rows)) {
            return false;
        }

        $i = 1;
        foreach ($rows as $id) {
            $this->setQuery(
                'UPDATE ' . $this->quoteName($table)
                . ' SET ' . $this->quoteName($column) . ' = ' . (int) $i
                . ' WHERE ' . $this->quoteName($pk) . ' = ' . $this->quote($id)
            );
            if (!$this->execute()) {
                return false;
            }
            $i++;
        }

        return true;
    }

    /**
     * Add an index.
     *
     * @param   string        $table
     * @param   string        $name
     * @param   string|array  $columns
     * @param   bool          $unique
     * @return  bool
     */
    protected function buildCreateIndexSql(string $table, string $name, array $columns, bool $unique): string
    {
        $columnList = $this->buildQuotedIdentifierList($columns);
        $uniqueStr = $unique ? 'UNIQUE ' : '';
        return 'ALTER TABLE ' . $this->quoteName($table)
            . ' ADD ' . $uniqueStr . 'INDEX ' . $this->quoteName($name)
            . ' (' . $columnList . ')';
    }

    /**
     * Drop an index.
     *
     * @param   string  $table
     * @param   string  $name
     * @return  bool
     */
    public function dropIndex(string $table, string $name): bool
    {
        $table = $this->replacePrefix($table);
        $precondition = $this->resolveDropKeyPrecondition($table, $name);
        if ($precondition !== null) {
            return $precondition;
        }

        return $this->executeAlterTableStatement(
            $table,
            'DROP INDEX ' . $this->quoteName($name)
        );
    }

    /**
     * Get the schema grammar instance for this driver
     *
     * @return  \Hubzero\Database\Drivers\Cubrid\CubridGrammar
     */
    public function getSchemaGrammar()
    {
        return $this->makeSchemaGrammarFromRegistry();
    }

    /**
     * Check if the database supports UNSIGNED integer modifier
     *
     * CUBRID doesn't support the UNSIGNED keyword.
     *
     * @return  bool
     */
    public function supportsUnsigned(): bool
    {
        return false;
    }

    /**
     * CUBRID allows AUTO_INCREMENT separate from PRIMARY KEY.
     *
     * Returning false ensures TableBuilder emits an explicit PRIMARY KEY
     * clause for id() columns.
     *
     * @return  bool
     */
    public function autoIncrementIncludesPrimaryKey(): bool
    {
        return false;
    }

    /**
     * Build auto-increment column definition.
     *
     * CUBRID uses MySQL-style AUTO_INCREMENT and relies on a separate
     * PRIMARY KEY clause emitted by TableBuilder.
     *
     * @param   string  $quotedName
     * @param   string  $type
     * @return  string
     */
    public function buildAutoIncrementColumn(string $quotedName, string $type): string
    {
        return "$quotedName $type AUTO_INCREMENT";
    }

    /**
     * Normalize column type
     *
     * CUBRID doesn't support UNSIGNED modifier, display widths, or ENUM/SET types. Convert them appropriately.
     *
     * @param   string  $type       The column type
     * @param   array   $modifiers  Column modifiers
     * @return  string
     */
    public function normalizeColumnType(string $type, array $modifiers = []): string
    {
        // Convert ENUM/SET to VARCHAR BEFORE calling parent (they're not abstract types)
        $enumOrSet = $this->mapEnumOrSetTypeToVarchar($type);
        if ($enumOrSet !== null) {
            return $enumOrSet;
        }

        // Call parent to map abstract types
        $normalized = parent::normalizeColumnType($type, $modifiers);

        // Convert TEXT types to STRING (CUBRID's equivalent)
        // CUBRID doesn't have TEXT, MEDIUMTEXT, LONGTEXT - uses STRING for all
        $normalized = preg_replace('/\b(TINY|MEDIUM|LONG)?TEXT\b/i', 'STRING', $normalized);

        // Convert BOOLEAN to TINYINT (CUBRID doesn't support native BOOLEAN)
        $normalized = preg_replace('/\bBOOLEAN\b/i', 'TINYINT', $normalized);

        // Strip UNSIGNED keyword (CUBRID doesn't support it)
        $normalized = preg_replace('/\s+UNSIGNED/i', '', $normalized);

        // Strip display widths from integer types (CUBRID doesn't support them)
        $normalized = preg_replace('/(TINYINT|SMALLINT|MEDIUMINT|INT|BIGINT)\(\d+\)/i', '$1', $normalized);

        return $normalized;
    }

    /**
     * Map raw ENUM/SET declarations to VARCHAR for CUBRID.
     *
     * @param   string  $type
     * @return  string|null
     */
    protected function mapEnumOrSetTypeToVarchar(string $type): ?string
    {
        if (!preg_match('/^(ENUM|SET)\(/i', $type)) {
            return null;
        }

        if (!preg_match('/^(ENUM|SET)\((.*)\)$/i', $type, $matches)) {
            return 'VARCHAR(255)';
        }

        preg_match_all("/'([^']*)'/", $matches[2], $valueMatches);
        $values = $valueMatches[1];

        if (strtoupper($matches[1]) === 'ENUM') {
            $maxLen = 0;
            foreach ($values as $val) {
                $maxLen = max($maxLen, strlen($val));
            }
            return 'VARCHAR(' . max(1, $maxLen) . ')';
        }

        $totalLen = array_sum(array_map('strlen', $values)) + count($values) - 1;
        return 'VARCHAR(' . max(1, $totalLen) . ')';
    }

    /**
     * Build column definition for ALTER TABLE ADD/MODIFY operations
     *
     * Override to prevent UNSIGNED from being added back after normalization.
     * CUBRID doesn't support UNSIGNED modifier or display widths.
     *
     * @param   string  $name        Column name
     * @param   array   $definition  Column definition array with 'type' and 'modifiers' keys
     * @return  string  Column definition SQL
     */
    public function buildAlterColumnDefinition(string $name, array $definition): string
    {
        $type = $definition['type'];
        $modifiers = $definition['modifiers'] ?? [];

        // Normalize abstract types - this strips UNSIGNED and display widths
        $type = $this->normalizeColumnType($type, $modifiers);

        // Detect and normalize AUTO_INCREMENT
        $hasAutoIncrement = !empty($modifiers['autoIncrement']) || stripos($type, 'AUTO_INCREMENT') !== false;
        if ($hasAutoIncrement) {
            $type = preg_replace('/\s*AUTO_INCREMENT\s*/i', ' ', $type);
            $type = trim($type);
        }

        // Keep UNSIGNED omitted; CUBRID does not support it and normalization
        // above has already removed any incoming UNSIGNED declarations.

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
            } elseif (is_string($default)) {
                $parts[] = 'DEFAULT ' . $this->quote($default);
            } else {
                $parts[] = 'DEFAULT ' . (string) $default;
            }
        }

        // AFTER (column positioning)
        if (!empty($modifiers['after'])) {
            $parts[] = 'AFTER `' . $modifiers['after'] . '`';
        }

        return implode(' ', $parts);
    }

    /**
     * Build a foreign key constraint definition for CREATE TABLE
     *
     * CUBRID doesn't support ON UPDATE CASCADE - convert to NO ACTION.
     *
     * @param   array   $fk     Foreign key definition array
     * @param   string  $table  Table name (without prefix)
     * @return  string  Foreign key constraint SQL
     */
    public function buildForeignKeyDefinition(array $fk, string $table): string
    {
        $refTable = $this->replacePrefix($fk['referencedTable']);
        $fkName = !empty($fk['name']) ? $fk['name'] : "fk_{$table}_{$fk['column']}";
        $constraintName = $this->quoteName($fkName);
        $fkColumn = $this->quoteName($fk['column']);
        $refTableQuoted = $this->quoteName($refTable);
        $refColumn = $this->quoteName($fk['referencedColumn']);

        // CUBRID doesn't support ON UPDATE CASCADE - convert to NO ACTION.
        $onDelete = $this->normalizeForeignKeyActionKeyword($fk['onDelete'] ?? 'NO ACTION');
        $onUpdate = $this->normalizeForeignKeyUpdateAction(
            $this->normalizeForeignKeyActionKeyword($fk['onUpdate'] ?? 'NO ACTION')
        );

        $constraint = "CONSTRAINT $constraintName ";
        $constraint .= "FOREIGN KEY ($fkColumn) ";
        $constraint .= "REFERENCES $refTableQuoted ($refColumn) ";
        $constraint .= "ON DELETE $onDelete ON UPDATE $onUpdate";

        return $constraint;
    }

    /**
     * Get foreign key information for a table
     *
     * Uses CUBRID's native PDO::cubrid_schema() method to retrieve
     * foreign key metadata via PDO::CUBRID_SCH_IMPORTED_KEYS.
     *
     * @param   string  $tableName  The table name
     * @return  array   Array of foreign key objects
     */
    public function getForeignKeys($tableName)
    {
        // Do NOT use replacePrefix() - cubrid_schema() needs just the table name
        // without schema prefix. Extract just the table name if prefixed.
        $tableName = $this->replacePrefix($tableName);
        $tableName = $this->stripSchemaPrefix($tableName);

        try {
            // Get the underlying PDO connection to call cubrid_schema()
            $pdo = $this->connection->getPdo();

            // Use CUBRID's native schema introspection method
            // PDO::CUBRID_SCH_IMPORTED_KEYS = 2 (imported foreign keys)
            $rows = $pdo->cubrid_schema(\PDO::CUBRID_SCH_IMPORTED_KEYS, $tableName);

            if (!is_array($rows) || empty($rows)) {
                return [];
            }

            // Group by FK_NAME (constraint name) to handle multi-column FKs
            $foreignKeys = [];
            foreach ($rows as $row) {
                $name = $this->resolveForeignKeyNameFromSchemaRow($row, $tableName);

                if (!isset($foreignKeys[$name])) {
                    $foreignKeys[$name] = $this->createForeignKeyInfoFromSchemaRow($name, $row);
                }

                $this->appendForeignKeyColumnsFromSchemaRow($foreignKeys[$name], $row);
            }

            return array_values($foreignKeys);
        } catch (\Throwable $e) {
            // If cubrid_schema() fails, return empty array
            return [];
        }
    }

    /**
     * Get primary key columns for a table
     *
     * CUBRID doesn't support WHERE clause with SHOW KEYS, and names primary keys
     * as "pk_tablename_..." instead of "PRIMARY". Override to fetch all keys and
     * filter for the pk_ prefix.
     *
     * @param   string  $table  The table name
     * @return  array   Array of primary key column names
     */
    public function getPrimaryKeyColumns($table): array
    {
        // CUBRID doesn't support: SHOW KEYS FROM table WHERE Key_name = 'PRIMARY'
        // Instead, get all keys and filter in PHP
        $allKeys = $this->getShowKeysRows($table);

        // CUBRID names primary keys as "pk_tablename_..." and unique indexes as "u_tablename_..."
        // Filter for keys whose name starts with "pk_"
        $rows = [];
        foreach ($allKeys as $key) {
            if (isset($key->Key_name) && strpos($key->Key_name, 'pk_') === 0) {
                $rows[] = $key;
            }
        }

        return $this->extractPrimaryKeyColumnsFromTableKeyRows($rows);
    }

    /**
     * Add an auto-incrementing primary key column to an existing table
     *
     * CUBRID doesn't support the SERIAL type (MySQL shorthand for
     * BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE). Use explicit
     * BIGINT AUTO_INCREMENT syntax instead (no UNSIGNED since CUBRID
     * doesn't support it).
     *
     * @param   string  $table      Table name (with optional prefix)
     * @param   string  $column     Column name (default: 'id')
     * @param   bool    $first      Add column as first column (default: false)
     * @param   bool    $useBigInt  Use BIGINT instead of INT (default: true)
     * @return  bool    True on success
     */
    public function addAutoIncrementPrimaryKey(
        string $table,
        string $column = 'id',
        bool $first = false,
        bool $useBigInt = true
    ): bool {
        $table = $this->replacePrefix($table);
        $precondition = $this->resolveAddColumnPrecondition($table, $column);
        if ($precondition !== null) {
            return $precondition;
        }

        // CUBRID requires special handling for tables with existing data
        // AUTO_INCREMENT does NOT automatically populate existing rows, so we must:
        // 1. Add nullable column first
        // 2. Manually populate with sequential values
        // 3. Make it NOT NULL + AUTO_INCREMENT
        // 4. Add PRIMARY KEY
        // 5. Set AUTO_INCREMENT start value
        $type = $useBigInt ? 'BIGINT' : 'INT';
        $quotedTable = $this->quoteName($table);
        $quotedColumn = $this->quoteName($column);
        $position = $first ? ' FIRST' : '';

        try {
            // Step 1: Add column as nullable INT (no AUTO_INCREMENT yet)
            $this->setQuery("ALTER TABLE {$quotedTable} ADD COLUMN {$quotedColumn} {$type}{$position}");
            $this->execute();

            // Step 2: Check if table has existing rows that need IDs
            $this->setQuery("SELECT COUNT(*) as cnt FROM {$quotedTable}");
            $result = $this->loadObject();
            $rowCount = (int) $result->cnt;

            if ($rowCount > 0) {
                // Table has data - populate the new column with sequential values
                // We need to identify each row uniquely, so we'll use all columns as a composite key
                $this->setQuery("SELECT * FROM {$quotedTable}");
                $rows = $this->loadObjectList();

                $counter = 1;
                foreach ($rows as $row) {
                    // Build WHERE clause using all columns to uniquely identify this row
                    $whereParts = [];
                    foreach ((array) $row as $colName => $colValue) {
                        if ($colName === $column) {
                            continue; // Skip the ID column we just added
                        }
                        $quotedCol = $this->quoteName($colName);
                        if ($colValue === null) {
                            $whereParts[] = "{$quotedCol} IS NULL";
                        } else {
                            $quotedVal = $this->quote($colValue);
                            $whereParts[] = "{$quotedCol} = {$quotedVal}";
                        }
                    }

                    if (!empty($whereParts)) {
                        $whereClause = implode(' AND ', $whereParts);
                        $this->setQuery("UPDATE {$quotedTable} SET {$quotedColumn} = {$counter} WHERE {$whereClause}");
                        $this->execute();
                        $counter++;
                    }
                }
            }

            // Step 3: Make column NOT NULL with AUTO_INCREMENT
            $this->setQuery("ALTER TABLE {$quotedTable} MODIFY {$quotedColumn} {$type} NOT NULL AUTO_INCREMENT");
            $this->execute();

            // Step 4: Add PRIMARY KEY
            $this->setQuery("ALTER TABLE {$quotedTable} ADD PRIMARY KEY ({$quotedColumn})");
            $this->execute();

            // Step 5: Set AUTO_INCREMENT to start after the highest existing value
            if ($rowCount > 0) {
                $this->setQuery("SELECT MAX({$quotedColumn}) as max_id FROM {$quotedTable}");
                $result = $this->loadObject();
                $nextId = ((int) $result->max_id) + 1;
                $this->setQuery("ALTER TABLE {$quotedTable} AUTO_INCREMENT = {$nextId}");
                $this->execute();
            }

            return true;
        } catch (\Throwable $e) {
            // If any step fails, try to clean up and return false
            try {
                if ($this->tableHasField($table, $column)) {
                    $this->setQuery("ALTER TABLE {$quotedTable} DROP COLUMN {$quotedColumn}");
                    $this->execute();
                }
            } catch (\Throwable $cleanupException) {
                // Ignore cleanup errors
            }
            return false;
        }
    }

    /**
     * Begin a transaction
     *
     * CUBRID doesn't support START TRANSACTION or BEGIN as SQL statements.
     * Use PDO's native beginTransaction() method instead.
     * Supports nested transactions via savepoints.
     *
     * @return  void
     */
    public function transactionStart()
    {
        if ($this->transactionDepth == 0) {
            // Use PDO's native transaction support
            if ($this->connection instanceof ConnectionInterface) {
                $this->connection->getPdo()->beginTransaction();
            } else {
                $this->connection->beginTransaction();
            }
        } else {
            $this->setQuery('SAVEPOINT SP_' . $this->transactionDepth)->execute();
        }

        $this->transactionDepth++;
    }

    /**
     * Commits a transaction
     *
     * CUBRID doesn't support RELEASE SAVEPOINT syntax. When committing a nested
     * transaction (savepoint), we don't need to explicitly release it - savepoints
     * are automatically released when the outer transaction commits.
     *
     * @return  void
     */
    public function transactionCommit()
    {
        // For nested transactions: CUBRID doesn't support RELEASE SAVEPOINT.
        // Savepoints are automatically released on outer COMMIT.
        $this->transactionCommitWithSavepoints(false);
    }

    /**
     * Rolls back a transaction
     *
     * Supports nested transactions via savepoints.
     * CUBRID uses standard ROLLBACK TO SAVEPOINT syntax.
     *
     * @return  void
     */
    public function transactionRollback()
    {
        $this->transactionRollbackWithSavepoints();
    }

    /**
     * Creates or replaces a database view
     *
     * CUBRID doesn't support ALGORITHM, DEFINER, or SQL SECURITY clauses.
     * Use simple CREATE OR REPLACE VIEW syntax.
     *
     * @param   string  $name       The view name (with or without prefix)
     * @param   string  $selectSql  The SELECT statement for the view
     * @param   array   $options    Ignored for CUBRID (MySQL compatibility)
     * @return  bool
     */
    public function createOrReplaceView($name, $selectSql, array $options = []): bool
    {
        $viewName = str_replace('#__', $this->tablePrefix, $name);
        $selectSql = str_replace('#__', $this->tablePrefix, $selectSql);

        // Simple CUBRID syntax: CREATE OR REPLACE VIEW view_name AS SELECT ...
        $sql = 'CREATE OR REPLACE VIEW ' . $this->quoteName($viewName) . ' AS ' . $selectSql;
        $this->setQuery($sql);
        $this->execute();

        return true;
    }

    /**
     * Gets the next AUTO_INCREMENT value for a table
     *
     * CUBRID stores auto-increment metadata in the db_serial system table.
     * Query it to get the current value and add the increment to get the next value.
     *
     * @param   string  $table  The table name
     * @return  int|false  The next auto-increment value, or false if not found
     */
    public function getAutoIncrement($table)
    {
        $table = $this->replacePrefix($table);

        // Query db_serial system table for the auto-increment serial
        // The serial name follows the pattern: tablename_ai_columnname
        $this->setQuery(
            "SELECT current_val, increment_val
             FROM db_serial
             WHERE class_name = " . $this->quote($table)
        );

        $result = $this->loadObject();

        if (!$result) {
            return false;
        }

        return $this->resolveNextAutoIncrementFromSerialRow($result);
    }

    /**
     * Sets the auto-increment starting value for the given table
     *
     * CUBRID doesn't support SET FOREIGN_KEY_CHECKS, so we skip the
     * TRUNCATE TABLE approach and just use ALTER TABLE directly.
     *
     * CUBRID restriction: ALTER TABLE ... AUTO_INCREMENT requires the table
     * to have exactly one AUTO_INCREMENT column. If the table doesn't have
     * an AUTO_INCREMENT column, this is a no-op.
     *
     * @param   string  $table  The table name
     * @param   int     $value  The auto-increment starting value
     * @return  bool
     */
    public function setAutoIncrement($table, $value): bool
    {
        $table = $this->replacePrefix($table);
        $value = max(1, (int) $value);

        // Only set AUTO_INCREMENT if the table actually has an AUTO_INCREMENT column
        if ($this->tableHasAutoIncrementColumn($table)) {
            $this->setQuery("ALTER TABLE " . $this->quoteName($table) . " AUTO_INCREMENT = $value");
            $this->execute();
        }

        return true;
    }

    /**
     * Resolve and validate storage engine
     *
     * CUBRID doesn't support SHOW ENGINES or multiple storage engines.
     * Always return null (use default engine).
     *
     * @param   string|null  $engine  Requested engine (ignored)
     * @return  string|null  Always null for CUBRID
     */
    protected function resolveEngine(?string $engine): ?string
    {
        // CUBRID uses a single storage engine
        return null;
    }

    /**
     * Build table options clause for CREATE TABLE
     *
     * CUBRID doesn't support ENGINE, DEFAULT CHARSET, or COLLATE options.
     * Return empty string.
     *
     * @param   string|null  $engine     Storage engine (ignored)
     * @param   string|null  $charset    Character set (ignored)
     * @param   string|null  $collation  Collation (ignored)
     * @return  string  Empty string for CUBRID
     */
    public function buildTableOptions($engine = null, $charset = null, $collation = null): string
    {
        // CUBRID doesn't support table options
        return '';
    }

    /**
     * Drop a view
     *
     * @param   string  $name      The view name
     * @param   bool    $ifExists  Add IF EXISTS clause
     * @return  bool
     */
    public function dropView($name, $ifExists = true): bool
    {
        $this->hasConnectionOrFail();

        $sql = 'DROP VIEW ';
        if ($ifExists) {
            $sql .= 'IF EXISTS ';
        }
        $sql .= $this->quoteName($name);

        $this->setQuery($sql);
        $this->execute();

        return true;
    }

    /**
     * Check if a view exists
     *
     * CUBRID doesn't support information_schema.views.
     * Use SHOW TABLES to check for view existence.
     *
     * @param   string  $viewName  The view name
     * @return  bool
     */
    public function viewExists($viewName): bool
    {
        $viewName = str_replace('#__', $this->tablePrefix, $viewName);

        // CUBRID's SHOW TABLES includes views
        $tables = $this->getTableListFromShowTables();

        return in_array($viewName, $tables);
    }

    /**
     * Return list of views in the current database.
     *
     * @return  array
     */
    public function getViews(): array
    {
        // CUBRID SHOW TABLES includes views; treat non-system objects as view candidates.
        return $this->getTableListFromShowTables();
    }

    /**
     * Determine if a table has an AUTO_INCREMENT column.
     *
     * @param   string  $table
     * @return  bool
     */
    protected function tableHasAutoIncrementColumn($table): bool
    {
        foreach ($this->getShowColumnsRows($table) as $col) {
            if (stripos($col->Extra ?? '', 'auto_increment') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Load SHOW COLUMNS rows for a table as objects.
     *
     * @param   string  $table
     * @return  array
     */
    protected function getShowColumnsRows($table): array
    {
        $this->setQuery($this->buildShowColumnsQuery($table));
        $columns = $this->loadObjectList();

        return is_array($columns) ? $columns : [];
    }

    /**
     * Load SHOW KEYS rows for a table as objects.
     *
     * @param   string  $table
     * @return  array
     */
    protected function getShowKeysRows($table): array
    {
        return $this->getTableKeysFromShowKeys((string) $table);
    }

    /**
     * Get ENUM values for a column.
     *
     * @param   string  $table
     * @param   string  $column
     * @return  array
     */
    public function getEnumValues($table, $column)
    {
        $table = $this->replacePrefix($table);
        $result = $this->getShowColumnAssoc($table, $column);

        if (!$result || empty($result['Type'])) {
            return [];
        }

        return $this->parseEnumValuesFromColumnType($result['Type']);
    }

    /**
     * Add ENUM value.
     *
     * @param   string  $table
     * @param   string  $column
     * @param   string  $value
     * @return  bool
     */
    public function addEnumValue($table, $column, $value)
    {
        $currentValues = $this->getEnumValues($table, $column);
        if (empty($currentValues) || in_array($value, $currentValues)) {
            return true;
        }

        $currentValues[] = $value;
        $enumDef = $this->buildEnumColumnDefinition($currentValues);

        return $this->modifyColumn($table, $column, $enumDef);
    }

    /**
     * Remove ENUM value.
     *
     * @param   string  $table
     * @param   string  $column
     * @param   string  $value
     * @return  bool
     */
    public function removeEnumValue($table, $column, $value)
    {
        $currentValues = $this->getEnumValues($table, $column);
        if (empty($currentValues) || !in_array($value, $currentValues)) {
            return true;
        }

        $currentValues = array_filter($currentValues, function ($v) use ($value) {
            return $v !== $value;
        });

        if (empty($currentValues)) {
            return false;
        }

        $enumDef = $this->buildEnumColumnDefinition($currentValues);
        return $this->modifyColumn($table, $column, $enumDef);
    }

    /**
     * Fetch SHOW COLUMNS row for a specific column as an associative array.
     *
     * @param   string  $table
     * @param   string  $column
     * @return  array|null
     */
    protected function getShowColumnAssoc($table, $column)
    {
        $this->setQuery(
            $this->buildShowColumnsQuery($table)
            . ' WHERE Field = ' . $this->quote($column)
        );

        return $this->loadAssoc();
    }

    /**
     * Determine whether a table exists and contains the specified field.
     *
     * @param   string  $table
     * @param   string  $field
     * @return  bool
     */
    protected function tableAndFieldExist($table, $field): bool
    {
        return $this->tableExists($table) && $this->tableHasField($table, $field);
    }

    /**
     * Determine whether a table is missing or does not contain a field.
     *
     * @param   string  $table
     * @param   string  $field
     * @return  bool
     */
    protected function tableMissingOrFieldMissing($table, $field): bool
    {
        return !$this->tableAndFieldExist($table, $field);
    }

    /**
     * Resolve common preconditions for column-add operations.
     *
     * Returns:
     * - `false` when table is missing
     * - `true` when column already exists (nothing to do)
     * - `null` when caller should proceed with add operation
     *
     * @param   string  $table
     * @param   string  $column
     * @return  bool|null
     */
    protected function resolveAddColumnPrecondition($table, $column)
    {
        if (!$this->tableExists($table)) {
            return false;
        }

        if ($this->tableHasField($table, $column)) {
            return true;
        }

        return null;
    }

    /**
     * Resolve common preconditions for add-key operations.
     *
     * Returns:
     * - `false` when table is missing
     * - `true` when key already exists (nothing to do)
     * - `null` when caller should proceed with add operation
     *
     * @param   string  $table
     * @param   string  $keyName
     * @return  bool|null
     */
    protected function resolveAddKeyPrecondition($table, $keyName)
    {
        if (!$this->tableExists($table)) {
            return false;
        }

        if ($this->tableHasKey($table, $keyName)) {
            return true;
        }

        return null;
    }

    /**
     * Resolve common preconditions for drop-key operations.
     *
     * Returns:
     * - `false` when table is missing
     * - `true` when key is already absent (nothing to do)
     * - `null` when caller should proceed with drop operation
     *
     * @param   string  $table
     * @param   string  $keyName
     * @return  bool|null
     */
    protected function resolveDropKeyPrecondition($table, $keyName)
    {
        if (!$this->tableExists($table)) {
            return false;
        }

        if (!$this->tableHasKey($table, $keyName)) {
            return true;
        }

        return null;
    }

    /**
     * Execute an ALTER TABLE statement against a prefixed table name.
     *
     * @param   string  $table   Prefixed table name
     * @param   string  $clause  Clause appended after "ALTER TABLE <table>"
     * @return  bool
     */
    protected function executeAlterTableStatement($table, $clause): bool
    {
        $this->setQuery(
            'ALTER TABLE ' . $this->quoteName($table) . ' ' . trim((string) $clause)
        );

        return (bool) $this->execute();
    }

    /**
     * Build a comma-separated, quoted identifier list.
     *
     * @param   array  $identifiers
     * @return  string
     */
    protected function buildQuotedIdentifierList(array $identifiers): string
    {
        $quoted = [];
        foreach ($identifiers as $identifier) {
            $quoted[] = $this->quoteName((string) $identifier);
        }

        return implode(', ', $quoted);
    }

    /**
     * Map CUBRID FK action code to SQL keyword.
     *
     * @param   mixed  $actionCode
     * @return  string
     */
    protected function mapForeignKeyActionCodeToSql($actionCode): string
    {
        $map = $this->getForeignKeyActionMap();
        return $map[$actionCode] ?? 'RESTRICT';
    }

    /**
     * Resolve FK name from a CUBRID schema row.
     *
     * @param   array   $row
     * @param   string  $tableName
     * @return  string
     */
    protected function resolveForeignKeyNameFromSchemaRow(array $row, string $tableName): string
    {
        if (!empty($row['FK_NAME'])) {
            return (string) $row['FK_NAME'];
        }

        return 'fk_' . $tableName . '_' . ($row['FKCOLUMN_NAME'] ?? 'unknown');
    }

    /**
     * Create normalized foreign key info object from a schema row.
     *
     * @param   string  $name
     * @param   array   $row
     * @return  object
     */
    protected function createForeignKeyInfoFromSchemaRow(string $name, array $row)
    {
        // Field names are DELETE_RULE and UPDATE_RULE, not DELETE_ACTION/UPDATE_ACTION.
        $deleteAction = $this->mapForeignKeyActionCodeToSql($row['DELETE_RULE'] ?? null);
        $updateAction = $this->mapForeignKeyActionCodeToSql($row['UPDATE_RULE'] ?? null);

        // PKTABLE_NAME may include schema prefix (e.g., "schema.tablename").
        $pkTable = $this->stripSchemaPrefix($row['PKTABLE_NAME'] ?? '');

        return (object) [
            'name'            => $name,
            'columns'         => [],
            'foreign_table'   => $pkTable,
            'foreign_columns' => [],
            'on_update'       => $updateAction,
            'on_delete'       => $deleteAction,
        ];
    }

    /**
     * Append local/foreign column names from schema row to FK info object.
     *
     * @param   object  $foreignKeyInfo
     * @param   array   $row
     * @return  void
     */
    protected function appendForeignKeyColumnsFromSchemaRow($foreignKeyInfo, array $row): void
    {
        // Add columns in KEY_SEQ order.
        if (isset($row['FKCOLUMN_NAME'])) {
            $foreignKeyInfo->columns[] = $row['FKCOLUMN_NAME'];
        }

        if (isset($row['PKCOLUMN_NAME'])) {
            $foreignKeyInfo->foreign_columns[] = $row['PKCOLUMN_NAME'];
        }
    }

    /**
     * Return CUBRID FK action code map.
     *
     * @return  array
     */
    protected function getForeignKeyActionMap(): array
    {
        return [
            0 => 'CASCADE',
            1 => 'RESTRICT',
            2 => 'NO ACTION',
            3 => 'SET NULL',
        ];
    }

    /**
     * Normalize ON UPDATE action for CUBRID compatibility.
     *
     * @param   string  $action
     * @return  string
     */
    protected function normalizeForeignKeyUpdateAction(string $action): string
    {
        return strtoupper($action) === 'CASCADE' ? 'NO ACTION' : $action;
    }

    /**
     * Normalize FK action keyword formatting.
     *
     * @param   string  $action
     * @return  string
     */
    protected function normalizeForeignKeyActionKeyword(string $action): string
    {
        return strtoupper(trim($action));
    }

    /**
     * Return no-op constraint toggle result for CUBRID.
     *
     * @return  bool
     */
    protected function constraintsToggleNoop(): bool
    {
        // CUBRID doesn't provide a FOREIGN_KEY_CHECKS session toggle.
        return true;
    }

    /**
     * Return the single storage engine name used by CUBRID.
     *
     * @return  string
     */
    protected function getDefaultEngineName(): string
    {
        return 'CUBRID';
    }

    /**
     * Normalize sequence name with table prefix replacement.
     *
     * @param   string  $name
     * @return  string
     */
    protected function normalizeSequenceName($name): string
    {
        return $this->replacePrefix((string) $name);
    }

    /**
     * Build `_sequences` name filter clause.
     *
     * @param   string  $name
     * @return  string
     */
    protected function buildSequenceNameWhereClause($name): string
    {
        return '`name` = ' . $this->quote((string) $name);
    }

    /**
     * Build SHOW COLUMNS query for a table.
     *
     * @param   string  $table
     * @return  string
     */
    protected function buildShowColumnsQuery($table): string
    {
        return $this->buildShowColumnsQueryForDriver((string) $table, false, false, 'COLUMNS');
    }

    /**
     * Strip optional schema prefix from a qualified object name.
     *
     * @param   string  $name
     * @return  string
     */
    protected function stripSchemaPrefix($name): string
    {
        $name = (string) $name;
        if (strpos($name, '.') === false) {
            return $name;
        }

        $parts = explode('.', $name);
        return (string) array_pop($parts);
    }

    /**
     * Resolve next AUTO_INCREMENT value from db_serial row payload.
     *
     * @param   object  $row
     * @return  int|false
     */
    protected function resolveNextAutoIncrementFromSerialRow($row)
    {
        if (!is_object($row) || !isset($row->current_val, $row->increment_val)) {
            return false;
        }

        return (int) $row->current_val + (int) $row->increment_val;
    }

    /**
     * Parse enum(...) column type string into value list.
     *
     * @param   string  $columnType
     * @return  array
     */
    protected function parseEnumValuesFromColumnType($columnType): array
    {
        if (preg_match("/^enum\\('(.*)'\\)$/i", (string) $columnType, $matches)) {
            return explode("','", $matches[1]);
        }

        return [];
    }

    /**
     * Build ENUM(...) type definition from values.
     *
     * @param   array  $values
     * @return  string
     */
    protected function buildEnumColumnDefinition(array $values): string
    {
        return "ENUM('" . implode("','", array_values($values)) . "')";
    }

    /**
     * List emulated sequences.
     *
     * @return  array
     */
    public function getSequences(): array
    {
        $this->ensureSequenceTable();
        $this->setQuery('SELECT * FROM `_sequences` ORDER BY `name`');
        $rows = $this->loadObjectList();

        if (!is_array($rows)) {
            return [];
        }

        return array_map([$this, 'mapSequenceRowToInfo'], $rows);
    }

    /**
     * Create a new emulated sequence.
     *
     * @param   string  $name
     * @param   int     $start
     * @param   int     $increment
     * @param   array   $options
     * @return  bool
     */
    public function createSequence($name, $start = 1, $increment = 1, array $options = []): bool
    {
        $this->ensureSequenceTable();
        $name = $this->normalizeSequenceName($name);
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
     * Map one `_sequences` row payload to SequenceInfo.
     *
     * @param   object  $row
     * @return  \Hubzero\Database\Schema\SequenceInfo
     */
    protected function mapSequenceRowToInfo($row): \Hubzero\Database\Schema\SequenceInfo
    {
        return new \Hubzero\Database\Schema\SequenceInfo([
            'name'          => (string) ($row->name ?? ''),
            'current_value' => (int) ($row->current_value ?? 0),
            'increment'     => (int) ($row->increment_value ?? 1),
        ]);
    }

    /**
     * Drop an emulated sequence.
     *
     * @param   string  $name
     * @param   bool    $ifExists
     * @return  bool
     */
    public function dropSequence($name, $ifExists = true): bool
    {
        $this->ensureSequenceTable();
        $name = $this->normalizeSequenceName($name);
        $this->setQuery('DELETE FROM `_sequences` WHERE ' . $this->buildSequenceNameWhereClause($name));
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
     * Check if an emulated sequence exists.
     *
     * @param   string  $name
     * @return  bool
     */
    public function sequenceExists($name): bool
    {
        $this->ensureSequenceTable();
        $name = $this->normalizeSequenceName($name);

        $this->setQuery('SELECT COUNT(*) FROM `_sequences` WHERE ' . $this->buildSequenceNameWhereClause($name));
        return (int) $this->loadResult() > 0;
    }

    /**
     * Get current emulated sequence value (without incrementing).
     *
     * @param   string  $name
     * @return  int
     */
    public function currentSequenceValue($name): int
    {
        $this->ensureSequenceTable();
        $name = $this->normalizeSequenceName($name);

        $this->setQuery('SELECT `current_value` FROM `_sequences` WHERE ' . $this->buildSequenceNameWhereClause($name));
        $result = $this->loadResult();

        return $result !== null ? (int) $result : 0;
    }

    /**
     * Sequence support flag.
     *
     * @return  bool
     */
    public function supportsSequences(): bool
    {
        return true;
    }

    /**
     * CUBRID uses INTEGER for CAST expressions.
     *
     * @return  string
     */
    public function getIntegerCastKeyword(): string
    {
        return 'INTEGER';
    }

    /**
     * CUBRID implements sequences via `_sequences` table emulation.
     *
     * @return  bool
     */
    public function usesSequenceEmulation(): bool
    {
        return true;
    }

    /**
     * Disable foreign key constraints
     *
     * CUBRID doesn't support SET FOREIGN_KEY_CHECKS.
     * This is a no-op for CUBRID.
     *
     * @return  bool
     */
    public function disableConstraints()
    {
        return $this->constraintsToggleNoop();
    }

    /**
     * Enable foreign key constraints
     *
     * CUBRID doesn't support SET FOREIGN_KEY_CHECKS.
     * This is a no-op for CUBRID.
     *
     * @return  bool
     */
    public function enableConstraints()
    {
        return $this->constraintsToggleNoop();
    }

    /**
     * Get available storage engines
     *
     * CUBRID doesn't support SHOW ENGINES.
     * Return a default engine list.
     *
     * @return  array
     */
    public function getEngines()
    {
        $engine = $this->getDefaultEngineName();

        return [
            (object) [
                'Engine' => $engine,
                'Support' => 'DEFAULT',
                'Comment' => $engine . ' default storage engine',
            ]
        ];
    }

    /**
     * Gets the database engine of the given table
     *
     * CUBRID doesn't support SHOW TABLE STATUS.
     * Return 'CUBRID' as the engine since CUBRID uses a single storage engine.
     *
     * @param   string  $table  The table for which to retrieve the engine type
     * @return  string  Always 'CUBRID'
     */
    public function getEngine($table)
    {
        return $this->getDefaultEngineName();
    }

    /**
     * Strip milliseconds from DATETIME/TIMESTAMP values
     *
     * CUBRID returns DATETIME with millisecond precision (.000) which differs
     * from MySQL behavior. This method strips the milliseconds for consistency.
     *
     * @param   mixed  $value  The value to process
     * @return  mixed
     */
    protected function stripMilliseconds($value)
    {
        if (is_string($value)) {
            // Strip .000 from datetime strings like "2024-06-15 10:30:00.000"
            return preg_replace('/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\.000$/', '$1', $value);
        }

        if (is_array($value)) {
            return array_map([$this, 'stripMilliseconds'], $value);
        }

        if (is_object($value)) {
            foreach ($value as $key => $prop) {
                $value->$key = $this->stripMilliseconds($prop);
            }
        }

        return $value;
    }

    /**
     * Fetches a row from the result set cursor as an object
     *
     * @param   string       $class  The class name to use for the returned row object
     * @return  object|null
     */
    protected function fetchObject($class = 'stdClass')
    {
        return $this->postProcessFetchedRow(
            parent::fetchObject($class)
        );
    }

    /**
     * Fetches a row from the result set as an array
     *
     * @return  array|null
     */
    protected function fetchArray()
    {
        return $this->postProcessFetchedRow(
            parent::fetchArray()
        );
    }

    /**
     * Fetches a row from the result set as an associative array
     *
     * @return  array|null
     */
    protected function fetchAssoc()
    {
        return $this->postProcessFetchedRow(
            parent::fetchAssoc()
        );
    }

    /**
     * Apply CUBRID fetch-value normalization to one fetched row payload.
     *
     * @param   mixed  $row
     * @return  mixed
     */
    protected function postProcessFetchedRow($row)
    {
        return $row ? $this->stripMilliseconds($row) : $row;
    }

    /**
     * Escape a string for SQL query usage
     *
     * PDO_CUBRID's quote() method has a quirk: it does NOT add surrounding quotes
     * like other PDO drivers do. It only escapes the internal quotes.
     * Most drivers: quote("test'value") → 'test''value' (with surrounding quotes)
     * PDO_CUBRID: quote("test'value") → test''value (without surrounding quotes)
     *
     * @param   string  $text   The string to be escaped
     * @param   bool    $extra  Optional parameter to escape extra chars (%, _)
     * @return  string  The escaped string
     */
    public function escape($text, $extra = false)
    {
        if ($this->connection instanceof ConnectionInterface) {
            $pdo = $this->connection->getPdo();
        } else {
            $pdo = $this->connection;
        }

        // PDO_CUBRID's quote() doesn't add surrounding quotes, so we don't strip them
        $result = $pdo->quote($text ?? '');

        if ($extra) {
            $result = addcslashes($result, '%_');
        }

        return $result;
    }

    /**
     * Binds the given bindings to the prepared statement
     *
     * CUBRID's PDO driver has a bug where it fails with "Type conversion error"
     * when using PDO::PARAM_INT type hints in bindValue(). Work around this by
     * not specifying types - let PDO infer them from the PHP values.
     *
     * @param   array  $bindings  The param bindings
     * @param   array  $type      The param types (ignored for CUBRID)
     * @return  $this
     */
    public function bind($bindings, $type = [])
    {
        $idx = 1;

        $this->bindings = $bindings;

        foreach ($bindings as $binding) {
            // CUBRID: Don't use type hints - bindValue without type parameter
            $this->statement->bindValue($idx, $binding);
            $idx++;
        }

        return $this;
    }

    /**
     * Get the next value for an emulated sequence (atomic increment and return)
     *
     * CUBRID doesn't support LAST_INSERT_ID() function. Use a transaction
     * with SELECT FOR UPDATE to achieve atomic increment.
     *
     * @param   string  $name  The sequence name
     * @return  int
     */
    public function nextSequenceValue($name): int
    {
        $this->ensureSequenceTable();
        $name = $this->normalizeSequenceName($name);

        // Check PDO's actual transaction state
        $pdo = $this->connection->getPdo();
        $wasInTransaction = $pdo->inTransaction();

        if (!$wasInTransaction) {
            $pdo->beginTransaction();
        }

        try {
            // Lock the row and get current value + increment
            $this->setQuery(
                'SELECT `current_value`, `increment_value` FROM `_sequences` '
                . 'WHERE ' . $this->buildSequenceNameWhereClause($name) . ' FOR UPDATE'
            );
            $row = $this->loadObject();

            if (!$row) {
                throw new \RuntimeException("Sequence '$name' does not exist");
            }

            $newValue = (int) $row->current_value + (int) $row->increment_value;

            // Update the sequence
            $this->setQuery(
                'UPDATE `_sequences` SET `current_value` = ' . $newValue
                . ' WHERE ' . $this->buildSequenceNameWhereClause($name)
            );
            $this->execute();

            if (!$wasInTransaction) {
                $pdo->commit();
            }

            return $newValue;
        } catch (\Throwable $e) {
            if (!$wasInTransaction && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Ensure the _sequences table exists for sequence emulation
     *
     * Override to remove ENGINE=InnoDB clause which CUBRID doesn't support.
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
                . ')'  // No ENGINE clause for CUBRID
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
     * Test whether the driver is available for use
     *
     * @return  bool
     */
    public static function test()
    {
        return extension_loaded('pdo_cubrid');
    }
}
