<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for add watching table
  *
**/
class Migration20130512175201ComForum extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__forum_posts')
            && !$schema->hasColumn('#__forum_posts', 'thread')
        ) {
            $schema->addColumn('#__forum_posts', 'thread')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__forum_posts', 'thread')) {
            $schema->dropColumn('#__forum_posts', 'thread');
        }
    }
}
