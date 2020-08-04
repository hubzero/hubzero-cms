<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Import\Adapter\Excel;

use Iterator;
use stdClass;

/**
 *  Excel Reader Iterator Class implemeting interator
 */
class Reader implements Iterator
{
	/**
	 * File path
	 *
	 * @var  string
	 */
	private $file = '';

	/**
	 * Number of rows
	 *
	 * @var  integer
	 */
	private $rows = 0;

	/**
	 * Number of columns
	 *
	 * Excel uses alpha chars for column names, so this
	 * will be the name od the last column. e.g., "F"
	 *
	 * @var  string
	 */
	private $cols = 0;

	/**
	 * Current row position
	 * Starts at 1 as row 0
	 *
	 * @var  array
	 */
	private $sheet = array();

	/**
	 * Current row position
	 * Starts at 1 as row 0
	 *
	 * @var  array
	 */
	private $position = 0;

	/**
	 * Container for column headers
	 *
	 * @var  array
	 */
	private $headers = array();

	/**
	 * Constructor
	 *
	 * @param   string  $file File we want to use
	 * @param   string  $key  Not currently used
	 * @return  void
	 */
	public function __construct($file, $key='')
	{
		$this->position = 0;
		$this->file     = $file;

		try
		{
			$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file);
			$reader->setReadDataOnly(true);
			$spreadsheet = $reader->load($file);

			$sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

			$this->headers = array_shift($sheet);

			$this->sheet = $sheet;

			$this->rows = count($this->sheet);
			$this->cols = count($this->headers);
		}
		catch (\Exception $e)
		{
			die($e->getMessage());
		}
	}

	/**
	 * Get the record total
	 *
	 * @return  integer
	 */
	public function total()
	{
		return $this->rows;
	}

	/**
	 * Get the list of headers
	 *
	 * @return  array
	 */
	public function headers()
	{
		if (!$this->headers)
		{
			$this->headers = $this->sheet[1];
		}

		return $this->headers;
	}

	/**
	 * Get the current row
	 *
	 * @return  object  Row node as a stdClass
	 */
	public function current()
	{
		$headers = $this->headers();

		$result = new stdClass;

		$currentRow = $this->sheet[$this->position];

		// Excel documents have alphanumeric columns
		foreach ($headers as $col => $column)
		{
			$result->$column = (isset($currentRow[$col]) ? $currentRow[$col] : '');
		}

		return $result;
	}

	/**
	 * Get our current position while iterating
	 *
	 * @return  integer  Current position
	 */
	public function key()
	{
		return $this->position;
	}

	/**
	 * Go to the next Node that matches our key
	 *
	 * @return  void
	 */
	public function next()
	{
		++$this->position;
	}

	/**
	 * Move to the first node that matches our key
	 *
	 * @return  void
	 */
	public function rewind()
	{
		$this->position = 0;
	}

	/**
	 * Is our current node valid
	 *
	 * @return  boolean  Is valid?
	 */
	public function valid()
	{
		return ($this->position < $this->rows);
	}
}
