<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for renaming admin menu module
 *
 */
class Migration20150121104223ModMenu extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__modules')) {
            $this->db->getQuery(true)
                ->update('#__modules')
                ->set(['module' => 'mod_menu'])
                ->where('client_id', '=', 1)
                ->where('module', '=', 'mod_hubmenu')
                ->execute();
        }

        $this->deleteModuleEntry('mod_hubmenu');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addModuleEntry('mod_hubmenu', 1, '', 1);
    }
}
