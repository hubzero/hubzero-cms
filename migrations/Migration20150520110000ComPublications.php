<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding curation version table and fill with available data
 **/
class Migration20150520110000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Create versions table
		if (!$this->db->tableExists('#__publication_curation_versions'))
		{
			$query = "CREATE TABLE `#__publication_curation_versions` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `type_id` int(11) NOT NULL DEFAULT '0',
			  `version_number` int(11) NOT NULL DEFAULT '0',
			  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `curation` text NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_type_id_version_number` (`type_id`,`version_number`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Add column to versions table and populate with historic data
		if ($this->db->tableExists('#__publication_versions'))
		{
			// Add curation_version_id column
			if (!$this->db->tableHasField('#__publication_versions', 'curation_version_id'))
			{
				$query = "ALTER TABLE `#__publication_versions` ADD COLUMN curation_version_id int(11)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			// Get versions with saved curation
			$query  = "SELECT DISTINCT(v.curation), t.id as type_id, t.curation as master_curation ";
			$query .= " FROM `#__publication_versions` AS v ";
			$query .= "JOIN `#__publications` AS p ON p.id = v.publication_id ";
			$query .= "JOIN `#__publication_master_types` AS t ON t.id = p.master_type ";
			$query .= "WHERE v.curation IS NOT NULL ";
			$query .= "AND v.curation != '' ";
			$query .= "AND v.accepted !='" . $this->db->getNullDate() . "' ";
			$query .= "ORDER BY v.accepted ASC";

			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

			if ($results && count($results) > 0)
			{
				$path = PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'curation.version.php';
				include_once($path);

				foreach ($results as $result)
				{
					// Determine version number
					$query = "SELECT MAX(version_number) FROM #__publication_curation_versions WHERE type_id=" . $this->db->Quote($result->type_id);
					$this->db->setQuery($query);
					$versionNumber = $this->db->loadResult();
					$versionNumber = intval($versionNumber) + 1;

					$stq = new \Components\Publications\Tables\CurationVersion($this->db);
					$stq->type_id         = $result->type_id;
					$stq->created         = Date::toSql();
					$stq->version_number  = $versionNumber;
					$stq->curation        = $result->curation;

					if ($stq->store())
					{
						$query = "UPDATE `#__publication_versions` SET `curation_version_id`=" . ($stq->id) . " WHERE `curation`=" . $this->db->Quote($result->curation);
						$this->db->setQuery($query);
						$this->db->query();
					}
				}
			}
		}
	}
}