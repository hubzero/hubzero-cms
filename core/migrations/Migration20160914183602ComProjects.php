<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for setting character set on tables to UTF8
 *
*/
class Migration20160914183602ComProjects extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // CHARACTER SET conversion is MySQL-specific (SQLite uses UTF-8 by default)
        $schema->setTableCharset('#__project_owners', 'utf8', 'utf8_general_ci');
        $schema->setTableCharset('#__project_activity', 'utf8', 'utf8_general_ci');
        $schema->setTableCharset('#__project_types', 'utf8', 'utf8_general_ci');
        $schema->setTableCharset('#__projects', 'utf8', 'utf8_general_ci');
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        // CHARACTER SET conversion is MySQL-specific (SQLite uses UTF-8 by default)
        $schema->setTableCharset('#__project_owners', 'latin1', 'latin1_swedish_ci');
        $schema->setTableCharset('#__project_activity', 'latin1', 'latin1_swedish_ci');
        $schema->setTableCharset('#__project_types', 'latin1', 'latin1_swedish_ci');
        $schema->setTableCharset('#__projects', 'latin1', 'latin1_swedish_ci');
    }
}
