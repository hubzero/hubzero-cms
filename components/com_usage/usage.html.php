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

class UsageHtml 
{
	public function error($msg, $tag='p')
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function warning($msg, $tag='p')
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function help( $msg, $tag='p' )
	{
		return '<'.$tag.' class="help">'.$msg.'</'.$tag.'>'.n;
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
		$html .= '>'.n;
		$html .= $txt.n;
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

	public function form( $options, $option, $task='' )
	{
		$html  = '<form method="post" action="'. JRoute::_('index.php?option='.$option.a.'task='.$task) .'">'.n;
		$html .= t.'<fieldset class="filters">'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('USAGE_SHOW_DATA_FOR').': '.n;
		$html .= t.t.t.'<select name="selectedPeriod" id="selectedPeriod">'.n;
		$html .= $options;
		$html .= t.t.t.'</select>'.n;
		$html .= t.t.'</label> <input type="submit" value="'.JText::_('USAGE_VIEW').'" />'.n;
		$html .= t.'</fieldset>'.n;
		$html .= '</form>'.n;
		
		return $html;
	}
	
	//-----------
	
	public function options( &$db, $enddate, $thisyear, $monthsReverse, $func='' )
	{
		$o = '';
		for ($i = $thisyear; $i >= 2004; $i--) 
		{
			foreach ($monthsReverse as $key => $month) 
			{
				$value = $i . '-' . $key;
				if (UsageHelper::$func($db, $value) ) {
					$o .= '<option value="'. $value .'"';
					if ($value == $enddate) {
						$o .= ' selected="selected"';
					}
					$o .= '>'. $month .' '. $i .'</option>'.n;
				}
			}
		}
		return $o;
	}
	
	//-----------
	//  Print Usage Navigation Header

	public function navlinks( $page=0, $option ) 
	{
		$html  = '<div id="sub-menu">'.n;
		$html .= t.'<ul>'.n;
		$html .= t.t.'<li';
		if ($page == 1) {
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_('index.php?option='.$option.a.'task=overview').'"><span>'.JText::_('USAGE_OVERVIEW').'</span></a></li>'.n;
		$html .= t.t.'<li';
		if ($page == 6) {
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_('index.php?option='.$option.a.'task=domainclass').'"><span>'.JText::_('USAGE_DOMAINCLASS').'</span></a></li>'.n;
		$html .= t.t.'<li';
		if ($page == 7) {
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_('index.php?option='.$option.a.'task=region').'"><span>'.JText::_('USAGE_REGION').'</span></a></li>'.n;
		$html .= t.t.'<li';
		if ($page == 2) {
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_('index.php?option='.$option.a.'task=tools').'"><span>'.JText::_('USAGE_TOOLS').'</span></a></li>'.n;
		$html .= t.t.'<li';
		if ($page == 4) {
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_('index.php?option='.$option.a.'task=domains').'"><span>'.JText::_('USAGE_DOMAINS').'</span></a></li>'.n;
		$html .= t.t.'<li';
		if ($page == 5) {
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_('index.php?option='.$option.a.'task=partners').'"><span>'.JText::_('USAGE_PARTNERS').'</span></a></li>'.n;
		$html .= t.'</ul>'.n;
		$html .= '</div>'.n;
		
		return $html;
	}
	
	//-----------
	
	public function valformat($value, $format) 
	{
		if ($format == 1) {
			return(number_format($value));
		} elseif ($format == 2 || $format == 3) {
			if ($format == 2) {
				$min = round($value / 60);
			} else {
				$min = floor($value / 60);
				$sec = $value - ($min * 60);
			}
			$hr = floor($min / 60);
			$min -= ($hr * 60);
			$day = floor($hr / 24);
			$hr -= ($day * 24);
			if ($day == 1) {
				$day = "1 ".JText::_('USAGE_DAY').", ";
			} elseif ($day > 1) {
				$day = number_format($day) . ' '.JText::_('USAGE_DAYS').', ';
			} else {
				$day = "";
			}
			if ($format == 2) {
				return(sprintf("%s%d:%02d", $day, $hr, $min));
			} else {
				return(sprintf("%s%d:%02d:%02d", $day, $hr, $min, $sec));
			}
		} else {
			return($value);
		}
	}
	
	//-----------
	
	public function view( $option, $cats, $sections, $tab, $title, $no_html=1 ) 
	{
		$html = '';
		if (!$no_html) {
			$html .= UsageHtml::div( UsageHtml::hed(2, $title), 'full', 'content-header' ).n;
			/*$html .= '<div id="content-header-extra">'.n;
			$html .= t.'<ul id="useroptions">'.n;
			$html .= t.t.'<li class="last"><a class="group" href="'.JRoute::_('index.php?option='.$option).'">'.JText::_('GROUPS_ALL_GROUPS').'</a></li>'.n;
			$html .= t.'</ul>'.n;
			$html .= '</div><!-- / #content-header-extra -->'.n;*/
			$html .= UsageHtml::tabs( $option, $cats, $tab );
		}
		$html .= UsageHtml::sections( $sections, $cats, $tab, 'hide', 'main' );
		
		return $html;
	}
	
	//-----------
	
	public function tabs( $option, $cats, $active='overview' ) 
	{
		$html  = '<div id="sub-menu">'.n;
		$html .= t.'<ul>'.n;
		$i = 1;
		$cs = array();
		foreach ($cats as $cat)
		{
			$name = key($cat);
			if ($cat[$name] != '') {
				$html .= t.t.'<li id="sm-'.$i.'"';
				$html .= (strtolower($name) == $active) ? ' class="active"' : '';
				$html .= '><a class="tab" rel="'.$name.'" href="'.JRoute::_('index.php?option='.$option.a.'task='.$name).'"><span>'.$cat[$name].'</span></a></li>'.n;
				$i++;
				$cs[] = $name;
			}
		}
		$html .= t.'</ul>'.n;
		$html .= t.'<div class="clear"></div>'.n;
		$html .= '</div><!-- / #sub-menu -->'.n;
		
		if (!in_array($active, $cs)) {
			return '';
		}
		
		return $html;
	}
	
	//-----------

	public function sections( $sections, $cats, $active='overview', $h, $c ) 
	{
		$html = '';
		
		if (!$sections) {
			return $html;
		}
		
		$k = 0;
		foreach ($sections as $section) 
		{
			if ($section != '') {
				$cls  = ($c) ? $c.' ' : '';
				if (key($cats[$k]) != $active) {
					$cls .= ($h) ? $h.' ' : '';
				}
				$html .= UsageHtml::div( $section, $cls.'section', 'statistics' );
			}
			$k++;
		}
		
		return $html;
	}
}
?>