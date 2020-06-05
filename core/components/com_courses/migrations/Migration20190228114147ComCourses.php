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
 * Migration script for adding indexes to #__courses table
 **/
class Migration20190228114147ComCourses extends Base
{
	/**
	 * List of tables
	 *
	 * @var  array
	 **/
	public static $tables = array(
		'#__courses_asset_views' => array(
			'asset_id',
			'course_id',
			'viewed_by'
		),
		'#__courses_certificates' => array(
			'course_id'
		),
		'#__courses_form_deployments' => array(
			'form_id',
			'user_id'
		),
		'#__courses_form_questions' => array(
			'form_id',
			'page'
		),
		'#__courses_form_respondent_progress' => array(
			'answer_id'
		),
		'#__courses_forms' => array(
			'asset_id'
		),
		'#__courses_log' => array(
			'user_id',
			'actor_id',
			'action'
		),
		'#__courses_offering_section_badges' => array(
			'section_id',
			'criteria_id',
			'published'
		),
		'#__courses_offering_section_codes' => array(
			'section_id'
		),
		'#__courses_pages' => array(
			'course_id',
			'section_id'
		),
		'#__courses_prerequisites' => array(
			'section_id'
		),
		'#__courses_progress_factors' => array(
			'section_id',
			'asset_id'
		),
		'#__courses_reviews' => array(
			'course_id',
			'offering_id'
		)
	);

	/**
	 * Up
	 **/
	public function up()
	{
		foreach (self::$tables as $table => $fields)
		{
			if ($this->db->tableExists($table))
			{
				foreach ($fields as $field)
				{
					if ($this->db->tableHasField($table, $field) && !$this->db->tableHasKey($table, 'idx_' . $field))
					{
						$query = "ALTER TABLE `" . $table . "` ADD INDEX `idx_" . $field . "` (`" . $field . "`)";
						$this->db->setQuery($query);
						$this->db->query();
					}
				}
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		foreach (self::$tables as $table => $fields)
		{
			if ($this->db->tableExists($table))
			{
				foreach ($fields as $field)
				{
					if ($this->db->tableHasKey($table, 'idx_' . $field))
					{
						$query = "ALTER TABLE `" . $table . "` DROP KEY `idx_" . $field . "`";
						$this->db->setQuery($query);
						$this->db->query();
					}
				}
			}
		}
	}
}
