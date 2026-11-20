<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for setting up hubgraph
 *
*/
class Migration20141112203625ComHubgraph extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('hg_update_queue')) {
            $schema
                ->createTable('hg_update_queue')
                ->enum('action', ['INSERT', 'UPDATE', 'DELETE'])
                ->string('table_name', 50)
                ->integer('id')
                ->integer('other_id')->nullable()
                ->text('note')->nullable()
                ->engine('InnoDB')
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

        $schema->dropTable('hg_update_queue');
    }
}
