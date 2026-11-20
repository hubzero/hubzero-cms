<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     **/
    public function up()
    {
        /* Future migration should drop the table */
        $this->db
            ->getQuery()
            ->delete('#__session_log')
            ->execute();
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        /*
           No down method, truncated data can not be recovered nor
           should it need to be
        */
    }
}
