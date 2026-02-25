<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

use Hubzero\Facades\User;

/**
 * Table definition for defining table structure
 *
 * Provides a fluent interface for defining columns, indexes, and constraints.
 * Used by the schema builder to generate database-specific DDL.
 */
class TableDefinition
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table;

    /**
     * Column definitions
     *
     * @var Column[]
     */
    protected $columns = [];

    /**
     * Index definitions
     *
     * @var array
     */
    protected $indexes = [];

    /**
     * Foreign key definitions
     *
     * @var array
     */
    protected $foreignKeys = [];

    /**
     * Primary key columns (for composite keys)
     *
     * @var array
     */
    protected $primaryKeys = [];

    /**
     * Table engine (MySQL)
     *
     * @var string
     */
    protected $engine = 'InnoDB';

    /**
     * Table charset
     *
     * @var string
     */
    protected $charset = 'utf8mb4';

    /**
     * Table collation
     *
     * @var string
     */
    protected $collation = 'utf8mb4_unicode_ci';

    /**
     * Whether this is a table modification (vs creation)
     *
     * @var bool
     */
    protected $modifying = false;

    /**
     * Columns to drop
     *
     * @var array
     */
    protected $dropColumns = [];

    /**
     * Columns to modify (for ALTER TABLE MODIFY)
     *
     * @var Column[]
     */
    protected $modifyColumns = [];

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
     * Whether to use IF NOT EXISTS in CREATE TABLE
     *
     * @var bool
     */
    protected $ifNotExists = true;

    /**
     * Create a new table definition
     *
     * @param  string  $table  Table name
     * @param  bool    $modifying  Whether modifying existing table
     */
    public function __construct(string $table, bool $modifying = false)
    {
        $this->table = $table;
        $this->modifying = $modifying;
    }

    /**
     * Get table name
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Get all column definitions
     *
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Get index definitions
     *
     * @return array
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    /**
     * Get foreign key definitions
     *
     * @return array
     */
    public function getForeignKeys(): array
    {
        return $this->foreignKeys;
    }

    /**
     * Get primary key columns
     *
     * @return array
     */
    public function getPrimaryKeys(): array
    {
        return $this->primaryKeys;
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
     * @return Column[]
     */
    public function getModifyColumns(): array
    {
        return $this->modifyColumns;
    }

    /**
     * Check if this is a modification definition
     *
     * @return bool
     */
    public function isModifying(): bool
    {
        return $this->modifying;
    }

    /**
     * Get table engine
     *
     * @return string
     */
    public function getEngine(): string
    {
        return $this->engine;
    }

    /**
     * Set table engine
     *
     * @param  string  $engine
     * @return $this
     */
    public function engine(string $engine): self
    {
        $this->engine = $engine;
        return $this;
    }

    /**
     * Get table charset
     *
     * @return string
     */
    public function getCharset(): string
    {
        return $this->charset;
    }

    /**
     * Set table charset
     *
     * @param  string  $charset
     * @return $this
     */
    public function charset(string $charset): self
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * Get table collation
     *
     * @return string
     */
    public function getCollation(): string
    {
        return $this->collation;
    }

    /**
     * Get unique index definitions
     *
     * @return array
     */
    public function getUniqueIndexes(): array
    {
        return $this->uniqueIndexes;
    }

    /**
     * Get fulltext index definitions
     *
     * @return array
     */
    public function getFulltextIndexes(): array
    {
        return $this->fulltextIndexes;
    }

    /**
     * Get IF NOT EXISTS flag
     *
     * @return bool
     */
    public function getIfNotExists(): bool
    {
        return $this->ifNotExists;
    }

    /**
     * Set table collation
     *
     * @param  string  $collation
     * @return $this
     */
    public function collation(string $collation): self
    {
        $this->collation = $collation;
        return $this;
    }

    // =========================================================================
    // Column Type Methods
    // =========================================================================

    /**
     * Add a column definition
     *
     * @param  Column  $column
     * @return Column
     */
    protected function addColumn(Column $column): Column
    {
        $this->columns[] = $column;
        return $column;
    }

    /**
     * Create auto-incrementing primary key
     *
     * @param  string  $name  Column name (default: 'id')
     * @return Column
     */
    public function id(string $name = 'id'): Column
    {
        return $this->addColumn(new Column($name, 'bigInteger'))
            ->unsigned()
            ->autoIncrement()
            ->primary();
    }

    /**
     * Create auto-incrementing integer primary key
     *
     * @param  string  $name
     * @return Column
     */
    public function increments(string $name): Column
    {
        return $this->addColumn(new Column($name, 'integer'))
            ->unsigned()
            ->autoIncrement()
            ->primary();
    }

    /**
     * Create auto-incrementing big integer primary key
     *
     * @param  string  $name
     * @return Column
     */
    public function bigIncrements(string $name): Column
    {
        return $this->addColumn(new Column($name, 'bigInteger'))
            ->unsigned()
            ->autoIncrement()
            ->primary();
    }

    /**
     * Create a tiny integer column
     *
     * @param  string  $name
     * @return Column
     */
    public function tinyInteger(string $name): Column
    {
        return $this->addColumn(new Column($name, 'tinyInteger'));
    }

    /**
     * Create a small integer column
     *
     * @param  string  $name
     * @return Column
     */
    public function smallInteger(string $name): Column
    {
        return $this->addColumn(new Column($name, 'smallInteger'));
    }

    /**
     * Create an integer column
     *
     * @param  string  $name
     * @return Column
     */
    public function integer(string $name): Column
    {
        return $this->addColumn(new Column($name, 'integer'));
    }

    /**
     * Create a big integer column
     *
     * @param  string  $name
     * @return Column
     */
    public function bigInteger(string $name): Column
    {
        return $this->addColumn(new Column($name, 'bigInteger'));
    }

    /**
     * Create an unsigned tiny integer column
     *
     * @param  string  $name
     * @return Column
     */
    public function unsignedTinyInteger(string $name): Column
    {
        return $this->tinyInteger($name)->unsigned();
    }

    /**
     * Create an unsigned integer column
     *
     * @param  string  $name
     * @return Column
     */
    public function unsignedInteger(string $name): Column
    {
        return $this->integer($name)->unsigned();
    }

    /**
     * Create an unsigned big integer column
     *
     * @param  string  $name
     * @return Column
     */
    public function unsignedBigInteger(string $name): Column
    {
        return $this->bigInteger($name)->unsigned();
    }

    /**
     * Create a boolean column
     *
     * @param  string  $name
     * @return Column
     */
    public function boolean(string $name): Column
    {
        return $this->addColumn(new Column($name, 'boolean'));
    }

    /**
     * Create a string (varchar) column
     *
     * @param  string  $name
     * @param  int     $length
     * @return Column
     */
    public function string(string $name, int $length = 255): Column
    {
        return $this->addColumn(new Column($name, 'string', ['length' => $length]));
    }

    /**
     * Create a char column
     *
     * @param  string  $name
     * @param  int     $length
     * @return Column
     */
    public function char(string $name, int $length = 255): Column
    {
        return $this->addColumn(new Column($name, 'char', ['length' => $length]));
    }

    /**
     * Create a text column
     *
     * @param  string  $name
     * @return Column
     */
    public function text(string $name): Column
    {
        return $this->addColumn(new Column($name, 'text'));
    }

    /**
     * Create a medium text column
     *
     * @param  string  $name
     * @return Column
     */
    public function mediumText(string $name): Column
    {
        return $this->addColumn(new Column($name, 'mediumText'));
    }

    /**
     * Create a long text column
     *
     * @param  string  $name
     * @return Column
     */
    public function longText(string $name): Column
    {
        return $this->addColumn(new Column($name, 'longText'));
    }

    /**
     * Create a float column
     *
     * @param  string  $name
     * @param  int     $precision
     * @param  int     $scale
     * @return Column
     */
    public function float(string $name, int $precision = 8, int $scale = 2): Column
    {
        return $this->addColumn(new Column($name, 'float', [
            'precision' => $precision,
            'scale' => $scale
        ]));
    }

    /**
     * Create a double column
     *
     * @param  string  $name
     * @param  int     $precision
     * @param  int     $scale
     * @return Column
     */
    public function double(string $name, int $precision = 16, int $scale = 4): Column
    {
        return $this->addColumn(new Column($name, 'double', [
            'precision' => $precision,
            'scale' => $scale
        ]));
    }

    /**
     * Create a decimal column
     *
     * @param  string  $name
     * @param  int     $precision
     * @param  int     $scale
     * @return Column
     */
    public function decimal(string $name, int $precision = 10, int $scale = 2): Column
    {
        return $this->addColumn(new Column($name, 'decimal', [
            'precision' => $precision,
            'scale' => $scale
        ]));
    }

    /**
     * Create a date column
     *
     * @param  string  $name
     * @return Column
     */
    public function date(string $name): Column
    {
        return $this->addColumn(new Column($name, 'date'));
    }

    /**
     * Create a time column
     *
     * @param  string  $name
     * @return Column
     */
    public function time(string $name): Column
    {
        return $this->addColumn(new Column($name, 'time'));
    }

    /**
     * Create a datetime column
     *
     * @param  string  $name
     * @return Column
     */
    public function datetime(string $name): Column
    {
        return $this->addColumn(new Column($name, 'datetime'));
    }

    /**
     * Create a timestamp column
     *
     * @param  string  $name
     * @return Column
     */
    public function timestamp(string $name): Column
    {
        return $this->addColumn(new Column($name, 'timestamp'));
    }

    /**
     * Create created_at and updated_at timestamp columns
     *
     * @return void
     */
    public function timestamps(): void
    {
        $this->timestamp('created_at')->nullable();
        $this->timestamp('updated_at')->nullable();
    }

    /**
     * Create a binary/blob column
     *
     * @param  string  $name
     * @return Column
     */
    public function binary(string $name): Column
    {
        return $this->addColumn(new Column($name, 'binary'));
    }

    /**
     * Create a JSON column
     *
     * @param  string  $name
     * @return Column
     */
    public function json(string $name): Column
    {
        return $this->addColumn(new Column($name, 'json'));
    }

    // =========================================================================
    // Additional Column Types
    // =========================================================================

    /**
     * Create a medium integer column
     *
     * @param  string  $name
     * @return Column
     */
    public function mediumInteger(string $name): Column
    {
        return $this->addColumn(new Column($name, 'mediumInteger'));
    }

    /**
     * Create an unsigned medium integer column
     *
     * @param  string  $name
     * @return Column
     */
    public function unsignedMediumInteger(string $name): Column
    {
        return $this->mediumInteger($name)->unsigned();
    }

    /**
     * Create a tiny text column
     *
     * @param  string  $name
     * @return Column
     */
    public function tinyText(string $name): Column
    {
        return $this->addColumn(new Column($name, 'tinyText'));
    }

    /**
     * Create a UUID column
     *
     * @param  string  $name
     * @return Column
     */
    public function uuid(string $name = 'uuid'): Column
    {
        return $this->addColumn(new Column($name, 'uuid'));
    }

    /**
     * Create a ULID column
     *
     * @param  string  $name
     * @return Column
     */
    public function ulid(string $name = 'ulid'): Column
    {
        return $this->addColumn(new Column($name, 'ulid'));
    }

    /**
     * Create a year column
     *
     * @param  string  $name
     * @return Column
     */
    public function year(string $name): Column
    {
        return $this->addColumn(new Column($name, 'year'));
    }

    /**
     * Create an IP address column
     *
     * @param  string  $name
     * @return Column
     */
    public function ipAddress(string $name = 'ip_address'): Column
    {
        return $this->addColumn(new Column($name, 'ipAddress'));
    }

    /**
     * Create a MAC address column
     *
     * @param  string  $name
     * @return Column
     */
    public function macAddress(string $name = 'mac_address'): Column
    {
        return $this->addColumn(new Column($name, 'macAddress'));
    }

    // =========================================================================
    // Relationship Helper Methods
    // =========================================================================

    /**
     * Create an unsigned big integer column for foreign keys
     *
     * @param  string  $name
     * @return Column
     */
    public function foreignId(string $name): Column
    {
        return $this->unsignedBigInteger($name);
    }

    /**
     * Create a foreign ID column for a model
     *
     * Automatically generates the column name from the model class name.
     * For example, User::class becomes 'user_id'.
     *
     * @param  string  $model  Fully qualified model class name
     * @param  string|null  $column  Custom column name (optional)
     * @return Column
     */
    public function foreignIdFor(string $model, ?string $column = null): Column
    {
        if ($column === null) {
            // Extract class name and convert to snake_case_id
            $parts = explode('\\', $model);
            $className = end($parts);
            $column = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className)) . '_id';
        }
        return $this->foreignId($column);
    }

    /**
     * Create polymorphic relationship columns (type and id)
     *
     * Creates two columns: {name}_type (string) and {name}_id (unsigned big integer)
     *
     * @param  string  $name  The morphable name
     * @param  string|null  $indexName  Optional index name
     * @return void
     */
    public function morphs(string $name, ?string $indexName = null): void
    {
        $this->string("{$name}_type");
        $this->unsignedBigInteger("{$name}_id");
        $this->index(["{$name}_type", "{$name}_id"], $indexName);
    }

    /**
     * Create nullable polymorphic relationship columns
     *
     * @param  string  $name  The morphable name
     * @param  string|null  $indexName  Optional index name
     * @return void
     */
    public function nullableMorphs(string $name, ?string $indexName = null): void
    {
        $this->string("{$name}_type")->nullable();
        $this->unsignedBigInteger("{$name}_id")->nullable();
        $this->index(["{$name}_type", "{$name}_id"], $indexName);
    }

    /**
     * Create a UUID morphable column set
     *
     * @param  string  $name  The morphable name
     * @param  string|null  $indexName  Optional index name
     * @return void
     */
    public function uuidMorphs(string $name, ?string $indexName = null): void
    {
        $this->string("{$name}_type");
        $this->uuid("{$name}_id");
        $this->index(["{$name}_type", "{$name}_id"], $indexName);
    }

    /**
     * Create a nullable UUID morphable column set
     *
     * @param  string  $name  The morphable name
     * @param  string|null  $indexName  Optional index name
     * @return void
     */
    public function nullableUuidMorphs(string $name, ?string $indexName = null): void
    {
        $this->string("{$name}_type")->nullable();
        $this->uuid("{$name}_id")->nullable();
        $this->index(["{$name}_type", "{$name}_id"], $indexName);
    }

    // =========================================================================
    // Common Column Helpers
    // =========================================================================

    /**
     * Create a soft delete column (deleted_at)
     *
     * @param  string  $name  Column name (default: 'deleted_at')
     * @return Column
     */
    public function softDeletes(string $name = 'deleted_at'): Column
    {
        return $this->timestamp($name)->nullable();
    }

    /**
     * Create a soft delete column with timezone
     *
     * @param  string  $name  Column name (default: 'deleted_at')
     * @return Column
     */
    public function softDeletesTz(string $name = 'deleted_at'): Column
    {
        return $this->addColumn(new Column($name, 'timestampTz'))->nullable();
    }

    /**
     * Create nullable created_at and updated_at timestamp columns
     *
     * @return void
     */
    public function nullableTimestamps(): void
    {
        $this->timestamp('created_at')->nullable();
        $this->timestamp('updated_at')->nullable();
    }

    /**
     * Create timezone-aware created_at and updated_at timestamp columns
     *
     * @return void
     */
    public function timestampsTz(): void
    {
        $this->addColumn(new Column('created_at', 'timestampTz'))->nullable();
        $this->addColumn(new Column('updated_at', 'timestampTz'))->nullable();
    }

    /**
     * Create a remember token column for authentication
     *
     * @return Column
     */
    public function rememberToken(): Column
    {
        return $this->string('remember_token', 100)->nullable();
    }

    // =========================================================================
    // Index Methods
    // =========================================================================

    /**
     * Add a primary key constraint
     *
     * @param  string|array  $columns
     * @param  string|null   $name
     * @return $this
     */
    public function primary($columns, ?string $name = null): self
    {
        $columns = (array) $columns;
        $this->primaryKeys = array_merge($this->primaryKeys, $columns);
        return $this;
    }

    /**
     * Add a unique index
     *
     * @param  string|array  $columns
     * @param  string|null   $name
     * @return $this
     */
    public function uniqueIndex($columns, ?string $name = null): self
    {
        $columns = (array) $columns;
        $name = $name ?? $this->table . '_' . implode('_', $columns) . '_unique';
        $this->indexes[] = [
            'type' => 'unique',
            'columns' => $columns,
            'name' => $name
        ];
        return $this;
    }

    /**
     * Add a regular index
     *
     * @param  string|array  $columns
     * @param  string|null   $name
     * @return $this
     */
    public function index($columns, ?string $name = null): self
    {
        $columns = (array) $columns;
        $name = $name ?? $this->table . '_' . implode('_', $columns) . '_index';
        $this->indexes[] = [
            'type' => 'index',
            'columns' => $columns,
            'name' => $name
        ];
        return $this;
    }

    /**
     * Add a fulltext index
     *
     * @param  string|array  $columns   Columns to index
     * @param  string|null   $name      Index name
     * @param  string        $language  Text search language (for PostgreSQL: 'english', 'simple', etc.)
     * @return $this
     */
    public function fulltextIndex($columns, ?string $name = null, string $language = 'english'): self
    {
        $columns = (array) $columns;
        $name = $name ?? $this->table . '_' . implode('_', $columns) . '_fulltext';
        $this->indexes[] = [
            'type' => 'fulltext',
            'columns' => $columns,
            'name' => $name,
            'language' => $language
        ];
        return $this;
    }

    /**
     * Add a spatial index (for geometry/geography columns)
     *
     * @param  string|array  $columns
     * @param  string|null   $name
     * @return $this
     */
    public function spatialIndex($columns, ?string $name = null): self
    {
        $columns = (array) $columns;
        $name = $name ?? $this->table . '_' . implode('_', $columns) . '_spatial';
        $this->indexes[] = [
            'type' => 'spatial',
            'columns' => $columns,
            'name' => $name
        ];
        return $this;
    }

    // =========================================================================
    // Foreign Key Methods
    // =========================================================================

    /**
     * Add a foreign key constraint
     *
     * @param  string  $column
     * @return ForeignKeyDefinition
     */
    public function foreign(string $column): ForeignKeyDefinition
    {
        $definition = new ForeignKeyDefinition($column);
        $this->foreignKeys[] = $definition;
        return $definition;
    }

    // =========================================================================
    // Drop Methods (for ALTER TABLE)
    // =========================================================================

    /**
     * Mark a column for removal
     *
     * @param  string  $column
     * @return $this
     */
    public function dropColumn(string $column): self
    {
        $this->dropColumns[] = $column;
        return $this;
    }

    // =========================================================================
    // Modify Methods (for ALTER TABLE)
    // =========================================================================

    /**
     * Define a column modification
     *
     * Used to change the definition of an existing column.
     * Returns a Column object that can be configured with the new definition.
     *
     * @param  string  $name  Column name
     * @param  string  $type  New column type
     * @param  array   $parameters  Type parameters (length, precision, etc.)
     * @return Column
     */
    public function modifyColumn(string $name, string $type, array $parameters = []): Column
    {
        $column = new Column($name, $type, $parameters);
        $this->modifyColumns[] = $column;
        return $column;
    }
}
