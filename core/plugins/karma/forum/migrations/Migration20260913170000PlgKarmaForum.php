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
 * Register plg_karma_forum and seed the rule it emits
 *
 * The rule is seeded rather than created on demand so that an administrator
 * can see and retune it before anybody has earned anything. It is seeded once:
 * a rule that already exists belongs to whoever last edited it.
 **/
class Migration20260913170000PlgKarmaForum extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('karma', 'forum', 1);

		if (!$this->db->tableExists('#__karma_rules') || !$this->db->tableExists('#__karma_scales'))
		{
			return;
		}

		$this->db->setQuery("SELECT `id` FROM `#__karma_scales` WHERE `alias` = 'global' LIMIT 1");

		if (!$scale = $this->db->loadResult())
		{
			$this->log('plg_karma_forum: no global scale, so no rule was seeded.');
			return;
		}

		$this->db->setQuery("SELECT `id` FROM `#__karma_rules` WHERE `alias` = 'forum.post.liked' LIMIT 1");

		if ($this->db->loadResult())
		{
			return;
		}

		$query = "INSERT INTO `#__karma_rules`
			(`scale_id`, `alias`, `title`, `description`, `delta`, `daily_cap`, `per_source_cap`, `requires_karma`, `state`)
			VALUES (" . (int) $scale . ", 'forum.post.liked', 'Forum post was marked useful',
			'Somebody other than the author marked a forum post useful.',
			1, 10, 1, 0, 1);";

		$this->db->setQuery($query);
		$this->db->query();
	}

	/**
	 * Down
	 *
	 * The rule is left where it has history, for the same reason a scale is:
	 * removing it would not undo the karma, only the explanation of it.
	 **/
	public function down()
	{
		$this->deletePluginEntry('karma', 'forum');

		if (!$this->db->tableExists('#__karma_rules'))
		{
			return;
		}

		if ($this->db->tableExists('#__karma_ledgers'))
		{
			$this->db->setQuery("SELECT COUNT(*) FROM `#__karma_ledgers` WHERE `rule` = 'forum.post.liked'");

			if ($this->db->loadResult())
			{
				$this->log('plg_karma_forum: the rule has ledger history and was left in place.');
				return;
			}
		}

		$this->db->setQuery("DELETE FROM `#__karma_rules` WHERE `alias` = 'forum.post.liked'");
		$this->db->query();
	}
}
