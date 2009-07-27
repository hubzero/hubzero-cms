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

class TagsHtml 
{
	public function encode_html($str, $quotes=1)
	{
		$str = TagsHtml::ampersands($str);
		
		$a = array(
			//'&' => '&#38;',
			'<' => '&#60;',
			'>' => '&#62;',
		);
		if ($quotes) $a = $a + array(
			"'" => '&#39;',
			'"' => '&#34;',
		);

		return strtr($str, $a);
	}
	
	//-----------
	
	public function ampersands( $str ) 
	{
		$str = stripslashes($str);
		$str = str_replace('&#','*-*', $str);
		$str = str_replace('&amp;','&',$str);
		$str = str_replace('&','&amp;',$str);
		$str = str_replace('*-*','&#', $str);
		return $str;
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

	public function passed( $msg, $tag='p' )
	{
		return '<'.$tag.' class="passed">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
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
	
	public function aside($txt, $id='')
	{
		return TagsHtml::div($txt, 'aside', $id);
	}
	
	//-----------
	
	public function subject($txt, $id='')
	{
		return TagsHtml::div($txt, 'subject', $id);
	}

	//-----------

	public function shortenText($text, $chars=300, $p=1) 
	{
		$text = strip_tags($text);
		$text = trim($text);

		if (strlen($text) > $chars) {
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
			$text = $text.' &#8230;';
		}
		
		if ($text == '') {
			$text = '&#8230;';
		}
		
		if ($p) {
			$text = '<p>'.$text.'</p>';
		}

		return $text;
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
	
	//-----------
	
	public function form( $sort, $option ) 
	{
		// Build some options for the time period <select>
		$sorts = array();
		$sorts[] = JHTMLSelect::option('',JText::_('OPT_SELECT'));
		//$sorts[] = JHTMLSelect::option('ranking',JText::_('OPT_RANKING'));
		$sorts[] = JHTMLSelect::option('title',JText::_('OPT_TITLE'));
		$sorts[] = JHTMLSelect::option('date',JText::_('OPT_DATE'));
		
		$html  = t.'<fieldset>'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('SORT_BY').n;
		$html .= t.t.t.JHTMLSelect::genericlist( $sorts, 'sort', '', 'value', 'text', $sort );
		$html .= t.t.'</label>'.n;
		$html .= t.t.'<input type="submit" value="'.JText::_('GO').'" />'.n;
		$html .= t.'</fieldset>'.n;

		return $html;
	}
	
	//-----------

	public function categories( $cats, $active, $total, $option, $tag ) 
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
				$blob = '';
				if ($cat['category']) {
					$blob = $cat['category'];
				}
				
				$url  = 'index.php?option='.$option.a.'tag='.$tag;
				$url .= ($blob) ? a.'area='. stripslashes($blob) : '';
				$sef = JRoute::_($url);
				$sef = str_replace('%20','+',$sef);
				
				// Is this the active category?
				$a = '';
				if ($cat['category'] == $active) {
					$a = ' class="active"';
					
					$app =& JFactory::getApplication();
					$pathway =& $app->getPathway();
					$pathway->addItem($cat['title'],'index.php?option='.$option.a.'tag='.$tag.a.'area='. stripslashes($blob));
				}
				
				// Build the HTML
				$l = t.'<li'.$a.'><a href="'.$sef.'">' . $cat['title'] . ' ('.$cat['total'].')</a>';
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
							$blob = '';
							if ($subcat['category']) {
								$blob = $subcat['category'];
							}
							
							// Is this the active category?
							$a = '';
							if ($subcat['category'] == $active) {
								$a = ' class="active"';
								
								$app =& JFactory::getApplication();
								$pathway =& $app->getPathway();
								$pathway->addItem($subcat['title'],'index.php?option='.$option.a.'tag='.$tag.a.'area='. stripslashes($blob));
							}
							
							// Build the HTML
							$sef = JRoute::_('index.php?option='.$option.a.'tag='.$tag.a.'area='. stripslashes($blob));
							$sef = str_replace('%20','+',$sef);
							$k[] = t.t.t.'<li'.$a.'><a href="'.$sef.'">' . $subcat['title'] . ' ('.$subcat['total'].')</a></li>';
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
		$html .= t.'<input type="hidden" name="area" value="'.$active.'" />'.n;

		return $html;
	}

	//-----------

	public function view( $title, $authorized, $tags, $option, $totals, $results, $cats, $active, $limitstart, $limit, $sort, $total, $related ) 
	{
		if (count($tags) == 1) {
			$tagstring = $tags[0]->tag;
			
			if ($authorized) {
				$title .= ' <a class="edit button" href="index.php?option='.$option.a.'task=edit'.a.'id='.$tags[0]->id.'">'.JText::_('EDIT_TAG').'</a>';
			}
		} else {
			$tagstring = array();
			foreach ($tags as $tag) 
			{
				$tagstring[] = $tag->tag;
			}
			$tagstring = implode('+',$tagstring);
		}
		
		$sef = JRoute::_('index.php?option='.$option.a.'tag='.$tagstring);
		
		$tagnav  = '<ul id="tagscopenav">';
		$tagnav .= t.'<li class="scope"><a class="currscope" href="'.JRoute::_('index.php?option='.$option).'">'.JText::_('TAGS').'</a></li>'.n;
		$tagnav .= t.'<li class="tags">'.n;
		$tagnav .= t.t.'<ul>'.n;
		for ($i=0, $n=count( $tags ); $i < $n; $i++) 
		{
			$tsarr = array();
			foreach ($tags as $tag) 
			{
				if ($tag->tag != $tags[$i]->tag) {
					$tsarr[] = $tag->tag;
				}
			}
			$tsarr = implode('+',$tsarr);
			
			$s  = 'index.php?option='.$option;
			$s .= ($tsarr) ? a.'tag='.$tsarr : '';
			
			$tagnav .= t.t.'<li class="tag">'.n;
			$tagnav .= t.t.t.'<a class="onlytag" href="'.JRoute::_('index.php?option='.$option.a.'tag='.$tags[$i]->tag).'">'.$tags[$i]->raw_tag.'</a>'.n;
			$tagnav .= t.t.t.'<a class="removetag" href="'.JRoute::_($s).'" title="Remove tag"><span>[x]</span></a>'.n;
			$tagnav .= t.t.'</li>'.n;
		}
		$tagnav .= t.t.'</ul>'.n;
		$tagnav .= t.'</li>'.n;
		$tagnav .= t.'<li class="addtag"><input type="text" name="addtag" size="15" /> <input type="submit" value="'.JText::_('Add tag').'" /></li>'.n;
		$tagnav .= '</ul>'.n;
		$tagnav = str_replace('%20','+',$tagnav);
		
		$html  = '<form method="get" action="'.$sef.'">'.n;
		//$html .= TagsHtml::div( TagsHtml::hed( 2, $title ).$tagnav, '', 'content-header' ).n;
		$html .= TagsHtml::div( TagsHtml::hed( 2, $title ), '', 'content-header' ).n;
		$html .= '<div id="content-header-extra">'.n;
		$html .= t.'<ul id="useroptions">'.n;
		$html .= t.t.'<li class="last"><a class="tag" href="'.JRoute::_('index.php?option='.$option).'">'.JText::_('MORE_TAGS').'</a></li>'.n;
		$html .= t.'</ul>'.n;
		$html .= '</div><!-- / #content-header-extra -->'.n;
		$html .= '<div class="main section">'.n;
		if (count($tags) == 1) {
			$tagobj = $tags[0];
			if ($tagobj->description != '') {
				$tagobj->description = TagsHtml::ampersands($tagobj->description);

				$html .= TagsHtml::hed( 3, JText::_('DESCRIPTION') ).n;
				$html .= TagsHtml::div( $tagobj->description.TagsHtml::div( '', 'clear'), 'tag-description' );
			}
		}
		if ($related) {
			$rl  = '<div class="block"><h3>'.JText::_('Items tagged with "'.$tags[0]->raw_tag.'" are commonly tagged with:').'</h3>'.n;
			$rl .= '<ol class="tags">'.n;
			foreach ($related as $rel)
			{
				$class = ($rel->admin == 1) ? ' class="admin"' : '';

				$rel->raw_tag = str_replace( '&amp;', '&', $rel->raw_tag );
				$rel->raw_tag = str_replace( '&', '&amp;', $rel->raw_tag );

				$rl .= t.'<li'.$class.'><a href="'.JRoute::_('index.php?option='.$option.a.'tag='.$rel->tag).'">'.$rel->raw_tag.'</a></li>'.n;
			}
			$rl .= '</ol></div>'.n;
		} else {
			$rl = '';
		}
		$html .= $rl;
		$html .= TagsHtml::hed( 3, JText::_('RESULTS') ).n;
		$html .= TagsHtml::div( 
					TagsHtml::form( $sort, $option ) .
					TagsHtml::categories( $cats, $active, $total, $option, $tagstring ), 
					'aside'
				);
		
		$html .= '<div class="subject">'.n;
		
		$juri =& JURI::getInstance();
		$foundresults = false;
		$dopaging = false;
		
		$jconfig =& JFactory::getConfig();
	
		$k = 0;
		foreach ($results as $category)
		{
			$amt = count($category);
			
			if ($amt > 0) {
				$foundresults = true;
				
				$name  = $cats[$k]['title'];
				$total = $cats[$k]['total'];
				$divid = 'search'.$cats[$k]['category'];
				
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
				
				$num  = $total .' ';
				$num .= ($total > 1) ? JText::_('RESULTS') : JText::_('RESULT');
			
				// A function for category specific items that may be needed
				$f = 'plgTags'.ucfirst($cats[$k]['category']).'Doc';
				if (function_exists($f)) {
					$f();
				}
				// Check if a method exist (using JPlugin style)
				$obj = 'plgTags'.ucfirst($cats[$k]['category']);
				if (method_exists($obj, 'documents')) {
					$html .= call_user_func( array($obj,'documents') );
				}
			
				$feed = JRoute::_('index.php?option='.$option.a.'task=feed.rss'.a.'tag='.$tagstring.a.'area='.$cats[$k]['category']);
				if (substr($feed, 0, 4) != 'http') {
					if (substr($feed, 0, 1) != DS) {
						$feed = DS.$feed;
					}
					$feed = $jconfig->getValue('config.live_site').$feed;
				}
				$feed = str_replace('https:://','http://',$feed);
			
				// Build the category HTML
				$html .= '<h4 class="category-header opened" id="rel-'.$divid.'">'.$name.' <small>'.$num.' (<a class="feed" href="'.$feed.'">'.JText::_('feed').'</a>)</small></h4>'.n;
				$html .= '<div class="category-wrap" id="'.$divid.'">'.n;
				$html .= '<ol class="search results">'.n;			
				foreach ($category as $row) 
				{
					$row->href = str_replace('&amp;', '&', $row->href);
					$row->href = str_replace('&', '&amp;', $row->href);
					
					// Does this category have a unique output display?
					$func = 'plgTags'.ucfirst($row->section).'Out';
					// Check if a method exist (using JPlugin style)
					$obj = 'plgTags'.ucfirst($cats[$k]['category']);
					
					if (function_exists($func)) {
						$html .= $func( $row );
					} elseif (method_exists($obj, 'out')) {
						$html .= call_user_func( array($obj,'out'), $row );
					} else {
						if (strstr( $row->href, 'index.php' )) {
							$row->href = JRoute::_($row->href);
						}
						if (substr($row->href,0,1) == '/') {
							$row->href = substr($row->href,1,strlen($row->href));
						}
						
						$html .= t.'<li>'.n;
						$html .= t.t.'<p class="title"><a href="'.$row->href.'">'.TagsHtml::encode_html($row->title).'</a></p>'.n;
						if ($row->text) {
							$row->text = strip_tags($row->text);
							$html .= t.t.TagsHtml::shortenText(TagsHtml::encode_html($row->text), 200).n;
						}
						$html .= t.t.'<p class="href">'.$juri->base().$row->href.'</p>'.n;
						$html .= t.'</li>'.n;
					}
				}
				$html .= '</ol>'.n;
				// Initiate paging if we we're displaying an active category
				if ($dopaging) {
					jimport('joomla.html.pagination');
					$pageNav = new JPagination( $total, $limitstart, $limit );

					//$html .= $pageNav->getListFooter();
					$pf = $pageNav->getListFooter();
					
					$nm = str_replace('com_','',$option);

					$pf = str_replace($nm.'/?',$nm.'/'.$tagstring.'/'.$active.'/?',$pf);
					$pf = str_replace('%20','+',$pf);
					$html .= $pf;
				} else {
					$html .= '<p class="moreresults">'.JText::sprintf('TOTAL_RESULTS_FOUND',$amt);
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
						$sef = JRoute::_('index.php?option='.$option.a.'tag='.$tagstring.a.'area='. urlencode(stripslashes($cats[$k]['category'])));
						$html .= ' | <a href="'.$sef.'">'.JText::_('SEE_MORE_RESULTS').'</a>';
					}
				}
				$html .= '</p>'.n.n;
				$html .= '</div><!-- / #'.$divid.' -->'.n;
			}
			$k++;
		}
		if (!$foundresults) {
			$html .= TagsHtml::warning( JText::_('NO_RESULTS') );
		}
		$html .= '</div><!-- / .subject -->'.n;
		$html .= TagsHtml::div('','clear').n;
		$html .= '</div><!-- / .main.section -->'.n;
		$html .= '</form>'.n;

		return $html; //TagsHtml::div($html, 'main section', 'tag-details');
	}
	
	//-----------

	public function browse( &$rows, &$pageNav, $option, $total, $filters, $authorized ) 
	{
		$html  = TagsHtml::div( TagsHtml::hed(2,JText::_('TAGS').': '.JText::_('BROWSE')), 'full', 'content-header' ).n;
		$html .= '<form action="'.JRoute::_('index.php?option='.$option.a.'task=browse').'" method="post">'.n;
		$html .= '<div class="main section">'.n;
		$html .= t.'<div class="aside">'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('SEARCH_TAGS').':'.n;
		$html .= t.t.t.'<input type="text" name="search" value="'. $filters['search'] .'" />'.n;
		$html .= t.t.'</label>'.n;
		$html .= t.t.'<input type="submit" value="'.JText::_('GO').'" />'.n;
		$html .= t.'</fieldset>'.n;
		$html .= t.'<p class="help"><strong>'.JText::_('TAGS_WHATS_AN_ALIAS').'</strong><br />'.JText::_('TAGS_ALIAS_EXPLANATION').'</p>'.n;
		$html .= t.'</div><!-- / .aside -->'.n;
		$html .= t.'<div class="subject">'.n;
		$html .= t.'<table id="taglist" summary="'.JText::_('TABLE_SUMMARY').'">'.n;
		$html .= t.' <thead>'.n;
		$html .= t.'  <tr>'.n;
		if ($authorized) {
			$html .= t.'   <th colspan="2">'.JText::_('COL_ACTION').'</th>'.n;
		}
		$html .= t.'   <th>'.JText::_('TAG').'</th>'.n;
		//$html .= t.'   <th>'.JText::_('TAG').'</th>'.n;
		$html .= t.'   <th>'.JText::_('COL_ALIAS').'</th>'.n;
		$html .= t.'   <th>'.JText::_('COL_NUMBER_TAGGED').'</th>'.n;
		$html .= t.'  </tr>'.n;
		$html .= t.' </thead>'.n;
		//if ($total > count($rows)) {
			/*$html .= t.' <tfoot>'.n;
			$html .= t.'  <tr>'.n;
			$html .= t.'   <td colspan="5">'.$pageNav->getListFooter().'</td>'.n;
			$html .= t.'  </tr>'.n;
			$html .= t.' </foot>'.n;*/
		//}
		$html .= t.' <tbody>'.n;

		$database =& JFactory::getDBO();
		$to = new TagsObject( $database );

		$k = 0;
		$cls = 'even';
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row = &$rows[$i];
			$now = date( "Y-m-d H:i:s" );
			
			$total = $to->getCount( $row->id );
			
			$cls = ($cls == 'even') ? 'odd' : 'even';
			
			$html .= t.'  <tr class="'.$cls.'">'.n;
			if ($authorized) {
				$html .= t.'   <td><a class="delete" href="index.php?option='.$option.a.'task=delete'.a.'id[]='.$row->id.'">'.JText::_('DELETE_TAG').'</a></td>'.n;
				$html .= t.'   <td><a class="edit" href="index.php?option='.$option.a.'task=edit'.a.'id='.$row->id.'" title="'.JText::_('EDIT_TAG').' &quot;'.stripslashes($row->raw_tag).'&quot;">'. JText::_('EDIT') .'</a></td>'.n;
			}
			$html .= t.'   <td><a href="'.JRoute::_('index.php?option='.$option.a.'tag='.$row->tag).'">'. stripslashes($row->raw_tag) .'</a></td>'.n;
			//$html .= t.'   <td><a href="'.JRoute::_('index.php?option='.$option.a.'tag='.$row->tag).'">'. stripslashes($row->tag) .'</a></td>'.n;
			$html .= t.'   <td>'. $row->alias .'</td>'.n;
			$html .= t.'   <td>'. $total .'</td>'.n;
			$html .= t.'  </tr>'.n;

			$k = 1 - $k;
		}

		$html .= t.' </tbody>'.n;
		$html .= t.'</table>'.n;
		
		$pn = $pageNav->getListFooter();
		$pn = str_replace('/?','/?task=browse&amp;',$pn);
		$pn = str_replace('task=browse&amp;task=browse','task=browse',$pn);
		$pn = str_replace('&amp;&amp;','&amp;',$pn);
		
		$html .= $pn;
		$html .= t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
		$html .= t.'<input type="hidden" name="task" value="browse" />'.n;
		$html .= t.'</div><div class="clear"></div>'.n;
		$html .= '</form>'.n;
		$html .= '</div><!-- / .main section -->'.n;
		
		return $html;
	}
	
