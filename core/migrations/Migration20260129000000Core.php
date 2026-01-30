<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script to fix xprofiles table columns that lack default values
 *
 * The reason, params, and note columns were defined as NOT NULL without defaults,
 * which causes errors when creating/updating users.
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class Migration20260129000000Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if ($this->db->tableExists('#__xprofiles')) {
            // Fix 'reason' column - allow NULL with default NULL
            if ($this->db->tableHasField('#__xprofiles', 'reason')) {
                $query = "ALTER TABLE `#__xprofiles` MODIFY `reason` text DEFAULT NULL";
                $this->db->setQuery($query);
                $this->db->query();
            }

            // Fix 'params' column - allow NULL with default NULL
            if ($this->db->tableHasField('#__xprofiles', 'params')) {
                $query = "ALTER TABLE `#__xprofiles` MODIFY `params` text DEFAULT NULL";
                $this->db->setQuery($query);
                $this->db->query();
            }

            // Fix 'note' column - allow NULL with default NULL
            if ($this->db->tableHasField('#__xprofiles', 'note')) {
                $query = "ALTER TABLE `#__xprofiles` MODIFY `note` text DEFAULT NULL";
                $this->db->setQuery($query);
                $this->db->query();
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
