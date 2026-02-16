<?php

namespace Migrations;

use Hubzero\Content\Migration\Base;

// no direct access
/**
 * Migration script for adding indexes to the `#__auth_link` table.
 *
*/
class Migration20200917000000AuthLinkIndex extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__auth_link')) {
            $schema->addIndex('#__auth_link', 'auth_domain_id_idx', 'auth_domain_id');

            $schema->addIndex('#__auth_link', 'user_id_idx', 'user_id');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__auth_link')) {
            $schema->dropIndex('#__auth_link', 'auth_domain_id_idx');

            $schema->dropIndex('#__auth_link', 'user_id_idx');
        }
    }
}
