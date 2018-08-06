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
 * @author    Anthony Fuentes <fuentesa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Helpers;

/**
 * Zip Archive helper
 */
class ZipHelper
{
	/**
	 * Builds a hierarchical array of archive contents
	 *
	 * @param   string  $path
	 * @return  array
	 */
	public static function getArchiveContents($path)
	{
		$archive  = self::getArchive($path);
		$contents = self::parseArchiveContents($archive);

		return $contents;
	}

	/**
	 * Load bundle archive
	 *
	 * @param    string  $path
	 * @return   object  \ZipArchive
	 */
	protected static function getArchive($path)
	{
		$archive = null;

		if (file_exists($path))
		{
			$archive = new \ZipArchive();
			$archive->open($path);
		}

		return $archive;
	}

	/**
	 * Builds a hierarchical array of archive contents
	 *
	 * @param   object  $archive  \ZipArchive
	 * @return  array
	 */
	protected static function parseArchiveContents($archive)
	{
		$parsedContents = [];
		$contents = self::readArchive($archive);

		foreach ($contents as $entry)
		{
			self::addEntryToContents($entry, $parsedContents);
		}

		return $parsedContents;
	}

	/**
	 * Reads contents of a ZipArchive
	 *
	 * @param   object  $archive  \ZipArchive
	 * @return  array
	 */
	protected static function readArchive($archive)
	{
		$contents = [];

		if ($archive instanceof \ZipArchive)
		{
			for ($i = 0; $i < $archive->numFiles; $i++)
			{
				$contents[] = $archive->statIndex($i);
			}
		}

		return $contents;
	}

	/**
	 * Adds an entry to representational array
	 *
	 * @param   array  $entry
	 * @param   array  $contents
	 * @return  void
	 */
	protected static function addEntryToContents($entry, &$contents)
	{
		$directory = self::getEntryDirectory($entry);
		$entry['name'] = self::parseName($entry['name']);
		$entry['isDirectory'] = false;

		if ($directory === '')
		{
			$contents[] = $entry;
			return true;
		}
		else
		{
			if (!isset($contents[$directory]))
			{
				$contents[$directory] = [];
				$contents[$directory]['contents'] = [];
				$contents[$directory]['name'] = $directory;
				$contents[$directory]['isDirectory'] = true;
			}

			$contents[$directory]['contents'][] = $entry;
		}
	}

	/**
	 * Gets archive entry's directory
	 *
	 * @param   array   $entry
	 * @return  string
	 */
	protected static function getEntryDirectory($entry)
	{
		$pathSegments = explode('/', $entry['name']);
		array_shift($pathSegments);
		array_pop($pathSegments);

		$directory = implode($pathSegments);

		return $directory;
	}

	/**
	 * Removes path from entry's name
	 *
	 * @param   string  $name
	 * @return  string
	 */
	protected static function parseName($name)
	{
		$pathSegments = explode('/', $name);

		$name = array_pop($pathSegments);

		return $name;
	}
}
