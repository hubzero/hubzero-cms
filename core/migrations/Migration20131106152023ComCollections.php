<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding fulltext key to collections tables
  *
**/
class Migration20131106152023ComCollections extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__collections_items')
            && $schema->hasColumn('#__collections_items', 'title')
            && $schema->hasColumn('#__collections_items', 'description')
        ) {
            $schema->addFulltextIndex('#__collections_items', 'idx_fulltxt_title_description', [
                'title',
                'description',
            ]);
        }

        if (
            $schema->tableExists('#__collections_posts')
            && $schema->hasColumn('#__collections_posts', 'description')
        ) {
            $schema->addFulltextIndex('#__collections_posts', 'idx_fulltxt_description', ['description']);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropIndex('#__collections_items', 'idx_fulltxt_title_description');

        $schema->dropIndex('#__collections_posts', 'idx_fulltxt_description');
    }
}
