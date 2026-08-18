<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding the com_saml trust store and session tables
 **/
class Migration20260807000000ComSamlTrustStore extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__saml_service_providers'))
		{
			$query = "CREATE TABLE `#__saml_service_providers` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `entity_id` varchar(255) NOT NULL,
			  `name` varchar(255) NOT NULL DEFAULT '',
			  `description` text,
			  `acs_url` varchar(500) NOT NULL DEFAULT '',
			  `slo_url` varchar(500) DEFAULT NULL,
			  `signing_cert` text,
			  `encryption_cert` text,
			  `want_requests_signed` tinyint(1) NOT NULL DEFAULT '1',
			  `sign_assertion` tinyint(1) NOT NULL DEFAULT '0',
			  `nameid_format` varchar(255) NOT NULL DEFAULT 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
			  `nameid_source` varchar(32) NOT NULL DEFAULT 'username',
			  `attribute_map` text,
			  `allowed_groups` text,
			  `metadata_url` varchar(500) DEFAULT NULL,
			  `state` tinyint(1) NOT NULL DEFAULT '1',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_entity_id` (`entity_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__saml_sessions'))
		{
			$query = "CREATE TABLE `#__saml_sessions` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `session_index` varchar(64) NOT NULL,
			  `request_id` varchar(128) DEFAULT NULL,
			  `hub_session_id` varchar(200) NOT NULL DEFAULT '',
			  `user_id` int(11) NOT NULL DEFAULT '0',
			  `sp_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `state` tinyint(1) NOT NULL DEFAULT '1',
			  `created` datetime DEFAULT NULL,
			  `ended` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_session_index` (`session_index`),
			  KEY `idx_hub_session` (`hub_session_id`),
			  KEY `idx_sp` (`sp_id`),
			  UNIQUE KEY `idx_request` (`sp_id`,`request_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		foreach (array('#__saml_service_providers', '#__saml_sessions') as $table)
		{
			if ($this->db->tableExists($table))
			{
				$this->db->setQuery("DROP TABLE IF EXISTS `" . $table . "`;");
				$this->db->query();
			}
		}
	}
}
