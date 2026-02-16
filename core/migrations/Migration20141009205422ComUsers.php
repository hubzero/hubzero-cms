<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing index name on users transactions table
  *
**/
class Migration20141009205422ComUsers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__users_transactions')) {
            $schema->dropIndex('#__users_transactions', 'idx_referenceid_categroy_type');
            $schema->dropIndex('#__users_transactions', 'jos_users_transactions_referenceid_categroy_type_idx');

            $schema->addIndex('#__users_transactions', 'idx_referenceid_category_type', [
                'referenceid',
                'category',
                'type',
            ]);
        }
    }
}
