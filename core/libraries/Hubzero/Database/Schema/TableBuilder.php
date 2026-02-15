<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

use Hubzero\Database\Driver;

/**
 * Fluent table builder for CREATE TABLE statements
 *
 * Provides a database-agnostic way to create tables that works with both
 * MySQL and SQLite backends.
 *
 */
class TableBuilder
{
    /**
     * The database driver instance
     *
     * @var Driver
     */
    protected $driver;

    /**
     * The table name
     *
     * @var string
     */
    protected $table;

    /**
     * Column definitions
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Primary key column(s)
     *
     * @var array
     */
    protected $primaryKey = [];

    /**
     * Index definitions
     *
     * @var array
     */
    protected $indexes = [];

    /**
     * Unique index definitions
     *
     * @var array
     */
    protected $uniqueIndexes = [];

    /**
     * Fulltext index definitions
     *
     * @var array
     */
    protected $fulltextIndexes = [];

    /**
     * Foreign key definitions
     *
     * @var array
     */
    protected $foreignKeys = [];

    /**
     * Table engine (MySQL only)
     *
     * @var string
     */
    protected $tableEngine = 'InnoDB';

    /**
     * Table charset (MySQL only)
     *
     * @var string
     */
    protected $tableCharset = 'utf8';

    /**
     * Table collation (MySQL only)
     *
     * @var string
     */
    protected $tableCollation = 'utf8_general_ci';

    /**
     * Whether to use IF NOT EXISTS
     *
     * @var bool
     */
    protected $ifNotExists = true;



    /**
     * Constructor
     *
     * @param Driver $driver Database driver instance
     * @param string $table  Table name (can include prefix placeholder #__)
     */
    public function __construct(Driver $driver, string $table)
    {
        $this->driver = $driver;
        $this->table = $table;
    }

    /**
     * Get the schema grammar instance for DDL generation
     *
     * @return \Hubzero\Database\Drivers\Base\BaseSchemaGrammar
     */
    protected function getGrammar()
    {
        return $this->driver->getSchemaGrammar();
    }

    /**
     * Add a column to the table
     *
     * @param  string      $name       Column name
     * @param  string      $type       Column type (e.g., 'INT(11)', 'VARCHAR(255)')
     * @param  array       $modifiers  Column modifiers (nullable, default, unsigned, autoIncrement, after, comment)
     * @return $this
     */
    public function column(string $name, string $type, array $modifiers = []): self
    {
        $this->columns[$name] = [
            'type' => $type,
            'modifiers' => $modifiers,
        ];
        return $this;
    }

    /**
     * Add an auto-incrementing primary key column
     *
     * @param  string $name Column name (default: 'id')
     * @return $this
     */
    public function id(string $name = 'id'): self
    {
        return $this->column($name, 'INT(11) UNSIGNED', [
            'autoIncrement' => true,
            'nullable' => false,
        ])->primaryKey($name);
    }

    /**
     * Add an auto-incrementing integer primary key (Laravel compatibility alias)
     *
     * @param  string $name Column name (default: 'id')
     * @return $this
     */
    public function increments(string $name = 'id'): self
    {
        return $this->id($name);
    }

    /**
     * Add an auto-incrementing big integer primary key
     *
     * @param  string $name Column name (default: 'id')
     * @return $this
     */
    public function bigIncrements(string $name = 'id'): self
    {
        return $this->column($name, 'BIGINT(20) UNSIGNED', [
            'autoIncrement' => true,
            'nullable' => false,
        ])->primaryKey($name);
    }

    /**
     * Add an auto-incrementing medium integer primary key
     *
     * @param  string $name Column name (default: 'id')
     * @return $this
     */
    public function mediumIncrements(string $name = 'id'): self
    {
        return $this->column($name, 'MEDIUMINT(8) UNSIGNED', [
            'autoIncrement' => true,
            'nullable' => false,
        ])->primaryKey($name);
    }

    /**
     * Add an auto-incrementing small integer primary key
     *
     * @param  string $name Column name (default: 'id')
     * @return $this
     */
    public function smallIncrements(string $name = 'id'): self
    {
        return $this->column($name, 'SMALLINT(5) UNSIGNED', [
            'autoIncrement' => true,
            'nullable' => false,
        ])->primaryKey($name);
    }

