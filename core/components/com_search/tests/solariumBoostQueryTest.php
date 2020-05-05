<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests;

$componentPath = Component::path('com_search');

require_once "$componentPath/helpers/solariumBoostQuery.php";
require_once "$componentPath/tests/traits/canMock.php";

use Components\Search\Helpers\SolariumBoostQuery as Query;
use Components\Search\Tests\Traits\canMock;
use Hubzero\Test\Basic;

class SolariumBoostQueryTest extends Basic
{
	use canMock;

	public function testOneReturnsInstance()
	{
		$expected = new Query(['boost' => []]);

		$queries = Query::one(['boost' => []]);

		$this->assertEquals($expected, $queries);
	}

	public function testToArray()
	{
		$boostMock = $this->mock([
			'class' => 'Boost',
			'methods' => [
				'getField' => 'resource',
				'getFieldValue' => 'Tools',
				'getStrength' => 83
			]
		]);
		$expected = ['query' => 'resource:Tools^83'];
		$query = new Query(['boost' => $boostMock]);

		$queryAsArray = $query->toArray();

		$this->assertEquals($expected, $queryAsArray);
	}

}
