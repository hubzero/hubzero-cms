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
     * @var  array
     */
    public $queries = [];

    /**
     * @param   array  $columns
     * @return  void
     */
    public function __construct(private array $columns) {}

    public function getTableColumns($table, $typeOnly = true)
    {
        $fields = [];
        foreach ($this->columns as $column) {
            $fields[$column] = (object) ['Field' => $column, 'Default' => null];
        }
        return $fields;
    }

    public function quoteName($name)
    {
        return '`' . $name . '`';
    }

    public function quote($value)
    {
        return "'" . $value . "'";
    }

    public function setQuery($query)
    {
        $this->queries[] = (string) $query;
        return $this;
    }

    public function execute()
    {
        return true;
    }

    public function query()
    {
        return true;
    }
}
