<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Format;

use Components\Publications\Models\Format as Base;
use stdClass;

/**
 * PHP class format handler
 */
class PHP extends Base
{
	/**
	 * Converts an object into a php class string.
	 *	- NOTE: Only one depth level is supported.
	 *
	 * @param   object  $object  Data Source Object
	 * @param   array   $params  Parameters used by the formatter
	 * @return  string  Config class formatted string
	 */
	public function objectToString($object, $params = array())
	{
		// Build the object variables string
		$vars = '';
		foreach (get_object_vars($object) as $k => $v)
		{
			if (is_scalar($v))
			{
				$vars .= "\tpublic $". $k . " = '" . addcslashes($v, '\\\'') . "';\n";
			}
			else if (is_array($v))
			{
				$vars .= "\tpublic $". $k . " = " . $this->getArrayString($v) . ";\n";
			}
		}

		$str = "<?php\nclass " . $params['class'] . " {\n";
		$str .= $vars;
		$str .= "}";

		// Use the closing tag if it not set to false in parameters.
		if (!isset($params['closingtag']) || $params['closingtag'] !== false)
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
	 * @return  object   Data object.
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
			if (is_array($v))
			{
				$s .= $this->getArrayString($v);
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
