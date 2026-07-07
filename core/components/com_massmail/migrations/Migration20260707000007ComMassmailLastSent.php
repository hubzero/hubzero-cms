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
 * Track the timestamp of the most recent external delivery the collector has
 * seen in the mail log, so the dashboard can show "last message sent".
 **/
class Migration20260707000007ComMassmailLastSent extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__massmail_postfix_state')
		 && !$this->db->tableHasField('#__massmail_postfix_state', 'last_sent'))
		{
			$this->db->setQuery("ALTER TABLE `#__massmail_postfix_state` ADD COLUMN `last_sent` datetime DEFAULT NULL AFTER `last_offset`;");
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__massmail_postfix_state')
		 && $this->db->tableHasField('#__massmail_postfix_state', 'last_sent'))
		{
			$this->db->setQuery("ALTER TABLE `#__massmail_postfix_state` DROP COLUMN `last_sent`;");
			$this->db->query();
		}
	}
}
