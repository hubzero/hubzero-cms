<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

use Hubzero\Database\Driver;

/**
 * Trait providing fluent column definition methods with abstract types
 *
 * This trait provides database-agnostic column type methods that map to
 * appropriate SQL types for each database backend (MySQL, PostgreSQL, SQLite, SQL Server).
 *
 * Usage:
 * ```php
 * // In migrations using Schema::addColumn()
 * $schema->addColumn('#__users', 'score')->integer()->nullable()->default(0);
 * $schema->addColumn('#__users', 'email')->string(100)->unique();
 * $schema->addColumn('#__users', 'uuid')->uuid();
 * $schema->modifyColumn('#__users', 'score')->bigInteger()->default(100);
 * ```
 */
trait ColumnDefinitionTrait
{
    /**
     * Abstract column type (e.g., 'integer', 'string', 'boolean')
     *
     * @var string|null
     */
    protected $columnType = null;

    /**
     * Type parameters (e.g., length for string, precision/scale for decimal)
     *
     * @var array
     */
    protected $typeParameters = [];

    /**
     * Column modifiers
     *
     * @var array
     */
    protected $columnModifiers = [];

    // =========================================================================
    // Integer Types
    // =========================================================================

    /**
     * Set column type to tiny integer (1 byte, -128 to 127 or 0 to 255)
     *
     * @return static
     */
    public function tinyInteger(int $length = null): static
    {
        $this->columnType = 'tinyInteger';
        $this->typeParameters['length'] = $length;
        return $this;
    }

    /**
     * Set column type to small integer (2 bytes, -32768 to 32767 or 0 to 65535)
     *
     * @return static
     */
    public function smallInteger(int $length = null): static
    {
        $this->columnType = 'smallInteger';
        $this->typeParameters['length'] = $length;
        return $this;
    }

    /**
     * Set column type to medium integer (3 bytes, MySQL only)
     *
     * @return static
     */
    public function mediumInteger(int $length = null): static
    {
        $this->columnType = 'mediumInteger';
        $this->typeParameters['length'] = $length;
        return $this;
    }

    /**
     * Set column type to integer (4 bytes)
     *
     * @return static
     */
    public function integer(int $length = null): static
    {
        $this->columnType = 'integer';
        $this->typeParameters['length'] = $length;
        return $this;
    }

    /**
     * Set column type to big integer (8 bytes)
     *
     * @return static
     */
    public function bigInteger(int $length = null): static
    {
        $this->columnType = 'bigInteger';
        $this->typeParameters['length'] = $length;
        return $this;
    }

    /**
     * Set column type to boolean (true/false)
     *
     * @return static
     */
    public function boolean(): static
    {
        $this->columnType = 'boolean';
        return $this;
    }

    // =========================================================================
    // String Types
    // =========================================================================

    /**
     * Set column type to variable-length string
     *
     * @param  int  $length  Maximum length (default 255)
     * @return static
     */
    public function string(int $length = 255): static
    {
        $this->columnType = 'string';
        $this->typeParameters['length'] = $length;
        return $this;
    }

    /**
     * Set column type to fixed-length string
     *
     * @param  int  $length  Fixed length (default 1)
     * @return static
     */
    public function char(int $length = 1): static
    {
        $this->columnType = 'char';
        $this->typeParameters['length'] = $length;
        return $this;
    }

    /**
     * Set column type to tiny text (~255 bytes)
     *
     * @return static
     */
    public function tinyText(): static
    {
        $this->columnType = 'tinyText';
        return $this;
    }

    /**
     * Set column type to text (~64KB)
     *
     * @return static
     */
    public function text(): static
    {
        $this->columnType = 'text';
        return $this;
    }

    /**
     * Set column type to medium text (~16MB)
     *
     * @return static
     */
    public function mediumText(): static
    {
        $this->columnType = 'mediumText';
        return $this;
    }

    /**
     * Set column type to long text (~4GB)
     *
     * @return static
     */
    public function longText(): static
    {
        $this->columnType = 'longText';
        return $this;
    }

    // =========================================================================
    // Numeric Types
    // =========================================================================

    /**
     * Set column type to floating point
     *
     * @param  int  $precision  Total digits (default 8)
     * @param  int  $scale      Decimal digits (default 2)
     * @return static
     */
    public function float(int $precision = 8, int $scale = 2): static
    {
        $this->columnType = 'float';
        $this->typeParameters['precision'] = $precision;
        $this->typeParameters['scale'] = $scale;
        return $this;
    }

