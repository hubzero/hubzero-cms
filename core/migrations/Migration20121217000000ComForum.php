<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding object id to forum tables
  *
**/
class Migration20121217000000ComForum extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__forum_sections')
            && !$schema->hasColumn('#__forum_sections', 'object_id')
        ) {
            $schema->addColumn('#__forum_sections', 'object_id')->integer()->notNull()->default(0)->execute();
        }
        if (
            $schema->tableExists('#__forum_categories')
            && !$schema->hasColumn('#__forum_categories', 'object_id')
        ) {
            $schema->addColumn('#__forum_categories', 'object_id')->integer()->notNull()->default(0)->execute();
        }
        if ($schema->tableExists('#__forum_posts') && !$schema->hasColumn('#__forum_posts', 'object_id')) {
            $schema->addColumn('#__forum_posts', 'object_id')->integer()->notNull()->default(0)->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__forum_sections') && $schema->hasColumn('#__forum_sections', 'object_id')) {
            $schema->dropColumn('#__forum_sections', 'object_id');
        }
        if (
            $schema->tableExists('#__forum_categories')
            && $schema->hasColumn('#__forum_categories', 'object_id')
        ) {
            $schema->dropColumn('#__forum_categories', 'object_id');
        }
        if ($schema->tableExists('#__forum_posts') && $schema->hasColumn('#__forum_posts', 'object_id')) {
            $schema->dropColumn('#__forum_posts', 'object_id');
        }
    }
}
