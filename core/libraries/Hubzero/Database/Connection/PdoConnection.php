<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Connection;

use Hubzero\Database\ConnectionInterface;
use Hubzero\Database\Exception\ConnectionFailedException;

/**
 * PDO Connection Implementation
 *
 * This class wraps a PDO connection and implements ConnectionInterface,
 * providing a consistent API for database operations regardless of the
 * underlying PDO driver (MySQL, PostgreSQL, SQLite, etc.).
 *
 * Connections are lazy — the PDO instance is created on first use, not
 * in the constructor. Call connect() explicitly to eagerly connect, or
 * let it auto-connect when a method needs the PDO instance.
 *
 * Example usage:
 * ```php
 * // Lazy connection (connects on first query)
 * $connection = new PdoConnection(
 *     'mysql:host=localhost;dbname=test',
 *     'username',
 *     'password'
 * );
 *
 * // Eager connection
 * $connection = new PdoConnection('mysql:host=localhost;dbname=test', 'user', 'pass');
 * $connection->connect();
 * ```
 */
class PdoConnection implements ConnectionInterface
{
    /**
     * The PDO connection instance (null when disconnected)
     *
     * @var \PDO|null
     */
    protected $pdo;

    /**
     * The Data Source Name for connection
     *
     * @var string
     */
    protected $dsn;

    /**
     * The database username
     *
     * @var string
     */
    protected $username;

    /**
     * The database password
     *
     * @var string
     */
    protected $password;

    /**
     * Additional PDO connection options
     *
     * @var array
     */
    protected $options;

