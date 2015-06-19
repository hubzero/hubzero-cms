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
use stdClass;

/**
 * XML Processor for Registry.
 */
class Xml extends Base
{
	/**
	 * {@inheritDoc}
	 */
	public function getSupportedExtensions()
	{
		return array('xml');
	}

	/**
	 * {@inheritDoc}
	 * Parses an XML file as an array
	 *
	 * @throws ParseException If there is an error parsing the XML file
	 */
	public function parse($path)
	{
		libxml_use_internal_errors(true);

		$data = simplexml_load_file($path, null, LIBXML_NOERROR);

		if ($data === false)
		{
			$errors      = libxml_get_errors();
			$latestError = array_pop($errors);
			$error       = array(
				'message' => $latestError->message,
				'type'    => $latestError->level,
				'code'    => $latestError->code,
				'file'    => $latestError->file,
				'line'    => $latestError->line,
			);
			throw new ParseException($error);
		}

		$data = json_decode(json_encode($data), true);

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

		if ((substr($data, 0, 1) != '<') && (substr($data, -1, 1) != '>'))
		{
			return false;
		}

		return true;
	}

	/**
	 * Converts an object into an XML formatted string.
	 *
	 * @param   object  $object   Data source object.
	 * @param   array   $options  Options used by the formatter.
	 * @return  string  XML formatted string.
	 */
	public function objectToString($object, $options = array())
	{
		// Initialise variables.
		$rootName = (isset($options['name'])) ? $options['name'] : 'registry';
		$nodeName = (isset($options['nodeName'])) ? $options['nodeName'] : 'node';

		// Create the root node.
		$root = simplexml_load_string('<' . $rootName . ' />');

		// Iterate over the object members.
		$this->getXmlChildren($root, $object, $nodeName);

		return $root->asXML();
	}

	/**
	 * Parse a XML formatted string and convert it into an object.
	 *
	 * @param   string  $data     XML formatted string to convert.
	 * @param   array   $options  Options used by the formatter.
	 * @return  object  Data object.
	 */
	public function stringToObject($data, $options = array())
	{
		$obj = new stdClass;

		$xml = simplexml_load_string($data);

		foreach ($xml->children() as $node)
		{
			$obj->$node['name'] = $this->getValueFromNode($node);
		}

		return $obj;
	}

	/**
	 * Method to get a PHP native value for a SimpleXMLElement object. -- called recursively
	 *
	 * @param   object  $node  SimpleXMLElement object for which to get the native value.
	 * @return  mixed   Native value of the SimpleXMLElement object.
	 */
	protected function getValueFromNode($node)
	{
		switch ($node['type'])
		{
			case 'integer':
				$value = (string) $node;
				return (int) $value;
				break;
			case 'string':
				return (string) $node;
				break;
			case 'boolean':
				$value = (string) $node;
				return (bool) $value;
				break;
			case 'double':
				$value = (string) $node;
				return (float) $value;
				break;
			case 'array':
				$value = array();
				foreach ($node->children() as $child)
				{
					$value[(string) $child['name']] = $this->getValueFromNode($child);
				}
				break;
			default:
				$value = new stdClass;
				foreach ($node->children() as $child)
				{
					$value->$child['name'] = $this->getValueFromNode($child);
				}
				break;
		}

		return $value;
	}

	/**
	 * Method to build a level of the XML string -- called recursively
	 *
	 * @param   object  &$node     SimpleXMLElement object to attach children.
	 * @param   object  $var       Object that represents a node of the XML document.
	 * @param   string  $nodeName  The name to use for node elements.
	 * @return  void
	 */
	protected function getXmlChildren(&$node, $var, $nodeName)
	{
		// Iterate over the object members.
		foreach ((array) $var as $k => $v)
		{
			if (is_scalar($v))
			{
				$n = $node->addChild($nodeName, $v);
				$n->addAttribute('name', $k);
				$n->addAttribute('type', gettype($v));
			}
			else
			{
				$n = $node->addChild($nodeName);
				$n->addAttribute('name', $k);
				$n->addAttribute('type', gettype($v));

				$this->getXmlChildren($n, $v, $nodeName);
			}
		}
	}
}
