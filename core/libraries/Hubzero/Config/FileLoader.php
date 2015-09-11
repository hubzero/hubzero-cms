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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config;

use Hubzero\Config\Exception\UnsupportedFormatException;
use Hubzero\Config\Exception\EmptyDirectoryException;

/**
 * File loader class
 */
class FileLoader
{
	/**
	 * The default configuration path.
	 *
	 * @var  string
	 */
	protected $defaultPath;

	/**
	 * Create a new file configuration loader.
	 *
	 * @param   string  $defaultPath
	 * @return  void
	 */
	public function __construct($defaultPath)
	{
		$this->defaultPath  = $defaultPath;
	}

	/**
	 * Load the given configuration group.
	 *
	 * @param   string  $client
	 * @return  array
	 */
	public function load($client = null)
	{
		$data = array();

		// First we'll get the root configuration path for the environment which is
		// where all of the configuration files live for that namespace, as well
		// as any environment folders with their specific configuration items.
		try
		{
			$paths = $this->getPath($this->defaultPath);

			if (empty($paths))
			{
				throw new EmptyDirectoryException("Configuration directory: [" . $this->defaultPath . "] is empty");
			}

			foreach ($paths as $path)
			{
				// Get file information
				$info      = pathinfo($path);
				$group     = isset($info['filename'])  ? strtolower($info['filename'])  : '';
				$extension = isset($info['extension']) ? strtolower($info['extension']) : '';
				if (!$extension || $extension == 'html')
				{
					continue;
				}
				$parser    = $this->getParser($extension);

				$data[$group] = $parser->parse($path);
			}

			if (empty($data))
			{
				throw new EmptyDirectoryException("Configuration directory: [" . $this->defaultPath . "] is empty");
			}

			if ($client)
			{
				$paths = $this->getPath($this->defaultPath . DS . $client);

				foreach ($paths as $path)
				{
					// Get file information
					$info      = pathinfo($path);
					$group     = isset($info['filename'])  ? strtolower($info['filename'])  : '';
					$extension = isset($info['extension']) ? strtolower($info['extension']) : '';
					if (!$extension || $extension == 'html')
					{
						continue;
					}
					$parser    = $this->getParser($extension);

					if (!isset($data[$group]))
					{
						$data[$group] = array();
					}
					$data[$group] = array_replace_recursive(
						$data[$group],
						$parser->parse($path)
					);
				}
			}
		}
		catch (\Exception $e)
		{
			$loader = new Legacy();
			$loader->split();

			$data = $loader->toArray();
		}

		return $data;
	}

	/**
	 * Merge the items in the given file into the items.
	 *
	 * @param   array   $items
	 * @param   string  $file
	 * @return  array
	 */
	protected function mergeEnvironment(array $items, $file)
	{
		return array_replace_recursive($items, $this->getRequire($file));
	}

	/**
	 * Gets a parser for a given file extension
	 *
	 * @param   string  $extension
	 * @return  object
	 * @throws  UnsupportedFormatException  If `$extension` is an unsupported file format
	 */
	protected function getParser($extension)
	{
		$parser = null;

		$extension = strtolower($extension);

		foreach (Processor::all() as $fileParser)
		{
			if (in_array($extension, $fileParser->getSupportedExtensions()))
			{
				$parser = $fileParser;
				break;
			}
		}

		// If none exist, then throw an exception
		if ($parser === null)
		{
			throw new UnsupportedFormatException(sprintf('Unsupported configuration format "%s"', $extension));
		}

		return $parser;
	}

	/**
	 * Checks `$path` to see if it is either an array, a directory, or a file
	 *
	 * @param   mixed  $path
	 * @return  array
	 * @throws  EmptyDirectoryException  If `$path` is an empty directory
	 * @throws  FileNotFoundException    If a file is not found at `$path`
	 */
	protected function getPath($path)
	{
		// If `$path` is array
		if (is_array($path))
		{
			$paths = array();

			foreach ($path as $unverifiedPath)
			{
				$paths = array_merge($paths, $this->getPath($unverifiedPath));
			}

			return $paths;
		}

		// If `$path` is a directory
		if (is_dir($path))
		{
			$paths = glob($path . '/*.*');

			/*if (empty($paths))
			{
				throw new EmptyDirectoryException("Configuration directory: [$path] is empty");
			}*/

			return $paths;
		}

		// If `$path` is not a file, throw an exception
		if (file_exists($path))
		{
			//throw new FileNotFoundException("Configuration file: [$path] cannot be found");
			return array($path);
		}

		return array();
	}
}