    /**
     * Add an auto-incrementing tiny integer primary key
     *
     * @param  string $name Column name (default: 'id')
     * @return $this
     */
    public function tinyIncrements(string $name = 'id'): self
    {
        return $this->column($name, 'TINYINT(3) UNSIGNED', [
            'autoIncrement' => true,
            'nullable' => false,
        ])->primaryKey($name);
    }

    /**
     * Add an integer column
     *
     * @param  string $name     Column name
     * @param  bool   $unsigned Whether the column is unsigned
     * @return $this
     */
    public function integer(string $name, bool $unsigned = false): self
    {
        return $this->column($name, 'integer')->unsigned($unsigned);
    }

    /**
     * Add a tiny integer column
     *
     * @param  string $name     Column name
     * @param  bool   $unsigned Whether the column is unsigned
     * @return $this
     */
    public function tinyInteger(string $name, bool $unsigned = false): self
    {
        return $this->column($name, 'tinyInteger')->unsigned($unsigned);
    }

    /**
     * Add a small integer column
     *
     * @param  string $name     Column name
     * @param  bool   $unsigned Whether the column is unsigned
     * @return $this
     */
    public function smallInteger(string $name, bool $unsigned = false): self
    {
        return $this->column($name, 'smallInteger')->unsigned($unsigned);
    }

    /**
     * Add a medium integer column
     *
     * @param  string $name     Column name
     * @param  bool   $unsigned Whether the column is unsigned
     * @return $this
     */
    public function mediumInteger(string $name, bool $unsigned = false): self
    {
        return $this->column($name, 'mediumInteger')->unsigned($unsigned);
    }

    /**
     * Add a big integer column
     *
     * @param  string $name     Column name
     * @param  bool   $unsigned Whether the column is unsigned
     * @return $this
     */
    public function bigInteger(string $name, bool $unsigned = false): self
    {
        return $this->column($name, 'bigInteger')->unsigned($unsigned);
    }

    /**
     * Add a float column
     *
     * @param  string $name      Column name
     * @param  int    $precision Total digits
     * @param  int    $scale     Decimal digits
     * @return $this
     */
    public function float(string $name, int $precision = 8, int $scale = 2): self
    {
        return $this->column($name, 'float')->precision($precision)->scale($scale);
    }

    /**
     * Add a double column
     *
     * @param  string $name      Column name
     * @param  int    $precision Total digits
     * @param  int    $scale     Decimal digits
     * @return $this
     */
    public function double(string $name, int $precision = 16, int $scale = 4): self
    {
        return $this->column($name, 'double')->precision($precision)->scale($scale);
    }

    /**
     * Add a decimal column
     *
     * @param  string $name      Column name
     * @param  int    $precision Total digits
     * @param  int    $scale     Decimal digits
     * @return $this
     */
    public function decimal(string $name, int $precision = 10, int $scale = 2): self
    {
        return $this->column($name, 'decimal')->precision($precision)->scale($scale);
    }

    /**
     * Add a string/varchar column
     *
     * @param  string $name   Column name
     * @param  int    $length Maximum length
     * @return $this
     */
    public function string(string $name, int $length = 255): self
    {
        return $this->column($name, 'string')->length($length);
    }

    /**
     * Add a char column
     *
     * @param  string $name   Column name
     * @param  int    $length Fixed length
     * @return $this
     */
    public function char(string $name, int $length = 1): self
    {
        return $this->column($name, 'char')->length($length);
    }

    /**
     * Add a text column
     *
     * @param  string $name Column name
     * @return $this
     */
    public function text(string $name): self
    {
        return $this->column($name, 'text');
    }

    /**
     * Add a tiny text column
     *
     * @param  string $name Column name
     * @return $this
     */
    public function tinyText(string $name): self
    {
        return $this->column($name, 'tinyText');
    }

    /**
     * Add a medium text column
     *
     * @param  string $name Column name
     * @return $this
     */
    public function mediumText(string $name): self
    {
        return $this->column($name, 'mediumText');
    }

    /**
     * Add a long text column
     *
     * @param  string $name Column name
     * @return $this
     */
    public function longText(string $name): self
    {
        return $this->column($name, 'longText');
    }

