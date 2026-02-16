<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing wrong datatype on column
 *
*/
class Migration20130617171001ComForum extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__forum_posts')
            && !$schema->hasColumn('#__forum_posts', 'closed')
        ) {
            $schema->addColumn('#__forum_posts', 'closed')->tinyInteger(2)->notNull()->default(0);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__forum_posts', 'closed')) {
            $schema->dropColumn('#__forum_posts', 'closed');
        }
    }
}
