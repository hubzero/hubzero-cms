<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding 'state' field to resource_types table
 *
*/
class Migration20170731201800ComResources extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__resource_types')) {
            if (!$schema->hasColumn('#__resource_types', 'state')) {
                $schema->addColumn('#__resource_types', 'state')->integer(3)->notNull()->default('0');

                $this->db->getQuery(true)
                    ->update('#__resource_types')
                    ->set(['state' => 1])
                    ->execute();
            }

            $schema->addIndex('#__resource_types', 'idx_state', 'state');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__resource_types')) {
            $schema->dropIndex('#__resource_types', 'idx_state');

            if ($schema->hasColumn('#__resource_types', 'state')) {
                $schema->dropColumn('#__resource_types', 'state');
            }
        }
    }
}
