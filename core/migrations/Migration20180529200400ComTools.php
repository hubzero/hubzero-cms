<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding versionid and doi columns to doi_mapping table
 *
*/
class Migration20180529200400ComTools extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__doi_mapping')) {
            if (!$schema->hasColumn('#__doi_mapping', 'versionid')) {
                $schema->addColumn('#__doi_mapping', 'versionid')
                    ->integer()
                    ->nullable()
                    ->default(0)
                    ->execute();
            }

            if (!$schema->hasColumn('#__doi_mapping', 'doi')) {
                $schema->addColumn('#__doi_mapping', 'doi')
                    ->string(50)
                    ->nullable()
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__doi_mapping')) {
            if ($schema->hasColumn('#__doi_mapping', 'versionid')) {
                $schema->dropColumn('#__doi_mapping', 'versionid');
            }

            if ($schema->hasColumn('#__doi_mapping', 'doi')) {
                $schema->dropColumn('#__doi_mapping', 'doi');
            }
        }
    }
}
