<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            if (!$schema->hasColumn('#__extensions', 'modified')) {
                $schema
                    ->addColumn('#__extensions', 'modified')
                    ->datetime()
                    ->nullable()
                    ->execute();
            }

            if (!$schema->hasColumn('#__extensions', 'modified_by')) {
                $schema
                    ->addColumn('#__extensions', 'modified_by')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }
        }
    }

    /**
     * Down
     *
     * @return  void
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
