<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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

/**
 * Module class for reading feed data
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
		// [!] Backwards compatibility for any view overrides
		$params = $this->params;

		$rssurl = $this->params->get('rssurl', '');
		$rssrtl = $this->params->get('rssrtl', 0);

		//check if cache diretory is writable as cache files will be created for the feed
		$cacheDir = PATH_APP . '/cache/admin';
		if (!is_writable($cacheDir))
		{
			echo '<div class="error">';
			echo Lang::txt('MOD_FEED_ERR_CACHE');
			echo '</div>';
			return;
		}

		//check if feed URL has been set
		if (empty ($rssurl))
		{
			echo '<div class="error">';
			echo Lang::txt('MOD_FEED_ERR_NO_URL');
			echo '</div>';
			return;
		}

		// module params
		$rssurl          = $this->params->get('rssurl', '');
		$rssitems        = $this->params->get('rssitems', 5);
		$rssdesc         = $this->params->get('rssdesc', 1);
		$rssimage        = $this->params->get('rssimage', 1);
		$rssitemdesc     = $this->params->get('rssitemdesc', 1);
		$words           = $this->params->def('word_count', 0);
		$rsstitle        = $this->params->get('rsstitle', 1);
		$rssrtl          = $this->params->get('rssrtl', 0);
		$moduleclass_sfx = $this->params->get('moduleclass_sfx', '');

		$filter = \JFilterInput::getInstance();

		// get RSS parsed object
		$cache_time = 0;
		if ($this->params->get('cache'))
		{
			// The cache_time will get fed into JCache to initiate the feed_parser cache group and eventually
			// JCacheStorage will multiply the value by 60 and use that for its lifetime. The only way to sync
			// the feed_parser cache (which caches with an empty dataset anyway) with the module cache is to
			// first divide the module's cache time by 60 then inject that forward, which once stored into the
			// JCacheStorage object, will be the correct value in minutes.
			$cache_time  = $this->params->get('cache_time', 15) / 60;
		}

		$rssDoc = \App::get('feed.parser');
		$rssDoc->set_feed_url($rssurl);
		$rssDoc->set_cache_duration($cache_time);
		$rssDoc->init();

		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}
}
