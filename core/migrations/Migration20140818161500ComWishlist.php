<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indices to com_wishlist tables
**/
class Migration20140818161500ComWishlist extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__wishlist_vote')) {
            // Standardize column types
            $schema->alterTable('#__wishlist_vote')
                ->modifyInteger('id', true)->notNull()->autoIncrement()
                ->modifyInteger('wishid', true)->notNull()->default(0)
                ->modifyInteger('userid', true)->notNull()->default(0)
                ->modifyInteger('importance', true)->notNull()->default(0)
                ->modifyInteger('effort')->notNull()->default(0)
                ->dropIndex('jos_wishlist_vote_wishid_idx')
                ->addIndex('idx_wishid', 'wishid')
                ->addIndex('idx_userid', 'userid')
                ->execute();
        }

        if ($schema->tableExists('#__wishlist_owners')) {
            // Standardize column types
            $schema->alterTable('#__wishlist_owners')
                ->modifyInteger('id', true)->notNull()->autoIncrement()
                ->modifyInteger('wishlist', true)->notNull()->default(0)
                ->modifyInteger('userid', true)->notNull()->default(0)
                ->modifyInteger('type', true)->notNull()->default(0)
                ->addIndex('idx_wishlist', 'wishlist')
                ->addIndex('idx_userid', 'userid')
                ->addIndex('idx_type', 'type')
                ->execute();
        }

        if ($schema->tableExists('#__wishlist_ownergroups')) {
            // Standardize column types
            $schema->alterTable('#__wishlist_ownergroups')
                ->modifyInteger('id', true)->notNull()->autoIncrement()
                ->modifyInteger('wishlist', true)->notNull()->default(0)
                ->modifyInteger('groupid', true)->notNull()->default(0)
                ->addIndex('idx_wishlist', 'wishlist')
                ->addIndex('idx_groupid', 'groupid')
                ->execute();
        }

        if ($schema->tableExists('#__wishlist_implementation')) {
            $schema->alterTable('#__wishlist_implementation')
                ->addIndex('idx_wishid', 'wishid')
                ->addIndex('idx_created_by', 'created_by')
                ->addIndex('idx_approved', 'approved')
                ->execute();
        }

        if ($schema->tableExists('#__wishlist')) {
            $schema->alterTable('#__wishlist')
                ->addIndex('idx_category_referenceid', ['category', 'referenceid'])
                ->addIndex('idx_created_by', 'created_by')
                ->addIndex('idx_state', 'state')
                ->execute();
        }

        if ($schema->tableExists('#__wish_attachments')) {
            $schema->addIndex('#__wish_attachments', 'idx_wish', 'wish');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__wishlist_vote')) {
            $schema->dropIndex('#__wishlist_vote', 'idx_wishid');

            $schema->dropIndex('#__wishlist_vote', 'idx_userid');
        }

        if ($schema->tableExists('#__wishlist_owner')) {
            $schema->dropIndex('#__wishlist_owner', 'idx_wishlist');

            $schema->dropIndex('#__wishlist_owner', 'idx_userid');

            $schema->dropIndex('#__wishlist_owner', 'idx_type');
        }

        if ($schema->tableExists('#__wishlist_ownergroups')) {
            $schema->dropIndex('#__wishlist_ownergroups', 'idx_wishlist');

            $schema->dropIndex('#__wishlist_ownergroups', 'idx_groupid');
        }

        if ($schema->tableExists('#__wishlist_implementation')) {
            $schema->dropIndex('#__wishlist_implementation', 'idx_wishid');

            $schema->dropIndex('#__wishlist_implementation', 'idx_created_by');

            $schema->dropIndex('#__wishlist_implementation', 'idx_approved');
        }

        if ($schema->tableExists('#__wishlist')) {
            $schema->dropIndex('#__wishlist', 'idx_category_referenceid');

            $schema->dropIndex('#__wishlist', 'idx_created_by');

            $schema->dropIndex('#__wishlist', 'idx_state');
        }

        if ($schema->tableExists('#__wish_attachments')) {
            $schema->dropIndex('#__wish_attachments', 'idx_wish');
        }
    }
}
