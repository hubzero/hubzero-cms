<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/mockProxy.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;

use Components\Forms\Helpers\MockProxy;
use Components\Forms\Tests\Traits\canMock;

class MockProxyTest extends Basic
{
	use canMock;

	public function testCallForwardsInvocationToWrappedClass()
	{
		$this->markTestSkipped('mocking static functions currently unavailable');

		$testFunctionName = 'testFunc';
		$wrappedClass = $this->mock([
			'class' => 'Test', 'methods' => [$testFunctionName]
		]);
		$mockProxy = new MockProxy([
			'class' => $wrappedClass
		]);

		$request->expects($this->once())
			->method($testFunctionName);

		$mockProxy->$testFunctionName();
	}

}
