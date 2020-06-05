<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Helpers;

class SolariumBoostQuery
{
	protected $boost,
		$query;

	public function __construct($args = [])
	{
		$this->query = null;
		$this->boost = $args['boost'];
	}

	public function toArray()
	{
		$query = $this->getQuery();

		return ['query' => $query];
	}

	protected function getQuery()
	{
		if (!$this->query)
		{
			$this->query = $this->generateQuery();
		}

		return $this->query;
	}

	protected function generateQuery()
	{
		$field = $this->boost->getField();
		$fieldValue = $this->boost->getFieldValue();
		$strength = $this->boost->getStrength();

		return "$field:$fieldValue^$strength";
	}

	public static function one($args)
	{
		return new self($args);
	}

}
