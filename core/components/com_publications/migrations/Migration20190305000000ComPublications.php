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
 * Migration script for changing DATETIME fields default to NULL for com_publications
 **/
class Migration20190305000000ComPublications extends Base
{
	/**
	 * List of tables and their datetime fields
	 *
	 * @var  array
	 **/
	public static $tables = array(
		'#__publication_audience' => array(
			'created'
		),
		'#__publication_curation_history' => array(
			'created'
		),
		'#__publication_curation_versions' => array(
			'created'
		),
		'#__publication_logs' => array(
			'modified'
		),
		'#__publication_ratings' => array(
			'created'
		),
		'#__publication_versions' => array(
			'created',
			'published_up',
			'published_down',
			'modified',
			'accepted',
			'archived',
			'submitted'
		),
		'#__publications' => array(
			'created',
			'checked_out_time'
		),
		//'#__publication_stats' => array(
		//	'datetime'
		//),
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
