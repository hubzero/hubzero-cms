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

/**
 * Wiki parser class
 * converts wiki syntax to HTML
 * 
 * Code was heavily influenced by MediaWiki's Parser, Trac's parser, and Textile.
 */
class WikiParser
{
	/**
	 * Perform a full parse
	 * False = limited macro usage, etc.
	 * 
	 * @var string
	 */
	var $fullparse = true;

	/**
	 * Description for 'mUniqPrefix'
	 * 
	 * @var string
	 */
	var $mUniqPrefix = NULL;

	/**
	 * Description for 'mDTopen'
	 * 
	 * @var boolean
	 */
	var $mDTopen;

	/**
	 * Description for 'mLastSection'
	 * 
	 * @var string
	 */
	var $mLastSection;

	/**
	 * Description for 'mInPre'
	 * 
	 * @var boolean
	 */
	var $mInPre;

	/**
	 * Description for 'mLinkHolders'
	 * 
	 * @var unknown
	 */
	var $mLinkHolders;

	/**
	 * Description for 'shelf'
	 * 
	 * @var array
	 */
	var $shelf = array();

	/**
	 * Description for 'hlgn'
	 * 
	 * @var string
	 */
	var $hlgn, $vlgn, $lnge, $clas, $styl, $cspn, $rspn, $a, $s, $c;

	/**
	 * Current page ID
	 * 
	 * @var integer
	 */
	var $pageid;

	/**
	 * Current component name
	 * 
	 * @var string
	 */
	var $option;

	/**
	 * Page scope
	 * 
	 * @var string
	 */
	var $scope;

	/**
	 * Page name
	 * 
	 * @var string
	 */
	var $pagename;

	/**
	 * Temp container for PREs
	 * 
	 * @var array
	 */
	var $pres;

	/**
	 * Constructor
	 * 
	 * @param      string  $option   Parameter description (if any) ...
	 * @param      string  $scope    Parameter description (if any) ...
	 * @param      string  $pagename Parameter description (if any) ...
	 * @param      integer $pageid   Parameter description (if any) ...
	 * @param      string  $filepath Parameter description (if any) ...
	 * @param      string  $domain   Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct($option='', $scope='', $pagename='', $pageid=0, $filepath='', $domain=null)
	{
		// We need this info for links that may get generated
		$this->option   = $option;
		$this->scope    = $scope;
		$this->pagename = $pagename;
		$this->pageid   = $pageid;
		$this->filepath = $filepath;
		$this->domain   = $domain;

		$this->mUniqPrefix = "\x07UNIQ" . $this->getRandomString();

		// Patterns for glyphs and attribute parsing
		$this->hlgn = "(?:\<(?!>)|(?<!<)\>|\<\>|\=|[()]+(?!))";
		$this->vlgn = "[\-^~]";
		$this->clas = "(?:\([^)]+\))";
		$this->lnge = "(?:\[[^]]+\])";
		$this->styl = "(?:\{[^}]+\})";
		$this->cspn = "(?:\\\\\d+)";
		$this->rspn = "(?:\/\d+)";
		$this->a    = "(?:{$this->hlgn}|{$this->vlgn})*";
		$this->s    = "(?:{$this->cspn}|{$this->rspn})*";
		//$this->c   = "(?:{$this->clas}|{$this->styl}|{$this->lnge}|{$this->hlgn})*";
		//$this->c   = "(?:{$this->clas}|{$this->styl}|{$this->lnge})*";
		$this->c    = "(?:{$this->clas}|{$this->styl})*";
	}

	/**
	 * Get the unique prefix
	 * 
	 * @return     string
	 */
	public function uniqPrefix()
	{
		return $this->mUniqPrefix;
	}

	/**
	 * Generate a unique prefix
	 * 
	 * @return     integer 
	 */
	public function getRandomString()
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
		$this->fullparse = $fullparse;

		if (!$this->fullparse) 
		{
			$camelcase = 0;
		}

		$text = trim($text);
		$text = "\n" . $text;

		// Clean out any carriage returns.
		// These can screw up some block parsing, such as tables
		$text = str_replace("\r", '', $text);

		// Strip out <pre> code
		// We'll put this back after other processes
		$text = $this->strip($text);

