<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/crudBatch.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\CrudBatch;

class CrudBatchTest extends Basic
{

	public function testSucceededReturnsTrueIfNoFailedSaves()
	{
		$crudBatch = new CrudBatch();

		$succeeded = $crudBatch->succeeded();

		$this->assertEquals(true, $succeeded);
	}

	public function testSucceededReturnsFalseIfFailedSave()
	{
		$crudBatch = new CrudBatch();

		$crudBatch->addFailedSave([]);
		$succeeded = $crudBatch->succeeded();

		$this->assertEquals(false, $succeeded);
	}

	public function testSucceededReturnsTrueIfNoFailedDestroys()
	{
		$crudBatch = new CrudBatch();

		$succeeded = $crudBatch->succeeded();

		$this->assertEquals(true, $succeeded);
	}

	public function testSucceededReturnsFalseIfFailedDestroy()
	{
		$crudBatch = new CrudBatch();

		$crudBatch->addFailedDestroy([]);
		$succeeded = $crudBatch->succeeded();

		$this->assertEquals(false, $succeeded);
	}

	public function testNewBatchGetFailedSavesReturnsEmptyArray()
	{
		$crudBatch = new CrudBatch();

		$failedSaves = $crudBatch->getFailedSaves();

		$this->assertEquals([], $failedSaves);
	}

	public function testBatchGetFailedSavesReturnsFailedSaves()
	{
		$crudBatch = new CrudBatch();
		$crudBatch->addFailedSave(1);
		$crudBatch->addFailedSave(2);

		$failedSaves = $crudBatch->getFailedSaves();

		$this->assertEquals([1, 2], $failedSaves);
	}

	public function testNewBatchGetFailedDestroysReturnsEmptyArray()
	{
		$crudBatch = new CrudBatch();

		$failedDestroys = $crudBatch->getFailedDestroys();

		$this->assertEquals([], $failedDestroys);
	}

	public function testBatchGetFailedDestroysReturnsFailedDestroys()
	{
		$crudBatch = new CrudBatch();
		$crudBatch->addFailedDestroy(1);
		$crudBatch->addFailedDestroy(2);

		$failedDestroys = $crudBatch->getFailedDestroys();

		$this->assertEquals([1, 2], $failedDestroys);
	}

	public function testNewBatchGetSuccessfulSavesReturnsEmptyArray()
	{
		$crudBatch = new CrudBatch();

		$successfulSaves = $crudBatch->getSuccessfulSaves();

		$this->assertEquals([], $successfulSaves);
	}

	public function testBatchGetSuccessfulSavesReturnsSuccessfulSaves()
	{
		$crudBatch = new CrudBatch();
		$crudBatch->addSuccessfulSave(1);
		$crudBatch->addSuccessfulSave(2);

		$successfulSaves = $crudBatch->getSuccessfulSaves();

		$this->assertEquals([1, 2], $successfulSaves);
	}

}
