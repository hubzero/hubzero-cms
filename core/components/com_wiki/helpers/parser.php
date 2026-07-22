<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Helpers;

use Hubzero\Base\Obj;
use Hubzero\Config\Registry;
use Exception;
use Plugin;
use Lang;

/**
 * Hubzero helper class for retrieving wiki parser
 */
class Parser extends Obj
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
	 * Build a standalone table of contents for already-parsed page HTML
	 *
	 * @param   string   $text    The parsed page HTML to extract headings from
	 * @param   array    $config  Params for the parser (scope, domain, url, etc.)
	 * @param   integer  $depth   Limit the TOC to this many heading levels (0 = no limit)
	 * @return  string   Rendered table-of-contents markup
	 */
	public function toc($text, $config, $depth = 0)
	{
		$this->load(array(), $config, true);
		$parser = $this->parser->onGetWikiParser($config, true);

		return $parser->toc($text, true, $depth);
	}

	/**
	 * Parse the positional arguments of a [[TableOfContents(...)]] macro.
	 *
	 * Args are type-dispatched and order-independent: an integer sets the
	 * depth, a keyword (here|inline|sidebar|off) sets the placement mode.
	 * Last value of each type wins. Unrecognized tokens are ignored.
	 *
	 * @param   string  $args  Raw argument string (contents of the parentheses)
	 * @return  array   array('mode' => string, 'depth' => int)
	 */
	public static function parseTocArgs($args)
	{
		$mode  = 'here';   // default: render at the macro's own position
		$depth = 0;        // 0 = no depth limit

		if ($args !== null && $args !== '')
		{
			foreach (explode(',', $args) as $arg)
			{
				$arg = trim($arg);
				if ($arg === '')
				{
					continue;
				}
				if (ctype_digit($arg))
				{
					$depth = (int) $arg;
				}
				elseif (in_array(strtolower($arg), array('here', 'inline', 'sidebar', 'off'), true))
				{
					$mode = strtolower($arg);
				}
			}
		}

		return array('mode' => $mode, 'depth' => $depth);
	}

	/**
	 * Detect an explicit [[TableOfContents(...)]] macro in raw wiki text and
	 * return its resolved mode/depth, or null when the page has no such macro.
	 *
	 * @param   string  $pagetext  Raw wiki markup
	 * @return  array|null  array('mode' => string, 'depth' => int) or null
	 */
	public static function tocDirective($pagetext)
	{
		if ($pagetext === null || $pagetext === '')
		{
			return null;
		}

		if (preg_match('/\[\[\s*TableOfContents\s*(?:\(([^)]*)\))?\s*\]\]/i', $pagetext, $m))
		{
			return self::parseTocArgs(isset($m[1]) ? $m[1] : '');
		}

		return null;
	}

	/**
	 * Resolve the effective automatic table-of-contents settings for a wiki.
	 *
	 * These govern pages that have no explicit [[TableOfContents]] macro. The
	 * component configuration provides the defaults; a group may override them
	 * via its own params (layered in by the group wiki plugin).
	 *
	 * @param   string   $domain     Scope type (e.g. 'group')
	 * @param   integer  $domain_id  Scope id (e.g. group gidNumber)
	 * @return  array    array('mode' => inline|sidebar|off, 'threshold' => int)
	 */
	public static function tocSettings($domain = null, $domain_id = null)
	{
		$params = \Component::params('com_wiki');

		$mode      = $params->get('automatic_toc', 'inline');
		$threshold = (int) $params->get('toc_threshold', 4);

		// Group-level override
		if ($domain == 'group' && $domain_id)
		{
			$group = \Hubzero\User\Group::getInstance($domain_id);
			if ($group)
			{
				$gparams = new Registry($group->get('params'));

				$gmode = $gparams->get('automatic_toc', '');
				if (in_array($gmode, array('inline', 'sidebar', 'off'), true))
				{
					$mode = $gmode;
				}

				$gthreshold = $gparams->get('toc_threshold', '');
				if ($gthreshold !== '' && $gthreshold !== null)
				{
					$threshold = (int) $gthreshold;
				}
			}
		}

		if (!in_array($mode, array('inline', 'sidebar', 'off'), true))
		{
			$mode = 'inline';
		}

		return array('mode' => $mode, 'threshold' => max(1, $threshold));
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
	 * @param   array  $config   Associative array of parser plugin config parameters
	 * @param   array  $pconfig  Associative array of parser config parameters
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

		$path = Plugin::path('wiki', $name) . DS . $name . '.php';

		if (!is_file($path))
		{
			throw new Exception(Lang::txt('Cannot load the parser'), 500);
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

	/**
	 * Get the path to the parser's default pages
	 *
	 * @return  string
	 */
	public function defaultPagesPath()
	{
		// Build the path to the needed parser plugin
		$name = (string) preg_replace('/[^A-Z0-9_\.-]/i', '', $this->name);
		$name = ltrim($name, '.');

		$path = Plugin::path('wiki', $name) . DS . 'default';

		if (!is_dir($path))
		{
			$path = '';
		}

		return $path;
	}
}
