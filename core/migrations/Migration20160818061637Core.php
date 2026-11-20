<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
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
     *
     * @return  void
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__activity_logs', 'anonymous')) {
            $schema->dropColumn('#__activity_logs', 'anonymous');
        }
    }
}
