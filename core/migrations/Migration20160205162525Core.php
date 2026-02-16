<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for creating table #__audit_results
  *
**/
class Migration20160205162525Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__audit_results')) {
            $schema->createTable('#__audit_results')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('scope', 100)->default('')
                ->unsignedInteger('scope_id')->default(0)
                ->datetime('processed')->default('0000-00-00 00:00:00')
                ->tinyInteger('status')->default(0)
                ->tinyText('notes')
                ->string('test_id', 255)->default('')
                ->primaryKey('id')
                ->index('idx_scope_scope_id', ['scope', 'scope_id'])
                ->index('idx_status', 'status')
                ->index('idx_test_id', 'test_id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__audit_results');
    }
}
