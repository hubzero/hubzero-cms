<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing group_id value for tool tickets
  *
**/
class Migration20170731200400ComTools extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__support_tickets')
            && $schema->hasColumn('#__support_tickets', 'group_id')
            && $schema->tableExists('#__xgroups')
            && $schema->hasColumn('#__xgroups', 'gidNumber')
            && $schema->hasColumn('#__xgroups', 'cn')
            && $schema->tableExists('#__tool')
            && $schema->hasColumn('#__tool', 'toolname')
            && $schema->hasColumn('#__tool', 'ticketid')
        ) {
            $prefix = 'app-';

            if ($schema->tableExists('#__extensions')) {
                $params = $this->db->getQuery(true)
                    ->select('params')
                    ->from('#__extensions')
                    ->where('element', '=', 'com_tools')
                    ->value('params');

                if ($params && substr($params, 0, 1) == '{') {
                    $params = json_decode($params);
                    $prefix = $params->group_prefix;
                }
            }

            $tools = $this->db->getQuery(true)
                ->select(['t.toolname', 't.ticketid'])
                ->from('#__tool', 't')
                ->innerJoin('#__support_tickets AS st', 'st.id', 't.ticketid')
                ->where('st.group_id', '=', 0)
                ->loadObjectList();

            foreach ($tools as $tool) {
                $gidNumber = $this->db->getQuery(true)
                    ->select('gidNumber')
                    ->from('#__xgroups')
                    ->where('cn', '=', $prefix . $tool->toolname)
                    ->value('gidNumber');

                if ($gidNumber) {
                    $this->db->getQuery(true)
                        ->update('#__support_tickets')
                        ->set(['group_id' => $gidNumber])
                        ->where('id', '=', $tool->ticketid)
                        ->execute();
                }
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        // No down. Just fixing incorrect data in up().
    }
}
