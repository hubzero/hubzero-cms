<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for setting character set on tables to UTF8
 **/
class Migration20190228183602Latin1ToUtf8 extends Base
{
	/**
	 * List of tables
	 *
	 * @var  array
	 **/
	public static $tables = array(
		'hg_update_queue',
		'#__audit_results',
		'#__developer_access_tokens',
		'#__developer_applications',
		'#__developer_authorization_codes',
		'#__developer_refresh_tokens',
		'#__geosearch_markers',
		'#__kb_comments',
		'#__media_tracking',
		'#__media_tracking_detailed',
		'#__resource_import_hooks',
		'#__search_blacklist',
		'#__shibboleth_sessions',
		'#__solr_search_searchcomponents',
		'#__storefront_skus',
		'#__support_criteria'
	);

	/**
	 * Get table character set
	 *
	 * @param   string  $tbl
	 * @return  string
	 **/
	private function getCharacterSet($tbl)
	{
		$query = "SELECT CCSA.character_set_name
					FROM information_schema.`TABLES` T, information_schema.`COLLATION_CHARACTER_SET_APPLICABILITY` CCSA
					WHERE CCSA.collation_name = T.table_collation
					AND T.table_schema = " . $this->db->quote(\Config::get('db')) . "
					AND T.table_name = " . $this->db->quote($tbl);

		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	/**
	 * Get table list
	 *
	 * @return  array
	 **/
	private function getTableList()
	{
		return self::$tables;

		//$this->db->setQuery("SHOW TABLES;");
		//return $this->db->loadColumn();
	}

	/**
	 * Up
	 **/
	public function up()
	{
		$tables = $this->getTableList();

		if (empty($tables))
		{
			return;
		}

		foreach ($tables as $tbl)
		{
			if ($this->db->tableExists($tbl))
			{
				$charset = $this->getCharacterSet($tbl);

				if ($charset != 'utf8')
				{
					$query = "ALTER TABLE `$tbl` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci";
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
		$tables = $this->getTableList();

		if (empty($tables))
		{
			return;
		}

		foreach (self::$tables as $tbl)
		{
			if ($this->db->tableExists($tbl))
			{
				$charset = $this->getCharacterSet($tbl);

				if ($charset != 'latin1')
				{
					$query = "ALTER TABLE `$tbl` CONVERT TO CHARACTER SET latin1 COLLATE latin1_swedish_ci";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}
}
