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
							<li><code>[[Twitter(@hubzeroplatform,2)]]</code></li>
						</ul>
						<p>Displays:</p>
						<a class="twitter-timeline"
							href="https://twitter.com/"
							data-widget-id="346714310770302976"
							data-screen-name="hubzeroplatform"
							data-tweet-limit="2"
							data-chrome=""
							>Loading Tweets...</a>
						<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';

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

		//get screen name & num tweets
		$screenName = (isset($args[0])) ? ltrim($args[0], '@') : '';
		$numItems   = (isset($args[1]) && is_numeric($args[1])) ? $args[1] : 3;

		//make sure we have a user name
		if ($screenName == '')
		{
			return "(Please enter a valid Twitter Username or ID)";
		}

		//output embeded timeline
		return '<a class="twitter-timeline"
					href="https://twitter.com/'. $screenName .'"
					data-widget-id="346714310770302976"
					data-screen-name="' . $screenName . '"
					data-tweet-limit="' . trim($numItems) . '"
					data-chrome=""
					>Loading Tweets...</a>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
	}
}
