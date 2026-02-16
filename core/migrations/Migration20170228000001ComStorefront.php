<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add `exclude` column to storefront access_groups table
  *
**/
class Migration20170228000001ComStorefront extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__storefront_skus')
            && !$schema->hasColumn('#__storefront_skus', 'sCheckoutNotes')
        ) {
            $schema->addColumn('#__storefront_skus', 'sCheckoutNotes')->string()->execute();
            $schema->addColumn('#__storefront_skus', 'sCheckoutNotesRequired')
                ->tinyInteger()
                ->notNull()
                ->default(0)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__storefront_skus')
            && $schema->hasColumn('#__storefront_skus', 'sCheckoutNotes')
        ) {
            $schema->dropColumn('#__storefront_skus', 'sCheckoutNotes');
            $schema->dropColumn('#__storefront_skus', 'sCheckoutNotesRequired');
        }
    }
}
