<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing some display issues with old support tickets
**/
class Migration20141022110100ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__support_tickets')) {
            $query = $this->db->getQuery(true)
                ->select(['id', 'report'])
                ->from('#__support_tickets')
                ->where('created', '<', '2013-01-01 00:00:00')
                ->where('report', 'LIKE', '%\\\\\'%') // Keep escaping logic
                ->where('type', '=', 0)
                ->where('open', '=', 1);
            if ($records = $query->loadObjectList()) {
                foreach ($records as $row) {
                    $row->report = str_replace('&quot;', '"', $row->report);
                    $row->report = stripslashes($row->report);
                    $row->report = html_entity_decode($row->report);
                    $row->summary = substr($row->report, 0, 70);
                    if (strlen($row->summary) >= 70) {
                        $row->summary .= '...';
                    }

                    $this->db->getQuery(true)
                        ->update('#__support_tickets')
                        ->set(['report' => $row->report, 'summary' => $row->summary])
                        ->where('id', '=', $row->id)
                        ->execute();
                }
            }
        }
    }
}
