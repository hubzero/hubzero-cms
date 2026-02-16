<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Database\Expression;

/**
 * Migration script for setting ticket closed time
  *
**/
class Migration20150601185958ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__support_tickets')) {
            $subquery = $this->db->getQuery(true)
                ->select('created')
                ->from('#__support_comments', 'c')
                ->where('c.ticket', '=', Expression::column('t.id'))
                ->order('c.created', 'DESC')
                ->limit(1);

            $this->db->getQuery(true)
                ->update('#__support_tickets', 't')
                ->set(['t.closed' => $subquery])
                ->where('t.open', '=', 0)
                ->where('t.closed', '=', '0000-00-00 00:00:00')
                ->execute();
        }
    }
}
