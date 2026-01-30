<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to change database engine for a few core tables that could have been missed
 *
*/
class Migration20160907104900Core extends Base
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
        $this->changeEngine('#__viewlevels', 'MyISAM');
        $this->changeEngine('#__languages', 'MyISAM');
        $this->changeEngine('#__associations', 'MyISAM');
    }

    public function down()
    {
    }
}
