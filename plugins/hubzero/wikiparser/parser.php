<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php');
include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'link.php');

/**
 * Wiki parser class
 * converts wiki syntax to HTML
 * 
 * Code was heavily influenced by MediaWiki's Parser, Trac's parser, and Textile.
 */
class WikiParser
{
	/**
	 * Preformatted NOT in code state
	 * i.e., not inside a <pre> block
	 * 
	 * @var integer
	 */
	const CS_NONE = 0;

	/**
	 * Preformatted code state
	 * i.e., inside a <pre> block
	 * 
	 * @var integer
	 */
	const CS_CODE = 1;

	/**
	 * Preformatted code within code state
	 * i.e., <pre> inside a <pre> block
	 * 
	 * @var integer
	 */
	const CS_CODE_SUB = 2;

	/**
	 * A unique token
	 * 
	 * @var string
	 */
	private $_token = null;

	/**
	 * Configuration options
	 * 
	 * @var array
	 */
	private $_config = array(
		'option'   => null,
		'scope'    => null,
		'pagename' => null,
		'pageid'   => null,
		'filepath' => null,
		'domain'   => null,

		'fullparse' => true,
		'camelcase' => true,
		'loglinks'  => false
	);

	/**
	 * Data store
	 * 
	 * @var array
	 */
	private $_data = array(
		'input'  => null,
		'output' => null,
		'links'  => array()
	);

	/**
	 * Parsed content temp storage
	 * 
	 * @var array
	 */
	private $_tokens = array(
		'pre'    => array(),
		'code'   => array(),
		'anchor' => array(),
		'macro'  => array(),
		'math'   => array()
	);

	/**
	 * Useful patterns
	 * 
	 * @var array
	 */
	private static $_patterns = array(
		'url'  => "(?:https?:|mailto:|ftp:|gopher:|news:|file:)(?:[^ |\\/\"\']*\\/)*[^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_]",

		'hlgn' => "(?:\<(?!>)|(?<!<)\>|\<\>|\=|[()]+(?!))",
		'vlgn' => "[\-^~]",
		'clas' => "(?:\([^)]+\))",
		'lnge' => "(?:\[[^]]+\])",
		'styl' => "(?:\{[^}]+\})",
		'cspn' => "(?:\\\\\d+)",
		'rspn' => "(?:\/\d+)",

		'algn' => "(?:(?:\<(?!>)|(?<!<)\>|\<\>|\=|[()]+(?!))|[\-^~])*",
		'spns' => "(?:(?:\\\\\d+)|(?:\/\d+))*",
		'clss' => "(?:(?:\([^)]+\))|(?:\{[^}]+\}))*"
	);

	/**
	 * Constructor
	 * 
	 * @param      array $config Configuration options
	 * @return     void
	 */
	public function __construct($config=array())
	{
		if (is_array($config))
		{
			// We need this info for links that may get generated
			foreach ($config as $k => $s)
			{
				$this->set($k, $s);
			}
		}

		// Set the unique token
		$this->_token = "\x07UNIQ" . $this->_randomString();
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $value     The value of the property to set.
	 * @return  mixed   Previous value of the property.
	 */
	public function set($property, $value = null)
	{
		$previous = isset($this->_config[$property]) ? $this->_config[$property] : null;
		$this->_config[$property] = $value;
		return $previous;
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $default   The default value.
	 * @return  mixed   The value of the property.
	 */
	public function get($property, $default = null)
	{
		if (isset($this->_config[$property]))
		{
			return $this->_config[$property];
		}
		return $default;
	}

	/**
	 * Get the unique string
	 * 
	 * @return     string
	 */
	public function token()
	{
		return $this->_token;
	}

	/**
	 * Get the raw input
	 * 
	 * @return     string
	 */
	public function input()
	{
		return $this->_data['input'];
	}

	/**
	 * Get the parsed output
	 * 
	 * @return     string
	 */
	public function output()
	{
		return $this->_data['output'];
	}

	/**
	 * Generate a unique prefix
	 * 
	 * @return     integer 
	 */
	private function _randomString()
	{
		return dechex(mt_rand(0, 0x7fffffff)) . dechex(mt_rand(0, 0x7fffffff));
	}

	/**
	 * Where all the magic takes place
	 * Turns raw wiki text to HTML
	 * 
	 * @param      string  $text      Raw wiki markup
	 * @param      boolean $fullparse Full or limited parse? Limited does not parse macros
	 * @param      integer $linestart Parameter description (if any) ...
	 * @param      integer $camelcase Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function parse($text, $fullparse=true, $linestart=0, $camelcase=1)
	{
		// Store the raw input
		$this->_data['input'] = $text;

		// Set some configs
		$this->set('fullparse', $fullparse);
		$this->set('camelcase', $camelcase);
		if (!$this->get('fullparse')) 
		{
			$this->set('camelcase', 0);
		}

		// Remove any trailing whitespace
		$text = rtrim($text);

		// Prepend a line break
		// Makes block parsing a little easier
		$text = "\n" . $text;

		// Clean out any carriage returns.
		// These can screw up some block parsing, such as tables
		$text = str_replace("\r", '', $text);

		// Strip out <pre> code
		// We'll put this back after other processes
		$text = $this->strip($text);

		// Process includes
		// Includes are essentially smaller other pages
		$text = $this->includes($text);

		// We need to temporarily put back any blocks we stripped and then restrip everything
		// This is because of any blocks the macros may have outputted - otherwise they wouldn't get processed
		$text = $this->unstrip($text, false);
		$text = $this->strip($text);

		// Clean out any Cross-Site Scripting attempts on the tags we didn't strip out
		$text = $this->cleanXss($text);

		// Process LaTeX math forumlas and strip out
		// This will return either simple HTML or an image
		// We'll put this back after other processes
		$text = $this->math($text);

		// Strip HTML tags out
		$text = preg_replace(
			array('/<\./', '/([0-9]+)<([0-9]+)/', '/<([0-9]+)/', '/>([0-9]+)/', '/([0-9]+)</'), //'/([0-9]+)>/'), 
			array('&lt;.', '$1 &lt; $2',          '&lt; $1',     '&gt; $1',     '$1 &lt;'), //    '$1 &gt;'), 
			$text
		);
		$text = str_replace('<>.', '&lt;&gt;.', $text);
		$text = str_replace('||>.', '||&gt;.', $text);

		$text = strip_tags($text, '<pre><code><xpre><math>');
		$text = str_replace(array('&lt;', '&gt;'), array('<', '>'), $text);

		// Tables need to come after variable replacement for things to work
		// properly; putting them before other transformations should keep
		// exciting things like link expansions from showing up in surprising
		// places.
		$text = $this->tables($text);

		if ($this->get('fullparse')) 
		{
			// Do horizontal rules <hr />
			$text = preg_replace('/(^|\n)-----*/', '\\1<hr />', $text);

			// Do headings <h1>, <h2>, etc.
			$text = $this->headings($text);
		}

		// Process macros
		// Individual macros determine if they're allowed in fullparse mode or not
		$text = $this->macros($text);

		// Do quotes. '''stuff''' => <strong>stuff</strong>
		$text = $this->quotes($text);

		// Do spans. ~~fast~~ => <del>fast</del>, ??me?? => <cite>me</cite>, etc.
		$text = $this->spans($text);

		// Do links. [MyLink]
		$text = $this->links($text);

		// Do glyphs. " => &#8220;
		$text = $this->glyphs($text);

		// Admonitions are only allowed in fullparse mode
		if ($this->get('fullparse')) 
		{
			$text = $this->admonitions($text);
		}

		// Do definition lists
		$text = $this->definitions($text);

		// Unstrip macro blocks BEFORE doing block levels or <p> tags will get messy
		//$text = preg_replace_callback('/MACRO' . $this->_token . '/i', array(&$this, 'restore_macros'), $text);
		$text = preg_replace_callback('/<(macro) (.+?)>(.*)<\/(\1) \2>/si', array(&$this, '_dataPull'), $text);
		$this->_tokens['macro'] = array();

		// Clean up special characters, only run once, next-to-last before block levels
		$fixtags = array(
			// french spaces, last one Guillemet-left
			// only if there is something before the space
			'/(.) (?=\\?|:|;|!|%|\\302\\273)/' => '\\1&nbsp;\\2',
			// french spaces, Guillemet-right
			'/(\\302\\253) /' => '\\1&nbsp;',
		);
		$text = preg_replace(array_keys($fixtags), array_values($fixtags), $text);

		// Only once and last
		$text = $this->blocks($text);

		// Put back removed blocks <pre>, <code>, <a>, <math>
		$text = $this->unstrip($text);

		// Strip out blank space
		$text = str_replace("<p><br />\n</p>", '', $text);
		$text = preg_replace('|<p>\s*?</p>|', '', $text);
		$text = preg_replace('/<p>\p{Z}*<\/p>/u', '', $text);
		$text = preg_replace('!<p>\s*(</?(?:table|tr|td|th|div|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)!', "$1", $text);
		$text = preg_replace('!(</?(?:table|tr|td|th|div|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)\s*</p>!', "$1", $text);

		// Format headings and build a table of contents
		if ($this->get('fullparse') && strstr($text, '<p>MACRO' . $this->token() . '[[TableOfContents]]' . "\n" . '</p>')) 
		{
			$text = $this->toc($text);
		}

		// If full parse and we have a page ID (i.e., this is a *wiki* page) and link logging is turned on...
		if ($this->get('fullparse') && $this->get('pageid') && $this->get('loglinks'))
		{
			$links = new WikiLink(JFactory::getDBO());
			$links->updateLinks($this->get('pageid'), $this->_data['links']);
		}

		$this->_data['output'] = $text;

		if (trim($this->_data['input']) && !trim($this->_data['output']))
		{
			$this->_data['output']  = '<p class="warning">Parsing error resulted in empty content. Displaying raw markup below.</p>';
			$this->_data['output'] .= '<pre>' . htmlentities($this->_data['input'], ENT_COMPAT, 'UTF-8') . '</pre>';
		}

		return $this->_data['output'];
	}

