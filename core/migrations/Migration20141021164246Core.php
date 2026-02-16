<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for making sure auto_increment values are sufficiently high
  *
**/
class Migration20141021164246Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__users')) {
            $auto = $schema->getAutoIncrement('#__users');

            if ($auto && is_numeric($auto) && $auto < 1000) {
                $schema->setAutoIncrement('#__users', $auto + 1000);
            }
        }

        if ($schema->tableExists('#__xgroups')) {
            $auto = $schema->getAutoIncrement('#__xgroups');

            if ($auto && is_numeric($auto) && $auto < 1000) {
                $schema->setAutoIncrement('#__xgroups', $auto + 1000);
            }
        }

        if ($schema->tableExists('#__extensions')) {
            $auto = $schema->getAutoIncrement('#__extensions');

            if ($auto && is_numeric($auto) && $auto < 10000) {
                $schema->setAutoIncrement('#__extensions', $auto + 10000);
            }
        }
    }
}
