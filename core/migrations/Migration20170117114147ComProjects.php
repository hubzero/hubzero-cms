<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indexes to #__project tables
 *
*/
class Migration20170117114147ComProjects extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__project_activity')) {
            $schema->addIndex('#__project_activity', 'idx_projectid', 'projectid');

            $schema->addIndex('#__project_activity', 'idx_state', 'state');
        }

        if ($schema->tableExists('#__project_comments')) {
            $schema->addIndex('#__project_comments', 'idx_itemid', 'itemid');

            $schema->addIndex('#__project_comments', 'idx_activityid', 'activityid');

            $schema->addIndex('#__project_comments', 'idx_state', 'state');
        }

        if ($schema->tableExists('#__project_logs')) {
            $schema->addIndex('#__project_logs', 'idx_projectid', 'projectid');
        }

        if ($schema->tableExists('#__project_microblog')) {
            $schema->addIndex('#__project_microblog', 'idx_projectid', 'projectid');

            $schema->addIndex('#__project_microblog', 'idx_state', 'state');
        }

        if ($schema->tableExists('#__project_owners')) {
            $schema->addIndex('#__project_owners', 'idx_projectid', 'projectid');

            $schema->addIndex('#__project_owners', 'idx_userid', 'userid');

            $schema->addIndex('#__project_owners', 'idx_groupid', 'groupid');

            $schema->addIndex('#__project_owners', 'idx_status', 'status');

            $schema->addIndex('#__project_owners', 'idx_role', 'role');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__project_activity')) {
            $schema->dropIndex('#__project_activity', 'idx_projectid');

            $schema->dropIndex('#__project_activity', 'idx_state');
        }

        if ($schema->tableExists('#__project_comments')) {
            $schema->dropIndex('#__project_comments', 'idx_itemid');

            $schema->dropIndex('#__project_comments', 'idx_activityid');

            $schema->dropIndex('#__project_comments', 'idx_state');
        }

        if ($schema->tableExists('#__project_logs')) {
            $schema->dropIndex('#__project_logs', 'idx_projectid');
        }

        if ($schema->tableExists('#__project_microblog')) {
            $schema->dropIndex('#__project_microblog', 'idx_projectid');

            $schema->dropIndex('#__project_microblog', 'idx_state');
        }

        if ($schema->tableExists('#__project_owners')) {
            $schema->dropIndex('#__project_owners', 'idx_projectid');

            $schema->dropIndex('#__project_owners', 'idx_userid');

            $schema->dropIndex('#__project_owners', 'idx_groupid');

            $schema->dropIndex('#__project_owners', 'idx_status');

            $schema->dropIndex('#__project_owners', 'idx_role');
        }
    }
}
