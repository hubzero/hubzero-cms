<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding scope column to #__citations table
  *
**/
class Migration20140916140933ComCitations extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // Checks whether table exists and if the 'scope' field already exists
        if ($schema->tableExists('#__citations') && !$schema->hasColumn('#__citations', 'scope')) {
            $schema->addColumn('#__citations', 'scope')->string(45)->nullable()->default(null)->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        // Checks to see if field exists and removes it
        if ($schema->tableExists('#__citations') && $schema->hasColumn('#__citations', 'scope')) {
            $schema->dropColumn('#__citations', 'scope');
        }
    }
}
