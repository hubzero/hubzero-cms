<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config\Processor;

use Hubzero\Config\Exception\ParseException;
use Hubzero\Config\Processor as Base;

/**
 * JSON processor for Registry.
 *
 * Based, in part, on Joomla's JRegistry format classes
 */
class Json extends Base
{
	/**
	 * Returns an array of allowed file extensions for this parser
	 *
	 * @return  array
	 */
	public function getSupportedExtensions()
	{
		return array('json');
	}

	/**
	 * Loads a JSON file as an array
	 *
	 * @param   string  $path
	 * @return  array
	 * @throws  ParseException  If there is an error parsing the JSON file
	 */
	public function parse($path)
	{
		$data = json_decode(file_get_contents($path), true);

		if (function_exists('json_last_error_msg'))
		{
			$error_message = json_last_error_msg();
		}
		else
		{
			$error_message  = 'Syntax error';
		}

		if (json_last_error() !== JSON_ERROR_NONE)
		{
			$error = array(
				'message' => $error_message,
				'type'    => json_last_error(),
				'file'    => $path,
			);
			throw new ParseException($error);
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
		$data = trim($data, '"');

		if ((substr($data, 0, 1) != '{') && (substr($data, -1, 1) != '}'))
		{
			return false;
		}

		$obj = json_decode($data);
		if (json_last_error() != JSON_ERROR_NONE)
		{
			return false;
		}

		return true;
	}

	/**
	 * Converts an object into a JSON formatted string.
	 *
	 * @param   object  $object   Data source object.
	 * @param   array   $options  Options used by the formatter.
	 * @return  string  JSON formatted string.
	 */
	public function objectToString($object, $options = array())
	{
		if (is_string($object))
		{
			return $object;
		}

		if (isset($options['pretty_print']) && $options['pretty_print'])
		{
			return json_encode($object, JSON_PRETTY_PRINT);
		}

		return json_encode($object);
	}

	/**
	 * Parse a JSON formatted string and convert it into an object.
	 *
	 * If the string is not in JSON format, this method will attempt to parse it as INI format.
	 *
	 * @param   string  $data     JSON formatted string to convert.
	 * @param   array   $options  Options used by the formatter.
	 * @return  object  Data object.
	 */
	public function stringToObject($data, $options = array('processSections' => false))
	{
		if (is_object($data))
		{
			return $data;
		}

		if (is_bool($options))
		{
			$options = array('processSections' => $options);
		}

		$data = trim($data);
		$data = trim($data, '"');

		if ((substr($data, 0, 1) != '{') && (substr($data, -1, 1) != '}'))
		{
			$obj = Base::instance('ini')->stringToObject($data, $options);
		}
		else
		{
			$obj = json_decode($data);
		}
		return $obj;
	}
}
