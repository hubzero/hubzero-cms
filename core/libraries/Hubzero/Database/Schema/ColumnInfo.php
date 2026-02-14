<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

/**
 * Represents metadata about a database column
 *
 * This class provides a structured, typed interface for column information
 * instead of raw arrays. It normalizes data across different database engines.
 *
 * Usage:
 * ```php
 * $table = $schema->introspectTable('#__users');
 * foreach ($table->getColumns() as $column) {
 *     echo $column->getName() . ': ' . $column->getType();
 *     if ($column->isNullable()) {
 *         echo ' (nullable)';
 *     }
 * }
 * ```
 */
class ColumnInfo
{
    /**
     * Column name
     *
     * @var string
     */
    protected $name;

    /**
     * Column type (e.g., 'varchar', 'int', 'text')
     *
     * @var string
     */
    protected $type;

    /**
     * Full type definition as returned by database (e.g., 'varchar(255)')
     *
     * @var string
     */
    protected $fullType;

    /**
     * Column length/size (for varchar, char, etc.)
     *
     * @var int|null
     */
    protected $length;

    /**
     * Numeric precision (for decimal, float, etc.)
     *
     * @var int|null
     */
    protected $precision;

    /**
     * Numeric scale (for decimal)
     *
     * @var int|null
     */
    protected $scale;

    /**
     * Whether the column allows NULL values
     *
     * @var bool
     */
    protected $nullable;

    /**
     * Default value
     *
     * @var mixed
     */
    protected $default;

    /**
     * Whether the column is auto-increment
     *
     * @var bool
     */
    protected $autoIncrement;

    /**
     * Whether the column is unsigned (for numeric types)
     *
     * @var bool
     */
    protected $unsigned;

    /**
     * Column comment
     *
     * @var string|null
     */
    protected $comment;

    /**
     * Column collation
     *
     * @var string|null
     */
    protected $collation;

    /**
     * ENUM/SET allowed values
     *
     * @var array|null
     */
    protected $enumValues;

    /**
     * Whether this column is part of the primary key
     *
     * @var bool
     */
    protected $primaryKey;

    /**
     * Create a new ColumnInfo instance
     *
     * @param array $data Column data from database introspection
     */
    public function __construct(array $data = [])
    {
        $this->name = $data['name'] ?? '';
        $this->fullType = $data['full_type'] ?? $data['type'] ?? '';
        $this->type = $this->parseBaseType($this->fullType);
        $this->length = $data['length'] ?? $this->parseLength($this->fullType);
        $this->precision = $data['precision'] ?? null;
        $this->scale = $data['scale'] ?? null;
        $this->nullable = $data['nullable'] ?? true;
        $this->default = $data['default'] ?? null;
        $this->autoIncrement = $data['auto_increment'] ?? false;
        $this->unsigned = $data['unsigned'] ?? $this->parseUnsigned($this->fullType);
        $this->comment = $data['comment'] ?? null;
        $this->collation = $data['collation'] ?? null;
        $this->enumValues = $data['enum_values'] ?? null;
        $this->primaryKey = $data['primary_key'] ?? false;
    }

    /**
     * Get the column name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the base column type (without length/precision)
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the full type definition as returned by database
     *
     * @return string
     */
    public function getFullType(): string
    {
        return $this->fullType;
    }

    /**
     * Get the column length (for string types)
     *
     * @return int|null
     */
    public function getLength(): ?int
    {
        return $this->length;
    }

    /**
     * Get numeric precision
     *
     * @return int|null
     */
    public function getPrecision(): ?int
    {
        return $this->precision;
    }

    /**
     * Get numeric scale
     *
     * @return int|null
     */
    public function getScale(): ?int
    {
        return $this->scale;
    }

    /**
     * Check if the column allows NULL values
     *
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * Get the default value
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Check if the column has a default value
     *
     * @return bool
     */
    public function hasDefault(): bool
    {
        return $this->default !== null;
    }

    /**
     * Check if the column is auto-increment
     *
     * @return bool
     */
    public function isAutoIncrement(): bool
    {
        return $this->autoIncrement;
    }

