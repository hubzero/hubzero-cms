<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Story\Models\Submission;
use Hubzero\Utility\Date;

/**
 * Scheduled work for the news component
 */
class plgCronStory extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Declare the jobs this plugin offers
	 *
	 * @return  object
	 */
	public function onCronEvents()
	{
		$this->loadLanguage();

		$obj = new stdClass();
		$obj->plugin = $this->_name;
		$obj->events = array(
			array(
				'name'   => 'decaySubmissionScores',
				'label'  => Lang::txt('PLG_CRON_STORY_DECAY_SCORES'),
				'params' => ''
			)
		);

		return $obj;
	}

	/**
	 * Let time take the queue back toward its resting order
	 *
	 * Without this a queue only ever grows: an entry nobody voted for three
	 * weeks ago sits at the same height as one submitted this morning, and an
	 * editor has to reject things purely to get them out of the way. Decay
	 * means the queue tidies itself and rejection stays a judgement rather
	 * than a chore.
	 *
	 * Only what is still open is touched. A decided submission's score is a
	 * record of what happened, not a live ranking.
	 *
	 * @param   object  $job
	 * @return  boolean
	 */
	public function decaySubmissionScores($job)
	{
		$path = PATH_CORE . DS . 'components' . DS . 'com_story' . DS . 'models' . DS . 'submission.php';

		if (!file_exists($path))
		{
			return true;
		}

		require_once $path;

		$config = \Component::params('com_story');
		$now    = Date::of('now');

		$rows = Submission::all()
			->whereIn('state', Submission::openStates())
			->rows();

		foreach ($rows as $row)
		{
			$before = (float) $row->get('popularity');

			$row->decay($config, $now);

			// A row that has not moved enough to matter is not worth a write.
			if (abs((float) $row->get('popularity') - $before) < 0.01)
			{
				continue;
			}

			$row->save();
		}

		return true;
	}
}
