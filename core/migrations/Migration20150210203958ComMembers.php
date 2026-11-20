<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for creating table #__xprofiles_tokens for password reset tokens
  *
**/
class Migration20150210203958ComMembers extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__xprofiles_tokens')) {
            $schema
                ->createTable('#__xprofiles_tokens')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('token', 100)->default('')
                ->integer('user_id')
                ->datetime('created')
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__xprofiles_tokens');
    }
}
