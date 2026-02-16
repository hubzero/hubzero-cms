<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding modified, version_id, and length fields to wiki table
**/
class Migration20121018000000ComWiki extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__wiki_page')) {
            if (!$schema->hasColumn('#__wiki_page', 'modified')) {
                $schema->addColumn('#__wiki_page', 'modified')
                    ->datetime()
                    ->notNull()
                    ->default('0000-00-00 00:00:00')
                    ->execute();
            }
            if (!$schema->hasColumn('#__wiki_page', 'version_id')) {
                $schema->addColumn('#__wiki_page', 'version_id')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }
        }

        if ($schema->tableExists('#__wiki_version')) {
            if (!$schema->hasColumn('#__wiki_version', 'length')) {
                $schema->addColumn('#__wiki_version', 'length')
                    ->integer()
                    ->notNull()
                    ->default(0)
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

        if ($schema->tableExists('#__wiki_page')) {
            if ($schema->hasColumn('#__wiki_page', 'modified')) {
                $schema->dropColumn('#__wiki_page', 'modified');
            }

            if ($schema->hasColumn('#__wiki_page', 'version_id')) {
                $schema->dropColumn('#__wiki_page', 'version_id');
            }
        }

        if ($schema->tableExists('#__wiki_version')) {
            if ($schema->hasColumn('#__wiki_version', 'length')) {
                $schema->dropColumn('#__wiki_version', 'length');
            }
        }
    }
}
