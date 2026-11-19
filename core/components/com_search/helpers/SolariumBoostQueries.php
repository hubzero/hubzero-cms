<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Helpers;

use Components\Search\Helpers\SolariumBoostQuery as Query;
use Hubzero\Utility\Arr;
use Component;

class SolariumBoostQueries
{
    /**
     * @var  mixed
     */
    protected $boosts;
    /**
     * @var  array<int, mixed>|null
     */
    protected $queries;
    /**
     * @var  mixed
     */
    protected $queryFactory;

    /**
     * @param   mixed  $args
     */
    public function __construct($args = [])
    {
        $this->boosts = $args['boosts'];
        $this->queries = null;
        $this->queryFactory = Arr::getValue(
            $args,
            'query',
            new MockProxy([
                'class' => 'Components\Search\Helpers\SolariumBoostQuery',
            ]),
        );
    }

    /**
     * @return  array<mixed>
     */
    public function toArray()
    {
        $this->setQueriesIfNeeded();

        $queriesAsArrays = array_map(function ($query) {
            return $query->toArray();
        }, $this->queries ?? []);

        return $queriesAsArrays;
    }

    /** @return void */
    protected function setQueriesIfNeeded()
    {
        if (!$this->queries) {
            $queries = $this->instantiateQueries();
            $this->setQueries($queries);
        }
    }

    /**
     * @return  array<int, mixed>
     */
    protected function instantiateQueries()
    {
        $queries = [];

        foreach ($this->boosts as $boost) {
            $query = $this->queryFactory->one(['boost' => $boost]);
            $queries[] = $query;
        }

        return $queries;
    }

    /**
     * @param   array<int, mixed>  $queries
     * @return void
     */
    protected function setQueries($queries)
    {
        $this->queries = $queries;
    }

    /**
     * @param   array<mixed>  $args
     * @return  \Components\Search\Helpers\SolariumBoostQueries
     */
    public static function one($args = [])
    {
        return new self($args);
    }
}
