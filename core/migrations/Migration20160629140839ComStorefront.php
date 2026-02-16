<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add Software Download product type to the storefront
**/
class Migration20160629140839ComStorefront extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__storefront_product_types')) {
            $id = $this->db->getQuery(true)
                ->select('ptId')
                ->from('#__storefront_product_types')
                ->where('ptModel', '=', 'software')
                ->value('ptId');

            if (!$id) {
                $this->db->getQuery(true)
                    ->insert('#__storefront_product_types')
                    ->columns(['ptId', 'ptName', 'ptModel'])
                    ->values("NULL, 'Software Download', 'software'")
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__storefront_product_types')) {
            $id = $this->db->getQuery(true)
                ->select('ptId')
                ->from('#__storefront_product_types')
                ->where('ptModel', '=', 'software')
                ->value('ptId');

            if ($id) {
                $this->db->getQuery(true)
                    ->delete('#__storefront_product_types')
                    ->where('ptId', '=', $id)
                    ->execute();
            }
        }
    }
}
