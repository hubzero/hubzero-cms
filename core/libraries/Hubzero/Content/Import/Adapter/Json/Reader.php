<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Import\Adapter\Json;

use Iterator;

/**
 *  JSON Reader Iterator Class
 */
class Reader implements Iterator
{
	/**
	 * File contents
	 *
	 * @var  object
	 */
	private $file;

	/**
	 * Key
	 *
	 * @var  string
	 */
	private $key;

	/**
	 * Cursor position
	 *
	 * @var  int
	 */
	private $position;

	/**
	 * Constructor
	 *
	 * @param   string  $file  XML file we want to use
	 * @param   string  $key   XML node we are looking to iterate over
	 * @return  void
	 */
	public function __construct($file, $key='record')
	{
		$this->position = 0;
		$this->file     = json_decode(file_get_contents($file), true);
		$this->key      = $key;
	}

	/**
	 * Get the current XML node
	 *
	 * @return  object  XML node as a stdClass
	 */
	public function current()
	{
		if ($this->valid())
		{
			return $this->file[$this->position];
		}
		return null;
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
	 * @return  bool  Is valid?
	 */
	public function valid()
	{
		return isset($this->file[$this->position]);
	}
}
