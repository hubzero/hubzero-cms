<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/criterion.php";
require_once "$componentPath/helpers/query.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Exception;
use Hubzero\Test\Basic;
use Components\Forms\Helpers\Criterion;
use Components\Forms\Helpers\Query;
use Components\Forms\Tests\Traits\canMock;

class QueryTest extends Basic
{
	use canMock;

	public function testSavedInvokesSetOnSession()
	{
		$session = $this->mock([
			'class' => 'Session', 'methods' => ['set']
		]);
		$query = new Query(['session' => $session]);

		$session->expects($this->once())
			->method('set');

		$query->save();
	}

	public function testLoadReturnsQueryInstance()
	{
		$session = $this->mock([
			'class' => 'Session', 'methods' => ['get' => []]
		]);

		$query = Query::load(['session' => $session]);
		$queryClass = get_class($query);

		$this->assertEquals('Components\Forms\Helpers\Query', $queryClass);
	}

	public function testToArray()
	{
		$testCriteria = [
			'name' => new Criterion([
				'name' => 'name',
				'operator' => '=',
				'value' => 'a'
			]),
			'number' => new Criterion([
				'name' => 'number',
				'operator' => '>',
				'value' => 3
			])
		];
		$query = new Query();

		foreach ($testCriteria as $criterion)
		{
			$query->set($criterion->getName(), $criterion->toArray());
		}

		$queryArray = $query->toArray();

		$this->assertEquals($testCriteria, $queryArray);
	}

	public function testSetAssociative()
	{
		$criteriaData = [
			'name' => [
				'name' => 'name',
				'operator' => '=',
				'value' => 'a'
			],
			'number' =>	[
				'name' => 'number',
				'operator' => '>',
				'value' => 3
			]
		];
		$expectedCriteria = array_map(function($criterionData) use($criteriaData) {
			return new Criterion($criterionData);
		}, $criteriaData);
		$query = new Query();

		$query->setAssociative($criteriaData);
		$queryArray = $query->toArray();

		$this->assertEquals($expectedCriteria, $queryArray);
	}

	public function testGetReturnsCriterion()
	{
		$query = new Query();
		$key = 'a';
		$criterionData = [
			'operator' => '<=',
			'value' => 49,
		];
		$expectedValue = new Criterion(
			array_merge(['name' => $key], $criterionData)
		);

		$query->set($key, $criterionData);
		$actualValue = $query->get('a');

		$this->assertEquals($expectedValue, $actualValue);
	}

	public function testGetReturnsCriterionIfKeyMissing()
	{
		$query = new Query();

		$getResult = $query->get('missing');
		$getResultClass = get_class($getResult);

		$this->assertEquals('Components\Forms\Helpers\Criterion', $getResultClass);
	}

	public function testGetValueReturnsCorrectValue()
	{
		$expectedValue = 'test';
		$key = 'a';
		$query = new Query();
		$criterionData = [
			'operator' => '',
			'value' => $expectedValue
		];

		$query->set($key, $criterionData);
		$actualValue = $query->getValue($key);

		$this->assertEquals($expectedValue, $actualValue);
	}

	public function testGetValueReturnsNullIfAttributeAbsent()
	{
		$query = new Query();

		$actualValue = $query->getValue('absent');

		$this->assertEquals(null, $actualValue);
	}


	public function testConstructSetsDefaultName()
	{
		$query = new Query();

		$name = $query->name;

		$this->assertEquals(Query::$defaultName, $name);
	}

	public function testConstructSetsGivenName()
	{
		$state = ['name' => 'test'];
		$query = new Query($state);

		$name = $query->name;

		$this->assertEquals($state['name'], $name);
	}

	public function testConstructSetsDefaultNamespace()
	{
		$query = new Query();

		$namespace = $query->namespace;

		$this->assertEquals(Query::$defaultNamespace, $namespace);
	}

	public function testConstructSetsGivenNamespace()
	{
		$state = ['namespace' => 'test'];
		$query = new Query($state);

		$namespace = $query->namespace;

		$this->assertEquals($state['namespace'], $namespace);
	}

	public function testNewQueryReturnsEmptyErrorsArray()
	{
		$query = new Query();

		$errors = $query->getErrors();

		$this->assertEquals([], $errors);
	}

}
