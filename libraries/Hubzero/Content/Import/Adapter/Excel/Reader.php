<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Content\Import\Adapter\Excel;

use Iterator;
use stdClass;

if (file_exists(JPATH_LIBRARIES . DS . 'phpexcel' . DS . 'PHPExcel.php'))
{
	include_once(JPATH_LIBRARIES . DS . 'phpexcel' . DS . 'PHPExcel.php');
}

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