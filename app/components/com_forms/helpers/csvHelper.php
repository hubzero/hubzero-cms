<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/csvFileFactory.php";
require_once "$componentPath/helpers/mockProxy.php";

use Components\Forms\Helpers\CsvFileFactory;
use Components\Forms\Helpers\MockProxy;
use Hubzero\Utility\Arr;

class CsvHelper
{

	/**
	 * Constructs a CsvHelper instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_fileFactory = Arr::getValue($args, 'files', new CsvFileFactory());
		$this->_systemHelper = Arr::getValue($args, 'system', new MockProxy(['class' => 'Config']));
	}

	/**
	 * Generates CSV file populated w/ data from provided rows
	 *
	 * @param    string   $name   File name
	 * @param    object   $rows   CSV-enabled rows
	 * @return   string
	 */
	public function generateCsv($name, $rows)
	{
		$file = $this->_createFile($name);

		$this->_writeToFile($file, $rows);

		return $file;
	}

	/**
	 * Creates a temporary file using given name
	 *
	 * @param    string    $name   File name
	 * @return   object
	 */
	protected function _createFile($name)
	{
		$path = $this->_getPath($name);

		$file = $this->_fileFactory->create($path, 'w');

		return $file;
	}

	/**
	 * Generates path to a temporary file using given name
	 *
	 * @param    string    $name   File name
	 * @return   string
	 */
	protected function _getPath($name)
	{
		$temporaryPath = $this->_systemHelper->get('tmp_path');

		return "$temporaryPath/$name.csv";
	}

	/**
	 * Writes data to CSV
	 *
	 * @param    object   $file   File to write to
	 * @param    object   $rows   CSV-enabled rows
	 * @return   void
	 */
	protected function _writeToFile($file, $rows)
	{
		$file->openForWriting();

		$this->_writeColumns($file, $rows);
		$this->_writeRows($file, $rows);

		$file->close();
	}

	/**
	 * Writes columns to CSV
	 *
	 * @param    object   $file   File to write to
	 * @param    object   $rows   CSV-enabled rows
	 * @return   void
	 */
	protected function _writeColumns($file, $rows)
	{
		$columns = $rows->getColumns();

		$file->writeRow($columns);
	}

	/**
	 * Writes rows' data to CSV
	 *
	 * @param    object   $file   File to write to
	 * @param    object   $rows   CSV-enabled rows
	 * @return   void
	 */
	protected function _writeRows($file, $rows)
	{
		foreach ($rows as $row)
		{
			$file->writeRow($row->toCsv());
		}
	}

}
