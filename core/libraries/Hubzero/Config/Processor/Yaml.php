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

namespace Hubzero\Config\Processor;

use Hubzero\Config\Exception\ParseException;
use Hubzero\Config\Processor as Base;
use Symfony\Component\Yaml\Yaml as SymfonyYaml;
use Exception;

/**
 * YAML Processor
 */
class Yaml extends Base
{
	/**
	 * Returns an array of allowed file extensions for this parser
	 *
	 * @return  array
	 */
	public function getSupportedExtensions()
	{
		return array('yaml', 'yml');
	}

	/**
	 * Loads a YAML/YML file as an array
	 *
	 * @param   string  $path
	 * @return  object
	 * @throws  ParseException If If there is an error parsing the YAML file
	 */
	public function parse($path)
	{
		try
		{
			$data = YamlParser::parse(file_get_contents($path));
		}
		catch (Exception $exception)
		{
			throw new ParseException(
				array(
					'message'   => 'Error parsing YAML',
					'exception' => $exception,
				)
			);
		}

		return $data;
	}

	/**
	 * Try to determine if the data can be parsed
	 *
	 * @param   string   $data
	 * @return  boolean
	 */
	public function canParse($data)
	{
		$data = trim($data);

		try
		{
			// Parse config string
			$parsed = SymfonyYaml::parse($data);
		}
		catch (Exception $e)
		{
			// Throw an exception Hubzero knows how to catch
			return false;
		}

		return true;
	}

	/**
	 * Converts an object into a YAML formatted string.
	 *
	 * @param   object  $object   Data source object.
	 * @param   array   $options  Options used by the formatter.
	 * @return  string  YAML formatted string.
	 */
	public function objectToString($object, $options = array())
	{
		if (is_string($object))
		{
			return $object;
		}

		return SymfonyYaml::dump((array) $this->asArray($object), 2);
	}

	/**
	 * Method to recursively convert an object of data to an array.
	 *
	 * @param   object  $data  An object of data to return as an array.
	 * @return  array   Array representation of the input object.
	 */
	protected function asArray($data)
	{
		$array = array();

		foreach (get_object_vars((object) $data) as $k => $v)
		{
			if (is_object($v))
			{
				$array[$k] = $this->asArray($v);
			}
			else
			{
				$array[$k] = $v;
			}
		}

		return $array;
	}

	/**
	 * Parse a YAML formatted string and convert it into an object.
	 *
	 * @param   string  $data     YAML formatted string to convert.
	 * @param   array   $options  Options used by the formatter.
	 * @return  object  Data object.
	 */
	public function stringToObject($data, $options = array())
	{
		if (is_object($data))
		{
			return $data;
		}

		$data = trim($data);

		// Try to parse, catching exception if it fails
		try
		{
			// Parse config string
			$parsed = SymfonyYaml::parse($data);
		}
		catch (Exception $e)
		{
			// Throw an exception Hubzero knows how to catch
			throw new ParseException(
				array(
					'message'   => 'Error parsing YAML',
					'exception' => $exception,
				)
			);
		}

		return $parsed;
	}
}
