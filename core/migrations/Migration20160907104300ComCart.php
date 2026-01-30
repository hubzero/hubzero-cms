<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to change database engine for cart_meta table
 *
*/
class Migration20160907104300ComCart extends Base
{
    private function changeEngine($table, $engine)
    {
        if ($this->db->tableExists($table) && strtolower($this->db->getEngine($table)) != $engine) {
            $query = "ALTER TABLE `" . $table . "` ENGINE = " . $engine;
            $this->db->setQuery($query);
            $this->db->query();
        }
    }

    public function up()
    {
        $this->changeEngine('#__cart_meta', 'MyISAM');
    }

    public function down()
    {
    }
}
