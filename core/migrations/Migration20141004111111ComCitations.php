<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding scope and scope_id to #__citations_secondary table
 *
*/
class Migration20141004111111ComCitations extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // Checks whether table exists and if the 'scope' field already exists
        if (
            $schema->tableExists('#__citations_secondary')
            && !$schema->hasColumn('#__citations_secondary', 'scope')
        ) {
            $schema->alterTable('#__citations_secondary')
                ->addColumn('scope')->string(250)->nullable()->default(null)
                ->addColumn('scope_id')->integer()->nullable()->default(null)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        // Checks to see if fieldd exists and removes it
        if (
            $schema->tableExists('#__citations_secondary')
            && $schema->hasColumn('#__citations_secondary', 'scope')
        ) {
            $schema->dropColumn('#__citations_secondary', 'scope');
            $schema->dropColumn('#__citations_secondary', 'scope_id');
        }
    }
}
