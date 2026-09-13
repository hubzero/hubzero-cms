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
 * Seed a gate, so the standing view has something to say
 *
 * A posting rate limit is the clearest example of what a gate is for: karma
 * in, a number the caller can act on out, and the caller never learns the
 * bands behind it.
 **/
class Migration20260913140100ComKarmaGates extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__karma_gates') || !$this->db->tableExists('#__karma_scales'))
		{
			return;
		}

		$this->db->setQuery("SELECT `id` FROM `#__karma_scales` WHERE `alias` = 'global' LIMIT 1");

		if (!$scale = $this->db->loadResult())
		{
			$this->log('Karma: no global scale, so no gate was seeded.');
			return;
		}

		$this->db->setQuery("SELECT `id` FROM `#__karma_gates` WHERE `alias` = 'karma.posts_per_day' LIMIT 1");

		if ($this->db->loadResult())
		{
			return;
		}

		$query = "INSERT INTO `#__karma_gates` (`scale_id`, `alias`, `title`, `description`, `bands`, `default_value`)
			VALUES (" . (int) $scale . ", 'karma.posts_per_day', 'Posts per day',
			'How many times a member may post in a day, by karma.',
			'-5=3|10=20|99999=40', '2');";

		$this->db->setQuery($query);
		$this->db->query();
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$this->db->tableExists('#__karma_gates'))
		{
			return;
		}

		$this->db->setQuery("DELETE FROM `#__karma_gates` WHERE `alias` = 'karma.posts_per_day'");
		$this->db->query();
	}
}