		// Process includes
		// Includes are essentially smaller other pages
		//if ($this->fullparse) 
		//{
			$text = $this->includes($text);
		//}

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
		//$text = preg_replace("/(\<|\>\.)|(\<\>)/i", '', $text);
		/*$text = preg_replace(
			array('/<\./', '/<>/', '/>\./', '/<([^>]+?)</', '/>([^<]+?)>/', '/([0-9]+)<([0-9]+)/', '/<([0-9]+)/', '/>([0-9]+)/', '/([0-9]+)</', '/([0-9]+)>/'), 
			array('&lt;.', '&lt;&gt;', '&gt;.', '&lt;$1&lt;',  '&rt;$1&rt;',  '$1 &lt; $2',          '&lt; $1',     '&gt; $1',     '$1 &lt;',     '$1 &gt;'), 
			$text
		);*/
		$text = preg_replace(
			array('/<\./', '/([0-9]+)<([0-9]+)/', '/<([0-9]+)/', '/>([0-9]+)/', '/([0-9]+)</', '/([0-9]+)>/'), 
			array('&lt;.', '$1 &lt; $2',          '&lt; $1',     '&gt; $1',     '$1 &lt;',     '$1 &gt;'), 
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

		if ($this->fullparse) 
		{
			// Do horizontal rules <hr />
			$text = preg_replace('/(^|\n)-----*/', '\\1<hr />', $text);

			// Do headings <h1>, <h2>, etc.
			$text = $this->headings($text);
		}

		// Process macros
		$text = $this->macros($text);

		// Do quotes. '''stuff''' => <strong>stuff</strong>
		$text = $this->quotes($text);

		// Do spans
		$text = $this->spans($text);

		// Do glyphs
		$text = $this->glyphs($text);

		// Do links
		$text = $this->links($text, $camelcase);

		// Clean up special characters, only run once, next-to-last before block levels
		$fixtags = array(
			// french spaces, last one Guillemet-left
			// only if there is something before the space
			'/(.) (?=\\?|:|;|!|%|\\302\\273)/' => '\\1&nbsp;\\2',
			// french spaces, Guillemet-right
			'/(\\302\\253) /' => '\\1&nbsp;',
		);
		$text = preg_replace(array_keys($fixtags), array_values($fixtags), $text);

		if ($this->fullparse) 
		{
			$text = $this->admonitions($text);
		}

		// Do definition lists
		$text = $this->doDFLists($text);

		// Unstrip macro blocks BEFORE doing block levels or <p> tags will get messy
		$text = preg_replace_callback('/MACRO' . $this->mUniqPrefix . '/i', array(&$this, 'restore_macros'), $text);

		// Only once and last
		$text = $this->doBlockLevels($text, $linestart);

		// Put back removed <math>
		$text = $this->aftermath($text);

		// Put back removed <pre> and <code>
		$text = $this->unstrip($text);

		// Strip out blank space
		$text = str_replace("<p><br />\n</p>", '', $text);
		$text = preg_replace('|<p>\s*?</p>|', '', $text);
		$text = preg_replace('/<p>\p{Z}*<\/p>/u', '', $text);
		$text = preg_replace('!<p>\s*(</?(?:table|tr|td|th|div|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)!', "$1", $text);
		$text = preg_replace('!(</?(?:table|tr|td|th|div|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)\s*</p>!', "$1", $text);

		// Format headings and build a table of contents
		if ($this->fullparse && strstr($text, '<p>MACRO' . $this->uniqPrefix() . '[[TableOfContents]]' . "\n" . '</p>')) 
		{
			//$output = $this->toc($text);
			//$text = $output['text'];
			$text = $this->toc($text);
		}

		return $text;
	}

