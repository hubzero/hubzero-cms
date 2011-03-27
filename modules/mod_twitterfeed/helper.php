<?php
/**
 * @package     hubzero-cms
 * @author      Christopher Smoak <csmoak@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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
