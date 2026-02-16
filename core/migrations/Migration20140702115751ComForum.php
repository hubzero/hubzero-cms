<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for setting state=3 on reported forum posts
**/
class Migration20140702115751ComForum extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__forum_posts', 'state')) {
            $query = $this->db->getQuery(true)
                ->select('referenceid')
                ->from('#__abuse_reports')
                ->where('state', '=', 0)
                ->where('category', 'IN', ['forum']);
            if ($ids = $query->loadColumn()) {
                $ids = array_map('intval', $ids);

                $this->db->getQuery(true)
                    ->update('#__forum_posts')
                    ->set(['state' => 3])
                    ->where('id', 'IN', $ids)
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__forum_posts', 'state')) {
            $this->db->getQuery(true)
                ->update('#__forum_posts')
                ->set(['state' => 1])
                ->where('state', '=', 3)
                ->execute();
        }
    }
}
