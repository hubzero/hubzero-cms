<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding twitter authentication plugin
 *
*/
class Migration20130530153638ComCron extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $query = "";

        if (!$this->db->tableHasField('#__cron_jobs', 'params')) {
            $query = "ALTER TABLE `#__cron_jobs` ADD `params` TEXT  NOT NULL  AFTER `ordering`;";
        }

        if (!empty($query)) {
            $this->db->setQuery($query);
            $this->db->query();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $query = "";

        if ($this->db->tableHasField('#__cron_jobs', 'params')) {
            $query .= "ALTER TABLE `#__cron_jobs` DROP `params`;";
        }

        if (!empty($query)) {
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
