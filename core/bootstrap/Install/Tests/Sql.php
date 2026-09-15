<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Install\Tests;

/**
 * As much of a database as reading the install files calls for
 *
 * Enough to say what a hub holds after schema.sql, data.sql and starter.sql
 * have been through it: which tables exist and what their columns are, and
 * which rows are in them. It understands CREATE TABLE, INSERT, REPLACE and
 * TRUNCATE, because those are the four things the install files do.
 *
 * It is not a database and does not want to be. There is no query planner, no
 * foreign keys, no indexes and no types beyond "may this be null". What it is
 * for is answering questions about the shipped data without needing a server
 * to answer them, so the answers can be asserted in an ordinary test.
 **/
class Sql
{
    /**
     * Column names per table, in order
     *
     * @var  array
     */
    protected $columns = array();

    /**
     * Columns that may not be null, per table
     *
     * @var  array
     */
    protected $required = array();

    /**
     * Column sets each table says are unique
     *
     * @var  array
     */
    protected $uniques = array();

    /**
     * Rows per table
     *
     * @var  array
     */
    protected $rows = array();

    /**
     * Read a file into this
     *
     * @param   string  $path  The .sql file
     * @return  $this
     */
    public function read($path)
    {
        foreach (file($path, FILE_IGNORE_NEW_LINES) as $line) {
            $line = trim($line);

            if ($line === '' || substr($line, 0, 2) === '--' || substr($line, 0, 2) === '/*') {
                continue;
            }

            if (preg_match('/^CREATE TABLE `([^`]+)`/i', $line, $m)) {
                $this->table($m[1], $path, $line);
                continue;
            }

            if (preg_match('/^TRUNCATE `([^`]+)`/i', $line, $m)) {
                $this->rows[$this->name($m[1])] = array();
                continue;
            }

            if (preg_match('/^(INSERT|REPLACE)(?: IGNORE)? INTO `([^`]+)`\s*(\([^)]*\))?\s*VALUES\s*\((.*)\);$/i', $line, $m)) {
                $this->insert(
                    $this->name($m[2]),
                    $m[3],
                    $m[4],
                    strtoupper($m[1]) === 'REPLACE'
                );
            }
        }

        return $this;
    }

    /**
     * A CREATE TABLE, which the dump writes across many lines
     *
     * Only the first line arrives here, so the rest is read from the file
     * again from that point. Cheaper than holding a parser state machine for
     * the one statement in these files that is not on a single line.
     *
     * @param   string  $table  Its name
     * @param   string  $path   The file it is in
     * @param   string  $first  The CREATE TABLE line
     * @return  void
     */
    protected function table($table, $path, $first)
    {
        $table = $this->name($table);

        if (isset($this->columns[$table])) {
            return;
        }

        $body = $this->body($path, $first);

        $this->columns[$table] = array();
        $this->required[$table] = array();
        $this->uniques[$table] = array();

        foreach ($body as $line) {
            // A key the schema says is unique, so the rows can be held to it
            // A column in a key may carry a prefix length - `saAddress`(100) -
            // whose closing bracket is not the end of the list.
            if (preg_match('/^(?:UNIQUE KEY|PRIMARY KEY)\s*(?:`[^`]+`)?\s*\(((?:[^()]|\(\d+\))+)\)/i', trim($line), $k)) {
                $columns = array();

                foreach (explode(',', $k[1]) as $column) {
                    // An index may name a prefix length; the column is the name
                    $columns[] = trim(preg_replace('/\(\d+\)$/', '', trim($column)), '` ');
                }

                $this->uniques[$table][] = $columns;
                continue;
            }

            if (!preg_match('/^`([^`]+)`\s+(.+?),?$/', trim($line), $m)) {
                continue;
            }

            $this->columns[$table][] = $m[1];

            // NOT NULL is enough on its own. A DEFAULT is what a column falls
            // back on when a statement leaves it out, not permission to hand
            // it a NULL - and these files name every column of every row, so
            // nothing is ever left out for a default to cover.
            if (stripos($m[2], 'NOT NULL') !== false) {
                $this->required[$table][] = $m[1];
            }
        }

        $this->rows[$table] = isset($this->rows[$table]) ? $this->rows[$table] : array();
    }

    /**
     * The lines of a CREATE TABLE, up to its closing bracket
     *
     * @param   string  $path   The file
     * @param   string  $first  The line it starts on
     * @return  array
     */
    protected function body($path, $first)
    {
        static $files = array();

        if (!isset($files[$path])) {
            $files[$path] = file($path, FILE_IGNORE_NEW_LINES);
        }

        $at = array_search($first, $files[$path], true);

        if ($at === false) {
            return array();
        }

        $body = array();

        for ($i = $at + 1, $n = count($files[$path]); $i < $n; $i++) {
            if (substr(trim($files[$path][$i]), 0, 1) === ')') {
                break;
            }

            $body[] = $files[$path][$i];
        }

        return $body;
    }

