<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/updateDelta.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\UpdateDelta;

class UpdateDeltaTest extends Basic
{

	public function testGetModelsToSaveReturnsEmptyArrayByDefault()
	{
		$updateDelta = new UpdateDelta();

		$modelsToSave = $updateDelta->getModelsToSave();

		$this->assertEquals([], $modelsToSave);
	}

	public function testGetModelsToSaveReturnsCorrectValues()
	{
		$expectedModels = ['a', 'b'];
		$updateDelta = new UpdateDelta([
			'save' => $expectedModels
		]);

		$modelsToSave = $updateDelta->getModelsToSave();

		$this->assertEquals($expectedModels, $modelsToSave);
	}

	public function testGetModelsToDestroyReturnsEmptyArrayByDefault()
	{
		$updateDelta = new UpdateDelta();

		$modelsToDestroy = $updateDelta->getModelsToDestroy();

		$this->assertEquals([], $modelsToDestroy);
	}

	public function testGetModelsToDestroyReturnsCorrectValues()
	{
		$expectedModels = ['a', 'b'];
		$updateDelta = new UpdateDelta([
			'destroy' => $expectedModels
		]);

		$modelsToDestroy = $updateDelta->getModelsToDestroy();

		$this->assertEquals($expectedModels, $modelsToDestroy);
	}

}
