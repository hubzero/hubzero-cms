<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for altering #__user_profiles to allow for multi-value fields
  *
**/
class Migration20160511142701ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__user_profiles')) {
            return;
        }

        if (!$schema->hasColumn('#__user_profiles', 'id')) {
            $schema->addAutoIncrementPrimaryKey('#__user_profiles', 'id', true, false);
        }

        $schema->table('#__user_profiles')->alter()
            ->dropIndex('idx_user_id_profile_key')
            ->addIndex('idx_user_id', 'user_id')
            ->execute();
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__user_profiles')) {
            return;
        }

        if ($schema->hasColumn('#__user_profiles', 'id')) {
            $schema->dropColumn('#__user_profiles', 'id');
        }

        $schema->table('#__user_profiles')->alter()
            ->dropIndex('idx_user_id')
            ->addUniqueIndex('idx_user_id_profile_key', ['user_id', 'profile_key'])
            ->execute();
    }
}
