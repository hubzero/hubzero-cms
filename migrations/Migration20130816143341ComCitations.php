<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20130816143341ComCitations extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		//create new format table
		$query = "CREATE TABLE IF NOT EXISTS `#__citations_format` (
					`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`typeid` int(11) DEFAULT NULL,
					`style` varchar(50) DEFAULT NULL,
					`format` text,
					PRIMARY KEY (`id`)
				  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($query);
		$db->query();
		
		//import jparameter
		jimport('joomla.html.parameter');
		
		//get citation params
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$sql = "SELECT params FROM `#__components` WHERE `option`='com_citations';";
		}
		else
		{
			$sql = "SELECT params FROM `#__extensions` WHERE `type=`='component' AND `element`='com_citations';";
		}
		
		$db->setQuery($sql);
		$rawCitationParams = $db->loadResult();
		$citationParams = new JParameter( $rawCitationParams );
		
		//insert default format
		$query = "INSERT INTO `#__citations_format` (`typeid`, `style`, `format`)
			SELECT NULL,'custom'," . $db->quote( $citationParams->get('citation_format', '') ) . "
			FROM DUAL WHERE NOT EXISTS (SELECT `typeid` FROM `#__citations_format` WHERE `typeid` IS NULL);";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		$query = "DROP TABLE `#__citations_format`";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}