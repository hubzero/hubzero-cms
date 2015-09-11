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

namespace Components\Wiki\Helpers;

use Hubzero\Base\Object;
use Hubzero\Config\Registry;
use Plugin;
use Lang;

/**
 * Hubzero helper class for retrieving the current wiki editor
 */
class Editor extends Object
{
	/**
	 * Editor Plugin object
	 *
	 * @var	 object
	 */
	private $editor = null;

	/**
	 * Editor Plugin name
	 *
	 * @var  string
	 */
	private $name = null;

	/**
	 * Constructor
	 *
	 * @param  string  The parser name
	 */
	public function __construct($editor = '')
	{
		if (!$editor)
		{
			$database = \App::get('db');
			$database->setQuery("SELECT element FROM `#__extensions` WHERE folder='wiki' AND type='plugin' AND enabled=1 AND element LIKE 'editor%' ORDER BY enabled DESC LIMIT 1");

			$editor = $database->loadResult();
		}
		$this->name = $editor;
	}

	/**
	 * Returns a reference to a global Parser object, only creating it
	 * if it doesn't already exist.
	 *
	 * This method must be invoked as:
	 *     $parser = WikiHelperParser::getInstance($parsername);
	 *
	 * @param   string  $parser  The name of the parser to use.
	 * @return  object  The Parser object.
	 */
	public static function &getInstance($editor = '')
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		$signature = serialize($editor);

		if (empty($instances[$signature]))
		{
			$instances[$signature] = new self($editor);
		}

		return $instances[$signature];
	}

	/**
	 * Initialize the parser
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
		$results[] = $this->editor->onInitEditor();

		foreach ($results as $result)
		{
			if (trim($result))
			{
				$return = $result;
			}
		}

		if ($return)
		{
			\Document::addCustomTag($return);
		}
	}

	/**
	 * Present a text area
	 *
	 * @param   string   $name    The control name
	 * @param   string   $id      The control id
	 * @param   string   $html    The contents of the text area
	 * @param   string   $cls     The width of the text area (px or %)
	 * @param   integer  $col     The number of columns for the textarea
	 * @param   integer  $row     The number of rows for the textarea
	 * @param   array    $params  Associative array of editor parameters
	 * @return  mixed
	 */
	public function display($name, $id, $html, $cls, $col, $row, $params = array())
	{
		// Return a standard textarea if no editor is found
		if (!$this->name)
		{
			return '<textarea name="' . $name . '" id="' . $id . '" cols="' . $col . '" rows="' . $row . '" class="' . $cls . '">' . $html . '</textarea>' . "\n";
		}

		$this->load($params);

		// Check if editor is already loaded
		if (is_null($this->editor))
		{
			return;
		}

		$return = null;

		$results[] = $this->editor->onDisplayEditor($name, $id, $html, $cls, $col, $row);

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
	 * @return  void
	 */
	public function save($editor)
	{
		$this->load();

		if (is_null($this->editor))
		{
			return;
		}

		$return = '';
		$results[] = $this->editor->onSaveEditorContent($editor);

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
	 * @return  mixed
	 */
	public function getContent($editor)
	{
		$this->load();

		if (is_null($this->editor))
		{
			return;
		}

		$return = '';
		$results[] = $this->editor->onGetEditorContent($editor);

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
	 * @return  mixed
	 */
	public function setContent($editor, $html)
	{
		$this->load();

		$args = array(
			'name'  => $editor,
			'html'  => $html,
			'event' => 'onSetEditorContent'
		);

		$return = '';
		$results[] = $this->editor->update($args);
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
	 * Load the editor
	 *
	 * @param   array  $config  Associative array of editor config paramaters
	 * @return  void
	 */
	private function load($config = array())
	{
		// Check if editor is already loaded
		if (!is_null($this->editor))
		{
			return;
		}

		// Build the path to the needed editor plugin
		$name = (string) preg_replace('/[^A-Z0-9_\.-]/i', '', $this->name);
		$name = ltrim($name, '.');

		$path = PATH_CORE . DS . 'plugins' . DS . 'wiki' . DS . $name . DS . $name . '.php';

		if (!is_file($path))
		{
			throw new Exception(Lang::txt('Cannot load the editor'), 500);
			return false;
		}

		// Require plugin file
		require_once $path;

		// Get the plugin
		$plugin = Plugin::byType('wiki', $this->name);

		$params = new Registry($plugin->params);
		$params->toArray($config);

		$plugin->params = $params;

		// Build editor plugin classname
		$name = 'plgWiki' . $this->name;

		if ($this->editor = new $name($this, (array)$plugin))
		{
			// Load plugin parameters
			$this->initialise();
		}
	}

	/**
	 * Attach an observer object
	 *
	 * @param   object  $observer  An observer object to attach
	 * @return  void
	 */
	public function attach($observer)
	{
	}

	/**
	 * Detach an observer object
	 *
	 * @param   object   $observer  An observer object to detach.
	 * @return  boolean  True if the observer object was detached.
	 */
	public function detach($observer)
	{
	}
}