    /**
     * Add a blob column
     *
     * @param  string $name Column name
     * @return $this
     */
    public function blob(string $name): self
    {
        return $this->column($name, 'BLOB');
    }

    /**
     * Add a medium blob column
     *
     * @param  string $name Column name
     * @return $this
     */
    public function mediumBlob(string $name): self
    {
        return $this->column($name, 'MEDIUMBLOB');
    }

    /**
     * Add a long blob column
     *
     * @param  string $name Column name
     * @return $this
     */
    public function longBlob(string $name): self
    {
        return $this->column($name, 'LONGBLOB');
    }

    /**
     * Add a binary column
     *
     * @param  string $name   Column name
     * @param  int    $length Length in bytes
     * @return $this
     */
    public function binary(string $name, int $length = 255): self
    {
        return $this->column($name, "VARBINARY($length)");
    }

    /**
     * Add a fixed-length binary column
     *
     * @param  string $name   Column name
     * @param  int    $length Length in bytes
     * @return $this
     */
    public function fixedBinary(string $name, int $length): self
    {
        return $this->column($name, "BINARY($length)");
    }

    /**
     * Add an unsigned integer column
     *
     * @param  string $name Column name
     * @return $this
     */
    public function unsignedInteger(string $name): self
    {
        return $this->column($name, 'INT(10) UNSIGNED');
    }

    /**
     * Add an unsigned big integer column
     *
     * @param  string $name Column name
     * @return $this
     */
    public function unsignedBigInteger(string $name): self
    {
        return $this->column($name, 'BIGINT(20) UNSIGNED');
    }

    /**
     * Add an unsigned tiny integer column
     *
     * @param  string $name Column name
     * @return $this
     */
    public function unsignedTinyInteger(string $name): self
    {
        return $this->tinyInteger($name, true);
    }

    /**
     * Add an unsigned small integer column
     *
     * @param  string $name Column name
     * @return $this
     */
    public function unsignedSmallInteger(string $name): self
    {
        return $this->smallInteger($name, true);
    }

    /**
     * Add an unsigned medium integer column
     *
     * @param  string $name Column name
     * @return $this
     */
    public function unsignedMediumInteger(string $name): self
    {
        return $this->mediumInteger($name, true);
    }

    /**
     * Add an unsigned decimal column
     *
     * @param  string $name      Column name
     * @param  int    $precision Total digits
     * @param  int    $scale     Decimal digits
     * @return $this
     */
    public function unsignedDecimal(string $name, int $precision = 10, int $scale = 2): self
    {
        return $this->column($name, "DECIMAL($precision, $scale) UNSIGNED");
    }

    /**
     * Add a boolean column (TINYINT(1))
     *
     * @param  string $name Column name
     * @return $this
     */
    public function boolean(string $name): self
    {
        return $this->column($name, 'boolean', ['default' => false]);
    }

    /**
     * Add a date column
     *
     * @param  string $name Column name
     * @return $this
     */
    public function date(string $name): self
    {
        return $this->column($name, 'date');
    }

    /**
     * Add a datetime column
     *
     * @param  string $name Column name
     * @return $this
     */
    public function datetime(string $name): self
    {
        return $this->column($name, 'datetime');
    }

    /**
     * Add a timestamp column
     *
     * @param  string $name Column name
     * @return $this
     */
    public function timestamp(string $name): self
    {
        return $this->column($name, 'timestamp');
    }

    /**
     * Add a time column
     *
     * @param  string $name Column name
     * @return $this
     */
    public function time(string $name): self
    {
        return $this->column($name, 'time');
    }

    /**
     * Add created_at and updated_at timestamp columns
     *
     * @return $this
     */
    public function timestamps(): self
    {
        $this->datetime('created')->nullable();
        $this->datetime('modified')->nullable();
        return $this;
    }

    /**
     * Add nullable created_at and updated_at timestamp columns
     *
     * Alias for timestamps() with explicit "nullable" naming.
     *
     * @return $this
     */
    public function nullableTimestamps(): self
    {
        return $this->timestamps();
    }

