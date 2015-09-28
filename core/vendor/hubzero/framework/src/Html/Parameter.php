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

namespace Hubzero\Html;

use Hubzero\Config\Registry;

/**
 * Parameter handler
 *
 * Inspired by Joomla's JPArameter class
 */
class Parameter extends Registry
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
	protected $_xml = null;

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
	 * Constructor
	 *
	 * @param   string  $data  The raw parms text.
	 * @param   string  $path  Path to the XML setup file.
	 * @return  void
	 */
	public function __construct($data = '', $path = '')
	{
		parent::__construct('_default');

		// Set base path.
		$this->_elementPath[] = __DIR__ . DS . 'Parameter' . DS . 'Element';

		if ($data = trim($data))
		{
			$this->parse($data);
		}

		if ($path)
		{
			$this->loadSetupFile($path);
		}

		$this->_raw = $data;
	}

	/**
	 * Sets a default value if not alreay assigned.
	 *
	 * @param   string  $key      The name of the parameter.
	 * @param   string  $default  An optional value for the parameter.
	 * @param   string  $group    An optional group for the parameter.
	 * @return  string  The value set, or the default if the value was not previously set (or null).
	 */
	public function def($key, $default = '', $group = '_default')
	{
		$value = $this->get($key, (string) $default, $group);

		return $this->set($key, $value);
	}

	/**
	 * Sets the XML object from custom XML files.
	 *
	 * @param   object  &$xml  An XML object.
	 * @return  void
	 */
	public function setXML(&$xml)
	{
		if (is_object($xml))
		{
			if ($group = $xml['group'])
			{
				$this->_xml[(string) $group] = $xml;
			}
			else
			{
				$this->_xml['_default'] = $xml;
			}

			if ($dir = $xml['addpath'])
			{
				$this->addElementPath(PATH_ROOT . str_replace('/', DS, (string) $dir));
			}
		}
	}

	/**
	 * Render the form control.
	 *
	 * @param   string  $name   An optional name of the HTML form control. The default is 'params' if not supplied.
	 * @param   string  $group  An optional group to render.  The default group is used if not supplied.
	 * @return  string  HTML
	 */
	public function render($name = 'params', $group = '_default')
	{
		if (!isset($this->_xml[$group]))
		{
			return false;
		}

		$params = $this->getParams($name, $group);
		$html = array();

		if ($description = $this->_xml[$group]['description'])
		{
			// Add the params description to the display
			$html[] = '<p class="paramrow_desc">' . \App::get('language')->txt((string) $description) . '</p>';
		}

		foreach ($params as $param)
		{
			if ($param[0])
			{
				$html[] = '<div class="input-wrap">';
				$html[] = $param[0];
				$html[] = $param[1];
				$html[] = '</div>';
			}
			else
			{
				$html[] = $param[1];
			}
		}

		if (count($params) < 1)
		{
			$html[] = '<p class="noparams">' . \App::get('language')->txt('JLIB_HTML_NO_PARAMETERS_FOR_THIS_ITEM') . '</p>';
		}

		return implode(PHP_EOL, $html);
	}

	/**
	 * Render all parameters to an array.
	 *
	 * @param   string  $name   An optional name of the HTML form control. The default is 'params' if not supplied.
	 * @param   string  $group  An optional group to render.  The default group is used if not supplied.
	 * @return  array
	 */
	public function renderToArray($name = 'params', $group = '_default')
	{
		if (!isset($this->_xml[$group]))
		{
			return false;
		}

		$results = array();

		foreach ($this->_xml[$group]->children() as $param)
		{
			$result = $this->getParam($param, $name, $group);
			$results[$result[5]] = $result;
		}

		return $results;
	}

	/**
	 * Return the number of parameters in a group.
	 *
	 * @param   string  $group  An optional group. The default group is used if not supplied.
	 * @return  mixed   False if no params exist or integer number of parameters that exist.
	 */
	public function getNumParams($group = '_default')
	{
		if (!isset($this->_xml[$group]) || !count($this->_xml[$group]->children()))
		{
			return false;
		}

		return count($this->_xml[$group]->children());
	}

	/**
	 * Get the number of params in each group.
	 *
	 * @return  array  Array of all group names as key and parameters count as value.
	 */
	public function getGroups()
	{
		if (!is_array($this->_xml))
		{

			return false;
		}

		$results = array();
		foreach ($this->_xml as $name => $group)
		{
			$results[$name] = $this->getNumParams($name);
		}

		return $results;
	}

	/**
	 * Render all parameters.
	 *
	 * @param   string  $name   An optional name of the HTML form control. The default is 'params' if not supplied.
	 * @param   string  $group  An optional group to render.  The default group is used if not supplied.
	 * @return  array   An array of all parameters, each as array of the label, the form element and the tooltip.
	 */
	public function getParams($name = 'params', $group = '_default')
	{
		if (!isset($this->_xml[$group]))
		{
			return false;
		}

		$results = array();
		foreach ($this->_xml[$group]->children() as $param)
		{
			$results[] = $this->getParam($param, $name, $group);
		}

		return $results;
	}

	/**
	 * Render a parameter type.
	 *
	 * @param   object  &$node         A parameter XML element.
	 * @param   string  $control_name  An optional name of the HTML form control. The default is 'params' if not supplied.
	 * @param   string  $group         An optional group to render.  The default group is used if not supplied.
	 * @return  array   Any array of the label, the form element and the tooltip.
	 */
	public function getParam(&$node, $control_name = 'params', $group = '_default')
	{
		// Get the type of the parameter.
		$type = (string) $node['type'];

		$element = $this->loadElement($type);

		// Check for an error.
		if ($element === false)
		{
			$result = array();
			$result[0] = (string) $node['name'];
			$result[1] = \App::get('language')->txt('Element not defined for type') . ' = ' . $type;
			$result[5] = $result[0];
			return $result;
		}

		// Get value.
		$value = $this->get((string) $node['name'], (string) $node['default'], $group);

		return $element->render($node, $value, $control_name);
	}

	/**
	 * Loads an XML setup file and parses it.
	 *
	 * @param   string  $path  A path to the XML setup file.
	 * @return  object
	 */
	public function loadSetupFile($path)
	{
		$result = false;

		if ($path)
		{
			if (!file_exists($path))
			{
				return $result;
			}

			$xml = simplexml_load_file($path);

			if ($params = $xml->params)
			{
				foreach ($params as $param)
				{
					$this->setXML($param);
					$result = true;
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
	 * Loads an element type.
	 *
	 * @param   string   $type  The element type.
	 * @param   boolean  $new   False (default) to reuse parameter elements; true to load the parameter element type again.
	 * @return  object
	 */
	public function loadElement($type, $new = false)
	{
		if ($type == 'list')
		{
			$type = 'select';
		}

		$signature = md5($type);

		if ((isset($this->_elements[$signature]) && !($this->_elements[$signature] instanceof __PHP_Incomplete_Class)) && $new === false)
		{
			return $this->_elements[$signature];
		}

		$elementClass = __NAMESPACE__ . '\\Parameter\\Element\\' . ucfirst($type);

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

			preg_match('/^[A-Za-z0-9_-]+[A-Za-z0-9_\.-]*([\\\\\/][A-Za-z0-9_-]+[A-Za-z0-9_\.-]*)*$/', str_replace('_', DS, $type) . '.php', $matches);
			$file = @ (string) $matches[0];

			if ($elementFile = \App::get('filesystem')->find($dirs, $file))
			{
				include_once $elementFile;
			}
			else
			{
				$false = false;
				return $false;
			}
		}

		if (!class_exists($elementClass))
		{
			$false = false;
			return $false;
		}

		$this->_elements[$signature] = new $elementClass($this);

		return $this->_elements[$signature];
	}

	/**
	 * Add a directory where Parameter should search for element types.
	 *
	 * You may either pass a string or an array of directories.
	 *
	 * Parameter will be searching for a element type in the same
	 * order you added them. If the parameter type cannot be found in
	 * the custom folders, it will look in
	 * Parameter/types.
	 *
	 * @param   mixed  $path  Directory (string) or directories (array) to search.
	 * @return  object
	 */
	public function addElementPath($path)
	{
		// Just force path to array.
		settype($path, 'array');

		// Loop through the path directories.
		foreach ($path as $dir)
		{
			// No surrounding spaces allowed!
			$dir = trim($dir);

			// Add trailing separators as needed.
			if (substr($dir, -1) != DIRECTORY_SEPARATOR)
			{
				// Directory
				$dir .= DIRECTORY_SEPARATOR;
			}

			// Add to the top of the search dirs.
			array_unshift($this->_elementPath, $dir);
		}

		return $this;
	}
}
