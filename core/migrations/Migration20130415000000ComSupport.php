<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for support ticket closed date
**/
class Migration20130415000000ComSupport extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__support_tickets')
            && !$schema->hasColumn('#__support_tickets', 'closed')
        ) {
            $schema->addColumn('#__support_tickets', 'closed')
                ->datetime()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->execute();

            // Closed tickets
            $clsd = $this->db->getQuery(true)
                ->select(['c.ticket', 'c.created'])
                ->from('#__support_comments', 'c')
                ->leftJoin('#__support_tickets AS t', 'c.ticket', 't.id')
                ->where('t.open', '=', 0)
                ->order('c.created', 'ASC')
                ->loadObjectList();

            // First we need to loop through all the entries and reove some potential duplicates
            $closedTickets = array();
            foreach ($clsd as $closed) {
                if (!isset($closedTickets[$closed->ticket])) {
                    $closedTickets[$closed->ticket] = $closed->created;
                } else {
                    if ($closedTickets[$closed->ticket] < $closed->created) {
                        $closedTickets[$closed->ticket] = $closed->created;
                    }
                }
            }

            foreach ($closedTickets as $ticket => $closed) {
                $this->db->getQuery(true)
                    ->update('#__support_tickets')
                    ->set(['closed' => $closed])
                    ->where('id', '=', $ticket)
                    ->execute();
            }
        }
    }

    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__support_tickets', 'closed')) {
            $schema->dropColumn('#__support_tickets', 'closed');
        }
    }
}
