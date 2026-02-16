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
class Migration20130610123201PlgCoursesDiscussions extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__forum_posts')
            && !$schema->hasColumn('#__forum_posts', 'scope_sub_id')
        ) {
            $schema->addColumn('#__forum_posts', 'scope_sub_id')->integer()->notNull()->default(0)->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__forum_posts', 'scope_sub_id')) {
            $schema->dropColumn('#__forum_posts', 'scope_sub_id');
        }
    }
}
