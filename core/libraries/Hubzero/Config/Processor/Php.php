<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Config\Processor;

use Hubzero\Config\Exception\UnsupportedFormatException;
use Hubzero\Config\Exception\ParseException;
use Hubzero\Config\Processor as Base;

/**
 * PHP class processor
 */
class Php extends Base
{
	/**
	 * Returns an array of allowed file extensions for this parser
	 *
	 * @return  array
	 */
	public function getSupportedExtensions()
	{
		return array('php');
	}

	/**
	 * {@inheritDoc}
	 * Loads a PHP file and gets its' contents as an array
	 *
	 * @param   string  $path
	 * @return  object
	 * @throws  ParseException              If the PHP file throws an exception
	 * @throws  UnsupportedFormatException  If the PHP file does not return an array
	 */
	public function parse($path)
	{
		// Require the file, if it throws an exception, rethrow it
		try
		{
			$temp = require $path;
		}
		catch (\Exception $exception)
		{
			throw new ParseException(
				array(
					'message'   => 'PHP file threw an exception',
					'exception' => $exception,
				)
			);
		}

		// If we have a callable, run it and expect an array back
		if (is_callable($temp))
		{
			$temp = call_user_func($temp);
		}

		// Check for array, if its anything else, throw an exception
		if (!$temp || !is_array($temp))
		{
			throw new UnsupportedFormatException('PHP file does not return an array');
		}

		return $temp;
	}

	/**
	 * Converts an object into a php class string.
	 *
	 * NOTE: Only one depth level is supported.
	 *
	 * @param   object  $object  Data Source Object
	 * @param   array   $params  Parameters used by the formatter
	 * @return  string  Config class formatted string
	 */
	public function objectToString($object, $options = array())
	{
		$format = 'object';
		if (isset($options['format']) && $options['format'])
		{
			$format = $options['format'];
		}

		// Build the object variables string
		$vars = '';
		foreach (get_object_vars($object) as $k => $v)
		{
			if (is_scalar($v))
			{
				switch ($format)
				{
					case 'object':
						$vars .= "\tvar $" . $k . " = '" . addcslashes($v, '\\\'') . "';\n";
					break;
					case 'array':
						$vars .= "\t'" . $k . "' => '" . addcslashes($v, '\\\'') . "',\n";
					break;
				}
			}
			elseif (is_array($v) || is_object($v))
			{
				switch ($format)
				{
					case 'object':
						$vars .= "\tvar $" . $k . " = " . $this->getArrayString((array) $v) . ";\n";
					break;
					case 'array':
						$vars .= "\t'" . $k . "' => " . $this->getArrayString((array) $v) . ",\n";
					break;
				}
			}
			elseif (is_null($v))
			{
				switch ($format)
				{
					case 'object':
						$vars .= "\tvar $" . $k . " = '';\n";
					break;
					case 'array':
						$vars .= "\t'" . $k . "' => '',\n";
					break;
				}
			}
		}

		$str  = "<?php\n";
		switch ($format)
		{
			case 'object':
				$str .= "class " . $options['class'] . "\n";
				$str .= "{\n";
				$str .= $vars;
				$str .= "}";
			break;
			case 'array':
				$str .= "return array(\n";
				$str .= $vars;
				$str .= ");";
			break;
		}

		// Use the closing tag if it not set to false in parameters.
		if (isset($options['closingtag']) && $options['closingtag'])
		{
			$str .= "\n?>";
		}

		return $str;
	}

	/**
	 * Parse a PHP class formatted string and convert it into an object.
	 *
	 * @param   string  $data     PHP Class formatted string to convert.
	 * @param   array   $options  Options used by the formatter.
	 * @return  object  Data object.
	 */
	public function stringToObject($data, $options = array())
	{
		return true;
	}

	/**
	 * Method to get an array as an exported string.
	 *
	 * @param   array  $a  The array to get as a string.
	 * @return  array
	 */
	protected function getArrayString($a)
	{
		$s = 'array(';
		$i = 0;
		foreach ($a as $k => $v)
		{
			$s .= ($i) ? ', ' : '';
			$s .= '"' . $k . '" => ';
			if (is_array($v) || is_object($v))
			{
				$s .= $this->getArrayString((array) $v);
			}
			else
			{
				$s .= '"' . addslashes($v) . '"';
			}
			$i++;
		}
		$s .= ')';
		return $s;
	}
}
