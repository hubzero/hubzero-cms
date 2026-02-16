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
class Migration20130507085501ComForum extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__forum_posts')) {
            if (!$schema->hasColumn('#__forum_posts', 'lft')) {
                $schema->addColumn('#__forum_posts', 'lft')->integer()->notNull()->default(0)->execute();
            }

            if (!$schema->hasColumn('#__forum_posts', 'rgt')) {
                $schema->addColumn('#__forum_posts', 'rgt')->integer()->notNull()->default(0)->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__forum_posts')) {
            if ($schema->hasColumn('#__forum_posts', 'lft')) {
                $schema->dropColumn('#__forum_posts', 'lft');
            }

            if ($schema->hasColumn('#__forum_posts', 'rgt')) {
                $schema->dropColumn('#__forum_posts', 'rgt');
            }
        }
    }
}
