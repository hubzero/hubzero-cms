<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\Feed;

use Hubzero\Module\Module;
use JFactory;
use stdClass;
use JText;

/**
 * Module class for displaying items from a feed
 */
class Helper extends Module
{
	/**
	 * Get contents of a feed
	 *
	 * @return  mixed
	 */
	public function getFeed()
	{
		$rssurl = $this->params->get('rssurl', '');

		// Get RSS parsed object
		$cache_time = 0;
		if ($this->params->get('cache'))
		{
			// The cache_time will get fed into JCache to initiate the feed_parser cache group and eventually
			// JCacheStorage will multiply the value by 60 and use that for its lifetime. The only way to sync
			// the feed_parser cache (which caches with an empty dataset anyway) with the module cache is to
			// first divide the module's cache time by 60 then inject that forward, which once stored into the
			// JCacheStorage object, will be the correct value in minutes.
			$cache_time = $this->params->get('cache_time', 15) / 60;
		}

		$rssDoc = JFactory::getFeedParser($rssurl, $cache_time);

		$feed = new stdClass;

		if ($rssDoc != false)
		{
			// Channel header and link
			$feed->title        = $rssDoc->get_title();
			$feed->link         = $rssDoc->get_link();
			$feed->description  = $rssDoc->get_description();

			// Channel image if exists
			$feed->image = new stdClass;
			$feed->image->url   = $rssDoc->get_image_url();
			$feed->image->title = $rssDoc->get_image_title();

			// Items
			$items = $rssDoc->get_items();

			// Feed elements
			$feed->items = array_slice($items, 0, $params->get('rssitems', 5));
		}
		else
		{
			$feed = false;
		}

		return $feed;
	}

	/**
	 * Display module
	 *
	 * @return  void
	 */
	public function display()
	{
		// Legacy compatibility for older view overrides
		$params = $this->params;
		$module = $this->module;

		$rssurl = $params->get('rssurl', '');
		$rssrtl = $params->get('rssrtl', 0);

		// Check if feed URL has been set
		if (empty($rssurl))
		{
			echo '<p class="warning">';
			echo JText::_('MOD_FEED_ERR_NO_URL');
			echo '</p>';
			return;
		}

		$feed = $this->getFeed();
		$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

		require $this->getLayoutPath($params->get('layout', 'default'));
	}
}
