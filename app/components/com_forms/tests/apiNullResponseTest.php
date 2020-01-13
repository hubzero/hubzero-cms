<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/apiNullResponse.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\ApiNullResponse;
use Components\Forms\Tests\Traits\canMock;

class ApiNullResponseTest extends Basic
{
	use canMock;

	public function testCanBeInstantiatedWithoutAnyArgs()
	{
		$response = new ApiNullResponse();

		$responseClass = get_class($response);

		$this->assertEquals('Components\Forms\Helpers\ApiNullResponse', $responseClass);
	}

	public function testArrayHasSuccessStatusIfResultSucceeded()
	{
		$result = $this->mock(
			['class' => 'Result', 'methods' => ['succeeded' => true]]
		);
		$response = new ApiNullResponse(['result' => $result]);

		$responseArray = $response->toArray();
		$status = $responseArray['status'];

		$this->assertEquals('success', $status);
	}

	public function testArrayHasSuccessStatusIfStatusSuccess()
	{
		$response = new ApiNullResponse(['status' => 'success']);

		$responseArray = $response->toArray();
		$status = $responseArray['status'];

		$this->assertEquals('success', $status);
	}

	public function testArrayHasErrorStatusIfReadResultFailed()
	{
		$result = $this->mock(
			['class' => 'Result', 'methods' => ['succeeded' => false]]
		);
		$response = new ApiNullResponse(['result' => $result]);

		$responseArray = $response->toArray();
		$status = $responseArray['status'];

		$this->assertEquals('error', $status);
	}

	public function testArrayHasErrorStatusIfNoResultOrStatus()
	{
		$response = new ApiNullResponse();

		$responseArray = $response->toArray();
		$status = $responseArray['status'];

		$this->assertEquals('error', $status);
	}

	public function testArrayHasSuccessMessageIfSucceeded()
	{
		$expectedMessage = 'test';
		$response = new ApiNullResponse([
			'status' => 'success',
			'success_message' => $expectedMessage
		]);

		$responseArray = $response->toArray();
		$actualMessage = $responseArray['message'];

		$this->assertEquals($expectedMessage, $actualMessage);
	}

	public function testArrayHasErrorMessageIfFailed()
	{
		$expectedMessage = 'test';
		$response = new ApiNullResponse(['error_message' => $expectedMessage]);

		$responseArray = $response->toArray();
		$actualMessage = $responseArray['message'];

		$this->assertEquals($expectedMessage, $actualMessage);
	}

}
