<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing up tags indices
  *
**/
class Migration20130426070126ComTags extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->hasKey('#__tags_object', 'jos_tags_object_objectid_tbl_idx')) {
            $schema->dropIndex('#__tags_object', 'jos_tags_object_objectid_tbl_idx');
        }
        if ($schema->hasKey('#__tags_object', 'jos_tags_object_label_tagid_idx')) {
            $schema->dropIndex('#__tags_object', 'jos_tags_object_label_tagid_idx');
        }
        if ($schema->hasKey('#__tags_object', 'jos_tags_object_tbl_objectid_label_tagid_idx')) {
            $schema->dropIndex('#__tags_object', 'jos_tags_object_tbl_objectid_label_tagid_idx');
        }
        if ($schema->hasKey('#__tags_object', 'jos_tags_object_tagid_idx')) {
            $schema->dropIndex('#__tags_object', 'jos_tags_object_tagid_idx');
        }
        $schema->addIndex('#__tags_object', 'idx_objectid_tbl', ['objectid', 'tbl']);
        $schema->addIndex('#__tags_object', 'idx_label_tagid', ['label', 'tagid']);
        $schema->addIndex('#__tags_object', 'idx_tbl_objectid_label_tagid', ['tbl', 'objectid', 'label', 'tagid']);
        $schema->addIndex('#__tags_object', 'idx_tagid', 'tagid');
        if (!$schema->hasKey('#__tags_substitute', 'idx_tag_id')) {
            $schema->addIndex('#__tags_substitute', 'idx_tag_id', 'tag_id');
        }
        if ($schema->hasKey('#__tags', 'jos_tags_raw_tag_alias_description_ftidx')) {
            $schema->dropIndex('#__tags', 'jos_tags_raw_tag_alias_description_ftidx');
        }
        if ($schema->hasKey('#__tags', 'jos_tags_raw_tag_description_ftidx')) {
            $schema->dropIndex('#__tags', 'jos_tags_raw_tag_description_ftidx');
        }
        if ($schema->hasKey('#__tags', 'description')) {
            $schema->dropIndex('#__tags', 'description');
        }

        // Add FULLTEXT indexes separately using helper for SQLite compatibility
        $schema->addFulltextIndex('#__tags', 'ftidx_raw_tag_description', ['raw_tag', 'description']);
        $schema->addFulltextIndex('#__tags', 'ftidx_description', 'description');
    }
}