    /**
     * Creates a new PdoConnection instance
     *
     * The constructor stores connection parameters but does NOT open a
     * connection. The PDO instance is created lazily on first use via
     * getPdo(), or eagerly by calling connect().
     *
     * @param   string  $dsn       The Data Source Name
     * @param   string  $username  The database username
     * @param   string  $password  The database password
     * @param   array   $options   Additional PDO options
     * @throws  ConnectionFailedException  If PDO extension is not installed
     */
    public function __construct(string $dsn, string $username = '', string $password = '', array $options = [])
    {
        if (!class_exists('PDO')) {
            throw new ConnectionFailedException('PDO extension is not installed or enabled.', 500);
        }

        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function connect(): void
    {
        if ($this->pdo !== null) {
            return;
        }

        try {
            $this->pdo = new \PDO($this->dsn, $this->username, $this->password, $this->options);
            $this->setExceptionMode();
        } catch (\PDOException $e) {
            throw new ConnectionFailedException($e->getMessage(), 500, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect(): void
    {
        $this->pdo = null;

        // Force garbage collection to ensure PDO connection is destroyed
        // immediately. This is critical for databases like DB2 where a
        // stale connection can leave catalog metadata cached.
        gc_collect_cycles();
    }

    /**
     * Gets the PDO instance, connecting lazily if needed
     *
     * Made public to allow driver-specific PDO methods (e.g., cubrid_schema())
     *
     * @return  \PDO
     * @throws  ConnectionFailedException
     */
    public function getPdo(): \PDO
    {
        if ($this->pdo === null) {
            $this->connect();
        }

        return $this->pdo;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare(string $statement)
    {
        return $this->getPdo()->prepare($statement);
    }

    /**
     * {@inheritdoc}
     */
    public function bind($statement, array $bindings, array $types = []): void
    {
        $idx = 1;

        foreach ($bindings as $key => $binding) {
            $type = $types[$key] ?? $this->inferType($binding);
            $statement->bindValue($idx, $binding, $type);
            $idx++;
        }
    }

    /**
     * Infers the PDO parameter type from the value
     *
     * @param   mixed  $value  The value to check
     * @return  int    PDO parameter type constant
     */
    protected function inferType($value): int
    {
        if (is_bool($value)) {
            return \PDO::PARAM_BOOL;
        }
        if (is_null($value)) {
            return \PDO::PARAM_NULL;
        }
        if (is_int($value)) {
            return \PDO::PARAM_INT;
        }
        return \PDO::PARAM_STR;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($statement): bool
    {
        return $statement->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchObject($statement, string $class = 'stdClass'): ?object
    {
        $result = $statement->fetchObject($class);
        return $result === false ? null : $result;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchArray($statement): ?array
    {
        $result = $statement->fetch(\PDO::FETCH_NUM);
        return $result === false ? null : $result;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAssoc($statement): ?array
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        return $result === false ? null : $result;
    }

    /**
     * {@inheritdoc}
     */
    public function lastInsertId()
    {
        return $this->getPdo()->lastInsertId();
    }

    /**
     * {@inheritdoc}
     */
    public function affectedRows($statement): int
    {
        return $statement->rowCount();
    }

    /**
     * {@inheritdoc}
     */
    public function freeResult($statement): void
    {
        $statement->closeCursor();
    }

    /**
     * {@inheritdoc}
     */
    public function escape(string $text, bool $extra = false): string
    {
        // PDO::quote includes surrounding quotes, so we strip them
        $result = substr($this->getPdo()->quote($text), 1, -1);

        if ($extra) {
            $result = addcslashes($result, '%_');
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function quote($value): string
    {
        if ($value === null) {
            return 'NULL';
        }
        return $this->getPdo()->quote((string) $value);
    }

    /**
     * {@inheritdoc}
     */
    public function isConnected(): bool
    {
        if (!$this->pdo) {
            return false;
        }

        try {
            $this->pdo->query('SELECT 1');
            return true;
        } catch (\PDOException $e) {
            // Some drivers (like Firebird) require specific syntax or a transaction for SELECT 1.
            // In these cases, we fall back to checking the server version attribute which
            // generally triggers a server roundtrip without requiring specific SQL.
            try {
                return (bool) $this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
            } catch (\Exception $e2) {
                // Last resort: if PDO was constructed without throwing, assume connected.
                // Some drivers (e.g. pdo_ibm for DB2) support neither SELECT 1 nor
                // ATTR_SERVER_VERSION, but the PDO object itself is valid.
                return true;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getNativeConnection()
    {
        return $this->getPdo();
    }

    /**
     * {@inheritdoc}
     */
    public function getDriverName(): string
    {
        if ($this->pdo) {
            return $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
        }

        // Try to parse driver from DSN to avoid connecting just for the name
        if (preg_match('/^([a-z0-9]+):/', $this->dsn, $matches)) {
            return $matches[1];
        }

        return $this->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
    }

    /**
     * Get a PDO attribute
     *
     * @param   int  $attribute  The PDO attribute constant
     * @return  mixed  The attribute value
     */
    public function getAttribute(int $attribute)
    {
        return $this->getPdo()->getAttribute($attribute);
    }

    /**
     * Set a PDO attribute
     *
     * @param   int    $attribute  The PDO attribute constant
     * @param   mixed  $value      The value to set
     * @return  bool   True on success
     */
    public function setAttribute(int $attribute, $value): bool
    {
        return $this->getPdo()->setAttribute($attribute, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setExceptionMode(): void
    {
        $this->getPdo()->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setSilentMode(): void
    {
        $this->getPdo()->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
    }

    /**
     * {@inheritdoc}
     */
    public function beginTransaction(): bool
    {
        return $this->getPdo()->beginTransaction();
    }

    /**
     * {@inheritdoc}
     */
    public function commit(): bool
    {
        return $this->getPdo()->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function rollBack(): bool
    {
        return $this->getPdo()->rollBack();
    }

    /**
     * {@inheritdoc}
     */
    public function inTransaction(): bool
    {
        if (!$this->pdo) {
            return false;
        }
        return $this->pdo->inTransaction();
    }

    /**
     * {@inheritdoc}
     */
    public function exec(string $statement): int
    {
        $result = $this->getPdo()->exec($statement);
        return $result === false ? 0 : $result;
    }

    /**
     * {@inheritdoc}
     */
    public function query(string $statement)
    {
        return $this->getPdo()->query($statement);
    }

    /**
     * Test if PDO is available
     *
     * @return  bool
     */
    public static function isAvailable(): bool
    {
        return class_exists('PDO');
    }
}
