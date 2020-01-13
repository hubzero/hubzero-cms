<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/params.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\Params;
use Components\Forms\Tests\Traits\canMock;

class ParamsTest extends Basic
{
	use canMock;

	public function testGetArrayInvokesGetArrayWithGivenKey()
	{
		$key = 'test';
		$request = $this->mock([
			'class' => 'Request', 'methods' => ['getArray' => []]
		]);
		$params = new Params([
			'request' => $request,
			'whitelist' => []
		]);

		$request->expects($this->once())
			->method('getArray')
			->with($this->equalTo($key));

		$params->getArray($key);
	}

	public function testGetArrayFiltersKeysCorrectly()
	{
		$whitelist = ['a', 'b'];
		$requestArray = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];
		$request = $this->mock([
			'class' => 'Request', 'methods' => ['getArray' => $requestArray]
		]);
		$params = new Params([
			'request' => $request,
			'whitelist' => $whitelist
		]);

		$filteredParams = $params->getArray('');
		$filteredParamsKeys = array_keys($filteredParams);

		$this->assertEquals($whitelist, $filteredParamsKeys);
	}

	public function testGetInvokesGetWithGivenKey()
	{
		$key = 'test';
		$request = $this->mock(['class' => 'Request', 'methods' => ['get']]);
		$params = new Params([
			'request' => $request,
			'whitelist' => []
		]);

		$request->expects($this->once())
			->method('get')
			->with($this->equalTo($key));

		$params->get($key);
	}

	public function testGetInvokesGetReturnsGivenValue()
	{
		$key = 'test';
		$sentData = 'sent data';
		$request = $this->mock([
			'class' => 'Request', 'methods' => ['get' => $sentData]
		]);
		$params = new Params([
			'request' => $request,
			'whitelist' => []
		]);

		$param = $params->get($key);

		$this->assertEquals($sentData, $param);
	}

}
