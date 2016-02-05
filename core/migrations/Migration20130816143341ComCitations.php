<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for specifying citation format
 **/
class Migration20130816143341ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		//create new format table
		$query = "CREATE TABLE IF NOT EXISTS `#__citations_format` (
					`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`typeid` int(11) DEFAULT NULL,
					`style` varchar(50) DEFAULT NULL,
					`format` text,
					PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->setQuery($query);
		$this->db->query();

		// get citation params
		if ($this->db->tableExists('#__extensions'))
		{
			$sql = "SELECT params FROM `#__extensions` WHERE `type`='component' AND `element`='com_citations';";
		}
		else
		{
			$sql = "SELECT params FROM `#__components` WHERE `option`='com_citations';";
		}

		$this->db->setQuery($sql);
		$rawCitationParams = $this->db->loadResult();
		$citationParams = new \Hubzero\Config\Registry($rawCitationParams);

		//insert default format
		$query = "INSERT INTO `#__citations_format` (`typeid`, `style`, `format`)
			SELECT NULL,'custom'," . $this->db->quote( $citationParams->get('citation_format', '') ) . "
			FROM DUAL WHERE NOT EXISTS (SELECT `typeid` FROM `#__citations_format` WHERE `typeid` IS NULL);";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "DROP TABLE `#__citations_format`";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}