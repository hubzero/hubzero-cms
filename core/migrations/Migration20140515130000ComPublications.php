<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for setting up publication building blocks
**/
class Migration20140515130000ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // Add opensource field
        if (
            $schema->tableExists('#__publication_licenses')
            && !$schema->hasColumn('#__publication_licenses', 'opensource')
        ) {
            $schema->addColumn('#__publication_licenses', 'opensource')
                ->tinyInteger(1)
                ->notNull()
                ->default(0)
                ->execute();
        }
        // Add restriction field
        if (
            $schema->tableExists('#__publication_licenses')
            && !$schema->hasColumn('#__publication_licenses', 'restriction')
        ) {
            $schema->addColumn('#__publication_licenses', 'restriction')
                ->string(100)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__publication_licenses', 'opensource')) {
            $schema->dropColumn('#__publication_licenses', 'opensource');
        }
        if ($schema->hasColumn('#__publication_licenses', 'restriction')) {
            $schema->dropColumn('#__publication_licenses', 'restriction');
        }
    }
}
