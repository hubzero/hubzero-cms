<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding com_forum component entry if missing, or adding admin_menu_link if missing
 *
 */
class Migration20130918132946ComForum extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $this->addComponentEntry('Forum');

        if ($schema->tableExists('#__components')) {
            $result = $this->db->getQuery(true)
                ->select('*')
                ->from('#__components')
                ->where('name', '=', 'Forum')
                ->first();

            if ($result && empty($result->admin_menu_link)) {
                $this->db->getQuery(true)
                    ->update('#__components')
                    ->set(['admin_menu_link' => 'option=com_forum'])
                    ->where('id', '=', $result->id)
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteComponentEntry('Forum');
    }
}