	//-----------
	
	public function cloud( $option, $tags, $showsizes, $min_font_size, $max_font_size, $min_qty, $step, $tagpile, $areas, $newtags )
	{
		$html  = '<div class="block">'.n;
		$html .= TagsHtml::hed(3,JText::_('Recently Used Tags'));
		if ($newtags) {
			$html .= '<ol class="tags">'.n;
			$tl = array();
			foreach ($newtags as $newtag)
			{
				$class = ($newtag->admin == 1) ? ' class="admin"' : '';

				$newtag->raw_tag = str_replace( '&amp;', '&', $newtag->raw_tag );
				$newtag->raw_tag = str_replace( '&', '&amp;', $newtag->raw_tag );

				if ($showsizes == 1) {
					$size = $min_font_size + ($newtag->tcount - $min_qty) * $step;
					$size = ($size > $max_font_size) ? $max_font_size : $size;
					$tl[$newtag->tag] = t.'<li'.$class.'><span style="font-size: '. round($size,1) .'em"><a href="'.JRoute::_('index.php?option='.$option.a.'tag='.$newtag->tag).'">'.stripslashes($newtag->raw_tag).'</a></span></li>'.n;
				} else {
					$tl[$newtag->tag] = t.'<li'.$class.'><a href="'.JRoute::_('index.php?option='.$option.a.'tag='.$newtag->tag).'">'.stripslashes($newtag->raw_tag).'</a></li>'.n;
				}
			}
			ksort($tl);
			$html .= implode('',$tl);
			$html .= '</ol>'.n;
			$html .= '<p class="moretags"><a href="'.JRoute::_('index.php?option='.$option.a.'task=browse').'">'.JText::_('Browse all tags &rsaquo;').'</a></p>'.n;
			//$html .= '<div class="clear"></div><!-- / .clear -->'.n;
		} else {
			$html  = TagsHtml::warning( JText::_('NO_TAGS') ).n;
		}
		$html .= '</div><!-- / .block -->'.n;
		
		$html .= '<div class="block">'.n;
		$html .= TagsHtml::hed(3,JText::_('Top 100 Tags'));
		if ($tags) {
			$html .= '<ol class="tags">'.n;
			$tll = array();
			foreach ($tags as $tag)
			{
				$class = ($tag->admin == 1) ? ' class="admin"' : '';

				$tag->raw_tag = str_replace( '&amp;', '&', $tag->raw_tag );
				$tag->raw_tag = str_replace( '&', '&amp;', $tag->raw_tag );

				if ($showsizes == 1) {
					$size = $min_font_size + ($tag->tcount - $min_qty) * $step;
					$size = ($size > $max_font_size) ? $max_font_size : $size;
					$tll[$tag->tag] = t.'<li'.$class.'><span style="font-size: '. round($size,1) .'em"><a href="'.JRoute::_('index.php?option='.$option.a.'tag='.$tag->tag).'">'.stripslashes($tag->raw_tag).'</a></span></li>'.n;
				} else {
					$tll[$tag->tag] = t.'<li'.$class.'><a href="'.JRoute::_('index.php?option='.$option.a.'tag='.$tag->tag).'">'.stripslashes($tag->raw_tag).'</a></li>'.n;
				}
			}
			ksort($tll);
			$html .= implode('',$tll);
			$html .= '</ol>'.n;
			$html .= '<p class="moretags"><a href="'.JRoute::_('index.php?option='.$option.a.'task=browse').'">'.JText::_('Browse all tags &rsaquo;').'</a></p>'.n;
			//$html .= '<div class="clear"></div><!-- / .clear -->'.n;
		} else {
			$html  = TagsHtml::warning( JText::_('NO_TAGS') ).n;
		}
		$html .= '</div><!-- / .block -->'.n;
		/*foreach ($tagpile as $cat=>$pile) 
		{
			if (count($pile) > 0) {				
				$divid = 'popular-'.$cat;
				
				$t = array();
				foreach ($pile as $tag)
				{
					if ($tag->tcount > 0) {
						$class = ($tag->admin == 1) ? ' class="admin"' : '';

						$tag->raw_tag = str_replace( '&amp;', '&', $tag->raw_tag );
						$tag->raw_tag = str_replace( '&', '&amp;', $tag->raw_tag );

						if ($showsizes == 1) {
							$size = $min_font_size + ($tag->tcount - $min_qty) * $step;
							$size = ($size > $max_font_size) ? $max_font_size : $size;
							$t[] = t.'<li'.$class.'><span style="font-size: '. round($size,1) .'em"><a href="'.JRoute::_('index.php?option='.$option.a.'tag='.$tag->tag).'">'.$tag->raw_tag.'</a></span></li>'.n;
						} else {
							$t[] = t.'<li'.$class.'><a href="'.JRoute::_('index.php?option='.$option.a.'tag='.$tag->tag).'">'.$tag->raw_tag.'</a></li>'.n;
						}
					}
				}
				if (count($t) > 0) {
					$html .= '<h4 class="category-header opened">Popular Tags in "'.$areas[$cat].'"</h4>'.n;
					$html .= '<div class="category-wrap" id="'.$divid.'">'.n;
					$html .= '<ol class="tags">'.n;
					$html .= implode('',$t);
					$html .= '</ol>'.n;
					$html .= '</div><!-- / #'.$divid.' -->'.n;
				}
			}
		}*/
		
		$tagnav  = '<ul id="tagscopenav">';
		$tagnav .= t.'<li class="scope"><a class="currscope" href="'.JRoute::_('index.php?option='.$option).'">'.JText::_('TAGS').'</a></li>'.n;
		$tagnav .= t.'<li class="tags">'.n;
		$tagnav .= t.'</li>'.n;
		$tagnav .= t.'<li class="addtag"><input type="text" name="addtag" size="15" /> <input type="submit" value="'.JText::_('Add tag').'" /></li>'.n;
		$tagnav .= '</ul>'.n;
		
		
		$a  = t.'<fieldset>'.n;
		$a .= t.t.'<label>'.n;
		$a .= t.t.t.JText::_('SEARCH_TAGS').':'.n;
		$a .= t.t.t.'<input type="text" name="search" value="" />'.n;
		$a .= t.t.'</label>'.n;
		$a .= t.t.'<input type="submit" value="'.JText::_('GO').'" />'.n;
		//$a .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
		$a .= t.t.'<input type="hidden" name="task" value="browse" />'.n;
		$a .= t.'</fieldset>'.n;
		$a .= t.'<p class="help"><strong>'.JText::_('WHAT_ARE_TAGS').'</strong><br />'.JText::_('TAGS_ARE').'</p>'.n;
		
		$o  = TagsHtml::aside( $a );
		$o .= TagsHtml::subject( $html );
		
		$out  = '<form action="'.JRoute::_('index.php?option='.$option).'" method="get">'.n;
		$out .= TagsHtml::div( TagsHtml::hed(2,JText::_('TAGS')), 'full', 'content-header' ).n;
		$out .= TagsHtml::div( $o, 'main section' );
		//$out .= $o;
		$out .= TagsHtml::div( '', 'clear' );
		//$out .= '<input name="option" value="'.$option.'" />'.n;
		//$out .= '<input type="hidden" name="task" value="view" />'.n;
		$out .= '</form>'.n;
		
		return $out;
	}
	
