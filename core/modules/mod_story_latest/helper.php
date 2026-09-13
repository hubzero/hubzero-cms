<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\StoryLatest;

use Hubzero\Module\Module;
use Components\Story\Models\Story;

/**
 * The most recent stories, for a sidebar
 */
class Helper extends Module
{
	/**
	 * Gather what the layout needs, then hand over to it
	 *
	 * @return  void
	 */
	public function display()
	{
		$path = PATH_CORE . DS . 'components' . DS . 'com_story' . DS . 'models' . DS . 'story.php';

		if (!file_exists($path))
		{
			return;
		}


		require_once $path;

		$limit = (int) $this->params->get('limit', 5);

		// Only what is actually out. A sidebar that lists a story before its
		// hour has come gives the schedule away, and links to nothing.
		$this->rows = Story::published()
			->order('publish_up', 'desc')
			->limit($limit > 0 ? $limit : 5)
			->rows();

		parent::display();
	}
}
