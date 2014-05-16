<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Config\Processor;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Ini parser/writer
 **/
class Ini
{
	/**
	 * Raw config string
	 *
	 * @var string
	 **/
	private $raw = null;

	/**
	 * Parsed config values
	 *
	 * @var array
	 **/
	private $parsed = null;

	/**
	 * Constructor
	 *
	 * @param  (string) - file/string/array/object of ini content
	 * @return void
	 **/
	public function __construct($config)
	{
		if (is_file($config) && is_readable($config))
		{
			$contents = file_get_contents($config);
			$this->raw = $contents;
		}
		else if (is_array($config) || is_object($config))
		{
			$this->parsed = (array)$config;
		}
		else if (is_string($config))
		{
			$this->raw = $content;
		}
	}

	/**
	 * Parse ini content (file or string) - static method
	 *
	 * @param  (string) - file/string/array/object of ini content
	 * @param  (bool)   - parse headings or keep flat
	 * @return (array)  - parsed content
	 **/
	public static function parse($config, $headings=true)
	{
		static $instances = array();

		$identifier = serialize($config).$headings;

		if (!isset($instances[$identifier]))
		{
			$instances[$identifier] = new self($config);
			$instances[$identifier]->_parse($headings);
		}

		return $instances[$identifier]->parsed;
	}

	/**
	 * Parse ini content
	 *
	 * @param  (bool)  - include headings or keep flat
	 * @return void
	 **/
	private function _parse($headings=true)
	{
		if (isset($this->parsed))
		{
			return $this->parsed;
		}

		$content = $this->raw;
		$lines   = explode("\n", $content);
		$parsed  = array();
		$heading = null;

		if (count($lines) > 0)
		{
			foreach ($lines as $line)
			{
				$line = trim($line);
				if (substr($line, 0, 1) == "#" || substr($line, 0, 1) == ";")
				{
					continue;
				}

				if (substr($line, 0, 1) == "[")
				{
					preg_match('/\[([[:alnum:]_-]*)\]/', $line, $match);

					if (isset($match[1]) && $headings)
					{
						$heading = $match[1];
					}
					continue;
				}

				if (strpos($line, "=") !== false)
				{
					$parts = explode("=", $line, 2);
					$key   = trim($parts[0]);
					$value = trim($parts[1]);

					if (substr($value, 0, 1) == '"')
					{
						$value = substr($value, 1, -1);
					}

					if (isset($heading))
					{
						$parsed[$heading][$key] = $value;
					}
					else
					{
						$parsed[$key] = $value;
					}
				}
			}
		}

		if (!empty($parsed))
		{
			$this->parsed = $parsed;
		}
	}
}