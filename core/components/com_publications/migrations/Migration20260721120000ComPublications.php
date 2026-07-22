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
 * Migration registering the asynchronous publication-bundle build worker as a
 * scheduled cron job (plg_cron_publications::buildPublicationBundles).
 *
 * The dispatcher this job runs is gated by the com_publications `bundle_async`
 * flag and returns immediately when the flag is off, so the job is safe to have
 * present and published on every host: it does nothing until an admin enables
 * async bundle building, at which point it drains the build queue (one build per
 * tick). Registering it here means a host only has to flip `bundle_async` to
 * enable the feature -- the worker is already scheduled.
 *
 * Idempotent: skips if the job is already registered.
 **/
class Migration20260721120000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__cron_jobs'))
		{
			return;
		}

		$this->db->setQuery(
			"SELECT COUNT(*) FROM `#__cron_jobs`
			 WHERE `plugin` = 'publications' AND `event` = 'buildPublicationBundles'"
		);

		if ((int) $this->db->loadResult() > 0)
		{
			return;
		}

		$this->db->setQuery(
			"INSERT INTO `#__cron_jobs`
				(`title`, `state`, `plugin`, `event`, `recurrence`, `next_run`, `created`, `created_by`, `active`, `ordering`, `params`)
			 VALUES
				('Build publication bundles (async)', 1, 'publications', 'buildPublicationBundles', '*/5 * * * *', NOW(), NOW(), 0, 0, 0, '')"
		);
		$this->db->query();
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__cron_jobs'))
		{
			$this->db->setQuery(
				"DELETE FROM `#__cron_jobs`
				 WHERE `plugin` = 'publications' AND `event` = 'buildPublicationBundles'"
			);
			$this->db->query();
		}
	}
}
