<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding 'blocked' state to auth log
 *
 **/
class Migration20160808124602ComUsers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__users_log_auth', 'status')) {
            $schema->addEnumValue('#__users_log_auth', 'status', 'blocked');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__users_log_auth', 'status')) {
            $schema->removeEnumValue('#__users_log_auth', 'status', 'blocked');
        }
    }
}
