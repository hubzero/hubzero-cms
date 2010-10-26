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

$html = '';
if ($this->resource->type == 4) {
	$parameters =& new JParameter( $this->resource->params );
	
	$this->helper->getChildren();

	$children = $this->helper->children;
	
	// We're going through a learning module
	$html .= '<div class="aside">'."\n";
	$n = count($children);
	$i = 0;
	$blorp = 0;
		
	$html .= '<ul class="sub-nav">'."\n";
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
			$class .= ($this->resid == $child->id || ($this->resid == '' && $i == 0)) ? ' active"': '"';

			$i++;
			if ((!$child->grouping && $blorp) || ($child->grouping && $blorp && $child->grouping != $blorp)) {
				$blorp = '';
				$html .= "\t".'</ul>'."\n";
				$html .= ' </li>'."\n";
			}
			if ($child->grouping && !$blorp) {
				$blorp = $child->grouping;

				$type = new ResourcesType( $this->database );
				$type->load( $child->grouping );
				
				$html .= ' <li class="grouping"><span>'.$type->type.'</span>'."\n";
				$html .= "\t".'<ul id="'.strtolower($type->type).$i.'">'."\n";
			}
			$html .= ($blorp) ? "\t" : '';
			$html .= ' <li'.$class.'>';
		
			$url  = ($link_action == 1) 
				  ? checkPath($child->path, $child->type, $child->logicaltype)
				  : JRoute::_('index.php?option='.$this->option.'&id='.$this->resource->id.'&resid='. $child->id);
			$html .= '<a href="'.$url.'" ';
			if ($link_action == 1) {
				$html .= 'target="_blank" ';
			} elseif($link_action == 2) {
				$html .= 'onclick="popupWindow(\''.$child->path.'\', \''.$child->title.'\', 400, 400, \'auto\');" ';
			}
			$html .= '>'. $child->title .'</a>';
			$html .= ($child->type == 33) 
				   ? ' '.ResourcesHtml::getFileAttribs( $child->path, '', $this->fsize ) 
				   : '';
			$html .= '</li>'."\n";
			if ($i == $n && $blorp) {
				$html .= "\t".'</ul>'."\n";
				$html .= ' </li>'."\n";
			}
		}
	}
	$html .= '</ul>'."\n";
	$html .= ResourcesHtml::license( $parameters->get( 'license', '' ) );
	$html .= '</div><!-- / .aside -->'."\n";
	$html .= '<div class="subject">'."\n";

	// Playing a learning module
	if (is_object($this->activechild)) {
		if (!$this->activechild->path) {
			// Output just text
			$html .= '<h3>'.stripslashes($this->activechild->title).'</h3>';
			$html .= stripslashes($this->activechild->fulltext);
		} else {
			// Output content in iFrame
			$html .= '<iframe src="'.$this->activechild->path.'" width="97%" height="500" name="lm_resource" frameborder="0" bgcolor="white"></iframe>'."\n";
		}
	}

	$html .= '</div><!-- / .subject -->'."\n";
	$html .= '<div class="clear"></div>'."\n";
}
else if ($this->resource->type == 67) {
	//Multimedia Type
	$url = $this->activechild->path;
	$this->helper->getChildren();
	$xhub =& XFactory::getHub();

	$children = $this->helper->children;
	$n = count($children);
	$br = strtolower($_SERVER['HTTP_USER_AGENT']); // what browser they use. 
	if(ereg("msie", $br)) { 
	    $n = $n+1; //annoying IE fix (can't display a playlist of one), put it into multi mode
	}
	
	// Get some attributes
	$attribs =& new JParameter( $this->activechild->attribs );
	$width  = $attribs->get( 'width', '' );
	$height = $attribs->get( 'height', '' );
	
	$type = '';
	$arr  = explode('.',$url);
	$type = end($arr);
	$type = (strlen($type) > 4) ? 'html' : $type;
	$type = (strlen($type) > 3) ? substr($type, 0, 3) : $type;

	$width = (intval($width) > 0) ? $width : 0;
	$height = (intval($height) > 0) ? $height : 0;
	$rType =  $this->activechild->type;
	if (strtolower($type) == 'mp4' | strtolower($type) == 'flv'){
		$rType = 41; //fix for old resources uploaded without the correct file type associated
		//if we have old image files, they will masquerade as an image/video type
	}
	else if (  strtolower($type) == 'gif'| strtolower($type) == 'png' | strtolower($type) == 'jpg' | strtolower($type) == 'jpeg' )
	{
		$rType = 70;
	}
	if ($rType == 41 | $rType == 70){
			
			//<!-- 1. load the Flowplayer JavaScript component -->
			if ($this->no_html == 1) {
				//need these scripts if we are not including header of index.php page
				$html .= '<script type="text/javascript" src="media/system/js/jquery-1.4.2.js"></script>'."\n";
				$html .= '<script type="text/javascript" src="media/system/js/jquery.tools.min.js"></script>'."\n";
				$html .= '<script type="text/javascript">var $jQ = jQuery.noConflict();</script> '."\n";
				$html .= '<link rel="stylesheet" type="text/css" media="screen" href="'.$this->baseurl.'/templates/fresh/css/main.css"/>';
				$html .= '<link rel="stylesheet" type="text/css" media="screen" href="'.$this->baseurl.'/templates/fresh/html/com_resources/resources.css"/>';
			}	
			$html .=  '<script src="site/flowplayer/flowplayer-3.2.4.min.js"></script>'."\n";
			$html .=  '<script src="site/flowplayer/flowplayer.ipad-3.2.1.js"></script>'."\n";
			$html .=  '<script src="site/flowplayer/flowplayer.playlist-3.0.8.js"></script>'."\n";
			
			$html .= '<div class="video_player_box"><!-- Video Player Box -->'."\n";
			$html .= '<img src="'.$this->baseurl.'/templates/fresh/images/logos/neeshubsmall.jpg"></img>';
			//Write in the Actual Video Player
			$html .= '<a id="player" class="player"';
			if (n <=1) {
				$html .= 'href="'. $xhub->getCfg('hubLongURL').$url.'"'; //Video URL for single video
			}
			$html .= '>';
			if (n > 1)
			{ 
				$html .= 'Loading...';
			}
			$html .= '</a>'."\n";
			
			//Write Playlist
			$html .= '<div id="playlist_wrap">'."\n";
			$html .= '<a class="prev"></a>'."\n";
			$html .= '<div id="pl">';
			$html .= '<div class="entries">'."\n";
			$vidCount = 0;
			$isMovie = ($rType == 41)? true : false; //set the movie player with controls
			//	<!-- single playlist entry as an "template" -->
			if ($n>1) {
				
				$html .= '<div class="page">'."\n";	
				$done = false;
				foreach ($children as $child) 
				{
					//build up playlist of attached videos
					$curl = $child->path;
				
					$ctype = '';
					$carr  = explode('.',$curl);
					$ctype = end($carr);
					$class = (!$done)? ' class = "first"' : '';
					$done = true;
					$ctype = (strlen($ctype) > 4) ? 'html' : $ctype;
					$ctype = (strlen($ctype) > 3) ? substr($ctype, 0, 3) : $ctype;
					//if (strtolower($type) == 'mp4' | strtolower($type) == 'flv'){
					$rcType =  $child->type;
					if (strtolower($ctype) == 'mp4' | strtolower($ctype) == 'flv'){
						$rcType = 41; //fix for old resources uploaded without the correct file type associated
						//if we have old image files, they will masquerade as an image/video type
					}
					else if (  strtolower($ctype) == 'gif'| strtolower($ctype) == 'png' | strtolower($ctype) == 'jpg' | strtolower($type) == 'jpeg' )
					{
						$rcType = 70;
					}
					if ($rcType == 41 | $rcType == 70){
						$html .= '	<a href="'.$xhub->getCfg('hubLongURL').'/site/resources/'.$child->path.'"'.$class.' duration=5>';
						$html .= 		substr($child->title,0,20);
						if ($rcType == 70) //image
						{
							$html .= '</br><img src="'.$xhub->getCfg('hubLongURL').'/site/resources/'.$child->path.'" width="58px"></img>';
							//$html .= '<div class="time">'."\n";	
						    //$html .= '<span>00:05</span>'."\n";	
						    //$html .= '<strong>00:05</strong>'."\n";	
						  	//$html .= '</div>'."\n";	
						}
						else
							$isMovie = true; //set the movie player
						//$html .= '		<span>'.$child->title.'</span>'."\n";
						$html .= '	</a>'."\n";
						$vidCount++;					
					}
					if ($vidCount >= 4) 
					{
						$html .= '</div><div class="page">'."\n";
						$vidCount = 0;
					}
						
				}
				$html .= '</div>'."\n";	
			}		
			$html .= '</div><!-- entries -->'."\n";
			$html .= '</div><!-- pl -->'."\n";
			$html .= '<a class="next"></a>'."\n";
			$html .= '</div><!-- playlist wrap -->'."\n";
			$html .= '</div><!-- Video Player Box -->'."\n";
			//Done with HTML - now write Javascript
			$html .= '<script type="text/javascript">'."\n";
			//make playlist scrollable
			if ($n>1) {
			$html .= '		$jQ("#pl").scrollable({ circular: true , loop:true});'."\n";
			}
			
			//switch between simple picture player and a better movie player (more controls)
			$player_src = (!$isMovie)? 'http://builds.flowplayer.netdna-cdn.com/48746/21045/flowplayer-3.2.4-0.swf' : "http://builds.flowplayer.netdna-cdn.com/48746/19312/flowplayer-3.2.4-0.swf"; 
			// now we install the player to the page
			$html .='  $f("player", "'.$player_src.'", { clip: { scaling: "orig", onFinish: function() { parent.$jQ.fancybox.next(); }}, wmode: \'transparent\', play:{opacity:0} ';
			$html .= "\n";
			
			if ($n>1) {
				
				$html .= ' ';
				$html .= "\n".', plugins: { controls : { playlist: true } }';
				$html .='});'."\n";
				//$html .= '$f("player").playlist("div.clips"); '."\n"; //set up playlist
				$html .= '$f("player").ipad().playlist("div.entries:first", {loop: false}, { onFinish: function() {  parent.$jQ.fancybox.next(); }}); '."\n"; //ipad compatibility	
			}
			else
			{
				$html .= ' ';
				$html .= '});'."\n";
				$html .= '$f("player").ipad(); '."\n";
			}
			
			//finally finish the script
			$html .= '</script>'."\n";
	}
	else if (strtolower($type) == 'html')
	{		
	
	}
			
	
		

} else {
	$url = $this->activechild->path;
	
	// Get some attributes
	$attribs =& new JParameter( $this->activechild->attribs );
	$width  = $attribs->get( 'width', '' );
	$height = $attribs->get( 'height', '' );
	
	$type = '';
	$arr  = explode('.',$url);
	$type = end($arr);
	$type = (strlen($type) > 4) ? 'html' : $type;
	$type = (strlen($type) > 3) ? substr($type, 0, 3) : $type;

	$width = (intval($width) > 0) ? $width : 0;
	$height = (intval($height) > 0) ? $height : 0;
	
	if (is_file(JPATH_ROOT.$url)) {
		if (strtolower($type) == 'swf') {
			$height = '400px';
			if ($this->no_html) {
				$height = '100%';
			}
			$html .= '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,65,0" width="100%" height="'.$height.'" id="SlideContent" VIEWASTEXT>'."\n";
			$html .= ' <param name="movie" value="'. $url .'" />'."\n";
			$html .= ' <param name="quality" value="high" />'."\n";
			$html .= ' <param name="menu" value="false" />'."\n";
			$html .= ' <param name="loop" value="false" />'."\n";
			$html .= ' <param name="scale" value="showall" />'."\n";
			$html .= ' <embed src="'. $url .'" menu="false" quality="best" loop="false" width="100%" height="'.$height.'" scale="showall" name="SlideContent" align="" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" swLiveConnect="true"></embed>'."\n";
			$html .= '</object>'."\n";
		

		}
		else
		{
			$html .= '<p class="error">Unsupported Playable Format</p>'."\n";
		}
	} else {
		$html .= '<p class="error">'.JText::_('COM_RESOURCES_FILE_NOT_FOUND').'</p>'."\n";
	}
}
echo $html;
?>