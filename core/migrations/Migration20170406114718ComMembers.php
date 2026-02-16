<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add `default_value` column to profile fields table
  *
**/
class Migration20170406114718ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__user_profile_fields')) {
            if (!$schema->hasColumn('#__user_profile_fields', 'default_value')) {
                $schema->addColumn('#__user_profile_fields', 'default_value')->string()->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__user_profile_fields')) {
            if ($schema->hasColumn('#__user_profile_fields', 'default_value')) {
                $schema->dropColumn('#__user_profile_fields', 'default_value');
            }
        }
    }
}
