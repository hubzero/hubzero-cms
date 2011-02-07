<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//-------------------------------------------------------------
//  Code was heavily influenced by MediaWiki's Parser, 
//  Trac's parser, and Textile.
//-------------------------------------------------------------

class WikiParser 
{
	var $mForceTocPosition = NULL;
	var $mShowToc = NULL;
	var $mTitle = NULL;
	var $mUniqPrefix = NULL;
	var $mOutput;
	var $mAutonumber;
	var $mDTopen;
	var $mStripState;
	var $mIncludeCount;
	var $mArgStack;
	var $mLastSection;
	var $mInPre;
	var $mInterwikiLinkHolders;
	var $mLinkHolders;
	var $glyph = NULL;
	var $shelf = array();
	var $hlgn,$vlgn,$lnge,$clas,$styl,$cspn,$rspn,$a,$s,$c;
	var $pageid, $option, $scope, $pagename, $pres;
	
	//-----------
	
	public function __construct( $option='', $scope='', $pagename='', $pageid=0, $filepath='', $domain=null ) 
	{
		// We need this info for links that may get generated
		$this->option = $option;
		$this->scope = $scope;
		$this->pagename = $pagename;
		
		$this->pageid = $pageid;
		$this->filepath = $filepath;
		$this->domain = $domain;

		// Primarily needed for Table of Contents
		$this->mForceTocPosition = true;
		$this->mShowToc = true;
		//$this->mTitle = $title;
		$this->mUniqPrefix = "\x07UNIQ" . WikiParser::getRandomString();
		
		// Patterns for glyphs and attribute parsing
		$this->glyph = array(
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
		$this->hlgn = "(?:\<(?!>)|(?<!<)\>|\<\>|\=|[()]+(?! ))";
		$this->vlgn = "[\-^~]";
		$this->clas = "(?:\([^)]+\))";
		$this->lnge = "(?:\[[^]]+\])";
		$this->styl = "(?:\{[^}]+\})";
		$this->cspn = "(?:\\\\\d+)";
		$this->rspn = "(?:\/\d+)";
		$this->a = "(?:{$this->hlgn}|{$this->vlgn})*";
		$this->s = "(?:{$this->cspn}|{$this->rspn})*";
		//$this->c = "(?:{$this->clas}|{$this->styl}|{$this->lnge}|{$this->hlgn})*";
		//$this->c = "(?:{$this->clas}|{$this->styl}|{$this->lnge})*";
		$this->c = "(?:{$this->clas}|{$this->styl})*";
	}
	
	//-----------
	
	public function uniqPrefix() 
	{
		return $this->mUniqPrefix;
	}
	
	//-----------
	
	public function getRandomString() 
	{
		return dechex(mt_rand(0, 0x7fffffff)) . dechex(mt_rand(0, 0x7fffffff));
	}
	
	//-------------------------------------------------------------
	//  Where all the magic takes place
	//  Turns raw wiki text to HTML
	//-------------------------------------------------------------
	
	public function parse( $text, $fullparse=true, $linestart=0, $camelcase=1 ) 
	{
		$text = "\n".$text;

		// Clean out any carriage returns.
		// These can screw up some block parsing, such as tables
		$text = str_replace("\r","",$text);

		// Strip out <pre> code
		// We'll put this back after other processes
		$text = $this->strip( $text );
		
		$text = $this->includes( $text );
		// We need to temporarily put back and blocks we stripped and then restrip everything
		// This is because of any blocks the macros may have outputted - otherwise they wouldn't get processed
		$text = $this->unstrip( $text, false );
		$text = $this->strip( $text );
		
		// Clean out any Cross-Site Scripting attempts on the tags we didn't strip out
		$text = $this->cleanXss( $text );
		
		// Process LaTeX math forumlas and strip out
		// This will return either simple HTML or an image
		// We'll put this back after other processes
		$text = $this->math( $text );
		
		// Strip HTML tags out
		$text = strip_tags( $text, '<pre><code><xpre><math>' );
		
		// Tables need to come after variable replacement for things to work
		// properly; putting them before other transformations should keep
		// exciting things like link expansions from showing up in surprising
		// places.
		$text = $this->tables( $text );
		
		// Do horizontal rules <hr />
		$text = preg_replace( '/(^|\n)-----*/', '\\1<hr />', $text );
		
		// Do headings <h1>, <h2>, etc.
		$text = $this->headings( $text );
		
		// Do quotes. '''stuff''' => <strong>stuff</strong>
		$text = $this->doAllQuotes( $text );
		
		// Do spans
		$text = $this->span($text);
		
		// Process macros
		$text = $this->macros($text);

		// Do glyphs
		$text = $this->glyphs($text);
		
		// Do links
		$text = $this->replaceLinks( $text, $camelcase );
		
		// Clean up special characters, only run once, next-to-last before block levels
		$fixtags = array(
			// french spaces, last one Guillemet-left
			// only if there is something before the space
			'/(.) (?=\\?|:|;|!|%|\\302\\273)/' => '\\1&nbsp;\\2',
			// french spaces, Guillemet-right
			'/(\\302\\253) /' => '\\1&nbsp;',
		);
		$text = preg_replace( array_keys($fixtags), array_values($fixtags), $text );
		
		$text = $this->admonitions( $text );
		
		// Do definition lists
		$text = $this->doDFLists( $text );
		
		// Only once and last
		$text = $this->doBlockLevels( $text, $linestart );
		
		// Put back removed <math>
		$text = $this->aftermath( $text );
		
		// Strip out blank space
		$text = str_replace("<p><br />\n</p>",'',$text);

		// Put back removed <pre> and <code>
		$text = $this->unstrip( $text );
		
		// Do some clean-up for XHTML compliance
		//$text = str_replace("<p><pre>",'<pre>',$text);
		//$text = str_replace("</pre>\n</p>",'</pre>',$text);
		//$text = str_replace('<p><dl>','<dl>',$text);
		//$text = str_replace("</dl>\n</p>",'</dl>',$text);
		
		// Format headings and build a table of contents
		if (strstr($text,'<p>MACRO'.$this->uniqPrefix().'[[TableOfContents]]'."\n".'</p>')) {
			$output = $this->formatHeadings( $text );
			$text = $output['text'];
		}
		
		return $text;
	}

	//-------------------------------------------------------------
	// Links
	//-------------------------------------------------------------
	
	public function q1($text) 
	{
		return str_replace('\\"', '"', $text);
	}

	//------------

	public function wikiname_token($name, $anchor)
	{
		if ($name[0] == '!') {
			return substr($name, 1);
		} // Trim leading '!'.
		$database =& JFactory::getDBO();
		//$database->setQuery( "SELECT COUNT(*) FROM #__wiki_page WHERE pagename='". $name ."'" );
		//$database->loadResult();
		$cls = 'wiki';
		$append = '';
		$p = new WikiPage( $database );
		$p->pagename = $name;
		$p->getID();
		if (!$p->id && substr($name,0,1) != '?') {
			$cls .= ' missing';
			//$append = '?';
		}

		$link = JRoute::_('index.php?option='.$this->option.'&scope='.$this->scope.'&pagename='.$name);
		//$link = 'index.php?option='.$this->option.'&scope='.$this->scope.'&pagename='.$name;

		return '<a href="'.$link.'" class="'.$cls.'">'.$name.$append.'</a>';
	}
	
	//------------
	
	public function replaceLinks( $text, $camelcase=1 ) 
	{
		$this->reference_wiki = '';
		
		if (!class_exists('WikiPage') && is_file(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'tables'.DS.'page.php')) {
			include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'tables'.DS.'page.php');
		}
		
		// Parse for link syntax 
		// e.g. [mylink My Link] => <a href="mylink">My Link</a>
		$char_regexes = array(
			/*'internallink'=>'('.
				'\[\['. // opening brackets
					'(([^\]]*?)\:)?'. // namespace (if any)
					'([^\]]*?)'. // target
					'(\|([^\]]*?))?'. // title (if any)
				'\]\]'. // closing brackets
				'([a-z]+)?'. // any suffixes
				')',*/
			'internallink'=>'('.
				'\['. // opening brackets
					'(([^\]]*?)\:)?'. // namespace (if any)
					'([^\]\[]*?)'.
					'(\s+[^\]]*?)?'.
				'\]'. // closing brackets
				//'([a-z]+)?'. // any suffixes
				')',
			'externallink'=>'('.
				'\['.
					'([^\]\[]*?)'.
					'(\s+[^\]]*?)?'.
				'\]'.
				')'//,
		);
		$this->links = array();
		$this->linkscount = 0;
		foreach ($char_regexes as $func=>$regex) 
		{
			$this->stop = false;
			$text = preg_replace_callback("/$regex/i",array(&$this,"handle_".$func),$text);
			if ($this->stop) break;
		}
		
		// Auto link http:, etc.
		$this->alinks = array();
		$UrlPtrn  = "[^=\"\'](https?:|mailto:|ftp:|gopher:|news:|file:)" . "([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_])";
		$text = preg_replace_callback("/$UrlPtrn/", array(&$this,"handle_autolink"), $text);
		
		// Camelcase links (e.g. MyLink) 
		if ($camelcase) {
			$UpperPtn = "[A-Z]"; //"[A-Z\xc0-\xde]";
			$LowerPtn = "[a-z]"; //"[a-z\xdf-\xfe]";
			$AlphaPtn = "[A-Za-z]"; //"[A-Za-z\xc0-\xfe]";
			$LinkPtn = $UpperPtn . $AlphaPtn . '*' . $LowerPtn . '+' .
			           $UpperPtn . $AlphaPtn . '*(?:(?:\\/' . $UpperPtn . $AlphaPtn . '*)+)?';

			$ptn = "/(^|[^A-Za-z])(!?\\/?$LinkPtn)((\#[A-Za-z]([-A-Za-z0-9_:.]*[-A-Za-z0-9_])?)?)(\"\")?/e";
			$text = preg_replace($ptn,
			                      "WikiParser::q1('\\1').WikiParser::wikiname_token(WikiParser::q1('\\2'),'\\3')",
			                      $text, -1);
		}
		
		// Replace our spot holders with the links
		// This is done to avoid accidental links within links generation
		// e.g. http://w3.org/MarkUp => <a href="http://w3.org/<a href="MarkUp">MarkUp</a>">http://w3.org/<a href="MarkUp">MarkUp</a></a>
		$text = preg_replace_callback('/<link><\/link>/i',array(&$this,"restore_links"),$text);
		$text = preg_replace_callback('/<alink><\/alink>/i',array(&$this,"restore_alinks"),$text);
		return $text;
	}
	
