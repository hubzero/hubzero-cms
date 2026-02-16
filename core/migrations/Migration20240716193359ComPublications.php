<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for ...
  *
**/
class Migration20240716193359ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__publication_authors')
            && !$schema->hasColumn('#__publication_authors', 'orcid')
        ) {
            $schema->addColumn('#__publication_authors', 'orcid')
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
            && $schema->hasColumn('#__publication_authors', 'orcid')
        ) {
            $schema->dropColumn('#__publication_authors', 'orcid');
        }
    }
}
