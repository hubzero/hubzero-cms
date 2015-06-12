<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for 2011/12 views
 **/
class Migration20120101000005Core extends Base
{
	public function up()
	{
		if (!$this->db->tableExists('#__resource_contributors_view'))
		{
			$query  = "CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `#__resource_contributors_view`";
			$query .= " AS SELECT `m`.`uidNumber` AS `uidNumber`,count(`AA`.`authorid`) AS `count`";
			$query .= " FROM ((`#__xprofiles` `m` left join `#__author_assoc` `AA` on(((`AA`.`authorid` = `m`.`uidNumber`) and (`AA`.`subtable` = _utf8'resources')))) join `#__resources` `R` on(((`R`.`id` = `AA`.`subid`) and (`R`.`published` = 1) and (`R`.`standalone` = 1)))) where (`m`.`public` = 1) group by `m`.`uidNumber`";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__wiki_contributors_view'))
		{
			$query  = "CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `#__wiki_contributors_view`";
			$query .= " AS SELECT `m`.`uidNumber` AS `uidNumber`,count(`w`.`id`) AS `count`";
			$query .= " FROM (`#__xprofiles` `m` left join `#__wiki_page` `w` on(((`w`.`access` <> 1) and ((`w`.`created_by` = `m`.`uidNumber`) or ((`m`.`username` <> _utf8'') and (`w`.`authors` like concat(_utf8'%',`m`.`username`,_utf8'%'))))))) where ((`m`.`public` = 1) and (`w`.`id` is not null)) group by `m`.`uidNumber`";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__contributor_ids_view'))
		{
			$query  = "CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `#__contributor_ids_view`";
			$query .= " AS SELECT `#__resource_contributors_view`.`uidNumber` AS `uidNumber`";
			$query .= " FROM `#__resource_contributors_view` union select `#__wiki_contributors_view`.`uidNumber` AS `uidNumber` from `#__wiki_contributors_view`";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__contributors_view'))
		{
			$query  = "CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `#__contributors_view`";
			$query .= " AS SELECT `c`.`uidNumber` AS `uidNumber`,coalesce(`r`.`count`,0) AS `resource_count`,coalesce(`w`.`count`,0) AS `wiki_count`,(coalesce(`w`.`count`,0) + coalesce(`r`.`count`,0)) AS `total_count`";
			$query .= " FROM ((`#__contributor_ids_view` `c` left join `#__resource_contributors_view` `r` on((`r`.`uidNumber` = `c`.`uidNumber`))) left join `#__wiki_contributors_view` `w` on((`w`.`uidNumber` = `c`.`uidNumber`)))";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}