    /**
     * Add a soft deletes column (deleted_at)
     *
     * @param  string $column Column name (default: 'deleted_at')
     * @return $this
     */
    public function softDeletes(string $column = 'deleted_at'): self
    {
        return $this->datetime($column)->nullable();
    }

    /**
     * Add a remember token column for authentication
     *
     * Creates a VARCHAR(100) column typically used for "remember me" functionality.
     *
     * @return $this
     */
    public function rememberToken(): self
    {
        return $this->string('remember_token', 100)->nullable();
    }

    /**
     * Add polymorphic relationship columns
     *
     * Creates {name}_type (VARCHAR) and {name}_id (BIGINT UNSIGNED) columns
     * for polymorphic relationships.
     *
     * @param  string $name      The morph name (e.g., 'taggable' creates taggable_type and taggable_id)
     * @param  string $indexName Optional index name (auto-generated if null)
     * @return $this
     */
    public function morphs(string $name, ?string $indexName = null): self
    {
        $this->string($name . '_type');
        $this->unsignedBigInteger($name . '_id');
        $this->index($indexName ?? "idx_{$name}_type_{$name}_id", [$name . '_type', $name . '_id']);
        return $this;
    }

    /**
     * Add nullable polymorphic relationship columns
     *
     * Same as morphs() but with nullable columns.
     *
     * @param  string $name      The morph name
     * @param  string $indexName Optional index name
     * @return $this
     */
    public function nullableMorphs(string $name, ?string $indexName = null): self
    {
        $this->string($name . '_type')->nullable();
        $this->unsignedBigInteger($name . '_id')->nullable();
        $this->index($indexName ?? "idx_{$name}_type_{$name}_id", [$name . '_type', $name . '_id']);
        return $this;
    }

    /**
     * Add polymorphic relationship columns with UUID
     *
     * Creates {name}_type (VARCHAR) and {name}_id (CHAR(36)) columns
     * for polymorphic relationships using UUIDs.
     *
     * @param  string $name      The morph name
     * @param  string $indexName Optional index name
     * @return $this
     */
    public function uuidMorphs(string $name, ?string $indexName = null): self
    {
        $this->string($name . '_type');
        $this->uuid($name . '_id');
        $this->index($indexName ?? "idx_{$name}_type_{$name}_id", [$name . '_type', $name . '_id']);
        return $this;
    }

    /**
     * Add nullable polymorphic relationship columns with UUID
     *
     * @param  string $name      The morph name
     * @param  string $indexName Optional index name
     * @return $this
     */
    public function nullableUuidMorphs(string $name, ?string $indexName = null): self
    {
        $this->string($name . '_type')->nullable();
        $this->uuid($name . '_id')->nullable();
        $this->index($indexName ?? "idx_{$name}_type_{$name}_id", [$name . '_type', $name . '_id']);
        return $this;
    }

    /**
     * Add a UUID column
     *
     * Creates a CHAR(36) column for storing UUIDs.
     *
     * @param  string $name Column name
     * @return $this
     */
    public function uuid(string $name): self
    {
        return $this->column($name, 'uuid');
    }

    /**
     * Add a ULID column
     *
     * Creates a CHAR(26) column for storing ULIDs (Universally Unique
     * Lexicographically Sortable Identifiers).
     *
     * @param  string $name Column name
     * @return $this
     */
    public function ulid(string $name): self
    {
        return $this->column($name, 'ulid');
    }

    /**
     * Add an IP address column
     *
     * Creates a VARCHAR(45) column to accommodate IPv6 addresses.
     *
     * @param  string $name Column name
     * @return $this
     */
    public function ipAddress(string $name): self
    {
        return $this->column($name, 'ipAddress');
    }

    /**
     * Add a MAC address column
     *
     * Creates a VARCHAR(17) column for MAC addresses (format: XX:XX:XX:XX:XX:XX).
     *
     * @param  string $name Column name
     * @return $this
     */
    public function macAddress(string $name): self
    {
        return $this->column($name, 'macAddress');
    }

    /**
     * Add a year column
     *
     * @param  string $name Column name
     * @return $this
     */
    public function year(string $name): self
    {
        return $this->column($name, 'year');
    }

