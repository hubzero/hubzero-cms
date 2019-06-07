<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing antispam tables
 **/
class Migration20170901000000PlgAntispamBayesian extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__antispam_message_hashes'))
		{
			$query = "CREATE TABLE `#__antispam_message_hashes` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `hash` varchar(256) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__antispam_token_counts'))
		{
			$query = "CREATE TABLE `#__antispam_token_counts` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `good_count` int(11) DEFAULT '0',
			  `bad_count` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__antispam_token_probs'))
		{
			$query = "CREATE TABLE `#__antispam_token_probs` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `token` varchar(256) NOT NULL,
			  `prob` float DEFAULT '0',
			  `prev_prob` float DEFAULT '0',
			  `in_ham` int(11) DEFAULT '0',
			  `in_spam` int(11) DEFAULT '0',
			  `provider` varchar(256) DEFAULT NULL,
			  `param1` varchar(256) NOT NULL,
			  `param2` varchar(256) NOT NULL,
			  `created` datetime DEFAULT NULL,
			  `modified` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`)
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
		if ($this->db->tableExists('#__antispam_message_hashes'))
		{
			$query = "DROP TABLE IF EXISTS `#__antispam_message_hashes`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__antispam_token_counts'))
		{
			$query = "DROP TABLE IF EXISTS `#__antispam_token_counts`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__antispam_token_probs'))
		{
			$query = "DROP TABLE IF EXISTS `#__antispam_token_probs`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
