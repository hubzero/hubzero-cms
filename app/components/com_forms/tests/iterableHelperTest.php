<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/iterableHelper.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\IterableHelper;
use Components\Forms\Tests\Traits\canMock;

class IterableHelperTest extends Basic
{
	use canMock;

	public function testFunctionMapReturnsEmptyArrayWhenGivenEmptyArray()
	{
		$iterableHelper = new IterableHelper();

		$map = $iterableHelper->functionMap([], '');

		$this->assertEquals([], $map);
	}

	public function testFunctionMapReturnsCorrectMap()
	{
		$expectedMap = [99, 'foo'];
		$objectA = $this->mock([
			'class' => 'Object', 'methods' => ['test' => $expectedMap[0]]
		]);
		$objectB = $this->mock([
			'class' => 'Object', 'methods' => ['test' => $expectedMap[1]]
		]);
		$objects = [$objectA, $objectB];
		$iterableHelper = new IterableHelper();

		$map = $iterableHelper->functionMap($objects, 'test');

		$this->assertEquals($expectedMap, $map);
	}

}