    /**
     * Add a SET column (MySQL only)
     *
     * @param  string $name   Column name
     * @param  array  $values Allowed values
     * @return $this
     */
    public function set(string $name, array $values): self
    {
        $quotedValues = array_map(function ($v) {
            return "'" . addslashes($v) . "'";
        }, $values);
        return $this->column($name, 'SET(' . implode(', ', $quotedValues) . ')');
    }

    /**
     * Add an enum column
     *
     * @param  string $name   Column name
     * @param  array  $values Allowed values
     * @return $this
     */
    public function enum(string $name, array $values): self
    {
        $quotedValues = array_map(function ($v) {
            return "'" . addslashes($v) . "'";
        }, $values);
        return $this->column($name, 'ENUM(' . implode(', ', $quotedValues) . ')');
    }

    /**
     * Add a JSON column (TEXT in MySQL < 5.7, JSON in MySQL 5.7+)
     *
     * @param  string $name Column name
     * @return $this
     */
    public function json(string $name): self
    {
        return $this->column($name, 'json');
    }

    /**
     * Add a JSONB column (PostgreSQL binary JSON)
     *
     * JSONB stores JSON in a decomposed binary format, which is faster to
     * process and supports indexing. Falls back to TEXT on non-PostgreSQL.
     *
     * @param  string $name Column name
     * @return $this
     */
    public function jsonb(string $name): self
    {
        return $this->column($name, $this->driver->getJsonBinaryColumnType());
    }

    /**
     * Set the current column to be nullable
     *
     * @param  bool $nullable
     * @return $this
     */
    public function nullable(bool $nullable = true): self
    {
        return $this->setModifier('nullable', $nullable);
    }

    /**
     * Set the current column to be NOT NULL
     *
     * @return $this
     */
    public function notNull(): self
    {
        return $this->setModifier('nullable', false);
    }

    /**
     * Set the current column to be unsigned
     *
     * @param  bool $unsigned
     * @return $this
     */
    public function unsigned(bool $unsigned = true): self
    {
        return $this->setModifier('unsigned', $unsigned);
    }

    /**
     * Set the current column's default value
     *
     * @param  mixed $value
     * @return $this
     */
    public function default($value): self
    {
        return $this->setModifier('default', $value);
    }

    /**
     * Set the current column to be auto-incrementing
     *
     * @param  bool $autoIncrement
     * @return $this
     */
    public function autoIncrement(bool $autoIncrement = true): self
    {
        return $this->setModifier('autoIncrement', $autoIncrement);
    }

    /**
     * Set the current column's length
     *
     * @param  int $length
     * @return $this
     */
    public function length(int $length): self
    {
        return $this->setModifier('length', $length);
    }

    /**
     * Set the current column's precision
     *
     * @param  int $precision
     * @return $this
     */
    public function precision(int $precision): self
    {
        return $this->setModifier('precision', $precision);
    }

    /**
     * Set the current column's scale
     *
     * @param  int $scale
     * @return $this
     */
    public function scale(int $scale): self
    {
        return $this->setModifier('scale', $scale);
    }

    /**
     * Set the current column comment
     *
     * @param  string $comment
     * @return $this
     */
    public function comment(string $comment): self
    {
        return $this->setModifier('comment', $comment);
    }

    /**
     * Set a modifier for the last added/modified column
     *
     * @param  string $name
     * @param  mixed  $value
     * @return $this
     */
    protected function setModifier(string $name, $value): self
    {
        $lastColumn = array_key_last($this->columns);
        if ($lastColumn !== null) {
            $this->columns[$lastColumn]['modifiers'][$name] = $value;
        }
        return $this;
    }

    /**
     * Add ON UPDATE clause to the last added column
     *
     * @param  string $expression Expression (e.g. CURRENT_TIMESTAMP)
     * @return $this
     */
    public function onUpdate(string $expression): self
    {
        return $this->setModifier('onUpdate', $expression);
    }

    /**
     * Position the last added column after another column
     *
     * @param  string $column Column name to position after
     * @return $this
     */
    public function after(string $column): self
    {
        $lastColumn = array_key_last($this->columns);
        if ($lastColumn !== null) {
            $this->columns[$lastColumn]['modifiers']['after'] = $column;
        }
        return $this;
    }

