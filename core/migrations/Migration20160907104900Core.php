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
        $schema = $this->db->schema();

        if ($schema->tableExists($table) && strtolower($schema->getEngine($table)) != strtolower($engine)) {
            $schema->setTableEngine($table, $engine);
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
