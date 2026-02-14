<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Connection;

use Hubzero\Database\ConnectionInterface;
use Hubzero\Database\Exception\ConnectionFailedException;
use Hubzero\Database\Exception\QueryFailedException;

/**
 * MySQLi Connection Implementation
 */
class MysqliConnection implements ConnectionInterface
{
    /**
     * The mysqli instance
     *
     * @var \mysqli|null
     */
    protected $mysqli;

    /**
     * Connection options
     *
     * @var array
     */
    protected $options;

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
     * The database name
     *
     * @var string
     */
    protected $database;

    /**
     * The database host
     *
     * @var string
     */
    protected $host;

    /**
     * The database port
     *
     * @var int
     */
    protected $port;

    /**
     * The database socket
     *
     * @var string
     */
    protected $socket;

    /**
     * Constructor
     *
     * @param   array  $options  Connection options
     */
    public function __construct(array $options)
    {
        $this->options  = $options;
        $this->host     = $options['host'] ?? 'localhost';
        $this->username = $options['user'] ?? null;
        $this->password = $options['password'] ?? null;
        $this->database = $options['database'] ?? null;
        $this->port     = $options['port'] ?? 3306;
        $this->socket   = $options['socket'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function connect(): void
    {
        if ($this->mysqli) {
            return;
        }

        $this->mysqli = new \mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->database,
            (int)$this->port,
            $this->socket
        );

        if ($this->mysqli->connect_error) {
            throw new ConnectionFailedException('Connection failed: ' . $this->mysqli->connect_error, 500);
        }

        $this->mysqli->set_charset('utf8mb4');
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect(): void
    {
        if ($this->mysqli) {
            $this->mysqli->close();
            $this->mysqli = null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepare(string $statement)
    {
        $this->connect();
        $stmt = $this->mysqli->prepare($statement);

        if (!$stmt) {
            throw new QueryFailedException($this->mysqli->error, $this->mysqli->errno);
        }

        return new MysqliStatement($stmt, $statement);
    }

    /**
     * {@inheritdoc}
     */
    public function bind($statement, array $bindings, array $types = []): void
    {
        if (empty($bindings)) {
            return;
        }

        foreach ($bindings as $key => $value) {
            $type = $types[$key] ?? $this->inferType($value);
            $statement->addBinding($value, $type);
        }
    }

    /**
     * Infer type for mysqli bind_param
     *
     * @param   mixed   $value
     * @return  string
     */
    protected function inferType($value): string
    {
        if (is_int($value)) {
            return 'i';
        }
        if (is_float($value)) {
            return 'd';
        }
        return 's';
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
        return $statement->fetchObject($class);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchArray($statement): ?array
    {
        return $statement->fetchArray();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAssoc($statement): ?array
    {
        return $statement->fetchAssoc();
    }

    /**
     * {@inheritdoc}
     */
    public function lastInsertId()
    {
        return $this->mysqli->insert_id;
    }

    /**
     * {@inheritdoc}
     */
    public function affectedRows($statement): int
    {
        return $statement->affectedRows();
    }

    /**
     * {@inheritdoc}
     */
    public function freeResult($statement): void
    {
        $statement->close();
    }

    /**
     * {@inheritdoc}
     */
    public function escape(string $text, bool $extra = false): string
    {
        $this->connect();
        $result = $this->mysqli->real_escape_string($text);

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
        return "'" . $this->escape((string)$value) . "'";
    }

    /**
     * {@inheritdoc}
     */
    public function isConnected(): bool
    {
        if (!$this->mysqli) {
            return false;
        }
        return $this->mysqli->ping();
    }

    /**
     * {@inheritdoc}
     */
    public function getNativeConnection()
    {
        return $this->mysqli;
    }

    /**
     * {@inheritdoc}
     */
    public function getDriverName(): string
    {
        return 'mysqli';
    }

    /**
     * {@inheritdoc}
     */
    public function setExceptionMode(): void
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    }

    /**
     * {@inheritdoc}
     */
    public function setSilentMode(): void
    {
        mysqli_report(MYSQLI_REPORT_OFF);
    }

    /**
     * {@inheritdoc}
     */
    public function beginTransaction(): bool
    {
        $this->connect();
        return $this->mysqli->begin_transaction();
    }

    /**
     * {@inheritdoc}
     */
    public function commit(): bool
    {
        return $this->mysqli->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function rollBack(): bool
    {
        return $this->mysqli->rollback();
    }

    /**
     * {@inheritdoc}
     */
    public function inTransaction(): bool
    {
        return false; // Not easily checking w/ mysqli alone
    }

    /**
     * {@inheritdoc}
     */
    public function exec(string $statement): int
    {
        $this->connect();
        $result = $this->mysqli->query($statement);
        if ($result === false) {
            throw new QueryFailedException($this->mysqli->error, $this->mysqli->errno);
        }
        return $this->mysqli->affected_rows;
    }
}
