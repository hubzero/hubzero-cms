<?php

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for dropping unused #__tags_group table
 *
*/
class Migration20190902000001ComTags extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__tags_object')) {
            $schema->addUniqueIndex('#__tags_object', 'unique_tag_per_obj', ['objectid', 'tagid', 'tbl']);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropIndex('#__tags_object', 'unique_tag_per_obj');
    }
}
