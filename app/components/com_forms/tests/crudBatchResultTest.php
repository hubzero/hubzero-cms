<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/crudBatchResult.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\CrudBatchResult;
use Components\Forms\Tests\Traits\canMock;

class CrudBatchResultTest extends Basic
{
	use canMock;

	public function testSucceededReturnsTrueIfAllBatchesSucceeded()
	{
		$successfulBatchArgs = [
			'class' => 'CrudBatch', 'methods' => ['succeeded' => true]
		];
		$batchA = $this->mock($successfulBatchArgs);
		$batchB = $this->mock($successfulBatchArgs);
		$batches = [$batchA, $batchB];
		$crudBatchResult = new CrudBatchResult(['batches' => $batches]);

		$succeeded = $crudBatchResult->succeeded();

		$this->assertEquals(true, $succeeded);
	}

	public function testSucceededReturnsTrueIfAnyBatchesFailed()
	{
		$batchA = $this->mock([
			'class' => 'CrudBatch', 'methods' => ['succeeded' => true]
		]);
		$batchB = $this->mock([
			'class' => 'CrudBatch', 'methods' => ['succeeded' => false]
		]);
		$batches = [$batchA, $batchB];
		$crudBatchResult = new CrudBatchResult(['batches' => $batches]);

		$succeeded = $crudBatchResult->succeeded();

		$this->assertEquals(false, $succeeded);
	}

	public function testGetFailedSavesReturnsAllBatchesFailedSaves()
	{
		$batchA = $this->mock([
			'class' => 'CrudBatch', 'methods' => ['getFailedSaves' => []]
		]);
		$batchB = $this->mock([
			'class' => 'CrudBatch', 'methods' => ['getFailedSaves' => [1, 2]]
		]);
		$batchC = $this->mock([
			'class' => 'CrudBatch', 'methods' => ['getFailedSaves' => [3, 4]]
		]);
		$batches = [$batchA, $batchB, $batchC];
		$crudBatchResult = new CrudBatchResult(['batches' => $batches]);

		$failedSaves = $crudBatchResult->getFailedSaves();

		$this->assertEquals([1,2, 3, 4], $failedSaves);
	}

	public function testGetFailedDestorysReturnsAllBatchesFailedDestroys()
	{
		$batchA = $this->mock([
			'class' => 'CrudBatch', 'methods' => ['getFailedDestroys' => []]
		]);
		$batchB = $this->mock([
			'class' => 'CrudBatch', 'methods' => ['getFailedDestroys' => [1, 2]]
		]);
		$batchC = $this->mock([
			'class' => 'CrudBatch', 'methods' => ['getFailedDestroys' => [3, 4]]
		]);
		$batches = [$batchA, $batchB, $batchC];
		$crudBatchResult = new CrudBatchResult(['batches' => $batches]);

		$failedDesroys = $crudBatchResult->getFailedDestroys();

		$this->assertEquals([1,2, 3, 4], $failedDesroys);
	}

	public function testGetErrorsReturnsCorrectErrors()
	{
		$recordA = $this->mock([
			'class' => 'Relational',
			'methods' => ['get' => 1, 'getErrors' => ['a']]
		]);
		$recordB = $this->mock([
			'class' => 'Relational',
			'methods' => ['get' => 2, 'getErrors' => ['b']]
		]);
		$batch = $this->mock([
			'class' => 'CrudBatch',
			'methods' => ['getFailedSaves' => [$recordA, $recordB]]
		]);
		$result = new CrudBatchResult(['batches' => [$batch]]);

		$errors = $result->getErrors();

		$this->assertEquals(['Record 1' => ['a'], 'Record 2' => ['b']], $errors);
	}

	public function testGetSuccessfulSavesReturnsAllBatchesFailedSaves()
	{
		$batchA = $this->mock([
			'class' => 'CrudBatch', 'methods' => ['getSuccessfulSaves' => [1]]
		]);
		$batchB = $this->mock([
			'class' => 'CrudBatch', 'methods' => ['getSuccessfulSaves' => [2]]
		]);
		$batchC = $this->mock([
			'class' => 'CrudBatch', 'methods' => ['getSuccessfulSaves' => [3, 4]]
		]);
		$batches = [$batchA, $batchB, $batchC];
		$crudBatchResult = new CrudBatchResult(['batches' => $batches]);

		$successfulSaves = $crudBatchResult->getSuccessfulSaves();

		$this->assertEquals([1,2, 3, 4], $successfulSaves);
	}

}
