<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for setting the correct engine on the migrations table
**/
class Migration20141009154241Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__migrations') && strtolower($schema->getEngine('#__migrations')) != 'myisam') {
            $schema->setTableEngine('#__migrations', 'MyISAM');
        }
    }
}
