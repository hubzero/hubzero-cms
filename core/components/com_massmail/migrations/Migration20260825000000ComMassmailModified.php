<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Track when a mailing was last modified and by whom, mirroring the created /
 * created_by pair. Stamped whenever the mailing's content or status changes.
 **/
class Migration20260825000000ComMassmailModified extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__massmail_mailings'))
		{
			if (!$this->db->tableHasField('#__massmail_mailings', 'modified'))
			{
				$this->db->setQuery("ALTER TABLE `#__massmail_mailings` ADD COLUMN `modified` datetime DEFAULT NULL AFTER `created_by`;");
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__massmail_mailings', 'modified_by'))
			{
				$this->db->setQuery("ALTER TABLE `#__massmail_mailings` ADD COLUMN `modified_by` int(11) DEFAULT NULL AFTER `modified`;");
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__massmail_mailings'))
		{
			if ($this->db->tableHasField('#__massmail_mailings', 'modified_by'))
			{
				$this->db->setQuery("ALTER TABLE `#__massmail_mailings` DROP COLUMN `modified_by`;");
				$this->db->query();
			}
			if ($this->db->tableHasField('#__massmail_mailings', 'modified'))
			{
				$this->db->setQuery("ALTER TABLE `#__massmail_mailings` DROP COLUMN `modified`;");
				$this->db->query();
			}
		}
	}
}