    /**
     * Set the primary key
     *
     * @param  string|array $columns Column name(s)
     * @return $this
     */
    public function primaryKey($columns): self
    {
        $this->primaryKey = is_array($columns) ? $columns : [$columns];
        return $this;
    }

    /**
     * Add a regular index
     *
     * Supports prefix/partial indexes (MySQL only, prefix ignored on SQLite):
     * - Simple: ->index('idx_name', 'column')
     * - Multi-column: ->index('idx_name', ['col1', 'col2'])
     * - With prefix: ->index('idx_name', [['url', 75]])  // url(75)
     * - Mixed: ->index('idx_name', ['col1', ['url', 75], 'col2'])
     *
     * @param  string       $name    Index name
     * @param  string|array $columns Column name(s), optionally with prefix lengths
     * @return $this
     */
    public function index(string $name, $columns): self
    {
        $this->indexes[$name] = is_array($columns) ? $columns : [$columns];
        return $this;
    }

    /**
     * Add a unique index
     *
     * @param  string       $name    Index name
     * @param  string|array $columns Column name(s)
     * @return $this
     */
    public function uniqueIndex(string $name, $columns): self
    {
        $this->uniqueIndexes[$name] = is_array($columns) ? $columns : [$columns];
        return $this;
    }

    /**
     * Add a fulltext index (MySQL only, ignored on SQLite)
     *
     * @param  string       $name    Index name
     * @param  string|array $columns Column name(s)
     * @return $this
     */
    public function fulltextIndex(string $name, $columns): self
    {
        $this->fulltextIndexes[$name] = is_array($columns) ? $columns : [$columns];
        return $this;
    }

    /**
     * Add a foreign key constraint with fluent builder
     *
     * Usage:
     * ```php
     * // Fluent syntax
     * $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
     *
     * // Shorthand with constrained()
     * $table->foreign('user_id')->constrained();  // References users.id
     *
     * // Legacy non-fluent syntax (still supported)
     * $table->foreign('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
     * ```
     *
     * @param  string      $column           Local column name
     * @param  string|null $referencedTable  Referenced table name (optional, for non-fluent usage)
     * @param  string      $referencedColumn Referenced column name (default: 'id')
     * @param  string      $onDelete         ON DELETE action (default: 'CASCADE')
     * @param  string      $onUpdate         ON UPDATE action (default: 'CASCADE')
     * @return ForeignKeyBuilder|$this       ForeignKeyBuilder for fluent usage, or $this for legacy
     */
    public function foreign(
        string $column,
        ?string $referencedTable = null,
        string $referencedColumn = 'id',
        string $onDelete = 'CASCADE',
        string $onUpdate = 'CASCADE'
    ) {
        // If referencedTable is provided, use legacy non-fluent syntax
        if ($referencedTable !== null) {
            $this->foreignKeys[$column] = [
                'column' => $column,
                'referencedTable' => $referencedTable,
                'referencedColumn' => $referencedColumn,
                'onDelete' => $onDelete,
                'onUpdate' => $onUpdate,
            ];
            return $this;
        }

        // Return fluent builder
        return new ForeignKeyBuilder($this, $column);
    }

    /**
     * Add an unsigned big integer column and return a foreign key builder
     *
     * Convenience method that creates the column and returns a ForeignKeyBuilder
     * for defining the foreign key constraint.
     *
     * Usage:
     * ```php
     * $table->foreignId('user_id')->constrained();
     * // Equivalent to:
     * // $table->unsignedBigInteger('user_id');
     * // $table->foreign('user_id')->constrained();
     * ```
     *
     * @param  string $column Column name
     * @return ForeignKeyBuilder
     */
    public function foreignId(string $column): ForeignKeyBuilder
    {
        $this->unsignedBigInteger($column);
        return new ForeignKeyBuilder($this, $column);
    }

    /**
     * Register a foreign key from a ForeignKeyBuilder
     *
     * Called internally by ForeignKeyBuilder when the constraint is finalized.
     *
     * @param  ForeignKeyBuilder $fkBuilder The foreign key builder
     * @return void
     */
    public function registerForeignKey(ForeignKeyBuilder $fkBuilder): void
    {
        $column = $fkBuilder->getColumn();
        $this->foreignKeys[$column] = [
            'column' => $column,
            'referencedTable' => $fkBuilder->getReferencedTable(),
            'referencedColumn' => $fkBuilder->getReferencedColumn(),
            'onDelete' => $fkBuilder->getOnDelete(),
            'onUpdate' => $fkBuilder->getOnUpdate(),
            'name' => $fkBuilder->getName(),
        ];
    }

