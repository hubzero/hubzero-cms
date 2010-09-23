<?php
/**
 * @package		HUBzero CMS
 * @author		Christopher Smoak <csmoak@purdue.edu>
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

//Dont allow direct access
defined('_JEXEC') or die( 'Restricted access' );

class modTwitterFeedHelper
{
	public function getTweets( $twitterID, $tweetCount ) 
	{
		//declare variables
		$i = 0;
		$tweets = array();
		$tweets['error'] = '';
		$subtract = strlen($twitterID) + 2;
		
		//Check to make sure a twitter ID has been entered
		if ($twitterID == null || $twitterID == '') {
			$tweets['error'] = JText::_('MOD_TWITTERFEED_MISSING_ID');
		}
		
		//Check to make sure admin didnt set # of Tweets to display too high or too low
		if ($tweetCount > 10 || $tweetCount < 0 || $tweetCount == '') {
			$tweetCount = 3;
		}
		
		//Declare Twitter user rss feed with TwitterID from Module Manager
		$tweetURL = 'http://twitter.com/statuses/user_timeline/'.$twitterID.'.rss';
		
		//set options for parsing feed
		$options = array();
		$options['rssUrl'] = $tweetURL;
		
		//Parse the rss feed
		$twitter =& JFactory::getXMLparser('rss', $options);
		
		// Check to make sure the RSS feed was parsed corectly
		if (!isset($twitter) && $tweets['error'] == '') {
			$tweets['error'] = JText::_('MOD_TWITTERFEED_INVALID_ID');
		}
		
		//Check to make sure there are no errors before obtaining tweets
		if ($tweets['error'] == '')  {
			// For each slice of pie we've got to get the goods
		 	foreach ($twitter->get_items(0, $tweetCount) as $tweet)
			{
				$tweetTitle = $tweet->get_title();
				$tweetTitle = substr($tweetTitle,$subtract);
				$tweetTitle = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]", "<a rel=\"external\" href=\"\\0\">\\0</a>", $tweetTitle);
				$tweets[$i]["tweet"] = $tweetTitle;
				$tweets[$i]["pubDate"] = $tweet->get_date();
				$tweets[$i]["link"] = $tweet->get_link();
				$i++;
			}
		}
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStyleSheet('mod_twitterfeed');
		return $tweets;
	} //end getTweets Function
}