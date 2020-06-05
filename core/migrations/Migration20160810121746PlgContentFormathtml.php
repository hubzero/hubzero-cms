<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for enabling the plugin by default.
 **/
class Migration20160810121746PlgContentFormathtml extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "UPDATE `#__extensions` SET `enabled`=1 WHERE `folder`=" . $this->db->quote('content') . " AND `element`=" . $this->db->quote('formathtml');
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "UPDATE `#__extensions` SET `enabled`=0 WHERE `folder`=" . $this->db->quote('content') . " AND `element`=" . $this->db->quote('formathtml');
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