	//------------
	
	public function handle_autolink($matches) 
	{
		$href = $matches[0];

		if (substr($href, 0, 1) == '!') {
			return substr($href, 1);
		}
		
		$href = str_replace('"','',$href);
		$href = str_replace("'",'',$href);
		$href = str_replace('&#8221','',$href);
		
		$h = array('h','m','f','g','n');
		if (!in_array(substr($href,0,1), $h)) {
			$href = substr($href, 1);
		}

		$l = sprintf(
			'<a class="ext-link" href="%s"%s>%s</a>',
			$href,
			' rel="external"',
			trim($href)
		);
		array_push($this->alinks,$l);
		return '<alink></alink>';
	}
	
	//------------
	
	public function restore_alinks($matches) 
	{
		return array_shift($this->alinks);
	}
	
	//------------

	public function handle_internallink($matches) 
	{
		$nolink = false;

		$href  = $matches[4];
		//$href .= (isset($matches[6])) ? $matches[6] : '';
		$title = (isset($matches[5])) ? $matches[5] : $href;
		$namespace = $matches[2];

		$title = preg_replace('/\(.*?\)/','',$title);
		$title = preg_replace('/^.*?\:/','',$title);
		
		$UrlPtn  = "(?:https?:|mailto:|ftp:|gopher:|news:|file:)" .
		           "(?:[^ |\\/\"\']*\\/)*[^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_]";

		if ((preg_match("/$UrlPtn/", $matches[2].$href) && strpos($matches[2].$href,'/') !== false) 
		 || substr($matches[0], 1, 1) == '/') {
			/*$matchesext = array(
				$matches[0],
				$matches[1],
				$matches[2].$href,
				$title
			);
			return $this->handle_externallink($matchesext);*/
			
			$regex = '('.'\['.'([^\]\[]*?)'.'(\s+[^\]]*?)?'.'\]'.')';
			return preg_replace_callback("/$regex/i",array(&$this,"handle_externallink"),$matches[0]);
		}
		
		$cls = 'wiki';
		
		$bits = explode('/',$href);

		$database =& JFactory::getDBO();
		$p = new WikiPage( $database );
		if (count($bits) > 1) {
			$p->pagename = array_pop($bits);
			$p->scope = implode('/',$bits);
		} else {
			$p->pagename = end($bits);
			$p->scope = $this->scope;
		}
		if (trim(strtolower($namespace)) == 'help:') {
			$p->pagename = 'Help:'.$p->pagename;
		}
		$p->getID();

		if (!$p->id) {
			$cls .= (substr($href,0,1) != '?') ? ' missing' : '';
			$p->scope = ($p->scope) ? $p->scope : $this->scope;
		} else {
			$cls .= ' int-link';
		}
		
		$href = JRoute::_('index.php?option='.$this->option.'&scope='.$p->scope.'&pagename='.$p->pagename);

		/*if ($this->reference_wiki) {
			$href = $this->reference_wiki.($namespace?$namespace.':':'').$this->wiki_link($href);
		} else {
			$nolink = true;
		}

		if ($nolink) return $title;*/
	
		$l = '<a class="'.$cls.'" href="'.$href.'">'.trim($title).'</a>';
		array_push($this->links,$l);
		return '<link></link>';
	}
	
	//------------
	
	public function restore_links($matches) 
	{
		return array_shift($this->links);
	}
	
	//------------
	
	public function wiki_link($topic) 
	{
		return ucfirst(str_replace(' ','_',$topic));
	}
	
	//------------
	
	public function handle_externallink($matches) 
	{
		$href = $matches[2];
		$title = (isset($matches[3])) ? $matches[3] : '';
		if (!$title) {
			$this->linknumber++;
			$title = "[{$this->linknumber}]";
		}
		$newwindow = false;
		
		$cls = 'int-link';
		
		$UrlPtn  = "(?:https?:|mailto:|ftp:|gopher:|news:|file:)" .
		           "(?:[^ |\\/\"\']*\\/)*[^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_]";
		
		if (!preg_match("/$UrlPtn/", $href) && strpos($href,'/') === false) {
			$href = JRoute::_('index.php?option='.$this->option.'&scope='.$this->scope.'&pagename='.$href);
			$cls = '';
		}
		$UrlPtn  = "(?:https?:|mailto:|ftp:|gopher:|news:|file:)";
		if (preg_match("/$UrlPtn/", $href)) {
			$cls = 'ext-link';
		}
		
		$l = sprintf(
			'<a class="'.$cls.'" href="%s"%s>%s</a>',
			$href,
			($newwindow?' rel="external"':''),
			trim($title)
		);
		array_push($this->links,$l);
		return '<link></link>';
	}

	//-------------------------------------------------------------
	// The following portions are for handling code and pre blocks
	//-------------------------------------------------------------
	
	private function strip( $text ) 
	{
		$this->pres = array();
		$this->codes = array();
		$this->counter = 0;
		
		$output = '';
		//$text = $this->doSpecial($text, '`{{{', '}}}`', 'fPCode');
		//$text = preg_replace('/\{\{\{([\S]*)\}\}\}/i',"<code>\\1</code>",$text);
		//$text = preg_replace('/\{\{\{([\S]*)\}\}\}/i',"`\\1`",$text);
		$bits = explode("\n", $text);
		foreach ($bits as $line) 
		{
			//$line = preg_replace_callback('/\{\{\{(.*?)/i',array(&$this,"handle_pre_up"),$line);
			//$line = preg_replace_callback('/(.*?)\}\}\}/i',array(&$this,"handle_pre_down"),$line);
			//$line = preg_replace_callback('/\{\{\{([\S]*)/Uis',array(&$this,"handle_code_up"),$line);
			//$line = preg_replace_callback('/\{\{\{([\S]*)\}\}\}/i',array(&$this,"handle_save_code"),$line);
			$line = preg_replace_callback('/\{\{\{([\s]*)/i',array(&$this,"handle_pre_up"),$line);
			$line = preg_replace_callback('/([\s]*)\}\}\}/i',array(&$this,"handle_pre_down"),$line);
			//$line = preg_replace_callback('/(.*?)\}\}\}/i',array(&$this,"handle_pre_down"),$line);
			$output .= $line."\n";
			//$output = preg_replace('/\{\{\{1([\S]*)1\}\}\}/i',"{{{\\1}}}",$output);
			$output = preg_replace('/\{\{\{1(.+)1\}\}\}/i',"{{{\\1}}}",$output);
			if ($this->counter == 0) {
				//$output = preg_replace_callback('/\{\{\{1([\S]*)1\}\}\}/i',array(&$this,"handle_save_code"),$output);
				$output = preg_replace_callback('/\{\{\{1([\s\S]*)1\}\}\}/i',array(&$this,"handle_save_pre"),$output);
				$output = $this->doSpecial($output, '`{{{', '}}}`', 'fPCode');
				$output = $this->doSpecial($output, '{{{`', '`}}}', 'fCCode');
				//$output = $this->doSpecial($output, '{{{', '}}}', 'fCode');
				//$output = str_replace('{{{','<code>',$output);
				$output = preg_replace_callback('/\{\{\{(.+?)\}\}\}/i',array(&$this,"handle_save_code"),$output);
				$output = $this->doSpecial($output, '`', '`', 'fCode');
			}
		}
		$output = str_replace('<code><code>','<code>',$output);
		$output = str_replace('</code></code>','</code>',$output);
		//$output = $this->doSpecial($output, '<code>', '</code>', 'fCode');
		//$output = $this->doSpecial($output, '<pre>', '</pre>', 'fPre');
		
		return $output;
	}
	
