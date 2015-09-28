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
