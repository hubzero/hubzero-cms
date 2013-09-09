<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing courses references to user_id that should really be member_id
 **/
class Migration20130905195600ComCourses extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		// Fix gradebook entires
		$query = "SELECT * FROM `#__courses_grade_book`";
		$db->setQuery($query);
		$results = $db->loadObjectList();

		if ($results && count($results) > 0)
		{
			foreach ($results as $r)
			{
				switch ($r->scope)
				{
					case 'asset':
						$query = "SELECT `course_id` FROM `#__courses_assets` WHERE `id` = '{$r->scope_id}'";
						$db->setQuery($query);
						$course_id = $db->loadResult();
					break;

					case 'unit':
						$query = "SELECT `course_id` FROM `#__courses_units` AS cu, `#__courses_offerings` AS co WHERE cu.offering_id = co.id AND cu.id = '{$r->scope_id}'";
						$db->setQuery($query);
						$course_id = $db->loadResult();
					break;

					case 'course':
						$course_id = $r->scope_id;
					break;
				}

				$query  = "SELECT `id` FROM `#__courses_members` WHERE `user_id` = '{$r->user_id}'";
				$query .= " AND `course_id` = '{$course_id}'";
				$query .= " ORDER BY student DESC, first_visit DESC";
				$db->setQuery($query);
				$id = $db->loadResult();

				if ($db->tableHasField('#__courses_grade_book', 'user_id'))
				{
					$query = "UPDATE `#__courses_grade_book` SET `user_id` = '{$id}' WHERE `id` = '{$r->id}'";
					$db->setQuery($query);
					$db->query();
				}
			}
		}

		if ($db->tableHasField('#__courses_grade_book', 'user_id') && !$db->tableHasField('#__courses_grade_book', 'member_id'))
		{
			$query = "ALTER TABLE `#__courses_grade_book` CHANGE `user_id` `member_id` INT(11) NOT NULL";
			$db->setQuery($query);
			$db->query();
		}

		// Fix asset views
		$query = "SELECT * FROM `#__courses_asset_views`";
		$db->setQuery($query);
		$results = $db->loadObjectList();

		if ($results && count($results) > 0)
		{
			foreach ($results as $r)
			{
				$query  = "SELECT `id` FROM `#__courses_members` WHERE `user_id` = '{$r->viewed_by}'";
				$query .= " AND `course_id` = '{$r->course_id}'";
				$query .= " ORDER BY student DESC, first_visit DESC";
				$db->setQuery($query);
				$id = $db->loadResult();

				if ($id)
				{
					$query = "UPDATE `#__courses_asset_views` SET `viewed_by` = '{$id}' WHERE `id` = '{$r->id}'";
					$db->setQuery($query);
					$db->query();
				}
			}
		}

		// Fix form respondents
		$query = "SELECT * FROM `#__courses_form_respondents`";
		$db->setQuery($query);
		$results = $db->loadObjectList();

		if ($results && count($results) > 0)
		{
			foreach ($results as $r)
			{
				$query  = "SELECT `course_id` FROM `#__courses_form_respondents` AS cfr,";
				$query .= " `#__courses_form_deployments` AS cfd,";
				$query .= " `#__courses_forms` AS cf,";
				$query .= " `#__courses_assets` AS ca";
				$query .= " WHERE cfr.deployment_id = cfd.id";
				$query .= " AND cfd.form_id = cf.id";
				$query .= " AND cf.asset_id = ca.id";
				$query .= " AND cfr.id = '{$r->id}'";
				$db->setQuery($query);
				$course_id = $db->loadResult();

				$query  = "SELECT `id` FROM `#__courses_members` WHERE `user_id` = '{$r->user_id}'";
				$query .= " AND `course_id` = '{$course_id}'";
				$query .= " ORDER BY student DESC, first_visit DESC";
				$db->setQuery($query);
				$id = $db->loadResult();

				if ($db->tableHasField('#__courses_form_respondents', 'user_id'))
				{
					$query = "UPDATE `#__courses_form_respondents` SET `user_id` = '{$id}' WHERE `id` = '{$r->id}'";
					$db->setQuery($query);
					$db->query();
				}
			}
		}

		if ($db->tableHasField('#__courses_form_respondents', 'user_id') && !$db->tableHasField('#__courses_form_respondents', 'member_id'))
		{
			$query = "ALTER TABLE `#__courses_form_respondents` CHANGE `user_id` `member_id` INT(11) NOT NULL";
			$db->setQuery($query);
			$db->query();
		}
	}
}