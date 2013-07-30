<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('joomla.event.dispatcher');

/**
 * Hubzero helper class for retrieving the current wiki editor
 */
class Hubzero_Wiki_Editor extends JObservable
{
	/**
	 * Editor Plugin object
	 *
	 * @var	object
	 */
	private $_editor = null;

	/**
	 * Editor Plugin name
	 *
	 * @var string
	 */
	private $_name = null;

	/**
	 * constructor
	 *
	 * @access	protected
	 * @param	string	The editor name
	 */
	public function __construct($editor = '')
	{
		if (!$editor) 
		{
			$database =& JFactory::getDBO();
			if (version_compare(JVERSION, '1.6', 'lt'))
			{
				$database->setQuery("SELECT element FROM #__plugins WHERE folder='hubzero' AND published=1 AND element LIKE 'wikieditor%' ORDER BY published DESC LIMIT 1");
			}
			else
			{
				$database->setQuery("SELECT element FROM #__extensions WHERE folder='hubzero' AND type='plugin' AND enabled=1 AND element LIKE 'wikieditor%' ORDER BY enabled DESC LIMIT 1");
			}
			$editor = $database->loadResult();
		}
		$this->_name = $editor;
	}

	/**
	 * Returns a reference to a global Editor object, only creating it
	 * if it doesn't already exist.
	 *
	 * This method must be invoked as:
	 * 		<pre>  $editor =& Hubzer_Wiki_Editor::getInstance($editor);</pre>
	 *
	 * @access	public
	 * @param	string	$editor  The editor to use.
	 * @return	JEditor	The Editor object.
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
			$instances[$signature] = new Hubzero_Wiki_Editor($editor);
		}

		return $instances[$signature];
	}

	/**
	 * Initialize the editor
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
			$document =& JFactory::getDocument();
			$document->addCustomTag($return);
		}
	}

	/**
	 * Present a text area
	 *
	 * @param	string	The control name
	 * @param	string	The control id
	 * @param	string	The contents of the text area
	 * @param	string	The width of the text area (px or %)
	 * @param	string	The height of the text area (px or %)
	 * @param	int		The number of columns for the textarea
	 * @param	int		The number of rows for the textarea
	 * @param	array	Associative array of editor parameters
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

		// Initialize variables
		$return = null;

		$args = array(
			'name'    => $name,
			'id'      => $id,
			'content' => $html,
			'cls'     => $cls,
			'col'     => $col,
			'row'     => $row,
			'event'   => 'onDisplayEditor'
		);

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
	 * @param	string	The name of the editor control
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
	 * @param	string	The name of the editor control
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
	 * @param	string	The name of the editor control
	 * @param	string	The contents of the text area
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
	 * @access	private
	 * @param	array	Associative array of editor config paramaters
	 * @since	1.5
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
		$name = JFilterInput::clean($this->_name, 'cmd');
		$path = JPATH_SITE . DS . 'plugins' . DS . 'hubzero' . DS . $name . '.php';

		if (!JFile::exists($path)) 
		{
			JError::raiseWarning(500, JText::_('Cannot load the editor'));
			return false;
		}

		// Require plugin file
		require_once $path;

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Get the plugin
		$plugin =& JPluginHelper::getPlugin('hubzero', $this->_name);
		$params = new $paramsClass($plugin->params);
		$params->loadArray($config);
		$plugin->params = $params;

		// Build editor plugin classname
		$name = 'plgHubzero' . $this->_name;
		if ($this->_editor = new $name($this, (array)$plugin)) 
		{
			// Load plugin parameters
			$this->initialise();
		}
	}
}
