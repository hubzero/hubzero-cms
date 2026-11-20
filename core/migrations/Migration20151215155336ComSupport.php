<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing unnecessary ticket severity level
 *
 */
class Migration20151215155336ComSupport extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__support_tickets')) {
            $this->db
                ->getQuery()
                ->update('#__support_tickets')
                ->set(['severity' => 'minor'])
                ->where('severity', '=', 'trivial')
                ->execute();
        }
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__support_tickets')) {
            // nothing to do here...
        }
    }
}
