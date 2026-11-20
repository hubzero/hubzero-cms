<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to create an index on `#__cart_meta`
 **/
class Migration20180718194712ComCart extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        if (
            $this->db->tableExists('#__cart_meta')
            && !$this->db->tableHasKey('#__cart_meta', 'idx_scope_id')
        ) {
            $query = "CREATE INDEX idx_scope_id ON `#__cart_meta` (scope_id)";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        if (
            $this->db->tableExists('#__cart_meta')
            && $this->db->tableHasKey('#__cart_meta', 'idx_scope_id')
        ) {
            $query = "DROP INDEX scope_id_idx ON #__cart_meta";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
