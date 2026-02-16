<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for tracking total viewing time in media tracking table
 *
*/
class Migration20130731203037ComResources extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__media_tracking', 'total_viewing_time')) {
            $schema->addColumn('#__media_tracking', 'total_viewing_time')->integer()->default(0)->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__media_tracking', 'total_viewing_time')) {
            $schema->dropColumn('#__media_tracking', 'total_viewing_time');
        }
    }
}
