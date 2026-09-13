<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Helpers;

use Hubzero\Config\Registry;
use App;

/**
 * What every comment in one discussion needs to know, worked out once
 *
 * Scoring is per reader, so it cannot be cached across readers, and it runs
 * for every comment on the page. Anything it needs that is the same for all
 * of them is resolved here instead — the score bounds, and the id above which
 * a member counts as new.
 *
 * Building this costs one query. Scoring a comment after it costs none.
 */
class Context
{
	/**
	 * The lowest a score may go
	 *
	 * @var  float
	 */
	protected $floor = -1;

	/**
	 * The highest a score may go
	 *
	 * @var  float
	 */
	protected $ceiling = 5;

	/**
	 * Members with an id above this are in the newest cohort
	 *
	 * @var  integer
	 */
	protected $newestFrom = 0;

	/**
	 * How a reason moves a score, by reason id
	 *
	 * Empty until moderation lands. The lookup is here from the start so the
	 * modifier stack does not have to grow a new shape later.
	 *
	 * @var  array
	 */
	protected $reasons = array();

	/**
	 * Build the context for one rendering
	 *
	 * @param   object  $config  the component's parameters
	 */
	public function __construct($config = null)
	{
		$config = $config ?: new Registry();

		$this->floor   = (float) $config->get('comment_min_score', -1);
		$this->ceiling = (float) $config->get('comment_max_score', 5);

		$this->newestFrom = $this->newestCohort((int) $config->get('new_user_percent', 10));
	}

	/**
	 * The id above which a member counts as new
	 *
	 * A percentile of the id range rather than a fixed count, so it keeps
	 * meaning the same thing as the hub grows. On a hub with no members it
	 * returns zero, which makes nobody new — the right answer, and not a
	 * division by zero.
	 *
	 * @param   integer  $percent
	 * @return  integer
	 */
	protected function newestCohort($percent)
	{
		$percent = max(0, min(100, (int) $percent));

		if (!$percent)
		{
			return 0;
		}

		// If the membership cannot be counted, nobody is new. That is a
		// display detail on every comment of the page, and it is not worth
		// taking the page down over — a reader who adjusts for new members
		// simply gets no adjustment rather than an error.
		try
		{
			$db = App::get('db');
			$db->setQuery("SELECT MAX(`id`) FROM `#__users`");

			$highest = (int) $db->loadResult();
		}
		catch (\Exception $e)
		{
			return 0;
		}

		if ($highest <= 0)
		{
			return 0;
		}

		return (int) ($highest - ($highest * $percent / 100));
	}

	/**
	 * Hold a score to the configured range
	 *
	 * @param   float  $score
	 * @return  float
	 */
	public function clamp($score)
	{
		return max($this->floor, min($this->ceiling, (float) $score));
	}

	/**
	 * The lowest a score may go
	 *
	 * @return  float
	 */
	public function floor()
	{
		return $this->floor;
	}

	/**
	 * The highest a score may go
	 *
	 * @return  float
	 */
	public function ceiling()
	{
		return $this->ceiling;
	}

	/**
	 * Whether a comment was written by one of the newest members
	 *
	 * @param   object   $comment
	 * @return  boolean
	 */
	public function isNewMember($comment)
	{
		if (!$this->newestFrom || !$comment->get('created_by'))
		{
			return false;
		}

		return ((int) $comment->get('created_by') > $this->newestFrom);
	}

	/**
	 * How a moderation reason moves a score
	 *
	 * @param   integer  $reasonId
	 * @return  float
	 */
	public function reasonDelta($reasonId)
	{
		return isset($this->reasons[$reasonId]) ? (float) $this->reasons[$reasonId] : 0;
	}

	/**
	 * Set the newest-member boundary directly
	 *
	 * The percentile is worked out from the member table, which is the right
	 * answer in production and an unhelpful dependency anywhere the boundary
	 * is already known.
	 *
	 * @param   integer  $userId
	 * @return  $this
	 */
	public function setNewestFrom($userId)
	{
		$this->newestFrom = (int) $userId;

		return $this;
	}

	/**
	 * Tell the context how reasons move scores
	 *
	 * @param   array  $reasons  reason id => delta
	 * @return  $this
	 */
	public function setReasons(array $reasons)
	{
		$this->reasons = $reasons;

		return $this;
	}
}
