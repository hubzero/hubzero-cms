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

class KbHtml 
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

	public function div( $txt, $cls='', $id='' )
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

	public function hed( $level, $words, $class='' ) 
	{
		$html  = '<h'.$level;
		$html .= ($class) ? ' class="'.$class.'"' : '';
		$html .= '>'.$words.'</h'.$level.'>'.n;
		return $html;
	}
	
	//-----------
	
	public function xhtml( $text ) 
	{
		$text = stripslashes($text);
		$text = strip_tags($text);
		$text = str_replace('&amp;', '&', $text);
		$text = str_replace('&', '&amp;', $text);
		$text = str_replace('&amp;quot;', '&quot;', $text);
		$text = str_replace('&amp;lt;', '&lt;', $text);
		$text = str_replace('&amp;gt;', '&gt;', $text);
		
		return $text;
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
			$text = $text.' ...';
		}
		
		if ($p) {
			$text = '<p>'.$text.'</p>';
		}

		return $text;
	}
	
	//-----------
	
	public function menu($catid, &$categories, $option)
	{
		$juser =& JFactory::getUser();
		$config = JFactory::getConfig();

		$html = '<ul>'.n;
		if ($catid == 0) {
			$cls = ' class="active"';
		} else {
			$cls = '';
		}
		$html .= t.'<li'.$cls.'><a href="'.JRoute::_('index.php?option='.$option).'">'.JText::_('COMPONENT_LONG_NAME').'</a></li>'.n;
		/*$html .= t.t.'<ul>'.n;*/
		foreach ($categories as $row) 
		{
			if ($catid == $row->id) {
				$cls = ' class="active"';
			} else {
				$cls = '';
			}
			// All other Categories are linkable
			if ($row->access <= $juser->get('aid')) {
				$link = JRoute::_('index.php?option='.$option.a.'section='. $row->alias);
				
				$html .= t.t.t.'<li'.$cls.'><a href="'. $link .'">'. KbHtml::xhtml($row->title) .'</a></li>'.n;
			} else {
				$html .= t.t.t.'<li'.$cls.'>'. $row->title .'<a href="'. JRoute::_( 'index.php?option=com_registration'.a.'task=register' ) .'"> ( '. _E_REGISTERED .' )</a></li>'.n;
			}
		}
		/*$html .= t.t.'</ul>'.n;
		$html .= t.'</li>'.n;
		if (is_dir(JPATH_ROOT.DS.'components'.DS.'com_answers')) {
			$html .= t.'<li><a href="'.JRoute::_('index.php?option=com_answers').'">'.$config->getValue('config.sitename').' '.JText::_('ANSWERS').'</a></li>'.n;
		}
		if (is_dir(JPATH_ROOT.DS.'components'.DS.'com_feedback')) {
			$html .= t.'<li><a href="'.JRoute::_('index.php?option=com_feedback&task=report_problems').'">'.JText::_('REPORT_PROBLEMS').'</a></li>'.n;
		}*/
		$html .= '</ul>'.n;
		
		return $html;
	}
	
	//-----------

	public function browse(&$categories, $articles, $option, $title)
	{
	    $juser =& JFactory::getUser();

		$html  = KbHtml::div( KbHtml::hed(2, $title), 'full', 'content-header').n;
		//$html .= KbHtml::useroptions();
		
		$html .= '<div class="main section withleft">'.n;
		$html .= KbHtml::div(
					KbHtml::menu(0, $categories, $option),
					'aside'
				);
		
		$html .= '<div class="subject">';
		$html .= t.KbHtml::hed(3,JText::_('CATEGORIES'), 'firstheader');
		$html .= t.'<div id="fixwrap">'.n;
		$i = 0;
		foreach ($categories as $row) 
		{
			$i++;
				
			switch ($i) 
			{
				case 1: $cls = 'farleft';  break;
				case 2: $cls = 'middle';   break;
				case 3: $cls = 'farright'; break;
			}
				
			if ($row->access <= $juser->get('aid')) {
				$link = JRoute::_('index.php?option='.$option.a.'section='. $row->alias);
				
				$html .= t.t.'<div class="threecolumn '.$cls.'">'.n;
				$html .= t.t.t.'<p><a class="dir" href="'. $link .'">'. KbHtml::xhtml($row->title) .'</a>';
				$html .= ' ('.$row->numitems.')';
				if ($row->description) {
					$html .= '<br />';
					$html .= KbHtml::xhtml(KbHtml::shortenText($row->description, 100, 0));
				}
				$html .= '</p>'.n;
				$html .= t.t.'</div><!-- / .threecolumn '.$cls.' -->'.n;
				$html .= ($i >= 3) ? '<div class="clear"></div>' : '';
			} else {
				$html .= t.t.'<div class="threecolumn '.$cls.'">'.n;
				$html .= t.t.t.'<p>'. $row->title .'<a href="'. JRoute::_( 'index.php?option=com_registration'.a.'task=register' ) .'"> ( '. _E_REGISTERED .' )</a></p>'.n;
				$html .= t.t.'</div><!-- / .threecolumn '.$cls.' -->'.n;
			}
			
			if ($i >= 3) { 
				$i = 0;
			}
		}
		$html .= t.'</div><!-- / #fixwrap -->'.n;
		
		$html .= KbHtml::div(
					KbHtml::hed(3,JText::_('MOST_POPULAR_ARTICLES')).n.
					KbHtml::articles($articles['top'], $option).n,
					'twocolumn left'
				);
		$html .= KbHtml::div(
					KbHtml::hed(3,JText::_('MOST_RECENT_ARTICLES')).n.
					KbHtml::articles($articles['new'], $option).n,
					'twocolumn right'
				);
		$html .= KbHtml::div('','clear').n;
		
		$html .= t.'</div><!-- / .subject -->'.n;
		$html .= t.'<div class="clear"></div>'.n;
		$html .= '</div><!-- / .main section withleft -->';
		
		return $html;
	}

	//-------------------------------------------------------------
	
	public function category($section, &$category, &$categories, &$subcategories, &$articles, $id, $option, $title)
	{
	    $juser =& JFactory::getUser();

		$html  = KbHtml::div( KbHtml::hed(2,$title), 'full', 'content-header').n;
		//$html .= KbHtml::useroptions();
		
		$html .= '<div class="main section withleft">'.n;
		$html .= t.'<div class="aside">'.n;
		if ($category->section) {
			$html .= KbHtml::menu($category->section, $categories, $option);
		} else {
			$html .= KbHtml::menu($category->id, $categories, $option);
		}
		$html .= t.'</div><!-- / .aside -->'.n;
		$html .= t.'<div class="subject">'.n;
		
		$html .= KbHtml::hed(3,stripslashes($category->title),'firstheader');
		
		//$html .= '<div style="float: left;width: 100%;">'.n;
		if ($category->description) {
			//$html .= t.'<p id="category-description">'.stripslashes($category->description).'</p>'.n;
			if (substr($category->description, 0, 2) != '<p') {
				$html .= t.'<p>';
			}
			$html .= stripslashes($category->description);
			if (substr($category->description, 0, 2) != '<p') {
				$html .= '</p>'.n;
			}
		}
		
		if ($subcategories) {
			$html .= KbHtml::hed(4,JText::_('SUBCATEGORIES'),'');
			$html .= t.'<ul class="categories">'.n;
			foreach ($subcategories as $cat) 
			{
				if ($cat->access <= $juser->get('aid')) {
					$link = 'index.php?option='.$option.a.'section='.$category->alias.a.'category='. $cat->alias;

					$html .= t.t.'<li><a href="'. JRoute::_($link) .'">'. stripslashes($cat->title) .'</a> ('.$cat->numitems.')</li>'.n;
				} else {
					$html .= t.t.'<li>'. stripslashes($cat->title) .'<a href="'. JRoute::_( 'index.php?option=com_registration'.a.'task=register' ) .'"> ( '. _E_REGISTERED .' )</a></li>'.n;
				}
			}
			$html .= t.'</ul>'.n;
			
			if ($articles) {
				$html .= KbHtml::hed(4,JText::_('ARTICLES'),'');
			}
		}
		
		if ($articles) {
			$html .= t.'<ul class="articles">'.n;
			foreach ($articles as $row) 
			{
				if ($row->access <= $juser->get('aid')) {
					if ($section->id) {
						$link = 'index.php?option='.$option.a.'section='.$section->alias;
						$link .= ($row->calias) ? a.'category='.$row->calias : '';
					} else {
						$link = 'index.php?option='.$option.a.'section='.$category->alias;
					}
					$link .= ($row->alias) ? a.'alias='. $row->alias : a.'alias='. $row->id;
				
					$html .= t.t.'<li><a href="'. JRoute::_($link) .'">'. stripslashes($row->title) .'</a></li>'.n;
				} else {
					$html .= t.t.'<li>'. stripslashes($row->title) .'<a href="'. JRoute::_( 'index.php?option=com_registration'.a.'task=register' ) .'"> ( '. _E_REGISTERED .' )</a></li>'.n;
				}
			}
			$html .= t.'</ul>'.n;
		} else {
			$html .= KbHtml::warning( JText::_('NO_ARTICLES_FOR_CATEGORY') );
		}
		
		$html .= t.'</div><!-- / .subject -->'.n;
		$html .= t.'<div class="clear"></div>'.n;
		$html .= '</div><!-- / .main seciton withleft -->'.n;
		
		return $html;
	}

	//-------------------------------------------------------------
	
	public function article(&$content, $section, $category, &$categories, $id, $option, &$juser, $helpful='', $title)
	{
		$html  = KbHtml::div( KbHtml::hed(2,$title), 'full', 'content-header').n;
		//$html .= KbHtml::useroptions();
		
		$html .= '<div class="main section withleft">'.n;
		
		$html .= KbHtml::div(
					KbHtml::menu($content->section, $categories, $option),
					'aside'
				);
		
		$html .= '<div class="subject">'.n;

		if ($category && $category->id != '') {
			$html .= KbHtml::hed(3,stripslashes($category->title),'firstheader');
			$html .= KbHtml::hed(4,stripslashes($content->title),'');
		} else {
			$html .= KbHtml::hed(3,stripslashes($content->title),'firstheader');
		}
		
		if ($content->introtext) {
			$html .= '<p>'.JText::_('DETAILED_QUESTION').':</p>'.n;
			$html .= '<blockquote>'. stripslashes($content->introtext) .'</blockquote>'.n;
		}
				
		if ($content->fulltext) {
			$html .= stripslashes( $content->fulltext );
		}
		
		/*$sef = JRoute::_('index.php?option='.$option.a.'task=article'.a.'id='.$content->id);
		if (strstr($sef,'?')) {
			$sef .= a;
		} else {
			$sef .= '?';
		}*/
		
		$total = $content->helpful + $content->nothelpful;
		
		$html .= t.'<div class="faq">'.n;
		$html .= t.t.'<p class="helpful">'.n;
		$html .= t.t.t.'<span>'.JText::sprintf('FOUND_THIS_HELPFUL', $content->helpful, $total).'</span> '.n;
		if (!$juser->get('guest')) {
			if ($helpful) {
				$html .= t.t.t.'<span>'.JText::_('YOU_FOUND_THIS_ARTICLE').' <strong>'.$helpful.'</strong></span>'.n;
			} else {
				$html .= t.t.t.'<span>'.JText::_('WAS_THIS_HELPFUL').'</span> '.n;
				$html .= t.t.t.'<a class="yesbutton" href="'.JRoute::_('index.php?option='.$option.a.'section='.$section->alias.a.'category='.$category->alias.a.'alias='.$content->alias.a.'helpful=yes').'">'.JText::_('YES').'</a> '.n;
				$html .= t.t.t.'<a class="nobutton" href="'.JRoute::_('index.php?option='.$option.a.'section='.$section->alias.a.'category='.$category->alias.a.'alias='.$content->alias.a.'helpful=no').'">'.JText::_('NO').'</a>'.n;
			}
		}
		$html .= t.t.'</p>'.n;
		$html .= t.'</div><!-- / .faq -->'.n;
		$html .= '</div><!-- / .subject -->'.n;
		$html .= '<div class="clear"></div>'.n;
		$html .= '</div><!-- / .main section withleft -->'.n;
		
		return $html;
	}
	
	//-----------
	
	public function articles( $rows, $option ) 
	{
		$juser =& JFactory::getUser();
		
		if ($rows) {
			$html  = t.'<ul class="articles">'.n;
			foreach ($rows as $row) 
			{
				if ($row->access <= $juser->get('aid')) {
					if (!empty($row->alias))
						$link_on = JRoute::_('index.php?option='.$option.a.'task=article'.a.'section='.$row->section.a.'category='.$row->category.a.'alias='.$row->alias, 1);
					else
						$link_on = JRoute::_('index.php?option='.$option.a.'task=article'.a.'section='.$row->section.a.'category='.$row->category.a.'id='.$row->id, 1);
				} else {
					$link_on = JRoute::_('index.php?option=com_hub'.a.'task=register');
				}
				$html .= t.' <li><a href="'. $link_on .'" title="'.JText::_('READ_ARTICLE').'">'.stripslashes($row->title).'</a></li>'.n;
			}
			$html .= t.'</ul>'.n;
		} else {
			$html  = t.'<p>'.JText::_('NO_ARTICLES').'</p>'.n;
		}
		return $html;
	}

	//-----------

	public function useroptions()
	{
		$html  = '<ul id="useroptions">'.n;
		$html .= t.'<li class="last"><a href="'.JRoute::_('index.php?option=com_support'.a.'task=tickets').'">'.JText::_('MY_SUPPORT_TICKETS').'</a></li>'.n;
		$html .= '</ul>'.n;
		return $html;
	}
}
?>
