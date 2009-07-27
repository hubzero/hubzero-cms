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

include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'resources.tags.php' );

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

	public function archive( $msg, $tag='p' )
	{
		return '<'.$tag.' class="archive">'.$msg.'</'.$tag.'>'.n;
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
		$out .= ($class) ? ' class="'.$class.'">'.n : '>'.n;
		foreach ($array as $avalue => $alabel) 
		{
			$selected = ($avalue == $value || $alabel == $value)
					  ? ' selected="selected"'
					  : '';
			$out .= ' <option value="'.$avalue.'"'.$selected.'>'.$alabel.'</option>'.n;
		}
		$out .= '</select>'.n;
		return $out;
	}

	//-----------

	public function tableRow($h,$c='')
	{
		$html  = t.'  <tr>'.n;
		$html .= t.'   <th>'.$h.'</th>'.n;
		$html .= t.'   <td>';
		$html .= ($c) ? $c : '&nbsp;';
		$html .= '</td>'.n;
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
					$link = 'index.php?option=com_contribtool'.a.'task=start'.a.'step=1'.a.'rid='. $id;
				} else {
					$link = JRoute::_('index.php?option=com_contribute').'?step=1'.a.'id='. $id;
				}
				$txt = JText::_('EDIT');
				break;
			/*case 'tag':
				$link = 'index.php?option=com_tags'.a.'task=edittags'.a.'id='. $id;
				$txt  = JText::_('EDIT').' tags';
				break;*/
			default:
				$txt  = '';
				$link = '';
				break;
		}
		
		return ' <a class="edit button" href="'. $link .'" title="'. $txt .'">'. $txt .'</a>';
	}

	//-----------
	
	public function shortenText($text, $chars=300, $p=1) 
	{
		$text = strip_tags($text);
		$text = str_replace(n,' ',$text);
		$text = str_replace(r,' ',$text);
		$text = str_replace(t,' ',$text);
		$text = str_replace('   ',' ',$text);
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
	
	public function writeVersions( $option, $resource, $versions, $active, $rid, $toolname) 
	{
		$out = '';
		if ($versions != NULL) {
			$out .= '<ul>'.n;
			$sef = JRoute::_('index.php?option='.$option.a.'alias='.$toolname);
			$i = 0;
			foreach ($versions as $v) 
			{
				$i++;
				if($v->state==3 && $active=='dev') {
					// display dev version as current
					$out .= t.'<li';
					$out .= ' class="currentversion"';
					$out .= '>';
					$out .= $v->version.' ('.JText::_('in development').')';
					$out .= t.'</li>'.n;
				
				}
				if ($i < 10 && $v->state!=3) { // limit to 5 recent versions
					$publishflag = ($v->state == 1) ? JText::_('PUBLISHED') : JText::_('UNPUBLISHED');
					$out .= t.'<li';
					if ($v->revision == $active) {
						$out .= ' class="currentversion"';
					}
					$out .= '>';
					if ($v->revision != $active) {
						$out .='<a href="'.$sef.'?rev='. $v->revision.'">';
					}
					$out .= $v->version.' ('.$publishflag.')';
					if ($v->revision != $active ) {
						$out .='</a>'.n;
					}
					
					// source code download
					if ($v->revision == $active) {
						if (isset($resource->toolsource) && $resource->toolsource == 1 && isset($resource->tool)) { // open source
							if ($resource->taravailable) {					
								$out .= ' <span class="downloadcode"><a href="index.php/'.$resource->tarname.'?option='.$option.a.'task=sourcecode'.a.'tool='.$resource->tool.'">'.JText::sprintf('DOWNLOAD_SOURCE', $resource->version).'</a></span>'.n;
							} else { // tarball is not there
								$out .= ' <span class="downloadcode"><span>'.JText::_('SOURCE_UNAVAILABLE').'</span></span>'.n;
							}
						}
					}
					
					$out .= t.'</li>'.n;
				}
			}
			if (count($versions) > 5) { // show 'more' link
				$out .= t.'<li><a href="'.$sef.DS.'versions">'.JText::_('MORE_VERSIONS').'</a></li>'.n;
			}
			$out .= t.'</ul>'.n;
		}
		return $out;
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
		//return $base.DS.$dir_id;
	}
	
	//-----------
	
	public function screenshots($id, $created, $upath, $wpath, $versionid=0, $sinfo=array(), $path='' )
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
			
			if (is_file(JPATH_ROOT.$upath.$path.DS.$tn)) {
			
				if (strtolower(end($images[$i]['type'])) == 'swf') {
					$g++;
					$title = (isset($images[$i]['title']) && $images[$i]['title']!='' ) ? $images[$i]['title'] : JText::_('DEMO').' #'.$g;
					$els .= ' <li><a class="popup" href="'.$wpath.$path.DS.$images[$i]['img'].'" title="'.$title.'">';
					$els .= '<img src="'.$wpath.$path.DS.$tn.'" alt="'.$title.'" /></a></li>'.n;
				} else {
					$k++;
					$title = (isset($images[$i]['title']) && $images[$i]['title']!='' )  ? $images[$i]['title']: JText::_('SCREENSHOT').' #'.$k;
					$els .= ' <li><a rel="lightbox" href="'.$wpath.$path.DS.$images[$i]['img'].'" title="'.$title.'">';
					$els .= '<img src="'.$wpath.$path.DS.$tn.'" alt="'.$title.'" /></a></li>'.n;
				}
			}
		}
		
		if ($els) {
			$html .= '<ul class="screenshots">'.n;
			$html .= $els;
			$html .= '</ul>'.n;
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
	
	public function metadata($params, $ranking, $statshtml, $id, $sections, $xtra='')
	{
		$html = '';
		if ($params->get('show_ranking')) {
			$html .= ResourcesHtml::ranking( $ranking, $statshtml, $id, '' );
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
		$html  = ResourcesHtml::hed(3,JText::_('SUPPORTING_DOCUMENTS')).n;
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
			return t.'<p class="'.$cls.' license">Licensed under '.$txt.' according to <a rel="license" href="'.$lnk.'">this deed</a>.</p>'.n;
		} else {
			return '';
		}
	}
	
	//-------------------------------------------------------------
	// Browser
	//-------------------------------------------------------------
	
	public function browser($level, $bits) 
	{
		$html = '';
		switch ($level)
		{
			case 1:
				$tags = $bits['tags'];
				$tg = $bits['tg'];
				$tg2 = $bits['tg2'];
				$type = $bits['type'];
				$id = $bits['id'];
				$d = 0;
				
				if ($tg2) {
					$html .= ResourcesHtml::hed(3, JText::_('TAG').' + '.$tg2);
				} else {
					$html .= ResourcesHtml::hed(3, JText::_('TAG'));
				}
				$html .= '<ul id="ultags">';
				if (!$tg2) {
					$html .= '<li><a id="col1_all" class="';
					if ($tg == '') {
						$html .= 'open';
					}
					$html .= '" href="javascript:HUB.TagBrowser.nextLevel(\''.$type.'\',\'\',\'\',2,\'col1_all\',\''.$id.'\');">[ All ]</a></li>';
				}
				$lis = '';
				$i = 0;
				foreach ($tags as $tag)
				{
					$i++;
					$li  = ' <li';
					if ($bits['supportedtag'] && $type == 7 && $tag->tag == $bits['supportedtag']) {
						$li .= ' class="supported"';
						$i = 0;
					}
					$li .= '><a id="col1_'.$tag->tag.'" class="';
					if ($tg == $tag->tag) { 
						$li .= 'open'; 
						$d = $i;
					}
					/*if ($bits['supportedtag'] && $type == 7 && $tag->tag == $bits['supportedtag']) {
						$tag->raw_tag = '[ '.$tag->raw_tag.' ]';
					}*/
					
					$li .= '" href="javascript:HUB.TagBrowser.nextLevel(\''.$type.'\',\''.$tag->tag.'\',\''.$tg2.'\',2,\'col1_'.$tag->tag.'\',\''.$id.'\');">'.$tag->raw_tag.' ('.$tag->ucount.')</a></li>';
					
					if ($bits['supportedtag'] && $type == 7 && $tag->tag == $bits['supportedtag']) {
						$html .= $li;
					} else {
						$lis .= $li;
					}
				}
				if ($tg == '') {
					$tg = 'all';
				}
				$html .= $lis;
				/*$html .= '</ul><script type="text/javascript">var objDiv = document.getElementById("ultags");var dist = document.getElementById("col1_'.$tg.'").offsetHeight; objDiv.scrollTop = ((dist * '.$d.') - dist);</script>';*/
				$html .= '</ul><input type="hidden" name="atg" id="atg" value="'.$tg.'" /><input type="hidden" name="d" id="d" value="'.$d.'" />';
			break;
			
			case 2:			
				$tools = $bits['tools'];
				$typetitle = $bits['typetitle'];
				$type = $bits['type'];
				$rt = $bits['rt'];
				
				$sortbys = array(
					'date'=>JText::_('Sort by:').' '.JText::_('DATE'),
					'title'=>JText::_('Sort by:').' '.JText::_('TITLE'),
					'ranking'=>JText::_('Sort by:').' '.JText::_('RANKING')
				);
				if ($type == 7) {
					$sortbys['users'] = JText::_('Sort by:').' '.JText::_('USERS');
					$sortbys['jobs'] = JText::_('Sort by:').' '.JText::_('JOBS');
				}
				
				//$html .= ResourcesHtml::hed(3,$typetitle);
				$html .= ResourcesHtml::hed(3,JText::_('Resources').' '.ResourcesHtml::formSelect('sortby', $sortbys, $bits['sortby'], '" onchange="javascript:HUB.TagBrowser.changeSort();"'));
				$html .= '<ul id="ulitems">';
				if ($tools && count($tools) > 0) {
					//$database =& JFactory::getDBO();
					foreach ($tools as $tool)
					{
						$tool->title = ResourcesHtml::shortenText($tool->title, 40, 0);
						
						$supported = null;
						if ($bits['supportedtag'] && $type == 7) {
							//$database->setQuery( "SELECT COUNT(*) FROM #__tags_object AS ta, #__tags AS t WHERE ta.tagid=t.id AND t.tag='".$bits['supportedtag']."' AND ta.tbl='resources' AND ta.objectid=".$tool->id );
							$supported = $rt->checkTagUsage( $bits['supportedtag'], $tool->id );
						}
						
						$html .= '<li ';
						if ($bits['supportedtag'] && $type == 7 && ($bits['tag'] == $bits['supportedtag'] || $supported)) {
							$html .= 'class="supported" ';
						}
						$html .= '><a id="col2_'.$tool->id.'" href="javascript:HUB.TagBrowser.nextLevel(\''.$type.'\',\''.$tool->id.'\',\'\',3,\'col2_'.$tool->id.'\',\'\');">'.$tool->title.'</a></li>';
					}
				} else {
					$html .= '<li><span>'.JText::_('No resources found.').'</span></li>';
				}
				$html .= '</ul>';
			break;
			
			case 3:
				$resource = $bits['resource'];
				$helper = $bits['helper'];
				$sef = $bits['sef'];
				$sections = $bits['sections'];
				$primary_child = (isset($bits['primary_child'])) ? $bits['primary_child'] : '';
				$params = $bits['params'];
				$rt = $bits['rt'];
				$config = $bits['config'];
			
				$statshtml = '';
				if ($params->get('show_ranking')) {
					$helper->getLastCitationDate();
					
					$database =& JFactory::getDBO();
					
					if ($resource->type == 7) {
						$stats = new ToolStats($database, $resource->id, $resource->type, $resource->rating, $helper->citationsCount, $helper->lastCitationDate);
					} else {
						$stats = new AndmoreStats($database, $resource->id, $resource->type, $resource->rating, $helper->citationsCount, $helper->lastCitationDate);
					}

					$statshtml = $stats->display();
				}
				
				$html .= ResourcesHtml::hed(3,JText::_('INFO'));
				$html .= '<ul id="ulinfo">'.n;
				$html .= t.'<li>'.n;
				$html .= t.t.ResourcesHtml::hed(4,'<a href="'.$sef.'">'.ResourcesHtml::encode_html($resource->title).'</a>').n;
				$html .= t.t.'<p>'.ResourcesHtml::shortenText(stripslashes($resource->introtext), 400, 0).' &nbsp; <a href="'.$sef.'">Learn more &rsaquo;</a></p>'.n;

				if ($helper->firstChild || $resource->type == 7) {
					$html .= $primary_child;
				}
				
				$supported = null;
				if ($bits['supportedtag'] && $resource->type == 7) {
					$supported = $rt->checkTagUsage( $bits['supportedtag'], $resource->id );
				}
				$xtra = '';
				if ($bits['supportedtag'] && $resource->type == 7 && $supported) {
					include_once(JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.tag.php');
					$tag = new TagsTag( $database );
					$tag->loadTag($config->get('supportedtag'));

					$sl = $config->get('supportedlink');
					if ($sl) {
						$link = $sl;
					} else {
						$link = JRoute::_('index.php?option=com_tags'.a.'tag='.$tag->tag);
					}

					$xtra = '<p class="supported"><a href="'.$link.'">'.$tag->raw_tag.'</a></p>';
				}
				
				if ($params->get('show_metadata')) {
					$html .= ResourcesHtml::metadata($params, $resource->ranking, $statshtml, $resource->id, $sections, $xtra);
				}
				$html .= t.'<input type="hidden" name="rid" id="rid" value="'.$resource->id.'" /></li>'.n;
				$html .= '</ul><script type="text/javascript">HUB.Base.popups();HUB.Base.launchTool();</script>'.n;
			break;
		}
		return $html;
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
		$html  = '<div id="sub-menu">'.n;
		$html .= t.'<ul>'.n;
		$i = 1;
		foreach ($cats as $cat)
		{
			$name = key($cat);
			if ($name != '') {
				if ($alias) {
					$url = JRoute::_('index.php?option='.$option.a.'alias='.$alias.a.'active='.$name);
				} else {
					$url = JRoute::_('index.php?option='.$option.a.'id='.$id.a.'active='.$name);
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
				$html .= t.t.'<li id="sm-'.$i.'"';
				$html .= (strtolower($name) == $active) ? ' class="active"' : '';
				$html .= '><a class="tab" rel="'.$name.'" href="'.$url.'"><span>'.$cat[$name].'</span></a></li>'.n;
				$i++;	
			}
		}
		$html .= t.'</ul>'.n;
		$html .= t.'<div class="clear"></div>'.n;
		$html .= '</div><!-- / #sub-menu -->'.n;
		
		return $html;
	}
	
	//-----------
	
	public function title( $option, $resource, $params, $show_edit, $config=null ) 
	{
		switch ($resource->published) 
		{
			case 1: $txt = ''; break;
			case 2: $txt = '<span>['.JText::_('DRAFT_EXTERNAL').']</span> '; break;
			case 3: $txt = '<span>['.JText::_('PENDING').']</span> '; break;
			case 4: $txt = '<span>['.JText::_('DELETED').']</span> '; break;
			case 5: $txt = '<span>['.JText::_('DRAFT_INTERNAL').']</span> '; break;
			case 0; $txt = '<span>['.JText::_('UNPUBLISHED').']</span> '; break;
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
		
		$html  = ResourcesHtml::hed(2,$txt).n;
		$html .= '<p>'.JText::_('POSTED').' ';
		$html .= ($thedate) ? JHTML::_('date', $thedate, '%d %b %Y').' ' : '';
		$html .= JText::_('IN').' <a href="'.JRoute::_('index.php?option='.$option.a.'type='.$typenorm).'">'.$resource->getTypeTitle().'</a></p>'.n;
		
		/*$supported = null;
		if ($resource->type == 7) {
			$database =& JFactory::getDBO();
			$rt = new ResourcesTags( $database );
			$supported = $rt->checkTagUsage( $config->get('supportedtag'), $resource->id );
		}
		
		if ($supported) {
			include_once(JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.tag.php');
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
	
	public function play( $database, $resource, $helper, $resid, $activechild, $fsize, $no_html=0 ) 
	{
		$helper->getChildren();
		
		$children = $helper->children;
		
		$html = '';
		if ($resource->type == 4) {
			$parameters =& new JParameter( $resource->params );
			
			// We're going through a learning module
			$html .= '<div class="aside">'.n;
			$html .= ResourcesHtml::lmChildren( $database, $children, $resource->id, $resid, $fsize );
			$html .= ResourcesHtml::license( $parameters->get( 'license', '' ) );
			$html .= '</div><!-- / .aside -->'.n;
			$html .= '<div class="subject">'.n;

			// Playing a learning module
			if (is_object($activechild)) {
				if (!$activechild->path) {
					// Output just text
					$html .= ResourcesHtml::hed(3,$activechild->title);
					//$html .= $this->KL_PHP($activechild->fulltext);
					$html .= stripslashes($activechild->fulltext);
				} else {
					// Output content in iFrame
					$html .= '<iframe src="'.$activechild->path.'" width="97%" height="500" name="lm_resource" frameborder="0" bgcolor="white"></iframe>'.n;
				}
			}

			$html .= '</div><!-- / .subject -->'.n;
			$html .= '<div class="clear"></div>'.n;
		} else {
			$url = $activechild->path;
			
			// Get some attributes
			$attribs =& new JParameter( $activechild->attribs );
			$width  = $attribs->get( 'width', '' );
			$height = $attribs->get( 'height', '' );
			
			$type = ResourcesHtml::getFileExtension($url);

			$width = (intval($width) > 0) ? $width : 0;
			$height = (intval($height) > 0) ? $height : 0;
			
			if (is_file(JPATH_ROOT.$url)) {
				if (strtolower($type) == 'swf') {
					$height = '400px';
					if ($no_html) {
						$height = '99%';
					}
					$html .= t.t.t.'<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,65,0" width="100%" height="'.$height.'" id="SlideContent" VIEWASTEXT>'.n;
					$html .= t.t.t.' <param name="movie" value="'. $url .'" />'.n;
					$html .= t.t.t.' <param name="quality" value="high" />'.n;
					$html .= t.t.t.' <param name="menu" value="false" />'.n;
					$html .= t.t.t.' <param name="loop" value="false" />'.n;
					$html .= t.t.t.' <param name="scale" value="showall" />'.n;
					$html .= t.t.t.' <embed src="'. $url .'" menu="false" quality="best" loop="false" width="100%" height="'.$height.'" scale="showall" name="SlideContent" align="" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" swLiveConnect="true"></embed>'.n;
					$html .= t.t.t.'</object>'.n;
				} else {
					$html .= t.t.t.'<applet code="Silicon" archive="'. $url .'" width="';
					$html .= ($width > 0) ? $width : '';
					$html .= '" height="';
					$html .= ($height > 0) ? $height : '';
					$html .= '">'.n;
					if ($width > 0) {
						$html .= t.t.t.' <param name="width" value="'. $width .'" />'.n;
					}
					if ($height > 0) {
						$html .= t.t.t.' <param name="height" value="'. $height .'" />'.n;
					}
					$html .= t.t.t.'</applet>'.n;
				}
			} else {
				$html .= ResourcesHtml::error( JText::_('FILE_NOT_FOUND') ).n;
			}
		}
		
		return $html;
	}
	
	//-----------
	
	public function about( $database, $show_edit, $usersgroups, $resource, $helper, $config, $sections, $thistool, $curtool, $alltools, $revision, $params, $attribs, $option, $fsize ) 
	{
		$xhub =& XFactory::getHub();
		
		//if ($resource->type != 31 || $resource->type != 2 || !$thistool) {
		if (!$thistool) {
			$helper->getChildren();
		}
		
		if ($resource->alias) {
			$url = 'index.php?option='.$option.a.'alias='.$resource->alias;
			// If tool version page is requested
			/*if ($thistool) {
				$url .= a.'v='.$thistool->revision;
			}*/
		} else {
			$url = 'index.php?option='.$option.a.'id='.$resource->id;
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
		$maintext = str_replace('&amp;','&',$maintext);
		$maintext = str_replace('&','&amp;',$maintext);
		$maintext = str_replace('<blink>','',$maintext);
		$maintext = str_replace('</blink>','',$maintext);
		
		if ($resource->type == 7) {
			//if (strlen($maintext) != strlen(strip_tags($maintext)){
			if (preg_match("/([\<])([^\>]{1,})*([\>])/i", $maintext)) {
				// Do nothing
			} else {
				// Get the wiki parser and parse the full description
				ximport('wiki.parser');
				$p = new WikiParser( $resource->title, $option, 'resources'.DS.$resource->id, 'resources', $resource->id, $config->get('uploadpath'));
				$maintext = $p->parse( n.stripslashes($maintext) );
			}
		}
		
		// Extract the matches to their own variables
		//extract($allnbtags);
		
		$html  = '<div class="aside">'.n;
		// Show resource ratings
		if (!$thistool) {
			$statshtml = '';
			
			//$xhub =& XFactory::getHub();
			//if ($xhub->getCfg('hubShowRanking')) {
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
					include_once(JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.tag.php');
					$tag = new TagsTag( $database );
					$tag->loadTag($config->get('supportedtag'));

					$sl = $config->get('supportedlink');
					if ($sl) {
						$link = $sl;
					} else {
						$link = JRoute::_('index.php?option=com_tags'.a.'tag='.$tag->tag);
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
			$html .= ResourcesHtml::warning( $ghtml ).n;
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
						//$html .= ResourcesHtml::allVersions($versionhtml);
						
						$vh  = ResourcesHtml::hed(3, JText::_('AVAILABLE_VERSIONS') ).n;
						$vh .= ResourcesHtml::writeVersions( $option, $resource, $alltools, $resource->revision, $resource->id, $resource->alias);

						$html .= ResourcesHtml::div($vh, 'versions');
					}
						
					if (count($helper->children) >= 1 && !(trim($helper->firstChild->introtext) == 'Launch Tool') && !$thistool) {
						$dls = ResourcesHtml::writeChildren( $config, $option, $database, $resource, $helper->children, $live_site, '', '', $resource->id, $fsize );

						$html .= ResourcesHtml::supportingDocuments($dls);
					}
					
					// Open/closed source
					if (isset($resource->toolsource) && $resource->toolsource == 1 && isset($resource->tool)) { // open source
						$html .= '<p class="opensource license">This tool is <a href="http://www.opensource.org/docs/definition.php" rel="external">open source</a>, according to <a class="popup" href="index.php?option=com_resources'.a.'task=license'.a.'tool='.$resource->tool.a.'no_html=1">this license</a>.</p>'.n;	
					} elseif (isset($resource->toolsource) && !$resource->toolsource) { // closed source, archive page
						$html .= '<p class="closedsource license">'.JText::_('TOOL_IS_CLOSED_SOURCE').'</p>'.n;
					}
				break;
					
				case 4:
					// Write primary button and downloads for a Learning Module
					$html .= ResourcesHtml::primary_child( $option, $resource, $helper->firstChild, '' );
					$dls = ResourcesHtml::writeDownloads( $database, $resource->id, $option, $config, $fsize );
					if ($dls) {
						$html .= ResourcesHtml::supportingDocuments($dls);
					}
				break;
					
				case 6:
					// If more than one child the show the list of children
					$helper->getChildren( $resource->id, 0, 'no' );
					$children = $helper->children;
					
					if ($children) {
						$dls = ResourcesHtml::writeChildren( $config, $option, $database, $resource, $children, $live_site, '', '', $resource->id, $fsize );
					
						$html .= ResourcesHtml::supportingDocuments($dls);
					}
				
					$html .= t.t.'<p><a class="feed" id="resource-audio-feed" href="'. $xhub->getCfg('hubLongURL') .'/resources/'.$resource->id.'/feed.rss?format=audio">'.JText::_('Audio podcast').'</a><br />'.n;
					$html .= t.t.'<a class="feed" id="resource-video-feed" href="'. $xhub->getCfg('hubLongURL') .'/resources/'.$resource->id.'/feed.rss?format=video">'.JText::_('Video podcast').'</a></p>'.n;
				break;
				
				case 8:
				case 31:
				case 2:
					$html .= t.t.'<p><a class="feed" id="resource-audio-feed" href="'. $xhub->getCfg('hubLongURL') .'/resources/'.$resource->id.'/feed.rss?format=audio">'.JText::_('Audio podcast').'</a><br />'.n;
					$html .= t.t.'<a class="feed" id="resource-video-feed" href="'. $xhub->getCfg('hubLongURL') .'/resources/'.$resource->id.'/feed.rss?format=video">'.JText::_('Video podcast').'</a></p>'.n;
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

		$html .= '</div><!-- / .aside -->'.n;
		$html .= '<div class="subject">'.n;
		
		// Show archive message
		if ($thistool && $revision!='dev') {
			$msg  = '<strong>'.JText::_('ARCHIVE').'</strong><br />';
			$msg .= JText::_('ARCHIVE_MESSAGE');
			if ($resource->version) {
				$msg .= ' <br />'.JText::_('THIS_VERSION').': '.$resource->version.'.';
			}
			if (isset($resource->curversion) && $resource->curversion) {
				$msg .= ' <br />'.JText::_('LATEST_VERSION').': <a href="'.$sef.'?rev='.$curtool->revision.'">'.$resource->curversion.'</a>.';
			}
			
			$html .= ResourcesHtml::archive( $msg ).n;
		}
		
		$html .= t.'<table class="resource" summary="'.JText::_('RESOURCE_TBL_SUMMARY').'">'.n;
		$html .= t.t.'<tbody>'.n;
		
		// Display version specific information
		if ($resource->type == 7 && $alltools) {
			$versiontext = '<strong>';
			if ($revision && $thistool) {
				$versiontext .= $thistool->version.'</strong>';
				if ($resource->revision!='dev') {
					$versiontext .=  ' - '.JText::_('PUBLISHED_ON').' ';
					$versiontext .= ($thistool->released && $thistool->released != '0000-00-00 00:00:00') ? JHTML::_('date', $thistool->released, '%d %b %Y'): JHTML::_('date', $resource->publish_up, '%d %b %Y');
					$versiontext .= ($thistool->unpublished && $thistool->unpublished != '0000-00-00 00:00:00') ? ', '.JText::_('UNPUBLISHED_ON').' '.JHTML::_('date', $thistool->unpublished, '%d %b %Y'): '';
				} else {
					$versiontext .= ' ('.JText::_('IN_DEVELOPMENT').')';
				}
			} else if ($curtool) {
				$versiontext .= $curtool->version.'</strong> - '.JText::_('PUBLISHED_ON').' ';
				$versiontext .= ($curtool->released && $curtool->released != '0000-00-00 00:00:00') ? JHTML::_('date', $curtool->released, '%d %b %Y'): JHTML::_('date', $resource->publish_up, '%d %b %Y');
			}
			
			if ($revision == 'dev') {
				$html .= t.t.t.'<tr class="devversion">'.n;
				$html .= t.t.t.t.'<th>'.JText::_('VERSION').'</th>'.n;
				$html .= t.t.t.t.'<td>'.$versiontext.'</td>'.n;
				$html .= t.t.t.'</tr>'.n;
			} else {
				$html .= ResourcesHtml::tableRow(JText::_('VERSION'), $versiontext);
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
				$html .= ResourcesHtml::tableRow( JText::_('CONTRIBUTORS'), $helper->contributors );
			}
		}
		
		// Display "at a glance"
		if ($resource->type == 7) {
			$html .= ResourcesHtml::tableRow( JText::_('AT_A_GLANCE'), $resource->introtext );
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
					$html .= ResourcesHtml::tableRow( JText::_('SCREENSHOTS'), $shots );
				}
			}
			
			if ($resource->type == 7) {
				$html .= ResourcesHtml::tableRow( JText::_('DESCRIPTION'), $maintext );
			} else {
				$html .= ResourcesHtml::tableRow( JText::_('ABSTRACT'), $maintext );
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
			
			/*if ($poweredby) {
				$html .= ResourcesHtml::tableRow( JText::_('POWERED_BY'), $poweredby);
			}
			
			if ($bio) {
				$html .= ResourcesHtml::tableRow( JText::_('BIOGRAPHY'), $bio );
			}

			if ($credits) {
				$html .= ResourcesHtml::tableRow( JText::_('CREDITS'), $credits );
			}
			
			if ($sponsoredby) {
				$html .= ResourcesHtml::tableRow( JText::_('SPONSORED_BY'), $sponsoredby );
			}
			
			if ($references) {
				if ($publications) {
					$references = $publications.$references;
				}
				$html .= ResourcesHtml::tableRow( JText::_('REFERENCES'), $references );
			}*/
			
			if ($params->get('show_citation')) {
				// Citation instructions
				$helper->getUnlinkedContributors();

				// Build our citation object
				$cite = new stdClass();
				$cite->title = $resource->title;
				$cite->year = JHTML::_('date', $thedate, '%Y');
				if ($alltools && $resource->doi) {
					$cite->location = ' <a href="'.$config->get('aboutdoi').'" title="'.JText::_('ABOUT_DOI').'">DOI</a>: '.$config->get('doi').'r'.$resource->id.'.'.$resource->doi;
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

				$citeinstruct = ResourcesHtml::citation( $option, $cite, $resource->id, $citations, $resource->type, $revision );
				$html .= ResourcesHtml::tableRow( JText::_('CITE_THIS'), $citeinstruct );
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
			$html .= ResourcesHtml::tableRow( JText::_('TIME'),$seminar_time);
		}
		// If the resource had a specific location
		if ($attribs->get( 'location', '' )) {
			$html .= ResourcesHtml::tableRow( JText::_('LOCATION'),$attribs->get( 'location', '' ));
		}
		// Tags
		if (!$thistool && $revision!='dev') {
			if ($params->get('show_assocs')) {
				$helper->getTagCloud( $show_edit );

				$juser =& JFactory::getUser();
				$frm = '';
				if (!$juser->get('guest') && !isset($resource->tagform)) {
					$rt = new ResourcesTags($database);
					$usertags = $rt->get_tag_string( $resource->id, 0, 0, $juser->get('id'), 0, 0 );
					
					$document =& JFactory::getDocument();
					$document->setMetaData('keywords',$rt->get_tag_string( $resource->id, 0, 0, null, 0, 0 ));
					
					JPluginHelper::importPlugin( 'tageditor' );
					$dispatcher =& JDispatcher::getInstance();

					$tf = $dispatcher->trigger( 'onTagsEdit', array(array('tags','actags','',$usertags,'')) );
					
					$frm .= '<form method="post" id="tagForm" action="'.JRoute::_('index.php?option='.$option.a.'id='.$resource->id).'">'.n;
					$frm .= t.'<fieldset>'.n;
					$frm .= t.t.'<label class="tag">'.n;
					$frm .= t.t.t.JText::_('Your tags').': '.n;
					if (count($tf) > 0) {
						$frm .= $tf[0];
					} else {
						//$frm .= t.t.t.'<textarea name="tags" id="tags-men" rows="6" cols="35">'. $usertags .'</textarea>'.n;
						$frm .= t.t.t.'<input type="text" name="tags" id="tags-men" size="30" value="'. $usertags .'" />'.n;
					}
					$frm .= t.t.'</label>'.n;
					$frm .= t.t.'<input type="submit" value="'.JText::_('SAVE').'"/>'.n;
					$frm .= t.t.'<input type="hidden" name="task" value="savetags" />'.n;
					$frm .= t.'</fieldset>'.n;
					$frm .= '</form>'.n;
				}

				if ($helper->tagCloud) {
					$html .= ResourcesHtml::tableRow( JText::_('TAGS'),$helper->tagCloud.$frm);
				}
			}
		}
		$html .= t.' </tbody>'.n;
		$html .= t.'</table>'.n;
		$html .= '</div><!-- / .subject -->'.n;
		$html .= '<div class="clear"></div>'.n;
		$html .= '<input type="hidden" name="rid" id="rid" value="'.$resource->id.'" />'.n;
		
		return $html;
	}

	//-----------

	public function citation( $option, $cite, $id, $citations, $type, $rev='') 
	{
		include_once( JPATH_ROOT.DS.'components'.DS.'com_citations'.DS.'citations.formatter.php' );

		$html  = '<p>'.JText::_('CITATION_INSTRUCTIONS').'</p>'.n;
		$html .= '<ul class="citations results">'.n;
		$html .= $citations;
		$html .= t.'<li>'.n;
		$html .= CitationsFormatter::formatReference($cite);
		if($rev!='dev') {
		$html .= t.t.'<p class="details">'.n;
		$html .= t.t.t.'<a href="index.php?option='.$option.a.'task=citation'.a.'id='.$id.a.'format=bibtex'.a.'no_html=1'.a.'rev='.$rev.'" title="'.JText::_('DOWNLOAD_BIBTEX_FORMAT').'">BibTex</a> <span>|</span> '.n;
		$html .= t.t.t.'<a href="index.php?option='.$option.a.'task=citation'.a.'id='.$id.a.'format=endnote'.a.'no_html=1'.a.'rev='.$rev.'" title="'.JText::_('DOWNLOAD_ENDNOTE_FORMAT').'">EndNote</a>'.n;
		$html .= t.t.'</p>'.n;
		}
		$html .= t.'</li>'.n;
		$html .= '</ul>'.n;
		/*if ($type == 7) {
			$html .= '<p>'.JText::_('In addition, we would appreciate it if you would add the following acknowledgment to your publication:').'</p>'.n;
			$html .= '<ul class="citations results">'.n;
			$html .= t.'<li>'.n;
			$html .= t.t.'<p>'.JText::_('Simulation services for results presented here were provided by the Network for Computational Nanotechnology (NCN) at nanoHUB.org').'</p>'.n;
			$html .= t.'</li>'.n;
			$html .= '</ul>'.n;
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

	public function ranking( $rank, $stats, $id, $sef='' )
	{
		$rank = round($rank, 1);
		
		$r = (10*$rank);
		if (intval($r) < 10) {
			$r = '0'.$r;
		}
		
		if (!$sef) {
			$sef = JRoute::_('index.php?option=com_resources'.a.'id='.$id);
		}
		
		$html  = '<dl class="rankinfo">'.n;
		$html .= t.'<dt class="ranking"><span class="rank-'.$r.'">This resource has a</span> '.number_format($rank,1).' Ranking</dt>'.n;
		$html .= t.'<dd>'.n;
		$html .= t.t.'<p>'.n;
		$html .= t.t.t.'Ranking is calculated from a formula comprised of <a href="'.$sef.'/reviews">user reviews</a> and usage statistics. <a href="about/ranking/">Learn more &rsaquo;</a>'.n;
		$html .= t.t.'</p>'.n;
		$html .= t.t.'<div>'.n;
		$html .= $stats;
		$html .= t.t.'</div>'.n;
		$html .= t.'</dd>'.n;
		$html .= '</dl>'.n;
		return $html;
	}

	//-----------
	
	public function writeChildren( $config, $option, $database, $resource, $children, $live_site, $id=0, $active=0, $pid=0, $fsize=0 ) 
	{
	    $juser =& JFactory::getUser();
		$out = '';
		$blorp = '';
		if ($children != NULL) {
			$out .= '<ul>'.n;
			/*if ($id) {
				$n = count($children);
				$i = 0;
				foreach ($children as $child) 
				{
					$child->title = str_replace( '"', '&quot;', $child->title );
					if ($child->logicaltype == 20) {
						$class  = ' class="withaudio';
						$class .= ($active == $child->id || ($active=='' && $i==0)) ? ' active"': '"';
					} elseif ($child->logicaltype == 19) {
						$class  = ' class="withoutaudio';
						$class .= ($active == $child->id || ($active=='' && $i==0)) ? ' active"': '"';
					} else {
						$class = ($active == $child->id || ($active=='' && $i==0)) ? ' class="active"': '';
					}
					$i++;
					if ((!$child->grouping && $blorp) || ($child->grouping && $blorp && $child->grouping != $blorp)) {
						$blorp = '';
						$out .= t.'</ul>'.n;
						$out .= ' </li>'.n;
					}
					if ($child->grouping && !$blorp) {
						$blorp = $child->grouping;
						$database->setQuery( "SELECT type FROM #__resource_types WHERE id=".$child->grouping );
						if ($database->query()) {
							$blorpt = $database->loadResult();
						}
						
						$out .= ' <li><a href="javascript:showHide(\''.$blorpt.'\');" title="View '.$blorpt.'">'.$blorpt.'</a>'.n;
						$out .= t.'<ul id="'.$blorpt.'">'.n;
					}
					$out .= ($blorp) ? "\t" : '';
					$out .= ' <li'.$class.'>';
					$out .= ($child->type == 11) 
						  ? '<a href="'. $child->path.'" rel="external" ' 
						  : '<a href="index.php?option='.$option.a.'id='.$id.a.'resid='. $child->id.'" ';
					$out .= '>'. stripslashes($child->title) .'</a></li>'.n;
					if ($i == $n && $blorp) {
						$out .= t.'</ul>'.n;
						$out .= ' </li>'.n;
					}
				}
			} else {*/
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
						
						$out .= ' <li'.$liclass.'><a'.$class.' href="'.$url.'" title="'.stripslashes($child->title).'">'.$title.'</a>';
						//if ($child->logicaltype == 49 || $child->logicaltype == 50) {
						//	$out .= ' <a class="help" href="/podcast/" title="Learn more about podcast">What\'s this?</a>';
						//}
						//if($child->type == 13 || $child->type == 15 || $child->type == 33) {
							$out .= ' '. ResourcesHtml::getFileAttribs( $child->path, $base, $fsize );
						//}
						$out .= '</li>'.n;
					}
				}
			//}
			$out .= '</ul>'.n;
		} else {
			$out .= '<p>[ none ]</p>';
		}
		/*if ($id) {
			$out .= '<p>Was this helpful? <a href="'.JRoute::_('index.php?option=com_resources'.a.'id='.$id.a.'task=addreview').'" title="Submit comments, suggestions, and a rating">Submit a review.</a></p>';
		}*/
		return $out;		
	}

	//-----------

	public function lmChildren($database, $children, $id, $active, $fsize='') 
	{
		$n = count($children);
		$i = 0;
		$blorp = 0;
			
		$html  = '<ul class="sub-nav">'.n;
		foreach ($children as $child) 
		{
			$attribs =& new JParameter( $child->attribs );

			if ($attribs->get( 'exclude', '' ) != 1) {
				$params =& new JParameter( $child->params );
				$link_action = $params->get( 'link_action', '' );
				switch ($child->logicaltype)
				{
					case 19: $class = ' class="withoutaudio'; break;
					case 20: $class = ' class="withaudio';    break;
					default: 
						if ($child->type == 33) {
							$class = ' class="pdf';
						} else {
							$class = ' class="';
						}
						break;
				}
				$class .= ($active == $child->id || ($active=='' && $i==0)) ? ' active"': '"';

				$i++;
				if ((!$child->grouping && $blorp) || ($child->grouping && $blorp && $child->grouping != $blorp)) {
					$blorp = '';
					$html .= t.'</ul>'.n;
					$html .= ' </li>'.n;
				}
				if ($child->grouping && !$blorp) {
					$blorp = $child->grouping;

					$type = new ResourcesType( $database );
					$type->load( $child->grouping );
					
					$html .= ' <li class="grouping"><span>'.$type->type.'</span>'.n;
					$html .= t.'<ul id="'.strtolower($type->type).$i.'">'.n;
				}
				$html .= ($blorp) ? t : '';
				$html .= ' <li'.$class.'>';
			
				$url  = ($link_action == 1) 
					  ? checkPath($child->path, $child->type, $child->logicaltype)
					  : JRoute::_('index.php?option=com_resources'.a.'id='.$id.a.'resid='. $child->id);
				$html .= '<a href="'.$url.'" ';
				if ($link_action == 1) {
					$html .= 'target="_blank" ';
				} elseif($link_action == 2) {
					$html .= 'onclick="popupWindow(\''.$child->path.'\', \''.$child->title.'\', 400, 400, \'auto\');" ';
				}
				$html .= 'title="View this item">'. $child->title .'</a>';
				$html .= ($child->type == 33) 
					   ? ' '.ResourcesHtml::getFileAttribs( $child->path, '', $fsize ) 
					   : '';
				$html .= '</li>'.n;
				if ($i == $n && $blorp) {
					$html .= t.'</ul>'.n;
					$html .= ' </li>'.n;
				}
			}
		}
		$html .= '</ul>'.n;
		return $html;
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
			$url = JRoute::_('index.php?option='.$option.a.'id='. $id);
		} else {
			switch ($type) 
			{
				case 12:
					if ($path) {
						// internal link, not a resource
						$url = $path; 
					} else {
						// internal link but a resource
						$url = JRoute::_('index.php?option='.$option.a.'id='. $id);
					}
				break;
				
				case 32:
					$url = JRoute::_('index.php?option='.$option.a.'id='.$pid.a.'task=play');
				break;
				
				default: 
					if ($action == 2) {
						$url = JRoute::_('index.php?option='.$option.a.'id='.$pid.a.'resid='.$id.a.'task=play');
					} else {
						if (strstr($path,'http') || substr($path,0,3) == 'mms') {
							$url = $path; 
						} else {
							$url = JRoute::_('index.php?option='.$option.a.'id='.$id.a.'task=download'.a.'file='.basename($path));
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
							$lurl = 'index.php?option=com_mw'.a.'task=invoke'.a.'sess='.$sess.a.'version='.$v;
						} elseif (!isset($resource->revision) or $resource->revision=='dev') { // serve dev version
							$lurl = 'index.php?option=com_mw'.a.'task=invoke'.a.'sess='.$resource->alias.'_dev'.a.'version=test';
						}
					} else {
						$lurl = 'index.php?option=com_mw'.a.'task=invoke'.a.'sess='.$resource->alias;
					}
					
	
					if (isset($resource->revision) && $resource->toolpublished or !isset($resource->revision)) { // dev or published tool
						//if ($juser->get('guest')) { 
							// Not logged-in = show message
							//$html .= ResourcesHtml::primaryButton('launchtool disabled', $lurl, 'Launch Tool');
							//$html .= ResourcesHtml::warning( 'You must <a href="'.JRoute::_('index.php?option=com_login').'">log in</a> before you can run this tool.' ).n;
						//} else {
							$html .= ResourcesHtml::primaryButton('launchtool', $lurl, JText::_('Launch Tool') );
						//}
					} else { // tool unpublished
						//$html .= ResourcesHtml::primaryButtonDisabled('link_disabled', '', 'Launch Tool', '', '', '', true);
						$html .= ResourcesHtml::warning( 'This tool version is unpublished and cannot be run. If you would like to have this version staged for you, you can put a request through <a href="'.JRoute::_("index.php?option=com_feedback&task=report").'">support</a>.' ).n;
					}
				break;
			
			case 4:
				// write primary button and downloads for a Learning Module
				$html .= ResourcesHtml::primaryButton('', JRoute::_('index.php?option=com_resources'.a.'id='.$resource->id.a.'task=play'), 'Start learning module');
			break;
						
			case 6:
			case 8:
			case 31:
			case 2:
				// do nothing
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
					$html .= ResourcesHtml::primaryButton($class.' disabled', JRoute::_('index.php?option=com_login'), $mesg);
					$html .= t.'<p class="warning" style="clear: none;">You must <a href="'.JRoute::_('index.php?option=com_login').'">log in</a> before you can download.</p>'.n;
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

	public function primaryButton($class, $href, $msg, $xtra='', $title='', $action='', $disabled=false)
	{
		$title = htmlentities($title, ENT_QUOTES);
		
		if ($disabled) {
			return t.'<p id="primary-document"><span class="'.$class.'" >'.$msg.'</span></p>'.n;
		} else {
			return t.'<p id="primary-document"><a class="'.$class.'" href="'.$href.'" title="'.$title.'" '.$action.'>'.$msg.'</a>'.$xtra.'</p>'.n;
		}
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
	
	//-----------

	public function writeDownloads( &$database, $id, $option, $config, $fsize=0 )
	{
		$html = '';
		
		$database->setQuery( "SELECT r.path, r.type, r.title, r.access, r.id, r.standalone, a.* FROM #__resources AS r, #__resource_assoc AS a WHERE a.parent_id=".$id." AND r.id=a.child_id AND r.access=1 ORDER BY a.ordering" );
		if ($database->query()) {
			$downloads = $database->loadObjectList();
		}
		$base = $config->get('uploadpath');
		if ($downloads) {
			$html .= '<ul>'.n;
			foreach ($downloads as $download)
			{
				$ftype = '';
				$liclass = '';
				$file_name_arr = explode('.',$download->path);
				//$file_name_arr = explode('.','learning_modules/'.$download->path);
				$ftype = end($file_name_arr);
				$ftype = (strlen($ftype) > 3) ? substr($ftype, 0, 3): $ftype;

				if ($download->type == 12) {
					$liclass = ' class="html"';
				} else {
					$liclass = ' class="'.$ftype.'"';
				}

				// access is used for grouping public files in a learning module resource
				// so don't apply normal access restrictions. This should be fixed.
				//if ($download->access != 0)
				//	$url = "/resources/$download->id/download/"  . basename($download->path);
				//else
				//	$url = $download->path;
				$url = ResourcesHtml::processPath($option, $download, $id);
				
				$html .= t.'<li'.$liclass.'><a href="'.$url.'">'.$download->title.'</a> ';
				$html .= ResourcesHtml::getFileAttribs( $download->path, $base, $fsize );
				//$html .= ' <li'.$liclass.'><a href="learning_modules/'.$download->path.'">'.$download->title.'</a> ';
				//$html .= ResourcesHtml::getFileAttribs( $download->path,'learning_modules/' );
				$html .= '</li>'.n;
			}
			$html .= '</ul>'.n;
		}
		
		return $html;
	}

	//-------------------------------------------------------------
	// Results
	//-------------------------------------------------------------

	public function writeResults( &$database, &$lines, $show_edit=0, $show_date=3 ) 
	{
		$juser =& JFactory::getUser();

		$config =& JComponentHelper::getParams( 'com_resources' );
		
		$html  = '<ol class="resources results">'.n;
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

			// Set the display date
			//switch ($params->get('show_date')) 
			switch ($show_date) 
			{
				case 0: $thedate = ''; break;
				case 1: $thedate = JHTML::_('date', $line->created, '%d %b %Y');    break;
				case 2: $thedate = JHTML::_('date', $line->modified, '%d %b %Y');   break;
				case 3: $thedate = JHTML::_('date', $line->publish_up, '%d %b %Y'); break;
			}
			
			// Build the link
			if ($line->alias) {
				$sef = JRoute::_('index.php?option=com_resources'.a.'alias='. $line->alias);
			} else {
				$sef = JRoute::_('index.php?option=com_resources'.a.'id='. $line->id);
			}

			// Build the HTML
			$html .= t.'<li>'.n;
			$html .= t.t.'<p class="title"><a href="'.$sef.'">'. ResourcesHtml::encode_html($line->title) . '</a>'.n;
			if ($show_edit != 0) {
				$html .= ResourcesHtml::adminIcon( $line->id, $line->published, $show_edit, 0, 'edit', $line->type);
			}
			$html .= '</p>'.n;
				
			if ($params->get('show_ranking')) {
				// Get statistics info
				$helper->getCitationsCount();
				$helper->getLastCitationDate();
				
				if ($line->type == 7) {
					$stats = new ToolStats($database, $line->id, $line->type, $line->rating, $helper->citationsCount, $helper->lastCitationDate);
				} else {
					$stats = new AndmoreStats($database, $line->id, $line->type, $line->rating, $helper->citationsCount, $helper->lastCitationDate);
				}
				$statshtml = $stats->display();
				/*$statshtml = '';*/
				//$sections = ;
				//$html .= ResourcesHtml::metadata($params, $line->ranking, $statshtml, $line->id, array());
				$line->ranking = round($line->ranking, 1);

				$html .= t.t.'<div class="metadata">'.n;
				$html .= ResourcesHtml::ranking( $line->ranking, $statshtml, $line->id, '' );
				$html .= t.t.'</div>'.n;
			} elseif ($params->get('show_rating')) {
				$html .= t.t.'<div class="metadata">'.n;
				$html .= t.t.t.'<p class="rating"><span title="'.JText::sprintf('%s out of 5 stars',$line->rating).'" class="avgrating'.ResourcesHtml::getRatingClass( $line->rating ).'"><span>'.JText::sprintf('%s out of 5 stars',$line->rating).'</span>&nbsp;</span></p>'.n;
				$html .= t.t.'</div>'.n;
			}
		
			$info = array();
			if ($thedate) {
				$info[] = $thedate;
			}
			if (($line->type && $params->get('show_type')) || $line->standalone == 1) {
				$info[] = $line->typetitle;
			}
			if ($helper->contributors && $params->get('show_authors')) {
				$info[] = JText::_('Contributor(s)').': '. $helper->contributors;
			}
			
			$html .= t.t.'<p class="details">'.implode(' <span>|</span> ',$info).'</p>'.n;
			if ($line->introtext) {
				$html .= t.t.ResourcesHtml::shortenText( stripslashes($line->introtext) ).n;
			} else if ($line->fulltext) {
				$html .= t.t.ResourcesHtml::shortenText( stripslashes($line->fulltext) ).n;
			}
			$html .= t.t.'<div class="clear"></div>'.n;
			$html .= t.'</li>'.n;
		}
		$html .= '</ol>'.n;
		
		return $html;
	}
	
	//-----------
	
	public function writeResultsTable( $database, $resource, $children, $option )
	{
		$o = 'even';
		
		$xhub =& XFactory::getHub();
		
		$html  = '<table class="child-listing" summary="'.JText::_('A table of resources associated to this resource').'">'.n;
		//$html .= t.'<caption>'.n;
		//$html .= t.t.'<a class="feed" id="resource-audio-feed" href="'. $xhub->getCfg('hubLongURL') .'/resources/'.$resource->id.'/feed.rss?format=audio">'.JText::_('Audio podcast').'</a> '.n;
		//$html .= t.t.'<a class="feed" id="resource-video-feed" href="'. $xhub->getCfg('hubLongURL') .'/resources/'.$resource->id.'/feed.rss?format=video">'.JText::_('Video podcast').'</a>'.n;
		//$html .= t.'</caption>'.n;
		$html .= t.'<thead>'.n;
		$html .= t.t.'<tr>'.n;
		$html .= t.t.t.'<th>'.JText::_('Lecture Number/Topic').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('Breeze').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('Video').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('Lecture Notes (PDF)').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('Supplemental Material').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('Suggested Exercises').'</th>'.n;
		$html .= t.t.'</tr>'.n;
		$html .= t.'</thead>'.n;
		$html .= t.' <tbody>'.n;
		foreach ($children as $child) 
		{
			// Retrieve the grandchildren
			$helper = new ResourcesHelper($child->id, $database);
			$helper->getChildren();
			
			$child_params =& new JParameter( $child->params );
			$link_action = $child_params->get( 'link_action', '' );
			
			$child->title = ResourcesHtml::encode_html($child->title);
			
			$o = ($o == 'odd') ? 'even' : 'odd';
			
			$html .= t.t.'<tr class="'.$o.'">'.n;
			$html .= t.t.t.'<td>';
			if ($child->standalone == 1) {
				$html .= '<a href="'.JRoute::_('index.php?option='.$option.a.'id='.$child->id).'"';
				if ($link_action == 1) {
					$html .= ' target="_blank"';
				} elseif ($link_action == 2) {
					$html .= ' onclick="popupWindow(\''.$url.'\', \''.$child->title.'\', 400, 400, \'auto\');"';
				}
				$html .= '>'.$child->title.'</a>';
				if ($child->type != 31) {
					$html .= ($child->introtext) ? '<br />'.ResourcesHtml::shortenText(stripslashes($child->introtext),200,0) : '';
				}
			}
			$html .= '</td>'.n;
			if ($helper->children && count($helper->children) > 0) {
				$videoi    = '';
				$breeze    = '';
				$pdf       = '';
				$video     = '';
				$exercises = '';
				$supp      = '';
				$grandchildren = $helper->children;
				foreach ($grandchildren as $grandchild) 
				{
					$grandchild->title = ResourcesHtml::encode_html($grandchild->title);
					
					$grandchild->path = ResourcesHtml::processPath($option, $grandchild, $child->id);
					
					switch ($grandchild->type) 
					{
						case 37:
						case 15:
							$videoi .= (!$videoi) ? '<a href="'.$grandchild->path.'">'.JText::_('View').'</a>' : '';
							break;
						case 32:
							$breeze .= (!$breeze) ? '<a class="breeze" href="'.$grandchild->path.'" title="'.htmlentities(stripslashes($grandchild->title)).'">'.JText::_('View').'</a>' : '';
							break;
						case 33:
						default:
							if ($grandchild->logicaltype == 14) {
								$pdf .= '<a href="'.$grandchild->path.'">'.JText::_('Notes').'</a>'.n;
							} elseif ($grandchild->logicaltype == 51) {
								$exercises .= '<a href="'.$grandchild->path.'">'.stripslashes($grandchild->title).'</a>'.n;
							} else {
								$supp .= '<a href="'.$grandchild->path.'">'.stripslashes($grandchild->title).'</a><br />'.n;
							}
							break;
					}
				}
				
				$html .= t.t.t.'<td>'.$breeze.'</td>'.n;
				$html .= t.t.t.'<td>'.$videoi.'</td>'.n;
				$html .= t.t.t.'<td>'.$pdf.'</td>'.n;
				$html .= t.t.t.'<td>'.$supp.'</td>'.n;
				$html .= t.t.t.'<td>'.$exercises.'</td>'.n;
			} else {
				//$html .= t.t.t.'<td colspan="5">'.JText::_('Currently unavilable').'</td>'.n;
				$html .= t.t.t.'<td colspan="5"> </td>'.n;
			}
			$html .= t.t.'</tr>'.n;
		}
		$html .= t.'</tbody>'.n;
		$html .= '</table>'.n;
		
		return $html;
	}
	
	//-------------------------------------------------------------
	// Forms
	//-------------------------------------------------------------
	
	public function browsetags( $option, $title, $types, $activetype, $results, $authorized, $config, $supportedtag, $tag='', $tag2='' ) 
	{
		$html  = ResourcesHtml::div( ResourcesHtml::hed( 2, $title ), '', 'content-header' );
		
		$html .= '<form method="get" action="'.JRoute::_('index.php?option='.$option).'">'.n;
		
		$html .= '<div id="content-header-extra">';
		$html .= t.'<fieldset>'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'<span>'.JText::_('Resource type').':</span> '.n;
		$html .= t.t.t.'<select name="type">'.n;
		foreach ($types as $type) 
		{
			$html .= t.t.t.t.'<option value="'.$type->title.'"';
			if ($type->id == $activetype) {
				$html .= ' selected="selected"';
			}
			$html .= '>'.$type->type.'</option>'.n;
		}
		$html .= t.t.t.'</select>'.n;
		$html .= t.t.'</label>'.n;
		$html .= t.t.'<input type="submit" value="'.JText::_('GO').'"/>'.n;
		$html .= t.t.'<input type="hidden" name="task" value="browsetags" />'.n;
		$html .= t.'</fieldset>'.n;
		$html .= '</div><!-- / #content-header-extra -->'.n;
		
		$html .= '<div class="main section" id="browse-resources">'.n;
		
		$html .= '<div id="tagbrowser">'.n;
		$html .= '<p class="info">'.JText::_('Select a "tag" (or keyword) from the list below to browse through available resources in that category.').'</p>'.n;
		$html .= t.'<div id="level-1">'.n;
		$html .= t.t.ResourcesHtml::hed(3,JText::_('Tag')).n;
		$html .= t.t.'<ul><li id="level-1-loading"></li></ul>'.n;
		$html .= t.'</div>'.n;
		$html .= t.'<div id="level-2">'.n;
		$html .= t.t.ResourcesHtml::hed(3,JText::_('Resources').'<select name="sortby" id="sortby"></select>').n;
		$html .= t.t.'<ul><li id="level-2-loading"></li></ul>'.n;
		$html .= t.'</div>'.n;
		$html .= t.'<div id="level-3">'.n;
		$html .= t.t.ResourcesHtml::hed(3,JText::_('Info')).n;
		$html .= t.t.'<ul><li>'.JText::_('Select a resource to see details.').'</li></ul>'.n;
		$html .= t.'</div>'.n;
		$html .= t.'<input type="hidden" name="pretype" id="pretype" value="'.$activetype.'" />'.n;
		$html .= t.'<input type="hidden" name="id" id="id" value="" />'.n;
		$html .= t.'<input type="hidden" name="preinput" id="preinput" value="'.$tag.'" />'.n;
		$html .= t.'<input type="hidden" name="preinput2" id="preinput2" value="'.$tag2.'" />'.n;
		$html .= t.'<div class="clear"></div>'.n;
		$html .= '</div>'.n;
		
		$html .= '<p id="viewalltools"><a href="'.JRoute::_('index.php?option='.$option.a.'type='.$activetype).'">'.JText::_('View more &rsaquo;').'</a></p>'.n;
		$html .= '<div class="clear"></div>'.n;
		
		if ($supportedtag && $activetype == 7) {
			include_once(JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.tag.php');
			$database =& JFactory::getDBO();
			
			$tag = new TagsTag( $database );
			$tag->loadTag($supportedtag);

			$sl = $config->get('supportedlink');
			if ($sl) {
				$link = $sl;
			} else {
				$link = JRoute::_('index.php?option=com_tags'.a.'tag='.$tag->tag);
			}

			$html .= '<p class="supported">What\'s this? <a href="'.$link.'">About '.$tag->raw_tag.' tools.</a></p>';
		}
		
		if ($results) {
			$database =& JFactory::getDBO();

			$html .= t.t.ResourcesHtml::hed(3,JText::_('Top Rated')).n;
			$html .= '<div class="aside">'.n;
			$html .= t.'<p>The following are top-rated resources of this type.</p>'.n;
			$html .= '</div><!-- / .aside -->'.n;
			$html .= '<div class="subject">'.n;
			$html .= ResourcesHtml::writeResults( $database, $results, $authorized );
			$html .= '</div><!-- / .subject -->'.n;
		}
		
		$html .= '</div>'.n;
		$html .= '</form>'.n;
		
		return $html;
	}
	
	//-----------
	
	public function browse($option, $authorized, $title, $types, $filters, $pageNav, $results, $total, $config)
	{
		$database =& JFactory::getDBO();
		
		$sortbys = array();
		if ($config->get('show_ranking')) {
			$sortbys['ranking'] = JText::_('Ranking');
		}
		$sortbys['date'] = JText::_('Date (published)');
		$sortbys['date_modified'] = JText::_('Date (modified)');
		$sortbys['title'] = JText::_('Title');
		
		$html  = ResourcesHtml::div( ResourcesHtml::hed( 2, $title ), 'full', 'content-header' );
		//$html .= ResourcesHtml::div( '<p id="tagline"><a href="'.JRoute::_('index.php?option=com_contribute').'">'.JText::_('BECOME_A_CONTRIBUTOR').'</a></p>', '', 'content-header-extra' );
		
		$html .= '<div class="main section">';
		$html .= t.'<form action="'.JRoute::_('index.php?option='.$option).'" id="resourcesform" method="post">'.n;
		$html .= t.t.'<div class="aside">';
		
		$html .= t.t.t.'<fieldset>'.n;
		$html .= t.t.t.t.'<label>'.JText::_('TYPE').': '.n;
		$html .= t.t.t.t.'<select name="type" id="type">'.n;
		$html .= t.t.t.t.t.'<option value="">'.JText::_('ALL').'</option>'.n;
		if (count($types) > 0) {
			foreach ($types as $item)
			{
				$html .= t.t.t.t.t.'<option value="' . $item->id . '"';
				$html .= ( $filters['type'] == $item->id ) ? ' selected="selected"' : '';
				$html .= '>' . $item->type . '</option>'.n;
			}
		}
		$html .= t.t.t.t.'</select></label>'.n;
		//$html .= t.t.'<label>Tag: '.n;
		//$html .= t.t.'<input type="text" name="tag" value="'.$tag.'" /></label>'.n;
		$html .= t.t.t.t.'<label>'.JText::_('SORT_BY').':'.n;
		$html .= ResourcesHtml::formSelect('sortby', $sortbys, $filters['sortby'], '');
		$html .= t.t.t.t.'</label>'.n;
		$html .= t.t.t.t.'<input type="submit" value="'.JText::_('GO').'" />'.n;
		$html .= t.t.t.'</fieldset>'.n;
		
		$html .= t.t.'</div>';
		$html .= t.t.'<div class="subject">';
		
		if ($results) {
			switch ($filters['sortby']) 
			{
				case 'date_created': $show_date = 1; break;
				case 'date_modified': $show_date = 2; break;
				case 'date':
				default: $show_date = 3; break;
			}
			$html .= ResourcesHtml::writeResults( $database, $results, $authorized, $show_date );
		} else {
			$html .= ResourcesHtml::warning( JText::_('NO_RESULTS') ).n;
		}
		
		//$html .= $pageNav->getListFooter();
		$pn = $pageNav->getListFooter();
		$pn = str_replace('/?/&amp;','/?',$pn);
		$f = '';
		foreach ($filters as $k=>$v) 
		{
			$f .= ($v && $k != 'authorized' && $k != 'limit' && $k != 'start') ? $k.'='.$v.a : '';
		}
		$pn = str_replace('?','?'.$f,$pn);
		$html .= $pn;
		
		$html .= t.t.'</div>';
		$html .= t.'</form>'.n;
		$html .= '</div>'.n;
		$html .= '<div class="clear"></div>'.n;
		
		return $html;
	}
	
	//-----------
	
	public function browseChildrenForm($option, $resource, $filters, $pageNav, $database, $children, $authorized, $config) 
	{
		$xhub =& XFactory::getHub();
		
		$sortbys = array();
		$sortbys['date'] = JText::_('DATE');
		$sortbys['title'] = JText::_('TITLE');
		$sortbys['author'] = JText::_('AUTHOR');
		if ($config->get('show_ranking')) {
			$sortbys['ranking'] = JText::_('RANKING');
		}
		$sortbys['ordering'] = JText::_('ORDERING');
		
		
		if ($resource->alias) {
			$url = 'index.php?option='.$option.a.'alias='.$resource->alias;
		} else {
			$url = 'index.php?option='.$option.a.'id='.$resource->id;
		}
		
		$html  = ResourcesHtml::hed(3, JText::_('In This Series') );
		
		$html .= '<form method="get" action="'.JRoute::_($url).'">'.n;
		$html .= t.'<div class="aside">'.n;
		$html .= t.t.'<fieldset class="controls">'.n;
		$html .= t.t.t.'<label>'.n;
		$html .= t.t.t.t.JText::_('SORT_BY').':'.n;
		$html .= t.t.t.t.ResourcesHtml::formSelect('sortby', $sortbys, $filters['sortby'], '').n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.t.'<input type="submit" value="'.JText::_('GO').'" />'.n;
		$html .= t.t.'</fieldset>'.n;
		//$html .= t.t.'<p><a class="feed" id="resource-audio-feed" href="'. $xhub->getCfg('hubLongURL') .'/resources/'.$resource->id.'/feed.rss?format=audio">'.JText::_('Audio podcast').'</a><br />'.n;
		//$html .= t.t.'<a class="feed" id="resource-video-feed" href="'. $xhub->getCfg('hubLongURL') .'/resources/'.$resource->id.'/feed.rss?format=video">'.JText::_('Video podcast').'</a></p>'.n;
		$html .= t.'</div><!-- / .aside -->'.n;
		$html .= t.'<div class="subject">'.n;
		$html .= ResourcesHtml::writeResults( $database, $children, $authorized );
		$paging = $pageNav->getListFooter();
		$paging = str_replace('resources/?','resources/'.$resource->id.'?',$paging);
		$paging = str_replace('resources?','resources/'.$resource->id.'?',$paging);
		$paging = str_replace('?/resources/'.$resource->id,'?',$paging);
		$paging = str_replace('?','?sortby='.$filters['sortby'].'&',$paging);
		$paging = str_replace('&&','&',$paging);
		$paging = str_replace('&amp;&amp;','&amp;',$paging);
		$html .= $paging;
		$html .= t.'</div><!-- / .subject -->'.n;
		$html .= t.'<div class="clear"></div><!-- / .clear -->'.n;
		$html .= '</form>'.n;
		
		return $html;
	}

	//-----------

	public function toollicense( $option, $app, $row, $title, $no_html )
	{
		$html = '';
		if ($no_html) {
			$iso = 'charset=utf-8'; //split( '=', _ISO );

			$html .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.n;
			$html .= '<html xmlns="http://www.w3.org/1999/xhtml">'.n;
			$html .= ' <head>'.n;
			$html .= t.'<meta http-equiv="Content-Type" content="text/html; '. $iso .'" />'.n;
			$html .= t.'<title>'. $title .'</title>'.n;
			$html .= t.'<link rel="stylesheet" type="text/css" href="/templates/'. $app->getTemplate() .'/css/main.css" />'.n;
			if (is_file(JPATH_ROOT.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$option.DS.'members.css')) {
				$html .= t.'<link rel="stylesheet" href="'.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$option.DS.'resources.css" type="text/css" />'.n;
			} else {
				$html .= t.'<link rel="stylesheet" href="'.DS.'components'.DS.$option.DS.'resources.css" type="text/css" />'.n;
			}
			$html .= ' </head>'.n;
			$html .= ' <body id="resource-license">'.n;

			$html .= t.'<div id="wrap">'.n;
			$html .= t.t.'<div id="main">'.n;
			$html .= ($title) ? ResourcesHtml::hed( 3, $title ).n : '';
		} else {
			$t = ResourcesHtml::hed( 2, $title ).n;
			if ($row->codeaccess=='@OPEN') {
				$t .= '<p>'.JText::sprintf('Open source tool, version %s', $row->version).'</p>'.n;
			} else {
				$t .= '<p>'.JText::sprintf('Closed source tool, version %s', $row->version).'</p>'.n;
			}
			$html .= ResourcesHtml::div( $t, 'full', 'content-header').n;
			$html .= '<div class="main section">'.n;
		}
		if ($row->license) {
			$html .= t.t.'<pre>'.$row->license.'</pre>'.n;
		} else {
			$html .= t.t.ResourcesHtml::warning( JText::_('License text not found') ).n;
		}
		if ($no_html) {
			$html .= t.t.'</div><!-- / #main -->'.n;
			$html .= t.'</div><!-- / #wrap -->'.n;

			$html .= ' </body>'.n;
			$html .= '</html>'.n;
		} else {
			$html .= '</div><!-- / .main section -->'.n;
		}
		return $html;
	
	}
	
	//-----------

	/*public function minimal( $title, $url, $width, $height )
	{
		$type = ResourcesHtml::getFileExtension($url);
		
		$width = (intval($width) > 0) ? $width : 0;
		$height = (intval($height) > 0) ? $height : 0;
		
		$iso = split( '=', _ISO );
		
		$html  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.n;
		$html .= '<html xmlns="http://www.w3.org/1999/xhtml">'.n;
		$html .= ' <head>'.n;
		$html .= t.'<meta http-equiv="Content-Type" content="text/html; '. $iso .'" />'.n;
		$html .= t.'<title>'. $title .'</title>'.n;
		$html .= t.'<link rel="stylesheet" type="text/css" href="templates/'. $GLOBALS['cur_template'] .'/css/main.css" />'.n;
		$html .= t.'<link rel="stylesheet" type="text/css" href="components/com_resources/resources.css" />'.n;
		$html .= t.'<style>'.n;
		$html .= t.t.'body { min-width: 300px; }'.n;
		$html .= t.t.'#wrap, #middle-column { margin-top: 0; width: auto; height: 100%; }'.n;
		$html .= t.t.'#middle-column h2 { margin-top: 0; }'.n;
		$html .= t.t.'#main { margin: 8px 2px 2px 2px; padding: 0; border: 2px solid #ccc; border-top: 1px solid #ccc; background: #fff; color: #666;height: 90%; }'.n;
		$html .= t.t.'html, body {height:100%;overflow:hidden;}'.n;
		$html .= t.t.'object, embed { height: 86%; }'.n;
		$html .= t.'</style>'.n;
		$html .= ' </head>'.n;
		$html .= ' <body>'.n;

		$html .= t.'<div id="wrap">'.n;
		$html .= t.t.'<div id="main">'.n;
		$html .= t.t.t.'<div id="middle-column">'.n;
 
		$html .= ($title) ? ResourcesHtml::hed( 2, $title ).n : ''; 
		
		if (strtolower($type) == 'swf') {
			$html .= t.t.t.'<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,65,0" width="100%" height="86%" id="SlideContent" VIEWASTEXT>'.n;
			$html .= t.t.t.' <param name="movie" value="'. $url .'" />'.n;
			$html .= t.t.t.' <param name="quality" value="high" />'.n;
			$html .= t.t.t.' <param name="menu" value="false" />'.n;
			$html .= t.t.t.' <param name="loop" value="false" />'.n;
			$html .= t.t.t.' <param name="scale" value="showall" />'.n;
			$html .= t.t.t.' <embed src="'. $url .'" menu="false" quality="best" loop="false" width="100%" height="86%" scale="showall" name="SlideContent" align="" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" swLiveConnect="true"></embed>'.n;
			$html .= t.t.t.'</object>'.n;
		} else {
			$html .= t.t.t.'<applet code="Silicon" archive="'. $url .'" width="';
			$html .= ($width > 0) ? $width : '';
			$html .= '" height="';
			$html .= ($height > 0) ? $height : '';
			$html .= '">'.n;
			if ($width > 0) {
				$html .= t.t.t.' <param name="width" value="'. $width .'" />'.n;
			}
			if ($height > 0) {
				$html .= t.t.t.' <param name="height" value="'. $height .'" />'.n;
			}
			$html .= t.t.t.'</applet>'.n;
		}
		
		$html .= t.t.t.'</div><!-- / #middle-column -->'.n;
		$html .= t.t.'</div><!-- / #main -->'.n;
		$html .= t.'</div><!-- / #wrap -->'.n;

		$html .= ' </body>'.n;
		$html .= '</html>'.n;
		
		return $html;
	}*/
}
?>
