<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding link columns to #__citations_secondary table
 *
*/
class Migration20141119180000ComCitations extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        //checks whether table exists and if the 'scope' field already exists
        if (
            $schema->tableExists('#__citations_secondary')
            && !$schema->hasColumn('#__citations_secondary', 'link1_url')
        ) {
            $schema->addColumn('#__citations_secondary', 'link1_url')
                ->tinyText()
                ->nullable()
                ->default(null)
                ->execute();
            $schema->addColumn('#__citations_secondary', 'link1_title')
                ->string(60)
                ->nullable()
                ->default(null)
                ->execute();
            $schema->addColumn('#__citations_secondary', 'link2_url')
                ->tinyText()
                ->nullable()
                ->default(null)
                ->execute();
            $schema->addColumn('#__citations_secondary', 'link2_title')
                ->string(60)
                ->nullable()
                ->default(null)
                ->execute();
            $schema->addColumn('#__citations_secondary', 'link3_url')
                ->tinyText()
                ->nullable()
                ->default(null)
                ->execute();
            $schema->addColumn('#__citations_secondary', 'link3_title')
                ->string(60)
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

        // Checks to see if gid field exists and removes it
        if (
            $schema->tableExists('#__citations_secondary')
            && $schema->hasColumn('#__citations_secondary', 'link1_url')
        ) {
            $schema->dropColumn('#__citations_secondary', 'link1_url');
            $schema->dropColumn('#__citations_secondary', 'link1_title');
            $schema->dropColumn('#__citations_secondary', 'link2_url');
            $schema->dropColumn('#__citations_secondary', 'link2_title');
            $schema->dropColumn('#__citations_secondary', 'link3_url');
            $schema->dropColumn('#__citations_secondary', 'link3_title');
        }
    }
}
