<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for column orcid_work_put_code in table #__publication_authors
 *
*/
class Migration20240502154413ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__publication_authors')
            && !$schema->hasColumn('#__publication_authors', 'orcid_work_put_code')
        ) {
            $schema->addColumn('#__publication_authors', 'orcid_work_put_code')
                ->string(50)
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

        if (
            $schema->tableExists('#__publication_authors')
            && $schema->hasColumn('#__publication_authors', 'orcid_work_put_code')
        ) {
            $schema->dropColumn('#__publication_authors', 'orcid_work_put_code');
        }
    }
}
