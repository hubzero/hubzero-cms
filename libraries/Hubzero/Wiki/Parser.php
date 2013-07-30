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
 * Hubzero helper class for retrieving wiki parser
 */
class Hubzero_Wiki_Parser extends JObservable
{
	/**
	 * Parser Plugin object
	 *
	 * @var	object
	 */
	public $_parser = null;

	/**
	 * Parser Plugin name
	 *
	 * @var string
	 */
	public $_name = null;

	/**
	 * constructor
	 *
	 * @access	protected
	 * @param	string	The parser name
	 */
	public function __construct($parser = '')
	{
		if (!$parser) 
		{
			$database =& JFactory::getDBO();
			if (version_compare(JVERSION, '1.6', 'lt'))
			{
				$database->setQuery("SELECT element FROM #__plugins WHERE folder='hubzero' AND published=1 AND element LIKE 'wikiparser%' ORDER BY published DESC LIMIT 1");
			}
			else
			{
				$database->setQuery("SELECT element FROM #__extensions WHERE folder='hubzero' AND type='plugin' AND enabled=1 AND element LIKE 'wikiparser%' ORDER BY enabled DESC LIMIT 1");
			}
			$parser = $database->loadResult();
		}
		$this->_name = $parser;
	}

	/**
	 * Returns a reference to a global Parser object, only creating it
	 * if it doesn't already exist.
	 *
	 * This method must be invoked as:
	 * 		<pre>  $parser =& Hubzero_Wiki_Parser::getInstance($parser_name);</pre>
	 *
	 * @access	public
	 * @param	string	$parser  The name of the parser to use.
	 * @return	object  Hubzero_Wiki_Parser  The Parser object.
	 */
	public static function &getInstance($parser = '')
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		$signature = serialize($parser);

		if (empty($instances[$signature])) 
		{
			$instances[$signature] = new Hubzero_Wiki_Parser($parser);
		}

		return $instances[$signature];
	}

	/**
	 * Initialize the parser
	 */
	public function initialise($config=array(), $getnew=false)
	{
		// Check if parser is already loaded
		if (is_null($this->_parser)) 
		{
			return;
		}

		$args = array(
			'config' => $config,
			'getnew' => $getnew,
			'event'  => 'onGetWikiParser'
		);

		$return = '';
		$results[] = $this->_parser->update($args); //$this->_parser->onGetWikiParser($config, $getnew);
		foreach ($results as $result)
		{
			if (is_object($result)) 
			{
				$return = $result;
			}
		}
	}

	/**
	 * Parse the text
	 *
	 * @param	string	The content to be parsed
	 * @param	array	Params for the parser
	 * @param	bool	Do a full parse or not
	 * @param	bool	Use the existing parser or get new
	 * @param	array	Params for the plugin
	 */
	public function parse($text, $config, $fullparse=true, $getnew=false, $params=array())
	{
		if (!$this->_name) 
		{
			return nl2br($text);
		}

		$this->_loadParser($params, $config, $getnew);

		// Check if parser is already loaded
		if (is_null($this->_parser)) 
		{
			return nl2br($text);
		}

		// Initialize variables
		$return = null;

		$args = array(
			'text'      => $text,
			'config'    => $config,
			'fullparse' => $fullparse,
			'getnew'    => $getnew,
			'event'     => 'onWikiParseText'
		);

		$results[] = $this->_parser->update($args);

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
	 * Load the parser
	 *
	 * @access	private
	 * @param	array	Associative array of parser plugin config paramaters
	 * @param	array	Associative array of parser config paramaters
	 * @param	bool	Tells initialise() to create new parser or not
	 * @since	1.5
	 */
	private function _loadParser($config=array(), $pconfig=array(), $getnew=false)
	{
		// Check if editor is already loaded
		if (!$getnew && !is_null($this->_parser)) 
		{
			return;
		}

		jimport('joomla.filesystem.file');

		// Build the path to the needed parser plugin
		$name = JFilterInput::clean($this->_name, 'cmd');
		$path = JPATH_SITE . DS . 'plugins' . DS . 'hubzero' . DS . $name . '.php';

		if (!JFile::exists($path)) 
		{
			JError::raiseWarning(500, JText::_('Cannot load the parser'));
			return false;
		}

		// Require plugin file
		require_once $path;

		// Get the plugin
		$plugin =& JPluginHelper::getPlugin('hubzero', $this->_name);
		if (is_string($plugin->params))
		{
			$paramsClass = 'JParameter';
			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$paramsClass = 'JRegistry';
			}
			$plugin->params = new $paramsClass($plugin->params);
		}
		$plugin->params->loadArray($config);

		// Build parser plugin classname
		$name = 'plgHubzero' . $this->_name;
		if ($this->_parser = new $name($this, (array)$plugin)) 
		{
			// Load plugin parameters
			$this->initialise($pconfig, $getnew);
		}
	}
}
