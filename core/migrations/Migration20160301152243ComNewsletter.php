<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding the autogen field to the table
**/
class Migration20160301152243ComNewsletter extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__newsletters') && !$schema->hasColumn('#__newsletters', 'autogen')) {
            $schema->addColumn('#__newsletters', 'autogen')->integer()->default(0)->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__newsletters') && $schema->hasColumn('#__newsletters', 'autogen')) {
            $schema->dropColumn('#__newsletters', 'autogen');
        }
    }
}
