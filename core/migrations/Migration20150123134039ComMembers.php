<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add #__users_quotas_classes_groups table
**/
class Migration20150123134039ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__users_quotas_classes_groups')) {
            $schema->createTable('#__users_quotas_classes_groups')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->unsignedInteger('class_id')->default(0)
                ->unsignedInteger('group_id')->default(0)
                ->primaryKey('id')
                ->index('idx_class_id', 'class_id')
                ->index('idx_group_id', 'group_id')
                ->engine('InnoDB')
                ->charset('utf8')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__users_quotas_classes_groups');
    }
}
