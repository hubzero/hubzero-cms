<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/csvHelper.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\CsvHelper;
use Components\Forms\Tests\Traits\canMock;

class CsvHelperTest extends Basic
{
	use canMock;

	public function testGenerateCsvGetsTemporaryPath()
	{
		$file = $this->mock([
			'class' => 'File', 'methods' => ['close', 'openForWriting', 'writeRow']
		]);
		$fileFactory = $this->mock([
			'class' => 'Files', 'methods' => ['create' => $file]
		]);
		$systemHelper = $this->mock(['class' => 'Config', 'methods' => ['get']]);
		$helper = new CsvHelper(['files' => $fileFactory, 'system' => $systemHelper]);
		$rows = $this->mock(['class' => 'CsvRows', 'methods' => ['getColumns']]);

		$systemHelper->expects($this->once())
			->method('get')
			->with('tmp_path');

		$helper->generateCsv('test', $rows);
	}

	public function testGenerateCsvCreatesFile()
	{
		$tempPath = '/tmp';
		$name = 'test';
		$expectedPath = "$tempPath/$name.csv";
		$file = $this->mock([
			'class' => 'File', 'methods' => ['close', 'openForWriting', 'writeRow']
		]);
		$fileFactory = $this->mock([
			'class' => 'Files', 'methods' => ['create' => $file]
		]);
		$systemHelper = $this->mock([
			'class' => 'Config', 'methods' => ['get' => $tempPath]
		]);
		$helper = new CsvHelper(['files' => $fileFactory, 'system' => $systemHelper]);
		$rows = $this->mock(['class' => 'CsvRows', 'methods' => ['getColumns']]);

		$fileFactory->expects($this->once())
			->method('create')
			->with($expectedPath);

		$helper->generateCsv($name, $rows);
	}

	public function testGenerateCsvOpensFile()
	{
		$file = $this->mock([
			'class' => 'File', 'methods' => ['close', 'openForWriting', 'writeRow']
		]);
		$fileFactory = $this->mock([
			'class' => 'Files', 'methods' => ['create' => $file]
		]);
		$helper = new CsvHelper(['files' => $fileFactory]);
		$rows = $this->mock(['class' => 'CsvRows', 'methods' => ['getColumns']]);

		$file->expects($this->once())
			->method('openForWriting');

		$helper->generateCsv('', $rows);
	}

	public function testGenerateCsvWritesColumnsToFile()
	{
		$columns = ['a', 'b', 'c'];
		$file = $this->mock([
			'class' => 'File', 'methods' => ['close', 'openForWriting', 'writeRow']
		]);
		$fileFactory = $this->mock([
			'class' => 'Files', 'methods' => ['create' => $file]
		]);
		$helper = new CsvHelper(['files' => $fileFactory]);
		$rows = $this->mock(
			['class' => 'CsvRows', 'methods' => ['getColumns' => $columns]
		]);

		$file->expects($this->once())
			->method('writeRow')
			->with($columns);

		$helper->generateCsv('', $rows);
	}

	public function testGenerateCsvClosesFile()
	{
		$file = $this->mock([
			'class' => 'File', 'methods' => ['close', 'openForWriting', 'writeRow']
		]);
		$fileFactory = $this->mock([
			'class' => 'Files', 'methods' => ['create' => $file]
		]);
		$helper = new CsvHelper(['files' => $fileFactory]);
		$rows = $this->mock(['class' => 'CsvRows', 'methods' => ['getColumns']]);

		$file->expects($this->once())
			->method('close');

		$helper->generateCsv('', $rows);
	}

}
