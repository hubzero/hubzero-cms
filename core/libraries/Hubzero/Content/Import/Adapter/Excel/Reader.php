<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
	private $file;

	/**
	 * Number of rows
	 *
	 * @var  integer
	 */
	private $rows;

	/**
	 * Number of columns
	 *
	 * Excel uses alpha chars for column names, so this
	 * will be the name od the last column. e.g., "F"
	 *
	 * @var  string
	 */
	private $cols;

	/**
	 * Current row position
	 * Starts at 1 as row 0
	 *
	 * @var  array
	 */
	private $sheet;

	/**
	 * Current row position
	 * Starts at 1 as row 0
	 *
	 * @var  array
	 */
	private $position;

	/**
	 * Container for column headers
	 *
	 * @var  array
	 */
	private $headers;

	/**
	 * Constructor
	 *
	 * @param   string  $file File we want to use
	 * @param   string  $key  Not currently used
	 * @return  void
	 */
	public function __construct($file, $key='')
	{
		$this->position = 1;
		$this->file     = $file;

		try
		{
			$inputFileType = \PHPExcel_IOFactory::identify($file);
			$objReader = \PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($file);

			$this->sheet = $objPHPExcel->getSheet(0);

			$this->rows = $this->sheet->getHighestRow();
			$this->cols = $this->sheet->getHighestColumn();
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
			$this->headers = array();

			// Excel documents have alphanumeric columns
			for ($col = 'A'; $col <= $this->cols; $col++)
			{
				$this->headers[$col] = $this->sheet->getCell($col . '1')->getValue();
			}
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
		// We don't want to count the headings row
		if ($this->position == 1)
		{
			return null;
		}

		$headers = $this->headers();

		$result = new stdClass;

		// Excel documents have alphanumeric columns
		for ($col = 'A'; $col <= $this->cols; $col++)
		{
			$column = $headers[$col];
			$result->$column = $this->sheet->getCell($col . $this->position)->getValue();
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
		$this->position = 1;
	}

	/**
	 * Is our current node valid
	 *
	 * @return  boolean  Is valid?
	 */
	public function valid()
	{
		return ($this->position <= $this->rows);
	}
}