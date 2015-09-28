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
 * @package   framework
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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