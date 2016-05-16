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

namespace Components\Resources\Models;

use stdClass;

include_once(__DIR__ . DS . 'format.php');
include_once(__DIR__ . DS . 'element.php');

/**
 * Resources elements class
 * Used for rendering custom resources fields
 */
class Elements
{
	/**
	 * The raw params string
	 *
	 * @var  string
	 */
	protected $_raw = null;

	/**
	 * The XML params element
	 *
	 * @var  object
	 */
	protected $_schema = null;

	/**
	 * Loaded elements
	 *
	 * @var  array
	 */
	protected $_elements = array();

	/**
	 * Directories, where element types can be stored
	 *
	 * @var  array
	 */
	protected $_elementPath = array();

	/**
	 * Registry Object
	 *
	 * @var  object
	 */
	protected $data;

	/**
	 * Constructor
	 *
	 * @param   mixed  $data  The data to bind to the new object.
	 * @return  void
	 */
	public function __construct($data = null, $setup = null)
	{
		$this->_elementPath[] = __DIR__ . DS . 'element';

		$this->_raw = $data;

		// Instantiate the internal data object.
		$this->data = new stdClass;

		// Optionally load supplied data.
		if (is_array($data) || is_object($data))
		{
			$this->bindData($this->data, $data);
		}
		elseif (!empty($data) && is_string($data))
		{
			$this->loadString($data);
		}

		if ($setup)
		{
			$this->loadSetup($setup);
		}
	}

	/**
	 * Magic function to clone the registry object.
	 *
	 * @return  void
	 */
	public function __clone()
	{
		$this->data = unserialize(serialize($this->data));
	}

	/**
	 * Magic function to render this object as a string using default args of toString method.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * Sets a default value if not already assigned.
	 *
	 * @param   string  $key      The name of the parameter.
	 * @param   string  $default  An optional value for the parameter.
	 * @return  string  The value set, or the default if the value was not previously set (or null).
	 */
	public function def($key, $default = '')
	{
		$value = $this->get($key, (string) $default);
		$this->set($key, $value);
		return $value;
	}

