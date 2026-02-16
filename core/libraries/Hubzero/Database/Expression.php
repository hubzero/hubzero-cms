<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

/**
 * Database Expression Builder
 *
 * Provides a fluent, database-agnostic interface for building SQL expressions
 * like aggregate functions, string functions, and date functions. The Expression
 * class abstracts differences between database systems (MySQL, PostgreSQL, SQLite)
 * so code can work across multiple databases without modification.
 *
 * ## Basic Usage
 *
 * ```php
 * use Hubzero\Database\Expression as Expr;
 *
 * // Aggregate functions
 * $query->select(Expr::sum('amount')->as('total'));
 * $query->select(Expr::count()->as('total_rows'));
 * $query->select(Expr::avg('price')->as('average_price'));
 *
 * // String functions
 * $query->select(Expr::concat('first_name', Expr::literal(' '), 'last_name')->as('full_name'));
 * $query->select(Expr::upper('name')->as('uppercase_name'));
 *
 * // Conditional functions
 * $query->select(Expr::coalesce('nickname', 'name')->as('display_name'));
 * $query->select(Expr::ifNull('deleted_at', Expr::literal('active'))->as('status'));
 *
 * // Date functions
 * $query->whereRaw(Expr::year('created_at')->build($syntax) . ' = ?', [2024]);
 * ```
 *
 * ## How It Works
 *
 * Each expression method stores the operation type and arguments. When `build()`
 * is called with a Syntax object, the expression is rendered as database-specific
 * SQL. For example, `Expr::concat('a', 'b')`:
 * - MySQL: `CONCAT(a, b)`
 * - PostgreSQL: `a || b`
 * - SQLite: `a || b`
 *
 * ## Nesting Expressions
 *
 * Expressions can be nested for complex operations:
 *
 * ```php
 * // UPPER(COALESCE(nickname, name))
 * $expr = Expr::upper(Expr::coalesce('nickname', 'name'));
 *
 * // CONCAT(first_name, ' ', UPPER(last_name))
 * $expr = Expr::concat('first_name', Expr::literal(' '), Expr::upper('last_name'));
 * ```
 */
class Expression
{
    /**
     * Expression type constants
     */
    public const TYPE_RAW = 'raw';
    public const TYPE_COLUMN = 'column';
    public const TYPE_LITERAL = 'literal';
    public const TYPE_FUNCTION = 'function';
    public const TYPE_OPERATOR = 'operator';

    /**
     * The expression type
     *
     * @var string
     */
    protected string $type;

    /**
     * The function name (for TYPE_FUNCTION)
     *
     * @var string|null
     */
    protected ?string $function = null;

    /**
     * The expression arguments
     *
     * @var array
     */
    protected array $arguments = [];

    /**
     * The column alias
     *
     * @var string|null
     */
    protected ?string $alias = null;

    /**
     * Parameter bindings for this expression
     *
     * @var array
     */
    protected array $bindings = [];

    /**
     * Create a new expression instance
     *
     * @param string      $type       The expression type
     * @param string|null $function   The function name (for function expressions)
     * @param array       $arguments  The expression arguments
     */
    protected function __construct(string $type, ?string $function = null, array $arguments = [])
    {
        $this->type = $type;
        $this->function = $function;
        $this->arguments = $arguments;
    }

    // =========================================================================
    // Static Factory Methods
    // =========================================================================

    /**
     * Create a raw SQL expression (bypasses escaping)
     *
     * Use with caution - ensure the SQL is safe from injection.
     *
     * @param string $sql The raw SQL string
     * @return static
     */
    public static function raw(string $sql): static
    {
        return new static(self::TYPE_RAW, null, [$sql]);
    }

    /**
     * Create a hexadecimal literal expression (e.g. 0xC2AD)
     *
     * @param string $hex Hex string with or without 0x prefix
     * @return static
     */
    public static function hexLiteral(string $hex): static
    {
        $hex = strtolower(trim($hex));
        if (str_starts_with($hex, '0x')) {
            $hex = substr($hex, 2);
        }

        if ($hex === '' || preg_match('/[^0-9a-f]/', $hex)) {
            throw new \InvalidArgumentException('Hex literal must contain only 0-9 and a-f.');
        }

        if ((strlen($hex) % 2) === 1) {
            $hex = '0' . $hex;
        }

        return new static(self::TYPE_RAW, null, ['0x' . $hex]);
    }

