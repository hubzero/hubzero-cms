<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__support_tickets')) {
            $this->db->getQuery(true)
                ->update('#__support_tickets')
                ->set(['severity' => 'minor'])
                ->where('severity', '=', 'trivial')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__support_tickets')) {
            // nothing to do here...
        }
    }
}
