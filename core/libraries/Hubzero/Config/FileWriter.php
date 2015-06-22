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
	 * @param  string  $format
	 * @param  string  $path
	 * @return void
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
			if (!mkdir($path, 0640))
			{
				return false;
			}
		}

		return file_put_contents($file, $contents);
	}
}