	/**
	 * Convert wiki links to HTML links
	 * 
	 * @param      string $text Wiki markup
	 * @return     string
	 */
	public function links($text)
	{
		// Parse for link syntax 
		// e.g. [mylink My Link] => <a href="mylink">My Link</a>
		$char_regexes = array(
			// [http://external.links]
			'external' => '('.
				'\['. // opening brackets
					'(https?:|mailto:|ftp:|gopher:|news:|file:|\/\/)' . // protocol
					'([^\]\[]*?)'. // link
					'(\s+[^\]]*?)?'. // optional title
				'\]'. // closing brackets
			')',

			// [InternalLinks]
			'internal' => '('.
				'\['. // opening brackets
					'(?:([^\]]*?)\:)?'. // namespace (if any)
					'([^\]\[]*?)'.
					'(\s+[^\]]*?)?'.
				'\]'. // closing brackets
			')',

			// URL pattern
			'autourl'    => "[^=\"\'\[]" .  // Make sure it's not preceeded by quotes and brackets
				"(https?:|mailto:|ftp:|gopher:|news:|file:)" .  // protocol
				"([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_])",  // link

			// Email pattern
			'autoemail'    => "([\s]*)" .  // whitespace
				"([\._a-zA-Z0-9-\+]+@" .  // characters leading up to @
				"(?:[0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6})",  // everything after @

			// Camelcase links (e.g. MyLink) 
			'wikiname' => "(" . 
					"!?\\/?" .  // Optionally starts with ! \ /
					"[A-Z][A-Za-z]*[a-z]+[A-Z][A-Za-z]*" .  // CamelCase pattern
					"(?:(?:\\/[A-Z][A-Za-z]*)+)?" . 
				")" . 
				"(" . 
					"(\#[A-Za-z]([-A-Za-z0-9_:.]*[-A-Za-z0-9_])?)?" . 
				")" . 
				"(\"\")?"
		);

		foreach ($char_regexes as $func => $regex)
		{
			if ($func == 'wikiname') 
			{
				if (!$this->get('camelcase'))
				{
					continue;
				}
				$text = preg_replace_callback("/$regex/", array(&$this, 'link' . ucfirst($func)), $text);
			}
			else
			{
				$text = preg_replace_callback("/$regex/i", array(&$this, 'link' . ucfirst($func)), $text);
			}
		}

		return $text;
	}

	/**
	 * Inject link back into text
	 * 
	 * @param      string $txt Link HTML
	 * @return     string
	 */
	private function _restoreAnchor($txt)
	{
		return $txt;
	}

	/**
	 * Automatically links any strings matching a URL pattern
	 * 
	 * @param      array $matches Text matching link pattern
	 * @return     string
	 */
	public function linkAutourl($matches)
	{
		return $this->linkAuto($matches);
	}

	/**
	 * Automatically links any strings matching an email pattern
	 * 
	 * @param      array $matches Text matching link pattern
	 * @return     string
	 */
	public function linkAutoemail($matches)
	{
		array_splice($matches, 1, 0, 'mailto:');
		return $this->linkAuto($matches);
	}

	/**
	 * Automatically links any strings matching a URL or email pattern
	 * 
	 * Link is pushed to internal array and placeholder returned
	 * This is to ensure links aren't parsed twice. We put the links back in place
	 * towards the end of parsing.
	 * 
	 * @param      array $matches Text matching link pattern
	 * @return     string
	 */
	public function linkAuto($matches)
	{
		if (empty($matches))
		{
			return '';
		}

		$whole = $matches[0];
		$prtcl = rtrim($matches[1], ':');
		$url   = $matches[3];

		$prfx  = preg_replace('/^([\s]*)(.*)/i', "$1", $whole);
		$href  = trim($whole);

		if (substr($href, 0, 1) == '!') 
		{
			return $prfx . ltrim($href, '!');
		}

		$txt = $href;

		if ($prtcl == 'mailto')
		{
			$txt  = $url;
			$href = 'mailto:' . $this->obfuscate($url);
		}

		$this->_data['links'][] = array(
			'link'     => $whole, 
			'url'      => $href, 
			'page_id'  => $this->get('pageid'),
			'scope'    => 'external',
			'scope_id' => 0
		);

		return $prfx . $this->_dataPush(array(
			$matches[0],
			'anchor',
			$this->_randomString(),
			'<a class="ext-link" href="' . $href . '" rel="external">' . $this->glyphs($txt) . '</a>'
		));
	}

	/**
	 * Obfuscate an email address
	 * 
	 * @param      string $email Address to obfuscate
	 * @return     string
	 */
	public function obfuscate($email)
	{
		$length = strlen($email);
		$obfuscatedEmail = '';
		for ($i = 0; $i < $length; $i++) 
		{
			$obfuscatedEmail .= '&#' . ord($email[$i]) . ';';
		}
		
		return $obfuscatedEmail;
	}

	/**
	 * Generate a link to an external (off-site) page
	 * Link is pushed to internal array and placeholder returned
	 * This is to ensure links aren't parsed twice. We put the links back in place
	 * towards the end of parsing.
	 * 
	 * @param      array $matches Text matching internal link pattern
	 * @return     string Placeholder tag
	 */
	public function linkInternal($matches)
	{
		$whole     = $matches[1];
		$namespace = trim(strtolower($matches[2]));
		$href      = $matches[3];
		$scope     = $this->get('scope');

		// Flag to NOT link contents
		if ($href[0] == '!') 
		{
			$whole = trim($whole, '[]');
			$whole = ltrim($whole, '!');

			return $this->_dataPush(array(
				$whole,
				'anchor',
				$this->_randomString(),
				'[' . trim($whole) . ']'
			));
		}

		$title = (isset($matches[4])) ? trim($matches[4]) : $href;
		$title = preg_replace('/\(.*?\)/', '', $title);
		$title = preg_replace('/^.*?\:/', '', $title);
		$cls   = 'wiki int-link';

		// Are we placing an anchor?
		if (substr($href, 0, 2) == '=#')
		{
			return $this->_dataPush(array(
				$whole,
				'anchor',
				$this->_randomString(),
				'<a name="' . ltrim($href, '=#') . '"></a>'
			));
		}
		// Are we jumping to an anchor?
		else if (substr($href, 0, 1) == '#')
		{
			return $this->_dataPush(array(
				$whole,
				'anchor',
				$this->_randomString(),
				'<a class="' . $cls . '" href="' . $href . '">' . $this->glyphs($title) . '</a>'
			));
		}

		// Break URL into parts
		$bits = explode('/', $href);

		// If there's more than one piece
		if (count($bits) > 1) 
		{
			// pagename will be the last piece
			$pagename = array_pop($bits);
			// scope is everything leading up to the last piece
			$scope = implode('/', $bits);
		} 
		// Only one part. pagename = URL
		else 
		{
			$pagename = end($bits);
		}

		// How parsing commences can depend upon the namespace
		switch ($namespace)
		{
			case 'page':
			case 'wiki':
				if (substr($href, 0, strlen('&#8220;')) == '&#8220;')
				{
					$title = substr($whole, strlen('[' . $namespace . ':&#8220;'));
					$title = substr($title, 0, -strlen('&#8221;]'));
					$href  = '@';
					$pagename = $title;
				}
				if (substr($href, 0, 1) == '"')
				{
					$title = substr($whole, strlen('[' . $namespace . ':"'));
					$title = substr($title, 0, -strlen('"]'));
					$href  = '@';
					$pagename = $title;
				}
			break;

			case 'help':
			case 'special':
			case 'template':
			case 'image':
			case 'file':
			default:
				$pagename = ($namespace) ? ucfirst($namespace) . ':' . $pagename : $pagename;
			break;
		}

		if (substr($scope, 0, strlen($this->get('scope'))) != $this->get('scope'))
		{
			$scope = $this->get('scope') . DS . ltrim($scope, DS);
		}

		if ($namespace == 'help')
		{
			$p = WikiPage::getInstance($pagename, '');
			$p->scope = $scope;
		}
		else
		{
			$p = WikiPage::getInstance($pagename, $scope);
		}

		switch (substr($href, 0, 1))
		{
			case '#':  // Anchors
			case '?':  // Links that start with just a query string
				$p->pagename = '';
				$p->scope = $scope;

				$href = JRoute::_('index.php?option=' . $this->get('option') . '&scope=' . $p->scope . '&pagename=' . $p->pagename . $href);
			break;

			case '@':  // Wiki page linked by title [wiki:"My Title"]
				// Page not found
				if (!$p->id)
				{
					$p->scope = $scope;
					$href = '#';

					if (in_array($namespace, array('wiki', 'page')))
					{
						$cls .= ' missing';
					}
					if (!$p->pagename && $title)
					{
						$p->pagename = urlencode($title);
					}
				}
				else
				{
					$href = '';
				}

				$href = JRoute::_('index.php?option=' . $this->get('option') . '&scope=' . $p->scope . '&pagename=' . $p->pagename . $href);
			break;

			case '/':  // Absolute paths
				//$p->pagename = '';
			break;

			default:   // Everything else
				// Page not found
				if (!$p->id)
				{
					// Check reserved and dynamic namespaces
					if (in_array($namespace, array('special', 'help', 'image', 'file', 'template')))
					{
						$href = ucfirst($namespace) . ':' . $href;
					}

					if (!in_array($namespace, array('special', 'image', 'file')))
					{
						$cls .= ' missing';
					}

					$p->scope    = ($p->scope) ? $p->scope : $scope;
					$p->pagename = $href;
				} 
				else 
				{
					//$title = ($title == $href) ? $p->title;
					$p->scope = $scope;
				}

				$href = JRoute::_('index.php?option=' . $this->get('option') . '&scope=' . $p->scope . '&pagename=' . $p->pagename);
			break;
		}

		$this->_data['links'][] = array(
			'link'     => $whole, 
			'url'      => $href, 
			'page_id'  => $this->get('pageid'),
			'scope'    => 'internal',
			'scope_id' => $p->id
		);

		return $this->_dataPush(array(
			$whole,
			'anchor',
			$this->_randomString(),
			'<a class="' . $cls . '" href="' . str_replace(array('\\', '"', "'"), '', $href) . '">' . $this->glyphs(trim($title)) . '</a>'
		));
	}

	/**
	 * Generate a link to an internal (same site) page
	 * Link is pushed to internal array and placeholder returned
	 * This is to ensure links aren't parsed twice. We put the links back in place
	 * towards the end of parsing.
	 * 
	 * @param      array $matches Text matching internal link pattern
	 * @return     string Placeholder tag
	 */
	public function linkExternal($matches)
	{
		// Name some parts to make work easier
		$whole    = $matches[1];
		$protocol = $matches[2];
		$href     = $matches[3];
		$title    = (isset($matches[4])) ? $matches[4] : '';

		if (!$title) 
		{
			$title = $href;
		}
		$href = $protocol . $href;

		$this->_data['links'][] = array(
			'link'     => $whole, 
			'url'      => $href, 
			'page_id'  => $this->get('pageid'),
			'scope'    => 'external',
			'scope_id' => 0
		);

		return $this->_dataPush(array(
			$matches[0],
			'anchor',
			$this->_randomString(),
			'<a class="ext-link" href="' . $href . '" rel="external">' . $this->glyphs(trim($title)) . '</a>'
		));
	}

