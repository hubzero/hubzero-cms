<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config\Processor;

use Hubzero\Config\Exception\ParseException;
use Hubzero\Config\Processor as Base;
use Symfony\Component\Yaml\Yaml as SymfonyYaml;
use Exception;
use stdClass;

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
	 * @return  array
	 * @throws  ParseException If If there is an error parsing the YAML file
	 */
	public function parse($path)
	{
		try
		{
			$data = SymfonyYaml::parse(file_get_contents($path));
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
			$parsed = SymfonyYaml::parse($data, true);
		}
		catch (Exception $e)
		{
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
			$parsed = SymfonyYaml::parse($data, true);
		}
		catch (Exception $e)
		{
			// Throw an exception Hubzero knows how to catch
			throw new ParseException(
				array(
					'message'   => 'Error parsing YAML',
					'exception' => $e,
				)
			);
		}

		if (!$parsed)
		{
			$parsed = '';
		}

		return (is_string($parsed) ? $parsed : $this->toObject($parsed));
	}

	/**
	 * Convert an array to an object
	 *
	 * @param   array   $data
	 * @return  object  Data object.
	 */
	protected function toObject($data)
	{
		$obj = new stdClass;

		foreach ($data as $key => $datum)
		{
			if (is_array($datum))
			{
				$obj->$key = $this->toObject($datum);
			}
			else
			{
				$obj->$key = $datum;
			}
		}

		return $obj;
	}
}
