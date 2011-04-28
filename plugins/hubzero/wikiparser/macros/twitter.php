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

class TwitterMacro extends WikiMacro 
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
		$args = explode(',', $this->args);
		
		//get the type of twitter feed based on the first arg
		$start = substr($args[0], 0, 1);
		
		//if
		if($start != "@" && $start != "#") {
			return "(Please enter a valid Twitter Username or trend)";
		}
		
		if($start == "@") {
			$type = "user";
			$username = $args[0];
			$trend = "";
		} else if ($start == "#") {
			$type = "trend";
			$username = "";
			$trend = $args[0];
		}
		
		//set default for num tweets
		$num_tweets = 3;
		
		//
		if(is_numeric(trim($args[1]))) {
			$num_tweets = $args[1];
		}
		
		$uniqid = uniqid();
		
		$doc =& JFactory::getDocument();
		$doc->addStyleSheet('/plugins/hubzero/wikiparser/macros/macro-assets/twitter/twitter.css');
		$doc->addScript('/plugins/hubzero/wikiparser/macros/macro-assets/twitter/twitter.js');
		$doc->addScriptDeclaration("
			window.addEvent(\"domready\",function() {
				var twitterFeed = new HUB.Twitter(\"twitter{$uniqid}\", {
					type: \"{$type}\",
					username: \"{$username}\",
					trend: \"{$trend}\",
					tweets: {$num_tweets},
					linkify:true
				});
			});
			
		");
	
		return "<div id=\"twitter{$uniqid}\" class=\"twitter_feed_container\">Loading Twitter Feed....</div>";
	}
}
?>