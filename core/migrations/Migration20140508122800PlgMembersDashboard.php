<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding id column to dashboard preferences
 *
*/
class Migration20140508122800PlgMembersDashboard extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__xprofiles_dashboard_preferences')
            && !$schema->hasColumn('#__xprofiles_dashboard_preferences', 'id')
        ) {
            // Adding auto-increment primary key to existing table requires multiple steps:
            // Step 1: Add the column without auto_increment
            $schema->addColumn('#__xprofiles_dashboard_preferences', 'id')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();

            // Step 2: Populate existing rows with unique sequential values (cross-database portable)
            $schema->populateSequentialValues('#__xprofiles_dashboard_preferences', 'id');

            // Step 3: Add primary key constraint
            $schema->addPrimaryKey('#__xprofiles_dashboard_preferences', 'id');

            // Step 4: Enable auto_increment
            $schema->modifyColumn('#__xprofiles_dashboard_preferences', 'id')
                ->integer()
                ->notNull()
                ->autoIncrement()
                ->execute();
        }
    }
}
