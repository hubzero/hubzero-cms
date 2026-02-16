<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding unique constraint to users.username field
 *
 */
class Migration20150109200952ComUsers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($this->db->tableExists('#__users') && $schema->hasColumn('#__users', 'username')) {
            // Check if the key already exists before adding it
            if (!$schema->hasKey('#__users', 'uidx_username')) {
                $schema->alterTable('#__users')
                    ->addUniqueIndex('uidx_username', 'username')
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasKey('#__users', 'uidx_username')) {
            $schema->dropIndex('#__users', 'uidx_username');
        }
    }
}