    /**
     * Create a column reference expression
     *
     * The column name will be properly quoted for the database.
     *
     * @param string $name The column name (can include table prefix: 'table.column')
     * @return static
     */
    public static function column(string $name): static
    {
        return new static(self::TYPE_COLUMN, null, [$name]);
    }

    /**
     * Create a subquery expression (wraps SQL in parentheses)
     *
     * @param string|\Hubzero\Database\Query $query SQL string or Query instance
     * @return static
     */
    public static function subquery($query): static
    {
        if ($query instanceof \Hubzero\Database\Query) {
            $query = $query->toSql();
        }

        if (!is_string($query) || trim($query) === '') {
            throw new \InvalidArgumentException('Subquery must be a non-empty SQL string or Query instance.');
        }

        return new static(self::TYPE_RAW, null, ['(' . $query . ')']);
    }

    /**
     * Create a literal value expression
     *
     * The value will be properly escaped/quoted.
     *
     * @param mixed $value The literal value
     * @return static
     */
    public static function literal($value): static
    {
        $expr = new static(self::TYPE_LITERAL, null, [$value]);
        $expr->bindings[] = $value;
        return $expr;
    }

    // =========================================================================
    // Aggregate Functions
    // =========================================================================

    /**
     * Create a SUM() expression
     *
     * @param string|Expression $column The column to sum
     * @return static
     */
    public static function sum($column): static
    {
        return new static(self::TYPE_FUNCTION, 'SUM', [$column]);
    }

    /**
     * Create a COUNT() expression
     *
     * @param string|Expression $column The column to count (default: '*')
     * @return static
     */
    public static function count($column = '*'): static
    {
        return new static(self::TYPE_FUNCTION, 'COUNT', [$column]);
    }

    /**
     * Create a COUNT(DISTINCT column) expression
     *
     * @param string|Expression $column The column to count distinct values
     * @return static
     */
    public static function countDistinct($column): static
    {
        return new static(self::TYPE_FUNCTION, 'COUNT_DISTINCT', [$column]);
    }

    /**
     * Create an AVG() expression
     *
     * @param string|Expression $column The column to average
     * @return static
     */
    public static function avg($column): static
    {
        return new static(self::TYPE_FUNCTION, 'AVG', [$column]);
    }

    /**
     * Create a MIN() expression
     *
     * @param string|Expression $column The column to find minimum
     * @return static
     */
    public static function min($column): static
    {
        return new static(self::TYPE_FUNCTION, 'MIN', [$column]);
    }

    /**
     * Create a MAX() expression
     *
     * @param string|Expression $column The column to find maximum
     * @return static
     */
    public static function max($column): static
    {
        return new static(self::TYPE_FUNCTION, 'MAX', [$column]);
    }

    // =========================================================================
    // String Functions
    // =========================================================================

    /**
     * Create a CONCAT() expression
     *
     * Concatenates multiple values. Database differences are handled:
     * - MySQL: CONCAT(a, b, c)
     * - PostgreSQL/SQLite: a || b || c
     *
     * @param mixed ...$parts The parts to concatenate (strings, columns, or Expressions)
     * @return static
     */
    public static function concat(...$parts): static
    {
        return new static(self::TYPE_FUNCTION, 'CONCAT', $parts);
    }

    /**
     * Create an UPPER() expression
     *
     * @param string|Expression $column The column or expression to uppercase
     * @return static
     */
    public static function upper($column): static
    {
        return new static(self::TYPE_FUNCTION, 'UPPER', [$column]);
    }

    /**
     * Create a LOWER() expression
     *
     * @param string|Expression $column The column or expression to lowercase
     * @return static
     */
    public static function lower($column): static
    {
        return new static(self::TYPE_FUNCTION, 'LOWER', [$column]);
    }

    /**
     * Create a LENGTH() expression
     *
     * Returns the length of a string in characters.
     * Database differences:
     * - MySQL: CHAR_LENGTH() for character count
     * - PostgreSQL: LENGTH() or CHAR_LENGTH()
     * - SQLite: LENGTH()
     *
     * @param string|Expression $column The column or expression
     * @return static
     */
    public static function length($column): static
    {
        return new static(self::TYPE_FUNCTION, 'LENGTH', [$column]);
    }

