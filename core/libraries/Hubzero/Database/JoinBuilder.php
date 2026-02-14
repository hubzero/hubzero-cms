<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

use InvalidArgumentException;

/**
 * Fluent join predicate builder.
 */
class JoinBuilder
{
    /**
     * @var  Query|null
     */
    protected $query;

    /**
     * @var  string|null
     */
    protected $table;

    /**
     * @var  string
     */
    protected $type;

    /**
     * @var  array
     */
    protected $conditions = [];

    /**
     * @var  bool
     */
    protected $collectorOnly;

    /**
     * @param  Query|null  $query
     * @param  string|null $table
     * @param  string      $type
     * @param  bool        $collectorOnly
     */
    public function __construct(?Query $query, ?string $table, string $type = 'inner', bool $collectorOnly = false)
    {
        $this->query = $query;
        $this->table = $table;
        $this->type = $type;
        $this->collectorOnly = $collectorOnly;
    }

    /**
     * Add an ON predicate comparing two columns/expressions.
     *
     * @param  mixed       $left
     * @param  string|null $operator
     * @param  mixed|null  $right
     * @return $this
     */
    public function on($left, $operator = null, $right = null)
    {
        return $this->addColumnCondition($left, $operator, $right, 'and');
    }

    /**
     * Add an AND ON predicate.
     *
     * @param  mixed       $left
     * @param  string|null $operator
     * @param  mixed|null  $right
     * @return $this
     */
    public function and($left, $operator = null, $right = null)
    {
        return $this->addColumnCondition($left, $operator, $right, 'and');
    }

    /**
     * Add an OR ON predicate.
     *
     * @param  mixed       $left
     * @param  string|null $operator
     * @param  mixed|null  $right
     * @return $this
     */
    public function or($left, $operator = null, $right = null)
    {
        return $this->addColumnCondition($left, $operator, $right, 'or');
    }

    /**
     * Add a join predicate comparing a column to a literal value.
     *
     * @param  mixed       $left
     * @param  string|null $operator
     * @param  mixed|null  $value
     * @return $this
     */
    public function where($left, $operator = null, $value = null)
    {
        return $this->addValueCondition($left, $operator, $value, 'and');
    }

    /**
     * Add an AND join predicate comparing a column to a literal value.
     *
     * @param  mixed       $left
     * @param  string|null $operator
     * @param  mixed|null  $value
     * @return $this
     */
    public function andWhere($left, $operator = null, $value = null)
    {
        return $this->addValueCondition($left, $operator, $value, 'and');
    }

    /**
     * Add an OR join predicate comparing a column to a literal value.
     *
     * @param  mixed       $left
     * @param  string|null $operator
     * @param  mixed|null  $value
     * @return $this
     */
    public function orWhere($left, $operator = null, $value = null)
    {
        return $this->addValueCondition($left, $operator, $value, 'or');
    }

    /**
     * Add a grouped set of join predicates.
     *
     * @param  callable  $callback
     * @param  string    $boolean
     * @return $this
     */
    public function group(callable $callback, string $boolean = 'and')
    {
        $nested = new self(null, null, $this->type, true);
        $callback($nested);
        $conditions = $nested->getConditions();

        if (empty($conditions)) {
            throw new InvalidArgumentException('Join condition groups cannot be empty.');
        }

        $this->conditions[] = [
            'group' => $conditions,
            'boolean' => strtolower($boolean),
        ];

        return $this;
    }

    /**
     * Finalize this join and return the parent query.
     *
     * @return Query
     */
    public function end()
    {
        if ($this->collectorOnly || !$this->query) {
            throw new InvalidArgumentException('JoinBuilder cannot be finalized without a parent query.');
        }

        if (empty($this->conditions)) {
            throw new InvalidArgumentException('JoinBuilder requires at least one join predicate.');
        }

        $this->query->joinOn($this->table, $this->conditions, $this->type);
        return $this->query;
    }

    /**
     * Alias for end().
     *
     * @return Query
     */
    public function done()
    {
        return $this->end();
    }

    /**
     * @return array
     */
    protected function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * @param  mixed       $left
     * @param  string|null $operator
     * @param  mixed|null  $right
     * @param  string      $boolean
     * @return $this
     */
    protected function addColumnCondition($left, $operator, $right, string $boolean)
    {
        if ($right === null && $operator !== null) {
            $right = $operator;
            $operator = '=';
        }

        if ($operator === null || $right === null) {
            throw new InvalidArgumentException('Join ON predicates require left and right operands.');
        }

        $this->conditions[] = [
            'left' => $left,
            'operator' => $operator,
            'right' => $right,
            'boolean' => strtolower($boolean),
        ];

        return $this;
    }

    /**
     * @param  mixed       $left
     * @param  string|null $operator
     * @param  mixed|null  $value
     * @param  string      $boolean
     * @return $this
     */
    protected function addValueCondition($left, $operator, $value, string $boolean)
    {
        if ($value === null && $operator !== null) {
            $value = $operator;
            $operator = '=';
        }

        if ($operator === null) {
            throw new InvalidArgumentException('Join WHERE predicates require an operator.');
        }

        $this->conditions[] = [
            'left' => $left,
            'operator' => $operator,
            'value' => $value,
            'boolean' => strtolower($boolean),
        ];

        return $this;
    }
}
