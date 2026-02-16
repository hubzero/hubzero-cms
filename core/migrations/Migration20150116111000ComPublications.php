<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add 'archived' column to #__publication_versions
**/
class Migration20150116111000ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__publication_versions')
            && !$schema->hasColumn('#__publication_versions', 'archived')
        ) {
            $schema->addColumn('#__publication_versions', 'archived')
                ->datetime()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->after('accepted')
                ->execute();
        }
    }
}
