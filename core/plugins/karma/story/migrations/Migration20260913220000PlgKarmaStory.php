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
 * Register plg_karma_story and seed the rules it emits
 *
 * Seeded rather than created on demand, so an administrator can see and
 * retune them before anybody has earned anything. Seeded once: a rule that
 * already exists belongs to whoever last edited it, and this migration is
 * not entitled to an opinion about that.
 **/
class Migration20260913220000PlgKarmaStory extends Base
{
	/**
	 * The rules, as they arrive
	 *
	 * @var  array
	 */
	protected $rules = array(
		array(
			'alias'          => 'story.comment.upmoderated',
			'title'          => 'Comment found worth reading',
			'description'    => 'Somebody spent a moderation credit raising this comment.',
			'delta'          => 1,
			'daily_cap'      => 8,
			'per_source_cap' => 2
		),
		array(
			'alias'          => 'story.comment.downmoderated',
			'title'          => 'Comment found not worth reading',
			'description'    => 'Somebody spent a moderation credit lowering this comment.',
			'delta'          => -1,
			'daily_cap'      => 4,
			'per_source_cap' => 1
		),
		array(
			'alias'          => 'story.submission.accepted',
			'title'          => 'Submission became a story',
			'description'    => 'An editor put out a story from this member\'s submission.',
			'delta'          => 3,
			'daily_cap'      => 9,
			'per_source_cap' => 1
		)
	);

	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('karma', 'story', 1);

		if (!$this->db->tableExists('#__karma_rules') || !$this->db->tableExists('#__karma_scales'))
		{
			return;
		}

		$this->db->setQuery("SELECT `id` FROM `#__karma_scales` WHERE `alias` = 'global' LIMIT 1");

		if (!$scale = $this->db->loadResult())
		{
			$this->log('plg_karma_story: no global scale, so no rules were seeded.');
			return;
		}

		foreach ($this->rules as $rule)
		{
			$this->db->setQuery("SELECT `id` FROM `#__karma_rules` WHERE `alias` = " . $this->db->quote($rule['alias']) . " LIMIT 1");

			if ($this->db->loadResult())
			{
				continue;
			}

			$query = "INSERT INTO `#__karma_rules`
				(`scale_id`, `alias`, `title`, `description`, `delta`, `daily_cap`, `per_source_cap`, `requires_karma`, `state`)
				VALUES (" . (int) $scale . ", "
				. $this->db->quote($rule['alias']) . ", "
				. $this->db->quote($rule['title']) . ", "
				. $this->db->quote($rule['description']) . ", "
				. (int) $rule['delta'] . ", "
				. (int) $rule['daily_cap'] . ", "
				. (int) $rule['per_source_cap'] . ", 0, 1);";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 *
	 * A rule that has already moved somebody's standing is left where it is.
	 * Deleting it would not undo the karma, only the explanation of it, and a
	 * ledger nobody can read back is worse than one nobody uses.
	 **/
	public function down()
	{
		$this->deletePluginEntry('karma', 'story');

		if (!$this->db->tableExists('#__karma_rules'))
		{
			return;
		}

		foreach ($this->rules as $rule)
		{
			if ($this->db->tableExists('#__karma_ledgers'))
			{
				$this->db->setQuery("SELECT COUNT(*) FROM `#__karma_ledgers` WHERE `rule` = " . $this->db->quote($rule['alias']));

				if ($this->db->loadResult())
				{
					$this->log('plg_karma_story: ' . $rule['alias'] . ' has ledger history and was left in place.');
					continue;
				}
			}

			$this->db->setQuery("DELETE FROM `#__karma_rules` WHERE `alias` = " . $this->db->quote($rule['alias']));
			$this->db->query();
		}
	}
}
