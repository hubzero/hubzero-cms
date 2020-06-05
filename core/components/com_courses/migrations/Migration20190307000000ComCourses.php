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
 * Migration script for changing DATETIME fields default to NULL for com_courses
 **/
class Migration20190307000000ComCourses extends Base
{
	/**
	 * List of tables and their datetime fields
	 *
	 * @var  array
	 **/
	public static $tables = array(
		'#__courses' => array(
			'created'
		),
		'#__courses_announcements' => array(
			'created',
			'publish_up',
			'publish_down'
		),
		'#__courses_asset_groups' => array(
			'created'
		),
		'#__courses_assets' => array(
			'created'
		),
		'#__courses_log' => array(
			'timestamp'
		),
		'#__courses_member_notes' => array(
			'created'
		),
		'#__courses_members' => array(
			'enrolled',
			'first_visit'
		),
		'#__courses_offering_section_codes' => array(
			'created',
			'expires',
			'redeemed'
		),
		'#__courses_offering_section_dates' => array(
			'created',
			'publish_up',
			'publish_down'
		),
		'#__courses_offering_sections' => array(
			'created',
			'publish_up',
			'publish_down',
			'start_date',
			'end_date'
		),
		'#__courses_offerings' => array(
			'created',
			'publish_up',
			'publish_down'
		),
		'#__courses_page_hits' => array(
			'datetime'
		),
		'#__courses_reviews' => array(
			'created',
			'modified'
		),
		'#__courses_units' => array(
			'created'
		)
	);

	/**
	 * Up
	 **/
	public function up()
	{
		foreach (self::$tables as $table => $fields)
		{
			foreach ($fields as $field)
			{
				if ($this->db->tableExists($table)
				 && $this->db->tableHasField($table, $field))
				{
					$query = "ALTER TABLE `$table` CHANGE `$field` `$field` DATETIME  NULL  DEFAULT NULL";

					$this->db->setQuery($query);
					$this->db->query();

					$query = "UPDATE `$table` SET `$field`=NULL WHERE `$field`='0000-00-00 00:00:00'";

					$this->db->setQuery($query);
					$this->db->query();
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
			foreach ($fields as $field)
			{
				if ($this->db->tableExists($table)
				 && $this->db->tableHasField($table, $field))
				{
					$query = "ALTER TABLE `$table` CHANGE `$field` `$field` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00'";

					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}
}
