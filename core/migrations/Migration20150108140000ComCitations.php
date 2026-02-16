<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding custom fields to citations
 *
*/
class Migration20150108140000ComCitations extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        //checks whether table exists and if the 'scope' field already exists
        if ($schema->tableExists('#__citations') && !$schema->hasColumn('#__citations', 'custom1')) {
            $schema->addColumn('#__citations', 'custom1')->text()->nullable()->default(null)->execute();
            $schema->addColumn('#__citations', 'custom2')->text()->nullable()->default(null)->execute();
            $schema->addColumn('#__citations', 'custom3')
                ->string(45)
                ->nullable()
                ->default(null)
                ->execute();
            $schema->addColumn('#__citations', 'custom4')
                ->string(45)
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
        if ($schema->tableExists('#__citations') && $schema->hasColumn('#__citations', 'custom1')) {
            $schema->dropColumn('#__citations', 'custom1');
            $schema->dropColumn('#__citations', 'custom2');
            $schema->dropColumn('#__citations', 'custom3');
            $schema->dropColumn('#__citations', 'custom4');
        }
    }
}
