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
 * Migration script for fixing courses references to user_id that should really be member_id
 **/
class Migration20130905195600ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableHasField('#__courses_grade_book', 'user_id'))
		{
			// Fix gradebook entires
			$query = "SELECT * FROM `#__courses_grade_book` ORDER BY `user_id` ASC";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

			if ($results && count($results) > 0)
			{
				foreach ($results as $r)
				{
					switch ($r->scope)
					{
						case 'asset':
							$query = "SELECT `course_id` FROM `#__courses_assets` WHERE `id` = '{$r->scope_id}'";
							$this->db->setQuery($query);
							$course_id = $this->db->loadResult();
						break;

						case 'unit':
							$query = "SELECT `course_id` FROM `#__courses_units` AS cu, `#__courses_offerings` AS co WHERE cu.offering_id = co.id AND cu.id = '{$r->scope_id}'";
							$this->db->setQuery($query);
							$course_id = $this->db->loadResult();
						break;

						case 'course':
							$course_id = $r->scope_id;
						break;
					}

					$query  = "SELECT `id` FROM `#__courses_members` WHERE `user_id` = '{$r->user_id}'";
					$query .= " AND `course_id` = '{$course_id}'";
					$query .= " ORDER BY student DESC, first_visit DESC";
					$this->db->setQuery($query);
					$id = $this->db->loadResult();

					if ($id)
					{
						$query = "UPDATE `#__courses_grade_book` SET `user_id` = '{$id}' WHERE `id` = '{$r->id}'";
						$this->db->setQuery($query);
						$this->db->query();
					}
					else
					{
						$query = "DELETE FROM `#__courses_grade_book` WHERE `id` = '{$r->id}'";
						$this->db->setQuery($query);
						$this->db->query();
					}
				}
			}
		}

		if ($this->db->tableHasField('#__courses_grade_book', 'user_id') && !$this->db->tableHasField('#__courses_grade_book', 'member_id'))
		{
			$query = "ALTER TABLE `#__courses_grade_book` CHANGE `user_id` `member_id` INT(11) NOT NULL";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Fix old asset views data that doesn't have course_id filled in...
		$query = "SELECT DISTINCT(asset_id) FROM `#__courses_asset_views` WHERE `course_id` IS NULL";
		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		if ($results && count($results) > 0)
		{
			foreach ($results as $r)
			{
				$query  = "SELECT `course_id` FROM `#__courses_assets` WHERE `id` = '{$r->asset_id}'";
				$this->db->setQuery($query);
				$id = $this->db->loadResult();

				if ($id)
				{
					$query = "UPDATE `#__courses_asset_views` SET `course_id` = '{$id}' WHERE `asset_id` = '{$r->asset_id}'";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}

		// Fix asset views
		$query = "SELECT * FROM `#__courses_asset_views`";
		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		if ($results && count($results) > 0)
		{
			foreach ($results as $r)
			{
				$query  = "SELECT `id` FROM `#__courses_members` WHERE `user_id` = '{$r->viewed_by}'";
				$query .= " AND `course_id` = '{$r->course_id}'";
				$query .= " ORDER BY student DESC, first_visit DESC";
				$this->db->setQuery($query);
				$id = $this->db->loadResult();

				if ($id)
				{
					$query = "UPDATE `#__courses_asset_views` SET `viewed_by` = '{$id}' WHERE `id` = '{$r->id}'";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}

		if ($this->db->tableHasField('#__courses_form_respondents', 'user_id'))
		{
			// Fix form respondents
			$query = "SELECT * FROM `#__courses_form_respondents`";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

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
					$this->db->setQuery($query);
					$course_id = $this->db->loadResult();

					$query  = "SELECT `id` FROM `#__courses_members` WHERE `user_id` = '{$r->user_id}'";
					$query .= " AND `course_id` = '{$course_id}'";
					$query .= " ORDER BY student DESC, first_visit DESC";
					$this->db->setQuery($query);
					$id = $this->db->loadResult();

					$query = "UPDATE `#__courses_form_respondents` SET `user_id` = '{$id}' WHERE `id` = '{$r->id}'";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}

		if ($this->db->tableHasField('#__courses_form_respondents', 'user_id') && !$this->db->tableHasField('#__courses_form_respondents', 'member_id'))
		{
			$query = "ALTER TABLE `#__courses_form_respondents` CHANGE `user_id` `member_id` INT(11) NOT NULL";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
