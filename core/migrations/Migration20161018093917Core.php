<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for truncating possibly large obsolete session_log table
 *
*/
class Migration20161018093917Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        /* Future migration should drop the table */
        $query = "TRUNCATE #__session_log;";
        $this->db->setQuery($query);
        $this->db->query();
    }

    /**
     * Down
     **/
    public function down()
    {
        /*
           No down method, truncated data can not be recovered nor
           should it need to be
        */
    }
}