    /**
     * Set column type to double precision floating point
     *
     * @param  int  $precision  Total digits (default 16)
     * @param  int  $scale      Decimal digits (default 4)
     * @return static
     */
    public function double(int $precision = 16, int $scale = 4): static
    {
        $this->columnType = 'double';
        $this->typeParameters['precision'] = $precision;
        $this->typeParameters['scale'] = $scale;
        return $this;
    }

    /**
     * Set column type to exact decimal
     *
     * @param  int  $precision  Total digits (default 10)
     * @param  int  $scale      Decimal digits (default 2)
     * @return static
     */
    public function decimal(int $precision = 10, int $scale = 2): static
    {
        $this->columnType = 'decimal';
        $this->typeParameters['precision'] = $precision;
        $this->typeParameters['scale'] = $scale;
        return $this;
    }

    // =========================================================================
    // Date/Time Types
    // =========================================================================

    /**
     * Set column type to date (no time)
     *
     * @return static
     */
    public function date(): static
    {
        $this->columnType = 'date';
        return $this;
    }

    /**
     * Set column type to time (no date)
     *
     * @return static
     */
    public function time(): static
    {
        $this->columnType = 'time';
        return $this;
    }

    /**
     * Set column type to datetime
     *
     * @return static
     */
    public function datetime(): static
    {
        $this->columnType = 'datetime';
        return $this;
    }

    /**
     * Set column type to timestamp
     *
     * @return static
     */
    public function timestamp(): static
    {
        $this->columnType = 'timestamp';
        return $this;
    }

    /**
     * Set column type to timestamp with timezone
     *
     * @return static
     */
    public function timestampTz(): static
    {
        $this->columnType = 'timestampTz';
        return $this;
    }

    /**
     * Set column type to year
     *
     * @return static
     */
    public function year(): static
    {
        $this->columnType = 'year';
        return $this;
    }

    // =========================================================================
    // Binary Types
    // =========================================================================

    /**
     * Set column type to binary/blob
     *
     * @return static
     */
    public function binary(): static
    {
        $this->columnType = 'binary';
        return $this;
    }

    // =========================================================================
    // Special Types
    // =========================================================================

    /**
     * Set column type to JSON
     *
     * @return static
     */
    public function json(): static
    {
        $this->columnType = 'json';
        return $this;
    }

    /**
     * Set column type to UUID (36-character string or native UUID)
     *
     * @return static
     */
    public function uuid(): static
    {
        $this->columnType = 'uuid';
        return $this;
    }

    /**
     * Set column type to ULID (26-character string)
     *
     * @return static
     */
    public function ulid(): static
    {
        $this->columnType = 'ulid';
        return $this;
    }

    /**
     * Set column type to IP address (IPv4 or IPv6)
     *
     * @return static
     */
    public function ipAddress(): static
    {
        $this->columnType = 'ipAddress';
        return $this;
    }

    /**
     * Set column type to MAC address
     *
     * @return static
     */
    public function macAddress(): static
    {
        $this->columnType = 'macAddress';
        return $this;
    }

    // =========================================================================
    // Modifiers
    // =========================================================================

    /**
     * Allow NULL values
     *
     * @param  bool  $nullable
     * @return static
     */
    public function nullable(bool $nullable = true): static
    {
        $this->columnModifiers['nullable'] = $nullable;
        return $this;
    }

    /**
     * Disallow NULL values (NOT NULL)
     *
     * @return static
     */
    public function notNull(): static
    {
        $this->columnModifiers['nullable'] = false;
        return $this;
    }

    /**
     * Set the default value
     *
     * @param  mixed  $value
     * @return static
     */
    public function default($value): static
    {
        $this->columnModifiers['default'] = $value;
        return $this;
    }

    /**
     * Make the column unsigned (integer types only)
     *
     * Note: Only affects MySQL; PostgreSQL and SQLite ignore this.
     *
     * @return static
     */
    public function unsigned(): static
    {
        $this->columnModifiers['unsigned'] = true;
        return $this;
    }

    /**
     * Make the column auto-incrementing
     *
     * @return static
     */
    public function autoIncrement(): static
    {
        $this->columnModifiers['autoIncrement'] = true;
        return $this;
    }

    /**
     * Make the column a primary key
     *
     * @return static
     */
    public function primary(): static
    {
        $this->columnModifiers['primary'] = true;
        return $this;
    }

    /**
     * Add a unique constraint
     *
     * @return static
     */
    public function unique(): static
    {
        $this->columnModifiers['unique'] = true;
        return $this;
    }

