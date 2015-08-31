<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