	/**
	 * Check if a registry path exists.
	 *
	 * @param   string  $path  Registry path
	 * @return  boolean
	 */
	public function exists($path)
	{
		// Explode the registry path into an array
		if ($nodes = explode('.', $path))
		{
			// Initialize the current node to be the registry root.
			$node = $this->data;

			// Traverse the registry to find the correct node for the result.
			for ($i = 0,$n = count($nodes); $i < $n; $i++)
			{
				if (isset($node->$nodes[$i]))
				{
					$node = $node->$nodes[$i];
				}
				else
				{
					break;
				}

				if ($i+1 == $n)
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Get a registry value.
	 *
	 * @param   string  $path     Registry path
	 * @param   mixed   $default  Optional default value, returned if the internal value is null.
	 * @return  mixed   Value of entry or null
	 */
	public function get($path, $default = null)
	{
		// Initialise variables.
		$result = $default;

		if (!strpos($path, '.'))
		{
			return (isset($this->data->$path) && $this->data->$path !== null && $this->data->$path !== '') ? $this->data->$path : $default;
		}
		// Explode the registry path into an array
		$nodes = explode('.', $path);

		// Initialize the current node to be the registry root.
		$node = $this->data;
		$found = false;
		// Traverse the registry to find the correct node for the result.
		foreach ($nodes as $n)
		{
			if (isset($node->$n))
			{
				$node = $node->$n;
				$found = true;
			}
			else
			{
				$found = false;
				break;
			}
		}
		if ($found && $node !== null && $node !== '')
		{
			$result = $node;
		}

		return $result;
	}

	/**
	 * Returns a reference to a global Elements object, only creating it
	 * if it doesn't already exist.
	 *
	 * This method must be invoked as:
	 * <pre>$registry = Elements:getInstance($id);</pre>
	 *
	 * @param   string  $id  An ID for the registry instance
	 * @return  object
	 */
	public static function getInstance($id)
	{
		static $instances;

		if (!isset ($instances))
		{
			$instances = array();
		}

		if (empty ($instances[$id]))
		{
			$instances[$id] = new self;
		}

		return $instances[$id];
	}

	/**
	 * Load a associative array of values into the default namespace
	 *
	 * @param   array    $array  Associative array of value to load
	 * @return  boolean  True on success
	 */
	public function loadArray($array)
	{
		$this->bindData($this->data, $array);

		return true;
	}

	/**
	 * Load the public variables of the object into the default namespace.
	 *
	 * @param   object   $object  The object holding the publics to load
	 * @return  boolean  True on success
	 */
	public function loadObject($object)
	{
		$this->bindData($this->data, $object);

		return true;
	}

	/**
	 * Load the contents of a file into the registry
	 *
	 * @param   string   $file     Path to file to load
	 * @param   string   $format   Format of the file [optional: defaults to JSON]
	 * @param   mixed    $options  Options used by the formatter
	 * @return  boolean  True on success
	 */
	public function loadFile($file, $format = 'JSON', $options = array())
	{
		// Get the contents of the file
		$data = \Filesystem::read($file);

		return $this->loadString($data, $format, $options);
	}

	/**
	 * Load a string into the registry
	 *
	 * @param   string   $data     String to load into the registry
	 * @param   string   $format   Format of the string
	 * @param   mixed    $options  Options used by the formatter
	 * @return  boolean  True on success
	 */
	public function loadString($data, $format = 'JSON', $options = array())
	{
		// Load a string into the given namespace [or default namespace if not given]
		$handler = Format::getInstance($format);

		$obj = $handler->stringToObject($data, $options);
		$this->loadObject($obj);

		return true;
	}

	/**
	 * Loads an XML setup file and parses it.
	 *
	 * @param   string  A path to the XML setup file.
	 * @return  object
	 */
	public function loadSetup($setup, $group = '_default')
	{
		$setup = trim($setup);

		$result = false;

		if ($setup)
		{
			// Legacy support
			if ((substr($setup, 0, 1) != '{') && (substr($setup, -1, 1) != '}'))
			{
				$obj = new stdClass();
				$obj->fields = array();

				$fs = explode("\n", trim($setup));
				foreach ($fs as $f)
				{
					$field = explode('=', $f);

					$element = new stdClass();
					$element->name     = $field[0];
					$element->label    = $field[1];
					$element->type     = $field[2];
					$element->required = $field[3];
					$element->value    = preg_replace('/<br\\s*?\/??>/i', "", end($field));
					$element->default  = '';
					$element->description = '';

					$obj->fields[] = $element;
				}

				$this->_schema[$group] = $obj;
			}
			else
			{
				$handler = Format::getInstance('JSON');
				if ($obj = $handler->stringToObject($setup, array()))
				{
					$this->_schema[$group] = $obj;
				}
			}
		}
		else
		{
			$result = true;
		}

		return $result;
	}

	/**
	 * Merge an element object into this one
	 *
	 * @param   object   &$source  Source element object to merge.
	 * @return  boolean  True on success
	 */
	public function merge(&$source)
	{
		if ($source instanceof Elements)
		{
			// Load the variables into the registry's default namespace.
			foreach ($source->toArray() as $k => $v)
			{
				if (($v !== null) && ($v !== ''))
				{
					$this->data->$k = $v;
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * Set a registry value.
	 *
	 * @param   string  $path   Registry Path
	 * @param   mixed   $value  Value of entry
	 * @return  mixed   The value of the that has been set.
	 */
	public function set($path, $value)
	{
		$result = null;

		// Explode the registry path into an array
		if ($nodes = explode('.', $path))
		{
			// Initialize the current node to be the registry root.
			$node = $this->data;

			// Traverse the registry to find the correct node for the result.
			for ($i = 0, $n = count($nodes) - 1; $i < $n; $i++)
			{
				if (!isset($node->$nodes[$i]) && ($i != $n))
				{
					$node->$nodes[$i] = new stdClass;
				}
				$node = $node->$nodes[$i];
			}

			// Get the old value if exists so we can return it
			$result = $node->$nodes[$i] = $value;
		}

		return $result;
	}

	/**
	 * Transforms a namespace to an array
	 *
	 * @return  array  An associative array holding the namespace data
	 */
	public function toArray()
	{
		return (array) $this->asArray($this->data);
	}

	/**
	 * Transforms a namespace to an object
	 *
	 * @return  object  An an object holding the namespace data
	 */
	public function toObject()
	{
		return $this->data;
	}

	/**
	 * Get a namespace in a given string format
	 *
	 * @param   string  $format   Format to return the string in
	 * @param   mixed   $options  Parameters used by the formatter, see formatters for more info
	 * @return  string  Namespace in string format
	 */
	public function toString($format = 'JSON', $options = array())
	{
		// Return a namespace in a given format
		$handler = Format::getInstance($format);

		return $handler->objectToString($this->data, $options);
	}

	/**
	 * Method to convert fields to html content
	 *
	 * @return  string
	 */
	public function toDatabaseHtml()
	{
		// get fields
		$schema = $this->getSchema();

		// var to hold html nodes
		$html = array();

		// if we have schema
		if (isset($schema->fields) && is_array($schema->fields))
		{
			// loop through each field
			foreach ($schema->fields as $index => $field)
			{
				// load resource element by type
				$resourceElement = $this->loadElement($field->type);

				// if this is hidden lets use option value as default
				if ($field->type == 'hidden')
				{
					$field->default = (isset($field->options[0])) ? $field->options[0]->value : null;
				}

				// Get key and value
				$key   = $field->name;
				$value = $this->get($field->name, $field->default);

				// convert element to html tag
				array_push($html, $resourceElement->toHtmlTag($key, $value));
			}
		}

		// return html
		return implode("\n", $html);
	}

	/**
	 * Method to recursively bind data to a parent object.
	 *
	 * @param   object  &$parent  The parent object on which to attach the data values.
	 * @param   mixed   $data     An array or object of data to bind to the parent object.
	 * @return  void
	 */
	protected function bindData(&$parent, $data)
	{
		// Ensure the input data is an array.
		if (is_object($data))
		{
			$data = get_object_vars($data);
		}
		else
		{
			$data = (array) $data;
		}

		foreach ($data as $k => $v)
		{
			if ((is_array($v) && $this->isAssociative($v)) || is_object($v))
			{
				$parent->$k = new stdClass;
				$this->bindData($parent->$k, $v);
			}
			else
			{
				$parent->$k = $v;
			}
		}
	}

	/**
	 * Method to determine if an array is an associative array.
	 *
	 * @param   array    $array  An array to test.
	 * @return  boolean  True if the array is an associative array.
	 */
	public function isAssociative($array)
	{
		if (is_array($array))
		{
			foreach (array_keys($array) as $k => $v)
			{
				if ($k !== $v)
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Method to recursively convert an object of data to an array.
	 *
	 * @param   object  $data  An object of data to return as an array.
	 * @return  array   Array representation of the input object.
	 */
	protected function asArray($data)
	{
		$array = array();

		foreach (get_object_vars((object) $data) as $k => $v)
		{
			if (is_object($v))
			{
				$array[$k] = $this->asArray($v);
			}
			else
			{
				$array[$k] = $v;
			}
		}

		return $array;
	}

	/**
	 * Render the form control.
	 *
	 * @param   string  $name   An optional name of the HTML form control. The default is 'params' if not supplied.
	 * @param   string  $group  An optional group to render.  The default group is used if not supplied.
	 * @return  string  HTML
	 */
	public function render($name = 'nbtag', $group = '_default')
	{
		if (!isset($this->_schema[$group]))
		{
			return false;
		}

		$fields = $this->getElements($name);

		$html = array();

		/*if ($description = $this->_schema[$group]->description)
		{
			// Add the params description to the display
			$desc   = Lang::txt($description);
			$html[] = '<p class="paramrow_desc">'.$desc.'</p>';
		}*/

		if (count($fields) > 0)
		{
			foreach ($fields as $field)
			{
				if ($field->label)
				{
					$html[] = $field->label;
				}
				$html[] = $field->element;
			}
		}

		return implode(PHP_EOL, $html);
	}

	/**
	 * Render an element value.
	 *
	 * @param   mixed   $node   An element.
	 * @param   string  $value  Value to display.
	 * @return  string
	 */
	public function display($node, $value='')
	{
		// Get the type of the parameter.
		if (is_object($node))
		{
			$type = $node->type;
		}
		else if (is_array($node))
		{
			$type = $node['type'];
		}
		else if (is_string($node))
		{
			$type = $node;
		}

		$element = $this->loadElement($type);

		// Check for an error.
		if ($element === false)
		{
			return $value;
		}

		return $element->display($value);
	}

	/**
	 * Render all parameters to an array.
	 *
	 * @param   string  $name   An optional name of the HTML form control. The default is 'params' if not supplied.
	 * @param   string  $group  An optional group to render.  The default group is used if not supplied.
	 * @return  array
	 */
	public function renderToArray($name = 'nbtag', $group = '_default')
	{
		if (!isset($this->_schema[$group]))
		{
			return false;
		}

		$results = array();
		foreach ($this->_schema[$group]->fields as $element)
		{
			$result = $this->getElement($element, $name, $group);
			$results[$result->name] = $result;
		}

		return $results;
	}

	/**
	 * Return the schema
	 *
	 * @param   string  $group  An optional group to render.  The default group is used if not supplied.
	 * @return  object
	 */
	public function getSchema($group = '_default')
	{
		if (!isset($this->_schema[$group]))
		{
			return false;
		}

		return $this->_schema[$group];
	}

	/**
	 * Render all parameters.
	 *
	 * @param   string  $name   An optional name of the HTML form control. The default is 'params' if not supplied.
	 * @param   string  $group  An optional group to render.  The default group is used if not supplied.
	 * @return  array   An array of all parameters, each as array of the label, the form element and the tooltip.
	 */
	public function getElements($name = 'nbtag', $group = '_default')
	{
		if (!isset($this->_schema[$group]))
		{
			return false;
		}

		$results = array();
		foreach ($this->_schema[$group]->fields as $element)
		{
			$results[] = $this->getElement($element, $name, $group);
		}

		return $results;
	}

	/**
	 * Render a parameter type.
	 *
	 * @param   object  $node          A parameter XML element.
	 * @param   string  $control_name  An optional name of the HTML form control. The default is 'params' if not supplied.
	 * @param   string  $group         An optional group to render.  The default group is used if not supplied.
	 * @return  array   Any array of the label, the form element and the tooltip.
	 */
	public function getElement(&$node, $control_name = 'nbtag', $group = '_default')
	{
		// Get the type of the parameter.
		$type = $node->type;

		$element = $this->loadElement($type);

		// Check for an error.
		if ($element === false)
		{
			$result = new stdClass;
			$result->label       = $node->label;
			$result->element     = Lang::txt('Element not defined for type').' = '.$type;
			$result->description = '';
			$result->text        = $result->label;
			$result->name        = $result->name;
			$result->default     = '';
			$result->type        = $type;
			return $result;
		}

		// Get value.
		$value = $this->get($node->name, $node->default, $group);

		return $element->render($node, $value, $control_name);
	}

	/**
	 * Render a parameter type.
	 *
	 * @param   object  $node          A parameter XML element.
	 * @param   string  $control_name  An optional name of the HTML form control. The default is 'params' if not supplied.
	 * @param   string  $group         An optional group to render.  The default group is used if not supplied.
	 * @return  array   Any array of the label, the form element and the tooltip.
	 */
	public function getElementOptions($name, &$node, $control_name = 'nbtag', $group = '_default')
	{
		// Get the type of the parameter.
		$type = $node->type;

		$element = $this->loadElement($type);

		// Check for an error.
		if ($element === false)
		{
			$result = new stdClass;
			$result->label   = $node->label;
			$result->element = Lang::txt('Element not defined for type').' = '.$type;
			$result->description = '';
			$result->text    = $result->label;
			$result->name    = $result->label;
			$result->default = '';
			$result->type    = $type;

			return $result;
		}

		// Get value.
		$value = $this->get($node->name, $node->default, $group);

		return $element->fetchOptions($name, $value, $node, $control_name);
	}

	/**
	 * Loads an element type.
	 *
	 * @param   string   The element type.
	 * @param   boolean  False (default) to reuse parameter elements; true to load the parameter element type again.
	 * @return  object
	 */
	public function loadElement($type, $new = false)
	{
		if ($type == 'list')
		{
			$type = 'select';
		}

		$signature = md5($type);

		if ((isset($this->_elements[$signature]) && !($this->_elements[$signature] instanceof __PHP_Incomplete_Class))  && $new === false)
		{
			return $this->_elements[$signature];
		}

		$elementClass = __NAMESPACE__ . '\\Element\\' . $type;

		if (!class_exists($elementClass))
		{
			if (isset($this->_elementPath))
			{
				$dirs = $this->_elementPath;
			}
			else
			{
				$dirs = array();
			}

			$source = str_replace('_', DS, $type) . '.php';
			preg_match('/^[A-Za-z0-9_-]+[A-Za-z0-9_\.-]*([\\\\\/][A-Za-z0-9_-]+[A-Za-z0-9_\.-]*)*$/', (string) $source, $matches);
			$file = @ (string) $matches[0];

			if ($elementFile = \Filesystem::find($dirs, $file))
			{
				include_once $elementFile;
			}
			else
			{
				return false;
			}
		}

		if (!class_exists($elementClass))
		{
			return false;
		}

		$this->_elements[$signature] = new $elementClass($this);

		return $this->_elements[$signature];
	}
}
