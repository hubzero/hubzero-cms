<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for updating view security to be invoker
 **/
class Migration20130828203404Core extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		try
		{
			$query = "ALTER ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `#__contributor_ids_view`
						AS SELECT `#__resource_contributors_view`.`uidNumber` AS `uidNumber`
						FROM `#__resource_contributors_view` union select `#__wiki_contributors_view`.`uidNumber` AS `uidNumber` from `#__wiki_contributors_view`;";
			$db->setQuery($query);
			$db->query();
		}
		catch (Exception $e)
		{
			// Do nothing
		}

		try
		{
			$query = "ALTER ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `#__contributors_view`
						AS SELECT
						`c`.`uidNumber` AS `uidNumber`,coalesce(`r`.`count`,0) AS `resource_count`,coalesce(`w`.`count`,0) AS `wiki_count`,(coalesce(`w`.`count`,0) + coalesce(`r`.`count`,0)) AS `total_count`
						FROM ((`#__contributor_ids_view` `c` left join `#__resource_contributors_view` `r` on((`r`.`uidNumber` = `c`.`uidNumber`))) left join `#__wiki_contributors_view` `w` on((`w`.`uidNumber` = `c`.`uidNumber`)));";
			$db->setQuery($query);
			$db->query();
		}
		catch (Exception $e)
		{
			// Do nothing
		}

		try
		{
			$query = "ALTER ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `#__courses_form_latest_responses_view`
						AS SELECT
						`fre`.`id` AS `id`,
						`fre`.`respondent_id` AS `respondent_id`,
						`fre`.`question_id` AS `question_id`,
						`fre`.`answer_id` AS `answer_id`
						FROM `#__courses_form_responses` `fre` where ((select count(0) from `#__courses_form_responses` `frei` where ((`frei`.`respondent_id` = `fre`.`respondent_id`) and (`frei`.`id` > `fre`.`id`))) < (select count(distinct `frei`.`question_id`) from `#__courses_form_responses` `frei` where (`frei`.`respondent_id` = `fre`.`respondent_id`)));";
			$db->setQuery($query);
			$db->query();
		}
		catch (Exception $e)
		{
			// Do nothing
		}

		try
		{
			$query = "ALTER ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `#__resource_contributors_view`
						AS SELECT
						`m`.`uidNumber` AS `uidNumber`,count(`AA`.`authorid`) AS `count`
						FROM ((`#__xprofiles` `m` left join `#__author_assoc` `AA` on(((`AA`.`authorid` = `m`.`uidNumber`) and (`AA`.`subtable` = _utf8'resources')))) join `#__resources` `R` on(((`R`.`id` = `AA`.`subid`) and (`R`.`published` = 1) and (`R`.`standalone` = 1)))) where (`m`.`public` = 1) group by `m`.`uidNumber`;";
			$db->setQuery($query);
			$db->query();
		}
		catch (Exception $e)
		{
			// Do nothing
		}

		try
		{
			$query = "ALTER ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `#__wiki_contributors_view`
						AS SELECT
						`m`.`uidNumber` AS `uidNumber`,count(`w`.`id`) AS `count`
						FROM (`#__xprofiles` `m` left join `#__wiki_page` `w` on(((`w`.`access` <> 1) and ((`w`.`created_by` = `m`.`uidNumber`) or ((`m`.`username` <> _utf8'') and (`w`.`authors` like concat(_utf8'%',`m`.`username`,_utf8'%'))))))) where ((`m`.`public` = 1) and (`w`.`id` is not null)) group by `m`.`uidNumber`;";
			$db->setQuery($query);
			$db->query();
		}
		catch (Exception $e)
		{
			// Do nothing
		}
	}
}