	/**
	 * Convert wiki links to HTML links
	 * 
	 * @param      string  $text      Wiki markup
	 * @param      integer $camelcase Convert camcel-cased text? 1=yes, 0=no
	 * @return     string
	 */
	public function links($text, $camelcase=1)
	{
		$this->reference_wiki = '';

		if (!class_exists('WikiPage') && is_file(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php')) 
		{
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php');
		}

		// Parse for link syntax 
		// e.g. [mylink My Link] => <a href="mylink">My Link</a>
		$char_regexes = array(
			'internal' => '('.
				'\['. // opening brackets
					'(([^\]]*?)\:)?'. // namespace (if any)
					'([^\]\[]*?)'.
					'(\s+[^\]]*?)?'.
				'\]'. // closing brackets
			')',
			'external' => '('.
				'\['.
					'([^\]\[]*?)'.
					'(\s+[^\]]*?)?'.
				'\]'.
			')'
		);
		$this->links = array();
		$this->linkscount = 0;
		foreach ($char_regexes as $func => $regex)
		{
			$this->stop = false;
			$text = preg_replace_callback("/$regex/i", array(&$this, 'link' . ucfirst($func)), $text);
			if ($this->stop) 
			{
				break;
			}
		}

		// Auto link http:, etc.
		$this->alinks = array();
		$UrlPtrn  = "[^=\"\'](https?:|mailto:|ftp:|gopher:|news:|file:)" . "([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_])";
		$text = preg_replace_callback("/$UrlPtrn/", array(&$this, 'linkAuto'), $text);
		$text = preg_replace_callback("/([\s]*)[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", array(&$this, 'linkAuto'), $text);

		// Camelcase links (e.g. MyLink) 
		if ($camelcase) 
		{
			$UpperPtn = "[A-Z]"; //"[A-Z\xc0-\xde]";
			$LowerPtn = "[a-z]"; //"[a-z\xdf-\xfe]";
			$AlphaPtn = "[A-Za-z]"; //"[A-Za-z\xc0-\xfe]";

			$LinkPtn  = $UpperPtn . $AlphaPtn . '*' . $LowerPtn . '+' . $UpperPtn . $AlphaPtn . '*(?:(?:\\/' . $UpperPtn . $AlphaPtn . '*)+)?';

			$ptn = "/(^|[^A-Za-z])(!?\\/?$LinkPtn)((\#[A-Za-z]([-A-Za-z0-9_:.]*[-A-Za-z0-9_])?)?)(\"\")?/e";
			$text = preg_replace($ptn, "WikiParser::q1('\\1').WikiParser::linkWikiName(WikiParser::q1('\\2'),'\\3')", $text, -1);
		}

		// Replace our spot holders with the links
		// This is done to avoid accidental links within links generation
		// e.g. http://w3.org/MarkUp => <a href="http://w3.org/<a href="MarkUp">MarkUp</a>">http://w3.org/<a href="MarkUp">MarkUp</a></a>
		$text = preg_replace_callback('/<link><\/link>/i', array(&$this, 'linkRestore'), $text);
		$text = preg_replace_callback('/<alink><\/alink>/i', array(&$this, 'linkAutoRestore'), $text);
		return $text;
	}

	/**
	 * Automatically links any strings matching a URL pattern
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
		$href = $matches[0];
		$sp = preg_replace('/^([\s]*)(.*)/i', "$1", $href);
		$pc = '';

		$href = trim($href);
		if (substr($href, -1) == '.') 
		{
			$href = rtrim($href, '.');
			$pc = substr($href, -1);
		}

		if (substr($href, 0, 1) == '!') 
		{
			return $sp . ltrim($href, '!') . $pc;
		}

		$href = str_replace('"', '', $href);
		$href = str_replace("'", '', $href);
		$href = str_replace('&#8221', '', $href);

		if (substr($href, 0, strlen('mailto:')) == 'mailto:') 
		{
			$href = 'mailto:' . $this->obfuscate(substr($href, strlen('mailto:')));
		}
		else if (preg_match("/^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $href))
		{
			
			$href = 'mailto:' . $this->obfuscate($href);
		}

		$h = array('h', 'm', 'f', 'g', 'n');
		$pfx = '';
		if (!in_array(substr($href, 0, 1), $h)) 
		{
			$pfx  = substr($href, 0, 1);
			$href = substr($href, 1);
		}

		$txt = $href;
		if (substr($href, 0, strlen('mailto:')) == 'mailto:') 
		{
			$txt = substr($href, strlen('mailto:'));
		}

		$l = $sp . sprintf(
			'<a class="ext-link" href="%s"%s>%s</a>',
			$href,
			' rel="external"',
			$txt
		) . $pc;
		array_push($this->alinks, $pfx . $l);
		return '<alink></alink>';
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
	 * Turn link placeholders into actual links
	 * (see linkAuto() method)
	 * 
	 * @param      array $matches Text matching autolink tag
	 * @return     string Placeholder tag
	 */
	public function linkAutoRestore($matches)
	{
		return array_shift($this->alinks);
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
		$nolink = false;

		$href  = $matches[4];
		$title = (isset($matches[5])) ? $matches[5] : $href;
		$namespace = $matches[2];

		$title = preg_replace('/\(.*?\)/', '', $title);
		$title = preg_replace('/^.*?\:/', '', $title);

		// Should this really be an external link?
		$UrlPtn  = "(?:https?:|mailto:|ftp:|gopher:|news:|file:)" .
		           "(?:[^ |\\/\"\']*\\/)*[^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_]";

		if ((preg_match("/$UrlPtn/", $matches[2] . $href) && strpos($matches[2] . $href, '/') !== false)
		 || substr($matches[0], 1, 1) == '/') 
		{
			$regex = '(' . '\[' . '([^\]\[]*?)' . '(\s+[^\]]*?)?' . '\]' . ')';
			return preg_replace_callback("/$regex/i", array(&$this, 'linkExternal'), $matches[0]);
		}

		// Are we placing an anchor?
		if (substr($href, 0, 2) == '=#')
		{
			$l = '<a name="' . ltrim($href, '=#') . '"></a>';
			array_push($this->links, $l);
			return '<link></link>';
		}
		// Are we jumping to an anchor?
		else if (substr($href, 0, 1) == '#')
		{
			$l = '<a class="wiki int-link" href="' . $href . '">' . $title . '</a>';
			array_push($this->links, $l);
			return '<link></link>';
		}

		$cls = 'wiki';

		$bits = explode('/', $href);

		if ($namespace == 'wiki:' && substr($href, 0, strlen('&#8220;')) == '&#8220;')
		{
			$title = substr($matches[1], strlen('[wiki:&#8220;'));
			$title = substr($title, 0, -strlen('&#8221;]'));
			$href = '#';

			$p = WikiPage::getInstance($title, $this->scope);
			if ($p->id) 
			{
				$href = $p->pagename;
			}
		}
		else 
		{
			if (count($bits) > 1) 
			{
				$pagename = array_pop($bits);
				$scope = implode('/', $bits);
			} 
			else 
			{
				$pagename = end($bits);
				$scope = $this->scope;
			}

			$pagename = ucfirst(strtolower($namespace)) . $pagename;
			$p = WikiPage::getInstance($pagename, $scope);
		}

		if (!$p->id) 
		{
			if (in_array(trim(strtolower($namespace)), array('special:', 'help:', 'image:', 'file:')))
			{
				$cls .= '';
			}
			else
			{
				$cls .= (substr($href, 0, 1) != '?') ? ' missing' : '';
			}
			$p->scope = ($p->scope) ? $p->scope : $this->scope;
		} 
		else 
		{
			$cls .= ' int-link';
		}

		$href = JRoute::_('index.php?option=' . $this->option . '&scope=' . $p->scope . '&pagename=' . $p->pagename);

		$l = '<a class="' . $cls . '" href="' . $href . '">' . trim($title) . '</a>';
		array_push($this->links, $l);
		return '<link></link>';
	}

	/**
	 * Turn link placeholders into actual links
	 * (see linkExternal() and linkInternal() methods)
	 * 
	 * @param      array $matches Text matching internal link placeholder pattern
	 * @return     string HTML
	 */
	public function linkRestore($matches)
	{
		return array_shift($this->links);
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
		$href = $matches[2];
		$title = (isset($matches[3])) ? $matches[3] : '';
		/*if (!$title) 
		{
			$this->linknumber++;
			$title = "[{$this->linknumber}]";
		}*/
		$newwindow = false;

		$cls = 'int-link';

		$UrlPtn  = "(?:https?:|mailto:|ftp:|gopher:|news:|file:)" .
		           "(?:[^ |\\/\"\']*\\/)*[^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_]";

		if (!preg_match("/$UrlPtn/", $href) && strpos($href, '/') === false) 
		{
			$href = JRoute::_('index.php?option=' . $this->option . '&scope=' . $this->scope . '&pagename=' . $href);
			$cls  = '';
		}
		$UrlPtn  = "(?:https?:|mailto:|ftp:|gopher:|news:|file:)";
		if (preg_match("/$UrlPtn/", $href)) 
		{
			$cls = 'ext-link';
		}
		if (!$title) 
		{
			$title = $href;
		}

		$l = sprintf(
			'<a class="' . $cls . '" href="%s"%s>%s</a>',
			$href,
			($newwindow ? ' rel="external"' : ''),
			trim($title)
		);
		array_push($this->links, $l);
		return '<link></link>';
	}

	/**
	 * Short description for 'q1'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $text Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function q1($text)
	{
		return str_replace('\\"', '"', $text);
	}

	/**
	 * Generate a link to a wiki page based on page name
	 * 
	 * @param      string $name   Page name
	 * @param      string $anchor Anchor
	 * @return     string
	 */
	public function linkWikiName($name, $anchor)
	{
		// Trim leading '!'.
		if ($name[0] == '!') 
		{
			return ltrim($name, '!');
		}
		//$database =& JFactory::getDBO();
		$cls = 'wiki';
		$append = '';

		
		//$p->pagename = $name;

		$bits = explode('/', $name);
		if (count($bits) > 1) 
		{
			$pagename = array_pop($bits);
			$scope = implode('/', $bits);
		} 
		else 
		{
			$pagename = end($bits);
			$scope = $this->scope;
		}
		$p = WikiPage::getInstance($pagename, $scope);

		//$p->getID();
		if ((!is_object($p) || !$p->id) && substr($name, 0, 1) != '?') 
		{
			$cls .= ' missing';
		}

		$link = JRoute::_('index.php?option=' . $this->option . '&scope=' . $scope . '&pagename=' . $pagename);

		return '<a href="' . $link . '" class="' . $cls . '">' . $name . $append . '</a>';
	}

	/**
	 * Strip <pre> and <code> blocks from text
	 * 
	 * @param      string $text Wiki markup
	 * @return     string 
	 */
	private function strip($text)
	{
		$this->pres = array();
		$this->codes = array();
		$this->counter = 0;

		$output = '';

		$bits = explode("\n", $text);
		foreach ($bits as $line)
		{
			$this->prefixLength = 0;
			$this->prefixLength = strspn($line, ' ');

			$line = $this->doSpecial($line, '`{{{', '}}}`', 'fPCode');
			$line = $this->doSpecial($line, '{{{`', '`}}}', 'fCCode');

			$line = preg_replace_callback('/\{\{\{([\s]*)/i', array(&$this, 'handle_pre_up'), $line);
			$line = preg_replace_callback('/([\s]*)\}\}\}/i', array(&$this, 'handle_pre_down'), $line);
			$output .= $line . "\n";
			$output = preg_replace('/\{\{\{1(.+)1\}\}\}/i', "{{{\\1}}}", $output);
			if ($this->counter == 0) 
			{
				$output = preg_replace_callback('/\{\{\{1([\s\S]*)1\}\}\}/i', array(&$this, 'handle_save_pre'), $output);
				//$output = $this->doSpecial($output, '`{{{', '}}}`', 'fPCode');
				//$output = $this->doSpecial($output, '{{{`', '`}}}', 'fCCode');
				$output = preg_replace_callback('/\{\{\{(.+?)\}\}\}/i', array(&$this, 'handle_save_code'), $output);
				$output = $this->doSpecial($output, '`', '`', 'fCode');
			}
		}

		$output = str_replace('<code><code>', '<code>', $output);
		$output = str_replace('</code></code>', '</code>', $output);

		return $output;
	}

	/**
	 * Store an item in the shelf
	 * Returns a unique ID as a placeholder for content retrieval later on
	 * 
	 * @param      string $val Content to store
	 * @return     integer Unique ID
	 */
	private function shelve($val)
	{
		$i = uniqid(rand());
		$this->shelf[$i] = $val;
		return $i;
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
		$this->_wikitohtml = $html;

		if (is_array($this->shelf))
		{
			do {
				$old = $text;
				$text = strtr($text, $this->shelf);
			} while ($text != $old);
		}

		$text = preg_replace_callback('/<pre><\/pre>/i', array(&$this, 'handle_restore_pre'), $text);
		$text = preg_replace_callback('/<code><\/code>/i', array(&$this, 'handle_restore_code'), $text);
		//$text = preg_replace_callback('/MACRO'.$this->mUniqPrefix.'/i',array(&$this,"restore_macros"),$text);

		$text = str_replace('<code><code>', '<code>', $text);
		$text = str_replace('</code></code>', '</code>', $text);

		return $text;
	}

	/**
	 * Adds a count to first level PRE blocks 
	 * Enables handling of nested blocks by appending a first level indicator
	 * {{{1
	 *    {{{
	 *       ...
	 *    }}}
	 * 1}}}
	 * 
	 * @param      array $matches Strings that matched the strating pre block syntax
	 * @return     string
	 */
	private function handle_pre_up($matches)
	{
		$this->counter++;
		if ($this->counter == 1) 
		{
			return '{{{' . $this->counter . $matches[1];
		} 
		else 
		{
			return '{{{' . $matches[1];
		}
	}

	/**
	 * Counter for closing pre blocks
	 * This helps us find nested pre blocks by prepending a first level indicator
	 * {{{1
	 *    {{{
	 *       ...
	 *    }}}
	 * 1}}}
	 * 
	 * @param      array $matches Strings that matched the closing pre block syntax
	 * @return     string
	 */
	private function handle_pre_down($matches)
	{
		if ($this->counter == 1) 
		{
			$html = $matches[1] . $this->counter . '}}}';
			$this->counter--;
		} 
		else 
		{
			$html = $matches[1] . '}}}';
			$this->counter--;
		}
		return $html;
	}

	/**
	 * Pushes pre blocks to an internal array and replaces the content
	 * with empty <pre> tags. This is to ensure the content isn't parsed
	 * any further.
	 * 
	 * @param      array $matches String matching pre syntax
	 * @return     string
	 */
	private function handle_save_pre($matches)
	{
		$t = trim($matches[1]);
		$t = str_replace("\n", '', $t);
		if (substr($t, 0, 6) == '#!wiki') 
		{
			return '{admonition}' . $matches[1] . '{/admonition}';
		} 
		else if (substr($t, 0, strlen('#!comment')) == '#!comment') 
		{
			return ''; //'<!-- ' . $matches[1] . ' -->';
		} 
		else 
		{
			$val = explode("\n", $matches[1]);
			foreach ($val as $k => $v)
			{
				$prfx = substr($v, 0, $this->prefixLength);
				if (substr_count($prfx, ' ') == $this->prefixLength)
				{
					$val[$k] = substr($v, $this->prefixLength);
				}
			}
			$val = implode("\n", $val);
			array_push($this->pres, $val);
			return '<pre></pre>';
		}
	}

	/**
	 * Pushes code blocks to an internal array and replaces the content
	 * with empty <code> tags. This is to ensure the content isn't parsed
	 * any further.
	 * 
	 * @param      array $matches String matching code syntax
	 * @return     string 
	 */
	private function handle_save_code($matches)
	{
		array_push($this->codes, $matches[1]);
		return '<code></code>';
	}

	/**
	 * Restores <pre></pre> blocks to their actual content
	 * 
	 * @param      array $matches Parameter description (if any) ...
	 * @return     string
	 */
	private function handle_restore_pre($matches)
	{
		$txt = array_shift($this->pres);

		if (!$this->_wikitohtml) 
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
			switch ($t)
			{
				case 'html':
					if ($this->fullparse)
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
					return '<!-- ' . $this->encode_html($txt) . ' -->';
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
					$txt = preg_replace("/(\#\!$t\s*)/i", '', $txt);
					$txt = trim($txt, "\n\r\t");
					return '<pre>' . $this->encode_html($txt) . '</pre>';
				break;
			}
		}

		return '<pre>' . $this->encode_html($txt) . '</pre>';
	}

	/**
	 * Restores <code></code> blocks to their actual content
	 * 
	 * @param      unknown $matches Parameter description (if any) ...
	 * @return     string 
	 */
	private function handle_restore_code($matches)
	{
		$txt = array_shift($this->codes);

		if (!$this->_wikitohtml) 
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
		return '<code>' . $this->encode_html($txt) . '</code>';
	}

	/**
	 * Short description for 'doSpecial'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $text Parameter description (if any) ...
	 * @param      unknown $start Parameter description (if any) ...
	 * @param      unknown $end Parameter description (if any) ...
	 * @param      string $method Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	private function doSpecial($text, $start, $end, $method='fSpecial')
	{
	  return preg_replace_callback('/(^|\s|[[({>"])' . preg_quote($start, '/') . '(.*?)' . preg_quote($end, '/') . '(\s|$|["\])}])?/ms', array(&$this, $method), $text);
	}

	/**
	 * Short description for 'fSpecial'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $m Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function fSpecial($m)
	{
		// A special block like notextile or code
		@list(, $before, $text, $after) = $m;
		return $before . $this->shelve($this->encode_html($text)) . $after;
	}

	/**
	 * `{{{...}}}`
	 * 
	 * @param      unknown $m Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function fPCode($m)
	{
		@list(, $before, $text, $after) = $m;
		array_push($this->codes,'{{{' . $text . '}}}');
		return $before . '<code></code>' . $after;
	}

	/**
	 * {{{`...`}}}
	 * 
	 * @param      unknown $m Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function fCCode($m)
	{
		@list(, $before, $text, $after) = $m;
		array_push($this->codes, '`' . $text . '`');
		return $before . '<code></code>' . $after;
	}

	/**
	 * `...`
	 * 
	 * @param      unknown $m Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function fCode($m)
	{
		@list(, $before, $text, $after) = $m;
		array_push($this->codes, $text);
		return $before . '<code></code>' . $after;
	}

	/**
	 * {{{...}}}
	 * 
	 * @param      unknown $m Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function fPre($m)
	{
		@list(, $before, $text, $after) = $m;
		return $before . '<pre>' . $this->shelve($this->encode_html($text)) . '</pre>' . $after;
	}

	/**
	 * Short description for 'encode_html'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $str Parameter description (if any) ...
	 * @param      integer $quotes Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	private function encode_html($str, $quotes=1)
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
	 * Short description for 'cleanXss'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $string Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
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
	 * @param      unknown $text Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	private function admonitions($text)
	{
		return preg_replace_callback('/\{admonition\}(.*?)\{\/admonition\}/s', array(&$this, 'admonitionCallback'), $text);
	}

	/**
	 * Short description for 'admonitionCallback'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $matches Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function admonitionCallback($matches)
	{
		$txt = $matches[1];
		$bits = explode("\n", $txt);
		$cls = array_shift($bits);
		if (strstr($cls, '#!wiki')) 
		{
			$cls = str_replace('#!wiki', '', $cls);
			$cls = trim($cls);
		}
		$txt = implode("\n",$bits);

		return '<div class="admon-' . $cls . '">' . "\n" . $txt . '</div>';
	}

	/**
	 * Short description for 'math'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $text Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
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

		$this->maths = array();

		return preg_replace_callback('/<math>(.*?)<\/math>/s', array(&$this, 'mathCallback'), $text);
	}

	/**
	 * Short description for 'aftermath'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $text Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	private function aftermath($text)
	{
		return preg_replace_callback('/<math><\/math>/i', array(&$this, 'restore_math'), $text);
	}

	/**
	 * Short description for 'restore_math'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $matches Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function restore_math($matches)
	{
		return '<span class="asciimath">' . array_shift($this->maths) . '</span>';
	}

	/**
	 * Short description for 'mathCallback'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $mtch_arr Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	private function mathCallback($mtch_arr)
	{
		$txt = trim($mtch_arr[1]);

		/*$database =& JFactory::getDBO();
		$tables = $database->getTableList();

		$table = $database->_table_prefix . 'wiki_math';
		if (!in_array($table, $tables)) 
		{
			$database->setQuery("CREATE TABLE `#__wiki_math` (
			  `inputhash` varbinary(16) NOT NULL,
			  `outputhash` varbinary(16) NOT NULL,
			  `conservativeness` tinyint(4) NOT NULL,
			  `html` text,
			  `mathml` text,
			  `id` int(11) NOT NULL auto_increment,
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `inputhash` (`inputhash`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1;");
			if (!$database->query()) 
			{
				return '<!-- '  . $database->getErrorMsg() . ' -->';
			}
		}*/

		$m = MathRenderer::renderMath($txt, array('option' => $this->option));

		array_push($this->maths, $m);
		return '<math></math>';
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
		//$pattern = '/\[\[(?P<includename>[\w]+)(\]\]|\((?P<includeargs>.*)\)\]\])/U';
		$pattern = '/\[\[(include)(\]\]|\((.*)\)\]\])/Ui';
		$text = preg_replace_callback($pattern, array(&$this, 'getInclude'), $text);

		return $text;
	}

