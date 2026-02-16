<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to move com_users notes categories to com_members
 *
*/
class Migration20170911141318ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__categories') && $schema->hasColumn('#__categories', 'extension')) {
            $this->db->getQuery(true)
                ->update('#__categories')
                ->set(['extension' => 'com_members'])
                ->where('extension', '=', 'com_users')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__categories') && $schema->hasColumn('#__categories', 'extension')) {
            $this->db->getQuery(true)
                ->update('#__categories')
                ->set(['extension' => 'com_users'])
                ->where('extension', '=', 'com_members')
                ->execute();
        }
    }
}
