<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/apiReadResponse.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\ApiReadResponse;
use Components\Forms\Tests\Traits\canMock;

class ApiReadResponseTest extends Basic
{
	use canMock;

	public function testArrayHasSuccessStatusIfReadResultSucceeded()
	{
		$result = $this->mock([
			'class' => 'ReadResult',
			'methods' => ['succeeded' => true, 'getData' => []]
		]);
		$response = new ApiReadResponse([
			'result' => $result, 'error_message' => '', 'success_message' => ''
		]);

		$responseArray = $response->toArray();
		$status = $responseArray['status'];

		$this->assertEquals('success', $status);
	}

	public function testArrayHasErrorStatusIfReadResultFailed()
	{
		$result = $this->mock([
			'class' => 'ReadResult',
			'methods' => ['succeeded' => false, 'getData']
		]);
		$response = new ApiReadResponse([
			'result' => $result, 'error_message' => '', 'success_message' => ''
		]);

		$responseArray = $response->toArray();
		$status = $responseArray['status'];

		$this->assertEquals('error', $status);
	}

	public function testArrayHasSuccessMessageIfReadResultSucceeded()
	{
		$result = $this->mock([
			'class' => 'ReadResult',
			'methods' => ['succeeded' => true, 'getData' => []]
		]);
		$responseArgs = [
			'result' => $result, 'error_message' => '', 'success_message' => 'test'
		];
		$response = new ApiReadResponse($responseArgs);

		$responseArray = $response->toArray();
		$message = $responseArray['message'];

		$this->assertEquals($responseArgs['success_message'], $message);
	}

	public function testArrayHasErrorMessageIfReadResultFailed()
	{
		$result = $this->mock([
			'class' => 'ReadResult',
			'methods' => ['succeeded' => false, 'getData' => []]
		]);
		$responseArgs = [
			'result' => $result, 'error_message' => 'test', 'success_message' => ''
		];
		$response = new ApiReadResponse($responseArgs);

		$responseArray = $response->toArray();
		$message = $responseArray['message'];

		$this->assertEquals($responseArgs['error_message'], $message);
	}

	public function testArrayHasCorrectAssociationData()
	{
		$modelA = $this->mock([
			'class' => 'Relational',
			'methods' => ['toArray' => [1, 'A']]
		]);
		$modelB = $this->mock([
			'class' => 'Relational',
			'methods' => ['toArray' => [1, 'A']]
		]);
		$result = $this->mock([
			'class' => 'ReadResult',
			'methods' => [
				'succeeded' => true,
			 	'getData' => [$modelA, $modelB]
			]
		]);
		$response = new ApiReadResponse([
			'result' => $result, 'error_message' => '', 'success_message' => ''
		]);

		$responseArray = $response->toArray();
		$actualAssociations = $responseArray['associations'];

		$expectedAssociations = [$modelA->toArray(), $modelB->toArray()];
		$this->assertEquals($expectedAssociations, $actualAssociations);
	}

}
