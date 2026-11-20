<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Connection;

/**
 * MySQLi Statement Wrapper
 */
class MysqliStatement
{
    /**
     * The mysqli statement
     *
     * @var \mysqli_stmt
     */
    protected $stmt;

    /**
     * Pending bindings
     *
     * @var array<mixed>
     */
    protected $bindings = [];

    /**
     * Binding types
     *
     * @var string
     */
    protected $types = '';

    /**
     * The result set
     *
     * @var \mysqli_result|null
     */
    protected $result = null;

    /**
     * The query string
     *
     * @var string
     */
    public $queryString;

    /**
     * Constructor
     *
     * @param   \mysqli_stmt  $stmt
     * @param   string        $queryString
     */
    public function __construct(\mysqli_stmt $stmt, string $queryString = '')
    {
        $this->stmt        = $stmt;
        $this->queryString = $queryString;
    }

    /**
     * Bind a value (stored for later execution)
     *
     * @param   mixed   $value
     * @param   string  $type
     * @return  void
     */
    public function addBinding($value, $type)
    {
        $this->bindings[] = $value;
        $this->types .= $type;
    }

    /**
     * Execute the statement
     *
     * @return  bool
     */
    public function execute()
    {
        if (!empty($this->bindings)) {
            // Reset bindings on statement before binding? No, bind_param overwrites.
            // But we need to pass by reference.
            $params = [];
            $params[] = $this->types;
            foreach ($this->bindings as $key => $value) {
                $params[] = &$this->bindings[$key];
            }
            call_user_func_array([$this->stmt, 'bind_param'], $params);
        }

        $success = $this->stmt->execute();

        if ($success) {
            // Get result set for metadata or fetching
            $this->result = $this->stmt->get_result() ?: null;
        }

        return $success;
    }

    /**
     * Fetch object
     *
     * @param   string  $class
     * @return  object|null
     */
    public function fetchObject($class = 'stdClass')
    {
        if (!$this->result) {
            return null;
        }
        if (!class_exists($class)) {
            throw new \InvalidArgumentException('No class "' . $class . '" to fetch a row into.');
        }
        return $this->result->fetch_object($class) ?: null;
    }

    /**
     * Fetch array
     *
     * @return  array<mixed>|null
     */
    public function fetchArray()
    {
        if (!$this->result) {
            return null;
        }
        return $this->result->fetch_row();
    }

    /**
     * Fetch assoc
     *
     * @return  array<mixed>|null
     */
    public function fetchAssoc()
    {
        if (!$this->result) {
            return null;
        }
        return $this->result->fetch_assoc() ?: null;
    }

    /**
     * Get affected rows
     *
     * @return  int
     */
    public function affectedRows()
    {
        return (int) $this->stmt->affected_rows;
    }

    /**
     * Close statement
     *
     * @return  void
     */
    public function close()
    {
        if ($this->result instanceof \mysqli_result) {
            try {
                $this->result->free();
            } catch (\Throwable $e) {
                // Ignore if already freed
            }
            $this->result = null;
        }

        try {
            $this->stmt->close();
        } catch (\Throwable $e) {
            // Ignore if already closed
        }
    }

    /**
     * Close cursor (PDO compatibility)
     *
     * @return  void
     */
    public function closeCursor()
    {
        $this->close();
    }
}
