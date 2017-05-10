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

		$path = PATH_APP . DS . 'plugins' . DS . 'wiki' . DS . $name . DS . $name . '.php';

		if (!is_file($path))
		{
			$path = PATH_CORE . DS . 'plugins' . DS . 'wiki' . DS . $name . DS . $name . '.php';

			if (!is_file($path))
			{
				throw new Exception(Lang::txt('Cannot load the parser'), 500);
				return false;
			}
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
