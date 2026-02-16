<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add meta columns to #__cart_transaction_steps and cart_transaction_items tables
  *
**/
class Migration20160620171427ComCart extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__cart_transaction_items')
            && !$schema->hasColumn('#__cart_transaction_items', 'tiMeta')
        ) {
            $schema->addColumn('#__cart_transaction_items', 'tiMeta')->text();
        }

        if (
            $schema->tableExists('#__cart_transaction_steps')
            && !$schema->hasColumn('#__cart_transaction_steps', 'tsMeta')
        ) {
            $schema->addColumn('#__cart_transaction_steps', 'tsMeta')->char(255);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__cart_transaction_items')
            && $schema->hasColumn('#__cart_transaction_items', 'tiMeta')
        ) {
            $schema->dropColumn('#__cart_transaction_items', 'tiMeta');
        }

        if (
            $schema->tableExists('#__cart_transaction_steps')
            && $schema->hasColumn('#__cart_transaction_steps', 'tsMeta')
        ) {
            $schema->dropColumn('#__cart_transaction_steps', 'tsMeta');
        }
    }
}