	//-----------

	public function edit( $task, $tag, $option, $err ) 
	{
		$html  = TagsHtml::div( TagsHtml::hed(2,JText::_('TAGS').': '.$task), 'full', 'content-header' ).n;
		
		if ($err) {
			$html .= TagsHtml::error( $err );
		}
		
		$html .= '<div class="main section">'.n;
		$html .= '<form action="index.php" method="post" id="hubForm">'.n;
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p>'.JText::_('NORMALIZED_TAG_EXPLANATION').'</p>'.n;
		$html .= t.'</div>'.n;
		$html .= t.'<fieldset>'.n;
		$html .= TagsHtml::hed(3,JText::_('DETAILS'));
		
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('TAG').':'.n;
		$html .= t.t.t.'<input type="text" name="raw_tag" value="'. htmlentities(stripslashes($tag->raw_tag),ENT_COMPAT,'UTF-8') .'" size="38" />'.n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('COL_ALIAS').':'.n;
		$html .= t.t.t.'<input type="text" name="alias" value="'. htmlentities(stripslashes($tag->alias),ENT_COMPAT,'UTF-8') .'" size="38" />'.n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'<input class="option" type="checkbox" name="minor_edit" value="1" /> <strong>'.JText::_('ADMINISTRATION').'</strong>'.n;
		$html .= t.t.'</label>'.n;
		$html .= t.t.'<p class="hint">'.JText::_('ADMINISTRATION_EXPLANATION').'</p>'.n;
			
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('DESCRIPTION').':'.n;
		$html .= t.t.t.'<textarea name="description" rows="10" cols="35">'. stripslashes($tag->description) .'</textarea>'.n;
		$html .= t.t.'</label>'.n;
		
		//$html .= t.t.'<input type="hidden" name="created_by" value="'. $tag->created_by .'" />'.n;
		//$html .= t.t.'<input type="hidden" name="created" value="'. $tag->created .'" />'.n;
		$html .= t.t.'<input type="hidden" name="tag" value="'. $tag->tag .'" />'.n;
		$html .= t.t.'<input type="hidden" name="id" value="'. $tag->id .'" />'.n;
		$html .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
		$html .= t.t.'<input type="hidden" name="task" value="save" />'.n;

		$html .= t.'</fieldset>'.n;
		$html .= t.'<p class="submit"><input type="submit" value="'.JText::_('SUBMIT').'" /></p>'.n;
		$html .= '</form>'.n;
		$html .= '</div><!-- / .main section -->'.n;
		
		return $html;
	}
}
?>
