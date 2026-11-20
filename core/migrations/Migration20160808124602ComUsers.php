<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
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
     *
     * @return  void
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__users_log_auth', 'status')) {
            $schema->removeEnumValue('#__users_log_auth', 'status', 'blocked');
        }
    }
}
