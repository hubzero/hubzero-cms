<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for dropping the steps index when it was named
 * automatically by the database and the name didn't match the previous migration
 *
*/
class Migration20151030000001ComCart extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__cart_transaction_steps')) {
            // Find and drop any index with 'tId' in the name
            foreach ($schema->getIndexNames('#__cart_transaction_steps') as $keyName) {
                if (stripos($keyName, 'tId') !== false) {
                    $schema->dropIndex('#__cart_transaction_steps', $keyName);
                    break;
                }
            }

            if (!$schema->hasColumn('#__cart_transaction_steps', 'tsMeta')) {
                $schema->addColumn('#__cart_transaction_steps', 'tsMeta')->char(255);
            } else {
                // Change tsMeta to 255 chars long
                $schema->modifyColumn('#__cart_transaction_steps', 'tsMeta')->char(255);
            }
        }
    }

    public function down()
    {
    }
}
