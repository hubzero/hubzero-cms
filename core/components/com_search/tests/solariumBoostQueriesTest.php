<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests;

$componentPath = Component::path('com_search');

require_once "$componentPath/helpers/solariumBoostQueries.php";
require_once "$componentPath/tests/traits/canMock.php";

use Components\Search\Helpers\SolariumBoostQueries as Queries;
use Components\Search\Tests\Traits\canMock;
use Hubzero\Test\Basic;

class SolariumBoostQueriesTest extends Basic
{
	use canMock;

	public function testOneReturnsInstance()
	{
		$expected = new Queries(['boosts' => []]);

		$queries = Queries::one(['boosts' => []]);

		$this->assertEquals($expected, $queries);
	}

	public function testToArrayInvokesToArrayOnQueryInstances()
	{
		$boostMock1 = $this->mock([
			'class' => 'Boost'
		]);
		$boostMock2 = $this->mock([
			'class' => 'Boost'
		]);
		$queryMock1 = $this->mock([
			'class' => 'SolariumBoostQuery',
			'methods' => ['toArray']
		]);
		$queryMock2 = $this->mock([
			'class' => 'SolariumBoostQuery',
			'methods' => ['toArray']
		]);
		$queryFactoryMock = $this->getMockBuilder('SolariumBoostQuery')
			->setMethods(['one'])
			->getMock();

		$queries = new Queries([
			'boosts' => [$boostMock1, $boostMock2],
			'query' => $queryFactoryMock
		]);

		$queryFactoryMock->expects($this->exactly(2))
			->method('one')
			->will($this->onConsecutiveCalls($queryMock1, $queryMock2));

		$queryMock1->expects($this->once())
			->method('toArray');

		$queryMock2->expects($this->once())
			->method('toArray');

		$queries->toArray();
	}

}
