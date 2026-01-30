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
class Migration20130524101101PlgCoursesDiscussions extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $query = "ALTER TABLE #__courses_member_notes CHANGE COLUMN `timestamp` `timestamp` "
            . "time NOT NULL DEFAULT '00:00:00';";

        if (!empty($query)) {
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
