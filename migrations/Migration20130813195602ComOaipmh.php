<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for installing oaipmh component
 **/
class Migration20130813195602ComOaipmh extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (!$db->tableExists('#__oaipmh_dcspecs'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__oaipmh_dcspecs` (
							`id` int(11) NOT NULL AUTO_INCREMENT,
							`name` varchar(255) NOT NULL,
							`query` text NOT NULL,
							`display` int(1) NOT NULL DEFAULT '0',
							PRIMARY KEY (`id`)
						) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

			$db->setQuery($query);
			$db->query();

			$query = "INSERT INTO `#__oaipmh_dcspecs` (`id`, `name`, `query`, `display`) VALUES
						(1, 'resource IDs', 'SELECT p.id FROM #__publications p, #__publication_versions pv WHERE p.id = pv.publication_id AND pv.state = 1', 1),
						(2, 'specify sets', '', 1),
						(3, 'title', 'SELECT pv.title FROM #__publication_versions pv, #__publications p WHERE p.id = pv.publication_id AND p.id = $id LIMIT 1', 1),
						(4, 'creator', 'SELECT pa.name FROM #__publication_authors pa, #__publication_versions pv, #__publications p WHERE pa.publication_version_id = pv.id AND pv.publication_id = p.id AND p.id = $id LIMIT 1', 1),
						(5, 'subject', 'SELECT t.raw_tag FROM #__tags t, #__tags_object tos WHERE t.id = tos.tagid AND tos.objectid = $id ORDER BY t.raw_tag', 1),
						(6, 'date', 'SELECT pv.submitted FROM #__publication_versions pv, #__publications p WHERE p.id = pv.publication_id AND p.id = $id ORDER BY pv.submitted LIMIT 1', 1),
						(7, 'identifier', 'SELECT pv.doi FROM #__publication_versions pv, #__publications p WHERE p.id = pv.publication_id AND pv.state = 1 AND p.id = $id', 1),
						(8, 'description', 'SELECT pv.description FROM #__publication_versions pv, #__publications p WHERE p.id = pv.publication_id AND p.id = $id LIMIT 1', 1),
						(9, 'type', 'Dataset', 1),
						(10, 'publisher', 'myhub', 1),
						(11, 'rights', 'SELECT pl.title FROM #__publications p, #__publication_versions pv, #__publication_licenses pl WHERE pl.id = pv.license_type AND pv.publication_id = p.id AND p.id = $id LIMIT 1', 1),
						(12, 'contributor', 'SELECT pa.name FROM #__publication_authors pa, #__publication_versions pv, #__publications p WHERE pa.publication_version_id = pv.id AND pv.publication_id = p.id AND p.id = $id AND pv.state = 1', 1),
						(13, 'relation', 'SELECT DISTINCT path FROM #__publication_attachments pa WHERE publication_id = $id AND role = 1 ORDER BY path', 1),
						(14, 'format', '', 1),
						(15, 'coverage', '', 1),
						(16, 'language', '', 1),
						(17, 'source', '', 1);";

			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableExists('#__oaipmh_dcspecs'))
		{
			$query = "DROP TABLE IF EXISTS `#__oaipmh_dcspecs`;";

			$db->setQuery($query);
			$db->query();
		}
	}
}