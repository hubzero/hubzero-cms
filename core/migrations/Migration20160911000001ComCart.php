<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add notes field to `#__cart_transaction_info` table
 *
*/
class Migration20160911000001ComCart extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__cart_transaction_info', 'tiNotes')) {
            $schema->addColumn('#__cart_transaction_info', 'tiNotes')->text()->nullable();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__cart_transaction_info', 'tiNotes')) {
            $schema->dropColumn('#__cart_transaction_info', 'tiNotes');
        }
    }
}
