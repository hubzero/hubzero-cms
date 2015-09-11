<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Feed;

use Hubzero\Module\Module;
use stdClass;
use Lang;

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

		$rssDoc = \App::get('feed.parser');
		$rssDoc->set_feed_url($rssurl);
		$rssDoc->set_cache_duration($cache_time);
		$rssDoc->init();

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
			$feed->items = array_slice($items, 0, $this->params->get('rssitems', 5));
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
			echo Lang::txt('MOD_FEED_ERR_NO_URL');
			echo '</p>';
			return;
		}

		$feed = $this->getFeed();
		$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

		require $this->getLayoutPath($params->get('layout', 'default'));
	}
}
