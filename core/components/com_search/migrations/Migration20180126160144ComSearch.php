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
 * Migration script to create the `#__solr_search_searchcomponents` table
 **/
class Migration20180126160144ComSearch extends Base
{
	static $tableName = '#__solr_search_searchcomponents';

	public function up()
	{
		$tableName = self::$tableName;
		if (!$this->db->tableExists($tableName))
		{
			$createTable = "CREATE TABLE `{$tableName}` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(45) NOT NULL,
			  `created` datetime DEFAULT NULL,
			  `indexed` datetime DEFAULT NULL,
			  `state` varchar(45) DEFAULT NULL,
			  `indexed_records` int(10) unsigned DEFAULT NULL,
			  PRIMARY KEY (`id`)
			);";
			$this->db->setQuery($createTable);
			$this->db->query();
		}
		$params = $this->getParams('com_search');
		$params->set('solr_commit', '300000');
		$params->set('solr_batchsize', '1500');
		$this->saveParams('com_search', $params);
	}

	public function	down()
	{
		$tableName = self::$tableName;

		if ($this->db->tableExists($tableName))
		{
			$dropTable = "DROP TABLE {$tableName};";
			$this->db->setQuery($dropTable);
			$this->db->query();
		}

	}
}
