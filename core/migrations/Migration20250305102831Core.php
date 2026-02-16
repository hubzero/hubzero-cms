<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding ratelimit memory table
  *
**/
class Migration20250305102831Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__ratelimit')) {
            $schema->createTable('#__ratelimit')
                ->increments('ratelimit_id')
                ->binary('ip', 16)
                ->unsignedInteger('rule_id')->default(0)
                ->unsignedBigInteger('count')->default(0)
                ->unsignedInteger('window_sz')->default(1)
                ->unsignedBigInteger('last_ts')->default(0)
                ->unsignedInteger('last_cnt')->default(0)
                ->unsignedBigInteger('current_ts')->default(0)
                ->unsignedInteger('current_cnt')->default(0)
                ->unsignedInteger('triggered')->default(0)
                ->string('action', 255)->default('log')
                ->boolean('banned')->default(0)
                ->primaryKey('ratelimit_id')
                ->uniqueIndex('uidx_ip_rule_id', ['ip', 'rule_id'])
                ->engine('MEMORY')
                ->charset('utf8mb4')
                ->collation('utf8mb4_general_ci')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__ratelimit')) {
            $schema->dropTable('#__ratelimit');
        }
    }
}
