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

if (!defined("n")) {
	define("t","\t");
	define("n","\n");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class WhatsnewHtml
{
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
	
	public function hed($level, $txt)
	{
		return '<h'.$level.'>'.$txt.'</h'.$level.'>';
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
		return WhatsnewHtml::div($txt, 'aside');
	}
	
	//-----------
	
	public function subject($txt)
	{
		return WhatsnewHtml::div($txt, 'subject');
	}

	//-----------

    public function pagenav( $pageNav, $category, $period, $option )
    {		
		$html = $pageNav->getListFooter();
		
		$qs_bad = 'searchword='.urlencode(strToLower($category).':').'&';
		$qs_good = 'searchword='.urlencode(strToLower($category).':'.$period).'&';

		$qs_bad2 = 'searchword='.urlencode(strToLower($category).':').'"';
		$qs_good2 = 'searchword='.urlencode(strToLower($category).':'.$period).'"';

		$html = str_replace('%5C%5C%5C%5C%5C%5C%5C%5C%5C%5C%5C%5C%5C%5C%5C', '',$html);
		$html = str_replace($qs_bad,$qs_good,$html);
		$html = str_replace($qs_bad2,$qs_good2,$html);

		$html = str_replace('whatsnew/?','whatsnew/'.$category.'-'.$period.'/?',$html);
		
		return $html;
    }
	
	//-----------

	public function categories( $cats, $active, $period, $total, $option ) 
	{
		// Add the "all" category
		$all = array('category'=>'','title'=>JText::_('ALL_CATEGORIES'),'total'=>$total);
		
		array_unshift($cats, $all);
		
		// An array for storing all the links we make
		$links = array();
		
		// Loop through each category
		foreach ($cats as $cat) 
		{
			// Only show categories that have returned search results
			if ($cat['total'] > 0) {
				// If we have a specific category, prepend it to the search term
				if ($cat['category']) {
					$blob = $cat['category'] .':'. $period;
				} else {
					$blob = $period;
				}
				
				// Is this the active category?
				$a = '';
				if ($cat['category'] == $active) {
					$a = ' class="active"';
					
					$app =& JFactory::getApplication();
					$pathway =& $app->getPathway();
					$pathway->addItem($cat['title'],'index.php?option='.$option.a.'period='. urlencode(stripslashes($blob)));
				}
				
				// Build the HTML
				$l = t.'<li'.$a.'><a href="'.JRoute::_('index.php?option='.$option.a.'period='. urlencode(stripslashes($blob))) .'">' . JText::_($cat['title']) . ' ('.$cat['total'].')</a>';
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
								$blob = $subcat['category'] .':'. $period;
							} else {
								$blob = $period;
							}
							
							// Is this the active category?
							$a = '';
							if ($subcat['category'] == $active) {
								$a = ' class="active"';
								
								$app =& JFactory::getApplication();
								$pathway =& $app->getPathway();
								$pathway->addItem($subcat['title'],'index.php?option='.$option.a.'period='. urlencode(stripslashes($blob)));
							}
							
							// Build the HTML
							$k[] = t.t.t.'<li'.$a.'><a href="'.JRoute::_('index.php?option='.$option.a.'period='. urlencode(stripslashes($blob))) .'">' . JText::_($subcat['title']) . ' ('.$subcat['total'].')</a></li>';
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
		$html .= t.'<input type="hidden" name="category" value="'.$active.'" />'.n;
		
		return $html;
	}

	//-----------
	
	public function form( $period, $option ) 
	{
		// Build some options for the time period <select>
		$periodlist = array();
		$periodlist[] = JHTMLSelect::option('week',JText::_('OPT_WEEK'));
		$periodlist[] = JHTMLSelect::option('month',JText::_('OPT_MONTH'));
		$periodlist[] = JHTMLSelect::option('quarter',JText::_('OPT_QUARTER'));
		$periodlist[] = JHTMLSelect::option('year',JText::_('OPT_YEAR'));
	
		$thisyear = strftime("%Y",time());
		for ($y = $thisyear; $y >= 2002; $y--) 
		{
			if (time() >= strtotime('10/1/'.$y)) {
				$periodlist[] = JHTMLSelect::option($y, JText::_('OPT_FISCAL_YEAR').' '.$y);
			}
		}
		for ($y = $thisyear; $y >= 2002; $y--) 
		{
			if (time() >= strtotime('01/01/'.$y)) {
				$periodlist[] = JHTMLSelect::option('c_'.$y, JText::_('OPT_CALENDAR_YEAR').' '.$y);
			}
		}
		
		$html  = t.'<fieldset>'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('TIME_PERIOD').n;
		$html .= t.t.t.JHTMLSelect::genericlist( $periodlist, 'period', '', 'value', 'text', $period );
		$html .= t.t.'</label>'.n;
		$html .= t.t.'<input type="submit" value="'.JText::_('GO').'" />'.n;
		$html .= t.'</fieldset>'.n;

		return $html;
	}
	
	//-----------
	
	public function jtext($period) 
	{
		switch ($period)
		{
			case 'week':    return JText::_('OPT_WEEK');    break;
			case 'month':   return JText::_('OPT_MONTH');   break;
			case 'quarter': return JText::_('OPT_QUARTER'); break;
			case 'year':    return JText::_('OPT_YEAR');    break;
			default:
				$thisyear = strftime("%Y",time());
				for ($y = $thisyear; $y >= 2002; $y--) 
				{
					if (time() >= strtotime('10/1/'.$y)) {
						if ($y == $period) {
							return JText::_('OPT_FISCAL_YEAR').' '.$y;
						}
					}
				}
				for ($y = $thisyear; $y >= 2002; $y--) 
				{
					if (time() >= strtotime('01/01/'.$y)) {
						if ('c_'.$y == $period) {
							return JText::_('OPT_CALENDAR_YEAR').' '.$y;
						}
					}
					
				}
			break;
		}
	}
	
	//-----------

	public function display( $period, $totals, $results, $cats, $active, $option, $start=0, $limit=0, $total ) 
	{
		$jconfig =& JFactory::getConfig();
		$feed = JRoute::_('index.php?option='.$option.a.'task=feed.rss'.a.'period='.$period);
		if (substr($feed, 0, 4) != 'http') {
			if (substr($feed, 0, 1) != DS) {
				$feed = DS.$feed;
			}
			$feed = $jconfig->getValue('config.live_site').$feed;
		}
		$feed = str_replace('https:://','http://',$feed);
		
		$html  = WhatsnewHtml::div( WhatsnewHtml::hed( 2, JText::_('WHATSNEW').': '.WhatsnewHtml::jtext($period) ), 'full', 'content-header').n;
		//$html .= WhatsnewHtml::div( '<p><a class="feed" href="'.$feed.'">'.JText::_('feed').'</a></p>', '', 'content-header-extra').n;
		
		$html .= '<div class="main section">'.n;
		$html .= '<form action="'.JRoute::_('index.php?option='.$option).'" method="get">'.n;
		$html .= WhatsnewHtml::aside( 
					WhatsnewHtml::form( $period, $option ) .
					WhatsnewHtml::categories( $cats, $active, $period, $total, $option )
				);
		
		$html .= '<div class="subject">'.n;
		
		$juri =& JURI::getInstance();
		$foundresults = false;
		$dopaging = false;
	
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
				
				$num  = $total .' result';
				$num .= ($total > 1) ? 's' : '';
			
				// A function for category specific items that may be needed
				// Check if a function exist (using old style plugins)
				$f = 'plgWhatsnew'.ucfirst($cats[$k]['category']).'Doc';
				if (function_exists($f)) {
					$f();
				}
				// Check if a method exist (using JPlugin style)
				$obj = 'plgWhatsnew'.ucfirst($cats[$k]['category']);
				if (method_exists($obj, 'documents')) {
					$html .= call_user_func( array($obj,'documents') );
				}
				
				$feed = JRoute::_('index.php?option='.$option.a.'task=feed.rss'.a.'period='.urlencode(strToLower($cats[$k]['category']).':'.stripslashes($period)));
				if (substr($feed, 0, 4) != 'http') {
					if (substr($feed, 0, 1) != DS) {
						$feed = DS.$feed;
					}
					$feed = $jconfig->getValue('config.live_site').$feed;
				}
				$feed = str_replace('https:://','http://',$feed);
			
				// Build the category HTML
				$html .= '<h4 class="category-header opened" id="rel-'.$divid.'">'.JText::_($name).' <small>'.$num.' (<a class="feed" href="'.$feed.'">'.JText::_('feed').'</a>)</small></h4>'.n;
				$html .= '<div class="category-wrap" id="'.$divid.'">'.n;
				
				// Does this category have custom output?
				// Check if a function exist (using old style plugins)
				$func = 'plgWhatsnew'.ucfirst($cats[$k]['category']).'Before';
				if (function_exists($func)) {
					$html .= $func( $keyword );
				}
				// Check if a method exist (using JPlugin style)
				$obj = 'plgWhatsnew'.ucfirst($cats[$k]['category']);
				if (method_exists($obj, 'before')) {
					$html .= call_user_func( array($obj,'before'), $keyword );
				}
				
				$html .= '<ol class="search results">'.n;			
				foreach ($category as $row) 
				{
					$row->href = str_replace('&amp;', '&', $row->href);
					$row->href = str_replace('&', '&amp;', $row->href);
					
					// Does this category have a unique output display?
					$func = 'plgWhatsnew'.ucfirst($row->section).'Out';
					// Check if a method exist (using JPlugin style)
					$obj = 'plgWhatsnew'.ucfirst($cats[$k]['category']);
					
					if (function_exists($func)) {
						$html .= $func( $row, $keyword );
					} elseif (method_exists($obj, 'out')) {
						$html .= call_user_func( array($obj,'out'), $row, $period );
					} else {
						if (strstr( $row->href, 'index.php' )) {
							$row->href = JRoute::_($row->href);
						}
						if (substr($row->href,0,1) == '/') {
							$row->href = substr($row->href,1,strlen($row->href));
						}
						
						$html .= t.'<li>'.n;
						$html .= t.t.'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'.n;
						if ($row->text) {
							$html .= t.t.'<p>&#133; '.WhatsnewHtml::cleanText(stripslashes($row->text),200).' &#133;</p>'.n;
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

					$html .= WhatsnewHtml::pagenav( $pageNav, $active, $period, $option );
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
						$html .= ' | <a href="'.JRoute::_( 'index.php?option='.$option.a.'period='.urlencode(strToLower($cats[$k]['category']).':'.stripslashes($period))).'">'.JText::_('SEE_MORE_RESULTS').'</a>';
					}
					$html .= '</p>'.n.n;
				}
				
				// Does this category have custom output?
				// Check if a function exist (using old style plugins)
				$func = 'plgWhatsnew'.ucfirst($cats[$k]['category']).'After';
				if (function_exists($func)) {
					$html .= $func( $keyword );
				}
				// Check if a method exist (using JPlugin style)
				$obj = 'plgWhatsnew'.ucfirst($cats[$k]['category']);
				if (method_exists($obj, 'after')) {
					$html .= call_user_func( array($obj,'after'), $keyword );
				}
				
				$html .= '</div><!-- / #'.$divid.' -->'.n;
			}
			$k++;
		}
		if (!$foundresults) {
			$html .= WhatsnewHtml::warning( JText::_('NO_RESULTS') );
		}
		$html .= '</div><!-- / .subject -->'.n;
		$html .= WhatsnewHtml::div('','clear').n;
		$html .= '</form>'.n;
		$html .= '</div><!-- / .main.section -->'.n;
		
		return $html;
	}

	//-----------
	
	public function cleanText($text, $desclen=300)
	{
		$elipse = false;

		$text = preg_replace( "'<script[^>]*>.*?</script>'si", "", $text );
		$text = str_replace( '{mosimage}', '', $text );
		$text = str_replace( "\n", ' ', $text );
		$text = str_replace( "\r", ' ', $text );
		$text = preg_replace( '/<a\s+.*href=["\']([^"\']+)["\'][^>]*>([^<]*)<\/a>/i','\\2', $text );
		$text = preg_replace( '/<!--.+?-->/', '', $text);
		$text = preg_replace( '/{.+?}/', '', $text);
		$text = strip_tags( $text );
		if (strlen($text) > $desclen) $elipse = true;
		$text = substr( $text, 0, $desclen );
		if ($elipse) $text .= '&#8230;';
		$text = trim($text);
		
		return $text;
	}
}
?>