	/**
	 * Generate a link to a wiki page based on page name
	 * 
	 * @param      string $name   Page name
	 * @param      string $anchor Anchor
	 * @return     string
	 */
	public function linkWikiName($matches)
	{
		$whole = $matches[0];
		$name  = $matches[1];
		$cls   = 'wiki';

		// Trim leading '!'.
		if ($name[0] == '!') 
		{
			return ltrim($name, '!');
		}

		$bits = explode('/', $name);
		if (count($bits) > 1) 
		{
			$pagename = array_pop($bits);
			$scope = implode('/', $bits);
		} 
		else 
		{
			$pagename = end($bits);
			$scope = $this->get('scope');
		}

		$p = WikiPage::getInstance($pagename, $scope);

		if ((!is_object($p) || !$p->id) && substr($name, 0, 1) != '?') 
		{
			$cls .= ' missing';
		}

		$link = JRoute::_('index.php?option=' . $this->get('option') . '&scope=' . $scope . '&pagename=' . $pagename);

		$this->_data['links'][] = array(
			'link'     => $name, 
			'url'      => $link, 
			'page_id'  => $this->get('pageid'),
			'scope'    => 'internal',
			'scope_id' => $p->id
		);

		return $this->_dataPush(array(
			$name,
			'anchor',
			$this->_randomString(),
			'<a href="' . $link . '" class="' . $cls . '">' . $this->glyphs($name) . '</a>'
		));
	}

	/**
	 * Strip <pre> and <code> blocks from text
	 * 
	 * @param      string $text Wiki markup
	 * @return     string 
	 */
	private function strip($text)
	{
		$output = array();

		// preformatted code state
		$codestate = self::CS_NONE;
		$level     = 0;     // Level of nesting
		$random    = '';    // Random string
		$admon     = false;
		$prfx      = '';    // Space indention

		$lines = explode("\n", $text);

		foreach ($lines as $i => $txt)
		{
			// Get the amount of indention
			// Indention occurs when formatting code blocks inside of list items
			//  * Item
			//    {{{
			//    blorg
			//    }}}
			// Since <pre> displays the extra spacing, we need to cut it out
			$indent = strspn($txt, ' ');
			$prfx = substr($txt, 0, $indent);

			$line = trim($txt);
			$processor = '';

			if ('' == $line) 
			{
				$output[] = $txt;
			} 
			else if ('{{{}}}' == $line) 
			{
				$output[] = '';
			}
			// pre blocks must start a line
			else if ('{{{' == substr($line, 0, strlen('{{{'))) 
			{
				if ($codestate == self::CS_NONE)
				{
					// Is there a processor declaration on the same line?
					if (strlen($line) > strlen('{{{')) 
					{
						$subline = substr($line, strlen('{{{'));
						if (substr($subline, 0, strlen('#!')) == '#!') 
						{
							if (substr($subline, 0, strlen('#!wiki')) == '#!wiki')
							{
								$admon = true;
							}
							$processor = "\n" . $subline;
						}
					}
					// Does the next line contain a processor declaration?
					else if (isset($lines[$i+1]))
					{
						$next = trim($lines[$i+1]);
						if (substr($next, 0, strlen('#!wiki')) == '#!wiki')
						{
							$admon = true;
						}
					}

					$random = $this->_randomString();
					$output[] = ($admon) 
							? $prfx . '{admonition ' . $random . '}' . $processor 
							: $prfx . '<pre ' . $random . '>' . $processor;
					$codestate = self::CS_CODE;
				}
				// We're already in a code block, so we have nested {{{ }}}
				else
				{
					$output[] = substr($txt, $indent); //$txt;
					$codestate = self::CS_CODE_SUB; // Nested code tags
					$level++;
				}
			} 
			// pre blocks must end a line
			else if ('}}}' == substr($line, 0, strlen('}}}'))) 
			{
				if ($codestate == self::CS_CODE_SUB)
				{
					$output[] = substr($txt, $indent);//$txt;
					$level--;
					if (!$level)
					{
						$codestate = self::CS_CODE;
					}
				}
				else if ($codestate == self::CS_CODE)
				{
					$output[] = ($admon) 
							? '{/admonition ' . $random . '}'
							: '</pre ' . $random . '>';
					$admon = false;
					$codestate = self::CS_NONE;
				}
				else
				{
					$output[] = $txt;
				}
			} 
			else
			{
				// Not in a code block
				if ($codestate == self::CS_NONE)
				{
					// Convert inline `code`
					$txt = $this->_code($txt);
				}
				// IN a code block
				else
				{
					// Strip indention
					$txt = substr($txt, $indent);
				}

				$output[] = $txt;
			}
		}

		// Added insurance that all tags are closed
		if ($codestate == self::CS_CODE)
		{
			do {
				$output[] = '</pre ' . $this->token() . '>';
				$codestate = self::CS_NONE;
				$level--;
			} while ($level >= 0);
		}

		$output = implode("\n", $output);
		$output = preg_replace_callback('/<(pre) (.+?)>(.*)<\/(\1) \2>/si', array(&$this, '_dataPush'), $output);

		return $output;
	}

	/**
	 * Store an item in the shelf
	 * Returns a unique ID as a placeholder for content retrieval later on
	 * 
	 * @param      string $val Content to store
	 * @return     integer Unique ID
	 */
	private function _dataPush($matches)
	{
		$tag = $matches[1];
		$key = $matches[2];
		$val = $matches[3];

		$this->_tokens[$tag][$key] = $val;
		return '<' . $tag . ' ' . $key . '></' . $tag . ' ' . $key . '>';
	}

	/**
	 * Store an item in the shelf
	 * Returns a unique ID as a placeholder for content retrieval later on
	 * 
	 * @param      string $val Content to store
	 * @return     integer Unique ID
	 */
	private function _dataPull($matches)
	{
		$tag = $matches[1];
		$key = $matches[2];

		if (isset($this->_tokens[$tag]) && isset($this->_tokens[$tag][$key]))
		{
			return $this->{'_restore' . ucfirst($tag)}($this->_tokens[$tag][$key]);
		}
		return $matches[0];
	}

	/**
	 * Put <pre> blocks back into the main content flow
	 * 
	 * @param      string  $text Wiki markup
	 * @param      boolean $html Escape HTML?
	 * @return     string
	 */
	private function unstrip($text, $html=true)
	{
		$this->set('wikitohtml', $html);

		foreach ($this->_tokens as $tag => $vals)
		{
			$text = preg_replace_callback('/<(' . $tag . ') (.+?)>(.*)<\/(\1) \2>/si', array(&$this, '_dataPull'), $text);
			$this->_tokens[$tag] = array();
		}

		return $text;
	}

	/**
	 * Restores <pre></pre> blocks to their actual content
	 * 
	 * @param      array $matches Parameter description (if any) ...
	 * @return     string
	 */
	private function _restorePre($txt)
	{
		//$txt = array_shift($this->pres);

		if (!$this->get('wikitohtml', true)) 
		{
			return '{{{' . $txt . '}}}';
		}

		$ttxt = trim($txt);

		$lines = explode("\n", $ttxt);
		$t = (isset($lines[0])) ? trim($lines[0]) : '';

		// Check for processor flag
		if (substr($t, 0, 2) == '#!') 
		{
			$t = strtolower(substr($t, 2));
			if (strstr($t, ' '))
			{
				$t = strstr($t, ' ', true);
			}

			switch ($t)
			{
				case 'wiki':
					$cls = (isset($lines[0])) ? trim($lines[0]) : '';
					$cls = trim(strstr($cls, ' '));

					$txt = str_replace('#!wiki ' . $cls, '', $txt);

					return '<div class="admon-' . $cls . '">' . $this->cleanXss($txt) . '</div>';
				break;

				case 'html':
					if ($this->get('fullparse'))
					{
						$txt = $this->cleanXss($txt);
						return preg_replace('/#!html/', '', $txt, 1);
					}
					else
					{
						return '<strong>' . JText::_('Wiki HTML blocks not allowed') . '</strong>';
					}
				break;

				case 'htmlcomment':
					$txt = preg_replace("/(\#\!$t\s*)/i", '', $txt);
					return '<!-- ' . $this->encodeHtml($txt) . ' -->';
				break;

				case 'c':
				case 'cpp':
				case 'python':
				case 'perl':
				case 'php':
				case 'ruby':
				case 'asp':
				case 'java':
				case 'js':
				case 'sql':
				case 'xml':
				case 'sh':
					jimport('geshi.geshi');

					$txt = preg_replace("/(\#\!$t\s*)/i", '', $txt);
					$txt = trim($txt, "\n\r\t");

					$geshi = new GeSHi('', $t);
					$geshi->set_header_type(GESHI_HEADER_DIV);
					$geshi->set_source($txt);

					return '<div class="pre ' . $t . '">' . $geshi->parse_code() . '</div>';
				break;

				case 'default':
				default:
					//$txt = preg_replace("/(\#\!$t\s*)/i", '', $txt);
					//$txt = trim($txt, "\n\r\t");
					return '<pre>' . $this->encodeHtml($txt) . '</pre>';
				break;
			}
		}

		return '<pre>' . $this->encodeHtml($txt) . '</pre>';
	}

	/**
	 * Parse code declarations
	 *    `{{{...}}}`  ->  <code>{{{...}}}</code>
	 *    {{{`...`}}}  ->  <code>`...`</code>
	 *    {{{...}}}    ->  <code>...</code>
	 *    `...`        ->  <code>...</code>
	 * 
	 * @param      string $text Text to replace code blocks in
	 * @return     string
	 */
	private function _code($text)
	{
		static $rules = array(
			'`({{{)' => '(}}})`',
			'{{{(`)' => '(`)}}}',
			'{{{'    => '}}}',
			'`'      => '`',
		);

		foreach ($rules as $start => $end)
		{
			$text = preg_replace_callback('/(^|\s|[[({>"])' . preg_quote($start, '/') . '(.*?)' . preg_quote($end, '/') . '(\s|$|["\])}])?/ms', array(&$this, '_getCode'), $text);
		}

		return $text;
	}

