<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Test\Database;

/**
 * Represents a database table for test comparisons
 *
 * Lightweight replacement for PHPUnit\DbUnit\DataSet\ITable
 */
class Table
{
    /**
     * The table name
     *
     * @var string
     */
    protected $tableName;

    /**
     * Column names
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Row data
     *
     * @var array
     */
    protected $rows = [];

    /**
     * Create a new table instance
     *
     * @param  string  $tableName
     * @param  array   $columns
     * @param  array   $rows
     */
    public function __construct(string $tableName, array $columns = [], array $rows = [])
    {
        $this->tableName = $tableName;
        $this->columns = $columns;
        $this->rows = $rows;
    }

    /**
     * Get the table name
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * Get column names
     *
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Get the number of rows
     *
     * @return int
     */
    public function getRowCount(): int
    {
        return count($this->rows);
    }

    /**
     * Get a specific row
     *
     * @param  int  $rowIndex
     * @return array|null
     */
    public function getRow(int $rowIndex): ?array
    {
        return $this->rows[$rowIndex] ?? null;
    }

    /**
     * Get all rows
     *
     * @return array
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * Get a specific value
     *
     * @param  int     $rowIndex
     * @param  string  $column
     * @return mixed
     */
    public function getValue(int $rowIndex, string $column)
    {
        return $this->rows[$rowIndex][$column] ?? null;
    }

    /**
     * Add a row to the table
     *
     * @param  array  $row
     * @return void
     */
    public function addRow(array $row): void
    {
        $this->rows[] = $row;

        // Update columns if new ones are found
        foreach (array_keys($row) as $column) {
            if (!in_array($column, $this->columns)) {
                $this->columns[] = $column;
            }
        }
    }

    /**
     * Set columns
     *
     * @param  array  $columns
     * @return void
     */
    public function setColumns(array $columns): void
    {
        $this->columns = $columns;
    }

    /**
     * Compare this table with another table
     *
     * @param  Table  $other
     * @return bool
     */
    public function matches(Table $other): bool
    {
        if ($this->getRowCount() !== $other->getRowCount()) {
            return false;
        }

        $thisRows = $this->getNormalizedRows();
        $otherRows = $other->getNormalizedRows();

        return $thisRows == $otherRows;
    }

    /**
     * Get rows normalized for comparison
     *
     * @return array
     */
    public function getNormalizedRows(): array
    {
        $rows = [];

        foreach ($this->rows as $row) {
            $normalized = [];
            foreach ($row as $key => $value) {
                // Convert to string for comparison (like DBUnit does)
                $normalized[$key] = $value === null ? null : (string) $value;
            }
            ksort($normalized);
            $rows[] = $normalized;
        }

        // Sort rows for consistent comparison
        usort($rows, function ($a, $b) {
            return $a <=> $b;
        });

        return $rows;
    }

    /**
     * Get the difference between tables for error messages
     *
     * @param  Table  $other
     * @return string
     */
    public function getDifference(Table $other): string
    {
        $diff = [];

        if ($this->getRowCount() !== $other->getRowCount()) {
            $diff[] = sprintf(
                "Row count mismatch: expected %d, actual %d",
                $this->getRowCount(),
                $other->getRowCount()
            );
        }

        $thisRows = $this->getNormalizedRows();
        $otherRows = $other->getNormalizedRows();

        $maxRows = max(count($thisRows), count($otherRows));

        for ($i = 0; $i < $maxRows; $i++) {
            $expected = $thisRows[$i] ?? '(missing)';
            $actual = $otherRows[$i] ?? '(missing)';

            if ($expected != $actual) {
                $diff[] = sprintf(
                    "Row %d differs:\n  Expected: %s\n  Actual:   %s",
                    $i,
                    is_array($expected) ? json_encode($expected) : $expected,
                    is_array($actual) ? json_encode($actual) : $actual
                );
            }
        }

        return implode("\n", $diff);
    }
}
