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

use Symfony\Component\Yaml\Yaml as SymfonyYaml;
use Symfony\Component\Yaml\Exception\ParseException;
use Hubzero\Error\Exception\RuntimeException;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Yaml parser/writer
 **/
class Yaml
{
	/**
	 * Parses yaml content (file or string)
	 *
	 * @param  string $config the file/string of yaml content
	 * @return array
	 **/
	public static function parse($config)
	{
		// See if it's a file or a string
		if (is_file($config) && is_readable($config))
		{
			$config = file_get_contents($config);
		}

		// Try to parse, catching exception if it fails
		try
		{
			// Parse config string
			$parsed = SymfonyYaml::parse($config);
		}
		catch (ParseException $e)
		{
			// Throw an exception Hubzero knows how to catch
			throw new RuntimeException("Failed to parse provided Yaml content.");
		}

		return $parsed;
	}

	/**
	 * Write yaml content
	 *
	 * @param  string $config the configuration content to write out
	 * @param  string $destination the path to write the content to
	 * @return array
	 **/
	public static function write($config, $destination)
	{
		// Generate Yaml representation of the data
		// 2 = the extended (i.e. non-inline) output format
		$content = SymfonyYaml::dump($config, 2);

		// Write the content to the destination path
		file_put_contents($destination, $content);
	}
}