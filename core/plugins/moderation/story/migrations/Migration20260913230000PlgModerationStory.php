<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Register plg_moderation_story and seed the reasons a comment can be moved for
 *
 * Four either way, and all eight reviewable.
 *
 * The negative four describe the contribution, not the contributor. Nothing
 * here labels a person. On a hub where comments carry real names and
 * institutional affiliations, a moderation vocabulary that judged people
 * rather than posts would be both unpleasant and a liability — 'Dismissive'
 * is as close to conduct as this set goes, and it still describes what the
 * comment did rather than what its author is.
 *
 * Levity is kept as its own category rather than folded into Substantive,
 * and that is the one worth explaining. A reader who wants only substance
 * sets its per-reason adjustment to -1 and never sees jokes again; a reader
 * who enjoys them leaves it alone. That single option is what makes the
 * per-reader modifier stack visibly worth having.
 **/
class Migration20260913230000PlgModerationStory extends Base
{
	/**
	 * The reasons, as they arrive
	 *
	 * alias, title, description, value, ordering
	 *
	 * @var  array
	 */
	protected $reasons = array(
		array('substantive', 'Substantive', 'Adds something, rather than agreeing at length.', 1, 1),
		array('well-sourced', 'Well-sourced', 'Backed by something a reader can go and follow.', 1, 2),
		array('clarifying', 'Clarifying', 'Made an earlier point easier to understand.', 1, 3),
		array('levity', 'Levity', 'Funny, and welcome. Kept separate so a reader who wants only substance can turn it down.', 1, 4),
		array('tangential', 'Tangential', 'About something else.', -1, 5),
		array('duplicative', 'Duplicative', 'Already said, upthread.', -1, 6),
		array('unsupported', 'Unsupported', 'Asserts what it does not show.', -1, 7),
		array('dismissive', 'Dismissive', 'Argues with the person rather than the point.', -1, 8)
	);

	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('moderation', 'story', 1);

		if (!$this->db->tableExists('#__moderation_reasons'))
		{
			return;
		}

		foreach ($this->reasons as $reason)
		{
			list($alias, $title, $description, $value, $ordering) = $reason;

			$this->db->setQuery("SELECT `id` FROM `#__moderation_reasons`
				WHERE `item_type` = 'com_story.comment' AND `alias` = " . $this->db->quote($alias) . " LIMIT 1");

			if ($this->db->loadResult())
			{
				continue;
			}

			$rule = ($value > 0) ? 'story.comment.upmoderated' : 'story.comment.downmoderated';

			$query = "INSERT INTO `#__moderation_reasons`
				(`item_type`, `alias`, `title`, `description`, `value`, `karma_rule`, `reviewable`, `listable`, `fair_fraction`, `state`, `ordering`)
				VALUES ('com_story.comment', " . $this->db->quote($alias) . ", " . $this->db->quote($title) . ", "
				. $this->db->quote($description) . ", " . (int) $value . ", " . $this->db->quote($rule) . ", 1, 1, 0.5, 1, " . (int) $ordering . ");";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 *
	 * A reason that has already been used to move something is left in place.
	 * Deleting it would leave the log entries that cite it pointing at
	 * nothing, and a moderation nobody can read back is worse than one nobody
	 * agrees with.
	 **/
	public function down()
	{
		$this->deletePluginEntry('moderation', 'story');

		if (!$this->db->tableExists('#__moderation_reasons'))
		{
			return;
		}

		foreach ($this->reasons as $reason)
		{
			$alias = $reason[0];

			$this->db->setQuery("SELECT `id` FROM `#__moderation_reasons`
				WHERE `item_type` = 'com_story.comment' AND `alias` = " . $this->db->quote($alias) . " LIMIT 1");

			if (!$id = $this->db->loadResult())
			{
				continue;
			}

			if ($this->db->tableExists('#__moderation_logs'))
			{
				$this->db->setQuery("SELECT COUNT(*) FROM `#__moderation_logs` WHERE `reason_id` = " . (int) $id);

				if ($this->db->loadResult())
				{
					$this->log('plg_moderation_story: ' . $alias . ' has been used and was left in place.');
					continue;
				}
			}

			$this->db->setQuery("DELETE FROM `#__moderation_reasons` WHERE `id` = " . (int) $id);
			$this->db->query();
		}
	}
}
