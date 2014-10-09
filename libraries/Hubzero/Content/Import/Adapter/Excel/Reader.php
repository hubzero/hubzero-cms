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

/**
 *  Excel Reader Iterator Class implemeting interator
 */
class Reader implements Iterator
{
	private $file;

	private $rows;

	private $cols;

	private $sheet;

	private $position;

	private $headers;

	/**
	 * XML Reader Iterator Constructor
	 *
	 * @param string $file XML file we want to use
	 * @param string $key  XML node we are looking to iterate over
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

			$this->rows = $sheet->getHighestRow();
			$this->cols = $sheet->getHighestColumn();

			/*$records = array();
			for ($this->position <= $this->rows; ++$this->position)
			{
				$result = new stdClass;
				for ($col = 0; $col <= $this->cols; ++$col)
				{
					$result->$column = $this->sheet->getCellByColumnAndRow($col, $this->position)->getValue();
				}
				$records[] = $result;
			}*/
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
	 * Get the current XML node
	 *
	 * @return  object  XML node as a stdClass
	 */
	public function headers()
	{
		if (!$this->headers)
		{
			$this->headers = array();
			for ($col = 0; $col <= $this->cols; ++$col)
			{
				$this->headers[] = $this->sheet->getCellByColumnAndRow($col, 1)->getValue();
			}
		}

		return $this->headers;
	}

	/**
	 * Get the current XML node
	 *
	 * @return object XML node as a stdClass
	 */
	public function current()
	{
		$result = new stdClass;
		for ($col = 0; $col <= $this->cols; ++$col)
		{
			$column = $this->columns[$col];
			$result->$column = $this->sheet->getCellByColumnAndRow($col, $this->position)->getValue();
		}

		return $result;
	}

	/**
	 * Get our current position while iterating
	 *
	 * @return int Current position
	 */
	public function key()
	{
		return $this->position;
	}

	/**
	 * Go to the next Node that matches our key
	 *
	 * @return void
	 */
	public function next()
	{
		++$this->position;
	}

	/**
	 * Move to the first node that matches our key
	 * @return void
	 */
	public function rewind()
	{
		$this->position = 0;
	}

	/**
	 * Is our current node valid
	 *
	 * @return bool Is valid?
	 */
	public function valid()
	{
		return ($this->position <= $this->rows);
	}
}