<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\PollTitle;

use Hubzero\Module\Module;
use Components\Poll\Models\Poll;
use Component;

/**
 * Module class for displaying the latest poll's title
 */
class Helper extends Module
{
	/**
	 * Get module contents
	 *
	 * @return  void
	 */
	public function run()
	{
		require_once Component::path('com_poll') . '/models/poll.php';

		// Load the latest poll
		$this->poll = Poll::current();

		require $this->getLayoutPath();
	}

	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		if ($content = $this->getCacheContent())
		{
			echo $content;
			return;
		}

		$this->run();
	}
}
