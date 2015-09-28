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
