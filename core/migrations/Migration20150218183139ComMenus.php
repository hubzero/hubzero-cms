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
 * Migration script for re-setting deprecated 'ordering' field on menu table
 **/
class Migration20150218183139ComMenus extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__menu'))
		{
			$query = "UPDATE `#__menu` SET `ordering`=0;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
