<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/criterion.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\Criterion;

class CriterionTest extends Basic
{

	public function testToArrayReturnsCorrectData()
	{
		$expectedState = [
			'name' => 'foo',
			'operator' => '>',
			'value' => '$',
		];
		$criterion = new Criterion($expectedState);

		$criterionAsArray = $criterion->toArray();

		$this->assertEquals($expectedState, $criterionAsArray);
	}

	public function testIsValidReturnsFalseIfNameNull()
	{
		$criterion = new Criterion([
			'operator' => 'operator',
			'value' => 'value'
		]);

		$isValid = $criterion->isValid();

		$this->assertEquals(false, $isValid);
	}

	public function testIsValidReturnsFalseIfOperatorEmpty()
	{
		$criterion = new Criterion([
			'name' => 'name',
			'operator' => '',
			'value' => 'value'
		]);

		$isValid = $criterion->isValid();

		$this->assertEquals(false, $isValid);
	}

	public function testIsValidReturnsFalseIfValueNull()
	{
		$criterion = new Criterion([
			'name' => 'name',
			'operator' => 'operator'
		]);

		$isValid = $criterion->isValid();

		$this->assertEquals(false, $isValid);
	}

	public function testConstructSetsName()
	{
		$name = 'foo';
		$criterion = new Criterion([
			'name' => $name
		]);

		$actualName = $criterion->getName();

		$this->assertEquals($name, $actualName);
	}

	public function testConstructSetsOperator()
	{
		$operator = '>';
		$criterion = new Criterion([
			'operator' => $operator
		]);

		$actualOperator = $criterion->getOperator();

		$this->assertEquals($operator, $actualOperator);
	}

	public function testConstructSetsValue()
	{
		$value = '$';
		$criterion = new Criterion([
			'value' => $value
		]);

		$actualValue = $criterion->getValue();

		$this->assertEquals($value, $actualValue);
	}

}
