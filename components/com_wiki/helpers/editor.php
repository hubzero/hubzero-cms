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

namespace Components\Wiki\Helpers;

use Exception;

jimport('joomla.event.dispatcher');

/**
 * Hubzero helper class for retrieving the current wiki editor
 */
class Editor extends \JObservable
{
	/**
	 * Editor Plugin object
	 *
	 * @var	 object
	 */
	private $_editor = null;

	/**
	 * Editor Plugin name
	 *
	 * @var  string
	 */
	private $_name = null;

	/**
	 * Constructor
	 *
	 * @param  string  The parser name
	 */
	public function __construct($editor = '')
	{
		if (!$editor)
		{
			$database = \JFactory::getDBO();
			$database->setQuery("SELECT element FROM `#__extensions` WHERE folder='wiki' AND type='plugin' AND enabled=1 AND element LIKE 'editor%' ORDER BY enabled DESC LIMIT 1");

			$editor = $database->loadResult();
		}
		$this->_name = $editor;
	}

	/**
	 * Returns a reference to a global Parser object, only creating it
	 * if it doesn't already exist.
	 *
	 * This method must be invoked as:
	 *     $parser = WikiHelperParser::getInstance($parser_name);
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
	 * @param   array    $config
	 * @param   boolean  $getnew
	 * @return  void
	 */
	public function initialise()
	{
		// Check if editor is already loaded
		if (is_null(($this->_editor)))
		{
			return;
		}

		$args = array(
			'event' => 'onInitEditor'
		);

		$return = '';
		$results[] = $this->_editor->update($args);
		foreach ($results as $result)
		{
			if (trim($result))
			{
				$return = $result;
			}
		}

		if ($return)
		{
			$document = \JFactory::getDocument();
			$document->addCustomTag($return);
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
		if (!$this->_name)
		{
			return '<textarea name="' . $name . '" id="' . $id . '" cols="' . $col . '" rows="' . $row . '" class="' . $cls . '">' . $html . '</textarea>' . "\n";
		}

		$this->_loadEditor($params);

		// Check if editor is already loaded
		if (is_null(($this->_editor)))
		{
			return;
		}

		$args = array(
			'name'    => $name,
			'id'      => $id,
			'content' => $html,
			'cls'     => $cls,
			'col'     => $col,
			'row'     => $row,
			'event'   => 'onDisplayEditor'
		);

		// Initialize variables
		$return = null;

		$results[] = $this->_editor->update($args);

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
		$this->_loadEditor();

		// Check if editor is already loaded
		if (is_null(($this->_editor)))
		{
			return;
		}

		$args[] = $editor;
		$args['event'] = 'onSaveEditorContent';

		$return = '';
		$results[] = $this->_editor->update($args);
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
		$this->_loadEditor();

		$args['name'] = $editor;
		$args['event'] = 'onGetEditorContent';

		$return = '';
		$results[] = $this->_editor->update($args);
		foreach ($results as $result)
		{
			if (trim($result)) {
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
		$this->_loadEditor();

		$args = array(
			'name'  => $editor,
			'html'  => $html,
			'event' => 'onSetEditorContent'
		);

		$return = '';
		$results[] = $this->_editor->update($args);
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
	private function _loadEditor($config = array())
	{
		// Check if editor is already loaded
		if (!is_null(($this->_editor)))
		{
			return;
		}

		jimport('joomla.filesystem.file');

		// Build the path to the needed editor plugin
		$name = \JFilterInput::getInstance()->clean($this->_name, 'cmd');
		$path = PATH_CORE . DS . 'plugins' . DS . 'wiki' . DS . $name . DS . $name . '.php';

		if (!\JFile::exists($path))
		{
			throw new Exception(Lang::txt('Cannot load the editor'), 500);
			return false;
		}

		// Require plugin file
		require_once $path;

		// Get the plugin
		$plugin = \JPluginHelper::getPlugin('wiki', $this->_name);
		$params = new \JRegistry($plugin->params);
		$params->loadArray($config);
		$plugin->params = $params;

		// Build editor plugin classname
		$name = 'plgWiki' . $this->_name;
		if ($this->_editor = new $name($this, (array)$plugin))
		{
			// Load plugin parameters
			$this->initialise();
		}
	}
}
