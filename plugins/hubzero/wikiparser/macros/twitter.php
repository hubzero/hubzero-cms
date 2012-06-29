<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Wiki macro class for dipslaying a twitter feed
 */
class TwitterMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 * 
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = "Embeds a Twitter Feed into the page";
		$txt['html'] = '<p>Embeds a Twitter Feed into the page. Can be a user feed(@hubzero) or search by trend(#hubzero), followed by a comma(,) and then the number of tweets to display.</p>
						<p>Examples:</p>
						<ul>
							<li><code>[[Twitter(@hubzero,2)]]</code></li>
							<li><code>[[Twitter(#hubzero,5)]]</code></li>
						</ul>
						<p>Displays:</p>
						<link type="text/css" rel="stylesheet" href="/plugins/hubzero/wikiparser/macros/macro-assets/twitter/twitter.css" />
						<script src="/plugins/hubzero/wikiparser/macros/macro-assets/twitter/twitter.js"></script>
						<script>
							window.addEvent("domready",function() {
											var twitterFeed = new HUB.Twitter("twitterMacroList", {
												type: "user",
												username: "@hubzero",
												trend: "",
												tweets: 2,
												linkify:true
											});
										});
						</script>
						<div id="twitterMacroList" class="twitter_feed_container">Loading Twitter Feed...</div><br><br>';

		return $txt['html'];
	}

	/**
	 * Generate macro output
	 * 
	 * @return     string
	 */
	public function render()
	{
		//get the args passed in
		$args = explode(',', $this->args);

		//get the type of twitter feed based on the first arg
		$start = substr($args[0], 0, 1);

		if ($start != '@' && $start != '#') 
		{
			return "(Please enter a valid Twitter Username or trend)";
		}

		if ($start == '@') 
		{
			$type = 'user';
			$username = $args[0];
			$trend = '';
		} 
		else if ($start == "#") 
		{
			$type = 'trend';
			$username = '';
			$trend = $args[0];
		}

		//set default for num tweets
		$num_tweets = 3;

		if (is_numeric(trim($args[1]))) 
		{
			$num_tweets = $args[1];
		}

		$uniqid = uniqid();

		$doc =& JFactory::getDocument();
		$doc->addStyleSheet('/plugins/hubzero/wikiparser/macros/macro-assets/twitter/twitter.css');

		if (JPluginHelper::isEnabled('system', 'jquery'))
		{
			$doc->addScript('/plugins/hubzero/wikiparser/macros/macro-assets/twitter/twitter.jquery.js');
			$doc->addScriptDeclaration("
				jQuery(document).ready(function($){
					var twitterFeed = $(\"#twitter{$uniqid}\").twitter({
						type: \"{$type}\",
						username: \"{$username}\",
						trend: \"{$trend}\",
						tweets: {$num_tweets},
						linkify:true
					});
				});
			");
		}
		else
		{
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
		}

		return '<div id="twitter' . $uniqid . '" class="twitter_feed_container">Loading Twitter Feed....</div>';
	}
}
