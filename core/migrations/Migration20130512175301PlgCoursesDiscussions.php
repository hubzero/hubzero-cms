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
*/
class Migration20130512175301PlgCoursesDiscussions extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__plugins')) {
            $this->db->getQuery(true)
                ->update('#__plugins')
                ->set(['element' => 'discussions'])
                ->where('element', '=', 'forum')
                ->where('folder', '=', 'courses')
                ->execute();
        } else {
            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['element' => 'discussions'])
                ->where('element', '=', 'forum')
                ->where('folder', '=', 'courses')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__plugins')) {
            $this->db->getQuery(true)
                ->update('#__plugins')
                ->set(['element' => 'forum'])
                ->where('element', '=', 'discussions')
                ->where('folder', '=', 'courses')
                ->execute();
        } else {
            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['element' => 'forum'])
                ->where('element', '=', 'discussions')
                ->where('folder', '=', 'courses')
                ->execute();
        }
    }
}
