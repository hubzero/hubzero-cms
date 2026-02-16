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
 *
*/
class Migration20141215165100ComCitations extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__citations', 'gid')) {
            $schema->renameColumn('#__citations', 'gid', 'scope_id')
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

        if ($schema->hasColumn('#__citations', 'scope_id')) {
            $schema->renameColumn('#__citations', 'scope_id', 'gid')
                ->string(45)
                ->nullable()
                ->default(null)
                ->execute();
        }
    }
}
