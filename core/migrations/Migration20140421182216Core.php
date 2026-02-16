<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for making sure passhash field is big enough (match uses_password table)
**/
class Migration20140421182216Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__users_password_history')
            && $schema->hasColumn('#__users_password_history', 'passhash')
        ) {
            $schema->modifyColumn('#__users_password_history', 'passhash')->char(127)->notNull()->default('');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__users_password_history')
            && $schema->hasColumn('#__users_password_history', 'passhash')
        ) {
            $schema->modifyColumn('#__users_password_history', 'passhash')->char(32)->notNull()->default('');
        }
    }
}
