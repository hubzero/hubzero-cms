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

/**
 * JSON processor for Registry.
 */
class Json extends Base
{
	/**
	 * {@inheritDoc}
	 */
	public function getSupportedExtensions()
	{
		return array('json');
	}

	/**
	 * {@inheritDoc}
	 * Loads a JSON file as an array
	 *
	 * @throws  ParseException If there is an error parsing the JSON file
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
