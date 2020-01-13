<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/factory.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\Factory;
use Components\Forms\Tests\Traits\canMock;

class FactoryTest extends Basic
{
	use canMock;

	public function testInstantiateInstantiatesModelOfGivenClass()
	{
		$instance = $this->mock([
			'class' => 'RelationalInstance', 'methods' => ['set']
		]);
		$modelClass = $this->mock([
			'class' => 'Relational', 'methods' => ['blank' => $instance]
		]);
		$factory = new Factory([
			'model_class' => $modelClass,
			'model_name' => ''
		]);

		$model = $factory->instantiate();

		$this->assertEquals($instance, $model);
	}

	public function testInstantiateManyReturnsEmptyArrayWhenGivenEmptyArray()
	{
		$factory = new Factory([
			'model_class' => '',
			'model_name' => ''
		]);

		$models = $factory->instantiateMany([]);

		$this->assertEquals([], $models);
	}

	public function testInstantiateManyReturnsModelInstancesWhenGivenData()
	{
		$instance = $this->mock([
			'class' => 'RelationalInstance', 'methods' => ['set']
		]);
		$modelClass = $this->mock([
			'class' => 'Relational', 'methods' => ['blank' => $instance]
		]);
		$factory = new Factory([
			'model_class' => $modelClass,
			'model_name' => ''
		]);

		$models = $factory->instantiateMany([[], [], []]);

		$this->assertEquals([$instance, $instance, $instance], $models);
	}

	public function testInstantiateReturnsModelInstance()
	{
		$instance = $this->mock([
			'class' => 'RelationalInstance', 'methods' => ['set']
		]);
		$modelClass = $this->mock([
			'class' => 'Relational', 'methods' => ['blank' => $instance]
		]);
		$factory = new Factory([
			'model_class' => $modelClass,
			'model_name' => ''
		]);

		$model = $factory->instantiate();

		$this->assertEquals($instance, $model);
	}

	public function testBatchUpdateInvokesBatchUpdateHelperUpdateDelta()
	{
		$updateDelta = $this->mock([
			'class' => 'UpdateDelta',
			'methods' => [
				'getModelsToDestroy' => [],
				'getModelsToSave' => []
			]
		]);
		$batchUpdateHelper =	$this->mock([
			'class' => 'BatchUpdateHelper',
		 	'methods' => ['updateDelta' => $updateDelta]
		]);
		$factory = new Factory([
			'batch_helper' => $batchUpdateHelper,
			'model_name' => ''
		]);

		$batchUpdateHelper->expects($this->once())
			->method('updateDelta');

		$factory->batchUpdate([], []);
	}


}
