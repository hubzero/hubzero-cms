<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add `min` and `max` columns to profile fields table
  *
**/
class Migration20170306114718ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__user_profile_fields')) {
            if (!$schema->hasColumn('#__user_profile_fields', 'min')) {
                $schema->addColumn('#__user_profile_fields', 'min')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }

            if (!$schema->hasColumn('#__user_profile_fields', 'max')) {
                $schema->addColumn('#__user_profile_fields', 'max')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();
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
            if ($schema->hasColumn('#__user_profile_fields', 'min')) {
                $schema->dropColumn('#__user_profile_fields', 'min');
            }

            if ($schema->hasColumn('#__user_profile_fields', 'max')) {
                $schema->dropColumn('#__user_profile_fields', 'max');
            }
        }
    }
}