    /**
     * Create a TRIM() expression
     *
     * @param string|Expression $column The column or expression to trim
     * @return static
     */
    public static function trim($column): static
    {
        return new static(self::TYPE_FUNCTION, 'TRIM', [$column]);
    }

    /**
     * Create a SUBSTRING() expression
     *
     * Database differences are handled:
     * - MySQL: SUBSTRING(str, start, length)
     * - PostgreSQL: SUBSTRING(str FROM start FOR length)
     * - SQLite: SUBSTR(str, start, length)
     *
     * @param string|Expression $column The column or expression
     * @param int               $start  Start position (1-indexed)
     * @param int|null          $length Length of substring (null = to end)
     * @return static
     */
    public static function substring($column, int $start, ?int $length = null): static
    {
        return new static(self::TYPE_FUNCTION, 'SUBSTRING', [$column, $start, $length]);
    }

    /**
     * Create a REPLACE() expression
     *
     * @param string|Expression $column  The column or expression
     * @param string            $search  The string to search for
     * @param string            $replace The replacement string
     * @return static
     */
    public static function replace($column, $search, $replace): static
    {
        $expr = new static(self::TYPE_FUNCTION, 'REPLACE', [$column, $search, $replace]);
        $expr->bindings = [$search, $replace];
        return $expr;
    }

    // =========================================================================
    // Conditional Functions
    // =========================================================================

    /**
     * Create a COALESCE() expression
     *
     * Returns the first non-null value.
     *
     * @param mixed ...$values The values to check
     * @return static
     */
    public static function coalesce(...$values): static
    {
        return new static(self::TYPE_FUNCTION, 'COALESCE', $values);
    }

    /**
     * Create an IFNULL()/COALESCE() expression
     *
     * Returns the column value if not null, otherwise the default.
     * Database differences:
     * - MySQL: IFNULL(column, default)
     * - PostgreSQL/SQLite: COALESCE(column, default)
     *
     * @param string|Expression $column  The column to check
     * @param mixed             $default The default value if null
     * @return static
     */
    public static function ifNull($column, $default): static
    {
        return new static(self::TYPE_FUNCTION, 'IFNULL', [$column, $default]);
    }

    /**
     * Create a NULLIF() expression
     *
     * Returns NULL if the two arguments are equal, otherwise returns the first.
     *
     * @param string|Expression $column The column or expression
     * @param mixed             $value  The value to compare
     * @return static
     */
    public static function nullIf($column, $value): static
    {
        return new static(self::TYPE_FUNCTION, 'NULLIF', [$column, $value]);
    }

    /**
     * Create a simple CASE expression
     *
     * Example: Expr::caseWhen('status', ['active' => 'Active', 'inactive' => 'Inactive'], 'Unknown')
     *
     * @param string|Expression $column   The column to switch on
     * @param array             $cases    Key-value pairs of value => result
     * @param mixed             $default  Default value (ELSE clause)
     * @return static
     */
    public static function caseWhen($column, array $cases, $default = null): static
    {
        return new static(self::TYPE_FUNCTION, 'CASE', [$column, $cases, $default]);
    }

    // =========================================================================
    // Math Functions
    // =========================================================================

    /**
     * Create a ROUND() expression
     *
     * @param string|Expression $column   The column or expression to round
     * @param int               $decimals Number of decimal places
     * @return static
     */
    public static function round($column, int $decimals = 0): static
    {
        return new static(self::TYPE_FUNCTION, 'ROUND', [$column, $decimals]);
    }

    /**
     * Create a FLOOR() expression
     *
     * @param string|Expression $column The column or expression
     * @return static
     */
    public static function floor($column): static
    {
        return new static(self::TYPE_FUNCTION, 'FLOOR', [$column]);
    }

    /**
     * Create a CEIL()/CEILING() expression
     *
     * @param string|Expression $column The column or expression
     * @return static
     */
    public static function ceil($column): static
    {
        return new static(self::TYPE_FUNCTION, 'CEIL', [$column]);
    }

    /**
     * Create an ABS() expression
     *
     * @param string|Expression $column The column or expression
     * @return static
     */
    public static function abs($column): static
    {
        return new static(self::TYPE_FUNCTION, 'ABS', [$column]);
    }

    /**
     * Create an ORDER BY random expression.
     *
     * This is intended for sort clauses (random row ordering), not for
     * generating random numeric values in select expressions.
     *
     * @return static
     */
    public static function randomOrder(): static
    {
        return new static(self::TYPE_FUNCTION, 'RANDOM_ORDER', []);
    }