    /**
     * Add a column comment
     *
     * Note: Only affects MySQL; PostgreSQL and SQLite ignore this in column definition.
     *
     * @param  string  $comment
     * @return static
     */
    public function comment(string $comment): static
    {
        $this->columnModifiers['comment'] = $comment;
        return $this;
    }

    /**
     * Add ON UPDATE clause (e.g., ON UPDATE CURRENT_TIMESTAMP)
     *
     * @param  string  $expression
     * @return static
     */
    public function onUpdate(string $expression): static
    {
        $this->columnModifiers['onUpdate'] = $expression;
        return $this;
    }

    /**
     * Set the ON UPDATE clause using an expression.
     *
     * @param  \Hubzero\Database\Expression  $expression
     * @return static
     */
    public function onUpdateExpression(\Hubzero\Database\Expression $expression): static
    {
        $this->columnModifiers['onUpdate'] = $expression;
        return $this;
    }

    /**
     * Set the default value using an expression.
     *
     * @param  \Hubzero\Database\Expression  $expression
     * @return static
     */
    public function defaultExpression(\Hubzero\Database\Expression $expression): static
    {
        $this->columnModifiers['default'] = $expression;
        return $this;
    }

    // =========================================================================
    // Compilation
    // =========================================================================

    /**
     * Check if an abstract type has been set
     *
     * @return bool
     */
    protected function hasAbstractType(): bool
    {
        return $this->columnType !== null;
    }

    /**
     * Compile the abstract type and modifiers to SQL for a specific driver
     *
     * @param  Driver  $driver
     * @return string
     */
    protected function compileAbstractTypeToSql(Driver $driver): string
    {
        if (!$this->hasAbstractType()) {
            return '';
        }

        // Get the appropriate type map for this driver
        $typeMap = $this->getTypeMapForDriver($driver);
        $baseType = $typeMap[$this->columnType] ?? 'VARCHAR(255)';

        // Build the complete type string with parameters
        $typeSql = $this->buildTypeSql($baseType, $driver);

        // Build modifiers
        $modifiersSql = $this->buildModifiersSql($driver);

        return $typeSql . $modifiersSql;
    }

    /**
     * Get the type map for a specific driver
     *
     * Delegates to the driver's getAbstractTypeMap() method.
     *
     * @param  Driver  $driver
     * @return array
     */
    protected function getTypeMapForDriver(Driver $driver): array
    {
        return $driver->getAbstractTypeMap();
    }

    /**
     * Build the complete type SQL with parameters
     *
     * Uses driver feature detection methods instead of instanceof checks.
     *
     * @param  string  $baseType
     * @param  Driver  $driver
     * @return string
     */
    protected function buildTypeSql(string $baseType, Driver $driver): string
    {
        // Handle types that need length/precision
        switch ($this->columnType) {
            case 'string':
            case 'char':
                // Only add length if driver requires it (SQLite doesn't)
                if ($driver->requiresStringLength()) {
                    $length = $this->typeParameters['length'] ?? 255;
                    return "{$baseType}({$length})";
                }
                return $baseType;

            case 'float':
            case 'double':
            case 'decimal':
                // Only apply precision for drivers that support it
                // PostgreSQL's REAL and DOUBLE PRECISION don't take parameters
                if ($driver->supportsFloatPrecision()) {
                    if ($this->columnType === 'decimal') {
                        $precision = $this->typeParameters['precision'] ?? 10;
                        $scale = $this->typeParameters['scale'] ?? 2;
                        return "{$baseType}({$precision},{$scale})";
                    }
                }
                return $baseType;

            case 'tinyInteger':
            case 'smallInteger':
            case 'mediumInteger':
            case 'integer':
            case 'bigInteger':
                if ($driver->supportsIntegerLength() && isset($this->typeParameters['length'])) {
                    return "{$baseType}({$this->typeParameters['length']})";
                }
                return $baseType;

            default:
                return $baseType;
        }
    }

