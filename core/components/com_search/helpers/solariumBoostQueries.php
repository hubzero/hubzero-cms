<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Helpers;

$componentPath = Component::path('com_search');

require_once "$componentPath/helpers/solariumBoostQuery.php";

use Components\Search\Helpers\SolariumBoostQuery as Query;
use Hubzero\Utility\Arr;

class SolariumBoostQueries
{
	protected $boosts,
		$queries,
		$queryFactory;

	public function __construct($args = [])
	{
		$this->boosts = $args['boosts'];
		$this->queries = null;
		$this->queryFactory = Arr::getValue(
			$args, 'query', new MockProxy([
				'class' => 'Components\Search\Helpers\SolariumBoostQuery'
			])
		);
	}

	public function toArray()
	{
		$this->setQueriesIfNeeded();

		$queriesAsArrays = array_map(function($query) {
			return $query->toArray();
		}, $this->queries);

		return $queriesAsArrays;
	}

	protected function setQueriesIfNeeded()
	{
		if (!$this->queries)
		{
			$queries = $this->instantiateQueries();
			$this->setQueries($queries);
		}
	}

	protected function instantiateQueries()
	{
		$queries = [];

		foreach ($this->boosts as $boost)
		{
			$query = $this->queryFactory->one(['boost' => $boost]);
			$queries[] = $query;
		}

		return $queries;
	}

	protected function setQueries($queries)
	{
		$this->queries = $queries;
	}

	public static function one($args = [])
	{
		return new self($args);
	}

}
