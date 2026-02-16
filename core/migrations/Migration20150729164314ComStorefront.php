<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding storefront component entry
 *
*/
class Migration20150729164314ComStorefront extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__storefront_collections')
            && $schema->hasColumn('#__storefront_collections', 'cParent')
        ) {
            $schema->modifyColumn('#__storefront_collections', 'cParent')->char(1)->nullable()->default(null);
        }

        if (
            $schema->tableExists('#__storefront_product_meta')
            && $schema->hasColumn('#__storefront_product_meta', 'pmValue')
        ) {
            $schema->modifyColumn('#__storefront_product_meta', 'pmValue')
                ->text()
                ->execute();
        }

        if ($schema->tableExists('#__storefront_product_types')) {
            $query = $this->db->getQuery(true)
                ->from('#__storefront_product_types')
                ->where('ptName', '=', 'SoftwareDownload')
                ->where('ptModel', '=', 'software');
            $count = $query->count();

            if (!$count) {
                $this->db->getQuery(true)
                    ->insert('#__storefront_product_types')
                    ->columns(['ptName', 'ptModel'])
                    ->values(['SoftwareDownload', 'software'])
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

        if (
            $schema->tableExists('#__storefront_collections')
            && $schema->hasColumn('#__storefront_collections', 'cParent')
        ) {
            $schema->modifyColumn('#__storefront_collections', 'cParent')->integer(16)->nullable()->default(null);
        }

        if (
            $schema->tableExists('#__storefront_product_meta')
            && $schema->hasColumn('#__storefront_product_meta', 'pmValue')
        ) {
            $schema->modifyColumn('#__storefront_product_meta', 'pmValue')
                ->string(255)
                ->execute();
        }

        if ($schema->tableExists('#__storefront_product_types')) {
            $this->db->getQuery(true)
                ->delete('#__storefront_product_types')
                ->where('ptName', '=', 'Software Download')
                ->where('ptModel', '=', 'software')
                ->execute();
        }
    }
}