    /**
     * Build the modifiers SQL (UNSIGNED, NOT NULL, DEFAULT, etc.)
     *
     * Uses driver feature detection methods instead of instanceof checks.
     *
     * @param  Driver  $driver
     * @return string
     */
    protected function buildModifiersSql(Driver $driver): string
    {
        $parts = [];

        // UNSIGNED (MySQL only, and only for integer types)
        if ($driver->supportsUnsigned() && !empty($this->columnModifiers['unsigned'])) {
            if (
                in_array($this->columnType, [
                'tinyInteger', 'smallInteger', 'mediumInteger',
                'integer', 'bigInteger',
                ])
            ) {
                $parts[] = 'UNSIGNED';
            }
        }

        // AUTO_INCREMENT / SERIAL handling
        // Note: Auto-increment syntax varies by database:
        // - MySQL: AUTO_INCREMENT keyword
        // - SQLite: INTEGER PRIMARY KEY implies AUTOINCREMENT
        // - PostgreSQL: type changes to SERIAL (handled elsewhere)
        // - SQL Server: IDENTITY(1,1) (handled elsewhere)
        // Only add AUTO_INCREMENT keyword if driver supports it
        if (!empty($this->columnModifiers['autoIncrement'])) {
            // AUTO_INCREMENT is MySQL-specific; other DBs handle it differently
            if ($driver->supportsUnsigned()) {  // MySQL family
                $parts[] = 'AUTO_INCREMENT';
            }
            // SQLite, PostgreSQL, SQL Server handle auto-increment in other ways
        }

        // Detect zero-date default — translate to DEFAULT NULL
        $hasZeroDateDefault = array_key_exists('default', $this->columnModifiers)
            && \Hubzero\Database\Driver\Sql::isZeroDate($this->columnModifiers['default']);

        // NULL / NOT NULL — force nullable when translating zero-date
        if ($hasZeroDateDefault) {
            $parts[] = 'NULL';
        } elseif (isset($this->columnModifiers['nullable'])) {
            if ($this->columnModifiers['nullable']) {
                $parts[] = 'NULL';
            } else {
                $parts[] = 'NOT NULL';
            }
        }

        // DEFAULT value — zero-date becomes DEFAULT NULL
        if (array_key_exists('default', $this->columnModifiers)) {
            if ($hasZeroDateDefault) {
                $parts[] = 'DEFAULT NULL';
            } else {
                $parts[] = 'DEFAULT ' . $this->compileDefaultValue(
                    $this->columnModifiers['default'],
                    $driver
                );
            }
        }

        // PRIMARY KEY
        if (!empty($this->columnModifiers['primary'])) {
            $parts[] = 'PRIMARY KEY';
        }

        // UNIQUE
        if (!empty($this->columnModifiers['unique'])) {
            $parts[] = 'UNIQUE';
        }

        // ON UPDATE
        if (isset($this->columnModifiers['onUpdate'])) {
            $onUpdate = $this->columnModifiers['onUpdate'];
            if ($onUpdate instanceof \Hubzero\Database\Expression) {
                $syntaxClass = '\\Hubzero\\Database\\Syntax\\' . ucfirst($driver->getSyntax());
                $syntax = new $syntaxClass($driver);
                $onUpdate = $syntax->buildExpression($onUpdate);
            }
            $parts[] = 'ON UPDATE ' . $onUpdate;
        }

        // COMMENT (MySQL-family only)
        if ($driver->supportsColumnComments() && isset($this->columnModifiers['comment'])) {
            $parts[] = "COMMENT '" . addslashes($this->columnModifiers['comment']) . "'";
        }

        return empty($parts) ? '' : ' ' . implode(' ', $parts);
    }

    /**
     * Compile a default value to SQL
     *
     * Uses driver's formatBooleanLiteral() for proper boolean formatting.
     *
     * @param  mixed   $value
     * @param  Driver  $driver
     * @return string
     */
    protected function compileDefaultValue($value, Driver $driver): string
    {
        if ($value instanceof \Hubzero\Database\Expression) {
            $syntaxClass = '\\Hubzero\\Database\\Syntax\\' . ucfirst($driver->getSyntax());
            $syntax = new $syntaxClass($driver);
            return $syntax->buildExpression($value);
        }

        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $driver->formatBooleanLiteral($value);
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        // Handle SQL expressions like CURRENT_TIMESTAMP
        $upperValue = strtoupper($value);
        if (in_array($upperValue, ['CURRENT_TIMESTAMP', 'NOW()', 'CURRENT_DATE', 'CURRENT_TIME'])) {
            return $upperValue;
        }

        return "'" . addslashes($value) . "'";
    }

    /**
     * Reset the type state for reuse
     *
     * @return void
     */
    protected function resetTypeState(): void
    {
        $this->columnType = null;
        $this->typeParameters = [];
        $this->columnModifiers = [];
    }

    /**
     * Get the raw column type name
     *
     * @return string|null
     */
    public function getColumnType(): ?string
    {
        return $this->columnType;
    }

    /**
     * Get the type parameters
     *
     * @return array
     */
    public function getTypeParameters(): array
    {
        return $this->typeParameters;
    }

    /**
     * Get the column modifiers
     *
     * @return array
     */
    public function getColumnModifiers(): array
    {
        return $this->columnModifiers;
    }
}
