<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/models/formResponse.php";

use Hubzero\Test\Basic;
use Components\Forms\Models\FormResponse;

class FormResponseTest extends Basic
{

	public function testInitiateHasCreated()
	{
		$response = FormResponse::blank();

		$initiate = $response->initiate;
		$hasCreated = in_array('created', $initiate);

		$this->assertEquals(true, $hasCreated);
	}

	public function testRulesRequireFormId()
	{
		$response = FormResponse::blank();

		$validation = $response->rules['form_id'];

		$this->assertEquals('notempty', $validation);
	}

	public function testRulesRequireUserId()
	{
		$response = FormResponse::blank();

		$validation = $response->rules['user_id'];

		$this->assertEquals('notempty', $validation);
	}

}
