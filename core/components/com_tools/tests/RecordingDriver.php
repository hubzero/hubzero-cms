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
class RecordingDriver extends \Hubzero\Database\Drivers\Mock\MockDriver
{
    /**
     * @var  array<mixed>
     */
    public $queries = [];

    /**
     * @var  array<mixed>
     */
    private $columns;

    /**
     * A Driver, so the tables take it. The parent's constructor is not
     * called: no database is opened, as the methods below answer everything
     * the tables ask.
     *
     * @param   array<mixed>  $columns
     * @return  void
     */
    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

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
     * @param   mixed  $as
     * @return  string
     */
    public function quoteName($name, $as = null)
    {
        return '`' . $name . '`';
    }

    /**
     * @param   mixed  $text
     * @param   bool   $escape
     * @return  string
     */
    public function quote($text, $escape = true)
    {
        return "'" . $text . "'";
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
     * @return  $this
     */
    public function execute()
    {
        return $this;
    }

    /**
     * @return  $this
     */
    public function query()
    {
        return $this;
    }
}
