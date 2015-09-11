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

namespace Components\Resources\Import\Iterators;

/**
 *  XML Reader Iterator Class implemeting interator
 */
class Xml implements \Iterator
{
	private $file;
	private $key;
	private $reader;
	private $position;

	/**
	 * XML Reader Iterator Constructor
	 * @param string $file XML file we want to use
	 * @param string $key  XML node we are looking to iterate over
	 */
	public function __construct($file, $key)
	{
		$this->reader   = new \XMLReader();
		$this->position = 0;
		$this->file     = $file;
		$this->key      = $key;
	}

	/**
	 * Get the current XML node
	 * @return object XML node as a stdClass
	 */
	public function current()
	{
		$doc = new \DOMDocument();
		$object = simplexml_import_dom($doc->importNode($this->reader->expand(), true));
		return json_decode(json_encode($object));
	}

	/**
	 * Get our current position while iterating
	 * @return int Current position
	 */
	public function key()
	{
		return $this->position;
	}

	/**
	 * Go to the next Node that matches our key
	 * @return void
	 */
	public function next()
	{
		if ($this->reader->next($this->key))
		{
			++$this->position;
		}
	}

	/**
	 * Move to the first node that matches our key
	 * @return void
	 */
	public function rewind()
	{
		// open file with reader
		// force UTF-8, validate XML, & substitute entities while reading
		$this->reader->open($this->file, 'UTF-8', \XMLReader::VALIDATE | \XMLReader::SUBST_ENTITIES);

		// fast forward to first record
		while ($this->reader->read() && $this->reader->name !== $this->key);
	}

	/**
	 * Is our current node valid
	 * @return bool Is valid?
	 */
	public function valid()
	{
		return $this->reader->name === $this->key;
	}
}