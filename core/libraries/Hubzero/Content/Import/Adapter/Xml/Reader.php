<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Import\Adapter\Xml;

use Iterator;
use XMLReader;
use DOMDocument;

/**
 *  XML Reader Iterator Class implemeting interator
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
	 * Key
	 *
	 * @var  string
	 */
	private $key;

	/**
	 * File reader
	 *
	 * @var  object
	 */
	private $reader;

	/**
	 * Cursor position
	 *
	 * @var  int
	 */
	private $position;

	/**
	 * XML Reader Iterator Constructor
	 *
	 * @param   string  $file  XML file we want to use
	 * @param   string  $key   XML node we are looking to iterate over
	 * @return  void
	 */
	public function __construct($file, $key)
	{
		$this->reader   = new XMLReader();
		$this->position = 0;
		$this->file     = $file;
		$this->key      = $key;
	}

	/**
	 * Get the current XML node
	 *
	 * @return  object  XML node as a stdClass
	 */

	#[\ReturnTypeWillChange]
	public function current()
	{
		$doc = new DOMDocument();
		$object = simplexml_import_dom($doc->importNode($this->reader->expand(), true));
		return json_decode(json_encode($object));
	}

	/**
	 * Get our current position while iterating
	 *
	 * @return  int  Current position
	 */

	#[\ReturnTypeWillChange]
	public function key()
	{
		return $this->position;
	}

	/**
	 * Go to the next Node that matches our key
	 *
	 * @return  void
	 */

	#[\ReturnTypeWillChange]
	public function next()
	{
		if ($this->reader->next($this->key))
		{
			++$this->position;
		}
	}

	/**
	 * Move to the first node that matches our key
	 *
	 * @return  void
	 */

	#[\ReturnTypeWillChange]
	public function rewind()
	{
		// open file with reader
		// force UTF-8, validate XML, & substitute entities while reading
		$this->reader->open($this->file, 'UTF-8', XMLReader::VALIDATE | XMLReader::SUBST_ENTITIES);

		// fast forward to first record
		while ($this->reader->read() && $this->reader->name !== $this->key)
		{
			// Do nothing...
		}
	}

	/**
	 * Is our current node valid
	 *
	 * @return  bool  Is valid?
	 */

	#[\ReturnTypeWillChange]
	public function valid()
	{
		return $this->reader->name === $this->key;
	}
}
