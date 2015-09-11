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

namespace Hubzero\Html;

use Hubzero\Base\Object;

/**
 * ToolBar handler
 *
 * Inspired by Joomla's JToolBar class
 */
class Toolbar extends Object
{
	/**
	 * Toolbar name
	 *
	 * @var  string
	 */
	protected $_name = '';

	/**
	 * Toolbar array
	 *
	 * @var  array
	 */
	protected $_bar = array();

	/**
	 * Loaded buttons
	 *
	 * @var  array
	 */
	protected $_buttons = array();

	/**
	 * Directories, where button types can be stored.
	 *
	 * @var  array
	 */
	protected $_buttonPath = array();

	/**
	 * Constructor
	 *
	 * @param   string  $name  The toolbar name
	 * @return  void
	 */
	public function __construct($name = 'toolbar')
	{
		$this->_name = $name;

		// Set base path to find buttons.
		$this->_buttonPath[] = __DIR__ . DS . 'Toolbar' . DS . 'Button';
	}

	/**
	 * Push button onto the end of the toolbar array.
	 *
	 * @return  string  The set value.
	 */
	public function appendButton()
	{
		$btn = func_get_args();

		array_push($this->_bar, $btn);

		return true;
	}

	/**
	 * Get the list of toolbar links.
	 *
	 * @return  array
	 */
	public function getItems()
	{
		return $this->_bar;
	}

	/**
	 * Get the name of the toolbar.
	 *
	 * @return  string
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Insert button into the front of the toolbar array.
	 *
	 * @return  boolean
	 */
	public function prependButton()
	{
		$btn = func_get_args();

		array_unshift($this->_bar, $btn);

		return true;
	}

	/**
	 * Render a tool bar.
	 *
	 * @return  string  HTML for the toolbar.
	 */
	public function render()
	{
		$html = array();

		// Start toolbar div.
		$html[] = '<div class="toolbar-list" id="' . $this->_name . '">';
		$html[] = '<ul>';

		foreach ($this->_bar as $key => $button)
		{
			$this->_bar[$key][9] = array();
			if ($button[0] == 'Separator')
			{
				continue;
			}
			if (!isset($this->_bar[$key - 1]) || $this->_bar[$key - 1][0] == 'Separator')
			{
				$this->_bar[$key][9][] = 'first';
			}
			if (!isset($this->_bar[$key + 1]) || $this->_bar[$key + 1][0] == 'Separator')
			{
				$this->_bar[$key][9][] = 'last';
			}
		}

		// Render each button in the toolbar.
		foreach ($this->_bar as $button)
		{
			$html[] = $this->renderButton($button);
		}

		// End toolbar div.
		$html[] = '</ul>';
		$html[] = '</div>';

		return implode("\n", $html);
	}

	/**
	 * Render a button.
	 *
	 * @param   object  &$node  A toolbar node.
	 * @return  string
	 */
	public function renderButton(&$node)
	{
		// Get the button type.
		$type = $node[0];

		$button = $this->loadButtonType($type);

		// Check for error.
		if ($button === false)
		{
			return \Lang::txt('JLIB_HTML_BUTTON_NOT_DEFINED', $type);
		}
		return $button->render($node);
	}

	/**
	 * Loads a button type.
	 *
	 * @param   string   $type  Button Type
	 * @param   boolean  $new   False by default
	 * @return  object
	 */
	public function loadButtonType($type, $new = false)
	{
		$signature = md5($type);
		if (isset($this->_buttons[$signature]) && $new === false)
		{
			return $this->_buttons[$signature];
		}

		$buttonClass = __NAMESPACE__ . '\\Toolbar\\Button\\' . $type;
		if (!class_exists($buttonClass))
		{
			$dirs = isset($this->_buttonPath) ? $this->_buttonPath : array();
			$file = preg_replace('/[^A-Z0-9_\.-]/i', '', str_replace('_', DIRECTORY_SEPARATOR, strtolower($type)))  . '.php';

			if ($buttonFile = $this->find($dirs, $file))
			{
				include_once $buttonFile;
			}
			else
			{
				throw new \InvalidArgumentException(\Lang::txt('JLIB_HTML_BUTTON_NO_LOAD', $buttonClass, $buttonFile), 500);
			}
		}

		if (!class_exists($buttonClass))
		{
			throw new \Exception("Module file $buttonFile does not contain class $buttonClass.", 500);
		}

		$this->_buttons[$signature] = new $buttonClass($this);

		return $this->_buttons[$signature];
	}

	/**
	 * Searches the directory paths for a given file.
	 *
	 * @param   mixed   $paths  An path string or array of path strings to search in
	 * @param   string  $file   The file name to look for.
	 * @return  mixed   The full path and file name for the target file, or boolean false if the file is not found in any of the paths.
	 */
	protected function find($paths, $file)
	{
		settype($paths, 'array'); //force to array

		// Start looping through the path set
		foreach ($paths as $path)
		{
			// Get the path to the file
			$fullname = $path . '/' . $file;

			// Is the path based on a stream?
			if (strpos($path, '://') === false)
			{
				// Not a stream, so do a realpath() to avoid directory
				// traversal attempts on the local file system.
				$path = realpath($path); // needed for substr() later
				$fullname = realpath($fullname);
			}

			// The substr() check added to make sure that the realpath()
			// results in a directory registered so that
			// non-registered directories are not accessible via directory
			// traversal attempts.
			if (file_exists($fullname) && substr($fullname, 0, strlen($path)) == $path)
			{
				return $fullname;
			}
		}

		// Could not find the file in the set of paths
		return false;
	}

	/**
	 * Add a directory where ToolBar should search for button types in LIFO order.
	 *
	 * You may either pass a string or an array of directories.
	 *
	 * Toolbar will be searching for an element type in the same order you
	 * added them. If the parameter type cannot be found in the custom folders,
	 * it will look in __DIR__ . /toolbar/button.
	 *
	 * @param   mixed  $path  Directory or directories to search.
	 * @return  void
	 */
	public function addButtonPath($path)
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
			array_unshift($this->_buttonPath, $dir);
		}
	}
}
