<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding all day event flag.
**/
class Migration20140820171853ComEvents extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // all day field
        if (!$schema->hasColumn('#__events', 'allday')) {
            $schema->addColumn('#__events', 'allday')->integer()->default(0)->after('publish_down')->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        // all day field
        if ($schema->hasColumn('#__events', 'allday')) {
            $schema->dropColumn('#__events', 'allday');
        }
    }
}