	/**
	 * Retrieve an included page
	 * This is recursive and should look for inclusions in any included page.
	 * 
	 * @param      array $matches Pattern matches from includes() method
	 * @return     string
	 */
	private function getInclude($matches)
	{
		if (isset($matches[1]) && $matches[1] != '') 
		{
			if (strtolower($matches[1]) != 'include') 
			{
				return $matches[0];
			}
			if (!$this->fullparse)
			{
				return "'''Includes not allowed.'''";
			}

			$scope = ($this->domain) ? $this->domain . DS . 'wiki' : $this->scope;
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
			if ($pagename == $this->pagename && $scope == $this->scope)
			{
				return '';
			}

			$p = WikiPage::getInstance($pagename, $scope);

			if ($p->id) 
			{
				$revision = $p->getCurrentRevision();

				return $this->includes($revision->pagetext);
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
		$pattern = '/\[\[(?P<macroname>[\w]+)(\]\]|\((?P<macroargs>.*)\)\]\])/U';
		$text = preg_replace_callback($pattern, array(&$this, 'getMacro'), $text);

		return $text;
	}

	/**
	 * Attempt to load a specific macro class and return its contents
	 * 
	 * @param      array $matches Parameter description (if any) ...
	 * @return     string
	 */
	private function getMacro($matches)
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

			if (!$this->domain && strtolower(substr($macroname, 0, 5)) == 'group') 
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

					if (!$this->fullparse && !$macro->allowPartial)
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
			$macro->option     = $this->option;
			$macro->scope      = $this->scope;
			$macro->pagename   = $this->pagename;
			$macro->domain     = $this->domain;
			$macro->uniqPrefix = $this->uniqPrefix();
			if ($this->pageid > 0) 
			{
				$macro->pageid = $this->pageid;
			} 
			else 
			{
				$macro->pageid = JRequest::getInt('lid', 0, 'post');
			}
			$macro->filepath   = $this->filepath;

			// Push contents to a container -- we'll retrieve this later
			// This is done to prevent any further wiki parsing of contents macro may return
			array_push($this->macros, $macro->render());
			return 'MACRO' . $this->mUniqPrefix;
		}
	}

