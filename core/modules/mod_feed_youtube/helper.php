<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\FeedYoutube;

use Hubzero\Module\Module;
use stdclass;
use Lang;

/**
 * Module class for displaying a YouTube feed
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 * 
	 * @return  void
	 */
	public function display()
	{
		// Module params
		$limit = (int) $this->params->get('rssitems', 5);

		// Get RSS parsed object
		$options = array(
			'rssUrl'     => $this->params->get('rssurl', ''),
			'cache_time' => null
		);

		if (!$options['rssUrl'])
		{
			echo '<p class="warning">' . Lang::txt('MOD_FEED_YOUTUBE_ERROR_NO_URL') . '</p>';
			return;
		}

		$rssDoc = \JFactory::getXMLparser('RSS', $options);

		$this->feed = new stdclass();

		if ($rssDoc != false)
		{
			// Channel header and link
			$this->feed->title        = $rssDoc->get_title();
			$this->feed->link         = $rssDoc->get_link();
			$this->feed->description  = $rssDoc->get_description();

			// Channel image if exists
			$this->feed->image        = new stdClass();
			$this->feed->image->url   = $rssDoc->get_image_url();
			$this->feed->image->title = $rssDoc->get_image_title();

			// Items
			$items = $rssDoc->get_items();

			// Feed elements
			if ($this->params->get('pick_random', 0))
			{
				// Randomize items
				shuffle($items);
			}
			$this->feed->items = array_slice($items, 0, $limit);
		}
		else
		{
			$this->feed = false;
		}

		require $this->getLayoutPath();
	}
}
