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

namespace Hubzero\Content\Import\Adapter\Csv;

use Iterator;
use stdClass;

/**
 *  CSV Iterator Class implemeting interator
 */
class Reader implements Iterator
{
	const ROW_LENGTH = 0;

	private $file;
	private $delimiter;
	private $position;
	private $headers;

	/**
	 * CSV Iterator Constructor
	 * 
	 * @param   string  $file  CSV file we want to use
	 * @param   string  $key   CSV field delimiter
	 * @return  void
	 */
	public function __construct($file, $delimiter)
	{
		// Line endings can vary depending on what App/OS outputted the CSV
		ini_set('auto_detect_line_endings', true);

		$this->file      = fopen($file, 'r');

		ini_set('auto_detect_line_endings', false);

		$this->delimiter = $delimiter;
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
			$this->rewind();

			$row = fgetcsv($this->file, self::ROW_LENGTH, $this->delimiter);

			$this->position++;

			// store headers for later
			if ($this->position == 1)
			{
				$this->headers = $row;
			}

			$this->rewind();
		}

		return $this->headers;
	}

	/**
	 * Get the current XML node
	 *
	 * @return  object  XML node as a stdClass
	 */
	public function current()
	{
		$row = fgetcsv($this->file, self::ROW_LENGTH, $this->delimiter);
		$this->position++;

		// store headers for later
		if ($this->position == 1)
		{
			$this->headers = $row;
		}

		// return null for the first row and last row if empty
		// we dont want to count the headings row
		if ($this->position == 1 || $row === false)
		{
			return null;
		}

		// map headings
		$object = new stdClass;
		foreach ($this->headers as $k => $header)
		{
			if (strpos($header, ':'))
			{
				$parts = explode(':', $header);

				if (!isset($object->$parts[0]))
				{
					$object->$parts[0] = new stdClass;
				}
				$object->$parts[0]->$parts[1] = $row[$k];
			}
			else
			{
				$object->$header = $row[$k];
			}
		}

		// return as object
		return $object;
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
		return !feof($this->file);
	}

	/**
	 * Move to the first node that matches our key
	 *
	 * @return  void
	 */
	public function rewind()
	{
		$this->position = 0;
		rewind($this->file);
	}

	/**
	 * Is our current node valid
	 *
	 * @return  boolean  Is valid?
	 */
	public function valid()
	{
		if (!$this->next())
		{
			fclose($this->file);
			return FALSE;
		}
		return TRUE;
	}
}