<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/batchUpdateHelper.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\BatchUpdateHelper;
use Components\Forms\Tests\Traits\canMock;

class BatchUpdateHelperTest extends Basic
{
	use canMock;

	public function testUpdateDeltaReturnsUpdateDeltaWithCorrectModelsToBeSaved()
	{
		$submittedModels = [
			$this->mock(['class' => 'Relational', 'methods' => ['get' => 2]]),
			$this->mock(['class' => 'Relational', 'methods' => ['get' => 3]])
		];
		$batchUpdateHelper = new BatchUpdateHelper();

		$updateDelta = $batchUpdateHelper->updateDelta([], $submittedModels);
		$modelsToSave = $updateDelta->getModelsToSave();

		$this->assertEquals($submittedModels, $modelsToSave);
	}

	public function testUpdateDeltaReturnsUpdateDeltaWithCorrectModelsToBeDestroyed()
	{
		$model1 =	$this->mock(['class' => 'Relational', 'methods' => ['get' => 1]]);
		$model2 =	$this->mock(['class' => 'Relational', 'methods' => ['get' => 2]]);
		$model3 = $this->mock(['class' => 'Relational', 'methods' => ['get' => 3]]);
		$currentModels = [
			$model1,
			$model2,
			$model3
		];
		$submittedModels = [
			$model2
		];
		$batchUpdateHelper = new BatchUpdateHelper();

		$updateDelta = $batchUpdateHelper->updateDelta($currentModels, $submittedModels);
		$modelsToDestroy = $updateDelta->getModelsToDestroy();

		$this->assertEquals([$model1, $model3], $modelsToDestroy);
	}

}