    /**
     * One row
     *
     * @param   string  $table    Where it goes
     * @param   string  $named    The column list, if the statement gave one
     * @param   string  $payload  What is between the brackets
     * @return  void
     */
    protected function insert($table, $named, $payload, $replace = false)
    {
        $values = $this->values($payload);

        if ($named) {
            $columns = array();

            foreach (explode(',', trim($named, '() ')) as $column) {
                $columns[] = trim(trim($column), '` ');
            }
        } else {
            $columns = isset($this->columns[$table]) ? $this->columns[$table] : array();
        }

        $row = array('#columns' => count($columns), '#values' => count($values));

        foreach ($values as $i => $value) {
            if (isset($columns[$i])) {
                $row[$columns[$i]] = $value;
            }
        }

        // REPLACE puts a row where one with the same unique key was, rather
        // than beside it. The install files lean on that - starter.sql replaces
        // assets data.sql already wrote - so a reader that appends sees pairs
        // of rows that never exist together in a database.
        if ($replace) {
            $this->displace($table, $row);
        }

        $this->rows[$table][] = $row;
    }

    /**
     * Drop any row a REPLACE would have written over
     *
     * @param   string  $table  Where
     * @param   array   $row    What is arriving
     * @return  void
     */
    protected function displace($table, array $row)
    {
        $keys = isset($this->uniques[$table]) ? $this->uniques[$table] : array();

        if (!$keys || empty($this->rows[$table])) {
            return;
        }

        foreach ($this->rows[$table] as $i => $existing) {
            foreach ($keys as $key) {
                $same = true;

                foreach ($key as $column) {
                    if (!array_key_exists($column, $row) || !array_key_exists($column, $existing)
                        || (string) $row[$column] !== (string) $existing[$column]) {
                        $same = false;
                        break;
                    }
                }

                if ($same) {
                    unset($this->rows[$table][$i]);
                    break;
                }
            }
        }

        $this->rows[$table] = array_values($this->rows[$table]);
    }

    /**
     * Split a VALUES payload, respecting quotes and escapes
     *
     * @param   string  $payload  What is between the brackets
     * @return  array   Values, unquoted, with SQL NULL as PHP null
     */
    protected function values($payload)
    {
        $out   = array();
        $buf   = '';
        $in    = false;
        $depth = 0;

        for ($i = 0, $n = strlen($payload); $i < $n; $i++) {
            $c = $payload[$i];

            if ($in) {
                if ($c === '\\') {
                    $buf .= substr($payload, $i, 2);
                    $i++;
                    continue;
                }

                if ($c === "'") {
                    $in = false;
                }

                $buf .= $c;
                continue;
            }

            if ($c === "'") {
                $in = true;
                $buf .= $c;
                continue;
            }

            // A value can be an expression - the footer is written as a
            // CONCAT of several strings - and the commas inside one of those
            // separate its arguments, not the row's values.
            if ($c === '(') {
                $depth++;
            } elseif ($c === ')') {
                $depth--;
            }

            if ($c === ',' && $depth === 0) {
                $out[] = $this->value($buf);
                $buf = '';
                continue;
            }

            $buf .= $c;
        }

        $out[] = $this->value($buf);

        return $out;
    }

    /**
     * One value, as PHP sees it
     *
     * @param   string  $raw  As written
     * @return  string|null
     */
    protected function value($raw)
    {
        $raw = trim($raw);

        if (strcasecmp($raw, 'NULL') === 0) {
            return null;
        }

        if (strlen($raw) > 1 && $raw[0] === "'") {
            $raw = substr($raw, 1, -1);

            return strtr($raw, array(
                "\\'"  => "'",
                '\\"'  => '"',
                '\\r'  => "\r",
                '\\n'  => "\n",
                '\\\\' => '\\',
            ));
        }

        return $raw;
    }

    /**
     * The prefix these files write is not a prefix anyone installs with
     *
     * @param   string  $table  As written
     * @return  string
     */
    protected function name($table)
    {
        return preg_replace('/^#__/', '', $table);
    }

    /**
     * Every table the schema creates
     *
     * @return  array
     */
    public function tables()
    {
        return array_keys($this->columns);
    }

    /**
     * Whether the schema creates this table
     *
     * @param   string  $table  Its name
     * @return  bool
     */
    public function has($table)
    {
        return isset($this->columns[$this->name($table)]);
    }

    /**
     * A table's columns, in order
     *
     * @param   string  $table  Its name
     * @return  array
     */
    public function columns($table)
    {
        $table = $this->name($table);

        return isset($this->columns[$table]) ? $this->columns[$table] : array();
    }

    /**
     * A table's columns that may not be null
     *
     * @param   string  $table  Its name
     * @return  array
     */
    public function required($table)
    {
        $table = $this->name($table);

        return isset($this->required[$table]) ? $this->required[$table] : array();
    }

    /**
     * What a table holds
     *
     * @param   string  $table  Its name
     * @return  array
     */
    /**
     * The keys the schema declares unique for a table
     *
     * @param   string  $table  Which
     * @return  array   One array of column names per key
     */
    public function uniques($table)
    {
        $table = $this->name($table);

        return isset($this->uniques[$table]) ? $this->uniques[$table] : array();
    }

    /**
     * Every row written to a table
     *
     * @param   string  $table  Which
     * @return  array
     */
    public function rows($table)
    {
        $table = $this->name($table);

        return isset($this->rows[$table]) ? $this->rows[$table] : array();
    }

    /**
     * The rows of a table matching every given column
     *
     * @param   string  $table  Its name
     * @param   array   $where  Column => value
     * @return  array
     */
    public function where($table, array $where)
    {
        $found = array();

        foreach ($this->rows($table) as $row) {
            foreach ($where as $column => $value) {
                if (!array_key_exists($column, $row) || (string) $row[$column] !== (string) $value) {
                    continue 2;
                }
            }

            $found[] = $row;
        }

        return $found;
    }
}
