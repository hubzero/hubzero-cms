<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for cleaning up indices
 *
*/
class Migration20130410000000Core extends Base
{
    public function up()
    {
        $schema = $this->db->schema();



        // Forum section indices
        $schema->addIndex('#__forum_sections', 'idx_scoped', ['scope', 'scope_id']);
        if (!$schema->hasKey('#__forum_sections', 'idx_asset_id')) {
            $schema->addIndex('#__forum_sections', 'idx_asset_id', 'asset_id');
        }
        if (!$schema->hasKey('#__forum_sections', 'idx_object_id')) {
            $schema->addIndex('#__forum_sections', 'idx_object_id', 'object_id');
        }
        if (!$schema->hasKey('#__forum_sections', 'idx_access')) {
            $schema->addIndex('#__forum_sections', 'idx_access', 'access');
        }

        // Forum categories indices
        $schema->addIndex('#__forum_categories', 'idx_scoped', ['scope', 'scope_id']);
        if (!$schema->hasKey('#__forum_categories', 'idx_asset_id')) {
            $schema->addIndex('#__forum_categories', 'idx_asset_id', 'asset_id');
        }
        if (!$schema->hasKey('#__forum_categories', 'idx_object_id')) {
            $schema->addIndex('#__forum_categories', 'idx_object_id', 'object_id');
        }
        if (!$schema->hasKey('#__forum_categories', 'idx_state')) {
            $schema->addIndex('#__forum_categories', 'idx_state', 'state');
        }
        if (!$schema->hasKey('#__forum_categories', 'idx_access')) {
            $schema->addIndex('#__forum_categories', 'idx_access', 'access');
        }
        if (!$schema->hasKey('#__forum_categories', 'idx_section_id')) {
            $schema->addIndex('#__forum_categories', 'idx_section_id', 'section_id');
        }
        if (!$schema->hasKey('#__forum_categories', 'idx_closed')) {
            $schema->addIndex('#__forum_categories', 'idx_closed', 'closed');
        }

        // Forum post indices
        $schema->addIndex('#__forum_posts', 'idx_scoped', ['scope', 'scope_id']);
        if (!$schema->hasKey('#__forum_posts', 'idx_category_id')) {
            $schema->addIndex('#__forum_posts', 'idx_category_id', 'category_id');
        }
        if (!$schema->hasKey('#__forum_posts', 'idx_access')) {
            $schema->addIndex('#__forum_posts', 'idx_access', 'access');
        }
        if (!$schema->hasKey('#__forum_posts', 'idx_asset_id')) {
            $schema->addIndex('#__forum_posts', 'idx_asset_id', 'asset_id');
        }
        if (!$schema->hasKey('#__forum_posts', 'idx_object_id')) {
            $schema->addIndex('#__forum_posts', 'idx_object_id', 'object_id');
        }
        if (!$schema->hasKey('#__forum_posts', 'idx_state')) {
            $schema->addIndex('#__forum_posts', 'idx_state', 'state');
        }
        if (!$schema->hasKey('#__forum_posts', 'idx_sticky')) {
            $schema->addIndex('#__forum_posts', 'idx_sticky', 'sticky');
        }
        if (!$schema->hasKey('#__forum_posts', 'idx_parent')) {
            $schema->addIndex('#__forum_posts', 'idx_parent', 'parent');
        }

        // Forum attachment indices
        $schema->addIndex('#__forum_attachments', 'idx_filename_postid', ['filename', 'post_id']);
        if (!$schema->hasKey('#__forum_attachments', 'idx_parent')) {
            $schema->addIndex('#__forum_attachments', 'idx_parent', 'parent');
        }

        // Blog comments index
        if (!$schema->hasKey('#__blog_comments', 'idx_entry_id')) {
            $schema->addIndex('#__blog_comments', 'idx_entry_id', 'entry_id');
        }

        // Xmessage recipient
        if (!$schema->hasKey('#__xmessage_recipient', 'idx_mid')) {
            $schema->addIndex('#__xmessage_recipient', 'idx_mid', 'mid');
        }
        if (!$schema->hasKey('#__xmessage_recipient', 'idx_uid')) {
            $schema->addIndex('#__xmessage_recipient', 'idx_uid', 'uid');
        }

        // Xmessage recipient
        if (!$schema->hasKey('#__resource_types', 'idx_category')) {
            $schema->addIndex('#__resource_types', 'idx_category', 'category');
        }

        $this->db->schema()->dropTable('#__resource_tags');
        $this->db->schema()->dropTable('#__support_tags');
        $this->db->schema()->dropTable('#__answers_tags');
    }
}
