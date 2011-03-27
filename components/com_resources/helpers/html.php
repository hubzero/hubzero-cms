<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'helpers'.DS.'tags.php' );

if (!defined('n')) {
	define('t',"\t");
	define('n',"\n");
	define('r',"\r");
	define('br','<br />');
	define('sp','&#160;');
	define('a','&amp;');
}

class ResourcesHtml 
{
	//-------------------------------------------------------------
	// Misc HTML
	//-------------------------------------------------------------

	public function encode_html($str, $quotes=1)
	{
		$str = stripslashes($str);
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

	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'."\n";
	}
	
	//-----------
	
	public function warning( $msg, $tag='p' )
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'."\n";
	}
	
	//-----------

	public function passed( $msg, $tag='p' )
	{
		return '<'.$tag.' class="passed">'.$msg.'</'.$tag.'>'."\n";
	}

	//-----------

	public function archive( $msg, $tag='p' )
	{
		return '<'.$tag.' class="archive">'.$msg.'</'.$tag.'>'."\n";
	}
	
	//-----------
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}

	//-----------

	public function div($txt, $cls='', $id='')
	{
		$html  = '<div';
		$html .= ($cls) ? ' class="'.$cls.'"' : '';
		$html .= ($id) ? ' id="'.$id.'"' : '';
		$html .= '>'."\n";
		$html .= $txt."\n";
		$html .= '</div><!-- / ';
		if ($id) {
			$html .= '#'.$id;
		}
		if ($cls) {
			$html .= '.'.$cls;
		}
		$html .= ' -->'."\n";
		return $html;
	}
	
	//-----------
	
	public function aside($txt, $id='')
	{
		return ResourcesHtml::div($txt, 'aside', $id);
	}
	
	//-----------
	
	public function subject($txt, $id='')
	{
		return ResourcesHtml::div($txt, 'subject', $id);
	}

	//-----------
	
	public function hed($level, $txt)
	{
		return '<h'.$level.'>'.$txt.'</h'.$level.'>';
	}

	//-----------

	public function formSelect($name, $array, $value, $class='')
	{
		$out  = '<select name="'.$name.'" id="'.$name.'"';
		$out .= ($class) ? ' class="'.$class.'">'."\n" : '>'."\n";
		foreach ($array as $avalue => $alabel) 
		{
			$selected = ($avalue == $value || $alabel == $value)
					  ? ' selected="selected"'
					  : '';
			$out .= ' <option value="'.$avalue.'"'.$selected.'>'.$alabel.'</option>'."\n";
		}
		$out .= '</select>'."\n";
		return $out;
	}

	//-----------

	public function tableRow($h, $c='', $s='')
	{
		$html  = t.'  <tr>'.n;
		$html .= t.'   <th>'.$h.'</th>'.n;
		$html .= t.'   <td>';
		$html .= ($c) ? $c : '&nbsp;';
		$html .= '</td>'.n;
		if($s) {
			$html .= t.'   <td class="secondcol">';
			$html .= $s;
			$html .= '</td>'.n;
		}
		$html .= t.'  </tr>'.n;
		
		return $html;
	}
	
	//-------------------------------------------------------------
	// Misc views parts
	//-------------------------------------------------------------

	public function adminIcon( $id, $published, $show_edit, $created_by=0, $type, $r_type ) 
	{
	    $juser =& JFactory::getUser();

		if ($published < 0) {
			return;
		}

		if (!$show_edit) {
			return;
		}
		
		switch ($type)
		{
			case 'edit':
				if ($r_type == '7') {
					$link = 'index.php?option=com_contribtool&task=start&step=1&rid='. $id;
				} else {
					$link = JRoute::_('index.php?option=com_contribute&step=1&id='. $id);
				}
				$txt = JText::_('EDIT');
				break;
			default:
				$txt  = '';
				$link = '';
				break;
		}
		
		return ' <a class="edit button" href="'. $link .'" title="'. $txt .'">'. $txt .'</a>';
	}

	//-------------------------------------------------------------
	// Main view parts
	//-------------------------------------------------------------
	
	public function parseTag($text, $tag)
	{
		preg_match("#<nb:".$tag.">(.*?)</nb:".$tag.">#s", $text, $matches);
		if (count($matches) > 0) {
			$match = $matches[0];
			$match = str_replace('<nb:'.$tag.'>','',$match);
			$match = str_replace('</nb:'.$tag.'>','',$match);
		} else {
			$match = '';
		}
		return $match;
	}
	
	//-----------

	public function niceidformat($someid) 
	{
		while (strlen($someid) < 5) 
		{
			$someid = 0 . "$someid";
		}
		return $someid;
	}
	
	//-----------
	
	public function build_path( $date='', $id, $base )
	{
		if ( $date && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $date, $regs ) ) {
			$date = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		if ($date) {
			$dir_year  = date('Y', $date);
			$dir_month = date('m', $date);
		} else {
			$dir_year  = date('Y');
			$dir_month = date('m');
		}
		$dir_id = ResourcesHtml::niceidformat( $id );

		return $base.DS.$dir_year.DS.$dir_month.DS.$dir_id;
	}
	
	//-----------
	
	public function screenshots($id, $created, $upath, $wpath, $versionid=0, $sinfo=array(), $slidebar=0, $path='' )
	{
		$path = ResourcesHtml::build_path( $created, $id, '' );
		//$path = DS.ResourcesHtml::niceidformat( $id );
	
		// Get contribtool parameters
		$tconfig =& JComponentHelper::getParams( 'com_contribtool' );
		$allowversions = $tconfig->get('screenshot_edit');
			
		if ($versionid && $allowversions) { 
			// Add version directory
			$path .= DS.$versionid;
		}
		
			
		$d = @dir(JPATH_ROOT.$upath.$path);
		
		//echo JPATH_ROOT.$upath.$path;
	
		$images = array();
		$tns = array();
		$all = array();
		$ordering = array();
		$html = '';
		
		if ($d) {
			while (false !== ($entry = $d->read())) 
			{			
				$img_file = $entry; 
				if (is_file(JPATH_ROOT.$upath.$path.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
					if (eregi( "bmp|gif|jpg|png|swf", $img_file )) {
						$images[] = $img_file;
					}
					if (eregi( "-tn", $img_file )) {
						$tns[] = $img_file;
					}
					$images = array_diff($images, $tns);
				}							
			}
										
			$d->close();
		}
		
		$b = 0;
		if ($images) {
			foreach ($images as $ima) 
			{
				$new = array();
				$new['img'] = $ima;
				$new['type'] = explode('.',$new['img']);
						
				// get title and ordering info from the database, if available
				if (count($sinfo) > 0) {
					foreach ($sinfo as $si) 
					{				
						if ($si->filename == $ima) {
							$new['title'] = stripslashes($si->title);
							$new['title'] = preg_replace( '/"((.)*?)"/i', "&#147;\\1&#148;", $new['title'] );
							$new['ordering'] = $si->ordering;
						}	
					}
				}
			
				$ordering[] = isset($new['ordering']) ? $new['ordering'] : $b;
				$b++;
				$all[] = $new;
			} 
		}
		
		if (count($sinfo) > 0)  {
			// Sort by ordering
			array_multisort($ordering, $all);
		} else {
			// Sort by name
			sort ($all);
		}
		$images = $all;

		$els = '';
		$k = 0;
		$g = 0;
		for ($i=0, $n=count( $images ); $i < $n; $i++) 
		{
			$tn = ResourcesHtml::thumbnail($images[$i]['img']);
			$els .=  ($slidebar && $i==0 ) ? '<div class="showcase-pane">'."\n" : '';
			
			if (is_file(JPATH_ROOT.$upath.$path.DS.$tn)) {
				if (strtolower(end($images[$i]['type'])) == 'swf') {
					$g++;					
					$title = (isset($images[$i]['title']) && $images[$i]['title']!='' ) ? $images[$i]['title'] : JText::_('DEMO').' #'.$g;
					$els .= $slidebar ? '' : '<li>';
					$els .= ' <a class="popup" href="'.$wpath.$path.DS.$images[$i]['img'].'" title="'.$title.'">';
					$els .= '<img src="'.$wpath.$path.DS.$tn.'" alt="'.$title.'" class="thumbima" /></a>';
					$els .= $slidebar ? '' : '</li>'."\n";
				} else {
					$k++;
					$title = (isset($images[$i]['title']) && $images[$i]['title']!='' )  ? $images[$i]['title']: JText::_('SCREENSHOT').' #'.$k;
					$els .= $slidebar ? '' : '<li>';
					$els .= ' <a rel="lightbox" href="'.$wpath.$path.DS.$images[$i]['img'].'" title="'.$title.'">';
					$els .= '<img src="'.$wpath.$path.DS.$tn.'" alt="'.$title.'" class="thumbima" /></a>';
					$els .= $slidebar ? '' : '</li>'."\n";
				}
			}
			$els .=  ($slidebar && $i == ($n - 1)) ? '</div>'."\n" : '';
		}
		
		if ($els) {
			$html .= $slidebar ? '<div id="showcase">'."\n" : '';
			$html .= $slidebar ? '  <div id="showcase-prev" ></div>'."\n" : '';
			$html .= $slidebar ? '  <div id="showcase-window">'."\n" : '';							
			$html .= $slidebar ? '' : '<ul class="screenshots">'."\n";
			$html .= $els;
			$html .= $slidebar ? '' : '</ul>'."\n";
			$html .= $slidebar ? '  </div>'."\n" : '';
			$html .= $slidebar ? '  <div id="showcase-next" ></div>'."\n" : '';	
			$html .= $slidebar ? '</div>'."\n" : '';	
		}
		
		return $html;
	}
	
	//-----------
	
	public function thumbnail($pic)
	{
		$pic = explode('.',$pic);
		$n = count($pic);
		$pic[$n-2] .= '-tn';
		$end = array_pop($pic);
		$pic[] = 'gif';
		$tn = implode('.',$pic);
		return $tn;
	}
	
	//-----------
	
	public function skillLevelCircle($levels = array(), $sel = 0)
	{
		$html = '';
		$html.= '<ul class="audiencelevel">'.n;
		foreach ($levels as $key => $value) 
		{				
			$class = $key != $sel ? ' isoff' : '';
			$class = $key != $sel && $key == 'level0' ? '_isoff' : $class;
			$html .= t.t.t.' <li class="'.$key.$class.'"><span>&nbsp;</span></li>'.n;				
		}
		$html.= t.t.t.'</ul>'.n;
		return $html;	
	}
	
	//-----------
	
	public function skillLevelTable($labels = array(), $audiencelink)
	{
		$html  = '';
		$html .= t.'<table class="skillset" summary="'.JText::_('Resource Audience Skill Rating Table').'">'.n;
		$html .= t.t.'<thead>'.n;
		$html .= t.t.t.'<tr>'.n;
		$html .= t.t.t.'<td colspan = "2" class="combtd">'.JText::_('Difficulty Level').'</td>'.n;
		$html .= t.t.t.'<td>'.JText::_('Target Audience').'</td>'.n;
		$html .= t.t.t.'</tr>'.n;
		$html .= t.t.'</thead>'.n;
		$html .= t.t.'<tbody>'.n;
		foreach ($labels as $key => $label) 
		{
			$ul = ResourcesHtml::skillLevelCircle($labels, $key);
			$html .= ResourcesHtml::tableRow($ul,$label['title'],$label['desc']);
		}
		$html .= t.t.'</tbody>'.n;	
		$html .= t.'</table>'.n;
		$html .= t.'<p class="learnmore"><a href="'.$audiencelink.'">'.JText::_('Learn more').' &rsaquo;</a></p>'.n;
		return $html;	
	}
	
	//-----------
	
	public function showSkillLevel($audience, $showtips = 1, $numlevels = 4, $audiencelink = '')
	{
		$html 		= '';
		$levels 	= array();
		$labels 	= array();
		$selected 	= array();
		$txtlabel 	= '';
				
		if ($audience && count($audience) > 0) {
			$audience = $audience[0];
			$html .= t.t.'<div class="showscale">'.n;
			
			for ($i = 0, $n = $numlevels; $i <= $n; $i++) 
			{
				$lb = 'label'.$i;
				$lv = 'level'.$i;
				$ds = 'desc'.$i;
				$levels[$lv] 		  	 = $audience->$lv;
				$labels[$lv]['title']    = $audience->$lb;
				$labels[$lv]['desc']     = $audience->$ds;
				if($audience->$lv) {
					$selected[]			 = $lv;
				}
			}
			
			$html.= '<ul class="audiencelevel">'.n;
			
			// colored circles
			foreach ($levels as $key => $value) 
			{				
				$class = !$value ? ' isoff' : '';
				$class = !$value && $key == 'level0' ? '_isoff' : $class;
				$html .= ' <li class="'.$key.$class.'"><span>&nbsp;</span></li>'.n;				
			}
			
			// figure out text label
			if (count($selected) == 1) {
				$txtlabel = $labels[$selected[0]]['title'];
			} else if(count($selected) > 1) {
				$first 	    = array_shift($selected);
				$first		= $labels[$first]['title'];
				$firstbits  = explode("-", $first);
				$first 	    = array_shift($firstbits);
				
				$last 		= end($selected);
				$last		= $labels[$last]['title'];
				$lastbits  	= explode("-", $last);
				$last	   	= end($lastbits);
											
				$txtlabel  	= $first.'-'.$last;
			} else {
				$txtlabel = JText::_('Tool Audience Unrated');
			}
			
			$html.= ' <li class="txtlabel">'.$txtlabel.'</li>'.n;
			$html.= '</ul>'.n;
			$html .= t.t.'</div>'.n;
			
			// pop-up with explanation
			if ($showtips) {
				$html .= t.t.'<div class="explainscale">'.n;
				$html .= ResourcesHtml::skillLevelTable($labels, $audiencelink);	
				$html .= t.t.'</div>'.n;
			}
		
			return Hubzero_View_Helper_Html::div($html, 'usagescale');		
		}
		
		return $html;				
	}
	
	//-----------
	
	public function metadata($params, $ranking, $statshtml, $id, $sections, $xtra='')
	{
		$html = '';
		if ($params->get('show_ranking')) {
			$rank = round($ranking, 1);

			$r = (10*$rank);
			if (intval($r) < 10) {
				$r = '0'.$r;
			}

			$html .= '<dl class="rankinfo">'."\n";
			$html .= "\t".'<dt class="ranking"><span class="rank-'.$r.'">This resource has a</span> '.number_format($rank,1).' Ranking</dt>'."\n";
			$html .= "\t".'<dd>'."\n";
			$html .= "\t\t".'<p>'."\n";
			$html .= "\t\t\t".'Ranking is calculated from a formula comprised of <a href="'.JRoute::_('index.php?option=com_resources&id='.$id.'&active=reviews').'">user reviews</a> and usage statistics. <a href="about/ranking/">Learn more &rsaquo;</a>'."\n";
			$html .= "\t\t".'</p>'."\n";
			$html .= "\t\t".'<div>'."\n";
			$html .= $statshtml;
			$html .= "\t\t".'</div>'."\n";
			$html .= "\t".'</dd>'."\n";
			$html .= '</dl>'."\n";
		}
		$html .= ($xtra) ? $xtra : '';
		foreach ($sections as $section)
		{
			$html .= (isset($section['metadata'])) ? $section['metadata'] : '';
		}
		$html .= ResourcesHtml::div('', 'clear');
		
		return ResourcesHtml::div($html, 'metadata');
	}
	
	//-----------

	public function supportingDocuments($content)
	{
		$html  = ResourcesHtml::hed(3,JText::_('COM_RESOURCES_SUPPORTING_DOCUMENTS'))."\n";
		$html .= $content;

		return ResourcesHtml::div($html, 'supportingdocs');
	}
	
	//-----------
	
	public function license($license)
	{
		switch ($license)
		{
			case 'cc3nd':
			case 'cc3.0nd':
				$cls = 'cc';
				$lnk = 'http://creativecommons.org/licenses/by-nc-nd/3.0/';
				$txt = JText::_('Creative Commons');
			break;
			case 'cc3':
			case 'cc3.0':
				$cls = 'cc';
				$lnk = 'http://creativecommons.org/licenses/by-nc-sa/3.0/';
				$txt = JText::_('Creative Commons');
			break;
			case 'cc2.5':
				$cls = 'cc';
				$lnk = 'http://creativecommons.org/licenses/by-nc-sa/2.5/';
				$txt = JText::_('Creative Commons');
			break;
			case 'cc':
				$cls = 'cc';
				$lnk = 'http://creativecommons.org/licenses/by-nc-sa/2.5/';
				$txt = JText::_('Creative Commons');
			break;
			default:
				$cls = '';
				$lnk = '';
				$txt = '';
			break;
		}
		if ($txt) {
			return '<p class="'.$cls.' license">Licensed under '.$txt.' according to <a rel="license" href="'.$lnk.'">this deed</a>.</p>'."\n";
		} else {
			return '';
		}
	}
	
	//-------------------------------------------------------------
	// Sections
	//-------------------------------------------------------------

	public function sections( $sections, $cats, $active='about', $h, $c ) 
	{
		$html = '';
		
		if (!$sections) {
			return $html;
		}
		
		$k = 0;
		foreach ($sections as $section) 
		{
			if ($section['html'] != '') {
				$cls  = ($c) ? $c.' ' : '';
				if (key($cats[$k]) != $active) {
					$cls .= ($h) ? $h.' ' : '';
				}
				$html .= ResourcesHtml::div( $section['html'], $cls.'section', key($cats[$k]).'-section' );
			}
			$k++;
		}
		
		return $html;
	}
	
	//-----------
	
	public function tabs( $option, $id, $cats, $active='about', $alias='' ) 
	{
		$html  = '<div id="sub-menu">'."\n";
		$html .= "\t".'<ul>'."\n";
		$i = 1;
		foreach ($cats as $cat)
		{
			$name = key($cat);
			if ($name != '') {
				if ($alias) {
					$url = JRoute::_('index.php?option='.$option.'&alias='.$alias.'&active='.$name);
				} else {
					$url = JRoute::_('index.php?option='.$option.'&id='.$id.'&active='.$name);
				}
				if (strtolower($name) == $active) {
					$app =& JFactory::getApplication();
					$pathway =& $app->getPathway();
					$pathway->addItem($cat[$name],$url);
					
					if ($active != 'about') {
						$document =& JFactory::getDocument();
						$title = $document->getTitle();
						$document->setTitle( $title.': '.$cat[$name] );
					}
				}
				$html .= "\t\t".'<li id="sm-'.$i.'"';
				$html .= (strtolower($name) == $active) ? ' class="active"' : '';
				$html .= '><a class="tab" rel="'.$name.'" href="'.$url.'"><span>'.$cat[$name].'</span></a></li>'."\n";
				$i++;	
			}
		}
		$html .= "\t".'</ul>'."\n";
		$html .= '</div><!-- / #sub-menu -->'."\n";
		
		return $html;
	}
	
	//-----------
	
	public function title( $option, $resource, $params, $show_edit, $config=null, $show_posted=1 ) 
	{
		switch ($resource->published) 
		{
			case 1: $txt = ''; break;
			case 2: $txt = '<span>['.JText::_('COM_RESOURCES_DRAFT_EXTERNAL').']</span> '; break;
			case 3: $txt = '<span>['.JText::_('COM_RESOURCES_PENDING').']</span> '; break;
			case 4: $txt = '<span>['.JText::_('COM_RESOURCES_DELETED').']</span> '; break;
			case 5: $txt = '<span>['.JText::_('COM_RESOURCES_DRAFT_INTERNAL').']</span> '; break;
			case 0; $txt = '<span>['.JText::_('COM_RESOURCES_UNPUBLISHED').']</span> '; break;
		}
		
		//$txt .= ResourcesHtml::encode_html($resource->title);
		$txt .= stripslashes($resource->title);
		$txt .= ResourcesHtml::adminIcon( $resource->id, $resource->published, $show_edit, 0, 'edit', $resource->type );
		
		switch ($params->get('show_date')) 
		{
			case 0: $thedate = ''; break;
			case 1: $thedate = $resource->created; break;
			case 2: $thedate = $resource->modified; break;
			case 3: $thedate = $resource->publish_up; break;
		}
		
		$normalized_valid_chars = 'a-zA-Z0-9';
		$typenorm = preg_replace("/[^$normalized_valid_chars]/", "", $resource->getTypeTitle());
		$typenorm = strtolower($typenorm);
		
		$html  = ResourcesHtml::hed(2,$txt)."\n";
		
		if ($show_posted) {
			$html .= '<p>'.JText::_('COM_RESOURCES_POSTED').' ';
			$html .= ($thedate) ? JHTML::_('date', $thedate, '%d %b %Y').' ' : '';
			$html .= JText::_('COM_RESOURCES_IN').' <a href="'.JRoute::_('index.php?option='.$option.'&type='.$typenorm).'">'.$resource->getTypeTitle().'</a></p>'."\n";
		}
		
		/*$supported = null;
		if ($resource->type == 7) {
			$database =& JFactory::getDBO();
			$rt = new ResourcesTags( $database );
			$supported = $rt->checkTagUsage( $config->get('supportedtag'), $resource->id );
		}
		
		if ($supported) {
			include_once(JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'handler.php');
			$tag = new TagsTag( $database );
			$tag->loadTag($config->get('supportedtag'));
			
			$sl = $config->get('supportedlink');
			if ($sl) {
				$link = $sl;
			} else {
				$link = JRoute::_('index.php?option=com_tags'.a.'tag='.$tag->tag);
			}
			
			$html  = ResourcesHtml::div($html,'','content-header');
			$html .= ResourcesHtml::div(
						'<p class="supported"><a href="'.$link.'">'.$tag->raw_tag.'</a></p>',
						'',
						'content-header-extra'
					);
		} else {*/
			$html = ResourcesHtml::div($html,'full','content-header');
		//}
		
		return $html;
	}
	
	//-----------
	
	public function citationCOins($cite, $resource, $config, $helper)
	{
		if (!$cite) {
			return '';
		}
		
		$html  = '<span ';
		$html .= ' class="Z3988"';
		$html .= ' title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Ajournal';
		$html .= isset($resource->doi) 
			? '&amp;rft_id=info%3Adoi%2F'.urlencode($config->get('doi').'r'.$resource->id.'.'.$resource->doi) 
			: '&amp;rfr_id=info%3Asid%2Fnanohub.org%3AnanoHUB';
		//$html.= '&amp;rfr_id=info%3Asid%2Fnanohub.org%3AnanoHUB';
		$html .= '&amp;rft.genre=article';
		$html .= '&amp;rft.atitle='.urlencode($cite->title);
		$html .= '&amp;rft.date='.urlencode($cite->year);
		
		if (isset($resource->revision) && $resource->revision!='dev') {
			$helper->getToolAuthors($resource->alias, $resource->revision);			
		} else {
			$helper->getCons();			
		}
		$author_array = $helper->_contributors;

		if ($author_array) {
			for ($i = 0; $i < count($author_array); $i++) 
			{
				if ($author_array[$i]->lastname || $author_array[$i]->firstname) {
					$name = stripslashes($author_array[$i]->firstname) .' ';
					if ($author_array[$i]->middlename != NULL) {
						$name .= stripslashes($author_array[$i]->middlename) .' ';
					}
					$name .= stripslashes($author_array[$i]->lastname);
				} else {
					$name = $author_array[$i]->name;
				}
				
				if ($i==0) {
					$lastname = $author_array[$i]->lastname ? $author_array[$i]->lastname : $author_array[$i]->name;
					$firstname = $author_array[$i]->firstname ? $author_array[$i]->firstname : $author_array[$i]->name;
					$html.= '&amp;rft.aulast='.urlencode($lastname).'&amp;rft.aufirst='.urlencode($firstname);
				}
				//$html.= '&amp;rft.au='.urlencode($name);
			}
		}
		
		$html.= '"></span>'."\n";
		
		return $html;
	}
	
	//-----------
		
	public function aboutnontool( $database, $show_edit, $usersgroups, $resource, $helper, $config, $sections, $params, $attribs, $option, $fsize ) 
	{
		$xhub =& Hubzero_Factory::getHub();
		$helper->getChildren();
		
		$url = 'index.php?option='.$option.'&id='.$resource->id;
		$sef = JRoute::_($url);
		
		// Set the display date
		switch ($params->get('show_date')) 
		{
			case 0: $thedate = ''; break;
			case 1: $thedate = $resource->created;    break;
			case 2: $thedate = $resource->modified;   break;
			case 3: $thedate = $resource->publish_up; break;
		}
		
		// Prepare/parse text
		$introtext = stripslashes($resource->introtext);
		$maintext  = ($resource->fulltext) 
				   ? stripslashes($resource->fulltext) 
				   : stripslashes($resource->introtext);

		$maintext = stripslashes($maintext);

		if ($introtext) {
			$document =& JFactory::getDocument();
			$document->setDescription(ResourcesHtml::encode_html(strip_tags($introtext)));
		}

		// Parse for <nb: > tags
		$type = new ResourcesType( $database );
		$type->load( $resource->type );
		
		$fields = array();
		if (trim($type->customFields) != '') {
			$fs = explode("\n", trim($type->customFields));
			foreach ($fs as $f) 
			{
				$fields[] = explode('=', $f);
			}
		} else {
			$flds = $config->get('tagstool');
			$flds = explode(',',$flds);
			foreach ($flds as $fld) 
			{
				$fields[] = array($fld, $fld, 'textarea', 0);
			}
		}
		
		if (!empty($fields)) {
			for ($i=0, $n=count( $fields ); $i < $n; $i++) 
			{
				// Explore the text and pull out all matches
				array_push($fields[$i], ResourcesHtml::parseTag($maintext, $fields[$i][0]));
				
				// Clean the original text of any matches
				$maintext = str_replace('<nb:'.$fields[$i][0].'>'.end($fields[$i]).'</nb:'.$fields[$i][0].'>','',$maintext);
			}
			$maintext = trim($maintext);
		}
		
		$maintext = ($maintext) ? stripslashes($maintext) : stripslashes(trim($resource->introtext));
		$maintext = str_replace('&amp;','&',$maintext);
		$maintext = str_replace('&','&amp;',$maintext);
		$maintext = str_replace('<blink>','',$maintext);
		$maintext = str_replace('</blink>','',$maintext);
	
		$html  = '<div class="subject abouttab">'."\n";		
		$html .= "\t".'<table class="resource" summary="'.JText::_('RESOURCE_TBL_SUMMARY').'">'."\n";
		$html .= "\t\t".'<tbody>'."\n";
				
		// Check how much we can display
		if ($resource->access == 3 && (!in_array($resource->group_owner, $usersgroups) || $show_edit=0)) {
			// Protected - only show the introtext
			$html .= ResourcesHtml::tableRow('',$introtext);
		} else {
			$html .= ResourcesHtml::tableRow( JText::_('COM_RESOURCES_ABSTRACT'), $maintext );
			
			$citations = '';
			foreach ($fields as $field) 
			{
				if (end($field) != NULL) {
					if ($field[0] == 'citations') {
						$citations = end($field);
					} else {
						$html .= ResourcesHtml::tableRow( $field[1], end($field) );
					}
				}
			}
			
			if ($params->get('show_citation')) {
				if ($params->get('show_citation') == 1 || $params->get('show_citation') == 2) {
					// Citation instructions
					$helper->getUnlinkedContributors();

					// Build our citation object
					$cite = new stdClass();
					$cite->title = $resource->title;
					$cite->year = JHTML::_('date', $thedate, '%Y');
					$juri =& JURI::getInstance();
					if (substr($sef,0,1) == '/') {
						$sef = substr($sef,1,strlen($sef));
					}
					$cite->location = $juri->base().$sef;
					$cite->date = date( "Y-m-d H:i:s" );			
					$cite->url = '';
					$cite->type = '';
					$cite->author = $helper->ul_contributors;
					if (isset($resource->doi)) {
						$cite->doi = $config->get('doi').'r'.$resource->id.'.'.$resource->doi;
					}
					
					if ($params->get('show_citation') == 2) {
						$citations = '';
					}
				} else {
					$cite = null;
				}

				$citeinstruct  = ResourcesHtml::citation( $option, $cite, $resource->id, $citations, $resource->type );
				$citeinstruct .= ResourcesHtml::citationCOins($cite, $resource, $config, $helper);
				$html .= ResourcesHtml::tableRow( '<a name="citethis"></a>'.JText::_('COM_RESOURCES_CITE_THIS'), $citeinstruct );
			}
		}
		// If the resource had a specific event date/time
		if ($attribs->get( 'timeof', '' )) {
			if (substr($attribs->get( 'timeof', '' ), -8, 8) == '00:00:00') {
				$exp = '%B %d, %Y';
			} else {
				$exp = '%I:%M %p, %B %d, %Y';
			}
			$seminar_time = ($attribs->get( 'timeof', '' ) != '0000-00-00 00:00:00' || $attribs->get( 'timeof', '' ) != '') 
						  ? JHTML::_('date', $attribs->get( 'timeof', '' ), $exp) 
						  : '';
			$html .= ResourcesHtml::tableRow( JText::_('COM_RESOURCES_TIME'),$seminar_time);
		}
		// If the resource had a specific location
		if ($attribs->get( 'location', '' )) {
			$html .= ResourcesHtml::tableRow( JText::_('COM_RESOURCES_LOCATION'),$attribs->get( 'location', '' ));
		}
		// Tags
		if ($params->get('show_assocs')) {
			$helper->getTagCloud( $show_edit );

			$juser =& JFactory::getUser();
			$frm = '';
			/*Jif (!$juser->get('guest') && !isset($resource->tagform)) {
				$rt = new ResourcesTags($database);
				$usertags = $rt->get_tag_string( $resource->id, 0, 0, $juser->get('id'), 0, 0 );
					
				$document =& JFactory::getDocument();
				$document->setMetaData('keywords',$rt->get_tag_string( $resource->id, 0, 0, null, 0, 0 ));
					
				JPluginHelper::importPlugin( 'hubzero' );
				$dispatcher =& JDispatcher::getInstance();

				$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','',$usertags)) );
					
				$frm .= '<form method="post" id="tagForm" action="'.JRoute::_('index.php?option='.$option.'&id='.$resource->id).'">'."\n";
				$frm .= "\t".'<fieldset>'."\n";
				$frm .= "\t\t".'<label class="tag">'."\n";
				$frm .= "\t\t\t".JText::_('Your tags').': '."\n";
				if (count($tf) > 0) {
					$frm .= $tf[0];
				} else {
					$frm .= "\t\t\t".'<input type="text" name="tags" id="tags-men" size="30" value="'. $usertags .'" />'."\n";
				}
				$frm .= "\t\t".'</label>'."\n";
				$frm .= "\t\t".'<input type="submit" value="'.JText::_('COM_RESOURCES_SAVE').'"/>'."\n";
				$frm .= "\t\t".'<input type="hidden" name="task" value="savetags" />'."\n";
				$frm .= "\t".'</fieldset>'."\n";
				$frm .= '</form>'."\n";
			}*/

			if ($helper->tagCloud) {
				$html .= ResourcesHtml::tableRow( JText::_('COM_RESOURCES_TAGS'),$helper->tagCloud.$frm);
			}
		}

		$html .= "\t".' </tbody>'."\n";
		$html .= "\t".'</table>'."\n";
		$html .= '</div><!-- / .subject -->'."\n";
		$html .= '<div class="clear"></div>'."\n";
		$html .= '<input type="hidden" name="rid" id="rid" value="'.$resource->id.'" />'."\n";

		return $html;
	}

	
	
	//-----------
	
	public function abouttool( $database, $show_edit, $usersgroups, $resource, $helper, $config, $sections, $thistool, $curtool, $alltools, $revision, $params, $attribs, $option, $fsize ) 
	{
		$xhub =& Hubzero_Factory::getHub();
		
		if (!$thistool) {
			$helper->getChildren();
		}
		
		if ($resource->alias) {
			$url = 'index.php?option='.$option.'&alias='.$resource->alias;
		} else {
			$url = 'index.php?option='.$option.'&id='.$resource->id;
		}
		$sef = JRoute::_($url);
		
		// Set the display date
		switch ($params->get('show_date')) 
		{
			case 0: $thedate = ''; break;
			case 1: $thedate = $resource->created;    break;
			case 2: $thedate = $resource->modified;   break;
			case 3: $thedate = $resource->publish_up; break;
		}
		if ($curtool) {
			$thedate = $curtool->released;
		}
		
		// Prepare/parse text
		$introtext = stripslashes($resource->introtext);
		$maintext  = ($resource->fulltext) 
				   ? stripslashes($resource->fulltext) 
				   : stripslashes($resource->introtext);

		$maintext = stripslashes($maintext);

		if ($introtext) {
			$document =& JFactory::getDocument();
			$document->setDescription(ResourcesHtml::encode_html(strip_tags($introtext)));
		}

		// Parse for <nb: > tags
		$type = new ResourcesType( $database );
		$type->load( $resource->type );
		
		$fields = array();
		if (trim($type->customFields) != '') {
			$fs = explode("\n", trim($type->customFields));
			foreach ($fs as $f) 
			{
				$fields[] = explode('=', $f);
			}
		} else {
			$flds = $config->get('tagstool');
			$flds = explode(',',$flds);
			foreach ($flds as $fld) 
			{
				$fields[] = array($fld, $fld, 'textarea', 0);
			}
		}
		
		if (!empty($fields)) {
			for ($i=0, $n=count( $fields ); $i < $n; $i++) 
			{
				// Explore the text and pull out all matches
				array_push($fields[$i], ResourcesHtml::parseTag($maintext, $fields[$i][0]));
				
				// Clean the original text of any matches
				$maintext = str_replace('<nb:'.$fields[$i][0].'>'.end($fields[$i]).'</nb:'.$fields[$i][0].'>','',$maintext);
			}
			$maintext = trim($maintext);
		}
		
		$maintext = ($maintext) ? stripslashes($maintext) : stripslashes(trim($resource->introtext));
		$maintext = preg_replace('/&(?!(?i:\#((x([\dA-F]){1,5})|(104857[0-5]|10485[0-6]\d|1048[0-4]\d\d|104[0-7]\d{3}|10[0-3]\d{4}|0?\d{1,6}))|([A-Za-z\d.]{2,31}));)/i',"&amp;",$maintext);
		$maintext = str_replace('<blink>','',$maintext);
		$maintext = str_replace('</blink>','',$maintext);
		
		if (preg_match("/([\<])([^\>]{1,})*([\>])/i", $maintext)) {
				// Do nothing
		} else {
				// Get the wiki parser and parse the full description
				$wikiconfig = array(
					'option'   => $option,
					'scope'    => 'resources'.DS.$resource->id,
					'pagename' => 'resources',
					'pageid'   => $resource->id,
					'filepath' => $config->get('uploadpath'),
					'domain'   => '' 
				);
				ximport('Hubzero_Wiki_Parser');
				$p =& Hubzero_Wiki_Parser::getInstance();
				$maintext = $p->parse($maintext, $wikiconfig);
		}
		
		$html  = '<div class="subject abouttab">'."\n";
		
		// Screenshots
		$ss = new ResourceScreenshot($database);			
		$shots = ResourcesHtml::screenshots($resource->id, $resource->created, $config->get('uploadpath'), $config->get('uploadpath'), $resource->versionid, $ss->getScreenshots($resource->id, $resource->versionid), 1);	
		if($shots) {
			$html .= ' <div class="sscontainer">'."\n";					
			$html .= $shots;
			$html .= ' </div><!-- / .sscontainer -->'."\n";
		}
	
		$html .= "\t".'<table class="resource" summary="'.JText::_('COM_RESOURCES_RESOURCE_TBL_SUMMARY').'">'."\n";
		$html .= "\t\t".'<tbody>'."\n";
		
		// Check how much we can display
		if ($resource->access == 3 && (!in_array($resource->group_owner, $usersgroups) || $show_edit=0)) {
			// Protected - only show the introtext
			$html .= ResourcesHtml::tableRow('',$introtext);
		} else {
		
			$html .= ResourcesHtml::tableRow( JText::_('COM_RESOURCES_DESCRIPTION'), $maintext );
			
			$citations = '';
			foreach ($fields as $field) 
			{
				if (end($field) != NULL) {
					if ($field[0] == 'citations') {
						$citations = end($field);
					} else {
						$html .= ResourcesHtml::tableRow( $field[1], end($field) );
					}
				}
			}
		
			if ($params->get('show_citation')) {
				if ($params->get('show_citation') == 1 || $params->get('show_citation') == 2) {
					// Citation instructions
					$helper->getUnlinkedContributors();

					// Build our citation object
					$cite = new stdClass();
					$cite->title = $resource->title;
					$cite->year = JHTML::_('date', $thedate, '%Y');
					if ($alltools && $resource->doi) {
						$cite->location = ' <a href="'.$config->get('aboutdoi').'" title="'.JText::_('COM_RESOURCES_ABOUT_DOI').'">DOI</a>: '.$config->get('doi').'r'.$resource->id.'.'.$resource->doi;
						$cite->date = '';
					} else {
						$juri =& JURI::getInstance();
						if (substr($sef,0,1) == '/') {
							$sef = substr($sef,1,strlen($sef));
						}
						$cite->location = $juri->base().$sef;
						$cite->date = date( "Y-m-d H:i:s" );
					}			
					$cite->url = '';
					$cite->type = '';
					$cite->author = $helper->ul_contributors;
					if ($revision != 'dev' && $resource->doi) {
						$cite->doi = $config->get('doi').'r'.$resource->id.'.'.$resource->doi;
					}
					
					if ($params->get('show_citation') == 2) {
						$citations = '';
					}
				} else {
					$cite = null;
				}
				
				$helper->getUnlinkedContributors();

				$citeinstruct  = ResourcesHtml::citation( $option, $cite, $resource->id, $citations, $resource->type, $revision );
				$citeinstruct .= ResourcesHtml::citationCOins($cite, $resource, $config, $helper);
				$html .= ResourcesHtml::tableRow( '<a name="citethis"></a>'.JText::_('COM_RESOURCES_CITE_THIS'), $citeinstruct );
				
			}
		}
		// If the resource had a specific event date/time
		if ($attribs->get( 'timeof', '' )) {
			if (substr($attribs->get( 'timeof', '' ), -8, 8) == '00:00:00') {
				$exp = '%B %d, %Y';
			} else {
				$exp = '%I:%M %p, %B %d, %Y';
			}
			$seminar_time = ($attribs->get( 'timeof', '' ) != '0000-00-00 00:00:00' || $attribs->get( 'timeof', '' ) != '') 
						  ? JHTML::_('date', $attribs->get( 'timeof', '' ), $exp) 
						  : '';
			$html .= ResourcesHtml::tableRow( JText::_('COM_RESOURCES_TIME'),$seminar_time);
		}
		// If the resource had a specific location
		if ($attribs->get( 'location', '' )) {
			$html .= ResourcesHtml::tableRow( JText::_('COM_RESOURCES_LOCATION'),$attribs->get( 'location', '' ));
		}
		// Tags
		if (!$thistool && $revision!='dev') {
			if ($params->get('show_assocs')) {
				$helper->getTagCloud( $show_edit );

				$juser =& JFactory::getUser();
				$frm = '';
				/*if (!$juser->get('guest') && !isset($resource->tagform)) {
					$rt = new ResourcesTags($database);
					$usertags = $rt->get_tag_string( $resource->id, 0, 0, $juser->get('id'), 0, 0 );
					
					$document =& JFactory::getDocument();
					$document->setMetaData('keywords',$rt->get_tag_string( $resource->id, 0, 0, null, 0, 0 ));
					
					JPluginHelper::importPlugin( 'hubzero' );
					$dispatcher =& JDispatcher::getInstance();

					$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','',$usertags)) );
					
					$frm .= '<form method="post" id="tagForm" action="'.JRoute::_('index.php?option='.$option.'&id='.$resource->id).'">'."\n";
					$frm .= "\t".'<fieldset>'."\n";
					$frm .= "\t\t".'<label class="tag">'."\n";
					$frm .= "\t\t\t".JText::_('Your tags').': '."\n";
					if (count($tf) > 0) {
						$frm .= $tf[0];
					} else {
						//$frm .= "\t\t\t".'<textarea name="tags" id="tags-men" rows="6" cols="35">'. $usertags .'</textarea>'."\n";
						$frm .= "\t\t\t".'<input type="text" name="tags" id="tags-men" size="30" value="'. $usertags .'" />'."\n";
					}
					$frm .= "\t\t".'</label>'."\n";
					$frm .= "\t\t".'<input type="submit" value="'.JText::_('COM_RESOURCES_SAVE').'"/>'."\n";
					$frm .= "\t\t".'<input type="hidden" name="task" value="savetags" />'."\n";
					$frm .= "\t".'</fieldset>'."\n";
					$frm .= '</form>'."\n";
				}*/

				if ($helper->tagCloud) {
					$html .= ResourcesHtml::tableRow( JText::_('COM_RESOURCES_TAGS'),$helper->tagCloud.$frm);
				}
			}
		}
		$html .= "\t".' </tbody>'."\n";
		$html .= "\t".'</table>'."\n";
		$html .= '</div><!-- / .subject -->'."\n";
		$html .= '<div class="clear"></div>'."\n";
		$html .= '<input type="hidden" name="rid" id="rid" value="'.$resource->id.'" />'."\n";
		return $html;
		
	}
	
	//-----------
	
	public function about( $database, $show_edit, $usersgroups, $resource, $helper, $config, $sections, $thistool, $curtool, $alltools, $revision, $params, $attribs, $option, $fsize ) 
	{
		$xhub =& Hubzero_Factory::getHub();
		
		//if ($resource->type != 31 || $resource->type != 2 || !$thistool) {
		if (!$thistool) {
			$helper->getChildren();
		}
		
		if ($resource->alias) {
			$url = 'index.php?option='.$option.'&alias='.$resource->alias;
			// If tool version page is requested
			/*if ($thistool) {
				$url .= a.'v='.$thistool->revision;
			}*/
		} else {
			$url = 'index.php?option='.$option.'&id='.$resource->id;
		}
		$sef = JRoute::_($url);
		
		// Set the display date
		switch ($params->get('show_date')) 
		{
			case 0: $thedate = ''; break;
			case 1: $thedate = $resource->created;    break;
			case 2: $thedate = $resource->modified;   break;
			case 3: $thedate = $resource->publish_up; break;
		}
		
		// Prepare/parse text
		$introtext = stripslashes($resource->introtext);
		$maintext  = ($resource->fulltext) 
				   ? stripslashes($resource->fulltext) 
				   : stripslashes($resource->introtext);

		$maintext = stripslashes($maintext);

		if ($introtext) {
			$document =& JFactory::getDocument();
			$document->setDescription(ResourcesHtml::encode_html(strip_tags($introtext)));
		}

		// Parse for <nb: > tags
		$type = new ResourcesType( $database );
		$type->load( $resource->type );
		
		$fields = array();
		if (trim($type->customFields) != '') {
			$fs = explode("\n", trim($type->customFields));
			foreach ($fs as $f) 
			{
				$fields[] = explode('=', $f);
			}
		} else {
			$flds = $config->get('tagstool');
			$flds = explode(',',$flds);
			foreach ($flds as $fld) 
			{
				$fields[] = array($fld, $fld, 'textarea', 0);
			}
		}
		
		if (!empty($fields)) {
			for ($i=0, $n=count( $fields ); $i < $n; $i++) 
			{
				// Explore the text and pull out all matches
				array_push($fields[$i], ResourcesHtml::parseTag($maintext, $fields[$i][0]));
				
				// Clean the original text of any matches
				$maintext = str_replace('<nb:'.$fields[$i][0].'>'.end($fields[$i]).'</nb:'.$fields[$i][0].'>','',$maintext);
			}
			$maintext = trim($maintext);
		}
		/*$nbtags = $config->get('tagstool');
		$nbtags = explode(',',$nbtags);
		foreach ($nbtags as $nbtag)
		{
			$nbtag = trim($nbtag);
			// Explore the text and pull out all matches
			$allnbtags[$nbtag] = ResourcesHtml::parseTag($maintext, $nbtag);
			
			// Clean the original text of any matches
			$maintext = str_replace('<nb:'.$nbtag.'>'.$allnbtags[$nbtag].'</nb:'.$nbtag.'>','',$maintext);
		}

		// Clean out any extra whitespace
		$maintext = trim($maintext);*/
		$maintext = ($maintext) ? stripslashes($maintext) : stripslashes(trim($resource->introtext));
		$maintext = preg_replace('/&(?!(?i:\#((x([\dA-F]){1,5})|(104857[0-5]|10485[0-6]\d|1048[0-4]\d\d|104[0-7]\d{3}|10[0-3]\d{4}|0?\d{1,6}))|([A-Za-z\d.]{2,31}));)/i',"&amp;",$maintext);
		$maintext = str_replace('<blink>','',$maintext);
		$maintext = str_replace('</blink>','',$maintext);
		
		if ($resource->type == 7) {
			//if (strlen($maintext) != strlen(strip_tags($maintext)){
			if (preg_match("/([\<])([^\>]{1,})*([\>])/i", $maintext)) {
				// Do nothing
			} else {
				// Get the wiki parser and parse the full description
				$wikiconfig = array(
					'option'   => $option,
					'scope'    => 'resources'.DS.$resource->id,
					'pagename' => 'resources',
					'pageid'   => $resource->id,
					'filepath' => $config->get('uploadpath'),
					'domain'   => '' 
				);
				ximport('Hubzero_Wiki_Parser');
				$p =& Hubzero_Wiki_Parser::getInstance();
				$maintext = $p->parse($maintext, $wikiconfig);
			}
		}
		
		// Extract the matches to their own variables
		//extract($allnbtags);
		
		$html  = '<div class="aside">'."\n";
		// Show resource ratings
		if (!$thistool) {
			$statshtml = '';
			
			if ($params->get('show_ranking')) {
				$helper->getCitations();
				$helper->getLastCitationDate();
				
				if ($resource->type == 7) {
					$stats = new ToolStats($database, $resource->id, $resource->type, $resource->rating, count($helper->citations), $helper->lastCitationDate);
				} else {
					$stats = new AndmoreStats($database, $resource->id, $resource->type, $resource->rating, count($helper->citations), $helper->lastCitationDate);
				}
				
				$statshtml = $stats->display();
			}
			
			if ($params->get('show_metadata')) {
				$supported = null;
				if ($resource->type == 7) {
					$database =& JFactory::getDBO();
					$rt = new ResourcesTags( $database );
					$supported = $rt->checkTagUsage( $config->get('supportedtag'), $resource->id );
				}
				$xtra = '';
				if ($supported) {
					include_once(JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'handler.php');
					$tag = new TagsTag( $database );
					$tag->loadTag($config->get('supportedtag'));

					$sl = $config->get('supportedlink');
					if ($sl) {
						$link = $sl;
					} else {
						$link = JRoute::_('index.php?option=com_tags&tag='.$tag->tag);
					}

					$xtra = '<p class="supported"><a href="'.$link.'">'.$tag->raw_tag.'</a></p>';
				}
				
				$html .= ResourcesHtml::metadata($params, $resource->ranking, $statshtml, $resource->id, $sections, $xtra);
			}
		}
				
		// Private/Public resource access check
		if ($resource->access == 3 && !in_array($resource->group_owner, $usersgroups) && !$show_edit) {
			$ghtml = JText::_('ERROR_MUST_BE_PART_OF_GROUP').' ';
			$allowedgroups = $resource->getGroups();
			foreach ($allowedgroups as $allowedgroup) 
			{
				$ghtml .= '<a href="'.JRoute::_('index.php?option=com_groups&gid='.$allowedgroup).'">'.$allowedgroup.'</a>, ';
			}
			$ghtml = substr($ghtml,0,strlen($ghtml) - 2);
			$html .= ResourcesHtml::warning( $ghtml )."\n";
		} else {
			$helper->getFirstChild();
			
			$live_site = $xhub->getCfg('hubLongURL');
			
			switch ($resource->type)
			{
				case 7:
					// Show launch button if simulation tool page
					$specapp = $resource->alias;
					$mytools = 1;

					$html .= ResourcesHtml::primary_child( $option, $resource, $helper->firstChild, '' );
					
					if ($alltools) {
						$vh  = ResourcesHtml::hed(3, JText::_('COM_RESOURCES_AVAILABLE_VERSIONS') )."\n";
						if ($alltools != NULL) {
							$vh .= '<ul>'."\n";
							$sef = JRoute::_('index.php?option='.$option.'&alias='.$resource->alias);
							$i = 0;
							foreach ($alltools as $v) 
							{
								$i++;
								if ($v->state==3 && $resource->revision=='dev') {
									// display dev version as current
									$vh .= "\t".'<li';
									$vh .= ' class="currentversion"';
									$vh .= '>';
									$vh .= $v->version.' ('.JText::_('in development').')';
									$vh .= "\t".'</li>'."\n";

								}
								if ($i < 10 && $v->state!=3) { // limit to 5 recent versions
									$publishflag = ($v->state == 1) ? JText::_('PUBLISHED') : JText::_('UNPUBLISHED');
									$vh .= "\t".'<li';
									if ($v->revision == $resource->revision) {
										$vh .= ' class="currentversion"';
									}
									$vh .= '>';
									if ($v->revision != $resource->revision) {
										$vh .='<a href="'.$sef.'?rev='. $v->revision.'">';
									}
									$vh .= $v->version.' ('.$publishflag.')';
									if ($v->revision != $resource->revision) {
										$vh .='</a>'."\n";
									}

									// source code download
									if ($v->revision == $resource->revision) {
										if (isset($resource->toolsource) && $resource->toolsource == 1 && isset($resource->tool)) { // open source
											if ($resource->taravailable) {					
												$out .= ' <span class="downloadcode"><a href="index.php/'.$resource->tarname.'?option='.$option.'&task=sourcecode&tool='.$resource->tool.'">'.JText::sprintf('DOWNLOAD_SOURCE', $resource->version).'</a></span>'."\n";
											} else { // tarball is not there
												$out .= ' <span class="downloadcode"><span>'.JText::_('SOURCE_UNAVAILABLE').'</span></span>'."\n";
											}
										}
									}

									$vh .= "\t".'</li>'."\n";
								}
							}
							if (count($versions) > 5) { // show 'more' link
								$vh .= "\t".'<li><a href="'.$sef.DS.'versions">'.JText::_('MORE_VERSIONS').'</a></li>'."\n";
							}
							$vh .= '</ul>'."\n";
						}
						
						$html .= ResourcesHtml::div($vh, 'versions');
					}
						
					if (count($helper->children) >= 1 && !(trim($helper->firstChild->introtext) == 'Launch Tool') && !$thistool) {
						$dls = ResourcesHtml::writeChildren( $config, $option, $database, $resource, $helper->children, $live_site, '', '', $resource->id, $fsize );

						$html .= ResourcesHtml::supportingDocuments($dls);
					}
					
					// Open/closed source
					if (isset($resource->toolsource) && $resource->toolsource == 1 && isset($resource->tool)) { // open source
						$html .= '<p class="opensource license">This tool is <a href="http://www.opensource.org/docs/definition.php" rel="external">open source</a>, according to <a class="popup" href="index.php?option=com_resources&task=license&tool='.$resource->tool.'&no_html=1">this license</a>.</p>'."\n";	
					} elseif (isset($resource->toolsource) && !$resource->toolsource) { // closed source, archive page
						$html .= '<p class="closedsource license">'.JText::_('COM_RESOURCES_TOOL_IS_CLOSED_SOURCE').'</p>'."\n";
					}
				break;
					
				/*case 4:
					// Write primary button and downloads for a Learning Module
					$html .= ResourcesHtml::primary_child( $option, $resource, $helper->firstChild, '' );
					$dls = ResourcesHtml::writeDownloads( $database, $resource->id, $option, $config, $fsize );
					if ($dls) {
						$html .= ResourcesHtml::supportingDocuments($dls);
					}
				break;*/
					
				case 6:
				case 31:
				case 2:
					// If more than one child the show the list of children
					$helper->getChildren( $resource->id, 0, 'no' );
					$children = $helper->children;

					if ($children) {
						$dls = ResourcesHtml::writeChildren( $config, $option, $database, $resource, $children, $live_site, '', '', $resource->id, $fsize );
					
						$html .= ResourcesHtml::supportingDocuments($dls);
					}
				
					$html .= "\t\t".'<p>'."\n";
					$html .= "\t\t\t".'<a class="feed" id="resource-audio-feed" href="'. $xhub->getCfg('hubLongURL') .'/resources/'.$resource->id.'/feed.rss?format=audio">'.JText::_('Audio podcast').'</a><br />'."\n";
					$html .= "\t\t\t".'<a class="feed" id="resource-video-feed" href="'. $xhub->getCfg('hubLongURL') .'/resources/'.$resource->id.'/feed.rss?format=video">'.JText::_('Video podcast').'</a><br />'."\n";
					$html .= "\t\t\t".'<a class="feed" id="resource-slides-feed" href="'. $xhub->getCfg('hubLongURL') .'/resources/'.$resource->id.'/feed.rss?format=slides">'.JText::_('Slides/Notes podcast').'</a>'."\n";
					$html .= "\t\t".'</p>'."\n";
				break;
				
				case 8:
					$html .= "\t\t".'<p><a class="feed" id="resource-audio-feed" href="'. $xhub->getCfg('hubLongURL') .'/resources/'.$resource->id.'/feed.rss?format=audio">'.JText::_('Audio podcast').'</a><br />'."\n";
					$html .= "\t\t".'<a class="feed" id="resource-video-feed" href="'. $xhub->getCfg('hubLongURL') .'/resources/'.$resource->id.'/feed.rss?format=video">'.JText::_('Video podcast').'</a></p>'."\n";
					// do nothing
				break;
					
				default:
					if ($helper->children) {
						$html .= ResourcesHtml::primary_child( $option, $resource, $helper->firstChild, '' );
					}
						
					// If more than one child the show the list of children
					if ($helper->children && count($helper->children) > 1 && !(trim($helper->firstChild->introtext) == 'Launch Tool')) {
						$dls = ResourcesHtml::writeChildren( $config, $option, $database, $resource, $helper->children, $live_site, '', '', $resource->id, $fsize );
						
						$html .= ResourcesHtml::supportingDocuments($dls);
					}
				break;
			}
		}
		
		$html .= ResourcesHtml::license( $params->get( 'license', '' ) );

		$html .= '</div><!-- / .aside -->'."\n";
		$html .= '<div class="subject">'."\n";
		
		// Show archive message
		if ($thistool && $revision!='dev') {
			$msg  = '<strong>'.JText::_('COM_RESOURCES_ARCHIVE').'</strong><br />';
			$msg .= JText::_('COM_RESOURCES_ARCHIVE_MESSAGE');
			if ($resource->version) {
				$msg .= ' <br />'.JText::_('COM_RESOURCES_THIS_VERSION').': '.$resource->version.'.';
			}
			if (isset($resource->curversion) && $resource->curversion) {
				$msg .= ' <br />'.JText::_('COM_RESOURCES_LATEST_VERSION').': <a href="'.$sef.'?rev='.$curtool->revision.'">'.$resource->curversion.'</a>.';
			}
			
			$html .= ResourcesHtml::archive( $msg )."\n";
		}
		
		$html .= "\t".'<table class="resource" summary="'.JText::_('COM_RESOURCES_RESOURCE_TBL_SUMMARY').'">'."\n";
		$html .= "\t\t".'<tbody>'."\n";
		
		// Display version specific information
		if ($resource->type == 7 && $alltools) {
			$versiontext = '<strong>';
			if ($revision && $thistool) {
				$versiontext .= $thistool->version.'</strong>';
				if ($resource->revision!='dev') {
					$versiontext .=  ' - '.JText::_('COM_RESOURCES_PUBLISHED_ON').' ';
					$versiontext .= ($thistool->released && $thistool->released != '0000-00-00 00:00:00') ? JHTML::_('date', $thistool->released, '%d %b %Y'): JHTML::_('date', $resource->publish_up, '%d %b %Y');
					$versiontext .= ($thistool->unpublished && $thistool->unpublished != '0000-00-00 00:00:00') ? ', '.JText::_('COM_RESOURCES_UNPUBLISHED_ON').' '.JHTML::_('date', $thistool->unpublished, '%d %b %Y'): '';
				} else {
					$versiontext .= ' ('.JText::_('COM_RESOURCES_IN_DEVELOPMENT').')';
				}
			} else if ($curtool) {
				$versiontext .= $curtool->version.'</strong> - '.JText::_('PUBLISHED_ON').' ';
				$versiontext .= ($curtool->released && $curtool->released != '0000-00-00 00:00:00') ? JHTML::_('date', $curtool->released, '%d %b %Y'): JHTML::_('date', $resource->publish_up, '%d %b %Y');
			}
			
			if ($revision == 'dev') {
				$html .= "\t\t\t".'<tr class="devversion">'."\n";
				$html .= "\t\t\t\t".'<th>'.JText::_('COM_RESOURCES_VERSION').'</th>'."\n";
				$html .= "\t\t\t\t".'<td>'.$versiontext.'</td>'."\n";
				$html .= "\t\t\t".'</tr>'."\n";
			} else {
				$html .= ResourcesHtml::tableRow(JText::_('COM_RESOURCES_VERSION'), $versiontext);
			}
		}
		
		if ($params->get('show_authors')) {
			// Get contributors of this version		
			if ($alltools && $resource->revision!='dev') {
				$helper->getToolAuthors($resource->alias, $resource->revision);			
			}

			// Get contributors on this resource
			$helper->getContributors(true);
			if ($helper->contributors && $helper->contributors != '<br />') {
				$html .= ResourcesHtml::tableRow( JText::_('COM_RESOURCES_CONTRIBUTORS'), $helper->contributors );
			}
		}
		
		// Display "at a glance"
		if ($resource->type == 7) {
			$html .= ResourcesHtml::tableRow( JText::_('COM_RESOURCES_AT_A_GLANCE'), $resource->introtext );
		}
		
		// Check how much we can display
		if ($resource->access == 3 && (!in_array($resource->group_owner, $usersgroups) || $show_edit=0)) {
			// Protected - only show the introtext
			$html .= ResourcesHtml::tableRow('',$introtext);
		} else {
			if ($resource->type == 7) {
				// Get screenshot information for this resource
				$ss = new ResourceScreenshot($database);
				
				$shots = ResourcesHtml::screenshots($resource->id, $resource->created, $config->get('uploadpath'), $config->get('uploadpath'), $resource->versionid, $ss->getScreenshots($resource->id, $resource->versionid));
				
				if ($shots) {
					$html .= ResourcesHtml::tableRow( JText::_('COM_RESOURCES_SCREENSHOTS'), $shots );
				}
			}
			
			if ($resource->type == 7) {
				$html .= ResourcesHtml::tableRow( JText::_('COM_RESOURCES_DESCRIPTION'), $maintext );
			} else {
				$html .= ResourcesHtml::tableRow( JText::_('COM_RESOURCES_ABSTRACT'), $maintext );
			}
			
			$citations = '';
			foreach ($fields as $field) 
			{
				if (end($field) != NULL) {
					if ($field[0] == 'citations') {
						$citations = end($field);
					} else {
						$html .= ResourcesHtml::tableRow( $field[1], end($field) );
					}
				}
			}
			
			if ($params->get('show_citation')) {
				if ($params->get('show_citation') == 1 || $params->get('show_citation') == 2) {
					// Citation instructions
					$helper->getUnlinkedContributors();

					// Build our citation object
					$cite = new stdClass();
					$cite->title = $resource->title;
					$cite->year = JHTML::_('date', $thedate, '%Y');
					if ($alltools && $resource->doi) {
						$cite->location = ' <a href="'.$config->get('aboutdoi').'" title="'.JText::_('COM_RESOURCES_ABOUT_DOI').'">DOI</a>: '.$config->get('doi').'r'.$resource->id.'.'.$resource->doi;
						$cite->date = '';
					} else {
						$juri =& JURI::getInstance();
						if (substr($sef,0,1) == '/') {
							$sef = substr($sef,1,strlen($sef));
						}
						$cite->location = $juri->base().$sef;
						$cite->date = date( "Y-m-d H:i:s" );
					}			
					$cite->url = '';
					$cite->type = '';
					$cite->author = $helper->ul_contributors;
					
					if ($params->get('show_citation') == 2) {
						$citations = '';
					}
				} else {
					$cite = null;
				}

				$citeinstruct = ResourcesHtml::citation( $option, $cite, $resource->id, $citations, $resource->type, $revision );
				$html .= ResourcesHtml::tableRow( JText::_('COM_RESOURCES_CITE_THIS'), $citeinstruct );
			}
		}
		// If the resource had a specific event date/time
		if ($attribs->get( 'timeof', '' )) {
			if (substr($attribs->get( 'timeof', '' ), -8, 8) == '00:00:00') {
				$exp = '%B %d, %Y';
			} else {
				$exp = '%I:%M %p, %B %d, %Y';
			}
			$seminar_time = ($attribs->get( 'timeof', '' ) != '0000-00-00 00:00:00' || $attribs->get( 'timeof', '' ) != '') 
						  ? JHTML::_('date', $attribs->get( 'timeof', '' ), $exp) 
						  : '';
			$html .= ResourcesHtml::tableRow( JText::_('COM_RESOURCES_TIME'),$seminar_time);
		}
		// If the resource had a specific location
		if ($attribs->get( 'location', '' )) {
			$html .= ResourcesHtml::tableRow( JText::_('COM_RESOURCES_LOCATION'),$attribs->get( 'location', '' ));
		}
		// Tags
		if (!$thistool && $revision!='dev') {
			if ($params->get('show_assocs')) {
				$helper->getTagCloud( $show_edit );

				$juser =& JFactory::getUser();
				$frm = '';
				/*if (!$juser->get('guest') && !isset($resource->tagform)) {
					$rt = new ResourcesTags($database);
					$usertags = $rt->get_tag_string( $resource->id, 0, 0, $juser->get('id'), 0, 0 );
					
					$document =& JFactory::getDocument();
					$document->setMetaData('keywords',$rt->get_tag_string( $resource->id, 0, 0, null, 0, 0 ));
					
					JPluginHelper::importPlugin( 'hubzero' );
					$dispatcher =& JDispatcher::getInstance();
					
					$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','',$usertags)) );
					
					$frm .= '<form method="post" id="tagForm" action="'.JRoute::_('index.php?option='.$option.'&id='.$resource->id).'">'."\n";
					$frm .= "\t".'<fieldset>'."\n";
					$frm .= "\t\t".'<label class="tag">'."\n";
					$frm .= "\t\t\t".JText::_('Your tags').': '."\n";
					if (count($tf) > 0) {
						$frm .= $tf[0];
					} else {
						//$frm .= "\t\t\t".'<textarea name="tags" id="tags-men" rows="6" cols="35">'. $usertags .'</textarea>'."\n";
						$frm .= "\t\t\t".'<input type="text" name="tags" id="tags-men" size="30" value="'. $usertags .'" />'."\n";
					}
					$frm .= "\t\t".'</label>'."\n";
					$frm .= "\t\t".'<input type="submit" value="'.JText::_('COM_RESOURCES_SAVE').'"/>'."\n";
					$frm .= "\t\t".'<input type="hidden" name="task" value="savetags" />'."\n";
					$frm .= "\t".'</fieldset>'."\n";
					$frm .= '</form>'."\n";
				}*/

				if ($helper->tagCloud) {
					$html .= ResourcesHtml::tableRow( JText::_('COM_RESOURCES_TAGS'),$helper->tagCloud.$frm);
				}
			}
		}
		$html .= "\t".' </tbody>'."\n";
		$html .= "\t".'</table>'."\n";
		$html .= '</div><!-- / .subject -->'."\n";
		$html .= '<div class="clear"></div>'."\n";
		$html .= '<input type="hidden" name="rid" id="rid" value="'.$resource->id.'" />'."\n";
		
		return $html;
	}

	//-----------

	public function citation( $option, $cite, $id, $citations, $type, $rev='') 
	{
		include_once( JPATH_ROOT.DS.'components'.DS.'com_citations'.DS.'citations.formatter.php' );

		$html  = '<p>'.JText::_('COM_RESOURCES_CITATION_INSTRUCTIONS').'</p>'."\n";
		$html .= $citations;
		if ($cite) {
			$html .= '<ul class="citations results">'."\n";
			$html .= "\t".'<li>'."\n";
			$html .= CitationsFormatter::formatReference($cite);
			if ($rev!='dev') {
				$html .= "\t\t".'<p class="details">'."\n";
				$html .= "\t\t\t".'<a href="index.php?option='.$option.'&task=citation&id='.$id.'&format=bibtex&no_html=1&rev='.$rev.'" title="'.JText::_('DOWNLOAD_BIBTEX_FORMAT').'">BibTex</a> <span>|</span> '."\n";
				$html .= "\t\t\t".'<a href="index.php?option='.$option.'&task=citation&id='.$id.'&format=endnote&no_html=1&rev='.$rev.'" title="'.JText::_('DOWNLOAD_ENDNOTE_FORMAT').'">EndNote</a>'."\n";
				$html .= "\t\t".'</p>'."\n";
			}
			$html .= "\t".'</li>'."\n";
			$html .= '</ul>'."\n";
		}
		/*if ($type == 7) {
			$html .= '<p>'.JText::_('In addition, we would appreciate it if you would add the following acknowledgment to your publication:').'</p>'."\n";
			$html .= '<ul class="citations results">'."\n";
			$html .= "\t".'<li>'."\n";
			$html .= "\t\t".'<p>'.JText::_('Simulation services for results presented here were provided by the Network for Computational Nanotechnology (NCN) at nanoHUB.org').'</p>'."\n";
			$html .= "\t".'</li>'."\n";
			$html .= '</ul>'."\n";
		}*/
		return $html;
	}

	//-------------------------------------------------------------
	// Bits
	//-------------------------------------------------------------

	public function getRatingClass($rating=0)
	{
		switch ($rating) 
		{
			case 0.5: $class = ' half-stars';      break;
			case 1:   $class = ' one-stars';       break;
			case 1.5: $class = ' onehalf-stars';   break;
			case 2:   $class = ' two-stars';       break;
			case 2.5: $class = ' twohalf-stars';   break;
			case 3:   $class = ' three-stars';     break;
			case 3.5: $class = ' threehalf-stars'; break;
			case 4:   $class = ' four-stars';      break;
			case 4.5: $class = ' fourhalf-stars';  break;
			case 5:   $class = ' five-stars';      break;
			case 0:
			default:  $class = ' no-stars';      break;
		}
		return $class;
	}

	//-----------
	
	public function writeChildren( $config, $option, $database, $resource, $children, $live_site, $id=0, $active=0, $pid=0, $fsize=0 ) 
	{
	    $juser =& JFactory::getUser();
		$out = '';
		$blorp = '';
		if ($children != NULL) {
			$out .= '<ul>'."\n";
				$base = $config->get('uploadpath');
				foreach ($children as $child) 
				{
					if ($child->access == 0 || ($child->access == 1 && !$juser->get('guest'))) {

						$ftype = ResourcesHtml::getFileExtension($child->path);
						
						$url = ResourcesHtml::processPath($option, $child, $pid);
						
						$class = '';
						if ($child->standalone == 1) {
							$liclass = ' class="html"';
							$title = stripslashes($child->title);
						} else {
							switch ($child->type)
							{
								case 70:
									$liclass = ' class="guide"';
									break;
								case 12:
									$liclass = ' class="html"';
									break;
								case 32:
									$liclass = ' class="swf"';
									$class = ' class="play"';
									break;
								default:
									$liclass = ' class="'.$ftype.'"';
									break;
							}
							//if (stripslashes($child->title) == stripslashes($resource->title)) {
								$title = ($child->logicaltitle) 
								       ? $child->logicaltitle 
									   : stripslashes($child->title);
							/*} else {
								$title = ($child->title) 
								       ? stripslashes($child->title) 
									   : $child->logicaltitle;
							}*/
						}
						
						$child->title = str_replace( '"', '&quot;', $child->title );
						$child->title = str_replace( '&amp;', '&', $child->title );
						$child->title = str_replace( '&', '&amp;', $child->title );
						$child->title = str_replace( '&amp;quot;', '&quot;', $child->title );
						
						// user guide 
						$guide = 0;
						if (strtolower($title) !=  preg_replace('/user guide/', '', strtolower($title))) {
							$liclass = ' class="guide"';
							$guide = 1;
						}
						
						$out .= ' <li'.$liclass.'>';
						$out .= ' '. ResourcesHtml::getFileAttribs( $child->path, $base, $fsize );
						$out .= '<a'.$class.' href="'.$url.'" title="'.stripslashes($child->title).'">'.$title.'</a>';
						$out .= '</li>'."\n";
					}
				}
			$out .= '</ul>'."\n";
		} else {
			$out .= '<p>[ none ]</p>';
		}
		return $out;		
	}

	//-----------
	
	public function getFileExtension($url)
	{
		$type = '';
		$arr  = explode('.',$url);
		$type = end($arr);
		$type = (strlen($type) > 4) ? 'html' : $type;
		$type = (strlen($type) > 3) 
			  ? substr($type, 0, 3)
			  : $type;
		return $type;
	}
	
	//-----------
	
	public function processPath($option, $item, $pid='', $action='')
	{
		$id = $item->id;
		$access = $item->access;
		$type = $item->type;
		$standalone = $item->standalone;
		$path = $item->path;
		
	    $juser =& JFactory::getUser();

		if ($standalone == 1) {
			$url = JRoute::_('index.php?option='.$option.'&id='. $id);
		} else {
			switch ($type) 
			{
				case 12:
					if ($path) {
						// internal link, not a resource
						$url = $path; 
					} else {
						// internal link but a resource
						$url = JRoute::_('index.php?option='.$option.'&id='. $id);
					}
				break;
				
				case 32:
					$url = JRoute::_('index.php?option='.$option.'&id='.$pid.'&resid='.$id.'&task=play');
				break;
				
				default: 
					if ($action == 2) {
						$url = JRoute::_('index.php?option='.$option.'&id='.$pid.'&resid='.$id.'&task=play');
					} else {
						if (strstr($path,'http') || substr($path,0,3) == 'mms') {
							$url = $path; 
						} else {
							$url = JRoute::_('index.php?option='.$option.'&id='.$id.'&task=download&file='.basename($path));
						}
					}
				break;
			}
		}
		return $url;
	}
	
	//-----------

	public function primary_child( $option, $resource, $firstChild, $xact='' )
	{
	    $juser =& JFactory::getUser();
		
		$html = '';
		
		switch ($resource->type)
		{
			case 7:
				// Generate the URL that launches a tool session			
				$lurl ='';
				$database =& JFactory::getDBO();
				$tables = $database->getTableList();
				$table = $database->_table_prefix.'tool_version';

					if (in_array($table,$tables)) {
						if (isset($resource->revision) && $resource->toolpublished) {
						
							$sess = $resource->tool ? $resource->tool : $resource->alias.'_r'.$resource->revision;
							$v = (!isset($resource->revision) or $resource->revision=='dev') ?  'test' : $resource->revision;
							$lurl = 'index.php?option=com_tools&app='.$resource->alias.'&task=invoke&version='.$v;
						} elseif (!isset($resource->revision) or $resource->revision=='dev') { // serve dev version
							$lurl = 'index.php?option=com_tools&app='.$resource->alias.'&task=invoke&version=dev';
						}
					} else {
						$lurl = 'index.php?option=com_tools&task=invoke&app='.$resource->alias;
					}
					
	
					if ((isset($resource->revision) && $resource->toolpublished) or !isset($resource->revision)) { // dev or published tool
						//if ($juser->get('guest')) { 
							// Not logged-in = show message
							//$html .= ResourcesHtml::primaryButton('launchtool disabled', $lurl, 'Launch Tool');
							//$html .= ResourcesHtml::warning( 'You must <a href="'.JRoute::_('index.php?option=com_login').'">log in</a> before you can run this tool.' )."\n";
						//} else {
							$pop = ($juser->get('guest')) ? ResourcesHtml::warning(JText::_('You must login before you can run this tool.')) : '';
							$pop = ($resource->revision =='dev') ? ResourcesHtml::warning(JText::_('Warning: This tool version is under development and may not be run until it is installed.')) : $pop;
							$html .= ResourcesHtml::primaryButton('launchtool', $lurl, JText::_('Launch Tool'), '', '', '', 0, $pop );
						//}
					} else { // tool unpublished
						$pop   = ResourcesHtml::warning(JText::_('This tool version is unpublished and cannot be run. If you would like to have this version staged, you can put a request through HUB Support.'));
						$html .= ResourcesHtml::primaryButton('link_disabled', '', 'Launch Tool', '', '', '', 1, $pop);
						//$html .= ResourcesHtml::warning( $pop )."\n";
					}
				break;
			
			case 4:
				// write primary button and downloads for a Learning Module
				$html .= ResourcesHtml::primaryButton('', JRoute::_('index.php?option=com_resources&id='.$resource->id.'&task=play'), 'Start learning module');
			break;
						
			case 6:
			case 8:
			case 31:
			case 2:
				// do nothing
				$mesg  = JText::_('View').' ';
				$mesg .= $resource->type == 6 ? 'Course Lectures' : '';
				$mesg .= $resource->type == 2 ? 'Workshop ' : '';
				$mesg .= $resource->type == 6 ? '' : 'Series';
				$html .= ResourcesHtml::primaryButton('download', JRoute::_('index.php?option=com_resources&id='.$resource->id).'#series', $mesg, '', $mesg, '');
			break;
						
			default:
				$firstChild->title = str_replace( '"', '&quot;', $firstChild->title );
				$firstChild->title = str_replace( '&amp;', '&', $firstChild->title );
				$firstChild->title = str_replace( '&', '&amp;', $firstChild->title );
		
				$mediatypes = array('11','20','34','19','37','32','15','40','41','15');
				$downtypes = array('60','59','57','55');
				$class = '';			
				if (in_array($firstChild->logicaltype,$downtypes)) {
					$mesg  = 'Download';
					$class = 'download';
				} elseif (in_array($firstChild->type,$mediatypes)) {
					$mesg  = 'View Presentation';
					$mediatypes = array('32','26');
					if (in_array($firstChild->type,$mediatypes)) {
						$class = 'play';
					}
				} else {
					$mesg  = 'Download';
					$class = 'download';
				}
				
				if ($firstChild->standalone == 1) {
					$mesg  = 'View Resource';
					$class = ''; //'play';
				}
				
				if (substr($firstChild->path, 0, 7) == 'http://' 
				 || substr($firstChild->path, 0, 8) == 'https://'
				 || substr($firstChild->path, 0, 6) == 'ftp://'
				 || substr($firstChild->path, 0, 9) == 'mainto://'
				 || substr($firstChild->path, 0, 9) == 'gopher://'
				 || substr($firstChild->path, 0, 7) == 'file://'
				 || substr($firstChild->path, 0, 7) == 'news://'
				 || substr($firstChild->path, 0, 7) == 'feed://'
				 || substr($firstChild->path, 0, 6) == 'mms://') {
					$mesg  = 'View Link';
				}
								
				// IF (not a simulator) THEN show the first child as the primary button
				if ($firstChild->access==1 && $juser->get('guest')) { 
					// first child is for registered users only and the visitor is not logged in
					$pop  = '<p class="warning">This resource requires you to log in before you can proceed with the download.</p>'."\n";
					$html .= ResourcesHtml::primaryButton($class.' disabled', JRoute::_('index.php?option=com_login'), $mesg, '', '', '', '', $pop);
					//$html .= t.'<p class="warning" style="clear: none;">You must <a href="'.JRoute::_('index.php?option=com_login').'">log in</a> before you can download.</p>'."\n";
				} else {
					$child_params =& new JParameter( $firstChild->params );
					$link_action = $child_params->get( 'link_action', '' );

					$url = ResourcesHtml::processPath($option, $firstChild, $resource->id, $link_action);
					
					//$class .= ($firstChild->type == 32) ? ' breeze' : '';
					
					$attribs =& new JParameter( $firstChild->attribs );
					$width  = $attribs->get( 'width', '' );
					$height = $attribs->get( 'height', '' );
					
					$action = '';
					if ($link_action == 1) {
						$action = 'rel="external"';
					} elseif ($link_action == 2) {
						$w = (intval($width) > 0) ? intval($width) : 400;
						$h = (intval($height) > 0) ? intval($height) : 400;
						//$action = 'onclick="popupWindow(\''.$url.'\', \''.$firstChild->title.'\', '.$w.', '.$h.', \'auto\');"';
						$mesg  = 'View Resource';
						//$class .= ' popup';
					}
					
					if (intval($width) > 0 && intval($height) > 0) {
						$class .= ' '.($width + 20).'x'.($height + 60);
					}
						
					$xtra = '';
					//if ($firstChild->type == 13 || $firstChild->type == 15 || $firstChild->type == 33) {
						$xtra = ' '. ResourcesHtml::getFileAttribs( $firstChild->path );
					//}
					
					if ($xact) {
						$action = $xact;
					}
					
					$html .= ResourcesHtml::primaryButton($class, $url, $mesg, $xtra, $firstChild->title, $action);
				}
			break;
		}
		
		return $html;
	}
	
	//-----------

	public function primaryButton($class, $href, $msg, $xtra='', $title='', $action='', $disabled=false, $pop = '')
	{
		$title = htmlentities($title, ENT_QUOTES);
		$out = '';
		
		if ($disabled) {
			$out .= "\t".'<p id="primary-document"><span class="'.$class.'" >'.$msg.'</span></p>'."\n";
		} else {
			$out .= "\t".'<p id="primary-document"><a class="'.$class.'" href="'.$href.'" title="'.$title.'" '.$action.'>'.$msg;
			$out .= $xtra ? $xtra : '';
			$out .= '</a></p>'."\n";
		}
		
		if ($pop) {
			$out .= "\t".'<div id="primary-document_pop"><div>'.$pop.'</div></div>'."\n";
		}
		
		return $out;
	}

	//-----------

	public function getFileAttribs( $path, $base_path='', $fsize=0 )
	{
		// Return nothing if no path provided
		if (!$path) {
			return '';
		}
		
		if ($base_path) {
			// Strip any trailing slash
			if (substr($base_path, -1) == DS) { 
				$base_path = substr($base_path, 0, strlen($base_path) - 1);
			}
			// Ensure a starting slash
			if (substr($base_path, 0, 1) != DS) { 
				$base_path = DS.$base_path;
			}
		}
		
		// Ensure a starting slash
		if (substr($path, 0, 1) != DS) { 
			$path = DS.$path;
			if (substr($path, 0, strlen($base_path)) == $base_path) {
				// Do nothing
			} else {
				$path = $base_path.$path;
			}
		}
		
		$path = JPATH_ROOT.$path;

		$file_name_arr = explode('.',$path);
	    $type = end($file_name_arr);
		if (strlen($type) > 4) {
			$type = 'HTML';
		}
		$type = strtoupper($type);
		
		$fs = '';
		
		// Get the file size if the file exist
		if (file_exists( $path )) {
			$fs = filesize( $path );
		}
		
		$html  = '<span class="caption">('.$type;
		if ($fs) {
			switch ($type)
			{
				case 'HTM':
				case 'HTML':
				case 'PHP':
				case 'ASF':
				case 'SWF': $fs = ''; break;
				default: 
					$fs = ($fsize) ? $fs : ResourcesHtml::formatsize($fs); 
					break;
			}
		
			$html .= ($fs) ? ', '.$fs : '';
		}
		$html .= ')</span>';
		
		return $html;
	}

	//-----------

	public function formatsize($file_size) 
	{
		if ($file_size >= 1073741824) {
			$file_size = round($file_size / 1073741824 * 100) / 100 . ' <abbr title="gigabytes">Gb</abbr>';
		} elseif ($file_size >= 1048576) {
			$file_size = round($file_size / 1048576 * 100) / 100 . ' <abbr title="megabytes">Mb</abbr>';
		} elseif ($file_size >= 1024) {
			$file_size = round($file_size / 1024 * 100) / 100 . ' <abbr title="kilobytes">Kb</abbr>';
		} else {
			$file_size = $file_size . ' <abbr title="bytes">b</abbr>';
		}
		return $file_size;
	}

	//-------------------------------------------------------------
	// Results
	//-------------------------------------------------------------

	public function writeResults( &$database, &$lines, $show_edit=0, $show_date=3 ) 
	{
		$juser =& JFactory::getUser();

		$config =& JComponentHelper::getParams( 'com_resources' );
		
		$html  = '<ol class="resources results">'."\n";
		foreach ($lines as $line)
		{
			// Instantiate a helper object
			$helper = new ResourcesHelper($line->id, $database);
			$helper->getContributors();
			$helper->getContributorIDs();
				
			// Determine if they have access to edit
			if (!$juser->get('guest')) {
				if ((!$show_edit && $line->created_by == $juser->get('id')) 
				|| in_array($juser->get('id'), $helper->contributorIDs)) {
					$show_edit = 2;
				}
			}
				
			// Get parameters
			$params = clone($config);
			$rparams =& new JParameter( $line->params );
			$params->merge( $rparams );
			
			// Instantiate a new view
			$view = new JView( array('name'=>'browse','layout'=>'item') );
			$view->option = 'com_resources';
			$view->config = $config;
			$view->params = $params;
			$view->juser = $juser;
			$view->helper = $helper;
			$view->line = $line;
			$view->show_edit = $show_edit;

			// Set the display date
			switch ($show_date) 
			{
				case 0: $view->thedate = ''; break;
				case 1: $view->thedate = JHTML::_('date', $line->created, '%d %b %Y');    break;
				case 2: $view->thedate = JHTML::_('date', $line->modified, '%d %b %Y');   break;
				case 3: $view->thedate = JHTML::_('date', $line->publish_up, '%d %b %Y'); break;
			}
			
			$html .= $view->loadTemplate();
		}
		$html .= '</ol>'."\n";
		
		return $html;
	}
}