    /**
     * Create a percentage expression: (numerator * scale) / denominator
     *
     * @param mixed $numerator
     * @param mixed $denominator
     * @param int|float $scale
     * @return static
     */
    public static function percent($numerator, $denominator, $scale = 100): static
    {
        $scaled = new static(self::TYPE_OPERATOR, '*', [$numerator, $scale]);
        return new static(self::TYPE_OPERATOR, '/', [$scaled, $denominator]);
    }

    // =========================================================================
    // Sequence Functions
    // =========================================================================

    /**
     * Create a NEXTVAL expression for a sequence
     *
     * Gets the next value from a sequence (incrementing it). The SQL
     * generated is database-specific:
     * - PostgreSQL: nextval('sequence_name')
     * - Firebird: NEXT VALUE FOR "SEQUENCE_NAME"
     * - Informix: sequence_name.NEXTVAL
     * - MariaDB 10.3+: NEXTVAL(`sequence_name`)
     * - MySQL/SQLite: Resolved via table-based emulation
     *
     * @param  string  $sequence  The sequence name
     * @return static
     */
    public static function nextVal(string $sequence): static
    {
        return new static(self::TYPE_FUNCTION, 'NEXTVAL', [$sequence]);
    }

    /**
     * Create a CURRVAL expression for a sequence
     *
     * Gets the current value of a sequence (without incrementing).
     * The SQL generated is database-specific:
     * - PostgreSQL: currval('sequence_name')
     * - Firebird: GEN_ID("SEQUENCE_NAME", 0)
     * - Informix: sequence_name.CURRVAL
     * - MariaDB 10.3+: LASTVAL(`sequence_name`)
     * - MySQL/SQLite: Resolved via table-based emulation
     *
     * @param  string  $sequence  The sequence name
     * @return static
     */
    public static function currVal(string $sequence): static
    {
        return new static(self::TYPE_FUNCTION, 'CURRVAL', [$sequence]);
    }

    // =========================================================================
    // Date/Time Functions
    // =========================================================================

    /**
     * Create a NOW() expression
     *
     * Returns the current date and time.
     * Database differences:
     * - MySQL: NOW()
     * - PostgreSQL: NOW() or CURRENT_TIMESTAMP
     * - SQLite: datetime('now')
     *
     * @return static
     */
    public static function now(): static
    {
        return new static(self::TYPE_FUNCTION, 'NOW', []);
    }

    /**
     * Create a CURRENT_TIMESTAMP expression
     *
     * Returns the current date and time.
     * Database differences can be handled by syntax overrides.
     *
     * @return static
     */
    public static function currentTimestamp(): static
    {
        return new static(self::TYPE_FUNCTION, 'CURRENT_TIMESTAMP', []);
    }

    /**
     * Create a CURRENT_DATE expression
     *
     * Returns the current date (without time).
     *
     * @return static
     */
    public static function currentDate(): static
    {
        return new static(self::TYPE_FUNCTION, 'CURRENT_DATE', []);
    }

    /**
     * Create a CURRENT_TIME expression
     *
     * Returns the current time (without date).
     *
     * @return static
     */
    public static function currentTime(): static
    {
        return new static(self::TYPE_FUNCTION, 'CURRENT_TIME', []);
    }

    /**
     * Create a DATE() expression - extract date part from datetime
     *
     * @param string|Expression $column The datetime column
     * @return static
     */
    public static function date($column): static
    {
        return new static(self::TYPE_FUNCTION, 'DATE', [$column]);
    }

    /**
     * Create a TIME() expression - extract time part from datetime
     *
     * @param string|Expression $column The datetime column
     * @return static
     */
    public static function time($column): static
    {
        return new static(self::TYPE_FUNCTION, 'TIME', [$column]);
    }

    /**
     * Create a YEAR() expression - extract year from date/datetime
     *
     * @param string|Expression $column The date/datetime column
     * @return static
     */
    public static function year($column): static
    {
        return new static(self::TYPE_FUNCTION, 'YEAR', [$column]);
    }

    /**
     * Create a MONTH() expression - extract month from date/datetime
     *
     * @param string|Expression $column The date/datetime column
     * @return static
     */
    public static function month($column): static
    {
        return new static(self::TYPE_FUNCTION, 'MONTH', [$column]);
    }

