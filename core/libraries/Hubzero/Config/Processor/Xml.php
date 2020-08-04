<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
	 * Returns an array of allowed file extensions for this parser
	 *
	 * @return  array
	 */
	public function getSupportedExtensions()
	{
		return array('xml');
	}

	/**
	 * Parses an XML file as an array
	 *
	 * @param   string  $path
	 * @return  array
	 * @throws  ParseException  If there is an error parsing the XML file
	 */
	public function parse($path)
	{
		libxml_use_internal_errors(true);

		$xml = simplexml_load_file($path, null, LIBXML_NOERROR);

		if ($xml === false)
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

		$data = new stdClass;
		foreach ($xml->children() as $node)
		{
			$data->{$node['name']} = $this->getValueFromNode($node);
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
		if (is_string($object))
		{
			return $object;
		}

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
		if (is_object($data))
		{
			return $data;
		}

		$obj = new stdClass;

		$xml = simplexml_load_string($data);

		foreach ($xml->children() as $node)
		{
			$obj->{$node['name']} = $this->getValueFromNode($node);
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
					$value->{$child['name']} = $this->getValueFromNode($child);
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
