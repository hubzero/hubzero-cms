<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Wiki\Helpers;

use Hubzero\Base\Object;
use Hubzero\Config\Registry;
use Exception;
use Plugin;
use Lang;

/**
 * Hubzero helper class for retrieving wiki parser
 */
class Parser extends Object
{
	/**
	 * Parser Plugin object
	 *
	 * @var	 object
	 */
	public $parser = null;

	/**
	 * Parser Plugin name
	 *
	 * @var  string
	 */
	public $name = null;

	/**
	 * Constructor
	 *
	 * @param  string  The parser name
	 */
	public function __construct($parser = '')
	{
		if (!$parser)
		{
			$database = \App::get('db');
			$database->setQuery("SELECT element FROM `#__extensions` WHERE folder='wiki' AND type='plugin' AND enabled=1 AND element LIKE 'parser%' ORDER BY enabled DESC LIMIT 1");

			$parser = $database->loadResult();
		}
		$this->name = $parser;
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
			$instances[$signature] = new self($parser);
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
	public function initialise($config=array(), $getnew=false)
	{
		// Check if parser is already loaded
		if (is_null($this->parser))
		{
			return;
		}

		$return = '';
		$results[] = $this->parser->onGetWikiParser($config, $getnew);

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
	 * @param   string  $text       The content to be parsed
	 * @param   array   $config     Params for the parser
	 * @param   bool    $fullparse  Do a full parse or not
	 * @param   bool    $getnew     Use the existing parser or get new
	 * @param   array   $params     Params for the plugin
	 * @return  void
	 */
	public function parse($text, $config, $fullparse=true, $getnew=false, $params=array())
	{
		if (!$this->name)
		{
			return nl2br($text);
		}

		$this->load($params, $config, $getnew);

		// Check if parser is already loaded
		if (is_null($this->parser))
		{
			return nl2br($text);
		}

		// Initialize variables
		$return = null;

		$results[] = $this->parser->onWikiParseText($text, $config, $fullparse, $getnew);

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
	 * @param   array  $config   Associative array of parser plugin config paramaters
	 * @param   array  $pconfig  Associative array of parser config paramaters
	 * @param   bool   $getnew   Tells initialise() to create new parser or not
	 * @return  void
	 */
	private function load($config=array(), $pconfig=array(), $getnew=false)
	{
		// Check if editor is already loaded
		if (!$getnew && !is_null($this->parser))
		{
			return;
		}

		// Build the path to the needed parser plugin
		$name = (string) preg_replace('/[^A-Z0-9_\.-]/i', '', $this->name);
		$name = ltrim($name, '.');

		$path = PATH_CORE . DS . 'plugins' . DS . 'wiki' . DS . $name . DS . $name . '.php';

		if (!is_file($path))
		{
			throw new Exception(Lang::txt('Cannot load the parser'), 500);
			return false;
		}

		// Require plugin file
		require_once $path;

		// Get the plugin
		$plugin = Plugin::byType('wiki', $this->name);
		if (is_string($plugin->params))
		{
			$plugin->params = new Registry($plugin->params);
		}
		$plugin->params->merge($config);

		// Build parser plugin classname
		$name = 'plgWiki' . $this->name;

		if ($this->parser = new $name($this, (array)$plugin))
		{
			// Load plugin parameters
			$this->initialise($pconfig, $getnew);
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
