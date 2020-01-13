<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

class CsvFile
{

	/**
	 * Constructs a File instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_stream = null;
		$this->_path = $args['path'];
	}

	/**
	 * Opens file with given mode
	 *
	 * @return   bool
	 */
	public function openForWriting()
	{
		if (!$this->_stream)
		{
			$this->_stream = fopen($this->_path, 'w');
		}

		return !!$this->_stream;
	}

	/**
	 * Appends given row data to file
	 *
	 * @param   array   $rowData    Row data
	 */
	public function writeRow($rowData)
	{
		if ($this->_stream)
		{
			fputcsv($this->_stream, $rowData);
		}
		else
		{
			throw new \Exception("$this->_path not open for writing");
		}
	}

	/**
	 * Closes file stream
	 *
	 * @return   bool
	 */
	public function close()
	{
		return fclose($this->_stream);
	}

	/**
	 * Returns path to file
	 *
	 * @return   string
	 */
	public function getPath()
	{
		return $this->_path;
	}

}
