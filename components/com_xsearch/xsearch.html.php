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

//----------------------------------------------------------

if (!defined("n")) {
	define("t","\t");
	define("n","\n");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class XSearchHtml 
{
	public function hed( $level, $txt ) 
	{
		return '<h'.$level.'>'.$txt.'</h'.$level.'>'.n;
	}

	//-----------
	
	public function form( $searchword, $option, $active='' ) 
	{
		$searchword = ($active && $active != 'all') ? $active.':'.$searchword : $searchword;
		
		$html  = t.'<fieldset>'.n;
		$html .= t.t.'<legend>'.JText::_('Search').'</legend>'.n;
		//$html  = t.'<fieldset>'.n;
		//$html .= t.t.'<label for="details">'.JText::_('Details').': </label> '.n;
		//$html .= t.t.'<input type="radio" id="activateDetails" name="details" checked="checked" /> On'.n;
		//$html .= t.t.'<input type="radio" id="disableDetails" name="details" /> Off'.n;
		//$html  = t.'</fieldset>'.n;
		$html .= t.t.'<label>'.JText::_('KEYWORDS').': '.n;
		$html .= t.t.'<input type="text" name="searchword" size="25" value="'. htmlentities(utf8_encode(stripslashes($searchword)),ENT_COMPAT,'UTF-8') .'" /></label>'.n;
		$html .= t.t.'<input type="submit" value="'.JText::_('SEARCH_AGAIN').'" />'.n;
		$html .= t.'</fieldset>'.n;

		return $html;
	}

	//-----------
	
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function warning( $msg, $tag='p' )
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------

	public function div($txt, $cls='', $id='')
	{
		$html  = '<div';
		$html .= ($cls) ? ' class="'.$cls.'"' : '';
		$html .= ($id) ? ' id="'.$id.'"' : '';
		$html .= '>';
		$html .= ($txt != '') ? n.$txt.n : '';
		$html .= '</div><!-- / ';
		if ($id) {
			$html .= '#'.$id;
		}
		if ($cls) {
			$html .= '.'.$cls;
		}
		$html .= ' -->'.n;
		return $html;
	}
	
	//-----------
	
	public function aside($txt)
	{
		return XSearchHtml::div($txt, 'aside');
	}
	
	//-----------
	
	public function subject($txt)
	{
		return XSearchHtml::div($txt, 'subject');
	}

	//-----------
	
	public function noKeyword( $keyword, $option ) 
	{
		$html  = '<form action="'.JRoute::_('index.php?option='.$option).'" method="get">'.n;
		$html .= XSearchHtml::aside( 
					XSearchHtml::form( $keyword, $option )
				);
		$html .= XSearchHtml::subject( 
					XSearchHtml::error( JText::_('NO_KEYWORD') )
				);
		$html .= XSearchHtml::div('','clear').n;
		$html .= '</form>'.n;
		
		$out  = XSearchHtml::div( XSearchHtml::hed( 2, JText::_('SEARCH_TITLE') ), 'full', 'content-header' ).n;
		$out .= XSearchHtml::div( $html, 'main section' );
		
		return $out;
	}

	//-----------

    public function pagenav( $pageNav, $category, $searchword, $option )
    {		
		$html = $pageNav->getListFooter();
		
		$qs_bad = 'searchword='.urlencode(strToLower($category).':').'&';
		$qs_good = 'searchword='.urlencode(strToLower($category).':'.$searchword).'&';

		$qs_bad2 = 'searchword='.urlencode(strToLower($category).':').'"';
		$qs_good2 = 'searchword='.urlencode(strToLower($category).':'.$searchword).'"';

		$html = str_replace('%5C%5C%5C%5C%5C%5C%5C%5C%5C%5C%5C%5C%5C%5C%5C', '',$html);
		$html = str_replace($qs_bad,$qs_good,$html);
		$html = str_replace($qs_bad2,$qs_good2,$html);
		$html = str_replace('&amp;&amp;','&amp;',$html);
		return $html;
    }

	//-----------

	public function categories( $cats, $active, $searchword, $total, $option ) 
	{
		// Add the "all" category
		$all = array('category'=>'all','title'=>JText::_('ALL_CATEGORIES'),'total'=>$total);
		
		array_unshift($cats, $all);
		
		// An array for storing all the links we make
		$links = array();
		
		// Loop through each category
		foreach ($cats as $cat) 
		{
			// Only show categories that have returned search results
			if ($cat['total'] > 0) {
				// If we have a specific category, prepend it to the search term
				if ($cat['category'] && $cat['category'] != 'all') {
					$blob = $cat['category'] .':'. $searchword;
				} else {
					$blob = $searchword;
				}
				
				// Is this the active category?
				$a = '';
				if ($cat['category'] == $active) {
					$a = ' class="active"';
					
					$app =& JFactory::getApplication();
					$pathway =& $app->getPathway();
					$pathway->addItem($cat['title'],'index.php?option='.$option.a.'searchword='. urlencode(stripslashes($blob)));
				}
				
				// Build the HTML
				$l = t.'<li'.$a.'><a href="/search/?searchword='. urlencode(stripslashes($blob)) .'">' . JText::_($cat['title']) . ' ('.$cat['total'].')</a>';
				// Are there sub-categories?
				if (isset($cat['_sub']) && is_array($cat['_sub'])) {
					// An array for storing the HTML we make
					$k = array();
					// Loop through each sub-category
					foreach ($cat['_sub'] as $subcat) 
					{
						// Only show sub-categories that returned search results
						if ($subcat['total'] > 0) {
							// If we have a specific category, prepend it to the search term
							if ($subcat['category']) {
								$blob = $subcat['category'] .':'. $searchword;
							} else {
								$blob = $searchword;
							}
							
							// Is this the active category?
							$a = '';
							if ($subcat['category'] == $active) {
								$a = ' class="active"';
								
								$app =& JFactory::getApplication();
								$pathway =& $app->getPathway();
								$pathway->addItem($subcat['title'],'index.php?option='.$option.a.'searchword='. urlencode(stripslashes($blob)));
							}
							
							// Build the HTML
							$k[] = t.t.t.'<li'.$a.'><a href="/search/?searchword='. urlencode(stripslashes($blob)) .'">' . JText::_($subcat['title']) . ' ('.$subcat['total'].')</a></li>';
						}
					}
					// Do we actually have any links?
					// NOTE: this method prevents returning empty list tags "<ul></ul>"
					if (count($k) > 0) {
						$l .= t.t.'<ul>'.n;
						$l .= implode( n, $k );
						$l .= t.t.'</ul>'.n;
					}
				}
				$l .= '</li>';
				$links[] = $l;
			}
		}
		// Do we actually have any links?
		// NOTE: this method prevents returning empty list tags "<ul></ul>"
		if (count($links) > 0) {
			// Yes - output the necessary HTML
			$html  = '<ul class="sub-nav">'.n;
			$html .= implode( n, $links );
			$html .= '</ul>'.n;
		} else {
			// No - nothing to output
			$html = '';
		}
		
		return $html;
	}

	//-----------
	
	public function display( $keyword, $totals, $results, $cats, $active, $option, $start=0, $limit=0, $total ) 
	{			
		$html  = XSearchHtml::div( XSearchHtml::hed( 2, JText::_('SEARCH_TITLE') ), 'full', 'content-header' ).n;
		
		$html .= '<div class="main section">'.n;
		$html .= '<form action="'.JRoute::_('index.php?option='.$option).'" method="get">'.n;
		$html .= XSearchHtml::aside( 
					XSearchHtml::form( $keyword, $option, $active ) .
					XSearchHtml::categories( $cats, $active, $keyword, $total, $option )
				);
		
		$html .= '<div class="subject">'.n;
		$html .= XSearchHtml::hed( 3, 'Search for: '.$keyword );
		
		$juri =& JURI::getInstance();
		$foundresults = false;
		$dopaging = true;
		$name = '';
		$divid = '';
		
		$k = 0;
		foreach ($results as $category)
		{
			$amt = count($category);
			
			if ($amt > 0) {
				$foundresults = true;
				
				// Is this category the active category?
				if (!$active || $active == $cats[$k]['category']) {
					// It is - get some needed info
					$name  = $cats[$k]['title'];
					$total = $cats[$k]['total'];
					$divid = 'search'.$cats[$k]['category'];
					
					if ($active == $cats[$k]['category']) {
						$dopaging = true;
					}
				} else {
					// It is not - does this category have sub-categories?
					if (isset($cats[$k]['_sub']) && is_array($cats[$k]['_sub'])) {
						// It does - loop through them and see if one is the active category
						foreach ($cats[$k]['_sub'] as $sub) 
						{
							if ($active == $sub['category']) {
								// Found an active category
								$name  = $sub['title'];
								$total = $sub['total'];
								$divid = 'search'.$sub['category'];
								
								$dopaging = true;
								break;
							}
						}
					}
				}
				
				$name  = ($name) ? $name : JText::_('ALL_CATEGORIES');
				$divid = ($divid) ? $divid : 'searchall';
				
				$num  = $total .' result';
				$num .= ($total > 1) ? 's' : '';
			
				// A function for category specific items that may be needed
				// Check if a function exist (using old style plugins)
				$f = 'plgXSearch'.ucfirst($cats[$k]['category']).'Doc';
				if (function_exists($f)) {
					$f();
				}
				// Check if a method exist (using JPlugin style)
				$obj = 'plgXSearch'.ucfirst($cats[$k]['category']);
				if (method_exists($obj, 'documents')) {
					$html .= call_user_func( array($obj,'documents') );
				}
			
				// Build the category HTML
				$html .= '<h4 class="category-header opened" id="rel-'.$divid.'">'.JText::_($name).' <small>'.$num.'</small></h4>'.n;
				$html .= '<div class="category-wrap" id="'.$divid.'">'.n;
				
				// Does this category have custom output?
				// Check if a function exist (using old style plugins)
				$func = 'plgXSearch'.ucfirst($cats[$k]['category']).'Before';
				if (function_exists($func)) {
					$html .= $func( $keyword );
				}
				// Check if a method exist (using JPlugin style)
				$obj = 'plgXSearch'.ucfirst($cats[$k]['category']);
				if (method_exists($obj, 'before')) {
					$html .= call_user_func( array($obj,'before'), $keyword );
				}
				
				$html .= '<ol class="search results">'.n;			
				foreach ($category as $row) 
				{
					$row->href = str_replace('&amp;', '&', $row->href);
					$row->href = str_replace('&', '&amp;', $row->href);
					
					// Does this category have a unique output display?
					$func = 'plgXSearch'.ucfirst($row->section).'Out';
					// Check if a method exist (using JPlugin style)
					$obj = 'plgXSearch'.ucfirst($row->section); //ucfirst($cats[$k]['category']);

					if (function_exists($func)) {
						$html .= $func( $row, $keyword );
					} elseif (method_exists($obj, 'out')) {
						$html .= call_user_func( array($obj,'out'), $row, $keyword );
					} else {
						if (strstr( $row->href, 'index.php' )) {
							$row->href = JRoute::_($row->href);
						}
						if (substr($row->href,0,1) == '/') {
							$row->href = substr($row->href,1,strlen($row->href));
						}
						
						$html .= t.'<li>'.n;
						$html .= t.t.'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'.n;
						if ($row->itext) {
							$html .= t.t.'<p>&#133; '.stripslashes($row->itext).' &#133;</p>'.n;
						} else if ($row->ftext) {
							$html .= t.t.'<p>&#133; '.stripslashes($row->ftext).' &#133;</p>'.n;
						}
						$html .= t.t.'<p class="href">'.$juri->base().$row->href.'</p>'.n;
						$html .= t.'</li>'.n;
					}
				}
				$html .= '</ol>'.n;
				// Initiate paging if we we're displaying an active category
				if ($dopaging) {
					jimport('joomla.html.pagination');
					$pageNav = new JPagination( $total, $start, $limit );

					$html .= XSearchHtml::pagenav( $pageNav, $active, $keyword, $option );
				} else {
					$html .= '<p class="moreresults">'.JText::sprintf('TOP_SHOWN', $amt);
					// Add a "more" link if necessary
					$ttl = 0;
					if (is_array($totals[$k])) {
						foreach ($totals[$k] as $t) 
						{
							$ttl += $t;
						}
					} else {
						$ttl = $totals[$k];
					}
					if ($ttl > 5) {
						$sef = JRoute::_( 'index.php?option='.$option );

						$qs = 'searchword='.urlencode(strToLower($cats[$k]['category']).':'.stripslashes($keyword));
						if (strstr( $sef, 'index' )) {
							$sef .= a.$qs;
						} else {
							$sef .= '?'.$qs;
						}
						$html .= ' | <a href="'.$sef.'">'.JText::_('SEE_MORE_RESULTS').'</a>';
					}
					$html .= '</p>'.n.n;
				}
				
				// Does this category have custom output?
				// Check if a function exist (using old style plugins)
				$func = 'plgXSearch'.ucfirst($cats[$k]['category']).'After';
				if (function_exists($func)) {
					$html .= $func( $keyword );
				}
				// Check if a method exist (using JPlugin style)
				$obj = 'plgXSearch'.ucfirst($cats[$k]['category']);
				if (method_exists($obj, 'after')) {
					$html .= call_user_func( array($obj,'after'), $keyword );
				}
				
				$html .= '</div><!-- / #'.$divid.' -->'.n;
			}
			$k++;
		}
		if (!$foundresults) {
			$html .= XSearchHtml::warning( JText::_('NO_RESULTS') );
		}
		$html .= '</div><!-- / .subject -->'.n;
		$html .= XSearchHtml::div('','clear').n;
		$html .= '</form>'.n;
		$html .= '</div><!-- / .main.section -->'.n;
		
		return $html;
	}
	
	//-----------
	
	public function str_highlight($text, $needles)
	{
		$highlight = '<span class="highlight">\1</span>';
	
		$pattern = '#(?!<.*?)(%s)(?![^<>]*?>)#i';
		$sl_pattern = '#<a\s(?:.*?)>(%s)</a>#i';

		foreach ($needles as $needle) 
		{
			$needle = preg_quote($needle);
			$regex = sprintf($pattern, $needle);
			$text = preg_replace($regex, $highlight, $text);
		}
		return $text;
	}

	//----------------------------------------------------------
	// Helper functions
	//----------------------------------------------------------

	public function shortenText($text, $chars=300, $p=1) 
	{
		$text = trim($text);
		if (strlen($text) > $chars) {
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
			$text = $text.' &#133;';
		}
		if ($p) {
			$text = '<p>'.$text.'</p>';
		}
		return $text;
	}
	
	//-----------

	public function purifyText( &$text ) 
	{
		$text = stripslashes($text);
		$text = preg_replace( '/{kl_php}(.*?){\/kl_php}/s', '', $text );
		$text = preg_replace( '/{.+?}/', '', $text );
		$text = preg_replace( "'<script[^>]*>.*?</script>'si", '', $text );
		$text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2', $text );
		$text = preg_replace( '/<!--.+?-->/', '', $text );
		$text = preg_replace( '/&nbsp;/', ' ', $text );
		$text = preg_replace( '/&amp;/', ' ', $text );
		$text = preg_replace( '/&quot;/', ' ', $text );
		$text = str_replace("\n",' ',$text);
		$text = str_replace("\r",' ',$text);
		$text = str_replace("\t",' ',$text);
		$text = strip_tags( $text );
		return $text;
	}
}
?>
