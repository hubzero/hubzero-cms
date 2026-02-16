<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for updating index for field that changed names
  *
**/
class Migration20141030163700ComBlog extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__blog_entries')) {
            if (
                $schema->hasKey('#__blog_entries', 'idx_group_id')
                && !$schema->hasColumn('#__blog_entries', 'group_id')
            ) {
                $schema->dropIndex('#__blog_entries', 'idx_group_id');

                $schema->addIndex('#__blog_entries', 'idx_scope_id', 'scope_id');
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__blog_entries')) {
            if ($schema->hasKey('#__blog_entries', 'idx_scope_id')) {
                $schema->dropIndex('#__blog_entries', 'idx_scope_id');

                $schema->addIndex('#__blog_entries', 'idx_group_id', 'group_id');
            }
        }
    }
}
