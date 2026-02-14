<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

/**
 * Database Connection Interface
 *
 * This interface defines the contract for database connections, allowing
 * different connection implementations (PDO, mysqli, native drivers, etc.)
 * to be used interchangeably with the database abstraction layer.
 *
 * The interface focuses on low-level connection mechanics:
 * - Query preparation and execution
 * - Parameter binding
 * - Result fetching
 * - String escaping/quoting
 * - Connection state management
 *
 * SQL dialect-specific operations (schema introspection, DDL, etc.) are
 * handled by the Driver classes, not the connection interface.
 */
interface ConnectionInterface
{
    /**
     * Opens the database connection
     *
     * Implementations should be idempotent — calling connect() on an
     * already-connected instance should be a no-op.
     *
     * @return  void
     * @throws  \Hubzero\Database\Exception\ConnectionFailedException
     */
    public function connect(): void;

    /**
     * Closes the database connection
     *
     * After disconnecting, the connection can be re-opened by calling connect().
     * Calling disconnect() on an already-disconnected instance should be a no-op.
     *
     * @return  void
     */
    public function disconnect(): void;

    /**
     * Prepares a query for execution
     *
     * @param   string  $statement  The SQL statement to prepare
     * @return  mixed   A prepared statement handle
     */
    public function prepare(string $statement);

    /**
     * Binds parameters to a prepared statement
     *
     * @param   mixed  $statement  The prepared statement handle
     * @param   array  $bindings   The parameter values to bind
     * @param   array  $types      Optional type hints for parameters
     * @return  void
     */
    public function bind($statement, array $bindings, array $types = []): void;

    /**
     * Executes a prepared statement
     *
     * @param   mixed  $statement  The prepared statement handle
     * @return  bool   True on success, false on failure
     */
    public function execute($statement): bool;

    /**
     * Fetches a single row as an object
     *
     * @param   mixed   $statement  The executed statement handle
     * @param   string  $class      The class name for the returned object
     * @return  object|null
     */
    public function fetchObject($statement, string $class = 'stdClass'): ?object;

    /**
     * Fetches a single row as a numeric array
     *
     * @param   mixed  $statement  The executed statement handle
     * @return  array|null
     */
    public function fetchArray($statement): ?array;

    /**
     * Fetches a single row as an associative array
     *
     * @param   mixed  $statement  The executed statement handle
     * @return  array|null
     */
    public function fetchAssoc($statement): ?array;

    /**
     * Gets the auto-incremented value from the last INSERT
     *
     * @return  int|string
     */
    public function lastInsertId();

    /**
     * Gets the number of affected rows from the last statement
     *
     * @param   mixed  $statement  The executed statement handle
     * @return  int
     */
    public function affectedRows($statement): int;

    /**
     * Frees the resources associated with a statement
     *
     * @param   mixed  $statement  The statement handle to free
     * @return  void
     */
    public function freeResult($statement): void;

    /**
     * Escapes a string for safe use in SQL
     *
     * @param   string  $text   The string to escape
     * @param   bool    $extra  Whether to escape wildcards (% and _)
     * @return  string
     */
    public function escape(string $text, bool $extra = false): string;

    /**
     * Quotes a value for use in SQL (escapes and wraps in quotes)
     *
     * @param   mixed  $value  The value to quote
     * @return  string
     */
    public function quote($value): string;

    /**
     * Checks if the connection is active
     *
     * @return  bool
     */
    public function isConnected(): bool;

    /**
     * Gets the native connection object (PDO, mysqli, etc.)
     *
     * This provides an escape hatch for cases where direct access
     * to the underlying connection is needed.
     *
     * @return  mixed  The native connection object
     */
    public function getNativeConnection();

    /**
     * Gets the name of the database driver
     *
     * @return  string  Driver name (e.g., 'mysql', 'pgsql', 'sqlite')
     */
    public function getDriverName(): string;

    /**
     * Sets error reporting to throw exceptions
     *
     * @return  void
     */
    public function setExceptionMode(): void;

    /**
     * Sets error reporting to silent mode (errors returned, not thrown)
     *
     * @return  void
     */
    public function setSilentMode(): void;

    /**
     * Begins a database transaction
     *
     * @return  bool
     */
    public function beginTransaction(): bool;

    /**
     * Commits the current transaction
     *
     * @return  bool
     */
    public function commit(): bool;

    /**
     * Rolls back the current transaction
     *
     * @return  bool
     */
    public function rollBack(): bool;

    /**
     * Checks if currently inside a transaction
     *
     * @return  bool
     */
    public function inTransaction(): bool;

    /**
     * Executes a raw SQL statement directly (no prepared statement)
     *
     * This is useful for statements that don't return results and don't
     * need parameter binding, such as SET, PRAGMA, or DDL statements.
     *
     * @param   string  $statement  The SQL statement to execute
     * @return  int     The number of affected rows
     */
    public function exec(string $statement): int;

    /**
     * Executes a SQL query directly and returns a statement with results
     *
     * Unlike prepare+execute, this does not create a reusable prepared
     * statement. Use for queries without parameter bindings.
     *
     * @param   string  $statement  The SQL query to execute
     * @return  mixed   A statement handle with fetchable results
     */
    public function query(string $statement);
}
