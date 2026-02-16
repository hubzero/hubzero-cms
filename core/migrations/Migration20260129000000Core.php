<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to fix xprofiles table columns that lack default values
 *
 * The reason, params, and note columns were defined as NOT NULL without defaults,
 * which causes errors when creating/updating users.
 *
*/
class Migration20260129000000Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__xprofiles')) {
            // Fix 'reason' column - allow NULL with default NULL
            if ($schema->hasColumn('#__xprofiles', 'reason')) {
                $schema->modifyColumn('#__xprofiles', 'reason')->text()->nullable()->default(null)->execute();
            }

            // Fix 'params' column - allow NULL with default NULL
            if ($schema->hasColumn('#__xprofiles', 'params')) {
                $schema->modifyColumn('#__xprofiles', 'params')->text()->nullable()->default(null)->execute();
            }

            // Fix 'note' column - allow NULL with default NULL
            if ($schema->hasColumn('#__xprofiles', 'note')) {
                $schema->modifyColumn('#__xprofiles', 'note')->text()->nullable()->default(null)->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        // Reverting would break user creation, so we don't revert this change
        // The original schema was incorrect
    }
}
