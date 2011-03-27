<?php
/**
 * @package     hubzero-cms
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

Class TwitterModule
{
	
	function __construct( $group )
	{
		//group object
		$this->group = $group;
	}
	
	//-----
	
	function onManageModules()
	{
		$mod = array(
			'name' => 'twitter',
			'title' => 'Twitter Feed',
			'description' => 'The Twitter module displays a mini feed for the supplied Twitter username',
			'input_title' => "Twitter Username & Number of Tweets (ex. nanohub:3): <span class=\"required\">Required</span>",
			'input' => "<input type=\"text\" name=\"module[content]\" value=\"{{VALUE}}\" />"
		);
		
		return $mod;
	}
	
	
	//-----
	
	/*
	function render_PHP()
	{
		//var to hold content being returned
		$content  = '';
		
		//default # of tweets to display
		$default_tweets = 4;
		
		//split the content into username and number of tweets
		$parts = explode(":", $this->content);
		
		//set username
		$username = $parts[0];
		
		//set user defined number of tweets
		$user_tweets = $parts[1];
		
		//determine limit of tweets based on if set by user
		$num_tweets = ($user_tweets != '' && is_numeric($user_tweets)) ? $user_tweets : $default_tweets;
		
		//feed to parse with username and num of tweets
		$url = 'http://twitter.com/statuses/user_timeline.json?screen_name='.$username.'&count='.$num_tweets;
		
		//check if username entered will return a valid result
		$twitter_status = get_headers($url, 1);
		
		//if we have a bad request on the url
		if($twitter_status['Status'] == '400 Bad Request') {
			$content = "<div class=\"group_module_custom\"><p class=\"error\">Error while trying to get twitter feed.</p></div>";
		} else {
			//get the contents from Twitter
			$tweets = file_get_contents($url);

			//decode the from JSON format
			$tweets = json_decode($tweets);

			//build the twitter feed container with link to usernames twitter account
			$content .= "<div id=\"twitter_feed\">";
			$content .= "<div id=\"user\"><a rel=\"external\" href=\"http://www.twitter.com/".$username."\">@".$username."</a></div>";

			//foreach tweet show tweet and date/time with link
			foreach($tweets as $tweet) {
				$content .= "<div class=\"tweet\">";
				$content .= $tweet->text;
				$content .= "<a rel=\"external\" class=\"time\" href=\"http://twitter.com/$username/statuses/$tweet->id_str\">".date("m/d/y g:ia", strtotime($tweet->created_at))."</a>";
				$content .= "</div>";
			}

			//close twitter feed container
			$content .= "</div>";
		}
		
		//return the content
		return $content;
	}
	*/
	
	//-----
	
	function render()
	{
		//default # of tweets to display
		$default_tweets = 4;
		
		//split the content into username and number of tweets
		$parts = explode(":", $this->content);
		
		//set username
		$username = $parts[0];
		
		//set user defined number of tweets
		$user_tweets = $parts[1];
		
		//determine limit of tweets based on if set by user
		$num_tweets = ($user_tweets != '' && is_numeric($user_tweets)) ? $user_tweets : $default_tweets;
		
		//get the document
		$document =& JFactory::getDocument();
		
		//add the twitter js to the page
		$document->addScript("http://twitterjs.googlecode.com/svn/trunk/src/twitter.min.js");
		
		//add the twitter js call to the page.
		$document->addScriptDeclaration(
			"getTwitters('tweets', { 
		  		id: '{$username}', 
		  		count: {$user_tweets}, 
		  		enableLinks: true, 
		  		ignoreReplies: true, 
		  		clearContents: true,
			});"
		);
		
		$content  = "<div class=\"group_module_custom\" id=\"twitter_feed\">";
		$content .= "<div id=\"user\"><a rel=\"external\" href=\"http://www.twitter.com/".$username."\">@".$username."</a></div>";
		$content .= "<div id=\"tweets\"><p>Please wait while the tweets load.</p><img src=\"/components/com_groups/assets/img/circling-ball-black.gif\" /></div>";
		$content .= "</div>";
		
		return $content;
	}
}

?>