<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Import\Iterators;

/**
 *  CSV Iterator Class implemeting interator
 */
class Csv implements \Iterator
{
	const ROW_LENGTH = 0;

	private $file;
	private $delimiter;
	private $position;
	private $headers;

	/**
	 * CSV Iterator Constructor
	 *
	 * @param  string  $file  CSV file we want to use
	 * @param  string  $key   CSV field delimiter
	 */
	public function __construct($file, $delimiter)
	{
		$this->file      = fopen($file, 'r');
		$this->delimiter = $delimiter;
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
		$object = new \stdClass;
		foreach ($this->headers as $k => $header)
		{
			if (strpos($header, ':'))
			{
				$parts = explode(':', $header);

				if (!isset($object->{$parts[0]}))
				{
					$object->{$parts[0]} = new \stdClass;
				}
				$object->{$parts[0]}->{$parts[1]} = $row[$k];
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
	 * @return  int  Current position
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
	 * @return  bool  Is valid?
	 */
	public function valid()
	{
		if (!$this->next())
		{
			fclose($this->file);
			return false;
		}
		return true;
	}
}