	/**
	 * Short description for 'restore_macros'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $matches Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	private function restore_macros($matches)
	{
		return array_shift($this->macros);
	}

	/**
	 * Short description for 'glyphs'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $text Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
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
				$line = $this->encode_html($line, 0);
				$line = preg_replace($glyph_search, $glyph_replace, $line);
			}
			$glyph_out[] = $line;
		}
		return join('', $glyph_out);
	}

	/**
	 * Parse Block Attributes
	 * 
	 * @param      unknown $in Parameter description (if any) ...
	 * @param      string $element Parameter description (if any) ...
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
				if (preg_match("/($this->vlgn)/", $matched, $vert))
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

			if (preg_match("/($this->hlgn)/", $matched, $horiz))
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
	 * @param      string $text Parameter description (if any) ...
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

		foreach ($qtags as $f) 
		{
			$text = preg_replace_callback("/
				(^|(?<=[\s>$pnct\(])|[{[])
				($f)(?!$f)
				({$this->c})
				(?::(\S+))?
				([^\s$f]+|\S.*?[^\s$f\n])
				([$pnct]*)
				$f
				($|[\]}]|(?=[[:punct:]]{1,2}|\s|\)))
			/x", array(&$this, 'doSpan'), $text);
		}
		$text = preg_replace('/\^(.*?)\^/', "<sup>\\1</sup>", $text);
		$text = preg_replace('/,,(.*?),,/', "<sub>\\1</sub>", $text);
		return $text;
	}

	/**
	 * Short description for 'fSpan'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $m Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function doSpan($m)
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

			$patterns = array("/^(.*){$h}(.+){$h}\s\#(.*)\\s*$/m","/^(.*){$h}(.+){$h}\\s*$/m");
			$replace  = array("\\1<h{$i} id=\"\\3\">\\2</h{$i}>\\4", "\\1<h{$i}>\\2</h{$i}>\\3");
			$text = preg_replace($patterns, $replace, $text);
		}
		return $text;
	}

	/**
	 * Quotes
	 * '' => <i>
	 * ''' => <b>
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
			$outtext .= $this->doQuotes($line) . "\n";
		}
		$outtext = substr($outtext, 0, -1);
		return $outtext;
	}

	/**
	 * Short description for 'doQuotes'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $text Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function doQuotes($text)
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
	 * @param      string $text Parameter description (if any) ...
	 * @return     string
	 */
	private function tables($text)
	{
		$text .= "\n\n";
		return preg_replace_callback("/^(?:table(_?{$this->s}{$this->a}{$this->c})\. ?\n)?^({$this->a}{$this->c}\.? ?\|\|.*\|\|)\n\n/smU", array(&$this, 'doTable'), $text);
	}