	//------------
	
	private function shelve($val)
	{
		$i = uniqid(rand());
		$this->shelf[$i] = $val;
		return $i;
	}
	
	//------------
	
	private function unstrip( $text, $html=true ) 
	{
		$this->_wikitohtml = $html;
		
		if (is_array($this->shelf))
			do {
				$old = $text;
				$text = strtr($text, $this->shelf);
			 } while ($text != $old);

		$text = preg_replace_callback('/<pre><\/pre>/i',array(&$this,"handle_restore_pre"),$text);
		$text = preg_replace_callback('/<code><\/code>/i',array(&$this,"handle_restore_code"),$text);
		$text = preg_replace_callback('/MACRO'.$this->mUniqPrefix.'/i',array(&$this,"restore_macros"),$text);
		
		$text = str_replace('<code><code>','<code>',$text);
		$text = str_replace('</code></code>','</code>',$text);
		
		return $text;
	}
	
	//-----------

	private function handle_pre_up($matches) 
	{
		$this->counter++;
		if ($this->counter == 1) {
			return "{{{".$this->counter.$matches[1];
		} else {
			return "{{{".$matches[1];
		}
	}

	//-----------

	private function handle_pre_down($matches) 
	{
		if ($this->counter == 1) {
			//if (trim($matches[1]) == '') {
				$html = $matches[1].$this->counter.'}}}';
				$this->counter--;
			//} else {
			//	$html = $matches[1].'}}}';
			//}
		} else {
			$html = $matches[1].'}}}';
			$this->counter--;
		}
		return $html;
	}

	//-----------

	private function handle_save_pre($matches) 
	{
		$t = trim($matches[1]);
		$t = str_replace("\n",'',$t);
		if (substr($t,0,6) == '#!wiki') {
			return '{admonition}'.$matches[1].'{/admonition}';
		} else {
			array_push($this->pres,$matches[1]);
			return "<pre></pre>";
		}
	}
	
	//-----------
	
	private function handle_save_code($matches) 
	{
		array_push($this->codes,$matches[1]);
		return "<code></code>";
	}
	
	//-----------
	
	private function handle_restore_pre($matches) 
	{
		$txt = array_shift($this->pres);
		
		if (!$this->_wikitohtml) {
			return '{{{'.$txt.'}}}';
		}

		$t = trim($txt);
		$t = str_replace("\n",'',$t);
		if (substr($t,0,6) == '#!html') {
			$txt = $this->cleanXss($txt);
			return preg_replace('/#!html/','',$txt,1);
		//} else if (substr($t,0,6) == '#!wiki') {
		//	$txt = preg_replace('/#!wiki/','',$txt,1);
		//	return '<div class="">'.$txt.'</div>';
		} else {
			return '<pre>'.$txt.'</pre>';
		}
	}
	
	//------------
	
	private function handle_restore_code($matches) 
	{
		$txt = array_shift($this->codes);
		
		if (!$this->_wikitohtml) {
			$txt = str_replace('<code>','',$txt);
			$txt = str_replace('</code>','',$txt);
			return '`'.$txt.'`';
		}
		
		$t = trim($txt);
		$t = str_replace("\n",'',$t);
		return '<code>'.$txt.'</code>';
	}
	
	//------------
	
	private function doSpecial($text, $start, $end, $method='fSpecial')
	{
	  return preg_replace_callback('/(^|\s|[[({>])'.preg_quote($start, '/').'(.*?)'.preg_quote($end, '/').'(\s|$|[\])}])?/ms', array(&$this, $method), $text);
	}

	//------------
	
	private function fSpecial($m)
	{
		// A special block like notextile or code
		@list(, $before, $text, $after) = $m;
		return $before.$this->shelve($this->encode_html($text)).$after;
	}

	//------------
	
	private function fPCode($m)
	{
		@list(, $before, $text, $after) = $m;
		//return $before.$this->shelve('<code>{{{'.$this->encode_html($text).'}}}</code>').$after;
		array_push($this->codes,'<code>{{{'.$this->encode_html($text).'}}}</code>');
		return $before.'<code></code>'.$after;
	}
	private function fCCode($m)
	{
		@list(, $before, $text, $after) = $m;
		//return $before.$this->shelve('<code>{{{'.$this->encode_html($text).'}}}</code>').$after;
		array_push($this->codes,'<code>`'.$this->encode_html($text).'`</code>');
		return $before.'<code></code>'.$after;
	}
	
	//-----------
	
	private function fCode($m)
	{
		@list(, $before, $text, $after) = $m;
		//return $before.$this->shelve('<code>'.$this->encode_html($text).'</code>').$after;
		array_push($this->codes,'<code>'.$this->encode_html($text).'</code>');
		return $before.'<code></code>'.$after;
	}

	//------------
	
	private function fPre($m)
	{
		@list(, $before, $text, $after) = $m;
		return $before.'<pre>'.$this->shelve($this->encode_html($text)).'</pre>'.$after;
	}

	//------------
	
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
	
	//------------
	