    /**
     * Create a DAY() expression - extract day from date/datetime
     *
     * @param string|Expression $column The date/datetime column
     * @return static
     */
    public static function day($column): static
    {
        return new static(self::TYPE_FUNCTION, 'DAY', [$column]);
    }

    /**
     * Create an HOUR() expression - extract hour from time/datetime
     *
     * @param string|Expression $column The time/datetime column
     * @return static
     */
    public static function hour($column): static
    {
        return new static(self::TYPE_FUNCTION, 'HOUR', [$column]);
    }

    /**
     * Create a MINUTE() expression - extract minute from time/datetime
     *
     * @param string|Expression $column The time/datetime column
     * @return static
     */
    public static function minute($column): static
    {
        return new static(self::TYPE_FUNCTION, 'MINUTE', [$column]);
    }

    /**
     * Create a SECOND() expression - extract second from time/datetime
     *
     * @param string|Expression $column The time/datetime column
     * @return static
     */
    public static function second($column): static
    {
        return new static(self::TYPE_FUNCTION, 'SECOND', [$column]);
    }

    /**
     * Create a DATE_FORMAT() expression
     *
     * Database differences:
     * - MySQL: DATE_FORMAT(date, format)
     * - PostgreSQL: TO_CHAR(date, format)
     * - SQLite: strftime(format, date)
     *
     * @param string|Expression $column The date/datetime column
     * @param string            $format The format string (MySQL format)
     * @return static
     */
    public static function dateFormat($column, string $format): static
    {
        $expr = new static(self::TYPE_FUNCTION, 'DATE_FORMAT', [$column, $format]);
        $expr->bindings[] = $format;
        return $expr;
    }

    // =========================================================================
    // Operators (Sequential Evaluation)
    // =========================================================================

    /**
     * Add a value to this expression (+)
     *
     * @param  mixed  $value  The value to add
     * @return static
     */
    public function plus($value): static
    {
        return new static(self::TYPE_OPERATOR, '+', [$this, $value]);
    }

    /**
     * Subtract a value from this expression (-)
     *
     * @param  mixed  $value  The value to subtract
     * @return static
     */
    public function minus($value): static
    {
        return new static(self::TYPE_OPERATOR, '-', [$this, $value]);
    }

    /**
     * Multiply this expression by a value (*)
     *
     * @param  mixed  $value  The value to multiply by
     * @return static
     */
    public function times($value): static
    {
        return new static(self::TYPE_OPERATOR, '*', [$this, $value]);
    }

    /**
     * Divide this expression by a value (/)
     *
     * @param  mixed  $value  The value to divide by
     * @return static
     */
    public function dividedBy($value): static
    {
        return new static(self::TYPE_OPERATOR, '/', [$this, $value]);
    }

    /**
     * Modulo of this expression by a value (%)
     *
     * @param  mixed  $value  The value to mod by
     * @return static
     */
    public function mod($value): static
    {
        return new static(self::TYPE_OPERATOR, 'MOD', [$this, $value]);
    }

    // =========================================================================
    // Alias and Build Methods
    // =========================================================================

    /**
     * Set an alias for this expression
     *
     * @param string $alias The alias name
     * @return $this
     */
    public function as(string $alias): static
    {
        $clone = clone $this;
        $clone->alias = $alias;
        return $clone;
    }

    /**
     * Get the alias for this expression
     *
     * @return string|null
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * Get parameter bindings for this expression
     *
     * @return array
     */
    public function getBindings(): array
    {
        $bindings = $this->bindings;

        // Collect bindings from nested expressions
        foreach ($this->arguments as $arg) {
            if ($arg instanceof self) {
                $bindings = array_merge($bindings, $arg->getBindings());
            }
        }

        return $bindings;
    }

    /**
     * Get the expression type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the function name
     *
     * @return string|null
     */
    public function getFunction(): ?string
    {
        return $this->function;
    }

    /**
     * Get the expression arguments
     *
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Build the SQL string for this expression
     *
     * @param object $syntax The syntax object (provides database-specific rendering)
     * @return string
     */
    public function build($syntax): string
    {
        return $syntax->buildExpression($this);
    }

    /**
     * Allow string casting for debugging
     *
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            'Expression(%s%s%s)',
            $this->type,
            $this->function ? ':' . $this->function : '',
            $this->alias ? ' AS ' . $this->alias : ''
        );
    }
}
