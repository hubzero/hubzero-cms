<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/apiResponseFactory.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\ApiResponseFactory;

class ApiResponseFactoryTest extends Basic
{

	public function testOneReturnsApiUpdateResponseWhenUpdateOperation()
	{
		$factory = new ApiResponseFactory();

		$response = $factory->one([
			'operation' => 'batchUpdate',
			'result' => [], 'error_message' => '', 'success_message' => ''
		]);
		$responseClass = get_class($response);

		$this->assertEquals('Components\Forms\Helpers\ApiBatchUpdateResponse', $responseClass);
	}

	public function testOneReturnsApiReadResponseWhenReadOperation()
	{
		$factory = new ApiResponseFactory();

		$response = $factory->one([
			'operation' => 'read',
			'result' => [], 'error_message' => '', 'success_message' => ''
		]);
		$responseClass = get_class($response);

		$this->assertEquals('Components\Forms\Helpers\ApiReadResponse', $responseClass);
	}

	public function testOneReturnsApiNullResponseByDefault()
	{
		$factory = new ApiResponseFactory();

		$response = $factory->one([
			'operation' => 'null',
			'result' => [], 'error_message' => '', 'success_message' => ''
		]);
		$responseClass = get_class($response);

		$this->assertEquals('Components\Forms\Helpers\ApiNullResponse', $responseClass);
	}

}
