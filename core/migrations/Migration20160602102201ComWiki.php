<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for updating SQL view with renamed wiki tables
 **/
class Migration20160602102201ComWiki extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "DROP VIEW IF EXISTS `#__wiki_contributors_view`;";
		$this->db->setQuery($query);
		$this->db->query();

		$query = "CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `#__wiki_contributors_view`
			AS
				SELECT `m`.`id` AS `uidNumber`, count(`w`.`id`) AS `count`
				FROM `#__users` AS `m`
				LEFT JOIN `#__wiki_pages` AS `w` ON `w`.`access` <> 1 AND `w`.`created_by` = `m`.`id`
				LEFT JOIN `#__wiki_authors` AS `a` ON a.`page_id`=w.`id` AND `m`.`id`=a.`user_id`
				WHERE (`m`.`access` = 1 AND `w`.`id` IS NOT NULL) group by `m`.`id`;";
		$this->db->setQuery($query);
		$this->db->query();
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "DROP VIEW IF EXISTS `#__wiki_contributors_view`;";
		$this->db->setQuery($query);
		$this->db->query();

		$query = "CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `#__wiki_contributors_view`
			AS SELECT `m`.`uidNumber` AS `uidNumber`,count(`w`.`id`) AS `count`
			FROM (`#__xprofiles` `m` left join `#__wiki_page` `w` on(((`w`.`access` <> 1) and ((`w`.`created_by` = `m`.`uidNumber`) or ((`m`.`username` <> _utf8'') and (`w`.`authors` like concat(_utf8'%',`m`.`username`,_utf8'%'))))))) where ((`m`.`public` = 1) and (`w`.`id` is not null)) group by `m`.`uidNumber`;";
		$this->db->setQuery($query);
		$this->db->query();
	}
}
