<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to rename production_collections primary key
  *
**/
class Migration20171201000001ComCart extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__cart_transaction_info')) {
            if (!$schema->hasColumn('#__cart_transaction_info', 'tiPayment')) {
                $schema->addColumn('#__cart_transaction_info', 'tiPayment')->char(30);
                $schema->addColumn('#__cart_transaction_info', 'tiPaymentDetails')->char(255);
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__cart_transaction_info')) {
            if ($schema->hasColumn('#__cart_transaction_info', 'tiPayment')) {
                $schema->dropColumn('#__cart_transaction_info', 'tiPayment');
                $schema->dropColumn('#__cart_transaction_info', 'tiPaymentDetails');
            }
        }
    }
}