	private function cleanXss($string) 
	{
		/*if (get_magic_quotes_gpc()) {
			$string = stripslashes($string);
		}*/
		
		// Strip out any KL_PHP, script, style, HTML comments
		$string = preg_replace( '/{kl_php}(.*?){\/kl_php}/s', '', $string );
		$string = preg_replace( "'<style[^>]*>.*?</style>'si", '', $string );
		$string = preg_replace( "'<script[^>]*>.*?</script>'si", '', $string );
		$string = preg_replace( '/<!--.+?-->/', '', $string );
		
		$string = str_replace(array("&amp;","&lt;","&gt;"),array("&amp;amp;","&amp;lt;","&amp;gt;",),$string);
		// Fix &entitiy\n;
		
		$string = preg_replace('#(&\#*\w+)[\x00-\x20]+;#u',"$1;",$string);
		$string = preg_replace('#(&\#x*)([0-9A-F]+);*#iu',"$1$2;",$string);
		$string = html_entity_decode($string, ENT_COMPAT, "UTF-8");
		
		// Remove any attribute starting with "on" or xmlns
		//$string = preg_replace('#(<[^>]+[\x00-\x20\"\'])(on|xmlns)[^>]*>#iUu',"$1>",$string);
		// Remove javascript: and vbscript: protocol
		$string = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*)[\\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu','$1=$2nojavascript...',$string);
		$string = preg_replace('#([a-z]*)[\x00-\x20]*=([\'\"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu','$1=$2novbscript...',$string);
		// <span style="width: expression(alert('Ping!'));"></span> 
		// Only works in ie...
		$string = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*expression[\x00-\x20]*\([^>]*>#iU',"$1>",$string);
		$string = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*behaviour[\x00-\x20]*\([^>]*>#iU',"$1>",$string);
		$string = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*>#iUu',"$1>",$string);
		// Remove namespaced elements (we do not need them...)
		$string = preg_replace('#</*\w+:\w[^>]*>#i',"",$string);
		// Remove really unwanted tags
		do {
			$oldstring = $string;
			$string = preg_replace('#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i',"",$string);
		} while ($oldstring != $string);
	
		return $string;
	}

	//-------------------------------------------------------------
	//  Admonitions
	//-------------------------------------------------------------
	
	private function admonitions($text) 
	{
		$text = preg_replace_callback('/\{admonition\}(.*?)\{\/admonition\}/s', array(&$this,'admonitionCallback'), $text);

		return $text;
	}
	
	//-----------
	
	private function admonitionCallback( $matches ) 
	{
		$txt = $matches[1];
		$bits = explode("\n", $txt);
		$cls = array_shift($bits);
		if (strstr($cls, '#!wiki')) {
			$cls = str_replace('#!wiki','',$cls);
			$cls = trim($cls);
		}
		$txt = implode("\n",$bits);
		
		return '<div class="admon-'.$cls.'">'."\n".$txt.'</div>';
	}

	//-------------------------------------------------------------
	// Math
	//-------------------------------------------------------------

	private function math( $text ) 
	{
		$path = dirname(__FILE__);
		if (is_file($path.DS.'math.php')) {
			include_once($path.DS.'math.php');
			include_once($path.DS.'math'.DS.'math.php');
		} else {
			return $text;
		}
		
		$this->maths = array();
		
		$regexp = '/<math>(.*?)<\/math>/s';
		$text = preg_replace_callback($regexp, array(&$this,'mathCallback'), $text);

		return $text;
	}
	
	//-----------
	
	private function aftermath( $text ) 
	{
		$text = preg_replace_callback('/<math><\/math>/i',array(&$this,"restore_math"),$text);
		return $text;
	}
	
	//-----------
	
	private function restore_math($matches) 
	{
		return '<span class="asciimath">'.array_shift($this->maths).'</span>';
	}
	
	//-----------
	
	private function mathCallback( $mtch_arr ) 
	{
		$txt = trim($mtch_arr[1]);
		
		$database =& JFactory::getDBO();
		$tables = $database->getTableList();
		
		$table = $database->_table_prefix.'wiki_math';
		if (!in_array($table,$tables)) {
			$database->setQuery( "CREATE TABLE `#__wiki_math` (
			  `inputhash` varbinary(16) NOT NULL,
			  `outputhash` varbinary(16) NOT NULL,
			  `conservativeness` tinyint(4) NOT NULL,
			  `html` text,
			  `mathml` text,
			  `id` int(11) NOT NULL auto_increment,
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `inputhash` (`inputhash`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1;" );
			if (!$database->query()) {
				echo $database->getErrorMsg();
				return false;
			}
		}
		
		//require_once('math.php');
		//require_once('math'.DS.'math.php');

		$m = MathRenderer::renderMath( $txt, array('option'=>$this->option) );
		
		array_push($this->maths,$m);
		return '<math></math>';
	}
	
	//-------------------------------------------------------------
	// Macros
	//-------------------------------------------------------------
	
	private function includes($text)
	{
		$path = dirname(__FILE__);
		if (is_file($path.DS.'macro.php')) {
			include_once($path.DS.'macro.php');
		} else {
			return $text;
		}
		
		$this->includes = array();
		
		// Get macros [[name(args)]]
		$pattern = '/\[\[(?P<includename>[\w]+)(\]\]|\((?P<includeargs>.*)\)\]\])/U';
		$text = preg_replace_callback($pattern, array(&$this,'getInclude'), $text);

		return $text;
	}
	
	//-----------

	private function getInclude($matches) 
	{
		if (isset($matches[1]) && $matches[1] != '') {
			if (strtolower($matches[1]) != 'include') {
				return $matches[0];
			}
			
			$scope = $this->scope;
			if (strstr($matches[3], '/')) {
				$bits = explode('/', $matches[3]);
				$pagename = array_pop($bits);
				$s = trim(implode('/', $bits));
				if (substr($s, 0, 1) == DS) {
					$s = substr($s, 1);
				}
				if (substr($s, -1) == DS) {
					$s = substr($s, 0, -1);
				}
				$scope .= DS.$s;
			} else {
				$pagename = $matches[3];
			}
			
			$database =& JFactory::getDBO();
			$p = new WikiPage( $database );
			$p->load( $pagename, $scope );
			
			if ($p->id) {
				$revision = $p->getCurrentRevision();
				
				return $revision->pagetext;
			} else {
				return '';
			}
		}
	}

	//-------------------------------------------------------------
	// Macros
	//-------------------------------------------------------------
	
	private function macros($text)
	{
		$path = dirname(__FILE__);
		if (is_file($path.DS.'macro.php')) {
			include_once($path.DS.'macro.php');
		} else {
			return $text;
		}
		
		$this->macros = array();
		
		// Get macros [[name(args)]]
		$pattern = '/\[\[(?P<macroname>[\w]+)(\]\]|\((?P<macroargs>.*)\)\]\])/U';
		$text = preg_replace_callback($pattern, array(&$this,'getMacro'), $text);

		return $text;
	}
	
	//-----------

	private function getMacro($matches) 
	{
		if (isset($matches[1]) && $matches[1] != '') {
			if (strtolower($matches[1]) == 'br') {
				return '<br />';
			}
			
			$matches[1] = strtolower($matches[1]);
			
			$path = dirname(__FILE__);
			if (is_file($path.DS.'macros'.DS.$matches[1].'.php')) {
				include_once($path.DS.'macros'.DS.$matches[1].'.php');
			} else {
				return '';
			}
			
			$matches[1] = ucfirst($matches[1]);
			
			$macroname = $matches[1].'Macro';

			if (class_exists($macroname)) {
				$macro = new $macroname();

				if (isset($matches[3]) && $matches[3]) {
					$macro->args = $matches[3];
				}
				$macro->option   = $this->option;
				$macro->scope    = $this->scope;
				$macro->pagename = $this->pagename;
				$macro->domain   = $this->domain;
				$macro->uniqPrefix = $this->uniqPrefix();
				if ($this->pageid > 0) {
					$macro->pageid   = $this->pageid;
				} else {
					$macro->pageid   = JRequest::getInt( 'lid', 0, 'post' );
				}
				$macro->filepath = $this->filepath;

				//return $macro->render();
				array_push($this->macros,$macro->render());
				return 'MACRO'.$this->mUniqPrefix;
			} else {
				return '';
			}
		}
	}
	
	//-----------
	
	private function restore_macros($matches) 
	{
		return array_shift($this->macros);
	}

	//-------------------------------------------------------------
	// Misc.
	//-------------------------------------------------------------

	public function glyphs($text)
	{
		// fix: hackish
		$text = preg_replace('/"\z/', "\" ", $text);
		$pnc = '[[:punct:]]';

		$glyph_search = array(
			'/(\w)\'(\w)/', 									 // apostrophe's
			'/(\s)\'(\d+\w?)\b(?!\')/', 						 // back in '88
			'/(\S)\'(?=\s|'.$pnc.'|<|$)/',						 //  single closing
			'/\'/', 											 //  single opening
			'/(\S)\"(?=\s|'.$pnc.'|<|$)/',						 //  double closing
			'/"/',												 //  double opening
			'/\b([A-Z][A-Z0-9]{2,})\b(?:[(]([^)]*)[)])/',		 //  3+ uppercase acronym
			'/(?<=\s|^|[>(;-])([A-Z]{3,})([a-z]*)(?=\s|'.$pnc.'|<|$)/',  //  3+ uppercase
			'/([^.]?)\.{3}/',									 //  ellipsis
			'/(\s?)--(\s?)/',									 //  em dash
			'/\s-(?:\s|$)/',									 //  en dash
			'/(\d+)( ?)x( ?)(?=\d+)/',							 //  dimension sign
			'/(\b ?|\s|^)[([]TM[])]/i', 						 //  trademark
			'/(\b ?|\s|^)[([]R[])]/i',							 //  registered
			'/(\b ?|\s|^)[([]C[])]/i',							 //  copyright
		);

		extract($this->glyph, EXTR_PREFIX_ALL, 'txt');

		$glyph_replace = array(
			'$1'.$txt_apostrophe.'$2',			 // apostrophe's
			'$1'.$txt_apostrophe.'$2',			 // back in '88
			'$1'.$txt_quote_single_close,		 //  single closing
			$txt_quote_single_open, 			 //  single opening
			'$1'.$txt_quote_double_close,		 //  double closing
			$txt_quote_double_open, 			 //  double opening
			'<acronym title="$2">$1</acronym>',  //  3+ uppercase acronym
			'<span class="caps">$1</span>$2',	 //  3+ uppercase
			'$1'.$txt_ellipsis, 				 //  ellipsis
			'$1'.$txt_emdash.'$2',				 //  em dash
			' '.$txt_endash.' ',				 //  en dash
			'$1$2'.$txt_dimension.'$3', 		 //  dimension sign
			'$1'.$txt_trademark,				 //  trademark
			'$1'.$txt_registered,				 //  registered
			'$1'.$txt_copyright,				 //  copyright
		);

		$text = preg_split("@(<[\w/!?].*>)@Us", $text, -1, PREG_SPLIT_DELIM_CAPTURE);
		$i = 0;
		foreach ($text as $line) 
		{
			// text tag text tag text ...
			if (++$i % 2) {
				$line = $this->encode_html($line,0);
				$line = preg_replace($glyph_search, $glyph_replace, $line);
			}
			$glyph_out[] = $line;
		}
		return join('', $glyph_out);
	}

	//------------

	private function pba($in, $element = "", $include_id = 1) // "parse block attributes"
	{
		$style = '';
		$class = '';
		$lang = '';
		$colspan = '';
		$rowspan = '';
		$id = '';
		$atts = '';

		if (!empty($in)) {
			$matched = $in;
			if ($element == 'td') {
				if (preg_match("/\\\\(\d+)/", $matched, $csp)) $colspan = $csp[1];
				if (preg_match("/\/(\d+)/", $matched, $rsp)) $rowspan = $rsp[1];
			}

			if ($element == 'td' or $element == 'tr') {
				if (preg_match("/($this->vlgn)/", $matched, $vert))
					$style[] = "vertical-align:" . $this->vAlign($vert[1]) . ";";
			}

			if (preg_match("/\{([^}]*)\}/", $matched, $sty)) {
				$style[] = rtrim($sty[1], ';') . ';';
				$matched = str_replace($sty[0], '', $matched);
			}

			if (preg_match("/\[([^]]+)\]/U", $matched, $lng)) {
				$lang = $lng[1];
				$matched = str_replace($lng[0], '', $matched);
			}

			if (preg_match("/\(([^()]+)\)/U", $matched, $cls)) {
				$class = $cls[1];
				$matched = str_replace($cls[0], '', $matched);
			}

			if (preg_match("/([(]+)/", $matched, $pl)) {
				$style[] = "padding-left:" . strlen($pl[1]) . "em;";
				$matched = str_replace($pl[0], '', $matched);
			}

			if (preg_match("/([)]+)/", $matched, $pr)) {
				$style[] = "padding-right:" . strlen($pr[1]) . "em;";
				$matched = str_replace($pr[0], '', $matched);
			}

			if (preg_match("/($this->hlgn)/", $matched, $horiz))
				$style[] = "text-align:" . $this->hAlign($horiz[1]) . ";";

			if (preg_match("/^(.*)#(.*)$/", $class, $ids)) {
				$id = $ids[2];
				$class = $ids[1];
			}

			return join('',array(
				($style)   ? ' style="'   . join("", $style) .'"':'',
				($class)   ? ' class="'   . $class			 .'"':'',
				($lang)    ? ' lang="'	  . $lang			 .'"':'',
				($id and $include_id) ? ' id="'. $id         .'"':'',
				($colspan) ? ' colspan="' . $colspan		 .'"':'',
				($rowspan) ? ' rowspan="' . $rowspan		 .'"':''
			));
		}
		return '';
	}

	//------------

	private function iAlign($in)
	{
		$vals = array(
			'<' => 'left',
			'=' => 'center',
			'>' => 'right');
		return (isset($vals[$in])) ? $vals[$in] : '';
	}

	//------------
	
	private function hAlign($in)
	{
		$vals = array(
			'<'  => 'left',
			'='  => 'center',
			'>'  => 'right',
			'<>' => 'justify');
		return (isset($vals[$in])) ? $vals[$in] : '';
	}

	//------------

	private function vAlign($in)
	{
		$vals = array(
			'^' => 'top',
			'-' => 'middle',
			'~' => 'bottom');
		return (isset($vals[$in])) ? $vals[$in] : '';
	}

	//------------

	private function span($text)
	{
		$qtags = array('\?\?','__','%','\+','~~',',,','\^');
		$pnct = ".,\"'?!;:";

		foreach($qtags as $f) {
			$text = preg_replace_callback("/
				(^|(?<=[\s>$pnct\(])|[{[])
				($f)(?!$f)
				({$this->c})
				(?::(\S+))?
				([^\s$f]+|\S.*?[^\s$f\n])
				([$pnct]*)
				$f
				($|[\]}]|(?=[[:punct:]]{1,2}|\s|\)))
			/x", array(&$this, "fSpan"), $text);
		}
		$text = preg_replace('/\^(.*?)\^/',"<sup>\\1</sup>",$text);
		$text = preg_replace('/,,(.*?),,/',"<sub>\\1</sub>",$text);
		return $text;
	}

	//------------
	
	private function fSpan($m)
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
		$tag = $qtags[$tag];
		$atts = ''; //$this->pba($atts);
		$atts .= ($cite != '') ? 'cite="' . $cite . '"' : '';

		if ($tag == 'u') {
			$atts .= ' style="text-decoration:underline;"';
			$tag = 'span';
		}
		
		$out = "<$tag$atts>$content$end</$tag>";

		if (($pre and !$tail) or ($tail and !$pre))
			$out = $pre.$out.$tail;
		
		return $out;
	}
	
	//-------------------------------------------------------------
	// Headings
	//-------------------------------------------------------------
	
	private function headings( $text ) 
	{
		for ( $i = 6; $i >= 1; --$i ) 
		{
			$h = str_repeat( '=', $i );

			$patterns = array("/^(.*){$h}(.+){$h}\s\#(.*)\\s*$/m","/^(.*){$h}(.+){$h}\\s*$/m");
			$replace = array("\\1<h{$i} id=\"\\3\">\\2</h{$i}>\\4", "\\1<h{$i}>\\2</h{$i}>\\3");
			$text = preg_replace( $patterns, $replace, $text );
		}
		return $text;
	}
	
	//-------------------------------------------------------------
	// Quotes
	//-------------------------------------------------------------

	private function doAllQuotes( $text ) 
	{
		$outtext = '';
		$lines = explode( "\n", $text );
		foreach ($lines as $line) 
		{
			$outtext .= $this->doQuotes( $line ) . "\n";
		}
		$outtext = substr($outtext, 0,-1);
		return $outtext;
	}

	//------------

	private function doQuotes( $text ) 
	{
		$arr = preg_split( "/(''+)/", $text, -1, PREG_SPLIT_DELIM_CAPTURE );
		if (count( $arr ) == 1) {
			return $text;
		} else {
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
					if ( strlen( $arr[$i] ) == 4 ) 
					{
						$arr[$i-1] .= "'";
						$arr[$i] = "'''";
					}
					// If there are more than 5 apostrophes in a row, assume they're all
					// text except for the last 5.
					else if ( strlen( $arr[$i] ) > 5 )
					{
						$arr[$i-1] .= str_repeat( "'", strlen( $arr[$i] ) - 5 );
						$arr[$i] = "'''''";
					}
					// Count the number of occurrences of bold and italics mark-ups.
					// We are not counting sequences of five apostrophes.
					if ( strlen( $arr[$i] ) == 2 )      { $numitalics++;             }
					else if ( strlen( $arr[$i] ) == 3 ) { $numbold++;                }
					else if ( strlen( $arr[$i] ) == 5 ) { $numitalics++; $numbold++; }
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
				$firstmultiletterword = -1;
				$firstspace = -1;
				foreach ($arr as $r)
				{
					if ( ( $i % 2 == 1 ) and ( strlen( $r ) == 3 ) )
					{
						$x1 = substr ($arr[$i-1], -1);
						$x2 = substr ($arr[$i-1], -2, 1);
						if ($x1 == ' ') {
							if ($firstspace == -1) $firstspace = $i;
						} else if ($x2 == ' ') {
							if ($firstsingleletterword == -1) $firstsingleletterword = $i;
						} else {
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
			$state = '';
			$i = 0;
			foreach ($arr as $r)
			{
				if (($i % 2) == 0)
				{
					if ($state == 'both')
						$buffer .= $r;
					else
						$output .= $r;
				} else {
					if (strlen ($r) == 2)
					{
						if ($state == 'i')
						{ $output .= '</i>'; $state = ''; }
						else if ($state == 'bi')
						{ $output .= '</i>'; $state = 'b'; }
						else if ($state == 'ib')
						{ $output .= '</b></i><b>'; $state = 'b'; }
						else if ($state == 'both')
						{ $output .= '<b><i>'.$buffer.'</i>'; $state = 'b'; }
						else // $state can be 'b' or ''
						{ $output .= '<i>'; $state .= 'i'; }
					}
					else if (strlen ($r) == 3)
					{
						if ($state == 'b')
						{ $output .= '</b>'; $state = ''; }
						else if ($state == 'bi')
						{ $output .= '</i></b><i>'; $state = 'i'; }
						else if ($state == 'ib')
						{ $output .= '</b>'; $state = 'i'; }
						else if ($state == 'both')
						{ $output .= '<i><b>'.$buffer.'</b>'; $state = 'i'; }
						else // $state can be 'i' or ''
						{ $output .= '<b>'; $state .= 'b'; }
					}
					else if (strlen ($r) == 5)
					{
						if ($state == 'b')
						{ $output .= '</b><i>'; $state = 'i'; }
						else if ($state == 'i')
						{ $output .= '</i><b>'; $state = 'b'; }
						else if ($state == 'bi')
						{ $output .= '</i></b>'; $state = ''; }
						else if ($state == 'ib')
						{ $output .= '</b></i>'; $state = ''; }
						else if ($state == 'both')
						{ $output .= '<i><b>'.$buffer.'</b></i>'; $state = ''; }
						else // ($state == '')
						{ $buffer = ''; $state = 'both'; }
					}
				}
				$i++;
			}
			// Now close all remaining tags.  Notice that the order is important.
			if ($state == 'b' || $state == 'ib')
				$output .= '</b>';
			if ($state == 'i' || $state == 'bi' || $state == 'ib')
				$output .= '</i>';
			if ($state == 'bi')
				$output .= '</b>';
			// There might be lonely ''''', so make sure we have a buffer
			if ($state == 'both' && $buffer)
				$output .= '<b><i>'.$buffer.'</i></b>';
			return $output;
		}
	}
	
	//-------------------------------------------------------------
	// Tables
	//-------------------------------------------------------------

	private function tables($text) 
	{
		$text .= "\n\n";
		return preg_replace_callback("/^(?:table(_?{$this->s}{$this->a}{$this->c})\. ?\n)?^({$this->a}{$this->c}\.? ?\|\|.*\|\|)\n\n/smU",
			array(&$this, "doTable"), $text);
	}
	
	//------------
	
	private function doTable($matches)
	{
		$tatts = $this->pba($matches[1], 'table');
		
		foreach (preg_split("/\|\|$/m", $matches[2], -1, PREG_SPLIT_NO_EMPTY) as $row) 
		{
			if (preg_match("/^($this->a$this->c\. )(.*)/m", ltrim($row), $rmtch)) {
				$ratts = $this->pba($rmtch[1], 'tr');
				$row = $rmtch[2];
			} else $ratts = '';
			
			$cells = array();
			foreach(explode("|", $row) as $cell) 
			{
				$ctyp = "d";
				if (preg_match("/^_/", $cell)) $ctyp = "h";
				if (preg_match("/^(_?$this->s$this->a$this->c\. )(.*)/", $cell, $cmtch)) {
					$catts = $this->pba($cmtch[1], 'td');
					$cell = $cmtch[2];
				} else $catts = '';
				
				$cell = $this->span($cell);
				
				if (trim($cell) != '')
					$cells[] = "\t\t\t<t$ctyp$catts>$cell</t$ctyp>";
			}
			$rows[] = "\t\t<tr$ratts>\n" . join("\n", $cells) . ($cells ? "\n" : "") . "\t\t</tr>";
			unset($cells, $catts);
		}
		return "\t<table$tatts>\n" . join("\n", $rows) . "\n\t</table>\n\n";
	}
	
	//-------------------------------------------------------------
	// Block levels
	//-------------------------------------------------------------

	private function closeParagraph() 
	{
		$result = '';
		if ( '' != $this->mLastSection ) {
			$result = '</' . $this->mLastSection  . ">\n";
		}
		$this->mInPre = false;
		$this->mLastSection = '';
		return $result;
	}
	
	//------------
	// Returns the length of the longest common substring
	// of both arguments, starting at the beginning of both.
	private function getCommon( $st1, $st2 ) 
	{
		$fl = $st1; //strlen( $st1 );
		$shorter = $st2; //strlen( $st2 );
		if ( $fl < $shorter ) { $shorter = $fl; }

		for ( $i = 0; $i < $shorter; ++$i ) {
			if ( $st1{$i} != $st2{$i} ) { break; }
		}
		return $i;
	}
	
	//------------
	// These next three functions open, continue, and close the list
	// element appropriate to the prefix character passed into them.
	private function openList( $char ) 
	{
		$result = $this->closeParagraph();

		if ( '*' == $char ) { $result .= '<ul><li>'; }
		else if ( '#' == $char ) { $result .= '<ol><li>'; }
		else if ( ':' == $char ) { $result .= '<dl><dd>'; }
		else if ( ';' == $char ) {
			$result .= '<dl><dt>';
			$this->mDTopen = true;
		}
		else { $result = '<!-- ERR 1 -->'; }

		return $result;
	}

	//------------
	
	private function nextItem( $char ) 
	{
		if ( '*' == $char || '#' == $char ) { return '</li><li>'; }
		else if ( ':' == $char || ';' == $char ) {
			$close = '</dd>';
			if ( $this->mDTopen ) { $close = '</dt>'; }
			if ( ';' == $char ) {
				$this->mDTopen = true;
				return $close . '<dt>';
			} else {
				$this->mDTopen = false;
				return $close . '<dd>';
			}
		}
		return '<!-- ERR 2 -->';
	}

	//------------

	private function closeList( $char ) 
	{
		if ( '*' == $char ) { $text = '</li></ul>'; }
		else if ( '#' == $char ) { $text = '</li></ol>'; }
		else if ( ':' == $char ) {
			if ( $this->mDTopen ) {
				$this->mDTopen = false;
				$text = '</dt></dl>';
			} else {
				$text = '</dd></dl>';
			}
		}
		else {	return '<!-- ERR 3 -->'; }
		return $text."\n";
	}
	
	//------------
	
	private function doDFLists( $text ) 
	{
		$textLines = explode( "\n", $text );
		//if ( !$linestart ) {
		//	$output .= array_shift( $textLines );
		//}
		$indl = false;
		$indd = false;
		$output = '';
		foreach ( $textLines as $oLine ) 
		{
			//$text = preg_replace_callback('/\[\[Tool:(.*?)*\]\]/sU', array(&$this,'getResource'), $text);
			//if ($indl && $oLine=='') {
			//	continue;
			//}
			/*if ($prefixLength == 2 && preg_match('/\s{2,}(.*?)/sU', $oLine) && $pref !=) {
				$output .= '<blockquote>'.trim($oLine).'</blockquote>'."\n";
			}*/
			if (preg_match('/(.*?)::(\s*)/sU', $oLine)) {
				if ($indl) {
					//$output .= '</dd></dl>'."\n";
					$output .= '</dd>'."\n";
					$output .= preg_replace('/\s(.*?)::(\s*)/sU', "<dt>\\1</dt>\n", $oLine);
				} else {
					$output .= preg_replace('/\s(.*?)::(\s*)/sU', "<dl><dt>\\1</dt>\n", $oLine);
				}
				$indl = true;
				$indd = false;
			} else {
				if ($indl) {
					if (preg_match('/\s{2,}(.*?)/sU', $oLine)) {
						if (!$indd) {
							$indd = true;
							$output .= '<dd>';
						}
						$output .= trim($oLine)."\n";
						//$output .= '<dd>'.trim($oLine).'</dd>'."\n";
					} else {
						$indd = false;
						if (!preg_match('/(.*?)::(\s*)/sU', $oLine)) {
							$indl = false;
							$output .= '</dd></dl>'."\n";
						}
					}
				} else {
					$output .= $oLine."\n";
				}
			}
		}
		return $output;
	}

	//------------
	// Make lists from lines starting with 'some text::', '*', '#', etc.
	private function doBlockLevels( $text, $linestart ) 
	{
		// Parsing through the text line by line.  The main thing
		// happening here is handling of block-level elements p, pre,
		// and making lists from lines starting with * # : etc.
		$textLines = explode( "\n", $text );

		$lastPrefix = $output = '';
		$lastPrefixLength = 0;
		$this->mDTopen = $inBlockElem = false;
		$prefixLength = 0;
		$paragraphStack = false;
		$openlist = array();$i = 0;
		if (!$linestart) {
			$output .= array_shift( $textLines );
		}
		$indl = false;
		$indd = false;
		foreach ($textLines as $oLine) 
		{
			$preCloseMatch = preg_match('/<\\/pre/i', $oLine );
			$preOpenMatch = preg_match('/<pre/i', $oLine );			
			if (!$this->mInPre) {
				$prefixLength = strspn( $oLine, ' ' );
				if ($prefixLength > 0) {
					$pref = substr( trim($oLine), 0, 1 );
				} else {
					$pref = substr( $oLine, $prefixLength, $prefixLength );
				}

				// eh?
				$pref2 = str_replace( ';', ':', $pref );
				if ($prefixLength > 0) {
					$t = substr( $oLine, $prefixLength+1 );
				} else {
					$t = substr( $oLine, $prefixLength );
				}
				$this->mInPre = !empty($preOpenMatch);
			} else {
				// Don't interpret any other prefixes in preformatted text
				$prefixLength = 0;
				$pref = $pref2 = '';
				$t = $oLine;
			}
			if ($prefixLength == 2 && $pref !='*' && $pref !='#' && $pref !=';') {
				$t = '<blockquote>'.trim($oLine).'</blockquote>'."\n";
				//$output .= '<blockquote>'.trim($oLine).'</blockquote>'."\n";
				$prefixLength = 0;
				//continue;
			}
			// List generation
			if ($prefixLength && 0 == strcmp( $lastPrefix, $pref2 ) && $prefixLength==$lastPrefixLength) {
				// Same as the last item, so no need to deal with nesting or opening stuff
				$output .= $this->nextItem( $pref );
				$paragraphStack = false;
				if (substr( $pref, -1 ) == ';') {
					// The one nasty exception: definition lists work like this:
					// ; title : definition text
					// So we check for : in the remainder text to split up the
					// title and definition, without b0rking links.
					$term = $t2 = '';
					if ($this->findColonNoLinks($t, $term, $t2) !== false) {
						$t = $t2;
						$output .= $term . $this->nextItem( ':' );
					}
				}
			} elseif ($prefixLength || $lastPrefixLength) {
				$commonPrefixLength = $this->getCommon( $prefixLength, $lastPrefixLength );
				$paragraphStack = false;

				while ($commonPrefixLength < $lastPrefixLength) 
				{
					$lastPrefix = $openlist[$i--];
					$output .= $this->closeList( $lastPrefix );
					--$lastPrefixLength;
				}
				if ($prefixLength <= $commonPrefixLength && $commonPrefixLength > 0) {
					$output .= $this->nextItem( $pref );
				}
				
				while ($prefixLength > $commonPrefixLength) 
				{
					$char = trim($pref);
					$output .= $this->openList( $char );
					$i++;
					$openlist[$i] = $char;
					if (';' == $char) {
						// FIXME: This is dupe of code above
						if ($this->findColonNoLinks($t, $term, $t2) !== false) {
							$t = $t2;
							$output .= $term . $this->nextItem( ':' );
						}
					}
					++$commonPrefixLength;
				}
				$lastPrefix = $pref2;
				$lastPrefixLength = $prefixLength;
			}
			if (0 == $prefixLength) {
				// No prefix (not in list)--go to paragraph mode
				// XXX: use a stack for nestable elements like span, table and div
				$openmatch = preg_match('/(?:<table|<blockquote|<h1|<h2|<h3|<h4|<h5|<h6|<pre|<tr|<p|<dl|<ul|<ol|<li|<\\/tr|<\\/td|<\\/th)/iS', $t );
				$closematch = preg_match(
					'/(?:<\\/table|<\\/blockquote|<\\/h1|<\\/h2|<\\/h3|<\\/h4|<\\/h5|<\\/h6|'.
					'<td|<th|<\\/?div|<hr|<\\/pre|<\\/p|'.$this->mUniqPrefix.'-pre|<\\/li|<\\/dl|<\\/ul|<\\/ol|<\\/?center)/iS', $t );
				if ($openmatch or $closematch) {
					$paragraphStack = false;
					// TODO bug 5718: paragraph closed
					$output .= $this->closeParagraph();
					if ($preOpenMatch and !$preCloseMatch) {
						$this->mInPre = true;
					}
					if ($closematch) {
						$inBlockElem = false;
					} else {
						$inBlockElem = true;
					}
				} else if (!$inBlockElem && !$this->mInPre) {
					if (' ' == $t{0} and ( $this->mLastSection == 'pre' or trim($t) != '' )) {
						// pre
						if ($this->mLastSection != 'pre') {
							$paragraphStack = false;
							$output .= $this->closeParagraph().'<pre>';
							$this->mLastSection = 'pre';
						}
						$t = substr( $t, 1 );
					} else {
						// paragraph
						if ( '' == trim($t) ) {
							if ( $paragraphStack ) {
								$output .= $paragraphStack.'<br />';
								$paragraphStack = false;
								$this->mLastSection = 'p';
							} else {
								if ($this->mLastSection != 'p' ) {
									$output .= $this->closeParagraph();
									$this->mLastSection = '';
									$paragraphStack = '<p>';
								} else {
									$paragraphStack = '</p><p>';
								}
							}
						} else {
							if ( $paragraphStack ) {
								$output .= $paragraphStack;
								$paragraphStack = false;
								$this->mLastSection = 'p';
							} else if ($this->mLastSection != 'p') {
								$output .= $this->closeParagraph().'<p>';
								$this->mLastSection = 'p';
							}
						}
					}
				}
			}
			// somewhere above we forget to get out of pre block (bug 785)
			if ($preCloseMatch && $this->mInPre) {
				$this->mInPre = false;
			}
			if ($paragraphStack === false) {
				$output .= $t."\n";
			}
		}
		while ($prefixLength) {
			$output .= $this->closeList( $pref2 );
			--$prefixLength;
		}
		if ('' != $this->mLastSection) {
			$output .= '</' . $this->mLastSection . '>';
			$this->mLastSection = '';
		}
		return $output;
	}
	
	//-------------------------------------------------------------
	// Break wikitext input into sections, and either pull or replace
	// some particular section's text.
	//
	// External callers should use the getSection and replaceSection methods.
	//
	// @param $text Page wikitext
	// @param $section Numbered section. 0 pulls the text before the first
	//                 heading; other numbers will pull the given section
	//                 along with its lower-level subsections.
	// @param $mode One of "get" or "replace"
	// @param $newtext Replacement text for section data.
	// @return string for "get", the extracted section text.
	//                for "replace", the whole page with the section replaced.
	//-------------------------------------------------------------
	private function extractSections( $text, $section, $mode, $newtext='' ) 
	{		
		// Strip PRE etc. to avoid confusion (true-parameter causes HTML
		// comments to be stripped as well)
		$striptext = $this->strip( $text );

		// Now that we can be sure that no pseudo-sections are in the source,
		// split it up by section
		$uniq = preg_quote( $this->uniqPrefix(), '/' );
		$comment = "(?:$uniq-!--.*?QINU\x07)";
		$secs = preg_split(
			"/
			(
				^
				(?:$comment|<\/?noinclude>)* # Initial comments will be stripped
				(=+) # Should this be limited to 6?
				.+?  # Section title...
				\\2  # Ending = count must match start
				(?:$comment|<\/?noinclude>|[ \\t]+)* # Trailing whitespace ok
				$
			|
				<h([1-6])\b.*?>
				.*?
				<\/h\\3\s*>
			)
			/mix",
			$striptext, -1,
			PREG_SPLIT_DELIM_CAPTURE);

		if ($mode == 'get') {
			if ($section == 0) {
				// "Section 0" returns the content before any other section.
				$rv = $secs[0];
			} else {
			  	// track missing section, will replace if found.
				$rv = $newtext;
			}
		} elseif ($mode == 'replace') {
			if ($section == 0) {
				$rv = $newtext . "\n\n";
				$remainder = true;
			} else {
				$rv = $secs[0];
				$remainder = false;
			}
		}
		$count = 0;
		$sectionLevel = 0;
		for ($index = 1; $index < count( $secs ); ) 
		{
			$headerLine = $secs[$index++];
			if ($secs[$index]) {
				// A wiki header
				$headerLevel = strlen( $secs[$index++] );
			} else {
				// An HTML header
				$index++;
				$headerLevel = intval( $secs[$index++] );
			}
			$content = $secs[$index++];

			$count++;
			if ($mode == 'get') {
				if ($count == $section) {
					$rv = $headerLine . $content;
					$sectionLevel = $headerLevel;
				} elseif ($count > $section) {
					if ($sectionLevel && $headerLevel > $sectionLevel) {
						$rv .= $headerLine . $content;
					} else {
						// Broke out to a higher-level section
						break;
					}
				}
			} elseif ($mode == 'replace') {
				if ($count < $section) {
					$rv .= $headerLine . $content;
				} elseif ($count == $section) {
					$rv .= $newtext . "\n\n";
					$sectionLevel = $headerLevel;
				} elseif ($count > $section) {
					if ($headerLevel <= $sectionLevel) {
						// Passed the section's sub-parts.
						$remainder = true;
					}
					if ($remainder) {
						$rv .= $headerLine . $content;
					}
				}
			}
		}
		if (is_string($rv))
			// reinsert stripped tags
			$rv = trim( $this->unstrip( $rv ) );

		return $rv;
	}

	//-------------------------------------------------------------
	// This function returns the text of a section, specified by a number ($section).
	// A section is text under a heading like == Heading == or \<h1\>Heading\</h1\>, or
	// the first section before any such heading (section 0).
	//
	// If a section contains subsections, these are also returned.
	//
	// @param $text String: text to look in
	// @param $section Integer: section number
	// @param $deftext: default to return if section is not found
	// @return string text of the requested section
	//-------------------------------------------------------------
	public function getSection( $text, $section, $deftext='' ) 
	{
		return $this->extractSections( $text, $section, 'get', $deftext );
	}

	public function replaceSection( $oldtext, $section, $text ) 
	{
		return $this->extractSections( $oldtext, $section, 'replace', $text );
	}
	
	//-------------------------------------------------------------
	//  Builds a Table of Contents and links to headings
	//-------------------------------------------------------------
	public function formatHeadings( $text, $isMain=true ) 
	{
		$wgMaxTocLevel = 15;
		$showEditLink = true;
		$doNumberHeadings = false;
		$doTocNumberHeadings = true;

		// Get all headlines for numbering them and adding funky stuff like [edit]
		// links - this is for later, but we need the number of headlines right now
		$matches = array();
		$numMatches = preg_match_all( '/<H(?P<level>[1-6])(?P<attrib>.*?'.'>)(?P<header>.*?)<\/H[1-6] *>/i', $text, $matches );

		// If there are fewer than 4 headlines in the article, do not show TOC
		// unless it's been explicitly enabled.
		$enoughToc = $this->mShowToc && (($numMatches >= 4) || $this->mForceTocPosition);
		
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

			if ($toclevel) {
				$prevlevel = $level;
				$prevtoclevel = $toclevel;
			}
			$level = $matches[1][$headlineCount];

			if ($doNumberHeadings || $doTocNumberHeadings || $enoughToc) {
				if ( $level > $prevlevel ) {
					// Increase TOC level
					$toclevel++;
					$sublevelCount[$toclevel] = 0;
					if ( $toclevel<$wgMaxTocLevel ) {
						$prevtoclevel = $toclevel;
						$toc .= $this->tocIndent();
						$numVisible++;
					}
				} elseif ( $level < $prevlevel && $toclevel > 1 ) {
					// Decrease TOC level, find level to jump to

					if ( $toclevel == 2 && $level <= $levelCount[1] ) {
						// Can only go down to level 1
						$toclevel = 1;
					} else {
						for ($i = $toclevel; $i > 0; $i--) 
						{
							if ($levelCount[$i] == $level) {
								// Found last matching level
								$toclevel = $i;
								break;
							} elseif ($levelCount[$i] < $level) {
								// Found first matching level below current level
								$toclevel = $i + 1;
								break;
							}
						}
					}
					if ($toclevel < $wgMaxTocLevel) {
						if ($prevtoclevel < $wgMaxTocLevel) {
							// Unindent only if the previous toc level was shown
							$toc .= $this->tocUnindent( $prevtoclevel - $toclevel );
						} else {
							$toc .= $this->tocLineEnd();
						}
					}
				} else {
					// No change in level, end TOC line
					if ($toclevel < $wgMaxTocLevel) {
						$toc .= $this->tocLineEnd();
					}
				}

				$levelCount[$toclevel] = $level;

				// count number of headlines for each level
				@$sublevelCount[$toclevel]++;
				$dot = 0;
				for ($i = 1; $i <= $toclevel; $i++) 
				{
					if (!empty( $sublevelCount[$i] )) {
						if ($dot) {
							$numbering .= '.';
						}
						$numbering .= $sublevelCount[$i];
						$dot = 1;
					}
				}
			}

			// The canonized header is a version of the header text safe to use for links
			// Avoid insertion of weird stuff like <math> by expanding the relevant sections
			$canonized_headline = $this->strip( $headline );

			// Strip out HTML (other than plain <sup> and <sub>: bug 8393)
			$tocline = preg_replace(
				array( '#<(?!/?(sup|sub)).*?'.'>#', '#<(/?(sup|sub)).*?'.'>#' ),
				array( '',                          '<$1>'),
				$canonized_headline
			);
			$tocline = trim( $tocline );

			// For the anchor, strip out HTML-y stuff period
			$canonized_headline = preg_replace( '/<.*?'.'>/', '', $canonized_headline );
			$canonized_headline = trim( $canonized_headline );

			// Save headline for section edit hint before it's escaped
			$headline_hint = $canonized_headline;
			$canonized_headline = Sanitizer::escapeId( $canonized_headline );
			$refers[$headlineCount] = $canonized_headline;

			// count how many in assoc. array so we can track dupes in anchors
			isset( $refers[$canonized_headline] ) ? $refers[$canonized_headline]++ : $refers[$canonized_headline] = 1;
			$refcount[$headlineCount]=$refers[$canonized_headline];

			// Don't number the heading if it is the only one (looks silly)
			if ($doNumberHeadings && count( $matches[3] ) > 1) {
				// the two are different if the line contains a link
				$headline = $numbering . ' ' . $headline;
			}

			// Create the anchor for linking from the TOC to the section
			$anchor = $canonized_headline;
			if ($refcount[$headlineCount] > 1 ) {
				$anchor .= '_' . $refcount[$headlineCount];
			}
			if ($enoughToc && ( !isset($wgMaxTocLevel) || $toclevel<$wgMaxTocLevel )) {
				if (!$doTocNumberHeadings) {
					$numbering = '';
				}
				$toc .= $this->tocLine($anchor, $tocline, $numbering, $toclevel);
			}
			// Give headline the correct <h#> tag
			if ($showEditLink && ( !$istemplate || $templatetitle !== "" )) {
				$editlink = '';//$this->editSectionLink($this->mTitle, $sectionCount+1, $headline_hint);
			} else {
				$editlink = '';
			}
			$head[$headlineCount] = $this->makeHeadline( $level, $matches['attrib'][$headlineCount], $anchor, $headline, $editlink );

			$headlineCount++;
			if( !$istemplate )
				$sectionCount++;
		}
		
		$toc .= ($toc) ? $this->tocUnindent( $toclevel - 1 ).'</ul>' : '';
		
		// Never ever show TOC if no headers
		if ($numVisible < 1) {
			$enoughToc = false;
		}
		
		// split up and insert constructed headlines
		$blocks = preg_split( '/<H[1-6].*?' . '>.*?<\/H[1-6]>/i', $text );
		$i = 0;

		foreach ($blocks as $block) 
		{
			$full .= $block;
			if ($enoughToc && !$i && $isMain && !$this->mForceTocPosition) {
				// Top anchor now in skin
				$full = $full.$toc;
			}

			if (!empty( $head[$i] )) {
				$full .= $head[$i];
			}
			$i++;
		}
		
		$tocc  = '<div class="article-toc">'."\n";
		$tocc .= '<h3 class="article-toc-heading">Content</h3>'."\n";
		$tocc .= $toc."\n";
		$tocc .= '</div>'."\n";
		
		$full = str_replace('<p>MACRO'.$this->uniqPrefix().'[[TableOfContents]]'."\n".'</p>',$tocc,$full);
		
		$bits = array();
		$bits['text'] = $full;
		$bits['toc'] = $toc;
		
		return $bits;
	}
	
	//------------

	private function makeHeadline( $level, $attribs, $anchor, $text, $link ) 
	{
		return '<h'.$level.$attribs.'<a name="'.$anchor.'"></a><span class="tp-headline">'.$text.'</span> '.$link.'</h'.$level.'>';
	}
	
	//------------
	
	private function tocIndent() 
	{
		return "\n<ul>";
	}

	//------------

	private function tocUnindent($level) 
	{
		return "</li>\n" . str_repeat( "</ul>\n</li>\n", $level>0 ? $level : 0 );
	}
	
	//------------

	private function tocLine( $anchor, $tocline, $tocnumber, $level ) 
	{
		return "\n".'<li class="toclevel-'.$level.'"><a href="'.JRoute::_('index.php?option='.$this->option.'&scope='.$this->scope.'&pagename='.$this->pagename).'#'.
			$anchor . '"><span class="tocnumber">' .
			$tocnumber . ' </span><span class="toctext">' .
			$tocline . '</span></a>';
	}
	
	//------------

	private function tocLineEnd() 
	{
		return "</li>\n";
 	}
	
	//------------
	
	private function editSectionLink( $title, $section, $hint='' ) 
	{
		if ($hint != '') {
			$hint = htmlspecialchars( $hint );
			$hint = ' title="'.$hint.'"';
		}
		return '<a class="edit button" href="'.$title.'?task=edit&amp;section='.$section.'"'.$hint.'>edit</a>';
	}
}
