<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for assigning appropriate status for closed tickets
 *
*/
class Migration20150427221158ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__support_tickets') && $schema->tableExists('#__support_statuses')) {
            $open = $this->db->getQuery(true)
                ->select('id')
                ->from('#__support_statuses')
                ->where('open', '=', 1)
                ->loadColumn();

            if (count($open)) {
                $this->db->getQuery(true)
                    ->update('#__support_tickets')
                    ->set(['status' => 0])
                    ->where('open', '=', 0)
                    ->whereIn('status', $open)
                    ->execute();
            }
        }
    }
}
