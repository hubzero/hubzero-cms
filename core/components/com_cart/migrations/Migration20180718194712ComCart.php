<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script to create an index on `#__cart_meta`
 **/
class Migration20180718194712ComCart extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__cart_meta')
		 && !$this->db->tableHasKey('#__cart_meta', 'idx_scope_id'))
		{
			$query = "CREATE INDEX idx_scope_id ON `#__cart_meta` (scope_id)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__cart_meta')
		 && $this->db->tableHasKey('#__cart_meta', 'idx_scope_id'))
		{
			$query = "DROP INDEX scope_id_idx ON #__cart_meta";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
