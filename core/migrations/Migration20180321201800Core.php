<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding 'modified' and 'modified_by' columns to extensions table
 *
*/
class Migration20180321201800Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            if (!$schema->hasColumn('#__extensions', 'modified')) {
                $schema->addColumn('#__extensions', 'modified')
                    ->datetime()
                    ->nullable()
                    ->execute();
            }

            if (!$schema->hasColumn('#__extensions', 'modified_by')) {
                $schema->addColumn('#__extensions', 'modified_by')
                    ->integer()
                    ->notNull()
                    ->default(0)
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

        if ($schema->tableExists('#__extensions')) {
            if ($schema->hasColumn('#__extensions', 'modified')) {
                $schema->dropColumn('#__extensions', 'modified');
            }

            if ($schema->hasColumn('#__extensions', 'modified_by')) {
                $schema->dropColumn('#__extensions', 'modified_by');
            }
        }
    }
}
