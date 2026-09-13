<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\StorySubmissions;

use Hubzero\Module\Module;
use Components\Story\Models\Submission;
use User;

/**
 * How deep the queue is, and what is at the top of it
 *
 * For editors. It renders for nobody else — a queue depth is not secret, but
 * it is not interesting to a reader either, and a sidebar earns its place by
 * being useful to whoever is looking at it.
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
		if (!User::authorise('story.publish', 'com_story'))
		{
			return;
		}

		$path = PATH_CORE . DS . 'components' . DS . 'com_story' . DS . 'models' . DS . 'submission.php';

		if (!file_exists($path))
		{
			return;
		}

		require_once $path;

		$limit = (int) $this->params->get('limit', 5);

		$this->depth = Submission::open()->total();

		// Ranked by what other editors have marked rather than by the public
		// vote: this is the editors' own signal to each other.
		$this->rows = Submission::open()
			->order('attention_needed', 'desc')
			->order('editor_popularity', 'desc')
			->limit($limit > 0 ? $limit : 5)
			->rows();

		parent::display();
	}
}
