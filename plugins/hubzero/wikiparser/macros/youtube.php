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

class YoutubeMacro extends WikiMacro 
{
	public function description() 
	{
		$txt = array();
		$txt['wiki'] = "Embeds a Youtube Video into the Page";
		$txt['html'] = '<p>Embeds a Youtube Video into the Page</p>';
		
		return $txt['html'];
	}
	
	//-----------
	
	public function render() 
	{
		//get the args passed in
		$content = $this->args;
		
		//declare the partial youtube embed url
		$youtube_url = "http://www.youtube.com/embed/";
		
		// args will be null if the macro is called without parenthesis.
		if (!$content) {
			return '';
		}
		
		//check is user entered full youtube url or just Video Id
		if (strstr($content,'http')) {
			//split the string into two parts 
			//uri and query string
			$full_url_parts = explode("?",$content);
			
			//split apart any key=>value pairs in query string
			$query_string_parts = explode("%26%2338%3B",urlencode($full_url_parts[1]));
			
			//foreach query string parts
			//explode at equals sign
			//check to see if v is the first part and if it is set the second part to the video id
			foreach($query_string_parts as $qsp) {
				$pairs_parts = explode("%3D",$qsp);
				if($pairs_parts[0] == 'v') {
					$video_id = $pairs_parts[1];
					break;
				}
			}
		} else {
			$video_id = $content;
		}
		
		//append to the youtube url
		$youtube_url .= $video_id;
		
		//return the emdeded youtube video
		return "<iframe src=\"{$youtube_url}\" width=\"640\" height=\"380\"></iframe>";
	}
}
?>