<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for changing citation field data type
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class Migration20131021225942ComCitations extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $query  = "ALTER TABLE `#__citations` MODIFY COLUMN `volume` VARCHAR(11);";
        $query .= "ALTER TABLE `#__citations` MODIFY COLUMN `year` VARCHAR(4);";

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
        $query  = "ALTER TABLE `#__citations` MODIFY COLUMN `volume` INT(11);";
        $query .= "ALTER TABLE `#__citations` MODIFY COLUMN `year` INT(4);";

        if (!empty($query)) {
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
