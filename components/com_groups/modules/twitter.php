<?php

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
			'input_title' => "Twitter Username & Number of Tweets (ex. username: # of tweets): <span class=\"required\">Required</span>",
			'input' => "<input type=\"text\" name=\"module[content]\" value=\"{{VALUE}}\" />"
		);
		
		return $mod;
	}
	
	
	//-----
	
	function render()
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
		if(!file_get_contents($url)) {
			return;
		}
		
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
		
		//return the content
		return $content;
	}
	
	//-----
}

?>