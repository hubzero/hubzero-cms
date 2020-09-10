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
 * Migration script for adding Authfactors - Authy plugin
 **/
class Migration20200804000000PlgAuthfactorsGoogle extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('authfactors', 'google', 0);

		if (!$this->db->tableExists('#__auth_factors'))
		{
			$query = "CREATE TABLE `#__auth_factors` (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                `user_id` int(11) NOT NULL DEFAULT '0',
                                `enrolled` tinyint(1) DEFAULT NULL,
                                `domain` varchar(255) DEFAULT NULL,
                                `factor_id` int(11) DEFAULT NULL,
                                `data` varchar(255) DEFAULT NULL,
                                PRIMARY KEY (`user_id`),
                                UNIQUE KEY `id` (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('authfactors', 'google');
	}
}