	/**
	 * `{{{...}}}`
	 * 
	 * @param      unknown $m Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function _getCode($m)
	{
		@list($whole, $before, $text, $after) = $m;

		return $before . $this->_dataPush(array(
			$whole,
			'code',
			$this->_randomString(),
			$text
		)) . $after;
	}

	/**
	 * Restores <code></code> blocks to their actual content
	 * 
	 * @param      string $txt
	 * @return     string 
	 */
	private function _restoreCode($txt)
	{
		if (!$this->get('wikitohtml', true)) 
		{
			$txt = str_replace('<code>', '', $txt);
			$txt = str_replace('</code>', '', $txt);
			if (substr($txt, 0, 1) == '`')
			{
				return '{{{' . $txt . '}}}';
			}
			return '`' . $txt . '`';
		}

		$t = trim($txt);
		$t = str_replace("\n", '', $t);

		return '<code>' . $this->encodeHtml($txt) . '</code>';
	}

	/**
	 * Very basic HTML entity encoder
	 * 
	 * @param      string  $str    Text to encode.
	 * @param      integer $quotes Encode quotes?
	 * @return     string
	 */
	private function encodeHtml($str, $quotes=1)
	{
		$a = array(
			'&' => '&#38;',
			'<' => '&#60;',
			'>' => '&#62;',
		);
		if ($quotes) $a = $a + array(
			"'" => '&#39;',
			'"' => '&#34;',
		);

		return strtr($str, $a);
	}

