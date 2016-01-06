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
use Hubzero\Config\Registry;
use Plugin;
use Lang;

/**
 * Editor class to handle WYSIWYG editors
 *
 * Inspired by Joomla's JEditor class
 */
class Editor extends Object
{
	/**
	 * The state of the observable object
	 *
	 * @var  mixed
	 */
	protected $state = null;

	/**
	 * Editor Plugin object
	 *
	 * @var  object
	 */
	protected $editor = null;

	/**
	 * Editor Plugin name
	 *
	 * @var  string
	 */
	protected $name = null;

	/**
	 * Object asset
	 *
	 * @var  string
	 */
	protected $asset = null;

	/**
	 * Object author
	 *
	 * @var  string
	 */
	protected $author = null;

	/**
	 * Editor instances container.
	 *
	 * @var  array
	 */
	protected static $instances = array();

	/**
	 * Constructor
	 *
	 * @param   string  $editor  The editor name
	 * @return  void
	 */
	public function __construct($editor = 'none')
	{
		$this->name = $editor;
	}

	/**
	 * Returns the global Editor object, only creating it
	 * if it doesn't already exist.
	 *
	 * @param   string  $editor  The editor to use.
	 * @return  object  The Editor object.
	 */
	public static function getInstance($editor = 'none')
	{
		$signature = serialize($editor);

		if (empty(self::$instances[$signature]))
		{
			self::$instances[$signature] = new self($editor);
		}

		return self::$instances[$signature];
	}

	/**
	 * Get the name of the Editor
	 *
	 * @return  string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get the state of the Editor object
	 *
	 * @return  mixed  The state of the object.
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * Attach an observer object
	 *
	 * @param   object  $observer  An observer object to attach
	 * @return  void
	 */
	public function attach($observer)
	{
		// [!] For Joomla compatibility only
	}

	/**
	 * Detach an observer object
	 *
	 * @param   object   $observer  An observer object to detach.
	 * @return  boolean  True if the observer object was detached.
	 */
	public function detach($observer)
	{
		// [!] For Joomla compatibility only
	}

	/**
	 * Initialise the editor
	 *
	 * @return  void
	 */
	public function initialise()
	{
		if (is_null($this->editor))
		{
			return;
		}

		$return = '';
		$results[] = $this->editor->onInit();

		foreach ($results as $result)
		{
			if (trim($result))
			{
				//$return .= $result;
				$return = $result;
			}
		}

		$document = \App::get('document');
		if ($document->getType() != 'html')
		{
			return;
		}
		$document->addCustomTag($return);
	}

	/**
	 * Display the editor area.
	 *
	 * @param   string   $name     The control name.
	 * @param   string   $html     The contents of the text area.
	 * @param   string   $width    The width of the text area (px or %).
	 * @param   string   $height   The height of the text area (px or %).
	 * @param   integer  $col      The number of columns for the textarea.
	 * @param   integer  $row      The number of rows for the textarea.
	 * @param   boolean  $buttons  True and the editor buttons will be displayed.
	 * @param   string   $id       An optional ID for the textarea (note: since 1.6). If not supplied the name is used.
	 * @param   string   $asset    The object asset
	 * @param   object   $author   The author.
	 * @param   array    $params   Associative array of editor parameters.
	 * @return  string
	 */
	public function display($name, $html, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null, $params = array())
	{
		$this->asset  = $asset;
		$this->author = $author;
		$this->load($params);

		// Check whether editor is already loaded
		if (is_null($this->editor))
		{
			return;
		}

		// Backwards compatibility. Width and height should be passed without a semicolon from now on.
		// If editor plugins need a unit like "px" for CSS styling, they need to take care of that
		$width  = str_replace(';', '', $width);
		$height = str_replace(';', '', $height);

		$id = $id ?: $name;

		// Initialise variables.
		$return = null;

		$results[] = $this->editor->onDisplay($name, $html, $width, $height, $col, $row, $buttons, $id, $asset, $author, $params);

		foreach ($results as $result)
		{
			if (trim($result))
			{
				$return .= $result;
			}
		}
		return $return;
	}

