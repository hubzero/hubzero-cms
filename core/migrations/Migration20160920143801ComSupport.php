<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to drop deprecated support resolutions table
 *
*/
class Migration20160920143801ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if ($this->db->tableExists('#__support_resolutions')) {
            $query = "DROP TABLE IF EXISTS `#__support_resolutions`";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        if (!$this->db->tableExists('#__support_resolutions')) {
            $query = "CREATE TABLE `#__support_resolutions` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `title` varchar(100) NOT NULL DEFAULT '',
			  `alias` varchar(100) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
