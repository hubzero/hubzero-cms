<?php

namespace Migrations;

use Hubzero\Content\Migration\Base;

// no direct access
/**
 * Migration script for adding indexes to the `#__auth_domain` table.
  *
**/
class Migration20200917100000AuthDomainIndex extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $schema->addIndex('#__auth_domain', 'authenticator_idx', 'authenticator');
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropIndex('#__auth_domain', 'authenticator_idx');
    }
}
