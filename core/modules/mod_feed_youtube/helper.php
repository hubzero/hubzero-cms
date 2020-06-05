<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\FeedYoutube;

use Hubzero\Module\Module;
use SimplePie;
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

		$rssDoc = new SimplePie(null, null, 0);
		$rssDoc->enable_cache(false);
		$rssDoc->set_feed_url($this->params->get('rssurl', ''));
		$rssDoc->force_feed(true);
		$rssDoc->init();

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
