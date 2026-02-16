<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding more details to asset views table
 *
*/
class Migration20130729084642ComForum extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__forum_sections', 'ordering')) {
            $schema->addColumn('#__forum_sections', 'ordering')
                ->integer()
                ->notNull()
                ->default(0)
                ->after('object_id')
                ->execute();
        }

        if (!$schema->hasColumn('#__forum_categories', 'ordering')) {
            $schema->addColumn('#__forum_categories', 'ordering')
                ->integer()
                ->notNull()
                ->default(0)
                ->after('object_id')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__forum_sections', 'ordering')) {
            $schema->dropColumn('#__forum_sections', 'ordering');
        }

        if ($schema->hasColumn('#__forum_categories', 'ordering')) {
            $schema->dropColumn('#__forum_categories', 'ordering');
        }
    }
}