    /**
     * Check if the column is unsigned
     *
     * @return bool
     */
    public function isUnsigned(): bool
    {
        return $this->unsigned;
    }

    /**
     * Get the column comment
     *
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Get the column collation
     *
     * @return string|null
     */
    public function getCollation(): ?string
    {
        return $this->collation;
    }

    /**
     * Get ENUM/SET allowed values
     *
     * @return array|null
     */
    public function getEnumValues(): ?array
    {
        return $this->enumValues;
    }

    /**
     * Check if this column is part of the primary key
     *
     * @return bool
     */
    public function isPrimaryKey(): bool
    {
        return $this->primaryKey;
    }

    /**
     * Check if the column is a string type
     *
     * @return bool
     */
    public function isString(): bool
    {
        return in_array(strtolower($this->type), [
            'char', 'varchar', 'varchar2', 'nvarchar', 'nvarchar2', 'nchar',
            'text', 'ntext', 'tinytext', 'mediumtext', 'longtext', 'clob', 'nclob',
            'binary', 'varbinary', 'blob', 'tinyblob', 'mediumblob', 'longblob',
            'lvarchar',
        ]);
    }

    /**
     * Check if the column is a numeric type
     *
     * @return bool
     */
    public function isNumeric(): bool
    {
        return in_array(strtolower($this->type), [
            'tinyint', 'smallint', 'mediumint', 'int', 'integer', 'bigint',
            'float', 'double', 'decimal', 'numeric', 'real',
            'serial', 'serial8', 'bigserial', 'int8', 'smallfloat',
            'number', 'binary_float', 'binary_double',
        ]);
    }

    /**
     * Check if the column is an integer type
     *
     * @return bool
     */
    public function isInteger(): bool
    {
        $type = strtolower($this->type);

        if (
            in_array($type, [
            'tinyint', 'smallint', 'mediumint', 'int', 'integer', 'bigint',
            'serial', 'serial8', 'bigserial', 'int8',
            ])
        ) {
            return true;
        }

        // Oracle NUMBER without scale is integer-like
        if ($type === 'number') {
            $scale = $this->scale ?? null;
            return $scale === null || $scale === 0 || $scale === '0';
        }

        return false;
    }

    /**
     * Check if the column is a date/time type
     *
     * @return bool
     */
    public function isDateTime(): bool
    {
        return in_array(strtolower($this->type), [
            'date', 'time', 'datetime', 'timestamp', 'year'
        ]);
    }

    /**
     * Check if the column is a JSON type
     *
     * @return bool
     */
    public function isJson(): bool
    {
        return strtolower($this->type) === 'json';
    }

    /**
     * Check if the column is an ENUM type
     *
     * @return bool
     */
    public function isEnum(): bool
    {
        return strtolower($this->type) === 'enum';
    }

    /**
     * Convert to array representation
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'full_type' => $this->fullType,
            'length' => $this->length,
            'precision' => $this->precision,
            'scale' => $this->scale,
            'nullable' => $this->nullable,
            'default' => $this->default,
            'auto_increment' => $this->autoIncrement,
            'unsigned' => $this->unsigned,
            'comment' => $this->comment,
            'collation' => $this->collation,
            'enum_values' => $this->enumValues,
            'primary_key' => $this->primaryKey,
        ];
    }

    /**
     * Parse base type from full type definition
     *
     * @param  string  $fullType
     * @return string
     */
    protected function parseBaseType(string $fullType): string
    {
        // Remove length/precision specifier: varchar(255) -> varchar
        $type = preg_replace('/\([^)]+\)/', '', $fullType);
        // Remove unsigned/signed
        $type = preg_replace('/\s+(unsigned|signed)/i', '', $type);
        return strtolower(trim($type));
    }

    /**
     * Parse length from full type definition
     *
     * @param  string  $fullType
     * @return int|null
     */
    protected function parseLength(string $fullType): ?int
    {
        if (preg_match('/\((\d+)\)/', $fullType, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    /**
     * Parse unsigned from full type definition
     *
     * @param  string  $fullType
     * @return bool
     */
    protected function parseUnsigned(string $fullType): bool
    {
        return (bool) preg_match('/\bunsigned\b/i', $fullType);
    }
}
