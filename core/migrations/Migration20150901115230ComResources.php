<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Creative Commons 4.0 license to resources
 **/
class Migration20150901115230ComResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__resource_licenses'))
		{
			$query = "SELECT id FROM `#__resource_licenses` WHERE `name` = 'cc40-by-nc-sa' LIMIT 1";
			$this->db->setQuery($query);
			$id = $this->db->loadResult();

			if (!$id)
			{
				include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'license.php');

				$query = "SELECT ordering FROM `#__resource_licenses` ORDER BY ordering DESC LIMIT 1";
				$this->db->setQuery($query);
				$ordering = $this->db->loadResult();

				$tbl = new \Components\Resources\Tables\License($this->db);
				$tbl->ordering = intval($ordering) + 1;
				$tbl->name     = 'cc40-by-nc-sa';
				$tbl->title    = 'Creative Commons BY-NC-SA 4.0';
				$tbl->url      = 'http://creativecommons.org/licenses/by-nc-sa/4.0/';
				$tbl->text     = 'You are free:

to Share — copy and redistribute the material in any medium or format
to Adapt — remix, transform, and build upon the material

The licensor cannot revoke these freedoms as long as you follow the license terms.
Under the following terms:

Attribution — You must give appropriate credit, provide a link to the license, and indicate if changes were made. You may do so in any reasonable manner, but not in any way that suggests the licensor endorses you or your use.
NonCommercial — You may not use the material for commercial purposes.
ShareAlike — If you remix, transform, or build upon the material, you must distribute your contributions under the same license as the original.
No additional restrictions — You may not apply legal terms or technological measures that legally restrict others from doing anything the license permits.

Notices:
You do not have to comply with the license for elements of the material in the public domain or where your use is permitted by an applicable exception or limitation.
No warranties are given. The license may not give you all of the permissions necessary for your intended use. For example, other rights such as publicity, privacy, or moral rights may limit how you use the material. 

For more information visit http://creativecommons.org/licenses/by-nc-sa/4.0/legalcode.';
				$tbl->check();
				$tbl->store();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__resource_licenses'))
		{
			$query = "SELECT id FROM `#__resource_licenses` WHERE `name` = 'cc40-by-nc-sa' LIMIT 1";
			$this->db->setQuery($query);
			$id = $this->db->loadResult();

			if ($id)
			{
				// Set the first zone as default
				$query = "DELETE FROM `#__resource_licenses` WHERE `name` = 'cc40-by-nc-sa'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}