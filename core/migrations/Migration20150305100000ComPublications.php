<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding master_doi field to #__publications
 **/
class Migration20150305100000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publications'))
		{
			if (!$this->db->tableHasField('#__publications', 'master_doi'))
			{
				$query = "ALTER TABLE `#__publications` ADD COLUMN master_doi varchar(255) DEFAULT '';";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__publications'))
		{
			if ($this->db->tableHasField('#__publications', 'master_doi'))
			{
				$query = "ALTER TABLE `#__publications` DROP `master_doi`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
