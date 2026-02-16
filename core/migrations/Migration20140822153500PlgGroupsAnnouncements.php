<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indices to announcements tables
  *
**/
class Migration20140822153500PlgGroupsAnnouncements extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__announcements')) {
            $schema->dropIndex('#__announcements', 'jos_wishlist_vote_wishid_idx');

            $schema->addIndex('#__announcements', 'idx_scope_scope_id', ['scope', 'scope_id']);

            $schema->addIndex('#__announcements', 'idx_created_by', 'created_by');

            $schema->addIndex('#__announcements', 'idx_state', 'state');

            $schema->addIndex('#__announcements', 'idx_priority', 'priority');

            $schema->addIndex('#__announcements', 'idx_sticky', 'sticky');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__announcements')) {
            $schema->dropIndex('#__announcements', 'idx_state');

            $schema->dropIndex('#__announcements', 'idx_created_by');

            $schema->dropIndex('#__announcements', 'idx_scope_scope_id');

            $schema->dropIndex('#__announcements', 'idx_priority');

            $schema->dropIndex('#__announcements', 'idx_sticky');
        }
    }
}
