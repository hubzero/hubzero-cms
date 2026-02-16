<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for column size in publication version table
 *
*/
class Migration20251120161115PlgProjectsPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__publication_versions')
            && !$schema->hasColumn('#__publication_versions', 'datasize')
        ) {
            $schema->addColumn('#__publication_versions', 'datasize')->bigInteger()->unsigned();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__publication_versions')
            && $schema->hasColumn('#__publication_versions', 'datasize')
        ) {
            $schema->dropColumn('#__publication_versions', 'datasize');
        }
    }
}