    /**
     * Update an existing foreign key from a ForeignKeyBuilder
     *
     * Called when onDelete/onUpdate/name are modified after initial registration.
     *
     * @param  ForeignKeyBuilder $fkBuilder The foreign key builder
     * @return void
     */
    public function updateForeignKey(ForeignKeyBuilder $fkBuilder): void
    {
        $this->registerForeignKey($fkBuilder);
    }

    /**
     * Set the table engine (MySQL only, ignored on SQLite)
     *
     * @param  string $engine Engine name (InnoDB, MyISAM, etc.)
     * @return $this
     */
    public function engine(string $engine): self
    {
        $this->tableEngine = $engine;
        return $this;
    }

    /**
     * Set the table charset (MySQL only, ignored on SQLite)
     *
     * @param  string $charset Character set
     * @return $this
     */
    public function charset(string $charset): self
    {
        $this->tableCharset = $charset;
        return $this;
    }

    /**
     * Set the table collation (MySQL only, ignored on SQLite)
     *
     * @param  string $collation Collation name
     * @return $this
     */
    public function collation(string $collation): self
    {
        $this->tableCollation = $collation;
        return $this;
    }

    /**
     * Set whether to use IF NOT EXISTS clause
     *
     * @param  bool $ifNotExists
     * @return $this
     */
    public function ifNotExists(bool $ifNotExists = true): self
    {
        $this->ifNotExists = $ifNotExists;
        return $this;
    }

    /**
     * Build and execute the CREATE TABLE statement(s)
     *
     * @return bool
     */
    public function execute(): bool
    {
        $statements = $this->toSqlStatements();
        foreach ($statements as $sql) {
            $this->driver->setQuery($sql);
            if (!$this->driver->execute()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Generate the SQL for this table creation (main CREATE TABLE only)
     *
     * @return string
     */
    public function toSql(): string
    {
        $statements = $this->toSqlStatements();
        return $statements[0] ?? '';
    }

    /**
     * Return a normalized data definition for CREATE TABLE operations.
     *
     * This is a non-executing adapter method intended for gradual migration
     * toward grammar-driven compilation. It reflects the current builder state
     * without changing SQL generation behavior.
     *
     * @return array
     */
    public function toCreateDefinition(): array
    {
        return [
            'table' => $this->driver->replacePrefix($this->table),
            'ifNotExists' => (bool) $this->ifNotExists,
            'options' => [
                'engine' => $this->tableEngine,
                'charset' => $this->tableCharset,
                'collation' => $this->tableCollation,
            ],
            'columns' => $this->columns,
            'primaryKey' => $this->primaryKey,
            'indexes' => $this->indexes,
            'uniqueIndexes' => $this->uniqueIndexes,
            'fulltextIndexes' => $this->fulltextIndexes,
            'foreignKeys' => array_values($this->foreignKeys),
        ];
    }

    /**
     * Generate all SQL statements for this table creation
     *
     * Some databases require indexes to be created separately after the table,
     * so this may return multiple statements.
     *
     * @return array Array of SQL statements
     */
    public function toSqlStatements(): array
    {
        $definition = $this->toCreateDefinition();
        $compiled = $this->getGrammar()->compileCreateTableFromDefinition($definition);

        if (!$this->isCompiledCreateStatementList($compiled)) {
            throw new \UnexpectedValueException(
                'Schema grammar must return a list of SQL statements from compileCreateTableFromDefinition().'
            );
        }

        return $compiled;
    }

    /**
     * Determine whether grammar output is a concrete CREATE statement list.
     *
     * @param  array $compiled
     * @return bool
     */
    protected function isCompiledCreateStatementList(array $compiled): bool
    {
        if (empty($compiled) || !array_is_list($compiled)) {
            return false;
        }

        foreach ($compiled as $statement) {
            if (!is_string($statement)) {
                return false;
            }
        }

        return true;
    }

}
