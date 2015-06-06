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

namespace Hubzero\Html;

use Hubzero\Base\Object;
use Hubzero\Config\Registry;
use Plugin;
use Lang;

/**
 * Editor class to handle WYSIWYG editors
 */
class Editor extends Object
{
	/**
	 * An array of Observer objects to notify
	 *
	 * @var    array
	 * @since  11.1
	 */
	//protected $observers = array();

	/**
	 * The state of the observable object
	 *
	 * @var    mixed
	 * @since  11.1
	 */
	protected $state = null;

	/**
	 * A multi dimensional array of [function][] = key for observers
	 *
	 * @var    array
	 * @since  11.1
	 */
	//protected $methods = array();

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
	 * @var    array  JEditor instances container.
	 * @since  11.3
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
	 * Get the state of the Editor object
	 *
	 * @return  mixed    The state of the object.
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
		/*if (is_array($observer))
		{
			if (!isset($observer['handler']) || !isset($observer['event']) || !is_callable($observer['handler']))
			{
				return;
			}

			// Make sure we haven't already attached this array as an observer
			foreach ($this->_observers as $check)
			{
				if (is_array($check) && $check['event'] == $observer['event'] && $check['handler'] == $observer['handler'])
				{
					return;
				}
			}

			$this->_observers[] = $observer;
			end($this->_observers);
			$methods = array($observer['event']);
		}
		else
		{
			if (!($observer instanceof Editor))
			{
				return;
			}

			// Make sure we haven't already attached this object as an observer
			$class = get_class($observer);

			foreach ($this->_observers as $check)
			{
				if ($check instanceof $class)
				{
					return;
				}
			}

			$this->_observers[] = $observer;
			$methods = array_diff(get_class_methods($observer), get_class_methods('JPlugin'));
		}

		$key = key($this->_observers);

		foreach ($methods as $method)
		{
			$method = strtolower($method);

			if (!isset($this->_methods[$method]))
			{
				$this->_methods[$method] = array();
			}

			$this->_methods[$method][] = $key;
		}*/
	}

	/**
	 * Detach an observer object
	 *
	 * @param   object   $observer  An observer object to detach.
	 * @return  boolean  True if the observer object was detached.
	 */
	public function detach($observer)
	{
		// Initialise variables.
		/*$retval = false;

		$key = array_search($observer, $this->_observers);

		if ($key !== false)
		{
			unset($this->_observers[$key]);
			$retval = true;

			foreach ($this->_methods as &$method)
			{
				$k = array_search($key, $method);

				if ($k !== false)
				{
					unset($method[$k]);
				}
			}
		}

		return $retval;*/
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

		/*$args['name'] = $name;
		$args['content'] = $html;
		$args['width'] = $width;
		$args['height'] = $height;
		$args['col'] = $col;
		$args['row'] = $row;
		$args['buttons'] = $buttons;
		$args['id'] = $id ? $id : $name;
		$args['asset'] = $asset;
		$args['author'] = $author;
		$args['params'] = $params;
		$args['event'] = 'onDisplay';*/

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

		//$args[] = $editor;
		//$args['event'] = 'onSave';

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

		//$args['name'] = $editor;
		//$args['event'] = 'onGetContent';

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

		//$args['name']  = $editor;
		//$args['html']  = $html;
		//$args['event'] = 'onSetContent';

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

		$path = PATH_CORE . '/plugins/editors/' . $name . '/' . $name . '.php';
		if (!is_file($path))
		{
			\Notify::error(Lang::txt('JLIB_HTML_EDITOR_CANNOT_LOAD'));
			return false;
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
