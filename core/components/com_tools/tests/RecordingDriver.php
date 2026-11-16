<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Tests;

/**
 * A database driver that knows a table's columns and records the queries
 * it is given instead of running them
 */
class RecordingDriver
{
    /**
     * @var  array<mixed>
     */
    public $queries = [];

    /**
     * @param   array<mixed>  $columns
     * @return  void
     */
    public function __construct(private array $columns) {}

    /**
     * @param   mixed  $table
     * @param   bool  $typeOnly
     * @return  array<object>
     */
    public function getTableColumns($table, $typeOnly = true)
    {
        $fields = [];
        foreach ($this->columns as $column) {
            $fields[$column] = (object) ['Field' => $column, 'Default' => null];
        }
        return $fields;
    }

    /**
     * @param   mixed  $name
     * @return  string
     */
    public function quoteName($name)
    {
        return '`' . $name . '`';
    }

    /**
     * @param   mixed  $value
     * @return  string
     */
    public function quote($value)
    {
        return "'" . $value . "'";
    }

    /**
     * @param   mixed  $query
     * @return  $this
     */
    public function setQuery($query)
    {
        $this->queries[] = (string) $query;
        return $this;
    }

    /**
     * @return  true
     */
    public function execute()
    {
        return true;
    }

    /**
     * @return  true
     */
    public function query()
    {
        return true;
    }
}
