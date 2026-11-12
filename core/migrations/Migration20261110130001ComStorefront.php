<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for renaming the software product type the converted
 * Migration20150729164314ComStorefront created as "SoftwareDownload"
 *
 * The store and the cart look the type up as "Software Download", so a hub
 * with the misspelled one cannot sell downloadable software. Nothing changes
 * unless the misspelled type is there.
 **/
class Migration20261110130001ComStorefront extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__storefront_product_types')) {
            return;
        }

        $misspelled = $this->db
            ->getQuery()
            ->select('ptId')
            ->from('#__storefront_product_types')
            ->where('ptName', '=', 'SoftwareDownload')
            ->where('ptModel', '=', 'software');

        if ($misspelled->doesntExist()) {
            return;
        }

        $correct = $this->db
            ->getQuery()
            ->select('ptId')
            ->from('#__storefront_product_types')
            ->where('ptName', '=', 'Software Download');

        // Both names present means someone has already worked around it, and
        // products may use either type; that is for an administrator to sort.
        if (!$correct->doesntExist()) {
            $this->log(
                'Both "SoftwareDownload" and "Software Download" product types exist; leaving them as they are',
                'warning',
            );
            return;
        }

        $this->db
            ->getQuery()
            ->update('#__storefront_product_types')
            ->set(['ptName' => 'Software Download'])
            ->where('ptName', '=', 'SoftwareDownload')
            ->where('ptModel', '=', 'software')
            ->execute();

        $this->log('Renamed the "SoftwareDownload" product type to "Software Download"');
    }
}
