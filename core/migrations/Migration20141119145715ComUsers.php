<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for extending password field length in #__users table
**/
class Migration20141119145715ComUsers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__users') && $schema->hasColumn('#__users', 'password')) {
            $info = $schema->getTableColumns('#__users', false);

            if ($info['password']->Type != "varchar(127)") {
                $schema->modifyColumn('#__users', 'password')
                    ->string(127)
                    ->notNull()
                    ->default('')
                    ->execute();
            }
        }
    }
}
