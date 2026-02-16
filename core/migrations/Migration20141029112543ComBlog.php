<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for changing 'group_id' into 'scope_id' and adding 'access' field
 *
*/
class Migration20141029112543ComBlog extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__blog_entries')) {
            return;
        }

        if (!$schema->hasColumn('#__blog_entries', 'access')) {
            $schema->addColumn('#__blog_entries', 'access')->tinyInteger()->notNull()->default(0)->execute();
        }

        if (!$schema->hasColumn('#__blog_entries', 'scope_id')) {
            $schema->renameColumn('#__blog_entries', 'group_id', 'scope_id')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();

            $this->db->getQuery(true)
                ->update('#__blog_entries')
                ->set(['scope_id' => 'created_by'], true)
                ->where('scope', '=', 'member')
                ->where('scope_id', '=', 0)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__blog_entries')) {
            return;
        }

        if ($schema->hasColumn('#__blog_entries', 'access')) {
            $schema->dropColumn('#__blog_entries', 'access');
        }

        if ($schema->hasColumn('#__blog_entries', 'scope_id')) {
            $schema->renameColumn('#__blog_entries', 'scope_id', 'group_id')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();

            $this->db->getQuery(true)
                ->update('#__blog_entries')
                ->set(['group_id' => $this->db->quote('0')])
                ->where('scope', '=', 'member')
                ->where('group_id', '>', 0)
                ->execute();
        }
    }
}
