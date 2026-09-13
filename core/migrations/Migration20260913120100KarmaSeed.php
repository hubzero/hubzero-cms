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
 * Seeds the global karma scale
 *
 * Separate from the migration that creates the tables so that a failure here
 * leaves schema that is still sound and a re-run that is still safe. Every
 * insert checks first, so running this twice changes nothing.
 *
 * The scale is bounded either side of zero so that standing can be lost as
 * well as earned, and described in words rather than numbers so that nobody
 * is tempted to optimise it. It is hidden from others by default: publishing
 * a number about somebody's conduct against their real name is a decision a
 * hub should make deliberately rather than inherit.
 **/
class Migration20260913120100KarmaSeed extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__karma_scales'))
		{
			return;
		}

		$this->db->setQuery("SELECT `id` FROM `#__karma_scales` WHERE `alias` = 'global' LIMIT 1");

		if ($this->db->loadResult())
		{
			return;
		}

		$query = "INSERT INTO `#__karma_scales`
			(`alias`, `title`, `description`, `floor`, `ceiling`, `initial`,
			 `decay_per_day`, `decay_after_days`, `decay_toward`,
			 `visibility_self`, `visibility_public`, `adjectives`, `state`, `ordering`)
			VALUES
			('global', 'Global', 'Site-wide standing, fed by every contributing component.',
			 -25, 50, 0,
			 0, 0, 0,
			 'adjective', 'hidden',
			 '-15=Restricted|-5=Provisional|0=Standing|10=Established|30=Trusted|99999=Distinguished', 1, 0);";

		$this->db->setQuery($query);
		$this->db->query();
	}

	/**
	 * Down
	 *
	 * Removes the seeded scale only when nothing has been recorded against
	 * it. A scale carrying real history is left alone: dropping it would
	 * silently discard everybody's standing, and the tables themselves are
	 * removed by the migration this one sits on top of.
	 **/
	public function down()
	{
		if (!$this->db->tableExists('#__karma_scales'))
		{
			return;
		}

		$this->db->setQuery("SELECT `id` FROM `#__karma_scales` WHERE `alias` = 'global' LIMIT 1");

		if (!$id = $this->db->loadResult())
		{
			return;
		}

		if ($this->db->tableExists('#__karma_ledgers'))
		{
			$this->db->setQuery("SELECT COUNT(*) FROM `#__karma_ledgers` WHERE `scale_id` = " . (int) $id);

			if ($this->db->loadResult())
			{
				$this->log('Karma: the global scale has ledger history and was left in place.');
				return;
			}
		}

		$this->db->setQuery("DELETE FROM `#__karma_scales` WHERE `id` = " . (int) $id);
		$this->db->query();
	}
}
