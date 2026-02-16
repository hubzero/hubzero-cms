<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for updating closed timestamp on support tickets
 *
*/
class Migration20150122165523ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__support_tickets')) {
            $query = $this->db->getQuery(true)
                ->select('t.id')
                ->from('#__support_tickets', 't')
                ->where('t.closed', '=', '0000-00-00 00:00:00')
                ->where('t.open', '=', 0);

            $tickets = $query->loadObjectList();

            if ($tickets) {
                foreach ($tickets as $ticket) {
                    $lastComment = $this->db->getQuery(true)
                        ->select('created')
                        ->from('#__support_comments')
                        ->where('ticket', '=', $ticket->id)
                        ->order('created', 'DESC')
                        ->value('created');

                    if ($lastComment) {
                        $this->db->getQuery(true)
                            ->update('#__support_tickets')
                            ->set(['closed' => $lastComment])
                            ->where('id', '=', $ticket->id)
                            ->execute();
                    }
                }
            }
        }
    }
}
