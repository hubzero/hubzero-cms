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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config;

/**
 * File writer class
 */
class FileWriter
{
	/**
	 * The format to write
	 *
	 * @var  string
	 */
	protected $format = 'php';

	/**
	 * The default configuration path.
	 *
	 * @var  string
	 */
	protected $path;

	/**
	 * Create a new file configuration loader.
	 *
	 * @param   string  $format
	 * @param   string  $path
	 * @return  void
	 */
	public function __construct($format, $path)
	{
		$this->format = $format;
		$this->path   = $path;
	}

	/**
	 * Create a new file configuration loader.
	 *
	 * @param   object  $contents
	 * @param   string  $group
	 * @param   string  $client
	 * @return  boolean
	 */
	public function write($contents, $group, $client = null)
	{
		$path = $this->getPath($client, $group);

		if (!$path)
		{
			return false;
		}

		$contents = $this->toContent($contents, $this->format);

		return !($this->putContent($path, $contents) === false);
	}

	/**
	 * Generate the path to write
	 *
	 * @param   string  $client
	 * @param   string  $group
	 * @return  string
	 */
	private function getPath($client, $group)
	{
		$path = $this->path;

		if (is_null($path))
		{
			return null;
		}

		$file = $path . DS . ($client ? $client . DS : '') . $group . '.' . $this->format;

		return $file;
	}

	/**
	 * Turn contents into a string of the correct format
	 *
	 * @param   string  $client
	 * @param   string  $group
	 * @return  string
	 */
	public function toContent($contents, $format)
	{
		if (!($contents instanceof Registry))
		{
			$contents = new Registry($contents);
		}

		return $contents->toString($format, array('format' => 'array'));
	}

	/**
	 * Write the contents of a file.
	 *
	 * @param   string   $path
	 * @param   string   $contents
	 * @return  boolean
	 */
	public function putContent($file, $contents)
	{
		$path = dirname($file);

		if (!is_dir($path))
		{
			if (!mkdir($path, 0750))
			{
				return false;
			}
		}

		return file_put_contents($file, $contents);
	}
}
