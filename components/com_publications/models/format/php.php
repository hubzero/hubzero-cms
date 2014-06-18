<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is within the rest of the framework
defined('_JEXEC') or die('Restricted access');

/**
 * PHP class format handler for PublicationsElementsFormat
 */
class PublicationsElementsFormatPHP extends PublicationsElementsFormat
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
