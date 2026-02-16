<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding 'derivatives' field to publication licenses
  *
**/
class Migration20170725133700ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__user_profile_fields')
            && !$schema->hasColumn('#__user_profile_fields', 'placeholder')
        ) {
            $schema->addColumn('#__user_profile_fields', 'placeholder')
                ->string(255)
                ->notNull()
                ->default(0)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__user_profile_fields')
            && $schema->hasColumn('#__user_profile_fields', 'placeholder')
        ) {
            $schema->dropColumn('#__user_profile_fields', 'placeholder');
        }
    }
}
