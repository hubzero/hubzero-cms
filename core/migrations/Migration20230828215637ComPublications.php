<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2023 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for ...
 *
*/
class Migration20230828215637ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__publication_versions')
            && !$schema->hasColumn('#__publication_versions', 'downloadDisabled')
        ) {
            $schema->addColumn('#__publication_versions', 'downloadDisabled')->boolean()->default(false);
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
            && $schema->hasColumn('#__publication_versions', 'downloadDisabled')
        ) {
            $schema->dropColumn('#__publication_versions', 'downloadDisabled');
        }
    }
}
