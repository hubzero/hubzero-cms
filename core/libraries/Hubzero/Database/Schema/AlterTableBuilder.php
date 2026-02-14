<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

use Hubzero\Database\Driver;

/**
 * Fluent table builder for ALTER TABLE statements
 *
 * Provides a database-agnostic way to modify tables that works with both
 * MySQL and SQLite backends. Note: SQLite has limited ALTER TABLE support,
 * so some operations may require table rebuilding.
 *
 */
class AlterTableBuilder
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
     * Columns to add
     *
     * @var array
     */
    protected $addColumns = [];

    /**
     * Columns to modify
     *
     * @var array
     */
    protected $modifyColumns = [];

    /**
     * Columns to drop
     *
     * @var array
     */
    protected $dropColumns = [];

    /**
     * Columns to rename
     *
     * @var array
     */
    protected $renameColumns = [];

    /**
     * Indexes to add
     *
     * @var array
     */
    protected $addIndexes = [];

    /**
     * Indexes to drop
     *
     * @var array
     */
    protected $dropIndexes = [];

    /**
     * Fulltext indexes to add (MySQL only)
     *
     * @var array
     */
    protected $addFulltextIndexes = [];

    /**
     * Foreign keys to add
     *
     * @var array
     */
    protected $addForeignKeys = [];

    /**
     * Foreign keys to drop
     *
     * @var array
     */
    protected $dropForeignKeys = [];

    /**
     * Primary key to add
     *
     * @var array|null
     */
    protected $addPrimaryKey = null;

    /**
     * Whether to drop the primary key
     *
     * @var bool
     */
    protected $dropPrimaryKeyFlag = false;

    /**
     * Table engine to change to (MySQL only)
     *
     * @var string|null
     */
    protected $newEngine = null;

    /**
     * Table character set to change to (MySQL only)
     *
     * @var string|null
     */
    protected $newCharset = null;

    /**
     * Table collation to change to (MySQL only)
     *
     * @var string|null
     */
    protected $newCollation = null;

    /**
     * Source table info for schema comparisons
     *
     * When generating ALTER statements from schema comparisons (TableDiff),
     * this contains the original table structure. SQLite uses this to perform
     * table rebuilds without querying the actual database.
     *
     * @var TableInfo|null
     */
    protected $sourceTableInfo = null;

    /**
     * The last added column name (for chaining modifiers)
     *
     * @var string|null
     */
    protected $lastColumn = null;

    /**
     * The last modified column name (for chaining modifiers)
     *
     * @var string|null
     */
    protected $lastModifyColumn = null;

    /**
     * The last renamed column name (for chaining modifiers)
     *
     * @var string|null
     */
    protected $lastRenameColumn = null;

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
     * Add a new column to the table
     *
     * @param  string $name      Column name
     * @param  string $type      Column type
     * @param  array  $modifiers Column modifiers
     * @return $this
     */
    public function addColumn(string $name, ?string $type = null, array $modifiers = []): self
    {
        $this->addColumns[$name] = [
            'type' => $type,
            'modifiers' => $modifiers,
        ];
        $this->lastColumn = $name;
        $this->lastModifyColumn = null;
        $this->lastRenameColumn = null;
        return $this;
    }

    /**
     * Add a string column
     *
     * @param  string $name
     * @param  int    $length
     * @return $this
     */
    public function addString(string $name, int $length = 255): self
    {
        return $this->addColumn($name, 'string', ['length' => $length]);
    }

    /**
     * Add an integer column
     *
     * @param  string $name
     * @param  int    $length
     * @param  bool   $unsigned
     * @return $this
     */
    public function addInteger(string $name, int $length = null, bool $unsigned = false): self
    {
        return $this->addColumn($name, 'integer', ['length' => $length, 'unsigned' => $unsigned]);
    }

    /**
     * Add a tiny integer column
     *
     * @param  string $name
     * @param  int    $length
     * @param  bool   $unsigned
     * @return $this
     */
    public function addTinyInteger(string $name, int $length = null, bool $unsigned = false): self
    {
        return $this->addColumn($name, 'tinyInteger', ['length' => $length, 'unsigned' => $unsigned]);
    }

    /**
     * Add a small integer column
     *
     * @param  string $name
     * @param  int    $length
     * @param  bool   $unsigned
     * @return $this
     */
    public function addSmallInteger(string $name, int $length = null, bool $unsigned = false): self
    {
        return $this->addColumn($name, 'smallInteger', ['length' => $length, 'unsigned' => $unsigned]);
    }

    /**
     * Add a medium integer column
     *
     * @param  string $name
     * @param  int    $length
     * @param  bool   $unsigned
     * @return $this
     */
    public function addMediumInteger(string $name, int $length = null, bool $unsigned = false): self
    {
        return $this->addColumn($name, 'mediumInteger', ['length' => $length, 'unsigned' => $unsigned]);
    }

    /**
     * Add a big integer column
     *
     * @param  string $name
     * @param  int    $length
     * @param  bool   $unsigned
     * @return $this
     */
    public function addBigInteger(string $name, int $length = null, bool $unsigned = false): self
    {
        return $this->addColumn($name, 'bigInteger', ['length' => $length, 'unsigned' => $unsigned]);
    }

    /**
     * Add a float column
     *
     * @param  string $name
     * @param  int    $precision
     * @param  int    $scale
     * @return $this
     */
    public function addFloat(string $name, int $precision = 8, int $scale = 2): self
    {
        return $this->addColumn($name, 'float', ['precision' => $precision, 'scale' => $scale]);
    }

    /**
     * Add a double column
     *
     * @param  string $name
     * @param  int    $precision
     * @param  int    $scale
     * @return $this
     */
    public function addDouble(string $name, int $precision = 16, int $scale = 4): self
    {
        return $this->addColumn($name, 'double', ['precision' => $precision, 'scale' => $scale]);
    }

    /**
     * Add a decimal column
     *
     * @param  string $name
     * @param  int    $precision
     * @param  int    $scale
     * @return $this
     */
    public function addDecimal(string $name, int $precision = 10, int $scale = 2): self
    {
        return $this->addColumn($name, 'decimal', ['precision' => $precision, 'scale' => $scale]);
    }

    /**
     * Add a boolean column
     *
     * @param  string $name
     * @return $this
     */
    public function addBoolean(string $name): self
    {
        return $this->addColumn($name, 'boolean');
    }

    /**
     * Add a text column
     *
     * @param  string $name
     * @return $this
     */
    public function addText(string $name): self
    {
        return $this->addColumn($name, 'text');
    }

    /**
     * Add a tiny text column
     *
     * @param  string $name
     * @return $this
     */
    public function addTinyText(string $name): self
    {
        return $this->addColumn($name, 'tinyText');
    }

    /**
     * Add a medium text column
     *
     * @param  string $name
     * @return $this
     */
    public function addMediumText(string $name): self
    {
        return $this->addColumn($name, 'mediumText');
    }

    /**
     * Add a long text column
     *
     * @param  string $name
     * @return $this
     */
    public function addLongText(string $name): self
    {
        return $this->addColumn($name, 'longText');
    }

    /**
     * Add a date column
     *
     * @param  string $name
     * @return $this
     */
    public function addDate(string $name): self
    {
        return $this->addColumn($name, 'date');
    }

    /**
     * Add a datetime column
     *
     * @param  string $name
     * @return $this
     */
    public function addDatetime(string $name): self
    {
        return $this->addColumn($name, 'datetime');
    }

    /**
     * Add a timestamp column
     *
     * @param  string $name
     * @return $this
     */
    public function addTimestamp(string $name): self
    {
        return $this->addColumn($name, 'timestamp');
    }

    /**
     * Add a time column
     *
     * @param  string $name
     * @return $this
     */
    public function addTime(string $name): self
    {
        return $this->addColumn($name, 'time');
    }

    /**
     * Add a UUID column
     *
     * @param  string $name
     * @return $this
     */
    public function addUuid(string $name): self
    {
        return $this->addColumn($name, 'uuid');
    }

    /**
     * Add a JSON column
     *
     * @param  string $name
     * @return $this
     */
    public function addJson(string $name): self
    {
        return $this->addColumn($name, 'json');
    }

    /**
     * Add a binary column
     *
     * @param  string $name
     * @param  int    $length
     * @return $this
     */
    public function addBinary(string $name, int $length = 255): self
    {
        return $this->addColumn($name, 'binary', ['length' => $length]);
    }

    /**
     * Add a new column only if it doesn't already exist (skipped otherwise)
     *
     * Allows fluent chaining without breaking on existing columns.
     *
     * @param  string $name      Column name
     * @param  string $type      Column type
     * @param  array  $modifiers Column modifiers
     * @return $this
     */
    public function addColumnIfNotExists(string $name, string $type, array $modifiers = []): self
    {
        if (!$this->columnExists($name)) {
            $this->addColumns[$name] = [
                'type' => $type,
                'modifiers' => $modifiers,
            ];
            $this->lastColumn = $name;
        }
        return $this;
    }

    /**
     * Add an auto-incrementing integer column
     *
     * @param  string $name Column name
     * @return $this
     */
    public function addId(string $name = 'id'): self
    {
        return $this->addColumn($name, 'INT(11) UNSIGNED', [
            'autoIncrement' => true,
            'nullable' => false,
        ]);
    }





    /**
     * Add created_at and updated_at timestamp columns
     *
     * @return $this
     */
    public function addTimestamps(): self
    {
        $this->addColumn('created', 'DATETIME', ['nullable' => true]);
        $this->addColumn('modified', 'DATETIME', ['nullable' => true]);
        return $this;
    }

    /**
     * Add nullable timestamps (alias for addTimestamps)
     *
     * @return $this
     */
    public function addNullableTimestamps(): self
    {
        return $this->addTimestamps();
    }

    /**
     * Add a soft deletes column (deleted_at)
     *
     * @param  string $column Column name (default: 'deleted_at')
     * @return $this
     */
    public function addSoftDeletes(string $column = 'deleted_at'): self
    {
        return $this->addColumn($column, 'DATETIME', ['nullable' => true]);
    }

    /**
     * Add a remember token column for authentication
     *
     * @return $this
     */
    public function addRememberToken(): self
    {
        return $this->addColumn('remember_token', 'VARCHAR(100)', ['nullable' => true]);
    }

    /**
     * Add polymorphic relationship columns
     *
     * @param  string $name      The morph name
     * @param  string $indexName Optional index name
     * @return $this
     */
    public function addMorphs(string $name, ?string $indexName = null): self
    {
        $this->addString($name . '_type', 255);
        $this->addBigInteger($name . '_id', null, true);  // unsigned bigint
        $this->addIndex($indexName ?? "idx_{$name}_type_{$name}_id", [$name . '_type', $name . '_id']);
        return $this;
    }

    /**
     * Add nullable polymorphic relationship columns
     *
     * @param  string $name      The morph name
     * @param  string $indexName Optional index name
     * @return $this
     */
    public function addNullableMorphs(string $name, ?string $indexName = null): self
    {
        $this->addString($name . '_type', 255)->nullable();
        $this->addBigInteger($name . '_id', null, true)->nullable();  // unsigned bigint
        $this->addIndex($indexName ?? "idx_{$name}_type_{$name}_id", [$name . '_type', $name . '_id']);
        return $this;
    }


    /**
     * Add polymorphic relationship columns with UUID
     *
     * @param  string $name      The morph name
     * @param  string $indexName Optional index name
     * @return $this
     */
    public function addUuidMorphs(string $name, ?string $indexName = null): self
    {
        $this->addColumn($name . '_type', 'VARCHAR(255)');
        $this->addColumn($name . '_id', 'CHAR(36)');
        $this->addIndex($indexName ?? "idx_{$name}_type_{$name}_id", [$name . '_type', $name . '_id']);
        return $this;
    }







    /**
     * Set the current column type to date
     *
     * @return $this
     */
    public function date(): self
    {
        return $this->setType('DATE');
    }

    /**
     * Set the current column type to datetime
     *
     * @return $this
     */
    public function datetime(): self
    {
        return $this->setType('DATETIME');
    }

    /**
     * Set the current column type to timestamp
     *
     * @return $this
     */
    public function timestamp(): self
    {
        return $this->setType('TIMESTAMP');
    }

    /**
     * Set the current column type to boolean
     *
     * @return $this
     */
    public function boolean(): self
    {
        return $this->setType('TINYINT(1)');
    }

    /**
     * Set the current column type to decimal
     *
     * @param  int $precision
     * @param  int $scale
     * @return $this
     */
    public function decimal(int $precision = 8, int $scale = 2): self
    {
        return $this->setType('decimal')->precision($precision)->scale($scale);
    }

    /**
     * Set the current column type to float
     *
     * @return $this
     */
    public function float(): self
    {
        return $this->setType('FLOAT');
    }

    /**
     * Set the current column type to double
     *
     * @return $this
     */
    public function double(): self
    {
        return $this->setType('DOUBLE');
    }

    /**
     * Set the current column type to integer
     *
     * @param  int $length
     * @return $this
     */
    public function integer(int $length = null): self
    {
        $this->setType('integer');

        if ($length !== null) {
            $this->length($length);
        }

        return $this;
    }

    /**
     * Set the current column type to tiny integer
     *
     * @param  int $length
     * @return $this
     */
    public function tinyInteger(int $length = null): self
    {
        $this->setType('tinyInteger');

        if ($length !== null) {
            $this->length($length);
        }

        return $this;
    }

    /**
     * Set the current column type to small integer
     *
     * @param  int $length
     * @return $this
     */
    public function smallInteger(int $length = null): self
    {
        $this->setType('smallInteger');

        if ($length !== null) {
            $this->length($length);
        }

        return $this;
    }

    /**
     * Set the current column type to medium integer
     *
     * @param  int $length
     * @return $this
     */
    public function mediumInteger(int $length = null): self
    {
        $this->setType('mediumInteger');

        if ($length !== null) {
            $this->length($length);
        }

        return $this;
    }

    /**
     * Set the current column type to big integer
     *
     * @param  int $length
     * @return $this
     */
    public function bigInteger(int $length = null): self
    {
        $this->setType('bigInteger');

        if ($length !== null) {
            $this->length($length);
        }

        return $this;
    }

    /**
     * Set the current column type to string
     *
     * @param  int $length
     * @return $this
     */
    public function string(int $length = null): self
    {
        $this->setType('string');

        if ($length !== null) {
            $this->length($length);
        }

        return $this;
    }

    /**
     * Set the current column type to text
     *
     * @return $this
     */
    public function text(): self
    {
        return $this->setType('TEXT');
    }

    /**
     * Set the current column type to tiny text
     *
     * @return $this
     */
    public function tinyText(): self
    {
        return $this->setType('TINYTEXT');
    }

    /**
     * Set the current column type to medium text
     *
     * @return $this
     */
    public function mediumText(): self
    {
        return $this->setType('MEDIUMTEXT');
    }

    /**
     * Set the current column type to long text
     *
     * @return $this
     */
    public function longText(): self
    {
        return $this->setType('LONGTEXT');
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
     * Set the current column position
     *
     * @param  string $column
     * @return $this
     */
    public function after(string $column): self
    {
        return $this->setModifier('after', $column);
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
        if ($this->lastColumn && isset($this->addColumns[$this->lastColumn])) {
            $this->addColumns[$this->lastColumn]['modifiers'][$name] = $value;
        } elseif ($this->lastModifyColumn && isset($this->modifyColumns[$this->lastModifyColumn])) {
            $this->modifyColumns[$this->lastModifyColumn]['modifiers'][$name] = $value;
        } elseif ($this->lastRenameColumn && isset($this->renameColumns[$this->lastRenameColumn])) {
            $this->renameColumns[$this->lastRenameColumn]['modifiers'][$name] = $value;
        }
        return $this;
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
     * Set the type of the last added/modified column
     *
     * @param  string $type
     * @return $this
     */
    protected function setType(string $type): self
    {
        if ($this->lastColumn && isset($this->addColumns[$this->lastColumn])) {
            $this->addColumns[$this->lastColumn]['type'] = $type;
        } elseif ($this->lastModifyColumn && isset($this->modifyColumns[$this->lastModifyColumn])) {
            $this->modifyColumns[$this->lastModifyColumn]['type'] = $type;
        } elseif ($this->lastRenameColumn && isset($this->renameColumns[$this->lastRenameColumn])) {
            $this->renameColumns[$this->lastRenameColumn]['type'] = $type;
        }
        return $this;
    }



    /**
     * Modify an existing column in the table
     *
     * @param  string $name      Column name
     * @param  string $type      Column type
     * @param  array  $modifiers Column modifiers
     * @return $this
     */
    public function modifyColumn(string $name, ?string $type = null, array $modifiers = []): self
    {
        $this->modifyColumns[$name] = [
            'type' => $type,
            'modifiers' => $modifiers,
        ];
        $this->lastModifyColumn = $name;
        $this->lastColumn = null;
        $this->lastRenameColumn = null;
        return $this;
    }

    /**
     * Drop a column
     *
     * @param  string $name Column name
     * @return $this
     */
    public function dropColumn(string $name): self
    {
        $this->dropColumns[] = $name;
        return $this;
    }

    /**
     * Rename a column
     *
     * @param  string $oldName Current column name
     * @param  string $newName New column name
     * @param  string $type    Column type (required for MySQL CHANGE)
     * @return $this
     */
    public function renameColumn(string $oldName, string $newName, ?string $type = null): self
    {
        $this->renameColumns[$oldName] = [
            'newName' => $newName,
            'type' => $type,
            'modifiers' => [],
        ];
        $this->lastRenameColumn = $oldName;
        $this->lastColumn = null;
        $this->lastModifyColumn = null;
        return $this;
    }

    /**
     * Add an index
     *
     * @param  string       $name    Index name
     * @param  string|array $columns Column name(s)
     * @return $this
     */
    public function addIndex(string $name, $columns): self
    {
        $this->addIndexes[$name] = [
            'columns' => is_array($columns) ? $columns : [$columns],
            'unique' => false,
        ];
        return $this;
    }

    /**
     * Add a unique index
     *
     * @param  string       $name    Index name
     * @param  string|array $columns Column name(s)
     * @return $this
     */
    public function addUniqueIndex(string $name, $columns): self
    {
        $this->addIndexes[$name] = [
            'columns' => is_array($columns) ? $columns : [$columns],
            'unique' => true,
        ];
        return $this;
    }

    /**
     * Add a fulltext index (MySQL only, no-op on SQLite)
     *
     * @param  string       $name    Index name
     * @param  string|array $columns Column name(s)
     * @return $this
     */
    public function addFulltextIndex(string $name, $columns): self
    {
        $this->addFulltextIndexes[$name] = is_array($columns) ? $columns : [$columns];
        return $this;
    }

    /**
     * Add a primary key
     *
     * @param  string|array $columns Column name(s)
     * @return $this
     */
    public function addPrimaryKey($columns): self
    {
        $this->addPrimaryKey = is_array($columns) ? $columns : [$columns];
        return $this;
    }

    /**
     * Drop the primary key
     *
     * On MySQL, this generates ALTER TABLE ... DROP PRIMARY KEY.
     * On SQLite, this is handled during table rebuild.
     *
     * @return $this
     */
    public function dropPrimaryKey(): self
    {
        $this->dropPrimaryKeyFlag = true;
        return $this;
    }

    /**
     * Drop an index
     *
     * @param  string $name Index name
     * @return $this
     */
    public function dropIndex(string $name): self
    {
        $this->dropIndexes[] = $name;
        return $this;
    }

    /**
     * Add a foreign key constraint (legacy non-fluent syntax)
     *
     * @param  string $column          Local column name
     * @param  string $referencedTable Referenced table name
     * @param  string $referencedColumn Referenced column name
     * @param  string $onDelete        ON DELETE action
     * @param  string $onUpdate        ON UPDATE action
     * @return $this
     */
    public function addForeign(
        string $column,
        string $referencedTable,
        string $referencedColumn = 'id',
        string $onDelete = 'CASCADE',
        string $onUpdate = 'CASCADE'
    ): self {
        $this->addForeignKeys[$column] = [
            'column' => $column,
            'referencedTable' => $referencedTable,
            'referencedColumn' => $referencedColumn,
            'onDelete' => $onDelete,
            'onUpdate' => $onUpdate,
        ];
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
     * // Legacy non-fluent syntax (still supported via addForeign)
     * $table->addForeign('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
     * ```
     *
     * @param  string $column Local column name
     * @return ForeignKeyBuilder
     */
    public function foreign(string $column): ForeignKeyBuilder
    {
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
     * // $table->addColumn('user_id', 'BIGINT(20) UNSIGNED');
     * // $table->foreign('user_id')->constrained();
     * ```
     *
     * @param  string $column Column name
     * @return ForeignKeyBuilder
     */
    public function foreignId(string $column): ForeignKeyBuilder
    {
        $this->addColumn($column, 'BIGINT(20) UNSIGNED', ['nullable' => false]);
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
        $this->addForeignKeys[$column] = [
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
     * Drop a foreign key constraint
     *
     * @param  string $name Constraint name
     * @return $this
     */
    public function dropForeign(string $name): self
    {
        $this->dropForeignKeys[] = $name;
        return $this;
    }

    /**
     * Change the table engine (MySQL only)
     *
     * @param  string $engine Engine name
     * @return $this
     */
    public function engine(string $engine): self
    {
        $this->newEngine = $engine;
        return $this;
    }

    /**
     * Change the table character set (MySQL only)
     *
     * @param  string $charset Character set name (e.g., 'utf8mb4')
     * @return $this
     */
    public function charset(string $charset): self
    {
        $this->newCharset = $charset;
        return $this;
    }

    /**
     * Change the table collation (MySQL only)
     *
     * @param  string $collation Collation name (e.g., 'utf8mb4_unicode_ci')
     * @return $this
     */
    public function collation(string $collation): self
    {
        $this->newCollation = $collation;
        return $this;
    }

    /**
     * Build and execute all ALTER TABLE statements
     *
     * @return bool
     */
    public function execute(): bool
    {
        $statements = $this->toSql();
        foreach ($statements as $sql) {
            $this->driver->setQuery($sql);
            $result = $this->driver->execute();
            // Check for explicit false (error), not falsy (0 affected rows is OK)
            if ($result === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * Generate the SQL statements for this table alteration
     *
     * @return array Array of SQL statements
     */
    public function toSql(): array
    {
        return $this->driver->getSchemaGrammar()->compileAlterTable($this);
    }
    /**
     * Get the table name
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Check if a column exists in the table
     *
     * @param  string $name Column name
     * @return bool
     */
    public function columnExists(string $name): bool
    {
        $columns = $this->driver->getTableColumns($this->table);
        return isset($columns[$name]);
    }

    /**
     * Get columns to drop
     *
     * @return array
     */
    public function getDropColumns(): array
    {
        return $this->dropColumns;
    }

    /**
     * Get columns to modify
     *
     * @return array
     */
    public function getModifyColumns(): array
    {
        return $this->modifyColumns;
    }

    /**
     * Get columns to rename
     *
     * @return array
     */
    public function getRenameColumns(): array
    {
        return $this->renameColumns;
    }

    /**
     * Get columns to add
     *
     * @return array
     */
    public function getAddColumns(): array
    {
        return $this->addColumns;
    }

    /**
     * Get indexes to add
     *
     * @return array
     */
    public function getAddIndexes(): array
    {
        return $this->addIndexes;
    }

    /**
     * Get indexes to drop
     *
     * @return array
     */
    public function getDropIndexes(): array
    {
        return $this->dropIndexes;
    }

    /**
     * Get drop primary key flag
     *
     * @return bool
     */
    public function getDropPrimaryKeyFlag(): bool
    {
        return $this->dropPrimaryKeyFlag;
    }

    /**
     * Get add primary key columns
     *
     * @return array|null
     */
    public function getAddPrimaryKey()
    {
        return $this->addPrimaryKey;
    }

    /**
     * Get new table name
     *
     * @return string|null
     */
    /**
     * Get fulltext indexes to add
     *
     * @return array
     */
    public function getAddFulltextIndexes(): array
    {
        return $this->addFulltextIndexes;
    }

    /**
     * Get foreign keys to add
     *
     * @return array
     */
    public function getAddForeignKeys(): array
    {
        return $this->addForeignKeys;
    }

    /**
     * Get foreign keys to drop
     *
     * @return array
     */
    public function getDropForeignKeys(): array
    {
        return $this->dropForeignKeys;
    }

    /**
     * Consume and clear pending rename-column operations.
     *
     * Intended for grammar-specific preprocessing when a dialect needs to
     * emit rename operations separately from the main ALTER TABLE statement.
     *
     * @return array
     */
    public function consumeRenameColumns(): array
    {
        $operations = $this->renameColumns;
        $this->renameColumns = [];
        return $operations;
    }

    /**
     * Restore rename-column operations after temporary consumption.
     *
     * @param  array  $operations
     * @return void
     */
    public function restoreRenameColumns(array $operations): void
    {
        $this->renameColumns = $operations;
    }

    /**
     * Consume and clear pending fulltext-index additions.
     *
     * @return array
     */
    public function consumeAddFulltextIndexes(): array
    {
        $operations = $this->addFulltextIndexes;
        $this->addFulltextIndexes = [];
        return $operations;
    }

    /**
     * Restore fulltext-index additions after temporary consumption.
     *
     * @param  array  $operations
     * @return void
     */
    public function restoreAddFulltextIndexes(array $operations): void
    {
        $this->addFulltextIndexes = $operations;
    }

    /**
     * Merge index additions into the current add-index operation set.
     *
     * @param  array  $indexes
     * @return void
     */
    public function mergeAddIndexes(array $indexes): void
    {
        $this->addIndexes = array_merge($this->addIndexes, $indexes);
    }
    /**
     * Get new table engine
     *
     * @return string|null
     */
    public function getNewEngine()
    {
        return $this->newEngine;
    }

    /**
     * Get new table charset
     *
     * @return string|null
     */
    public function getNewCharset()
    {
        return $this->newCharset;
    }

    /**
     * Get new table collation
     *
     * @return string|null
     */
    public function getNewCollation()
    {
        return $this->newCollation;
    }

    /**
     * Set the source table info for schema comparisons
     *
     * This is used by SQLite to perform table rebuilds without querying
     * the actual database when generating SQL from schema comparisons.
     *
     * @param  TableInfo $tableInfo
     * @return $this
     */
    public function setSourceTableInfo(TableInfo $tableInfo): self
    {
        $this->sourceTableInfo = $tableInfo;
        return $this;
    }

    /**
     * Get the source table info
     *
     * @return TableInfo|null
     */
    public function getSourceTableInfo(): ?TableInfo
    {
        return $this->sourceTableInfo;
    }
}
