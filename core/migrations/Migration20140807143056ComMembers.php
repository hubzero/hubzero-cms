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
 * Migration script for renaming previously added members fulltext index on givenName, middleName and surname fields
 **/
class Migration20140807143056ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// drop orignal key and create new one
		if ($this->db->tableExists('#__xprofiles'))
		{
			if ($this->db->tableHasKey('#__xprofiles', 'jos_xprofiles_fullname_ftidx'))
			{
				$query = "ALTER TABLE `#__xprofiles` DROP INDEX jos_xprofiles_fullname_ftidx;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__xprofiles', 'ftidx_fullname')
				&& $this->db->tableHasField('#__xprofiles', 'givenName')
				&& $this->db->tableHasField('#__xprofiles', 'middleName')
				&& $this->db->tableHasField('#__xprofiles', 'surname'))
			{
				$query = "ALTER TABLE `#__xprofiles` ADD FULLTEXT ftidx_fullname (givenName, middleName, surname);";
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
		if ($this->db->tableExists('#__xprofiles'))
		{
			if ($this->db->tableHasKey('#__xprofiles', 'ftidx_fullname'))
			{
				$query = "ALTER TABLE `#__xprofiles` DROP INDEX ftidx_fullname;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__xprofiles', 'jos_xprofiles_fullname_ftidx')
				&& $this->db->tableHasField('#__xprofiles', 'givenName')
				&& $this->db->tableHasField('#__xprofiles', 'middleName')
				&& $this->db->tableHasField('#__xprofiles', 'surname'))
			{
				$query = "ALTER TABLE `#__xprofiles` ADD FULLTEXT jos_xprofiles_fullname_ftidx (givenName, middleName, surname);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
