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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'modFeedYoutubeHelper'
 * 
 * Long description (if any) ...
 */
class modFeedYoutubeHelper
{

	/**
	 * Short description for 'getFeed'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $params Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	static function getFeed($params)
	{
		// module params
		$rssurl			= $params->get('rssurl', '');
		$limit			= (int) $params->get('rssitems', 5);
		$pick_random	= $params->get('pick_random', 0);

		// if ordered, get only limited number of items
		//$rssurl			.= $pick_random ? '' : '&max-results='.$limit;

		//  get RSS parsed object
		$options = array();
		$options['rssUrl'] 		= $rssurl;
		$options['cache_time'] = null;

		$rssDoc =& JFactory::getXMLparser('RSS', $options);

		$feed = new stdclass();

		if ($rssDoc != false)
		{
			// channel header and link
			$feed->title = $rssDoc->get_title();
			$feed->link = $rssDoc->get_link();
			$feed->description = $rssDoc->get_description();

			// channel image if exists
			$feed->image->url = $rssDoc->get_image_url();
			$feed->image->title = $rssDoc->get_image_title();

			// items
			$items = $rssDoc->get_items();

			// feed elements
			if($pick_random) {
				// randomize items
				shuffle($items);
			}
			$feed->items = array_slice($items, 0, $limit);
		} else {
			$feed = false;
		}

		return $feed;
	}
}
