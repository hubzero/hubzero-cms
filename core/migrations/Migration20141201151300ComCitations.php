<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding scope_id column to #__citations table
**/
class Migration20141201151300ComCitations extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // Checks whether table exists and if the 'scope' field already exists
        if ($schema->tableExists('#__citations') && !$schema->hasColumn('#__citations', 'scope_id')) {
            $schema->addColumn('#__citations', 'scope_id')
                ->string(45)
                ->nullable()
                ->default(null)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        // Checks to see if field exists and removes it
        if ($schema->tableExists('#__citations') && $schema->hasColumn('#__citations', 'scope_id')) {
            $schema->dropColumn('#__citations', 'scope_id');
        }
    }
}
