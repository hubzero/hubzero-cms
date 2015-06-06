<?php
/**
 * HUBzero CMS
 *
 * Copyright 2011-2014 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2011-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Api\Response;

/**
 * Hubzero class for encoding and decoding XML
 */
class Xml
{
	/**
	 * Error message type for depth
	 */
	const XML_ERROR_DEPTH = 1;

	/**
	 * Error message type for state mismatch
	 */
	const XML_ERROR_STATE_MISMATCH = 2;

	/**
	 * Error message type for ctrl char
	 */
	const XML_ERROR_CTRL_CHAR = 3;
	//const XML_ERROR_SYNTAX = 4;

	/**
	 * Error message type for UTF8
	 */
	const XML_ERROR_UTF8 = 5;

	/**
	 * Encode a variable into XML
	 *
	 * @param      mixed   $mixed            Data to encode (array/object)
	 * @param      string  $tag              Root tag name
	 * @param      string  $attributes       Attributes to add
	 * @param      number  $depth            Depth to encode to
	 * @param      boolean $show_declaration Show XML doctype declaration
	 * @return     string XML
	 */
	public function encode($mixed, $tag='root', $attributes='', $depth = 1, $show_declaration = true)
	{
		$declaration = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';

		$indent = ($depth > 1) ? str_repeat("\t", $depth-1) : '';

		$xml = ($show_declaration) ? $indent . $declaration . "\n" : '';

		$element_type = gettype($mixed);

		if (is_array($mixed))
		{
			$i = 0;

			foreach ($mixed as $key => $value)
			{
				if ($key !== $i)
				{
					$element_type = 'a-array';
					break;
				}

				++$i;
			}
		}

		$sp = (!empty($attributes)) ? ' ' : '';

		if (in_array($element_type, array('object','a-array')))
		{
			$xml_element_type = 'object';
		}
		else if (in_array($element_type, array('double','integer')))
		{
			$xml_element_type = 'number';
		}
		else if (in_array($element_type, array('array','boolean','string')))
		{
			$xml_element_type = $element_type;
		}
		else if ($element_type == 'NULL')
		{
			$xml_element_type = 'null';
		}
		else
		{
			$xml_element_type = '';
		}

		if (!empty($xml_element_type))
		{
			$attributes .= $sp . 'type="' . $xml_element_type . '"';
		}

		$sp = (!empty($attributes)) ? ' ' : '';

		$xml .= $indent . '<' . $tag . $sp . $attributes . '>';

		if (is_array($mixed) || is_object($mixed))
		{
			foreach ($mixed as $key=>$value)
			{
				if (!isset($first))
				{
					$xml .= "\n";
					$first = true;
				}

				$value_type = gettype($value);

				if (is_array($value))
				{
					$i = 0;

					foreach ($value as $vkey => $vvalue)
					{
						if ($vkey !== $i)
						{
							$value_type = 'a-array';
							break;
						}

						++$i;
					}
				}

				$vattributes = '';

				if ($element_type == 'array')
				{
					$vname = 'item';
				}
				else if ($element_type == 'a-array')
				{
					$vname = 'a:item';
					$vattributes = 'xmlns:a="item" item="' . $key . '"';
				}
				else
				{
					$vname = $key;
				}

				$xml .= self::encode($value, $vname, $vattributes, $depth+1, false);
			}

			$xml .= $indent;
		}
		else
		{
			if (is_bool($mixed))
			{
				$xml .= ($mixed) ? 'true' : 'false';
			}
			else if (is_null($mixed))
			{
				$xml .= '';
			}
			else
			{
				$xml .= htmlspecialchars($mixed, ENT_QUOTES);
			}
		}

		$xml .= "</$tag>";

		if ($depth > 1)
		{
			$xml .= "\n";
		}

		self::last_error(self::XML_ERROR_NONE);
	    return $xml;
	}

	/**
	 * Set the last error
	 *
	 * @param      integer $id Error number
	 * @return     integer
	 */
	public function last_error($id)
	{
		static $last_error = 0;

		if (isset($id))
		{
			$last_error = $id;
		}

		return $last_error;
	}

	/**
	 * Parse XML and return as array or object
	 *
	 * @param      string $xml XML
	 * @return     mixed Array or object
	 */
	public function decode($xml)
	{
		$p = xml_parser_create();

		xml_parser_set_option($p, self::XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($p, self::XML_OPTION_SKIP_WHITE,   1);
		xml_parse_into_struct($p, $xml, $vals, $index);
		xml_parser_free($p);

		$count = count($vals);

		$stack = array();

		$obj = null;

		for ($i = 0; $i < $count; $i++)
		{
			$v = $vals[$i];

			if ($v['type'] == 'open')
			{
				if ($v['attributes']['type'] == 'object')
				{
					if (is_null($obj))
					{
						array_push($stack, array(null,$obj));
					}
					else if (is_array($obj) || is_object($obj))
					{
						array_push($stack, array($v['tag'],$obj));
					}
					else
					{
						if ($fatal)
						{
							die('invalid container');
						}
						else
						{
							return false;
						}
					}

					$obj = new \stdClass();
				}
				else if ($v['attributes']['type'] == 'array')
				{
					if (is_null($obj))
					{
						array_push($stack, array(null,$obj));
					}
					else if (is_array($obj) || is_object($obj))
					{
						array_push($stack, array($v['tag'],$obj));
					}
					else
					{
						die('invalid container');
					}

					$obj = array();
				}
				else
				{
					die('invalid element');
				}
			}
			else if ($v['type'] == 'complete')
			{
				if ($v['attributes']['type'] == 'string')
				{
					$value = $v['value'];
				}
				else if ($v['attributes']['type'] == 'boolean')
				{
					$value = $v['value'] == 'true';
				}
				else if ($v['attributes']['type'] == 'number')
				{
					$value = $v['value'];

					if (!is_numeric($value))
					{
						die('invalid numeric value');
					}

					$value = $value + 0; // converts to type int or float as required
				}
				else if ($v['attributes']['type'] == 'null')
				{
					$value = null;
				}
				else if ($v['attributes']['type'] == 'array')
				{
					$value = array();
				}
				else if ($v['attributes']['type'] == 'object')
				{
					$value = new \stdClass();
				}
				else
				{
					die('invalid value type');
				}

				if (is_null($obj))
				{
					$obj = $value;
				}
				else if (is_array($obj))
				{
					$obj[] = $value;
				}
				else if (is_object($obj))
				{
					if (isset($v['attributes']['item']))
					{
						$obj->$v['attributes']['item'] = $value;
					}
					else
					{
						$obj->$v['tag'] = $value;
					}
				}
				else
				{
					die('invalid container 2');
				}
			}
			else if ($v['type'] == 'close')
			{
				$prev = array_pop($stack);

				if (is_object($prev[1]))
				{
					$prev[1]->$prev[0] = $obj;
				}
				else if (is_array($prev[1]))
				{
					$prev[1][$prev[0]] = $obj;
				}
				else if (is_null($prev[1]))
				{
					break;
				}
				else
				{
					die('invalid container in stack');
				}

				$obj = $prev[1];
			}
			else
			{
				die('unknown parse part');
			}

		}

		self::last_error(self::XML_ERROR_NONE);
		return $obj;
	}
}
