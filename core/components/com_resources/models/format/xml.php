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
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models\Format;

use Components\Resources\Models\Format as Base;
use stdClass;

/**
 * XML format handler
 */
class XML extends Base
{
	/**
	 * Converts an object into an XML formatted string.
	 *	-	If more than two levels of nested groups are necessary, since INI is not
	 *		useful, XML or another format should be used.
	 *
	 * @param   object  $object   Data source object.
	 * @param   array   $options  Options used by the formatter.
	 * @return  string  XML formatted string.
	 */
	public function objectToString($object, $options = array())
	{
		// Initialise variables.
		$rootName = (isset($options['name']))     ? $options['name']     : 'registry';
		$nodeName = (isset($options['nodeName'])) ? $options['nodeName'] : 'node';

		// Create the root node.
		$root = simplexml_load_string('<' . $rootName . ' />');

		// Iterate over the object members.
		foreach ((array) $object as $k => $v)
		{
			if (is_scalar($v))
			{
				$n = $root->addChild($nodeName, $v);
				$n->addAttribute('name', $k);
				$n->addAttribute('type', gettype($v));
			}
			else
			{
				$n = $root->addChild($nodeName);
				$n->addAttribute('name', $k);
				$n->addAttribute('type', gettype($v));

				$this->getXmlChildren($n, $v, $nodeName);
			}
		}

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
		// Initialize variables.
		$obj = new stdClass;

		// Parse the XML string.
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
		switch ($node['type']) {
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