	/**
	 * Save the editor content
	 *
	 * @param   string  $editor  The name of the editor control
	 * @return  string
	 */
	public function save($editor)
	{
		$this->load();

		// Check whether editor is already loaded
		if (is_null($this->editor))
		{
			return;
		}

		$return = '';
		$results[] = $this->editor->onSave($editor);

		foreach ($results as $result)
		{
			if (trim($result))
			{
				$return .= $result;
			}
		}

		return $return;
	}

	/**
	 * Get the editor contents
	 *
	 * @param   string  $editor  The name of the editor control
	 * @return  string
	 */
	public function getContent($editor)
	{
		$this->load();

		$return = '';
		$results[] = $this->editor->onGetContent($editor);

		foreach ($results as $result)
		{
			if (trim($result))
			{
				$return .= $result;
			}
		}

		return $return;
	}

	/**
	 * Set the editor contents
	 *
	 * @param   string  $editor  The name of the editor control
	 * @param   string  $html    The contents of the text area
	 * @return  string
	 */
	public function setContent($editor, $html)
	{
		$this->load();

		$return = '';
		$results[] = $this->editor->onSetContent($editor, $html);

		foreach ($results as $result)
		{
			if (trim($result))
			{
				$return .= $result;
			}
		}

		return $return;
	}

	/**
	 * Get the editor extended buttons (usually from plugins)
	 *
	 * @param   string  $editor   The name of the editor.
	 * @param   mixed   $buttons  Can be boolean or array, if boolean defines if the buttons are
	 *                            displayed, if array defines a list of buttons not to show.
	 * @return  array
	 */
	public function getButtons($editor, $buttons = true)
	{
		$result = array();

		if (is_bool($buttons) && !$buttons)
		{
			return $result;
		}

		// Get plugins
		$plugins = Plugin::byType('editors-xtd');

		foreach ($plugins as $plugin)
		{
			if (is_array($buttons) && in_array($plugin->name, $buttons))
			{
				continue;
			}

			Plugin::import('editors-xtd', $plugin->name, false);
			$className = 'plgButton' . $plugin->name;

			if (class_exists($className))
			{
				$plugin = new $className($this, (array) $plugin);
			}

			// Try to authenticate
			if ($temp = $plugin->onDisplay($editor, $this->asset, $this->author))
			{
				$result[] = $temp;
			}
		}

		return $result;
	}

	/**
	 * Load the editor
	 *
	 * @param   array  $config  Associative array of editor config paramaters
	 * @return  mixed
	 */
	protected function load($config = array())
	{
		// Check whether editor is already loaded
		if (!is_null($this->editor))
		{
			return;
		}

		// Build the path to the needed editor plugin
		$name = (string) preg_replace('/[^A-Z0-9_\.-]/i', '', $this->name);
		$name = ltrim($name, '.');

		$path = PATH_APP . '/plugins/editors/' . $name . '/' . $name . '.php';
		if (!is_file($path))
		{
			$path = PATH_CORE . '/plugins/editors/' . $name . '/' . $name . '.php';
			if (!is_file($path))
			{
				\Notify::error(Lang::txt('JLIB_HTML_EDITOR_CANNOT_LOAD'));
				return false;
			}
		}

		// Require plugin file
		require_once $path;

		// Get the plugin
		$plugin = Plugin::byType('editors', $this->name);

		$params = new Registry($plugin->params);
		$params->merge($config);

		$plugin->params = $params;

		// Build editor plugin classname
		$name = 'plgEditor' . $this->name;

		if ($this->editor = new $name($this, (array) $plugin))
		{
			// Load plugin parameters
			$this->initialise();

			Plugin::import('editors-xtd');
		}
	}
}