	/**
	 * Convert a string for wiki table syntax into a table
	 * 
	 * @param      array $matches Parameter description (if any) ...
	 * @return     string
	 */
	private function doTable($matches)
	{
		$tatts = $this->pba($matches[1], 'table');

		foreach (preg_split("/\|\|( *)$/m", $matches[2], -1, PREG_SPLIT_NO_EMPTY) as $row)
		{
			if (preg_match("/^($this->a$this->c\.)(.*)/m", ltrim($row), $rmtch)) 
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
				if (preg_match("/^(_?$this->s$this->a$this->c\.)(.*)/", $cell, $cmtch) || preg_match("/^(=?$this->s$this->a$this->c\.)(.*)=?/", $cell, $cmtch)) 
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
	private function closeParagraph()
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
	 * @param      string $st1 Parameter description (if any) ...
	 * @param      string $st2 Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	private function getCommon($st1, $st2)
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
	private function openList($char)
	{
		$result = $this->closeParagraph();

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
	private function nextItem($char)
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
	private function closeList($char)
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
	 * @param      unknown $text Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function doDFLists($text)
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
					$output .= preg_replace('/\s(.*?)::(\s*)/sU', "<dt>\\1</dt>\n", $oLine);
				} 
				else 
				{
					$output .= preg_replace('/\s(.*?)::(\s*)/sU', "<dl><dt>\\1</dt>\n", $oLine);
				}
				$indl = true;
				$indd = false;
			} 
			else 
			{
				if ($indl) 
				{
					if (preg_match('/\s{2,}(.*?)/sU', $oLine)) 
					{
						if (!$indd) 
						{
							$indd = true;
							$output .= '<dd>';
						}
						$output .= trim($oLine) . "\n";
					} 
					else 
					{
						$indd = false;
						if (!preg_match('/(.*?)::(\s*)/sU', $oLine)) 
						{
							$indl = false;
							$output .= '</dd></dl>' . "\n";
						}
					}
				} 
				else 
				{
					$output .= $oLine . "\n";
				}
			}
		}
		return $output;
	}

	/**
	 * Make lists from lines starting with 'some text::', '*', '#', etc.
	 * 
	 * @param      string  $text Parameter description (if any) ...
	 * @param      integer $linestart Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function doBlockLevels($text, $linestart)
	{
		// Parsing through the text line by line.  The main thing
		// happening here is handling of block-level elements p, pre,
		// and making lists from lines starting with * # : etc.
		$textLines = explode("\n", $text);

		$lastPrefix = $output = '';
		$lastPrefixLength = 0;
		$this->mDTopen = $inBlockElem = false;
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
				$output .= $this->nextItem($pref);
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
						$output .= $term . $this->nextItem(':');
					}
				}*/
			} 
			elseif ($prefixLength || $lastPrefixLength) 
			{
				$commonPrefixLength = $this->getCommon($prefixLength, $lastPrefixLength);
				$paragraphStack = false;

				while ($commonPrefixLength < $lastPrefixLength)
				{
					$lastPrefix = $openlist[$i--];
					$output .= $this->closeList($lastPrefix) . '<!-- common: ' . $commonPrefixLength . ', last: ' . $lastPrefixLength . ', prefx:' . $prefixLength . ', ' . $lastPrefix . ' -->';
					--$lastPrefixLength;
				}
				if ($prefixLength <= $commonPrefixLength && $commonPrefixLength > 0) 
				{
					$output .= $this->nextItem($pref);
				}

				$listOpened = false;
				while ($prefixLength > $commonPrefixLength)
				{
					$char = trim($pref);
					if (!$listOpened)
					{
						$output .= $this->openList($char);
						$listOpened = true;
					}
					//if (in_array($char, array('*', '#', ':', ';')) || is_numeric($char))
					//{
						$i++;
						$openlist[$i] = $char;
					//}
					if (';' == $char) 
					{
						// FIXME: This is dupe of code above
						if ($this->findColonNoLinks($t, $term, $t2) !== false) 
						{
							$t = $t2;
							$output .= $term . $this->nextItem(':');
						}
					}
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
					'<td|<th|<\\/?div|<hr|<\\/pre|<\\/p|' . $this->mUniqPrefix . '-pre|<\\/li|<\\/dl|<\\/ul|<\\/ol|<\\/?center)/iS', $t);
				if ($openmatch or $closematch) 
				{
					$paragraphStack = false;
					// TODO bug 5718: paragraph closed
					$output .= $this->closeParagraph();
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
							$output .= $this->closeParagraph() . '<pre>';
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
									$output .= $this->closeParagraph();
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
								$output .= $this->closeParagraph() . '<p>';
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
			$output .= $this->closeList($pref2);
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
	 * @param      boolean $isMain Parameter description (if any) ...
	 * @return     array 
	 */
	public function toc($text)
	{
		$isMain = true;
		$wgMaxTocLevel = 15;
		$showEditLink = true;
		$doNumberHeadings = false;
		$doTocNumberHeadings = true;
		$mForceTocPosition = true;
		$mShowToc = true;

		// Get all headlines for numbering them and adding funky stuff like [edit]
		// links - this is for later, but we need the number of headlines right now
		$matches = array();
		$numMatches = preg_match_all('/<H(?P<level>[1-6])(?P<attrib>.*?'.'>)(?P<header>.*?)<\/H[1-6] *>/i', $text, $matches);

		// If there are fewer than 4 headlines in the article, do not show TOC
		// unless it's been explicitly enabled.
		$enoughToc = $mShowToc && (($numMatches >= 4) || $mForceTocPosition);

		// Headline counter
		$headlineCount = 0;
		$sectionCount = 0; // headlineCount excluding template sections
		$numVisible = 0;

		// Ugh .. the TOC should have neat indentation levels which can be
		// passed to the skin functions. These are determined here
		$toc = '';
		$full = '';
		$head = array();
		$sublevelCount = array();
		$levelCount = array();
		$toclevel = 0;
		$level = 0;
		$prevlevel = 0;
		$toclevel = 0;
		$prevtoclevel = 0;

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
					if ($toclevel < $wgMaxTocLevel) 
					{
						$prevtoclevel = $toclevel;
						$toc .= $this->tocIndent();
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
					if ($toclevel < $wgMaxTocLevel) 
					{
						if ($prevtoclevel < $wgMaxTocLevel) 
						{
							// Unindent only if the previous toc level was shown
							$toc .= $this->tocUnindent($prevtoclevel - $toclevel);
						} 
						else 
						{
							$toc .= $this->tocLineEnd();
						}
					}
				} 
				else 
				{
					// No change in level, end TOC line
					if ($toclevel < $wgMaxTocLevel) 
					{
						$toc .= $this->tocLineEnd();
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
			$tocline = preg_replace(
				array('#<(?!/?(sup|sub)).*?'.'>#', '#<(/?(sup|sub)).*?'.'>#'),
				array('',                          '<$1>'),
				$canonized_headline
			);
			$tocline = trim($tocline);

			// For the anchor, strip out HTML-y stuff period
			$canonized_headline = preg_replace('/<.*?'.'>/', '', $canonized_headline);
			$canonized_headline = trim($canonized_headline);

			// Save headline for section edit hint before it's escaped
			$headline_hint = $canonized_headline;
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'sanitizer.php');
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
			if ($enoughToc && (!isset($wgMaxTocLevel) || $toclevel<$wgMaxTocLevel)) 
			{
				if (!$doTocNumberHeadings) 
				{
					$numbering = '';
				}
				$toc .= $this->tocLine($anchor, $tocline, $numbering, $toclevel);
			}
			// Give headline the correct <h#> tag
			if ($showEditLink && (!$istemplate || $templatetitle !== '')) 
			{
				$editlink = '';//$this->editSectionLink($this->mTitle, $sectionCount+1, $headline_hint);
			} 
			else 
			{
				$editlink = '';
			}
			$head[$headlineCount] = $this->makeHeadline($level, $matches['attrib'][$headlineCount], $anchor, $headline, $editlink);

			$headlineCount++;
			if (!$istemplate)
			{
				$sectionCount++;
			}
		}

		$toc .= ($toc) ? $this->tocUnindent($toclevel - 1) . '</ul>' : '';

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
			if ($enoughToc && !$i && $isMain && !$mForceTocPosition) 
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

		$tocc  = '<div class="article-toc">' . "\n";
		$tocc .= '<h3 class="article-toc-heading">Contents</h3>' . "\n";
		$tocc .= $toc . "\n";
		$tocc .= '</div>' . "\n";

		/*$full = str_replace('<p>MACRO' . $this->uniqPrefix() . '[[TableOfContents]]' . "\n" . '</p>', $tocc, $full);

		$bits = array();
		$bits['text'] = $full;
		$bits['toc']  = $toc;

		return $bits;*/
		return str_replace('<p>MACRO' . $this->uniqPrefix() . '[[TableOfContents]]' . "\n" . '</p>', $tocc, $full);
	}

	/**
	 * Generate an HTML header with an anchor for the table of contents to jump to
	 * 
	 * @param      string $level Parameter description (if any) ...
	 * @param      string $attribs Parameter description (if any) ...
	 * @param      string $anchor Parameter description (if any) ...
	 * @param      string $text Parameter description (if any) ...
	 * @param      string $link Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function makeHeadline($level, $attribs, $anchor, $text, $link)
	{
		return '<h' . $level . $attribs . '<a name="' . $anchor . '"></a><span class="tp-headline">' . $text . '</span> ' . $link . '</h' . $level . '>';
	}

	/**
	 * Start a sub-list
	 * 
	 * @return     string
	 */
	private function tocIndent()
	{
		return "\n<ul>";
	}

	/**
	 * Close a sub-list
	 * 
	 * @param      integer $level Nested list depth
	 * @return     string
	 */
	private function tocUnindent($level)
	{
		return "</li>\n" . str_repeat("</ul>\n</li>\n", $level > 0 ? $level : 0);
	}

	/**
	 * Open a list item and generate link
	 * 
	 * @param      string $anchor    Parameter description (if any) ...
	 * @param      string $tocline   Parameter description (if any) ...
	 * @param      string $tocnumber Parameter description (if any) ...
	 * @param      string $level     Parameter description (if any) ...
	 * @return     string 
	 */
	private function tocLine($anchor, $tocline, $tocnumber, $level)
	{
		return "\n" . '<li class="toclevel-' . $level . '">' .
						'<a href="' . JRoute::_('index.php?option=' . $this->option . '&scope=' . $this->scope . '&pagename=' . $this->pagename) . '#' . $anchor . '">' .
							'<span class="tocnumber">' . $tocnumber . ' </span>' .
							'<span class="toctext">' . $tocline . '</span>' .
						'</a>';
	}

	/**
	 * Close a list item
	 * 
	 * @return     string 
	 */
	private function tocLineEnd()
	{
		return "</li>\n";
 	}
}

