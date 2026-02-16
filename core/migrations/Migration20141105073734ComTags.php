<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for dropping unused #__tags_group table
 *
*/
class Migration20141105073734ComTags extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__tags_group')) {
            $schema->dropTable('#__tags_group');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__tags_group')) {
            $schema->createTable('#__tags_group')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->unsignedInteger('groupid')->default(0)
                ->unsignedInteger('tagid')->default(0)
                ->integer('priority')->default(0)
                ->primaryKey('id')
                ->index('idx_tagid', 'tagid')
                ->index('idx_groupid', 'groupid')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }
}
