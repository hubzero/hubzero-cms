<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding column to track when activity should be reported as anonymous
**/
class Migration20160818061637Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__activity_logs', 'anonymous')) {
            $schema->addColumn('#__activity_logs', 'anonymous')->tinyInteger(2)->unsigned()->notNull()->default('0');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__activity_logs', 'anonymous')) {
            $schema->dropColumn('#__activity_logs', 'anonymous');
        }
    }
}
