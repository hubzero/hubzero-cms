<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add missing #__user_roles table
 **/
class Migration2016090511300000Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__user_roles'))
                {
                        $query = "CREATE TABLE IF NOT EXISTS `#__user_roles` (
                                  `user_id` INT(11) NOT NULL ,
                                  `role` VARCHAR(20) NOT NULL ,
                                  `group_id` INT(11) NULL DEFAULT NULL ,
                                  `id` INT(11) NOT NULL AUTO_INCREMENT ,
                                  PRIMARY KEY (`id`) ,
                                  UNIQUE INDEX `uidx_role_user_id_group_id` (`role` ASC, `user_id` ASC, `group_id` ASC) )
                                ENGINE = MyISAM
                                DEFAULT CHARACTER SET = utf8";
                        $this->db->setQuery($query);
                        $this->db->query();
                }

	}

	/**
	 * Down
	 **/
	public function down()
	{
		/* This is a repair migration. A down method would be invalid */
		/* as this change should have happened in Migration20120101000001Core.php */
		/* Repair is only needed on some hubs, perhaps predating that migration.  */
	}
}
