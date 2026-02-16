<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding approval field to users table
 *
*/
class Migration20140521145232ComUsers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__users')
            && !$schema->hasColumn('#__users', 'approved')
            && $schema->hasColumn('#__users', 'block')
        ) {
            $schema->addColumn('#__users', 'approved')->tinyInteger(4)->notNull()->default(2);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__users') && $schema->hasColumn('#__users', 'approved')) {
            $schema->dropColumn('#__users', 'approved');
        }
    }
}
