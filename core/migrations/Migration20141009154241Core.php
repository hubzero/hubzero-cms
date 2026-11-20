<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__migrations')
            && strtolower((string) $schema->getEngine('#__migrations')) != 'myisam'
        ) {
            $schema->setTableEngine('#__migrations', 'MyISAM');
        }
    }
}
