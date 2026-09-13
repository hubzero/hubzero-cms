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
 * Make forum posts moderatable
 *
 * Adds the score a post carries, the vocabulary its moderators use, and the
 * plugin that ties com_forum to Hubzero\Moderation.
 *
 * The reasons describe the contribution rather than the contributor. On a hub
 * where posts carry real names and institutional affiliations, a vocabulary
 * that labels people would be both unpleasant and a liability.
 **/
class Migration20260913190000PlgModerationForum extends Base
{
	/**
	 * The vocabulary, and what each reason is worth
	 *
	 * @var  array
	 */
	protected $reasons = array(
		array('substantive',  'Substantive',  'Adds something, rather than agreeing at length.',        1, 'forum.post.praised', 1),
		array('well-sourced', 'Well-sourced', 'Backed by something a reader can follow up.',            1, 'forum.post.praised', 2),
		array('clarifying',   'Clarifying',   'Made an earlier point easier to understand.',            1, 'forum.post.praised', 3),
		array('tangential',   'Tangential',   'About something else.',                                 -1, 'forum.post.faulted', 4),
		array('duplicative',  'Duplicative',  'Already said, further up the thread.',                  -1, 'forum.post.faulted', 5),
		array('unsupported',  'Unsupported',  'Asserts what it does not show.',                        -1, 'forum.post.faulted', 6),
	);

	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('moderation', 'forum', 1);

		// A post's moderated score. Nothing reads it until the plugin is
		// enabled, so adding it to an existing forum changes nothing.
		if ($this->db->tableExists('#__forum_posts') && !$this->db->tableHasField('#__forum_posts', 'score'))
		{
			$this->db->setQuery("ALTER TABLE `#__forum_posts` ADD `score` FLOAT NOT NULL DEFAULT 0 AFTER `hits`, ADD INDEX `idx_score` (`score`);");
			$this->db->query();
		}

		if (!$this->db->tableExists('#__moderation_reasons'))
		{
			return;
		}

		foreach ($this->reasons as $reason)
		{
			list($alias, $title, $description, $value, $rule, $ordering) = $reason;

			$this->db->setQuery("SELECT `id` FROM `#__moderation_reasons`
				WHERE `item_type` = 'com_forum.post' AND `alias` = " . $this->db->quote($alias) . " LIMIT 1");

			if ($this->db->loadResult())
			{
				continue;
			}

			$query = "INSERT INTO `#__moderation_reasons`
				(`item_type`, `alias`, `title`, `description`, `value`, `karma_rule`, `reviewable`, `listable`, `fair_fraction`, `state`, `ordering`)
				VALUES ('com_forum.post', " . $this->db->quote($alias) . ", " . $this->db->quote($title) . ", "
				. $this->db->quote($description) . ", " . (int) $value . ", " . $this->db->quote($rule) . ", 1, 1, 0.5, 1, " . (int) $ordering . ");";

			$this->db->setQuery($query);
			$this->db->query();
		}

		$this->seedKarmaRules();
	}

	/**
	 * The karma a moderated post moves
	 *
	 * Named by the reasons rather than baked into them, so what a moderation
	 * is worth to somebody's standing stays in one place.
	 *
	 * @return  void
	 */
	protected function seedKarmaRules()
	{
		if (!$this->db->tableExists('#__karma_rules') || !$this->db->tableExists('#__karma_scales'))
		{
			return;
		}

		$this->db->setQuery("SELECT `id` FROM `#__karma_scales` WHERE `alias` = 'global' LIMIT 1");

		if (!$scale = $this->db->loadResult())
		{
			return;
		}

		$rules = array(
			array('forum.post.praised', 'Forum post was moderated up', 1, 20),
			array('forum.post.faulted', 'Forum post was moderated down', -1, 0),
		);

		foreach ($rules as $rule)
		{
			list($alias, $title, $delta, $cap) = $rule;

			$this->db->setQuery("SELECT `id` FROM `#__karma_rules` WHERE `alias` = " . $this->db->quote($alias) . " LIMIT 1");

			if ($this->db->loadResult())
			{
				continue;
			}

			$query = "INSERT INTO `#__karma_rules`
				(`scale_id`, `alias`, `title`, `description`, `delta`, `daily_cap`, `per_source_cap`, `requires_karma`, `state`)
				VALUES (" . (int) $scale . ", " . $this->db->quote($alias) . ", " . $this->db->quote($title) . ", '', "
				. (int) $delta . ", " . (int) $cap . ", 1, 0, 1);";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 *
	 * The score column stays. Dropping it would discard moderation that
	 * happened, and it costs nothing to leave where nothing reads it.
	 **/
	public function down()
	{
		$this->deletePluginEntry('moderation', 'forum');

		if ($this->db->tableExists('#__moderation_reasons'))
		{
			$this->db->setQuery("DELETE FROM `#__moderation_reasons` WHERE `item_type` = 'com_forum.post'");
			$this->db->query();
		}
	}
}