	/**
	 * Clean potential Cross-Site Scripting hazards from a strong
	 * 
	 * @param      string $string Content to be cleaned
	 * @return     string
	 */
	private function cleanXss($string)
	{
		// Strip out any KL_PHP, script, style, HTML comments
		$string = preg_replace('/{kl_php}(.*?){\/kl_php}/s', '', $string);
		$string = preg_replace("'<style[^>]*>.*?</style>'si", '', $string);
		$string = preg_replace("'<script[^>]*>.*?</script>'si", '', $string);
		$string = preg_replace('/<!--.+?-->/', '', $string);

		$string = str_replace(
			array('&amp;', '&lt;', '&gt;'), 
			array('&amp;amp;', '&amp;lt;', '&amp;gt;'), 
			$string
		);

		// Fix &entitiy\n;
		$string = preg_replace('#(&\#*\w+)[\x00-\x20]+;#u',"$1;",$string);
		$string = preg_replace('#(&\#x*)([0-9A-F]+);*#iu',"$1$2;",$string);
		$string = html_entity_decode($string, ENT_COMPAT, "UTF-8");

		// Remove any attribute starting with "on" or xmlns
		//$string = preg_replace('#(<[^>]+[\x00-\x20\"\'])(on|xmlns)[^>]*>#iUu', "$1>", $string);

		// Remove javascript: and vbscript: protocol
		$string = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*)[\\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2nojavascript...', $string);
		$string = preg_replace('#([a-z]*)[\x00-\x20]*=([\'\"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2novbscript...', $string);

		// <span style="width: expression(alert('Ping!'));"></span> 
		// Only works in ie...
		$string = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*expression[\x00-\x20]*\([^>]*>#iU', "$1>", $string);
		$string = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*behaviour[\x00-\x20]*\([^>]*>#iU', "$1>", $string);
		$string = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*>#iUu', "$1>", $string);

		// Remove namespaced elements (we do not need them...)
		$string = preg_replace('#</*\w+:\w[^>]*>#i',"",$string);

		// Remove really unwanted tags
		do {
			$oldstring = $string;
			$string = preg_replace('#</*(applet|meta|xml|blink|link|head|body|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i', '', $string);
		} while ($oldstring != $string);

		return $string;
	}

	/**
	 * Admonitions
	 * 
	 * @param      string $text Wiki markup
	 * @return     string Parsed wiki content
	 */
	private function admonitions($text)
	{
		$this->set('wikitohtml', true);
		return preg_replace_callback('/\{admonition ([^\}]+)\}(.*?)\{\/admonition \1\}/s', array(&$this, '_getAdmonition'), $text); //admonitionCallback
	}

	/**
	 * Convert the admonition content to HTML
	 * 
	 * @param      array $matches Content matching {admonition} ... {/admonition}
	 * @return     string Parsed wiki content
	 */
	private function _getAdmonition($matches)
	{
		return $this->_restorePre($matches[2]);
	}

	/**
	 * Convert math forumlas
	 * 
	 * @param      string $text Wiki markup
	 * @return     string Parsed wiki content
	 */
	private function math($text)
	{
		$path = dirname(__FILE__);
		if (is_file($path . DS . 'math.php')) 
		{
			include_once($path . DS . 'math.php');
			include_once($path . DS . 'math' . DS . 'math.php');
		} 
		else 
		{
			return $text;
		}

		return preg_replace_callback('/<math>(.*?)<\/math>/s', array(&$this, '_getMath'), $text);
	}

	/**
	 * Render a math forumla
	 * Output depends on complexity of formula. HTML is tried first with image used for complex formulas
	 * 
	 * @param      array $mtch_arr Wiki markup matching a <math>formula</math>
	 * @return     string
	 */
	private function _getMath($matches)
	{
		$m = MathRenderer::renderMath(trim($matches[1]), array(
			'option' => $this->get('option')
		));

		return $this->_dataPush(array(
			$matches[0],
			'math',
			$this->_randomString(),
			$m
		));
	}

	/**
	 * Format math output before injecting back into primary text
	 * 
	 * @param      string $txt Math output
	 * @return     string
	 */
	private function _restoreMath($txt)
	{
		return '<span class="asciimath">' . $txt . '</span>';
	}

	/**
	 * Search for include syntax and replace with any included text
	 *   [[Include(SomePage)]]
	 * 
	 * @param      string $text Raw wiki markup
	 * @return     string
	 */
	private function includes($text)
	{
		return preg_replace_callback('/\[\[(include)(\]\]|\((.*)\)\]\])/Ui', array(&$this, '_getInclude'), $text);
	}

	/**
	 * Retrieve an included page
	 * This is recursive and should look for inclusions in any included page.
	 * 
	 * @param      array $matches Pattern matches from includes() method
	 * @return     string
	 */
	private function _getInclude($matches)
	{
		if (isset($matches[1]) && $matches[1] != '') 
		{
			if (strtolower($matches[1]) != 'include') 
			{
				return $matches[0];
			}
			if (!$this->get('fullparse'))
			{
				return "'''Includes not allowed.'''";
			}

			$scope = ($this->get('domain')) ? $this->get('domain') . DS . 'wiki' : $this->get('scope');
			if (strstr($matches[3], '/')) 
			{
				$bits = explode('/', $matches[3]);
				$pagename = array_pop($bits);
				$s = trim(implode('/', $bits));
				$scope .= DS . trim($s, DS);
			} 
			else 
			{
				$pagename = $matches[3];
			}

			// Don't include this page (infinite loop!)
			if ($pagename == $this->get('pagename') 
				&& $scope == $this->get('scope'))
			{
				return '';
			}

			// Load the page
			$p = WikiPage::getInstance($pagename, $scope);
			if ($p->id) 
			{
				// Parse any nested includes
				return $this->includes(
					$p->getCurrentRevision()->pagetext
				);
			}
		}
		return '';
	}

	/**
	 * Parse macro tags
	 * [[MacroName(args)]]
	 * 
	 * @param      string $text Raw wiki markup
	 * @return     string
	 */
	private function macros($text)
	{
		$path = dirname(__FILE__);
		if (is_file($path . DS . 'macro.php')) 
		{
			// Include abstract macro class
			include_once($path . DS . 'macro.php');
		} 
		else 
		{
			// Abstract macro class not found
			// Proceed no further
			return $text;
		}

		$this->macros = array();

		// Get macros [[name(args)]]
		return preg_replace_callback('/\[\[(?P<macroname>[\w]+)(\]\]|\((?P<macroargs>.*)\)\]\])/U', array(&$this, '_getMacro'), $text);
	}

	/**
	 * Attempt to load a specific macro class and return its contents
	 * 
	 * @param      array $matches Result form [[Macro()]] pattern matching
	 * @return     string
	 */
	private function _getMacro($matches)
	{
		static $_macros;

		if (isset($matches[1]) && $matches[1] != '') 
		{
			// [[br]]
			if (strtolower($matches[1]) == 'br') 
			{
				return '<br />';
			}

			$matches[1] = strtolower($matches[1]);
			$macroname = ucfirst($matches[1]) . 'Macro';

			if (!$this->get('domain') && strtolower(substr($macroname, 0, 5)) == 'group') 
			{
				return '<strong>Macro "' . $macroname . '" can only be used in a group wiki.</strong>';
			}

			if (!isset($_macros[$matches[1]])) 
			{
				$path = dirname(__FILE__);
				if (is_file($path . DS . 'macros' . DS . $matches[1] . '.php')) 
				{
					include_once($path . DS . 'macros' . DS . $matches[1] . '.php');
				} 
				else 
				{
					return '';
				}

				if (class_exists($macroname)) 
				{
					$macro = new $macroname();

					if (!$this->get('fullparse') && !$macro->allowPartial)
					{
						return '<strong>Macro "' . $matches[1] . '" not allowed.</strong>';
					}

					$_macros[$matches[1]] =& $macro;
				} 
				else 
				{
					$_macros[$matches[1]] = false;
				}
			} 
			else 
			{
				$macro =& $_macros[$matches[1]];
			}

			if (!is_object($macro)) 
			{
				return '';
			}

			$macro->args = null;
			if (isset($matches[3]) && $matches[3]) 
			{
				$macro->args = $matches[3];
			}
			$macro->option     = $this->get('option');
			$macro->scope      = $this->get('scope');
			$macro->pagename   = $this->get('pagename');
			$macro->domain     = $this->get('domain');
			$macro->uniqPrefix = $this->token();
			if ($this->get('pageid') > 0) 
			{
				$macro->pageid = $this->get('pageid');
			} 
			else 
			{
				$macro->pageid = JRequest::getInt('lid', 0, 'post');
			}
			$macro->filepath   = $this->get('filepath');

			// Push contents to a container -- we'll retrieve this later
			// This is done to prevent any further wiki parsing of contents macro may return
			if (count($macro->linkLog) > 0)
			{
				foreach ($macro->linkLog as $linkLog)
				{
					array_push($this->_data['links'], $linkLog);
				}
			}

			return $this->_dataPush(array(
				$matches[1],
				'macro',
				$this->_randomString(),
				$macro->render()
			));
		}
	}

	/**
	 * Put macro output back into the text
	 * 
	 * @param      string $txt
	 * @return     string
	 */
	private function _restoreMacro($txt)
	{
		return $txt;
	}

	/**
	 * Convert common special characters to their HTML entity counterpart
	 * 
	 * @param      string $text Wiki markup
	 * @return     string
	 */
	public function glyphs($text)
	{
		// fix: hackish
		$text = preg_replace('/"\z/', "\" ", $text);
		$pnc = '[[:punct:]]';

		$glyph_search = array(
			'/(\w)\'(\w)/', 									 //  apostrophe's
			'/(\s)\'(\d+\w?)\b(?!\')/', 						 //  back in '88
			'/(\S)\'(?=\s|' . $pnc . '|<|$)/',					 //  single closing
			'/\'/', 											 //  single opening
			'/(\S)\"(?=\s|' . $pnc . '|<|$)/',					 //  double closing
			'/"/',												 //  double opening
			//'/\b([A-Z][A-Z0-9]{2,})\b(?:[(]([^)]*)[)])/',		 //  3+ uppercase acronym
			//'/(?<=\s|^|[>(;-])([A-Z]{3,})([a-z]*)(?=\s|' . $pnc . '|<|$)/',  //  3+ uppercase
			'/([^.]?)\.{3}/',									 //  ellipsis
			'/(\s?)--(\s?)/',									 //  em dash
			'/\s-(?:\s|$)/',									 //  en dash
			'/(\d+)(?)x(?)(?=\d+)/',							 //  dimension sign
			'/(\b ?|\s|^)[([]TM[])]/i', 						 //  trademark
			'/(\b ?|\s|^)[([]R[])]/i',							 //  registered
			'/(\b ?|\s|^)[([]C[])]/i',							 //  copyright
		);

		$glyph = array(
			'quote_single_open'	=> '&#8216;',
			'quote_single_close' => '&#8217;',
			'quote_double_open'	=> '&#8220;',
			'quote_double_close' => '&#8221;',
			'apostrophe' 		=> '&#8217;',
			'prime'				=> '&#8242;',
			'prime_double'		=> '&#8243;',
			'ellipsis'			=> '&#8230;',
			'emdash' 			=> '&#8212;',
			'endash' 			=> '&#8211;',
			'dimension'			=> '&#215;',
			'trademark'			=> '&#8482;',
			'registered' 		=> '&#174;',
			'copyright'			=> '&#169;',
		);
		extract($glyph, EXTR_PREFIX_ALL, 'txt');

		$glyph_replace = array(
			'$1' . $txt_apostrophe . '$2',       //  apostrophe's
			'$1' . $txt_apostrophe . '$2',       //  back in '88
			'$1' . $txt_quote_single_close,      //  single closing
			$txt_quote_single_open,              //  single opening
			'$1' . $txt_quote_double_close,      //  double closing
			$txt_quote_double_open,              //  double opening
			//'<acronym title="$2">$1</acronym>',  //  3+ uppercase acronym
			//'<span class="caps">$1</span>$2',    //  3+ uppercase
			'$1' . $txt_ellipsis,                //  ellipsis
			'$1' . $txt_emdash . '$2',           //  em dash
			' ' . $txt_endash . ' ',             //  en dash
			'$1$2' . $txt_dimension . '$3',      //  dimension sign
			'$1' . $txt_trademark,               //  trademark
			'$1' . $txt_registered,              //  registered
			'$1' . $txt_copyright,               //  copyright
		);

		$text = preg_split("@(<[\w/!?].*>)@Us", $text, -1, PREG_SPLIT_DELIM_CAPTURE);
		$i = 0;
		foreach ($text as $line)
		{
			// text tag text tag text ...
			if (++$i % 2) 
			{
				$line = $this->encodeHtml($line, 0);
				$line = preg_replace($glyph_search, $glyph_replace, $line);
			}
			$glyph_out[] = $line;
		}
		return join('', $glyph_out);
	}

	/**
	 * Parse Block Attributes
	 * 
	 * @param      string  $in         Wiki markup for attributes
	 * @param      string  $element    HTML element type
	 * @param      integer $include_id Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	private function pba($in, $element = '', $include_id = 1)
	{
		$style = '';
		$class = '';
		$lang = '';
		$colspan = '';
		$rowspan = '';
		$id = '';
		$atts = '';

		if (!empty($in)) 
		{
			$matched = $in;
			if ($element == 'td') 
			{
				if (preg_match("/\\\\(\d+)/", $matched, $csp)) 
				{
					$colspan = $csp[1];
				}
				if (preg_match("/\/(\d+)/", $matched, $rsp)) 
				{
					$rowspan = $rsp[1];
				}
			}

			if ($element == 'td' or $element == 'tr') 
			{
				if (preg_match("/(" . self::$_patterns['vlgn'] . ")/", $matched, $vert))
				{
					$style[] = 'vertical-align:' . $this->vAlign($vert[1]) . ';';
				}
			}

			if (preg_match("/\{([^}]*)\}/", $matched, $sty)) 
			{
				$style[] = rtrim($sty[1], ';') . ';';
				$matched = str_replace($sty[0], '', $matched);
			}

			if (preg_match("/\[([^]]+)\]/U", $matched, $lng)) 
			{
				$lang = $lng[1];
				$matched = str_replace($lng[0], '', $matched);
			}

			if (preg_match("/\(([^()]+)\)/U", $matched, $cls)) 
			{
				$class = $cls[1];
				$matched = str_replace($cls[0], '', $matched);
			}

			if (preg_match("/([(]+)/", $matched, $pl)) 
			{
				$style[] = 'padding-left:' . strlen($pl[1]) . 'em;';
				$matched = str_replace($pl[0], '', $matched);
			}

			if (preg_match("/([)]+)/", $matched, $pr)) 
			{
				$style[] = 'padding-right:' . strlen($pr[1]) . 'em;';
				$matched = str_replace($pr[0], '', $matched);
			}

			if (preg_match("/(" . self::$_patterns['hlgn'] . ")/", $matched, $horiz))
			{
				$style[] = 'text-align:' . $this->hAlign($horiz[1]) . ';';
			}

			if (preg_match("/^(.*)#(.*)$/", $class, $ids)) 
			{
				$id = $ids[2];
				$class = $ids[1];
			}

			return join('', array(
				($style)   ? ' style="'   . join("", $style) . '"':'',
				($class)   ? ' class="'   . $class           . '"':'',
				($lang)    ? ' lang="'    . $lang            . '"':'',
				($id and $include_id) ? ' id="' . $id        . '"':'',
				($colspan) ? ' colspan="' . $colspan         . '"':'',
				($rowspan) ? ' rowspan="' . $rowspan         . '"':''
			));
		}
		return '';
	}

	/**
	 * Horizontal alignment 
	 * markup => value
	 * 
	 * @param      string $in Markup
	 * @return     string Value
	 */
	private function hAlign($in)
	{
		$vals = array(
			'<'  => 'left',
			'='  => 'center',
			'>'  => 'right',
			'<>' => 'justify'
		);
		return (isset($vals[$in])) ? $vals[$in] : '';
	}

	/**
	 * Vertical alignment 
	 * markup => value
	 * 
	 * @param      string $in Markup
	 * @return     string Value
	 */
	private function vAlign($in)
	{
		$vals = array(
			'^' => 'top',
			'-' => 'middle',
			'~' => 'bottom'
		);
		return (isset($vals[$in])) ? $vals[$in] : '';
	}

	/**
	 * Parse markup syntax for several inline elements and their allowed attributes
	 * [cite, u, del, span, ins, sub, sup]
	 * 
	 * @param      string $text Wiki markup
	 * @return     string
	 */
	private function spans($text)
	{
		$qtags = array(
			'\?\?',
			'__',
			'%',
			'\+',
			'~~',
			',,',
			'\^'
		);
		$pnct = ".,\"'?!;:";

		$c = self::$_patterns['clss'];

		foreach ($qtags as $f) 
		{
			$text = preg_replace_callback("/
				(^|(?<=[\s>$pnct\(])|[{[])
				($f)(?!$f)
				({$c})
				(?::(\S+))?
				([^\s$f]+|\S.*?[^\s$f\n])
				([$pnct]*)
				$f
				($|[\]}]|(?=[[:punct:]]{1,2}|\s|\)))
			/x", array(&$this, '_getSpan'), $text);
		}
		$text = preg_replace('/\^(.*?)\^/', "<sup>\\1</sup>", $text);
		$text = preg_replace('/,,(.*?),,/', "<sub>\\1</sub>", $text);
		return $text;
	}

	/**
	 * Convert wiki markup to HTML for common inline elements
	 * 
	 * @param      array $m Pattern matches
	 * @return     string
	 */
	private function _getSpan($m)
	{
		$qtags = array(
			'??' => 'cite',
			'__' => 'u',
			'~~' => 'del',
			'%'  => 'span',
			'+'  => 'ins',
			',,' => 'sub',
			'^'  => 'sup',
		);

		list(, $pre, $tag, $atts, $cite, $content, $end, $tail) = $m;
		$tag   = $qtags[$tag];

		$atts  = ''; //$this->pba($atts);
		$atts .= ($cite != '') ? 'cite="' . $cite . '"' : '';

		if ($tag == 'u') 
		{
			$atts .= ' style="text-decoration: underline;"';
			$tag = 'span';
		}

		$out = "<$tag$atts>$content$end</$tag>";

		if (($pre and !$tail) or ($tail and !$pre))
		{
			$out = $pre . $out . $tail;
		}

		return $out;
	}

	/**
	 * Headings h[1-6]
	 * 
	 * @param      string $text Raw wiki markup
	 * @return     string
	 */
	private function headings($text)
	{
		for ($i = 6; $i >= 1; --$i)
		{
			$h = str_repeat('=', $i);

			$patterns = array(
				"/^(.*){$h}(.+){$h}\s\#(.*)\\s*$/m",    // === Header #myheader ===
				"/^(.*){$h}(.+){$h}\\s*$/m"             // === Header ===
			);
			$replace  = array(
				"\\1<h{$i} id=\"\\3\">\\2</h{$i}>\\4",  // <h3 id="myheader">Header</h3>
				"\\1<h{$i}>\\2</h{$i}>\\3"              // <h3>Header</h3>
			);
			$text = preg_replace($patterns, $replace, $text);
		}
		return $text;
	}

	/**
	 * Quotes
	 * ''    => <i>
	 * '''   => <b>
	 * ''''' => <b><i>
	 * 
	 * @param      string $text Raw wiki markup
	 * @return     string 
	 */
	private function quotes($text)
	{
		$outtext = '';
		$lines = explode("\n", $text);
		foreach ($lines as $line)
		{
			$outtext .= $this->_getQuotes($line) . "\n";
		}
		$outtext = substr($outtext, 0, -1);
		return $outtext;
	}

	/**
	 * Convert quotes to HMTL
	 * 
	 * @param      string $text Wiki markup
	 * @return     string
	 */
	private function _getQuotes($text)
	{
		$arr = preg_split("/(''+)/", $text, -1, PREG_SPLIT_DELIM_CAPTURE);
		if (count($arr) == 1) 
		{
			return $text;
		} 
		else 
		{
			// First, do some preliminary work. This may shift some apostrophes from
			// being mark-up to being text. It also counts the number of occurrences
			// of bold and italics mark-ups.
			$i = 0;
			$numbold = 0;
			$numitalics = 0;
			foreach ($arr as $r)
			{
				if (($i % 2) == 1)
				{
					// If there are ever four apostrophes, assume the first is supposed to
					// be text, and the remaining three constitute mark-up for bold text.
					if (strlen($arr[$i]) == 4)
					{
						$arr[$i-1] .= "'";
						$arr[$i] = "'''";
					}
					// If there are more than 5 apostrophes in a row, assume they're all
					// text except for the last 5.
					else if (strlen($arr[$i]) > 5)
					{
						$arr[$i-1] .= str_repeat("'", strlen($arr[$i]) - 5);
						$arr[$i] = "'''''";
					}
					// Count the number of occurrences of bold and italics mark-ups.
					// We are not counting sequences of five apostrophes.
					if (strlen($arr[$i]) == 2)      { $numitalics++;             }
					else if (strlen($arr[$i]) == 3) { $numbold++;                }
					else if (strlen($arr[$i]) == 5) { $numitalics++; $numbold++; }
				}
				$i++;
			}

			// If there is an odd number of both bold and italics, it is likely
			// that one of the bold ones was meant to be an apostrophe followed
			// by italics. Which one we cannot know for certain, but it is more
			// likely to be one that has a single-letter word before it.
			if (($numbold % 2 == 1) && ($numitalics % 2 == 1))
			{
				$i = 0;
				$firstsingleletterword = -1;
				$firstmultiletterword  = -1;
				$firstspace = -1;
				foreach ($arr as $r)
				{
					if (($i % 2 == 1) and (strlen($r) == 3))
					{
						$x1 = substr ($arr[$i-1], -1);
						$x2 = substr ($arr[$i-1], -2, 1);
						if ($x1 == ' ') 
						{
							if ($firstspace == -1) $firstspace = $i;
						} 
						else if ($x2 == ' ') 
						{
							if ($firstsingleletterword == -1) $firstsingleletterword = $i;
						} 
						else 
						{
							if ($firstmultiletterword == -1) $firstmultiletterword = $i;
						}
					}
					$i++;
				}

				// If there is a single-letter word, use it!
				if ($firstsingleletterword > -1)
				{
					$arr[ $firstsingleletterword ] = "''";
					$arr[ $firstsingleletterword-1 ] .= "'";
				}
				// If not, but there's a multi-letter word, use that one.
				else if ($firstmultiletterword > -1)
				{
					$arr[ $firstmultiletterword ] = "''";
					$arr[ $firstmultiletterword-1 ] .= "'";
				}
				// ... otherwise use the first one that has neither.
				// (notice that it is possible for all three to be -1 if, for example,
				// there is only one pentuple-apostrophe in the line)
				else if ($firstspace > -1)
				{
					$arr[ $firstspace ] = "''";
					$arr[ $firstspace-1 ] .= "'";
				}
			}

			// Now let's actually convert our apostrophic mush to HTML!
			$output = '';
			$buffer = '';
			$state  = '';
			$i = 0;
			foreach ($arr as $r)
			{
				if (($i % 2) == 0)
				{
					if ($state == 'both')
					{
						$buffer .= $r;
					}
					else
					{
						$output .= $r;
					}
				} 
				else 
				{
					switch (strlen($r))
					{
						case 2:
							switch ($state)
							{
								case 'i':
									$output .= '</i>'; 
									$state = '';
								break;
								
								case 'bi':
									$output .= '</i>'; 
									$state = 'b';
								break;
								
								case 'ib':
									$output .= '</b>';
									$state = 'i';
								break;
								
								case 'both':
									$output .= '<b><i>' . $buffer . '</i>'; 
									$state = 'b';
								break;
								
								default:  // $state can be 'b' or ''
									$output .= '<i>'; 
									$state .= 'i';
								break;
							}
						break;
						
						case 3:
							switch ($state)
							{
								case 'b':
									$output .= '</b>';
									$state = '';
								break;
								
								case 'bi':
									$output .= '</i></b><i>';
									$state = 'i';
								break;
								
								case 'ib':
									$output .= '</i></b>';
									$state = '';
								break;
								
								case 'both':
									$output .= '<i><b>' . $buffer . '</b>';
									$state = 'i';
								break;
								
								default:  // $state can be 'b' or ''
									$output .= '<b>';
									$state .= 'b';
								break;
							}
						break;
						
						case 5:
							switch ($state)
							{
								case 'b':
									$output .= '</b><i>';
									$state = 'i';
								break;

								case 'i':
									$output .= '</i><b>';
									$state = 'b';
								break;

								case 'bi':
									$output .= '</i></b>';
									$state = '';
								break;

								case 'ib':
									$output .= '</b></i>';
									$state = '';
								break;

								case 'both':
									$output .= '<i><b>' . $buffer . '</b></i>';
									$state = '';
								break;

								default:  // $state can be 'b' or ''
									$buffer = '';
									$state = 'both';
								break;
							}
						break;
					}
				}
				$i++;
			}
			// Now close all remaining tags.  Notice that the order is important.
			if ($state == 'b' || $state == 'ib')
			{
				$output .= '</b>';
			}
			if ($state == 'i' || $state == 'bi' || $state == 'ib')
			{
				$output .= '</i>';
			}
			if ($state == 'bi')
			{
				$output .= '</b>';
			}
			// There might be lonely ''''', so make sure we have a buffer
			if ($state == 'both' && $buffer)
			{
				$output .= '<b><i>' . $buffer . '</i></b>';
			}
			return $output;
		}
	}

	/**
	 * Look for table syntax and convert
	 *   ||Cell 1||Cell 2||Cell 3||
	 *   ||Cell 4||Cell 5||Cell 6||
	 * 
	 * @param      string $text Wiki markup
	 * @return     string
	 */
	private function tables($text)
	{
		$ac  = self::$_patterns['algn'] . self::$_patterns['clss'];
		$sac = self::$_patterns['spns'] . self::$_patterns['algn'] . self::$_patterns['clss'];

		$text .= "\n\n";
		return preg_replace_callback("/^(?:table(_?{$sac})\. ?\n)?^({$ac}\.? ?\|\|.*\|\|)\n\n/smU", array(&$this, '_getTable'), $text);
	}

	/**
	 * Convert a string for wiki table syntax into a table
	 * 
	 * @param      array $matches Pattern matches for table syntax
	 * @return     string
	 */
	private function _getTable($matches)
	{
		$tatts = $this->pba($matches[1], 'table');

		$ac  = self::$_patterns['algn'] . self::$_patterns['clss'];
		$sac = self::$_patterns['spns'] . self::$_patterns['algn'] . self::$_patterns['clss'];

		foreach (preg_split("/\|\|( *)$/m", $matches[2], -1, PREG_SPLIT_NO_EMPTY) as $row)
		{
			if (preg_match("/^({$ac}\.)(.*)/m", ltrim($row), $rmtch)) 
			{
				$ratts = $this->pba($rmtch[1], 'tr');
				$row = $rmtch[2];
			} 
			else 
			{
				$ratts = '';
			}

			$cells = array();

			$colspan = 0;
			// Can't use trim($row, '|') as it would remove empy (colspan) cells.
			// EX:
			//    ||||  content ||
			//    trim result: "  content "
			// This would lead to an incorrect cell count.
			$row = preg_replace("/^\|\|(.*)(\|\|)?$/", "$1", ltrim($row));
			foreach (explode('||', $row) as $cell)
			{
				// If it's an empty cell, we're colspanning
				if ($cell == '')
				{
					// If colspan isn't set, start with 2 (we can't colspan="1"), 
					// otherwise up the count by 1 and move on to the next cell
					$colspan = ($colspan > 0) ? $colspan + 1 : 2;
					continue;
				}
				// Cell type
				$ctyp = 'd';
				if (preg_match("/^_/", $cell) || preg_match("/^=(.*)=$/", $cell)) 
				{
					$ctyp = 'h';
					$cell = substr($cell, 1, -1); //trim($cell, '=');
				}

				// Cell attributes
				if (preg_match("/^(_?{$sac}\.)(.*)/", $cell, $cmtch) || preg_match("/^(=?{$sac}\.)(.*)=?/", $cell, $cmtch)) 
				{
					// Parse Block Attributes
					$catts = $this->pba($cmtch[1], 'td');
					$cell = $cmtch[2];
				} 
				else 
				{
					$catts = '';
				}
				$prefixLength = strspn($cell, ' ', 0);
				$suffixLength = strspn($cell, ' ', $prefixLength + strlen(trim($cell)));
				if (!$catts)
				{
					if ($suffixLength == 0 && $prefixLength > 0)
					{
						$catts .= ' style="text-align:right;"';
					}
					else if ($suffixLength > 0 && $prefixLength == 0)
					{
						$catts .= ' style="text-align:left;"';
					}
					/*else if ($suffixLength > 0 && $prefixLength > 0 && $suffixLength == $prefixLength)
					{
						$catts .= ' style="text-align:justify;"';
					}*/
				}
				// Is there a colspan set?
				// If so, apply it and reset $colspan
				if ($colspan)
				{
					$catts .= ' colspan="' . $colspan . '"';
					$colspan = 0;
				}
				// Apply formatting to cell contents
				$cell = $this->spans($cell);

				$cells[] = "\t\t\t<t$ctyp$catts>$cell</t$ctyp>";
			}
			$rows[] = "\t\t<tr$ratts>\n" . join("\n", $cells) . ($cells ? "\n" : '') . "\t\t</tr>";
			unset($cells, $catts);
		}
		return "\t<table$tatts>\n" . join("\n", $rows) . "\n\t</table>\n\n";
	}

	/**
	 * Generate a closed paragraph tag
	 * 
	 * @return     string 
	 */
	private function _closeParagraph()
	{
		$result = '';
		if ('' != $this->mLastSection) 
		{
			$result = '</' . $this->mLastSection  . ">\n";
		}
		$this->mInPre = false;
		$this->mLastSection = '';
		return $result;
	}

	/**
	 * Returns the length of the longest common substring
	 * of both arguments, starting at the beginning of both.
	 * 
	 * @param      string $st1
	 * @param      string $st2
	 * @return     integer
	 */
	private function _getCommon($st1, $st2)
	{
		$fl = $st1; //strlen($st1);
		$shorter = $st2; //strlen($st2);
		if ($fl < $shorter) 
		{
			$shorter = $fl;
		}

		for ($i = 0; $i < $shorter; ++$i) 
		{
			if ($st1{$i} != $st2{$i}) 
			{ 
				break;
			}
		}
		return $i;
	}

	/**
	 * Open a list
	 * 
	 * @param      string $char List type indicator
	 * @return     string 
	 */
	private function _openList($char)
	{
		$result = $this->_closeParagraph();

		if ('*' == $char) 
		{ 
			$result .= '<ul><li>';
		}
		else if ('#' == $char) 
		{ 
			$result .= '<ol><li>';
		}
		else if (is_numeric($char)) 
		{
			$result .= '<ol style="counter-reset: item ' . (intval($char) - 1) . ';"><li>';
			$this->mDTopen = true;
		}
		else if (':' == $char) 
		{
			$result .= '<dl><dd>';
		}
		else if (';' == $char) 
		{
			$result .= '<dl><dt>';
			$this->mDTopen = true;
		}
		else 
		{ 
			$result = ''; //'<!-- ERR 1 "' . $char . '" -->';
		}

		return $result;
	}

	/**
	 * Close the current list item and continue to the next list item
	 * 
	 * @param      string $char List type indicator
	 * @return     string 
	 */
	private function _nextItem($char)
	{
		if ('*' == $char || '#' == $char || is_numeric($char)) 
		{ 
			return '</li><li>'; 
		}
		else if (':' == $char || ';' == $char) 
		{
			$close = '</dd>';
			if ($this->mDTopen) 
			{ 
				$close = '</dt>';
			}
			if (';' == $char) 
			{
				$this->mDTopen = true;
				return $close . '<dt>';
			} 
			else 
			{
				$this->mDTopen = false;
				return $close . '<dd>';
			}
		}
		return ''; //'<!-- ERR 2 "' . $char . '" -->';
	}

	/**
	 * Close a list
	 * 
	 * @param      string $char List type indicator
	 * @return     string 
	 */
	private function _closeList($char)
	{
		if ('*' == $char) 
		{ 
			$text = '</li></ul>';
		}
		else if ('#' == $char || is_numeric($char)) 
		{ 
			$text = '</li></ol>';
		}
		else if (':' == $char) 
		{
			if ($this->mDTopen) 
			{
				$this->mDTopen = false;
				$text = '</dt></dl>';
			} 
			else 
			{
				$text = '</dd></dl>';
			}
		}
		else 
		{
			return ''; //'<!-- ERR 3 "' . $char . '" -->';
		}
		return $text . "\n";
	}

	/**
	 * Create definition lists
	 *  term::
	 *    definition
	 * 
	 * @param      string $text Wiki markup
	 * @return     string
	 */
	private function definitions($text)
	{
		$textLines = explode("\n", $text);

		$indl = false;
		$indd = false;
		$output = '';
		foreach ($textLines as $oLine)
		{
			if (preg_match('/(.*?)::(\s*)/sU', $oLine)) 
			{
				if ($indl) 
				{
					$output .= '</dd>'."\n";
					//$output .= preg_replace('/\s(.*?)::(\s*)/sU', "<dt>\\1</dt>\n", $oLine);
					$output .= preg_replace('/\s*(.*?)::(\s*)/sU', "<dt>\\1</dt>\n", $oLine);
				} 
				else 
				{
					//$output .= preg_replace('/\s(.*?)::(\s*)/sU', "<dl><dt>\\1</dt>\n", $oLine);
					$output .= preg_replace('/\s*(.*?)::[ \t]*$/', "<dl><dt>\\1</dt>\n", $oLine);
				}
				$indl = true;
				$indd = false;
			} 
			else 
			{
				if ($indl) 
				{
					if (trim($oLine) == '')
					{
						$output .= '<br />' . $oLine . "\n";
						continue;
					}
					if (preg_match('/\s{2,}(.*?)/sU', $oLine)) 
					{
						if (!$indd) 
						{
							$indd = true;
							$output .= '<dd>';
						}
						else
						{
							$output .= '<br />';
						}
						$output .= trim($oLine) . "\n";
					} 
					else 
					{
						$indd = false;
						if (!preg_match('/(.*?)::(\s*)/sU', $oLine)) 
						{
							$indl = false;
							$output .= '</dd></dl>' . "\n" . $oLine . "\n";
						}
					}
				} 
				else 
				{
					$output .= $oLine . "\n";
				}
			}
		}
		//echo $output;
		return $output;
	}

	/**
	 * Make lists from lines starting with 'some text::', '*', '#', etc.
	 * 
	 * @param      string  $text      Wiki markup
	 * @param      integer $linestart Parameter description (if any) ...
	 * @return     string
	 */
	private function blocks($text, $linestart=0)
	{
		// Parsing through the text line by line.  The main thing
		// happening here is handling of block-level elements p, pre,
		// and making lists from lines starting with * # : etc.
		$textLines = explode("\n", $text);

		$lastPrefix = $output = '';
		$lastPrefixLength = 0;
		$this->mDTopen = $inBlockElem = false;
		$this->mInPre = false;
		$this->mLastSection = '';

		$prefixLength = 0;
		$paragraphStack = false;
		$openlist = array();
		$i = 0;
		if (!$linestart) 
		{
			$output .= array_shift($textLines);
		}
		$indl = false;
		$indd = false;
		foreach ($textLines as $oLine)
		{
			$preCloseMatch = preg_match('/<\\/pre/i', $oLine);
			$preOpenMatch  = preg_match('/<pre/i', $oLine);

			if (!$this->mInPre) 
			{
				// Get the prefix length
				$prefixLength = strspn($oLine, ' ');
				// Get the prefix
				if ($prefixLength > 0) 
				{
					$pref = substr(trim($oLine), 0, 1);
				} 
				else 
				{
					$pref = substr($oLine, $prefixLength, $prefixLength);
				}

				// eh?
				$pref2 = str_replace(';', ':', $pref);
				if ($prefixLength > 0 && ($pref =='*' || $pref =='#' || $pref ==';' || is_numeric($pref))) 
				{
					if (is_numeric($pref))
					{
						$t = substr($oLine, $prefixLength+2);
					}
					else
					{
						$t = substr($oLine, $prefixLength+1);
					}
				} 
				else 
				{
					$t = substr($oLine, $prefixLength);
				}
				$this->mInPre = !empty($preOpenMatch);
			} 
			else 
			{
				// Don't interpret any other prefixes in preformatted text
				$prefixLength = 0;
				$pref = $pref2 = '';
				$t = $oLine;
			}
			
			if ($prefixLength == 2 && $pref !='*' && $pref !='#' && $pref !=';' && !is_numeric($pref)) 
			{
				$t = '<blockquote>' . trim($oLine) . '</blockquote>' . "\n";
				$prefixLength = 0;
			}
			
			// List generation
			if ($prefixLength && 0 == strcmp($lastPrefix, $pref2) && $prefixLength == $lastPrefixLength) 
			{
				// Same as the last item, so no need to deal with nesting or opening stuff
				$output .= $this->_nextItem($pref);
				$paragraphStack = false;
				/*if (substr($pref, -1) == ';') 
				{
					// The one nasty exception: definition lists work like this:
					// ; title : definition text
					// So we check for : in the remainder text to split up the
					// title and definition, without b0rking links.
					$term = $t2 = '';
					if ($this->findColonNoLinks($t, $term, $t2) !== false) 
					{
						$t = $t2;
						$output .= $term . $this->_nextItem(':');
					}
				}*/
			} 
			elseif ($prefixLength || $lastPrefixLength) 
			{
				$commonPrefixLength = $this->_getCommon($prefixLength, $lastPrefixLength);
				$paragraphStack = false;

				while ($commonPrefixLength < $lastPrefixLength)
				{
					$lastPrefix = $openlist[$i--];
					$output .= $this->_closeList($lastPrefix) . '<!-- common: ' . $commonPrefixLength . ', last: ' . $lastPrefixLength . ', prefx:' . $prefixLength . ', ' . $lastPrefix . ' -->';
					--$lastPrefixLength;
				}
				if ($prefixLength <= $commonPrefixLength && $commonPrefixLength > 0) 
				{
					$output .= $this->_nextItem($pref);
				}

				$listOpened = false;
				while ($prefixLength > $commonPrefixLength)
				{
					$char = trim($pref);
					if (!$listOpened)
					{
						$output .= $this->_openList($char);
						$listOpened = true;
					}
					//if (in_array($char, array('*', '#', ':', ';')) || is_numeric($char))
					//{
						$i++;
						$openlist[$i] = $char;
					//}
					/*if (';' == $char) 
					{
						// FIXME: This is dupe of code above
						if ($this->findColonNoLinks($t, $term, $t2) !== false) 
						{
							$t = $t2;
							$output .= $term . $this->_nextItem(':');
						}
					}*/
					++$commonPrefixLength;
				}
				$lastPrefix = $pref2;
				$lastPrefixLength = $prefixLength;
			}

			if (0 == $prefixLength) 
			{
				// No prefix (not in list)--go to paragraph mode
				// XXX: use a stack for nestable elements like span, table and div
				$openmatch  = preg_match('/(?:<table|<blockquote|<h1|<h2|<h3|<h4|<h5|<h6|<pre|<tr|<p|<dl|<ul|<ol|<li|<\\/tr|<\\/td|<\\/th)/iS', $t);
				$closematch = preg_match(
					'/(?:<\\/table|<\\/blockquote|<\\/h1|<\\/h2|<\\/h3|<\\/h4|<\\/h5|<\\/h6|'.
					'<td|<th|<\\/?div|<hr|<\\/pre|<\\/p|' . $this->token() . '-pre|<\\/li|<\\/dl|<\\/ul|<\\/ol|<\\/?center)/iS', $t);
				if ($openmatch or $closematch) 
				{
					$paragraphStack = false;
					// TODO bug 5718: paragraph closed
					$output .= $this->_closeParagraph();
					if ($preOpenMatch and !$preCloseMatch) {
						$this->mInPre = true;
					}
					if ($closematch) {
						$inBlockElem = false;
					} 
					else 
					{
						$inBlockElem = true;
					}
				} 
				else if (!$inBlockElem && !$this->mInPre) 
				{
					if (' ' == $t{0} and ($this->mLastSection == 'pre' or trim($t) != '')) 
					{
						// pre
						if ($this->mLastSection != 'pre') 
						{
							$paragraphStack = false;
							$output .= $this->_closeParagraph() . '<pre>';
							$this->mLastSection = 'pre';
						}
						$t = substr($t, 1);
					} 
					else 
					{
						// paragraph
						if ('' == trim($t)) 
						{
							if ($paragraphStack) 
							{
								$output .= $paragraphStack . '<br />';
								$paragraphStack = false;
								$this->mLastSection = 'p';
							} 
							else 
							{
								if ($this->mLastSection != 'p') 
								{
									$output .= $this->_closeParagraph();
									$this->mLastSection = '';
									$paragraphStack = '<p>';
								} 
								else 
								{
									$paragraphStack = '</p><p>';
								}
							}
						} 
						else 
						{
							if ($paragraphStack) 
							{
								$output .= $paragraphStack;
								$paragraphStack = false;
								$this->mLastSection = 'p';
							} 
							else if ($this->mLastSection != 'p') 
							{
								$output .= $this->_closeParagraph() . '<p>';
								$this->mLastSection = 'p';
							}
						}
					}
				}
			}

			// somewhere above we forget to get out of pre block (bug 785)
			if ($preCloseMatch && $this->mInPre) 
			{
				$this->mInPre = false;
			}
			if ($paragraphStack === false) 
			{
				$output .= $t . "\n";
			}
		}
		while ($prefixLength) 
		{
			$output .= $this->_closeList($pref2);
			--$prefixLength;
		}
		if ('' != $this->mLastSection) 
		{
			$output .= '</' . $this->mLastSection . '>';
			$this->mLastSection = '';
		}
		return $output;
	}

	/**
	 * Builds a Table of Contents and links to headings
	 * 
	 * @param      string  $text   Text to build TOC from
	 * @return     string
	 */
	public function toc($text)
	{
		$isMain              = true;
		$maxTocLevel         = 15;
		$showEditLink        = true;
		$doNumberHeadings    = false;
		$doTocNumberHeadings = true;
		$forceTocPosition    = true;
		//$mShowToc            = true;

		// Get all headlines for numbering them and adding funky stuff like [edit]
		// links - this is for later, but we need the number of headlines right now
		$matches = array();
		$numMatches = preg_match_all('/<H(?P<level>[1-6])(?P<attrib>.*?'.'>)(?P<header>.*?)<\/H[1-6] *>/i', $text, $matches);

		// If there are fewer than 4 headlines in the article, do not show TOC
		// unless it's been explicitly enabled.
		$enoughToc = (($numMatches >= 4) || $forceTocPosition); //$mShowToc && 

		// Headline counter
		$headlineCount = 0;
		$sectionCount  = 0; // headlineCount excluding template sections
		$numVisible    = 0;

		// Ugh .. the TOC should have neat indentation levels which can be
		// passed to the skin functions. These are determined here
		$toc           = '';
		$full          = '';
		$head          = array();
		$sublevelCount = array();
		$levelCount    = array();
		$toclevel      = 0;
		$level         = 0;
		$prevlevel     = 0;
		$toclevel      = 0;
		$prevtoclevel  = 0;

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'sanitizer.php');

		foreach ($matches[3] as $headline)
		{
			$istemplate = 0;
			$templatetitle = '';
			$templatesection = 0;
			$numbering = '';
			$mat = array();

			if ($toclevel) 
			{
				$prevlevel = $level;
				$prevtoclevel = $toclevel;
			}
			$level = $matches[1][$headlineCount];

			if ($doNumberHeadings || $doTocNumberHeadings || $enoughToc) 
			{
				if ($level > $prevlevel) 
				{
					// Increase TOC level
					$toclevel++;
					$sublevelCount[$toclevel] = 0;
					if ($toclevel < $maxTocLevel) 
					{
						$prevtoclevel = $toclevel;
						$toc .= $this->_tocIndent();
						$numVisible++;
					}
				} 
				elseif ($level < $prevlevel && $toclevel > 1) 
				{
					// Decrease TOC level, find level to jump to

					if ($toclevel == 2 && $level <= $levelCount[1]) 
					{
						// Can only go down to level 1
						$toclevel = 1;
					} 
					else 
					{
						for ($i = $toclevel; $i > 0; $i--)
						{
							if ($levelCount[$i] == $level) 
							{
								// Found last matching level
								$toclevel = $i;
								break;
							} 
							elseif ($levelCount[$i] < $level) 
							{
								// Found first matching level below current level
								$toclevel = $i + 1;
								break;
							}
						}
					}
					if ($toclevel < $maxTocLevel) 
					{
						if ($prevtoclevel < $maxTocLevel) 
						{
							// Unindent only if the previous toc level was shown
							$toc .= $this->_tocUnindent($prevtoclevel - $toclevel);
						} 
						else 
						{
							$toc .= $this->_tocLineEnd();
						}
					}
				} 
				else 
				{
					// No change in level, end TOC line
					if ($toclevel < $maxTocLevel) 
					{
						$toc .= $this->_tocLineEnd();
					}
				}

				$levelCount[$toclevel] = $level;

				// count number of headlines for each level
				@$sublevelCount[$toclevel]++;
				$dot = 0;
				for ($i = 1; $i <= $toclevel; $i++)
				{
					if (!empty($sublevelCount[$i])) 
					{
						if ($dot) 
						{
							$numbering .= '.';
						}
						$numbering .= $sublevelCount[$i];
						$dot = 1;
					}
				}
			}

			// The canonized header is a version of the header text safe to use for links
			// Avoid insertion of weird stuff like <math> by expanding the relevant sections
			$canonized_headline = $this->strip($headline);

			// Strip out HTML (other than plain <sup> and <sub>: bug 8393)
			$_tocLine = preg_replace(
				array('#<(?!/?(sup|sub)).*?'.'>#', '#<(/?(sup|sub)).*?'.'>#'),
				array('',                          '<$1>'),
				$canonized_headline
			);
			$_tocLine = trim($_tocLine);

			// For the anchor, strip out HTML-y stuff period
			$canonized_headline = preg_replace('/<.*?'.'>/', '', $canonized_headline);
			$canonized_headline = trim($canonized_headline);

			// Save headline for section edit hint before it's escaped
			$headline_hint = $canonized_headline;

			$canonized_headline = Sanitizer::escapeId($canonized_headline);
			$refers[$headlineCount] = $canonized_headline;

			// count how many in assoc. array so we can track dupes in anchors
			isset($refers[$canonized_headline]) ? $refers[$canonized_headline]++ : $refers[$canonized_headline] = 1;
			$refcount[$headlineCount] = $refers[$canonized_headline];

			// Don't number the heading if it is the only one (looks silly)
			if ($doNumberHeadings && count($matches[3]) > 1) 
			{
				// the two are different if the line contains a link
				$headline = $numbering . ' ' . $headline;
			}

			// Create the anchor for linking from the TOC to the section
			$anchor = $canonized_headline;
			if ($refcount[$headlineCount] > 1) 
			{
				$anchor .= '_' . $refcount[$headlineCount];
			}
			if ($enoughToc && (!isset($maxTocLevel) || $toclevel<$maxTocLevel)) 
			{
				if (!$doTocNumberHeadings) 
				{
					$numbering = '';
				}
				$toc .= $this->_tocLine($anchor, $_tocLine, $numbering, $toclevel);
			}
			// Give headline the correct <h#> tag
			/*if ($showEditLink && (!$istemplate || $templatetitle !== '')) 
			{
				$editlink = $this->editSectionLink($this->mTitle, $sectionCount+1, $headline_hint);
			} 
			else 
			{*/
				$editlink = '';
			//}
			$head[$headlineCount] = $this->_makeHeadline($level, $matches['attrib'][$headlineCount], $anchor, $headline, $editlink);

			$headlineCount++;
			if (!$istemplate)
			{
				$sectionCount++;
			}
		}

		$toc .= ($toc) ? $this->_tocUnindent($toclevel - 1) . '</ul>' : '';

		// Never ever show TOC if no headers
		if ($numVisible < 1) 
		{
			$enoughToc = false;
		}

		// split up and insert constructed headlines
		$blocks = preg_split('/<H[1-6].*?' . '>.*?<\/H[1-6]>/i', $text);
		$i = 0;

		foreach ($blocks as $block)
		{
			$full .= $block;
			if ($enoughToc && !$i && $isMain && !$forceTocPosition) 
			{
				// Top anchor now in skin
				$full = $full . $toc;
			}

			if (!empty($head[$i])) 
			{
				$full .= $head[$i];
			}
			$i++;
		}

		$output  = '<div class="article-toc">' . "\n";
		$output .= '<h3 class="article-toc-heading">Contents</h3>' . "\n";
		$output .= $toc . "\n";
		$output .= '</div>' . "\n";

		return str_replace('<p>MACRO' . $this->token() . '[[TableOfContents]]' . "\n" . '</p>', $output, $full);
	}

	/**
	 * Generate an HTML header with an anchor for the table of contents to jump to
	 * 
	 * @param      string $level   TOC level
	 * @param      string $attribs Header attributes
	 * @param      string $anchor  Link anchor name #anchor
	 * @param      string $text    Header text
	 * @param      string $link    Link
	 * @return     string 
	 */
	private function _makeHeadline($level, $attribs, $anchor, $text, $link)
	{
		return '<h' . $level . $attribs . '<a name="' . $anchor . '"></a><span class="tp-headline">' . $text . '</span> ' . $link . '</h' . $level . '>';
	}

	/**
	 * Start a sub-list
	 * 
	 * @return     string
	 */
	private function _tocIndent()
	{
		return "\n<ul>";
	}

	/**
	 * Close a sub-list
	 * 
	 * @param      integer $level Nested list depth
	 * @return     string
	 */
	private function _tocUnindent($level)
	{
		return "</li>\n" . str_repeat("</ul>\n</li>\n", $level > 0 ? $level : 0);
	}

	/**
	 * Open a list item and generate link
	 * 
	 * @param      string $anchor    Link anchor #anchor
	 * @param      string $tocLine   TOC item Text
	 * @param      string $tocnumber TOC item number
	 * @param      string $level     TOC level
	 * @return     string 
	 */
	private function _tocLine($anchor, $tocLine, $tocnumber, $level)
	{
		return "\n" . '<li class="toclevel-' . $level . '">' .
						'<a href="' . JRoute::_('index.php?option=' . $this->get('option') . '&scope=' . $this->get('scope') . '&pagename=' . $this->get('pagename')) . '#' . $anchor . '">' .
							'<span class="tocnumber">' . $tocnumber . ' </span>' .
							'<span class="toctext">' . $tocLine . '</span>' .
						'</a>';
	}

	/**
	 * Close a list item
	 * 
	 * @return     string 
	 */
	private function _tocLineEnd()
	{
		return "</li>\n";
 	}
}

